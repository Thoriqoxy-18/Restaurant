@extends('admin.layouts.app')
@section('title', 'Pesanan')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" placeholder="Cari order..." value="{{ request('search') }}" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary w-48 sm:w-64">
        <select name="status" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button class="h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Order</th>
                <th class="px-5 py-3 font-medium">Meja</th>
                <th class="px-5 py-3 font-medium">Customer</th>
                <th class="px-5 py-3 font-medium">Item</th>
                <th class="px-5 py-3 font-medium">Total</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Pembayaran</th>
                <th class="px-5 py-3 font-medium">Waktu</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800 text-xs">#{{ $order->order_number }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $order->restaurantTable?->table_number ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $order->customer_name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $order->orderItems->count() }}</td>
                    <td class="px-5 py-3 text-gray-800 font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                            @elseif($order->status == 'completed') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif
                        ">{{ $order->status }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-xs {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-gray-400' }}">{{ $order->payment_status }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-medium">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-5 py-8 text-center text-gray-400">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($orders->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
