@extends('customer.layouts.app')
@section('title', 'QR Meja - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex items-center gap-3 px-4 h-16 bg-surface border-b border-outline-variant/10">
    <div class="w-10 h-10 bg-primary-container/10 rounded-xl flex items-center justify-center">
        <svg class="w-5 h-5 text-primary-container" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 21h-3M17 21v-3"/></svg>
    </div>
    <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">QR Meja</h1>
</header>

<main class="pt-20 pb-10 px-4 max-w-lg mx-auto">
    <p class="text-sm text-gray-500 mb-5 text-center">Pindai QR untuk membuka menu meja tertentu, atau gunakan tombol untuk mencoba tanpa pindai.</p>

    <div class="space-y-5">
        @foreach ($tables as $table)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="font-bold text-gray-800">{{ $table->label }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5 break-all">{{ route('menu', $table) }}</p>
                </div>
                <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">{{ $table->status }}</span>
            </div>

            <div class="mx-auto w-40 h-40 bg-white rounded-xl border-2 border-gray-100 flex items-center justify-center overflow-hidden p-1.5 demo-qr mb-3">
                {!! \App\Support\DemoQrCode::svg(route('menu', $table)) !!}
            </div>

            <a href="{{ route('menu', $table) }}"
               class="tap-btn pointer-events-auto w-full h-11 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                Lihat Menu Meja Ini
            </a>
        </div>
        @endforeach
    </div>
</main>

@push('styles')
<style>
    .demo-qr svg { width: 100%; height: 100%; display: block; }
</style>
@endpush
@endsection
