@extends('customer.layouts.app')
@section('title', 'Pembayaran Tunai - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex items-center gap-3 px-4 h-16 bg-surface border-b border-outline-variant/10">
    <a href="{{ route('order.payment.select', $table) }}" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center rounded-full active:scale-90 transition-transform">
        <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">Bayar di Kasir</h1>
</header>

<main class="pt-20 pb-10 px-4 max-w-lg mx-auto">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 text-center mb-4">
        <div class="w-16 h-16 bg-secondary-container rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-on-secondary-container" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M15 9.5c0-1.4-1.34-2.5-3-2.5s-3 1.1-3 2.5 1.34 2.5 3 2.5 3 1.1 3 2.5-1.34 2.5-3 2.5-3-1.1-3-2.5"/></svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Silakan lakukan pembayaran</h2>
        <p class="text-sm text-gray-500 mt-1">Bayar langsung kepada kasir atau pelayan di meja.</p>

        <div class="mt-5 bg-gray-50 rounded-2xl p-4">
            <p class="text-xs text-gray-400">Total yang harus dibayar</p>
            <p class="text-2xl font-bold text-primary mt-1">Rp{{ number_format($total['total'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
        <div class="flex justify-between text-sm text-gray-500 py-1.5"><span>Subtotal</span><span>Rp{{ number_format($total['subtotal'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-sm text-gray-500 py-1.5"><span>Pajak & Service</span><span>Rp{{ number_format($total['tax'] + $total['service'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-base font-bold text-gray-800 py-1.5 border-t border-gray-100 mt-1.5"><span>Total</span><span class="text-primary">Rp{{ number_format($total['total'], 0, ',', '.') }}</span></div>
    </div>

    <form method="POST" action="{{ route('order.confirm', $table) }}">
        @csrf
        <input type="hidden" name="payment_method" value="cash">
        <input type="hidden" name="idempotency_key" value="{{ session('order_idempotency_key') }}">
        <button type="submit" class="tap-btn pointer-events-auto w-full h-12 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20 mt-4">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Konfirmasi Pesanan
        </button>
    </form>
</main>
@endsection
