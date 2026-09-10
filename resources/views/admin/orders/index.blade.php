@extends('admin.layouts.app')
@section('title', 'Manajemen Pesanan - Verdant Bistro')

@section('content')
@php
    $statusLabel = ['pending' => 'Baru', 'confirmed' => 'Diterima', 'preparing' => 'Diproses', 'served' => 'Siap Diambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusClass = ['pending' => 'bg-error-container text-on-error-container border border-error/20', 'confirmed' => 'bg-tertiary-container/10 text-tertiary-container', 'preparing' => 'bg-tertiary-container/10 text-tertiary-container', 'served' => 'bg-primary-fixed text-on-primary-fixed border border-primary-fixed-dim/50', 'completed' => 'bg-surface-container-high text-on-surface-variant', 'cancelled' => 'bg-surface-container-high text-on-surface-variant'];
    $payLabel = ['qris' => 'QRIS', 'cash' => 'Tunai'];
@endphp

<div class="max-w-container-max mx-auto p-margin-mobile md:p-margin-desktop flex flex-col gap-6" x-data="{ q: '', f: 'all' }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-background">Manajemen Pesanan</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Pantau dan kelola semua pesanan aktif restoran.</p>
        </div>
        <div class="relative w-full md:w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input x-model="q" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all outline-none" placeholder="Cari nomor pesanan atau meja..." type="text"/>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 pb-2">
        @foreach ([
            'all' => 'Semua', 'pending' => 'Baru', 'proses' => 'Diproses', 'served' => 'Siap Disajikan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'
        ] as $key => $label)
        <button @click="f = '{{ $key }}'" :class="f === '{{ $key }}' ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-lowest text-on-surface-variant border-outline-variant hover:border-secondary hover:text-secondary'"
                class="px-4 py-2 rounded-full border font-label-sm text-label-sm transition-colors">{{ $label }}</button>
        @endforeach
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden flex-1">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-surface-container-low border-b border-outline-variant sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">No Pesanan</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">Meja</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold w-64">Item</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">Total</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">Pembayaran</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">Status</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold">Waktu</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-md text-body-md">
                    @forelse ($orders as $order)
                    @php
                        $group = $order->status === 'pending' ? 'pending' : ($order->status === 'confirmed' || $order->status === 'preparing' ? 'proses' : $order->status);
                        $items = $order->orderItems->map(fn ($i) => $i->quantity . 'x ' . ($i->menuItem->name ?? 'Menu'))->take(2)->implode(', ');
                    @endphp
                    <tr x-show="(f === 'all' || f === '{{ $group }}') && (q === '' || '#{{ $order->order_number }}'.toLowerCase().includes(q.toLowerCase()) || '{{ $order->restaurantTable?->label ?? 'Takeaway' }}'.toLowerCase().includes(q.toLowerCase()))"
                        class="hover:bg-surface-bright transition-colors bg-surface-container-lowest">
                        <td class="px-4 py-3 font-semibold text-secondary">#{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->restaurantTable?->label ?? 'Takeaway' }}</td>
                        <td class="px-4 py-3 text-on-surface-variant line-clamp-1">{{ $items }}</td>
                        <td class="px-4 py-3 font-medium">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"><span class="flex items-center gap-1 text-on-surface-variant"><span class="material-symbols-outlined text-[16px]">{{ $order->payment_method === 'cash' ? 'payments' : 'credit_card' }}</span> {{ $payLabel[$order->payment_method] ?? ucfirst($order->payment_method) }}</span></td>
                        <td class="px-4 py-3">
                            @if ($order->payment_status === 'paid')
                            <span class="inline-flex items-center px-2 py-1 rounded bg-secondary/20 text-secondary font-label-sm text-label-sm whitespace-nowrap">LUNAS</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded bg-[#FDE68A] text-[#92400E] font-label-sm text-label-sm whitespace-nowrap">Menunggu Pembayaran</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-1 rounded-full {{ $statusClass[$order->status] ?? 'bg-surface-container-high text-on-surface-variant' }} font-label-sm text-label-sm whitespace-nowrap">{{ $statusLabel[$order->status] ?? $order->status }}</span></td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                            @if ($order->payment_status === 'unpaid')
                            <a href="{{ route('kasir.orders.show', $order) }}" class="p-1.5 text-secondary hover:bg-secondary/10 rounded-lg transition-colors inline-flex" title="Konfirmasi Pembayaran">
                                <span class="material-symbols-outlined">payments</span>
                            </a>
                            @endif
                            <a href="{{ route('kasir.orders.show', $order) }}" class="p-1.5 text-secondary hover:bg-secondary/10 rounded-lg transition-colors inline-flex" title="Detail">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-8 text-center text-on-surface-variant">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
