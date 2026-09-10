@extends('customer.layouts.app')
@section('title', 'Pilih Meja - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex items-center gap-3 px-4 h-16 bg-surface border-b border-outline-variant/10">
    <div class="w-10 h-10 bg-primary-container/10 rounded-xl flex items-center justify-center">
        <svg class="w-5 h-5 text-primary-container" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h18M8 11V5m8 6V5M6 11v8h12v-8"/></svg>
    </div>
    <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">Verdant Bistro</h1>
</header>

<main class="pt-20 pb-10 px-4 min-h-[70vh] flex flex-col items-center justify-center text-center">
    <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-5">
        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h18M8 11V5m8 6V5M6 11v8h12v-8"/></svg>
    </div>
    <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $message }}</h2>
    <p class="text-sm text-gray-500 mb-6 max-w-xs leading-relaxed">Silakan pindai QR Code pada meja Anda untuk memulai pesanan.</p>
    <a href="{{ route('menu.qr-test') }}" class="tap-btn pointer-events-auto px-6 h-12 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
        Lihat Daftar QR Meja
    </a>
</main>
@endsection
