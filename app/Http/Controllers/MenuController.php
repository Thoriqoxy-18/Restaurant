<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(RestaurantTable $table, \Illuminate\Http\Request $request): View
    {
        $menuItems = MenuItem::with(['category', 'variations', 'toppings', 'sauces'])
            ->where('is_available', true)
            ->orderBy('is_signature', 'desc')
            ->orderBy('is_bestseller', 'desc')
            ->get();

        $categories = Category::orderBy('sort_order')->get();

        // Order aktif hanya untuk SESSION customer ini (bukan semua order meja).
        $customerSession = $request->attributes->get('customer_session');
        $activeOrders = collect();
        if ($customerSession) {
            $activeOrders = Order::with('orderItems')
                ->where('customer_session_id', $customerSession->id)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'served'])
                ->latest('id')
                ->get();

            // Jika tidak ada order aktif, tampilkan order terakhir yang selesai/dibatalkan
            // agar customer tetap bisa melihat status akhir (mis. "Pesanan Selesai") dari halaman menu.
            if ($activeOrders->isEmpty()) {
                $lastOrder = Order::with('orderItems')
                    ->where('customer_session_id', $customerSession->id)
                    ->whereIn('status', ['completed', 'cancelled'])
                    ->latest('id')
                    ->first();
                if ($lastOrder) {
                    $activeOrders = collect([$lastOrder]);
                }
            }
        }

        return view('customer.menu', compact('menuItems', 'categories', 'table', 'activeOrders'));
    }

    /**
     * Entry dari QR meja: /menu?table=1
     */
    public function tableEntry(Request $request): View|RedirectResponse
    {
        $tableId = $request->input('table');

        if ($tableId === null || $tableId === '') {
            return view('customer.table-status', ['message' => 'Nomor meja belum dipilih.']);
        }

        $table = RestaurantTable::query()->find($tableId);
        if (!$table) {
            return view('customer.table-status', ['message' => 'Meja tidak ditemukan.']);
        }

        session(['table_id' => $table->id]);

        return redirect()->route('menu', $table);
    }

    public function qrTest(): View
    {
        $tables = RestaurantTable::query()->orderBy('table_number')->get();

        return view('customer.table-qr', compact('tables'));
    }
}
