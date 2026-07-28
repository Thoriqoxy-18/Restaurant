@extends('customer.layouts.app')

@section('title', 'Cart - Verdant Bistro')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Your Cart</h2>
        <p class="text-sm text-gray-500">Review your selection</p>
    </div>

    {{-- Cart Items --}}
    <div class="space-y-3">
        <template x-for="item in $store.cart.items" :key="item.id">
            @include('customer.components.cart-item')
        </template>
    </div>

    {{-- Empty State --}}
    <template x-if="$store.cart.items.length === 0">
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">shopping_basket</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Your cart is empty</h3>
            <p class="text-sm text-gray-500 mb-6">Start adding delicious items from our menu.</p>
            <a href="{{ route('home') }}" class="px-6 py-2.5 bg-primary text-white rounded-full text-sm font-semibold active:scale-95 transition-transform shadow-md shadow-primary/20">
                Browse Menu
            </a>
        </div>
    </template>

    {{-- Order Summary --}}
    <template x-if="$store.cart.items.length > 0">
        <section class="mt-8 p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="space-y-3">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span x-text="'Rp ' + $store.cart.subtotal.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Service Fee (5%)</span>
                    <span x-text="'Rp ' + $store.cart.serviceFee.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Tax (10%)</span>
                    <span x-text="'Rp ' + $store.cart.tax.toLocaleString('id-ID')"></span>
                </div>
                <div class="h-px bg-gray-100"></div>
                <div class="flex justify-between text-base font-semibold text-gray-800">
                    <span>Total</span>
                    <span class="text-primary" x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span>
                </div>
            </div>
        </section>
    </template>

    {{-- Checkout Button (pushed to bottom of main, before footer) --}}
    <template x-if="$store.cart.items.length > 0">
        <div class="mt-6">
            <a href="{{ route('checkout') }}" class="gradient-button w-full h-11 sm:h-12 rounded-full flex items-center justify-between px-4 sm:px-6 text-white font-semibold text-xs sm:text-sm active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    Proceed to Checkout
                </span>
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </a>
        </div>
    </template>
</div>
@endsection
