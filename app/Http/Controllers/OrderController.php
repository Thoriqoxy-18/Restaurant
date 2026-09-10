<?php
namespace App\Http\Controllers;

use App\Models\CustomerSession;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function status(RestaurantTable $table, Order $order): View
    {
        $order->load(['orderItems.menuItem', 'orderItems.options', 'restaurantTable']);
        return view('customer.order-status', compact('order', 'table'));
    }

    public function payment(Request $request, RestaurantTable $table)
    {
        // Store cart in session temporarily for the payment flow
        $cart = json_decode($request->input('cart', '[]'), true);
        if (empty($cart)) {
            return redirect()->route('menu', $table)->with('error', 'Cart kosong.');
        }
        session(['pending_cart' => $cart]);
        return view('customer.payment-select', compact('table'));
    }

    public function paymentSelect(RestaurantTable $table): View|RedirectResponse
    {
        if (!session('pending_cart')) {
            return redirect()->route('menu', $table);
        }
        return view('customer.payment-select', compact('table'));
    }

    public function qris(RestaurantTable $table): View
    {
        if (!session('pending_cart')) {
            return redirect()->route('menu', $table);
        }
        $cart = session('pending_cart');
        $total = $this->calculateTotal($cart);
        $qrSvg = \App\Support\DemoQrCode::svg('DEMO-QRIS-VERDANT-BISTRO');
        return view('customer.payment-qris', compact('table', 'cart', 'total', 'qrSvg'));
    }

    public function cash(RestaurantTable $table): View
    {
        if (!session('pending_cart')) {
            return redirect()->route('menu', $table);
        }
        $cart = session('pending_cart');
        $total = $this->calculateTotal($cart);
        return view('customer.payment-cash', compact('table', 'cart', 'total'));
    }

    public function confirm(Request $request, RestaurantTable $table): RedirectResponse
    {
        $session = $request->attributes->get('customer_session');
        if (!$session || !$session instanceof CustomerSession) {
            return redirect()->route('menu', $table)->with('error', 'Session tidak valid.');
        }

        $cart = session('pending_cart');
        if (empty($cart)) {
            return redirect()->route('menu', $table)->with('error', 'Cart kosong.');
        }

        $method = $request->input('payment_method', 'cash');

        $subtotal = 0;
        $items = [];

        foreach ($cart as $item) {
            $menuItem = MenuItem::find($item['id']);
            if (!$menuItem || !$menuItem->is_available) continue;

            $itemPrice = (float) $menuItem->price;
            $itemNotes = $item['notes'] ?? '';
            $variationPrice = 0;
            $options = [];

            if (!empty($item['variation_id'])) {
                $var = $menuItem->variations()->find($item['variation_id']);
                if ($var) {
                    $variationPrice = (float) $var->extra_price;
                    $options[] = ['type' => 'variation', 'name' => $var->name, 'extra_price' => $variationPrice];
                }
            }

            if (!empty($item['topping_ids'])) {
                $toppings = $menuItem->toppings()->whereIn('id', $item['topping_ids'])->get();
                foreach ($toppings as $t) {
                    $variationPrice += (float) $t->extra_price;
                    $options[] = ['type' => 'topping', 'name' => $t->name, 'extra_price' => (float) $t->extra_price];
                }
            }

            if (!empty($item['sauce_ids'])) {
                $sauces = $menuItem->sauces()->whereIn('id', $item['sauce_ids'])->get();
                foreach ($sauces as $s) {
                    $variationPrice += (float) $s->extra_price;
                    $options[] = ['type' => 'sauce', 'name' => $s->name, 'extra_price' => (float) $s->extra_price];
                }
            }

            $unitPrice = $itemPrice + $variationPrice;
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $subtotal += $unitPrice * $qty;

            $items[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $qty,
                'price' => $unitPrice,
                'notes' => $itemNotes,
                'options' => $options,
            ];
        }

        if (empty($items)) {
            return redirect()->route('menu', $table)->with('error', 'Item tidak valid.');
        }

        $tax = round($subtotal * 0.05, 2);
        $service = round($subtotal * 0.05, 2);
        $total = round($subtotal + $tax + $service, 2);
        $orderNum = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Semua pembayaran (Tunai & QRIS demo) mulai sebagai "Menunggu Pembayaran".
        // Status menjadi Lunas hanya setelah Kasir mengonfirmasi bahwa uang diterima.
        $paymentStatus = 'unpaid';
        $paymentProvider = $method === 'qris' ? $request->input('provider') : null;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'order_number' => $orderNum,
                'restaurant_table_id' => $table->id,
                'customer_session_id' => $session->id,
                'status' => 'pending',
                'payment_method' => $method,
                'payment_status' => $paymentStatus,
                'payment_provider' => $paymentProvider,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'service_charge' => $service,
                'total' => $total,
            ]);

            foreach ($items as $itemData) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'notes' => $itemData['notes'],
                ]);

                foreach ($itemData['options'] as $opt) {
                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'type' => $opt['type'],
                        'name' => $opt['name'],
                        'extra_price' => $opt['extra_price'],
                    ]);
                }
            }

            DB::commit();
            session()->forget('pending_cart');

            // Notifikasi untuk Kasir
            try {
                \App\Models\Notification::create([
                    'type' => 'new_order',
                    'title' => 'Pesanan baru masuk',
                    'message' => "Pesanan #{$order->order_number} dari {$table->label} telah masuk.",
                    'order_id' => $order->id,
                ]);
            } catch (\Throwable $e) {
                // Notifikasi bersifat opsional — jangan gagalkan order.
            }

            return redirect()->route('order.status', [$table, $order, 'placed' => 1]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('menu', $table)->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function pollStatus(RestaurantTable $table, Order $order)
    {
        $order = $order->fresh();

        return response()->json(['status' => $order->status, 'payment_status' => $order->payment_status]);
    }

    private function calculateTotal(array $cart): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $menuItem = MenuItem::find($item['id']);
            if (!$menuItem) continue;
            $extra = 0;
            if (!empty($item['variation_id'])) {
                $var = $menuItem->variations()->find($item['variation_id']);
                if ($var) $extra += (float) $var->extra_price;
            }
            if (!empty($item['topping_ids'])) {
                $extra += (float) $menuItem->toppings()->whereIn('id', $item['topping_ids'])->sum('extra_price');
            }
            if (!empty($item['sauce_ids'])) {
                $extra += (float) $menuItem->sauces()->whereIn('id', $item['sauce_ids'])->sum('extra_price');
            }
            $subtotal += ((float) $menuItem->price + $extra) * max(1, (int) ($item['quantity'] ?? 1));
        }
        $tax = round($subtotal * 0.05, 2);
        $service = round($subtotal * 0.05, 2);
        $total = round($subtotal + $tax + $service, 2);
        return ['subtotal' => $subtotal, 'tax' => $tax, 'service' => $service, 'total' => $total];
    }
}
