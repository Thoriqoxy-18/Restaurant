<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(RestaurantTable $table, \Illuminate\Http\Request $request): View
    {
        $menuItems = MenuItem::with([
                'category:id,name,slug',
                'variations:id,menu_item_id,name,extra_price,sort_order',
                'toppings:id,menu_item_id,name,extra_price,sort_order',
                'sauces:id,menu_item_id,name,extra_price,sort_order',
            ])
            ->select(['id', 'name', 'slug', 'description', 'price', 'old_price', 'image_path', 'category_id', 'prep_time_minutes', 'is_vegan', 'has_spice_level', 'rating', 'is_signature', 'is_bestseller', 'is_available', 'created_at'])
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
     * Halaman demo QR meja — HANYA untuk environment local/testing.
     * Di production endpoint ini 404 agar token meja tidak ter-expose.
     */
    public function qrTest(): View
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        $tables = RestaurantTable::query()->orderBy('table_number')->get();

        return view('customer.table-qr', compact('tables'));
    }
}
