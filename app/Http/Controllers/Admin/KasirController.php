<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function home(): View
    {
        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();

        $stats = [
            'new' => Order::where('status', 'pending')->count(),
            'processing' => Order::whereIn('status', ['confirmed', 'preparing'])->count(),
            'ready' => Order::where('status', 'served')->count(),
            'revenue' => Order::where('payment_status', 'paid')->whereBetween('created_at', [$todayStart, $todayEnd])->sum('total'),
        ];

        $newOrders = Order::with(['orderItems.menuItem', 'restaurantTable'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $processing = Order::with(['orderItems.menuItem', 'restaurantTable'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->latest()
            ->take(6)
            ->get();

        $activity = Order::with('restaurantTable')->latest()->take(6)->get();

        return view('admin.dashboard.index', compact('stats', 'newOrders', 'processing', 'activity'));
    }

    public function pendingOrdersData()
    {
        $orders = Order::with(['orderItems.menuItem', 'restaurantTable'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'table' => $o->restaurantTable?->label ?? 'Takeaway',
                'items_html' => $o->orderItems->take(3)
                    ->map(fn ($i) => e($i->quantity.'x '.($i->menuItem->name ?? 'Menu')))
                    ->implode('<br>'),
                'url' => route('kasir.orders.status', $o),
            ]);

        return response()->json([
            'count' => Order::where('status', 'pending')->count(),
            'orders' => $orders,
        ]);
    }

    public function orders(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $f = $request->query('f', 'all');

        $query = Order::with(['orderItems.menuItem', 'orderItems.options', 'restaurantTable'])->latest();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('order_number', 'like', '%'.$q.'%')
                    ->orWhereHas('restaurantTable', fn ($t) => $t->where('name', 'like', '%'.$q.'%'));
            });
        }

        $groups = [
            'pending' => ['pending'],
            'proses' => ['confirmed', 'preparing'],
            'served' => ['served'],
            'completed' => ['completed'],
            'cancelled' => ['cancelled'],
        ];
        if ($f !== 'all' && isset($groups[$f])) {
            $query->whereIn('status', $groups[$f]);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders', 'q', 'f'));
    }

    public function orderDetail(Order $order): View
    {
        $order->load(['orderItems.menuItem', 'orderItems.options', 'restaurantTable']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $allowed = ['confirmed', 'preparing', 'served', 'completed', 'cancelled'];
        $status = $request->input('status');

        if (! in_array($status, $allowed, true)) {
            return back()->with('error', 'Status tidak valid.');
        }

        // State machine transisi status.
        // pending -> confirmed -> preparing -> served -> completed
        // cancelled hanya boleh dari pending/confirmed/preparing.
        $transitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['served', 'cancelled'],
            'served' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        $statusLabel = [
            'pending' => 'Menunggu', 'confirmed' => 'Diterima', 'preparing' => 'Diproses',
            'served' => 'Siap Diambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];

        $current = $order->status;
        $next = $transitions[$current] ?? [];

        if (! in_array($status, $next, true)) {
            return back()->with('error', 'Transisi status tidak valid: pesanan tidak dapat diubah dari "'.($statusLabel[$current] ?? $current).'" ke "'.($statusLabel[$status] ?? $status).'".');
        }

        // Pesanan tidak boleh diselesaikan sebelum pembayaran LUNAS.
        if ($status === 'completed' && $order->payment_status !== 'paid') {
            return back()->with('error', 'Pesanan belum LUNAS. Konfirmasi pembayaran terlebih dahulu sebelum menyelesaikan pesanan.');
        }

        $order->status = $status;
        if ($status === 'completed') {
            $order->completed_at = now();
            $order->completed_by = $request->user()->id;
        }
        $order->save();

        $label = match ($status) {
            'confirmed' => 'pesanan diterima',
            'preparing' => 'mulai diproses',
            'served' => 'siap diambil',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan',
        };

        return back()->with('success', 'Status pesanan diperbarui: '.$label.'.');
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        // Hanya Kasir (dicek middleware role:kasir). Pembayaran hanya bisa dikonfirmasi SEKALI.
        if ($order->payment_status !== 'unpaid') {
            return back()->with('error', 'Pembayaran order ini sudah dikonfirmasi.');
        }

        // Order yang sudah dibatalkan tidak boleh ditandai LUNAS.
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Pesanan sudah dibatalkan — pembayaran tidak dapat dikonfirmasi.');
        }

        $order->payment_status = 'paid';
        $order->paid_at = now();
        $order->confirmed_by = $request->user()->id;
        $order->save();

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi dan dinyatakan LUNAS.');
    }

    public function payments(): View
    {
        $orders = Order::with(['restaurantTable'])
            ->latest()
            ->paginate(20);

        $stats = [
            'revenue' => Order::where('payment_status', 'paid')->whereBetween('created_at', [today()->startOfDay(), today()->endOfDay()])->sum('total'),
            'success' => Order::where('payment_status', 'paid')->count(),
            'waiting' => Order::where('payment_status', 'unpaid')->count(),
        ];

        return view('admin.payments.index', compact('orders', 'stats'));
    }

    public function notifications(): View
    {
        $notifications = \App\Models\Notification::with('order')->latest()->take(30)->get();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function notificationsData()
    {
        $items = \App\Models\Notification::latest()->take(20)->get()->map(fn ($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'message' => $n->message,
            'time' => $n->created_at->diffForHumans(),
            'read' => $n->read_at !== null,
            'order_id' => $n->order_id,
        ]);

        return response()->json([
            'count' => \App\Models\Notification::whereNull('read_at')->count(),
            'items' => $items,
        ]);
    }

    public function notificationsMarkRead(\App\Models\Notification $notification)
    {
        $notification->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function notificationsMarkAllRead()
    {
        \App\Models\Notification::whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function tables(): View
    {
        $tables = RestaurantTable::query()
            ->with(['orders' => fn ($q) => $q->latest()->with('orderItems')])
            ->orderBy('table_number')
            ->get();

        $stats = [
            'all' => $tables->count(),
            'available' => $tables->where('status', 'available')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
        ];

        return view('admin.tables.index', compact('tables', 'stats'));
    }

    public function tableStatus(Request $request, RestaurantTable $table): RedirectResponse
    {
        $allowed = ['available', 'occupied', 'reserved'];
        $status = $request->input('status');

        if (! in_array($status, $allowed, true)) {
            return back()->with('error', 'Status meja tidak valid.');
        }

        $table->status = $status;
        $table->save();

        return back()->with('success', 'Status meja diperbarui.');
    }
}
