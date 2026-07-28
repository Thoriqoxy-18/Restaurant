@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menu</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalMenus }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalCategories }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Meja</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalTables }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pesanan</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-yellow-50 rounded-2xl border border-yellow-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-1">Pending</p>
        <p class="text-2xl font-bold text-yellow-700">{{ $pendingOrders }}</p>
    </div>
    <div class="bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Processing</p>
        <p class="text-2xl font-bold text-blue-700">{{ $processingOrders }}</p>
    </div>
    <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">Completed</p>
        <p class="text-2xl font-bold text-green-700">{{ $completedOrders }}</p>
    </div>
    <div class="bg-red-50 rounded-2xl border border-red-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Cancelled</p>
        <p class="text-2xl font-bold text-red-700">{{ $cancelledOrders }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <h2 class="text-sm font-semibold text-gray-800 mb-4">Pesanan Terbaru</h2>
    @if ($recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-100">
                        <th class="pb-2 font-medium">Order</th>
                        <th class="pb-2 font-medium">Meja</th>
                        <th class="pb-2 font-medium">Total</th>
                        <th class="pb-2 font-medium">Status</th>
                        <th class="pb-2 font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr class="border-b border-gray-50">
                            <td class="py-2.5 font-medium text-gray-800">#{{ $order->order_number }}</td>
                            <td class="py-2.5 text-gray-500">{{ $order->restaurantTable?->table_number ?? '-' }}</td>
                            <td class="py-2.5 text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                                    @elseif($order->status == 'completed') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700 @endif
                                ">{{ $order->status }}</span>
                            </td>
                            <td class="py-2.5 text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-400">Belum ada pesanan.</p>
    @endif
</div>
@endsection
