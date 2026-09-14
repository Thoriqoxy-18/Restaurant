@extends('admin.layouts.admin')
@section('title', 'Meja & QR - Verdant Bistro')

@section('content')
@php
    $statusLabel = ['available' => 'Tersedia', 'occupied' => 'Digunakan', 'reserved' => 'Menunggu'];
    $statusClass = ['available' => 'bg-secondary/20 text-secondary', 'occupied' => 'bg-error-container text-error', 'reserved' => 'bg-[#FDE68A] text-[#92400E]'];
@endphp

<div class="max-w-container-max mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">
        <div class="md:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <h3 class="font-headline-md text-headline-md mb-2">Ringkasan Area</h3>
            <p class="font-body-md text-body-md text-on-surface-variant">Total {{ $stats['all'] }} meja terdaftar.</p>
            <div class="flex gap-8 mt-6">
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Tersedia</p>
                    <p class="font-display-brand text-display-brand text-secondary">{{ $stats['available'] }}</p>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Digunakan</p>
                    <p class="font-display-brand text-display-brand text-error">{{ $stats['occupied'] }}</p>
                </div>
            </div>
        </div>
        <div class="md:col-span-4 bg-primary text-on-primary rounded-xl p-6 flex flex-col items-center justify-center text-center">
            <button @click="document.getElementById('add-table-form').classList.toggle('hidden')" class="w-full flex flex-col items-center hover:bg-primary-container rounded-xl p-4 transition-colors">
                <span class="material-symbols-outlined text-4xl mb-3">add_circle</span>
                <span class="font-headline-md text-headline-md">Tambah Meja Baru</span>
            </button>
            <form method="POST" action="{{ route('admin.tables.store') }}" id="add-table-form" class="hidden w-full mt-3 space-y-2">
                @csrf
                <input name="name" required placeholder="Nama meja (mis. Meja 11)" aria-label="Nama meja" class="w-full px-3 py-2 rounded-lg bg-on-primary text-primary placeholder:text-primary/60 font-body-md text-body-md border border-on-primary/30"/>
                <input name="capacity" type="number" min="1" value="4" aria-label="Kapasitas meja" class="w-full px-3 py-2 rounded-lg bg-on-primary text-primary font-body-md text-body-md border border-on-primary/30"/>
                <button type="submit" class="w-full py-2 bg-secondary text-on-secondary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-secondary-container hover:text-on-secondary-container transition-colors">Simpan Meja</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($tables as $table)
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 flex justify-between items-center border-b border-surface-variant">
                <h3 class="font-headline-md text-headline-md">{{ $table->label }}</h3>
                <span class="px-2 py-1 rounded {{ $statusClass[$table->status] ?? 'bg-surface-container-high text-on-surface-variant' }} font-label-sm text-label-sm">{{ $statusLabel[$table->status] ?? $table->status }}</span>
            </div>
            <div class="p-6 flex flex-col items-center justify-center flex-1 bg-surface-bright">
                <div class="w-32 h-32 bg-white border border-outline-variant p-2 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset(\App\Support\DemoQrCode::svgFile(route('menu', $table), $table->qr_token)) }}" alt="QR {{ $table->label }}" class="w-full h-full object-contain"/>
                </div>
                <p class="font-label-sm text-label-sm text-on-surface-variant break-all text-center">{{ route('menu', $table) }}</p>
            </div>
            <div class="p-4 border-t border-surface-variant flex gap-2">
                <a href="{{ route('menu', $table) }}" target="_blank" class="flex-1 py-2 border border-secondary text-secondary rounded font-label-sm text-label-sm hover:bg-secondary hover:text-on-secondary transition-colors text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">visibility</span> Lihat
                </a>
                <a href="{{ route('admin.tables.qr', $table) }}" class="flex-1 py-2 border border-outline-variant text-on-surface rounded font-label-sm text-label-sm hover:bg-surface-variant transition-colors text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                </a>
                <a href="{{ route('admin.tables.qr', ['table' => $table, 'inline' => 1]) }}" target="_blank" class="flex-1 py-2 border border-outline-variant text-on-surface rounded font-label-sm text-label-sm hover:bg-surface-variant transition-colors text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">print</span>
                </a>
            </div>
            <div class="px-4 pb-4 pt-0">
                <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja {{ $table->label }}? Seluruh data terkait meja ini akan ikut terhapus.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 border border-error/40 text-error rounded font-label-sm text-label-sm hover:bg-error-container hover:text-on-error-container transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Hapus Meja
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-on-surface-variant py-10">Belum ada meja.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tables->links() }}
    </div>
</div>

@push('styles')
<style>
    .demo-qr svg { width: 100%; height: 100%; display: block; }
</style>
@endpush
@endsection
