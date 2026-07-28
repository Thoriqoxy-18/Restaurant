@extends('admin.layouts.app')
@section('title', 'Order #' . $order->order_number . ' - ' . config('app.name'))
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-1">
                <span class="material-symbols-outlined text-base">arrow_back</span> Back to Orders
            </a>
            <h2 class="text-xl font-bold text-gray-800">Order #{{ $order->order_number }}</h2>
            <p class="text-sm text-gray-500">Table {{ $order->restaurantTable->table_number ?? '-' }} &middot; {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold
            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
            @elseif($order->status == 'processing') bg-blue-100 text-blue-700
            @elseif($order->status == 'completed') bg-green-100 text-green-700
            @else bg-red-100 text-red-700 @endif
        ">{{ $order->status }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Order Items</h3>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->orderItems as $item)
                        <div class="flex gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 bg-gradient-to-br from-primary/5 to-secondary/5">
                                <img src="{{ $item->menuItem && $item->menuItem->image ? asset($item->menuItem->image) : asset('assets/images/default/no-image.svg') }}" alt="" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-medium text-gray-800">{{ $item->menuItem->name ?? 'Menu' }}</p>
                                    <p class="text-sm font-medium text-gray-800">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-xs text-gray-400">Qty: {{ $item->quantity }} @if($item->notes) &middot; {{ $item->notes }} @endif</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($order->notes)
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Order Notes</h3>
                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
            </section>
            @endif
        </div>

        <div class="space-y-4">
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Payment Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-gray-500"><span>Service &amp; Tax</span><span>Rp {{ number_format($order->service_fee + $order->tax, 0, ',', '.') }}</span></div>
                    <div class="pt-3 mt-3 border-t border-gray-100 flex justify-between font-semibold text-gray-800"><span>Total</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                    @if ($order->payment_method)
                        <div class="pt-2 text-xs text-gray-400">Paid via {{ $order->payment_method }}</div>
                    @endif
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full h-10 rounded-xl border border-gray-200 text-sm px-3 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="w-full h-10 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">Update Status</button>
                </form>
            </section>

            @if ($order->status == 'completed' && !$order->payment_method)
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Process Payment</h3>
                <form method="POST" action="{{ route('admin.orders.payment', $order->id) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full h-10 rounded-xl border border-gray-200 text-sm px-3 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                            <option value="debit">Debit Card</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full h-10 rounded-xl bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition-colors flex items-center justify-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-sm">payments</span> Confirm Payment
                    </button>
                </form>
            </section>
            @endif
        </div>
    </div>
</div>
@endsection
