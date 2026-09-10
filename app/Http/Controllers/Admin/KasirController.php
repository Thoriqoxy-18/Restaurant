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
        $stats = [
            'new' => Order::where('status', 'pending')->count(),
            'processing' => Order::whereIn('status', ['confirmed', 'preparing'])->count(),
            'ready' => Order::where('status', 'served')->count(),
            'revenue' => Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total'),
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

    public function orders(): View
    {
        $orders = Order::with(['orderItems.menuItem', 'orderItems.options', 'restaurantTable'])
            ->latest()
            ->get();

        return view('admin.orders.index', compact('orders'));
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
            ->get();

        $stats = [
            'revenue' => Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total'),
            'success' => Order::where('payment_status', 'paid')->count(),
            'waiting' => Order::where('payment_status', 'unpaid')->count(),
        ];

        return view('admin.payments.index', compact('orders', 'stats'));
    }

    public function notifications(): View
    {
        $notifications = \App\Models\Notification::latest()->take(30)->get();

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
            ->with(['orders' => fn ($q) => $q->latest()])
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

        if (in_array($status, $allowed, true)) {
            $table->status = $status;
            $table->save();
        }

        return back()->with('success', 'Status meja diperbarui.');
    }
}
