@extends('customer.layouts.app')
@section('title', 'Order #' . $order->order_number . ' - ' . config('app.name'))
@section('content')
<div class="max-w-2xl mx-auto pb-8">
    <div class="mb-6">
        <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-2">
            <span class="material-symbols-outlined text-base">arrow_back</span> Back to Orders
        </a>
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Order #{{ $order->order_number }}</h2>
        <p class="text-sm text-gray-500">We're preparing your fresh meal with love.</p>
    </div>

    <section class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6 relative overflow-hidden">
        <div class="flex justify-between items-start relative z-10">
            @php
                $steps = ['pending' => 0, 'processing' => 1, 'completed' => 2, 'cancelled' => 2];
                $currentStep = $steps[$order->status] ?? 0;
                $stepLabels = ['Waiting', 'Processing', 'Delivery', 'Served'];
                $stepIcons = ['check', 'restaurant', 'delivery_dining', 'flatware'];
            @endphp
            @foreach ($stepLabels as $i => $label)
                <div class="flex flex-col items-center gap-1.5 w-1/4">
                    @if ($i < $currentStep)
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white shadow-sm">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">{{ $stepIcons[$i] }}</span>
                        </div>
                        <span class="text-[11px] font-medium text-primary text-center">{{ $label }}</span>
                    @elseif ($i == $currentStep)
                        <div class="relative flex items-center justify-center w-10 h-10">
                            <div class="absolute w-full h-full bg-primary/20 rounded-full animate-ping"></div>
                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-lg">{{ $stepIcons[$i] }}</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-primary text-center">{{ $label }}</span>
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-300">
                            <span class="material-symbols-outlined text-lg">{{ $stepIcons[$i] }}</span>
                        </div>
                        <span class="text-[11px] font-medium text-gray-300 text-center">{{ $label }}</span>
                    @endif
                </div>
                @if ($i < count($stepLabels) - 1)
                    <div class="absolute top-5 h-0.5 {{ $i < $currentStep ? 'bg-primary' : 'bg-gray-200' }}" style="left: {{ $i * 25 + 12.5 }}%; right: {{ 75 - $i * 25 }}%"></div>
                @endif
            @endforeach
        </div>
        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-tertiary/10 flex items-center justify-center"><span class="material-symbols-outlined text-tertiary">timer</span></div>
                <div><p class="text-xs text-gray-400">Estimated Arrival</p><p class="text-sm font-semibold text-gray-800">15-20 mins</p></div>
            </div>
            <div class="text-right"><p class="text-xs text-gray-400">Order Time</p><p class="text-sm font-semibold text-primary">{{ $order->created_at->format('h:i A') }}</p></div>
        </div>
    </section>

    <section class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Order Items</h3>
            <span class="text-xs font-semibold text-primary bg-primary/5 px-3 py-1 rounded-full">{{ $order->orderItems->count() }} Items</span>
        </div>
        <div class="space-y-2">
            @foreach ($order->orderItems as $orderItem)
                <div class="flex gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 rounded-lg overflow-hidden shrink-0 bg-gradient-to-br from-primary/5 to-secondary/5">
                        <img src="{{ $orderItem->menuItem && $orderItem->menuItem->image ? asset($orderItem->menuItem->image) : asset('assets/images/default/no-image.svg') }}" alt="" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-0.5">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $orderItem->menuItem->name ?? 'Menu' }}</p>
                            <p class="text-sm font-medium text-gray-800 whitespace-nowrap ml-2">Rp {{ number_format($orderItem->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-xs text-gray-400 truncate">{{ $orderItem->notes ?? '-' }}</p>
                        <p class="text-xs font-semibold text-primary mt-1">Qty: {{ $orderItem->quantity }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-6">
        <div class="space-y-2">
            <div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm text-gray-500"><span>Service &amp; Tax (15%)</span><span>Rp {{ number_format($order->service_fee + $order->tax, 0, ',', '.') }}</span></div>
            <div class="pt-3 mt-3 border-t border-gray-100 flex justify-between items-center"><span class="text-sm font-semibold text-gray-800">Total Paid</span><span class="text-sm font-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
        </div>
        <div class="mt-4 p-3 bg-primary/5 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
            <p class="text-xs font-medium text-primary/80">Paid via {{ $order->payment_method ?? 'Cash' }}</p>
        </div>
    </section>

    <div class="flex flex-col items-center gap-4">
        <button class="w-full h-12 bg-primary text-white rounded-full text-sm font-semibold flex items-center justify-center gap-2 shadow-lg active:scale-[0.98] transition-transform">
            <span class="material-symbols-outlined text-lg">chat</span> Need Help?
        </button>
        <p class="text-xs text-gray-400 text-center">Our team is available 24/7 for your convenience.</p>
    </div>
</div>
@endsection
