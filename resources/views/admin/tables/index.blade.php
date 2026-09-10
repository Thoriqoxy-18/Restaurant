@extends('admin.layouts.app')
@section('title', 'Status Meja - Verdant Bistro')

@section('content')
@php
    $statusLabel = ['available' => 'Tersedia', 'occupied' => 'Digunakan', 'reserved' => 'Menunggu'];
    $statusClass = ['available' => 'bg-secondary/10 text-secondary', 'occupied' => 'bg-error/10 text-error', 'reserved' => 'bg-[#FDE68A] text-[#92400E]'];
@endphp

<div class="max-w-container-max mx-auto p-margin-mobile md:p-margin-desktop" x-data="{ f: 'all' }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-gutter gap-4">
        <h2 class="font-headline-md text-headline-md text-on-background">Status Meja</h2>
        <div class="flex gap-2 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0">
            @foreach (['all' => 'Semua Meja (' . $stats['all'] . ')', 'available' => 'Tersedia (' . $stats['available'] . ')', 'occupied' => 'Digunakan (' . $stats['occupied'] . ')', 'reserved' => 'Menunggu (' . $stats['reserved'] . ')'] as $key => $label)
            <button @click="f = '{{ $key }}'" :class="f === '{{ $key }}' ? 'bg-secondary text-on-primary' : 'border border-outline-variant text-on-surface hover:bg-surface-container'"
                    class="px-4 py-2 rounded-full font-label-sm text-label-sm whitespace-nowrap transition-colors">{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @forelse ($tables as $table)
        @php
            $activeOrder = $table->orders->first(fn ($o) => ! in_array($o->status, ['completed', 'cancelled']));
        @endphp
        <div x-show="f === 'all' || f === '{{ $table->status }}'"
             class="bg-surface-container-lowest border rounded-xl p-4 flex flex-col h-full transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-sm {{ $table->status === 'occupied' ? 'border-error/30' : ($table->status === 'reserved' ? 'border-[#D97706]/30' : 'border-outline-variant') }}">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="font-headline-md text-headline-md text-on-background">{{ $table->label }}</span>
                    <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">{{ $table->capacity }} Kapasitas</div>
                </div>
                <span class="px-2 py-1 rounded font-label-sm text-label-sm {{ $statusClass[$table->status] ?? 'bg-surface-container text-on-surface-variant' }}">{{ $statusLabel[$table->status] ?? $table->status }}</span>
            </div>

            @if ($activeOrder)
            <div class="my-4">
                <div class="flex justify-between text-body-md text-on-surface mb-1">
                    <span>{{ $activeOrder->orderItems->count() }} Item</span>
                    <span class="font-semibold">Rp{{ number_format($activeOrder->total, 0, ',', '.') }}</span>
                </div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">schedule</span> {{ $activeOrder->created_at->diffForHumans() }}
                </div>
            </div>
            @endif

            <div class="mt-auto pt-4 border-t border-outline-variant/50">
                @if ($table->status === 'available')
                <form method="POST" action="{{ route('kasir.tables.status', $table) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="occupied">
                    <button type="submit" class="w-full py-2 bg-surface-container text-on-surface hover:bg-surface-container-high rounded font-label-sm text-label-sm transition-colors">Buka Meja</button>
                </form>
                @elseif ($activeOrder)
                <a href="{{ route('kasir.orders.show', $activeOrder) }}" class="block w-full text-center py-2 bg-secondary text-on-primary rounded font-label-sm text-label-sm transition-colors hover:bg-secondary/90 shadow-sm">
                    {{ $table->status === 'reserved' ? 'Bayar' : 'Lihat Pesanan' }}
                </a>
                @else
                <form method="POST" action="{{ route('kasir.tables.status', $table) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="available">
                    <button type="submit" class="w-full py-2 border border-outline-variant text-on-surface rounded font-label-sm text-label-sm transition-colors hover:bg-surface-container">Tutup Meja</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-on-surface-variant py-10">Tidak ada meja terdaftar.</p>
        @endforelse
    </div>
</div>
@endsection
