<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderController;
use App\Models\Category;
use App\Models\CustomerSession;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class OrderSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function makeTable(string $code): RestaurantTable
    {
        return RestaurantTable::create([
            'code' => $code,
            'table_number' => random_int(100000, 999999),
            'name' => 'Meja '.$code,
            'capacity' => 4,
            'status' => 'available',
        ]);
    }

    private function makeMenu(): MenuItem
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test', 'sort_order' => 1]);

        return MenuItem::create([
            'category_id' => $cat->id,
            'name' => 'Menu Test',
            'slug' => 'menu-test-'.uniqid(),
            'description' => 'test',
            'price' => 10000,
            'prep_time_minutes' => 10,
            'is_available' => true,
        ]);
    }

    private function customerRequest(CustomerSession $session, array $input): Request
    {
        $req = Request::create('/order/x/confirm', 'POST', $input);
        $req->setLaravelSession(app('session')->driver());
        $req->attributes->set('customer_session', $session);

        return $req;
    }

    
    public function test_cart_dari_meja_a_tidak_bisa_disubmit_ke_meja_b()
    {
        $tableA = $this->makeTable('A');
        $tableB = $this->makeTable('B');
        $sessA = CustomerSession::create(['restaurant_table_id' => $tableA->id, 'started_at' => now()]);
        $sessB = CustomerSession::create(['restaurant_table_id' => $tableB->id, 'started_at' => now()]);
        $menu = $this->makeMenu();

        // Cart dibuat untuk meja A (sesi A).
        session([
            'pending_cart' => [['id' => $menu->id, 'name' => $menu->name, 'quantity' => 1]],
            'order_idempotency_key' => 'key-cross',
            'pending_cart_table_id' => $tableA->id,
            'pending_cart_session_id' => $sessA->id,
        ]);

        $before = Order::count();
        $ctrl = new OrderController();
        $ctrl->confirm($this->customerRequest($sessB, ['payment_method' => 'cash', 'idempotency_key' => 'key-cross']), $tableB);

        $this->assertSame($before, Order::count(), 'Cross-table order harus ditolak (tidak ada order baru).');
        $this->assertNull(Order::where('restaurant_table_id', $tableB->id)->first(), 'Tidak boleh ada order untuk meja B.');
    }

    
    public function test_order_status_meja_lain_ditolak_404()
    {
        $tableA = $this->makeTable('A');
        $tableB = $this->makeTable('B');
        $sessA = CustomerSession::create(['restaurant_table_id' => $tableA->id, 'started_at' => now()]);
        $sessB = CustomerSession::create(['restaurant_table_id' => $tableB->id, 'started_at' => now()]);

        $orderB = Order::create([
            'order_number' => 'ORD-TESTB-'.uniqid(),
            'restaurant_table_id' => $tableB->id,
            'customer_session_id' => $sessB->id,
            'status' => 'pending',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'subtotal' => 10000,
            'tax' => 500,
            'service_charge' => 500,
            'total' => 11000,
        ]);

        $ctrl = new OrderController();
        $this->expectException(NotFoundHttpException::class);
        $ctrl->status($tableA, $orderB, $this->customerRequest($sessA, []));
    }

    
    public function test_double_submit_tidak_membuat_order_duplikat()
    {
        $table = $this->makeTable('A');
        $sess = CustomerSession::create(['restaurant_table_id' => $table->id, 'started_at' => now()]);
        $menu = $this->makeMenu();
        $cart = [['id' => $menu->id, 'name' => $menu->name, 'quantity' => 1]];

        $ctrl = new OrderController();

        // Submit #1 — order dibuat.
        session(['pending_cart' => $cart, 'order_idempotency_key' => 'key-dup', 'pending_cart_table_id' => $table->id, 'pending_cart_session_id' => $sess->id]);
        $ctrl->confirm($this->customerRequest($sess, ['payment_method' => 'cash', 'idempotency_key' => 'key-dup']), $table);

        // Submit #2 dengan key yang sama (klik ganda) — harus ditolak oleh cache idempotency.
        session(['pending_cart' => $cart, 'order_idempotency_key' => 'key-dup', 'pending_cart_table_id' => $table->id, 'pending_cart_session_id' => $sess->id]);
        $ctrl->confirm($this->customerRequest($sess, ['payment_method' => 'cash', 'idempotency_key' => 'key-dup']), $table);

        $this->assertSame(1, Order::where('customer_session_id', $sess->id)->count(), 'Double-submit harus menghasilkan tepat 1 order.');
    }
}