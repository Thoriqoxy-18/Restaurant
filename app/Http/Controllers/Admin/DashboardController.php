<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalCategories' => Category::count(),
            'totalMenus' => MenuItem::count(),
            'totalTables' => RestaurantTable::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'processingOrders' => Order::whereIn('status', ['confirmed', 'preparing'])->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'recentOrders' => Order::with('restaurantTable')->orderBy('created_at', 'desc')->take(10)->get(),
        ]);
    }
}
