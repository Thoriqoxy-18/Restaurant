<?php
namespace App\Http\Controllers\Kasir;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendingOrders = Order::with(['restaurantTable', 'orderItems.menuItem'])
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderBy('created_at', 'desc')
            ->get();

        $recentOrders = Order::with('restaurantTable')
            ->whereIn('status', ['served', 'completed'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('kasir.dashboard', compact('pendingOrders', 'recentOrders'));
    }
}
