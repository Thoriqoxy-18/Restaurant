@extends('admin.layouts.admin')
@section('title', 'Admin Dashboard - Verdant Bistro')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));
    $statusLabel = ['pending' => 'Baru', 'confirmed' => 'Diterima', 'preparing' => 'Diproses', 'served' => 'Siap Disajikan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusClass = ['pending' => 'bg-error-container text-on-error-container', 'confirmed' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant', 'preparing' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant', 'served' => 'bg-primary-fixed text-on-primary-fixed-variant', 'completed' => 'bg-secondary-container text-on-secondary-container', 'cancelled' => 'bg-surface-container-high text-on-surface-variant'];
    $payLabel = ['qris' => 'QRIS', 'cash' => 'Tunai'];
@endphp

<div class="max-w-container-max mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background">{{ $greeting }}, {{ Auth::user()->name }}</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">calendar_today</span>
                {{ now()->translatedFormat('l, d F Y') }} • {{ now()->format('H:i') }} WIB
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.menu.create') }}" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-primary-container transition-colors flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah Menu
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center text-on-primary-fixed"><span class="material-symbols-outlined">receipt_long</span></div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Total Pesanan (Hari Ini)</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $stats['orders'] }}</h3>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-on-primary"><span class="material-symbols-outlined">payments</span></div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Pendapatan Hari Ini</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-surface-variant flex items-center justify-center text-on-surface-variant"><span class="material-symbols-outlined">soup_kitchen</span></div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Pesanan Diproses</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $stats['processing'] }}</h3>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-lg bg-tertiary-fixed-dim flex items-center justify-center text-on-tertiary-fixed-variant"><span class="material-symbols-outlined">fastfood</span></div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Menu Terjual (Hari Ini)</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $stats['sold'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 flex flex-col gap-6">
            <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Penjualan 7 Hari Terakhir</h3>
                </div>
                <div class="h-48 w-full flex items-end gap-2 sm:gap-4 pt-4 border-b border-outline-variant">
                    @foreach ($last7 as $day)
                    <div class="flex-1 flex flex-col items-center group">
                        <div class="w-full rounded-t-sm transition-colors" style="height:{{ $day['amount'] > 0 ? max(8, ($day['amount'] / $max) * 100) : 4 }}%;background-color:#c1ecd4;"></div>
                        <span class="text-xs text-on-surface-variant mt-2 font-label-sm">{{ $day['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="lg:col-span-1">
            <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 h-full flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Menu Terlaris</h3>
                </div>
                <div class="flex-1 flex flex-col gap-4">
                    @forelse ($topMenus as $m)
                    <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-surface-container-low transition-colors border border-transparent hover:border-outline-variant">
                        <div class="w-14 h-14 rounded-md overflow-hidden bg-surface-variant shrink-0">
                            @if ($m->image_path)
                            <img src="{{ asset($m->image_path) }}" alt="" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant"><span class="material-symbols-outlined">restaurant</span></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-label-sm text-label-sm font-semibold text-on-surface truncate">{{ $m->name }}</h4>
                            <p class="text-xs text-on-surface-variant mt-0.5">Rp{{ number_format($m->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-headline-md text-headline-md text-sm text-primary">{{ $m->total }}</span>
                            <p class="text-[10px] text-on-surface-variant">porsi</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-on-surface-variant text-sm">Belum ada penjualan.</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.menu') }}" class="w-full mt-4 py-2 border border-primary text-primary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-primary-fixed-dim hover:text-on-primary-fixed transition-colors text-center">Lihat Menu Lengkap</a>
            </section>
        </div>
    </div>
</div>
@endsection
