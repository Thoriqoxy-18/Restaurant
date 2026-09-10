@extends('admin.layouts.app')
@section('title', 'Notifikasi - Verdant Bistro')

@section('content')
@php
    $statusLabel = ['pending' => 'Baru', 'confirmed' => 'Diterima', 'preparing' => 'Diproses', 'served' => 'Siap Disajikan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusDot = ['pending' => 'bg-error', 'confirmed' => 'bg-tertiary-container', 'preparing' => 'bg-tertiary-container', 'served' => 'bg-primary-fixed-dim', 'completed' => 'bg-secondary', 'cancelled' => 'bg-outline'];
    $payLabel = ['qris' => 'QRIS', 'cash' => 'Tunai'];
@endphp

<div class="max-w-3xl mx-auto p-margin-mobile md:p-margin-desktop">
    <div class="mb-8">
        <h2 class="font-headline-lg text-headline-lg text-on-background">Notifikasi</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Aktivitas pesanan dan pembayaran terbaru.</p>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        @forelse ($notifications as $order)
        <div class="flex items-start gap-4 p-4 border-b border-outline-variant/50 hover:bg-surface-bright transition-colors {{ $loop->even ? 'bg-background' : '' }}">
            <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $statusDot[$order->status] ?? 'bg-outline' }}"></span>
            <div class="flex-1 min-w-0">
                <p class="font-body-md text-body-md text-on-surface">
                    Order <span class="font-semibold">#{{ $order->order_number }}</span>
                    <span class="text-on-surface-variant">· {{ $order->restaurantTable?->label ?? 'Takeaway' }}</span>
                </p>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                    @if ($order->status === 'pending')
                        Pesanan baru masuk dan menunggu diterima.
                    @elseif ($order->status === 'preparing')
                        Pesanan sedang dimasak.
                    @elseif ($order->status === 'served')
                        Pesanan siap disajikan.
                    @elseif ($order->status === 'completed')
                        Pesanan selesai.
                    @elseif ($order->status === 'cancelled')
                        Pesanan dibatalkan.
                    @else
                        Status pesanan diperbarui menjadi {{ $statusLabel[$order->status] ?? $order->status }}.
                    @endif
                    <span class="inline-flex items-center gap-1 ml-2">
                        <span class="material-symbols-outlined text-[14px]">payments</span> {{ $payLabel[$order->payment_method] ?? ucfirst($order->payment_method) }} · {{ $order->payment_status === 'paid' ? 'Berhasil' : 'Menunggu' }}
                    </span>
                </p>
            </div>
            <div class="text-right shrink-0">
                <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }}</p>
                <a href="{{ route('kasir.orders.show', $order) }}" class="text-secondary text-label-sm hover:underline">Lihat</a>
            </div>
        </div>
        @empty
        <p class="p-10 text-center text-on-surface-variant">Belum ada notifikasi.</p>
        @endforelse
    </div>
</div>
@endsection
