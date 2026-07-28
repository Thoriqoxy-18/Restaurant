<?php
namespace App\Http\Controllers\Kasir;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('restaurantTable')
            ->orderByRaw("FIELD(status, 'pending','confirmed','preparing','served','completed')")
            ->orderBy('created_at', 'desc')
            ->get();
        return view('kasir.orders', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['orderItems.menuItem', 'restaurantTable', 'customerSession']);
        return view('kasir.order-detail', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,served,completed,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
