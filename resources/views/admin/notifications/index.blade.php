@extends('admin.layouts.app')
@section('title', 'Notifikasi - Verdant Bistro')

@section('content')
@php
    $typeDot = ['new_order' => 'bg-tertiary-container', 'payment' => 'bg-secondary'];
@endphp

<div class="max-w-3xl mx-auto p-margin-mobile md:p-margin-desktop">
    <div class="mb-8">
        <h2 class="font-headline-lg text-headline-lg text-on-background">Notifikasi</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Aktivitas pesanan dan pembayaran terbaru.</p>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        @forelse ($notifications as $notification)
        <div class="flex items-start gap-4 p-4 border-b border-outline-variant/50 hover:bg-surface-bright transition-colors {{ $loop->even ? 'bg-background' : '' }}">
            <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $typeDot[$notification->type] ?? 'bg-outline' }}"></span>
            <div class="flex-1 min-w-0">
                <p class="font-body-md text-body-md text-on-surface">{{ $notification->title }}</p>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5 break-words">{{ $notification->message }}</p>
            </div>
            <div class="text-right shrink-0">
                <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $notification->created_at->format('H:i') }}</p>
                @if ($notification->order)
                <a href="{{ route('kasir.orders.show', $notification->order) }}" class="text-secondary text-label-sm hover:underline">Lihat</a>
                @endif
            </div>
        </div>
        @empty
        <p class="p-10 text-center text-on-surface-variant">Belum ada notifikasi.</p>
        @endforelse
    </div>
</div>
@endsection