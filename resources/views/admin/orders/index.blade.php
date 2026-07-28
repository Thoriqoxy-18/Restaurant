@extends('admin.layouts.app')
@section('title', 'Orders - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Orders</h2>
            <p class="text-sm text-gray-500">View all orders across all tables.</p>
        </div>
        <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-gray-100 text-gray-500">{{ $orders->count() }} total</span>
    </div>

    @if ($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <span class="material-symbols-outlined text-4xl text-gray-300">receipt_long</span>
            <h3 class="text-base font-semibold text-gray-700 mt-2">No orders</h3>
            <p class="text-sm text-gray-400">Orders will appear once customers start ordering.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order #</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Table</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Items</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->restaurantTable->table_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->orderItems->count() }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                                        @elseif($order->status == 'completed') bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif
                                    ">{{ $order->status }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->payment_method)
                                        <span class="text-xs text-gray-500">{{ $order->payment_method }}</span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:text-primary/80">
                                        Detail <span class="material-symbols-outlined text-sm">chevron_right</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
