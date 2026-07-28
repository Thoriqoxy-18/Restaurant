@extends('admin.layouts.app')
@section('title', 'Dashboard - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-sm text-gray-500">Overview of your restaurant's performance.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue Today</span>
                <span class="material-symbols-outlined text-gray-300">trending_up</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">from {{ $completedOrdersToday ?? 0 }} completed orders</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Orders</span>
                <span class="material-symbols-outlined text-gray-300">receipt_long</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ $activeOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">pending + processing</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Menus</span>
                <span class="material-symbols-outlined text-gray-300">menu_book</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalMenus ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">items on menu</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tables</span>
                <span class="material-symbols-outlined text-gray-300">table_restaurant</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTables ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">total tables</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Recent Orders</h3>
            @if (isset($recentOrders) && $recentOrders->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">#{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400">Table {{ $order->restaurantTable->table_number ?? '-' }} &middot; {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                <p class="text-xs">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                                        @elseif($order->status == 'completed') bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif
                                    ">{{ $order->status }}</span>
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-6">No recent orders.</p>
            @endif
        </section>

        <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.menus.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-primary/5 text-primary hover:bg-primary/10 transition-colors">
                    <span class="material-symbols-outlined text-2xl">add_circle</span>
                    <span class="text-xs font-medium">Add Menu Item</span>
                </a>
                <a href="{{ route('admin.categories.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-secondary/5 text-secondary hover:bg-secondary/10 transition-colors">
                    <span class="material-symbols-outlined text-2xl">create_new_folder</span>
                    <span class="text-xs font-medium">Add Category</span>
                </a>
                <a href="{{ route('admin.tables.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                    <span class="material-symbols-outlined text-2xl">add_location</span>
                    <span class="text-xs font-medium">Add Table</span>
                </a>
                <a href="{{ route('admin.staff.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-100 transition-colors">
                    <span class="material-symbols-outlined text-2xl">person_add</span>
                    <span class="text-xs font-medium">Add Staff</span>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
