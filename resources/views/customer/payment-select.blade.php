@extends('customer.layouts.app')
@section('title', 'Metode Pembayaran - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex items-center gap-3 px-4 h-16 bg-surface border-b border-outline-variant/10">
    <a href="{{ route('menu', $table) }}" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center rounded-full active:scale-90 transition-transform">
        <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">Metode Pembayaran</h1>
</header>

<main class="pt-20 pb-10 px-4 max-w-lg mx-auto" x-data="{ method: '' }">
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Metode Pembayaran</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih cara pembayaran yang kamu inginkan</p>
    </div>

    <div class="space-y-3">
        {{-- QRIS --}}
        <button type="button" @click="method = 'qris'"
                class="tap-btn pointer-events-auto w-full bg-white rounded-2xl p-5 shadow-sm border-2 transition-all active:scale-[0.98] text-left"
                :class="method === 'qris' ? 'border-primary ring-2 ring-primary/20 bg-primary/5' : 'border-gray-100'">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 21h-3M17 21v-3"/></svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">QRIS</p>
                    <p class="text-xs text-gray-500 mt-0.5">Scan QR untuk melakukan pembayaran.</p>
                </div>
                <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" :class="method === 'qris' ? 'border-primary' : 'border-gray-300'">
                    <span class="w-3 h-3 rounded-full transition-all" :class="method === 'qris' ? 'bg-primary scale-100' : 'scale-0'"></span>
                </span>
            </div>
        </button>

        {{-- Bayar di Kasir --}}
        <button type="button" @click="method = 'cash'"
                class="tap-btn pointer-events-auto w-full bg-white rounded-2xl p-5 shadow-sm border-2 transition-all active:scale-[0.98] text-left"
                :class="method === 'cash' ? 'border-primary ring-2 ring-primary/20 bg-primary/5' : 'border-gray-100'">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M15 9.5c0-1.4-1.34-2.5-3-2.5s-3 1.1-3 2.5 1.34 2.5 3 2.5 3 1.1 3 2.5-1.34 2.5-3 2.5-3-1.1-3-2.5"/></svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">Bayar di Kasir</p>
                    <p class="text-xs text-gray-500 mt-0.5">Bayar langsung kepada kasir atau pelayan.</p>
                </div>
                <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" :class="method === 'cash' ? 'border-primary' : 'border-gray-300'">
                    <span class="w-3 h-3 rounded-full transition-all" :class="method === 'cash' ? 'bg-primary scale-100' : 'scale-0'"></span>
                </span>
            </div>
        </button>
    </div>

    <button type="button"
            @click="if (method === 'qris') window.location = '{{ route('order.payment.qris', $table) }}'; else if (method === 'cash') window.location = '{{ route('order.payment.cash', $table) }}';"
            :disabled="!method"
            class="tap-btn pointer-events-auto w-full h-12 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all mt-6"
            :class="method ? 'bg-primary text-white shadow-lg shadow-primary/20 active:scale-[0.98]' : 'bg-gray-200 text-gray-400'">
        Lanjutkan Pembayaran
    </button>

    <p class="text-xs text-gray-400 text-center mt-6">Pembayaran akan dikonfirmasi setelah pesanan diproses.</p>
</main>
@endsection
