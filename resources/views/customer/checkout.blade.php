@extends('customer.layouts.app')
@section('title', 'Checkout - ' . config('app.name'))
@section('content')
<div class="max-w-2xl mx-auto pb-32">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-primary/10 text-primary mb-3">
            <span class="material-symbols-outlined text-3xl">restaurant_menu</span>
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Finalize Your Order</h2>
        <p class="text-sm text-gray-500">Confirm your items for Table {{ session('table_number', '—') }}</p>
    </div>

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Order Summary</span>
            <span class="text-xs font-semibold text-primary bg-primary/5 px-3 py-1 rounded-full" x-text="$store.cart.count + ' Items'"></span>
        </div>
        <ul class="divide-y divide-gray-50">
            <template x-for="item in $store.cart.items" :key="item.id">
                <li class="px-5 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-gradient-to-br from-primary/5 to-secondary/5">
                            <img src="{{ asset('assets/images/default/no-image.svg') }}" alt="" class="w-full h-full object-cover" loading="lazy">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800" x-text="item.name"></p>
                            <p class="text-xs text-gray-400" x-text="'Qty: ' + item.quantity"></p>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-800" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                </li>
            </template>
        </ul>
        <div class="px-5 py-3 bg-gray-50/50 space-y-1.5">
            <div class="flex justify-between items-center text-sm"><span class="text-gray-500">Subtotal</span><span class="text-gray-800" x-text="'Rp ' + $store.cart.subtotal.toLocaleString('id-ID')"></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-gray-500">Tax &amp; Service (15%)</span><span class="text-gray-800" x-text="'Rp ' + ($store.cart.serviceFee + $store.cart.tax).toLocaleString('id-ID')"></span></div>
        </div>
    </section>

    <section class="mb-6">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-1">Additional Notes</h3>
        <textarea name="notes" form="checkout-form" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none h-24" placeholder="Any special requests? (e.g., less spicy, no onion...)"></textarea>
    </section>

    <section class="mb-6">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-1">Payment Method</h3>
        <div class="space-y-3">
            <label class="flex items-center gap-3 w-full px-5 py-4 bg-white border-2 border-primary rounded-xl cursor-pointer">
                <input type="radio" name="payment_method" value="cash" checked class="w-4 h-4 text-primary focus:ring-primary/30">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center"><span class="material-symbols-outlined">payments</span></div>
                    <div class="text-left"><p class="text-sm font-semibold text-gray-800">Pay at Cashier</p><p class="text-xs text-gray-400">Pay after your meal</p></div>
                </div>
            </label>
            <div class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-xl flex items-center gap-3 opacity-60 cursor-not-allowed">
                <input type="radio" disabled class="w-4 h-4 text-gray-300">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center"><span class="material-symbols-outlined">qr_code_2</span></div>
                    <div class="text-left"><p class="text-sm font-semibold text-gray-400">QRIS / Digital</p><p class="text-xs text-gray-300">Fast &amp; secure payment</p></div>
                </div>
                <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-tight">Soon</span>
            </div>
        </div>
    </section>

    <form id="checkout-form" method="POST" action="{{ route('customer.checkout.store') }}">
        @csrf
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-100 px-4 sm:px-6 pt-3 pb-4 sm:pb-6">
            <div class="max-w-2xl mx-auto flex flex-col gap-3">
                <div class="flex justify-between items-end">
                    <div><p class="text-xs text-gray-400">Total Payable</p><p class="text-lg sm:text-xl font-bold text-primary" x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></p></div>
                </div>
                <button type="submit" class="w-full h-11 sm:h-12 rounded-full bg-primary text-white font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg active:scale-[0.98] transition-transform">
                    <span class="material-symbols-outlined text-base sm:text-lg">send</span> Send Order
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
