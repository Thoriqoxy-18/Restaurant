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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function status(RestaurantTable $table, Order $order, Request $request): View
    {
        $this->ensureOrderAccess($table, $order, $request);
        $order->load(['orderItems.menuItem', 'orderItems.options', 'restaurantTable']);
        return view('customer.order-status', compact('order', 'table'));
    }

    public function payment(Request $request, RestaurantTable $table)
    {
        // Store cart in session temporarily for the payment flow
        $rawCart = $request->input('cart');
        if (! is_string($rawCart) || strlen($rawCart) > 50000) {
            return redirect()->route('menu', $table)->with('error', 'Data keranjang tidak valid atau terlalu besar.');
        }

        $cart = json_decode($rawCart, true);

        if (! is_array($cart) || empty($cart)) {
            return redirect()->route('menu', $table)->with('error', 'Keranjang Anda masih kosong. Silakan pilih menu terlebih dahulu.');
        }

        if (count($cart) > 50) {
            return redirect()->route('menu', $table)->with('error', 'Terlalu banyak item dalam keranjang (maksimal 50).');
        }

        // Validasi struktur keranjang sebelum disimpan ke session.
        foreach ($cart as $item) {
            if (! is_array($item) || ! isset($item['id']) || ! is_numeric($item['id'])) {
                return redirect()->route('menu', $table)->with('error', 'Keranjang tidak valid. Silakan perbarui dan coba lagi.');
            }
            $qty = (int) ($item['quantity'] ?? 1);
            if ($qty < 1 || $qty > 50) {
                return redirect()->route('menu', $table)->with('error', 'Jumlah item tidak valid (maksimal 50 per item).');
            }
            if (isset($item['notes']) && mb_strlen((string) $item['notes']) > 500) {
                return redirect()->route('menu', $table)->with('error', 'Catatan terlalu panjang (maksimal 500 karakter).');
            }
        }

        // Validasi dini variasi/topping/saus terhadap menu item (bukan menunggu confirm).
        $menuItems = $this->loadCartMenuItems($cart);
        foreach ($cart as $item) {
            $menuItem = $menuItems[(int) $item['id']] ?? null;
            if (! $menuItem) {
                continue;
            }
            $variationId = (int) ($item['variation_id'] ?? 0);
            if ($variationId > 0 && ! $menuItem->variations->contains('id', $variationId)) {
                return redirect()->route('menu', $table)->with('error', 'Varian menu tidak valid.');
            }
            $toppingIds = is_array($item['topping_ids'] ?? null) ? array_filter(array_map('intval', $item['topping_ids'])) : [];
            if (! empty($toppingIds) && $menuItem->toppings->whereIn('id', $toppingIds)->count() !== count($toppingIds)) {
                return redirect()->route('menu', $table)->with('error', 'Topping menu tidak valid.');
            }
            $sauceIds = is_array($item['sauce_ids'] ?? null) ? array_filter(array_map('intval', $item['sauce_ids'])) : [];
            if (! empty($sauceIds) && $menuItem->sauces->whereIn('id', $sauceIds)->count() !== count($sauceIds)) {
                return redirect()->route('menu', $table)->with('error', 'Saus menu tidak valid.');
            }
        }

        // Keranjang diikat ke meja & sesi customer yang membuatnya, agar tidak bisa
        // di-submit ke meja lain (cegah cross-table order).
        $session = $request->attributes->get('customer_session');
        session([
            'pending_cart' => $cart,
            'order_idempotency_key' => (string) Str::uuid(),
            'pending_cart_table_id' => $table->id,
            'pending_cart_session_id' => $session instanceof CustomerSession ? $session->id : null,
        ]);
        return view('customer.payment-select', compact('table'));
    }

    protected function hasValidPendingCart(RestaurantTable $table, Request $request): bool
    {
        if (empty(session('pending_cart'))) {
            return false;
        }
        if (session('pending_cart_table_id') !== $table->id) {
            return false;
        }
        // Sesi harus ada & cocok; session_id null dianggap tidak valid (bukan lolos).
        $session = $request->attributes->get('customer_session');
        $storedSession = session('pending_cart_session_id');
        if (! ($session instanceof CustomerSession) || $storedSession === null || $session->id !== $storedSession) {
            return false;
        }

        return true;
    }

    protected function clearPendingCart(): void
    {
        session()->forget(['pending_cart', 'order_idempotency_key', 'pending_cart_table_id', 'pending_cart_session_id']);
    }

    public function paymentSelect(RestaurantTable $table, Request $request): View|RedirectResponse
    {
        if (! $this->hasValidPendingCart($table, $request)) {
            $this->clearPendingCart();
            return redirect()->route('menu', $table)->with('error', 'Sesi keranjang telah berakhir. Silakan ulangi pemesanan Anda.');
        }
        return view('customer.payment-select', compact('table'));
    }

    public function qris(RestaurantTable $table, Request $request): View
    {
        if (! $this->hasValidPendingCart($table, $request)) {
            $this->clearPendingCart();
            return redirect()->route('menu', $table)->with('error', 'Sesi keranjang telah berakhir. Silakan ulangi pemesanan Anda.');
        }
        $cart = session('pending_cart');
        $total = $this->calculateTotal($cart);
        $qrSvg = \App\Support\DemoQrCode::svg('DEMO-QRIS-VERDANT-BISTRO');
        return view('customer.payment-qris', compact('table', 'cart', 'total', 'qrSvg'));
    }

    public function cash(RestaurantTable $table, Request $request): View
    {
        if (! $this->hasValidPendingCart($table, $request)) {
            $this->clearPendingCart();
            return redirect()->route('menu', $table)->with('error', 'Sesi keranjang telah berakhir. Silakan ulangi pemesanan Anda.');
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

        // Keranjang harus milik meja & sesi ini (cegah cross-table order).
        if (! $this->hasValidPendingCart($table, $request)) {
            $this->clearPendingCart();
            return redirect()->route('menu', $table)->with('error', 'Sesi keranjang telah berakhir. Silakan ulangi pemesanan Anda.');
        }

        $cart = session('pending_cart');

        // Idempotency: cegah double-submit (klik ganda / koneksi lambat).
        $idempotencyKey = $request->input('idempotency_key');
        $sessionKey = session('order_idempotency_key');

        if (! $idempotencyKey || ! $sessionKey || $idempotencyKey !== $sessionKey) {
            return redirect()->route('menu', $table)->with('error', 'Sesi pemesanan tidak valid. Silakan ulangi pemesanan Anda.');
        }

        // Validasi metode & penyedia pembayaran (whitelist).
        $method = $request->input('payment_method', 'cash');
        if (! in_array($method, ['cash', 'qris'], true)) {
            return redirect()->route('menu', $table)->with('error', 'Metode pembayaran tidak valid.');
        }
        $provider = null;
        if ($method === 'qris') {
            $provider = $request->input('provider');
            if (! in_array($provider, ['dana', 'gopay', 'ovo', 'shopeepay', 'mobilebanking'], true)) {
                return redirect()->route('menu', $table)->with('error', 'Aplikasi pembayaran tidak valid.');
            }
        }

        $subtotal = 0;
        $items = [];
        $unavailable = [];

        // Load semua menu item + relasi dalam satu query (hindari N+1 per item cart).
        $menuItems = $this->loadCartMenuItems($cart);

        foreach ($cart as $item) {
            if (! is_array($item) || ! isset($item['id']) || ! is_numeric($item['id'])) {
                continue;
            }
            $menuItem = $menuItems[(int) $item['id']] ?? null;
            if (!$menuItem || !$menuItem->is_available) {
                $unavailable[] = $item['name'] ?? ('Menu (id: '.$item['id'].')');
                continue;
            }

            $itemPrice = (float) $menuItem->price;
            $itemNotes = mb_substr((string) ($item['notes'] ?? ''), 0, 500);
            $variationPrice = 0;
            $options = [];

            $variationId = (int) ($item['variation_id'] ?? 0);
            if ($variationId > 0) {
                $var = $menuItem->variations->firstWhere('id', $variationId);
                if ($var) {
                    $variationPrice = (float) $var->extra_price;
                    $options[] = ['type' => 'variation', 'name' => $var->name, 'extra_price' => $variationPrice];
                }
            }

            $toppingIds = is_array($item['topping_ids'] ?? null)
                ? array_filter(array_map('intval', $item['topping_ids']))
                : [];
            if (! empty($toppingIds)) {
                $toppings = $menuItem->toppings->whereIn('id', $toppingIds);
                foreach ($toppings as $t) {
                    $variationPrice += (float) $t->extra_price;
                    $options[] = ['type' => 'topping', 'name' => $t->name, 'extra_price' => (float) $t->extra_price];
                }
            }

            $sauceIds = is_array($item['sauce_ids'] ?? null)
                ? array_filter(array_map('intval', $item['sauce_ids']))
                : [];
            if (! empty($sauceIds)) {
                $sauces = $menuItem->sauces->whereIn('id', $sauceIds);
                foreach ($sauces as $s) {
                    $variationPrice += (float) $s->extra_price;
                    $options[] = ['type' => 'sauce', 'name' => $s->name, 'extra_price' => (float) $s->extra_price];
                }
            }

            $unitPrice = $itemPrice + $variationPrice;
            $qty = min(50, max(1, (int) ($item['quantity'] ?? 1)));
            $subtotal += $unitPrice * $qty;

            $items[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $qty,
                'price' => $unitPrice,
                'notes' => $itemNotes,
                'options' => $options,
            ];
        }

        // Jangan skip diam-diam: jika ada item yang sudah tidak tersedia,
        // batalkan seluruh proses dan beri tahu customer item mana yang bermasalah.
        if (! empty($unavailable)) {
            $names = implode(', ', array_unique($unavailable));

            return redirect()->route('menu', $table)->with('error', 'Beberapa item sudah tidak tersedia: '.$names.'. Silakan perbarui keranjang Anda lalu pesan ulang.');
        }

        if (empty($items)) {
            return redirect()->route('menu', $table)->with('error', 'Item tidak valid.');
        }

        $tax = round($subtotal * 0.05, 2);
        $service = round($subtotal * 0.05, 2);
        $total = round($subtotal + $tax + $service, 2);
        $orderNum = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        // Semua pembayaran (Tunai & QRIS demo) mulai sebagai "Menunggu Pembayaran".
        // Status menjadi Lunas hanya setelah Kasir mengonfirmasi bahwa uang diterima.
        $paymentStatus = 'unpaid';
        $paymentProvider = $provider;

        // Claim idempotency HANYA setelah semua validasi lolos, tepat sebelum order disimpan.
        // Cache::add bersifat atomik: hanya satu request yang berhasil claim key ini.
        if (! Cache::add('order_idem_'.$idempotencyKey, true, now()->addHours(2))) {
            return redirect()->route('menu', $table)->with('error', 'Pesanan ini sudah diproses. Jangan mengirim ulang pesanan.');
        }

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

            // Bulk insert order_items, lalu map id untuk order_item_options (2 insert besar,
            // bukan N+1 write per item).
            $now = now();
            $itemRows = [];
            foreach ($items as $itemData) {
                $itemRows[] = [
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'notes' => $itemData['notes'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            OrderItem::insert($itemRows);

            $insertedIds = OrderItem::where('order_id', $order->id)->orderBy('id')->pluck('id')->all();
            $optionRows = [];
            foreach (array_values($items) as $i => $itemData) {
                $itemId = $insertedIds[$i] ?? null;
                if ($itemId === null) {
                    continue;
                }
                foreach ($itemData['options'] as $opt) {
                    $optionRows[] = [
                        'order_item_id' => $itemId,
                        'type' => $opt['type'],
                        'name' => $opt['name'],
                        'extra_price' => $opt['extra_price'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if (! empty($optionRows)) {
                OrderItemOption::insert($optionRows);
            }

            DB::commit();
            session()->forget(['pending_cart', 'order_idempotency_key']);

            // Notifikasi untuk Kasir
            try {
                \App\Models\Notification::create([
                    'type' => 'new_order',
                    'title' => 'Pesanan baru masuk',
                    'message' => "Pesanan #{$order->order_number} dari {$table->label} telah masuk.",
                    'order_id' => $order->id,
                ]);
            } catch (\Throwable $e) {
                // Notifikasi bersifat opsional — jangan gagalkan order, tapi catat kegagalannya.
                Log::warning('Gagal membuat notifikasi pesanan', ['order_id' => $order->id ?? null, 'exception' => $e]);
            }

            return redirect()->route('order.status', [$table, $order, 'placed' => 1]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal memproses pesanan', ['exception' => $e, 'table_id' => $table->id, 'session_id' => $session->id ?? null]);
            return redirect()->route('menu', $table)->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function pollStatus(RestaurantTable $table, Order $order, Request $request)
    {
        $this->ensureOrderAccess($table, $order, $request);
        $order = $order->fresh();

        // Order bisa terhapus di antara request (race) — jangan 500.
        if (! $order) {
            return response()->json(['error' => 'order tidak ditemukan'], 404);
        }

        return response()->json(['status' => $order->status, 'payment_status' => $order->payment_status]);
    }

    /**
     * Pastikan order benar-benar milik meja & sesi customer yang mengakses.
     * Kembalikan 404 agar keberadaan order tidak bocor (tidak membedakan
     * order tidak ada vs order milik orang lain).
     */
    protected function ensureOrderAccess(RestaurantTable $table, Order $order, Request $request): void
    {
        $session = $request->attributes->get('customer_session');
        $sessionId = $session instanceof CustomerSession ? $session->id : null;

        abort_unless(
            $order->restaurant_table_id === $table->id && $order->customer_session_id === $sessionId,
            404
        );
    }

    private function calculateTotal(array $cart): array
    {
        $menuItems = $this->loadCartMenuItems($cart);
        $subtotal = 0;
        foreach ($cart as $item) {
            if (! is_array($item) || ! isset($item['id'])) {
                continue;
            }
            $menuItem = $menuItems[(int) $item['id']] ?? null;
            if (!$menuItem) continue;
            $extra = 0;
            $variationId = (int) ($item['variation_id'] ?? 0);
            if ($variationId > 0) {
                $var = $menuItem->variations->firstWhere('id', $variationId);
                if ($var) $extra += (float) $var->extra_price;
            }
            $toppingIds = is_array($item['topping_ids'] ?? null) ? array_filter(array_map('intval', $item['topping_ids'])) : [];
            if (! empty($toppingIds)) {
                $extra += (float) $menuItem->toppings->whereIn('id', $toppingIds)->sum('extra_price');
            }
            $sauceIds = is_array($item['sauce_ids'] ?? null) ? array_filter(array_map('intval', $item['sauce_ids'])) : [];
            if (! empty($sauceIds)) {
                $extra += (float) $menuItem->sauces->whereIn('id', $sauceIds)->sum('extra_price');
            }
            $subtotal += ((float) $menuItem->price + $extra) * min(50, max(1, (int) ($item['quantity'] ?? 1)));
        }
        $tax = round($subtotal * 0.05, 2);
        $service = round($subtotal * 0.05, 2);
        $total = round($subtotal + $tax + $service, 2);
        return ['subtotal' => $subtotal, 'tax' => $tax, 'service' => $service, 'total' => $total];
    }

    /**
     * Ambil semua menu item + relasi (variations/toppings/sauces) dalam SATU query,
     * agar loop cart tidak memicu 4-8 query per item.
     */
    private function loadCartMenuItems(array $cart): \Illuminate\Support\Collection
    {
        $ids = collect($cart)->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return MenuItem::with(['variations', 'toppings', 'sauces'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }
}
