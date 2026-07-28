@extends('customer.layouts.app')

@section('title', 'Orders - Verdant Bistro')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Your Orders</h2>
        <p class="text-sm text-gray-500">Order history for Table {{ session('table_number', '—') }}</p>
    </div>

    @if ($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">No orders yet</h3>
            <p class="text-sm text-gray-400 mb-6">Start by browsing our menu and placing an order.</p>
            <a href="{{ route('home') }}" class="px-5 py-2 bg-primary text-white rounded-full text-sm font-semibold active:scale-95 transition-transform shadow-md shadow-primary/20">
                Browse Menu
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                <a href="{{ route('order.detail', $order->id) }}" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-800">#{{ $order->order_number }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                            @elseif($order->status == 'completed') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif
                        ">{{ $order->status }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                        <span class="font-semibold text-gray-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-1.5 text-xs text-gray-400">
                        {{ $order->orderItems->count() }} item(s) · Table {{ $order->restaurantTable?->table_number ?? '-' }}
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
