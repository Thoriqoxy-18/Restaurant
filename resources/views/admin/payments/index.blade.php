@extends('admin.layouts.app')
@section('title', 'Pembayaran - Verdant Bistro')

@section('content')
@php
    $payLabel = ['qris' => 'QRIS', 'cash' => 'Tunai'];
    $paymentLabel = ['paid' => 'Lunas', 'unpaid' => 'Menunggu Pembayaran'];
    $paymentClass = ['paid' => 'bg-secondary/20 text-secondary', 'unpaid' => 'bg-[#FDE68A] text-[#92400E]'];
@endphp

<div class="max-w-container-max mx-auto p-margin-mobile md:p-margin-desktop">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
        <div class="md:col-span-12 grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Total Pendapatan (Hari Ini)</p>
                <p class="font-headline-lg text-headline-lg text-primary">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Transaksi Berhasil</p>
                <p class="font-headline-lg text-headline-lg text-primary">{{ $stats['success'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Menunggu Pembayaran</p>
                <p class="font-headline-lg text-headline-lg text-error">{{ $stats['waiting'] }}</p>
            </div>
        </div>

        <div class="md:col-span-12 bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-bright">
                <h3 class="font-headline-md text-headline-md text-on-surface">Transaksi Masuk</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[720px]">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant">
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">No Order</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Waktu</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Meja</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Metode</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Status</th>
                            <th class="p-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-body-md">
                        @forelse ($orders as $order)
                        <tr class="border-b border-outline-variant hover:bg-surface-bright transition-colors {{ $loop->even ? 'bg-background' : '' }}">
                            <td class="p-4 font-medium text-on-surface">#{{ $order->order_number }}</td>
                            <td class="p-4 text-on-surface-variant">{{ $order->created_at->format('H:i') }}</td>
                            <td class="p-4">{{ $order->restaurantTable?->label ?? 'Takeaway' }}</td>
                            <td class="p-4 font-medium">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="p-4"><span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-surface-container text-on-surface"><span class="material-symbols-outlined text-[16px]">{{ $order->payment_method === 'cash' ? 'payments' : 'qr_code' }}</span> {{ $payLabel[$order->payment_method] ?? ucfirst($order->payment_method) }}</span></td>
                            <td class="p-4"><span class="px-2 py-1 rounded {{ $paymentClass[$order->payment_status] ?? 'bg-surface-container-high text-on-surface-variant' }} font-label-sm text-label-sm">{{ $paymentLabel[$order->payment_status] ?? ucfirst($order->payment_status) }}</span></td>
                            <td class="p-4 text-right"><a href="{{ route('kasir.orders.show', $order) }}" class="text-primary hover:text-secondary"><span class="material-symbols-outlined">visibility</span></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="p-8 text-center text-on-surface-variant">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-outline-variant">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
