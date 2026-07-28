<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkoutPage()
    {
        $cart = session()->get('cart', []);
        if (empty($cart) || !session('table_id')) {
            return redirect()->route('home');
        }
        return view('customer.checkout', ['cart' => $cart, 'tableNumber' => session('table_number')]);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart) || !session('table_id')) {
            return redirect()->route('home')->with('error', 'Cart kosong atau meja tidak valid.');
        }

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $tax = round($subtotal * 0.1, 2);
        $service = round($subtotal * 0.05, 2);
        $total = round($subtotal + $tax + $service, 2);
        $orderNum = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        try {
            DB::beginTransaction();

            $order = Order::create([
                'order_number' => $orderNum,
                'restaurant_table_id' => session('table_id'),
                'customer_session_id' => session('customer_session_id'),
                'status' => 'pending',
                'subtotal' => $subtotal, 'tax' => $tax, 'service_charge' => $service, 'total' => $total,
                'notes' => $request->notes,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'spice_level' => $item['spice_level'] ?? null,
                    'notes' => $item['notes'] ?? '',
                ]);
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('customer.order.detail', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan.');
        }
    }

    public function index()
    {
        $orders = Order::with('restaurantTable')
            ->where('customer_session_id', session('customer_session_id'))
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders', compact('orders'));
    }

    public function detail($id)
    {
        $order = Order::with(['orderItems.menuItem', 'restaurantTable'])->findOrFail($id);
        return view('customer.order-detail', compact('order'));
    }
}
