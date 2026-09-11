@extends('customer.layouts.app')
@section('title', 'Pembayaran QRIS - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex items-center gap-3 px-4 h-16 bg-surface border-b border-outline-variant/10">
    <a href="{{ route('order.payment.select', $table) }}" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center rounded-full active:scale-90 transition-transform">
        <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">Pembayaran QRIS</h1>
</header>

<main class="pt-20 pb-10 px-4 max-w-lg mx-auto" x-data="{ provider: '', stage: 'waiting' }">
    {{-- MODE DEMO label --}}
    <div class="flex items-center justify-center gap-2 mb-4">
        <span class="bg-red-100 text-red-600 border border-red-200 px-3 py-1 rounded-full text-[11px] font-bold tracking-wide">MODE DEMO</span>
        <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[11px] font-semibold">QRIS DEMO</span>
    </div>

    {{-- Status --}}
    <div class="flex justify-center mb-4">
        <template x-if="stage === 'waiting'">
            <span class="flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 px-4 py-1.5 rounded-full text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Pembayaran
            </span>
        </template>
        <template x-if="stage === 'processing'">
            <span class="flex items-center gap-2 bg-blue-50 text-blue-700 border border-blue-200 px-4 py-1.5 rounded-full text-sm font-semibold">
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Memproses Pembayaran
            </span>
        </template>
        <template x-if="stage === 'success'">
            <span class="flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 px-4 py-1.5 rounded-full text-sm font-semibold">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Pembayaran Berhasil
            </span>
        </template>
    </div>

    {{-- QR Code Demo --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 text-center mb-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-4">Scan QR Code menggunakan aplikasi pembayaran yang mendukung QRIS</p>

        <div class="mx-auto w-56 h-56 bg-white rounded-2xl border-2 border-gray-100 flex items-center justify-center overflow-hidden p-2 demo-qr">
            @if (!empty($qrSvg))
                {!! $qrSvg !!}
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-24 h-24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 21h-3M17 21v-3"/></svg>
                </div>
            @endif
        </div>

        <p class="text-[11px] font-semibold text-gray-500 mt-3">QRIS DEMO · <span class="text-gray-400 font-normal">Bukan QR pembayaran sungguhan</span></p>

        <div class="mt-4 bg-gray-50 rounded-2xl p-4">
            <p class="text-xs text-gray-400">Total Pembayaran</p>
            <p class="text-2xl font-bold text-primary mt-1">Rp{{ number_format($total['total'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Pilih aplikasi pembayaran (simulasi) --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 mb-4" x-show="stage === 'waiting'">
        <h2 class="text-sm font-bold text-gray-800 mb-1">Simulasi Pembayaran</h2>
        <p class="text-xs text-gray-400 mb-3">Pilih aplikasi pembayaran (hanya simulasi UI)</p>

        <div class="grid grid-cols-2 gap-2.5">
            @foreach ([
                'dana' => 'DANA',
                'gopay' => 'GoPay',
                'ovo' => 'OVO',
                'shopeepay' => 'ShopeePay',
                'mobilebanking' => 'Mobile Banking',
            ] as $val => $label)
            <button type="button" @click="provider = '{{ $val }}'"
                    class="tap-btn pointer-events-auto flex items-center gap-2 p-3 rounded-xl border-2 transition-all text-left"
                    :class="provider === '{{ $val }}' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-gray-200 bg-white'">
                <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" :class="provider === '{{ $val }}' ? 'border-primary' : 'border-gray-300'">
                    <span class="w-2.5 h-2.5 rounded-full transition-all" :class="provider === '{{ $val }}' ? 'bg-primary scale-100' : 'scale-0'"></span>
                </span>
                <span class="text-sm font-medium text-gray-700" x-text="'{{ $label }}'"></span>
            </button>
            @endforeach
        </div>

        <p class="text-xs text-gray-500 mt-3" x-show="provider" x-cloak>
            <span class="font-semibold text-primary" x-text="provider === 'dana' ? 'DANA' : (provider === 'gopay' ? 'GoPay' : (provider === 'ovo' ? 'OVO' : (provider === 'shopeepay' ? 'ShopeePay' : 'Mobile Banking')))"></span>
            dipilih
        </p>

        <button type="button"
                @click="if (stage === 'waiting' && provider) { stage = 'processing'; setTimeout(() => { stage = 'success'; }, 1000); }"
                :disabled="!provider"
                :class="provider ? 'bg-primary text-white shadow-lg shadow-primary/20 active:scale-[0.98]' : 'bg-gray-200 text-gray-400'"
                class="tap-btn pointer-events-auto w-full h-12 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all mt-4">
            Simulasikan Pembayaran
        </button>
    </div>

    {{-- Sukses --}}
    <div x-show="stage === 'success'" x-cloak class="bg-white rounded-3xl shadow-sm border border-green-200 p-6 text-center mb-4" x-transition:enter="transition-all duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="mx-auto w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-3">
            <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Pembayaran Berhasil</h2>
        <p class="text-xs text-gray-500 mt-1 mb-4">Ini hanya simulasi. Pesanan akan dibuat.</p>

        <div class="space-y-2 bg-gray-50 rounded-2xl p-4 text-left">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Metode</span><span class="font-semibold text-gray-800">QRIS</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Aplikasi</span>
                <span class="font-semibold text-gray-800" x-text="provider === 'dana' ? 'DANA' : (provider === 'gopay' ? 'GoPay' : (provider === 'ovo' ? 'OVO' : (provider === 'shopeepay' ? 'ShopeePay' : 'Mobile Banking')))"></span>
            </div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Total</span><span class="font-semibold text-primary">Rp{{ number_format($total['total'], 0, ',', '.') }}</span></div>
        </div>

        <form method="POST" action="{{ route('order.confirm', $table) }}" class="mt-5">
            @csrf
            <input type="hidden" name="payment_method" value="qris">
            <input type="hidden" name="simulated" value="1">
            <input type="hidden" name="provider" :value="provider">
            <input type="hidden" name="idempotency_key" value="{{ session('order_idempotency_key') }}">
            <button type="submit" class="tap-btn pointer-events-auto w-full h-12 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                Lihat Pesanan
            </button>
        </form>
    </div>

    {{-- Keterangan demo --}}
    <p class="text-[11px] text-gray-400 text-center mt-5 leading-relaxed">
        Ini hanya simulasi pembayaran untuk keperluan testing.<br>
        QRIS DEMO tidak dapat digunakan untuk pembayaran sungguhan.
    </p>
</main>

@push('styles')
<style>
    .demo-qr svg { width: 100%; height: 100%; display: block; }
</style>
@endpush
@endsection
