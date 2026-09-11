@extends('admin.layouts.app')
@section('title', 'Beranda Kasir - Verdant Bistro')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));
    $statusLabel = ['pending' => 'Baru', 'confirmed' => 'Diterima', 'preparing' => 'Diproses', 'served' => 'Siap Diambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusClass = ['pending' => 'bg-secondary/10 text-secondary', 'confirmed' => 'bg-tertiary-container/10 text-tertiary-container', 'preparing' => 'bg-tertiary-container/10 text-tertiary-container', 'served' => 'bg-primary-fixed text-on-primary-fixed', 'completed' => 'bg-surface-container-high text-on-surface-variant', 'cancelled' => 'bg-error/10 text-error'];
@endphp

<div class="max-w-container-max mx-auto p-margin-mobile md:p-margin-desktop">
    <div class="mb-8">
        <h2 class="font-headline-lg text-headline-lg text-primary mb-2">{{ $greeting }}, {{ Auth::user()->name }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Berikut ringkasan aktivitas restoran saat ini.</p>
    </div>

    <div class="grid grid-cols-12 gap-6 mb-8">
        <div class="col-span-12 grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 md:p-6 flex flex-col justify-between">
                <span class="material-symbols-outlined fill text-secondary mb-3">receipt_long</span>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Pesanan Baru</p>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface" id="new-orders-count">{{ $stats['new'] }}</h3>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 md:p-6 flex flex-col justify-between">
                <span class="material-symbols-outlined fill text-tertiary-container mb-3">skillet</span>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Sedang Diproses</p>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['processing'] }}</h3>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 md:p-6 flex flex-col justify-between">
                <span class="material-symbols-outlined fill text-secondary-container mb-3">check_circle</span>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">Siap Disajikan</p>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['ready'] }}</h3>
                </div>
            </div>
            <div class="bg-primary-container border border-primary-container rounded-xl p-5 md:p-6 flex flex-col justify-between text-on-primary-container">
                <span class="material-symbols-outlined fill mb-3">payments</span>
                <div>
                    <p class="font-label-sm text-label-sm opacity-80 mb-1">Pendapatan Hari Ini</p>
                    <h3 class="font-headline-lg text-headline-lg text-on-primary">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8 space-y-6">
            {{-- Pesanan Baru --}}
            <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 md:p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Pesanan Baru</h3>
                        <span id="new-order-indicator" class="hidden font-label-sm text-label-sm text-secondary bg-secondary/10 px-2 py-0.5 rounded-full">Pesanan baru masuk</span>
                    </div>
                    <a href="{{ route('kasir.orders') }}" class="font-label-sm text-label-sm text-secondary hover:underline">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="new-orders-grid">
                    @foreach ($newOrders as $order)
                    <div class="border border-outline-variant rounded-lg p-4 bg-surface-bright flex flex-col" data-order-id="{{ $order->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant block">#{{ $order->order_number }}</span>
                                <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $order->restaurantTable?->label ?? 'Takeaway' }}</span>
                            </div>
                            <span class="bg-secondary/10 text-secondary font-label-sm text-label-sm px-2 py-1 rounded">Baru</span>
                        </div>
                        <div class="font-body-md text-body-md text-on-surface-variant mb-4">
                            @foreach ($order->orderItems->take(3) as $oi)
                                {{ $oi->quantity }}x {{ $oi->menuItem->name ?? 'Menu' }}<br/>
                            @endforeach
                        </div>
                        <div class="mt-auto">
                            <form method="POST" action="{{ route('kasir.orders.status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="w-full bg-secondary text-white font-body-md text-body-md py-2 rounded-lg hover:bg-secondary/90 transition-colors">Terima Pesanan</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p id="new-orders-empty" class="font-body-md text-body-md text-on-surface-variant py-6 text-center {{ $newOrders->isEmpty() ? '' : 'hidden' }}">Belum ada pesanan baru.</p>
            </section>

            {{-- Sedang Diproses --}}
            <section class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Sedang Diproses</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[560px]">
                        <thead class="bg-surface-container-low font-label-sm text-label-sm text-on-surface-variant">
                            <tr>
                                <th class="p-4 font-semibold border-b border-outline-variant">ID Pesanan</th>
                                <th class="p-4 font-semibold border-b border-outline-variant">Meja/Tipe</th>
                                <th class="p-4 font-semibold border-b border-outline-variant">Waktu Pesan</th>
                                <th class="p-4 font-semibold border-b border-outline-variant">Status Dapur</th>
                                <th class="p-4 font-semibold border-b border-outline-variant text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md text-on-surface">
                            @forelse ($processing as $order)
                            <tr class="hover:bg-surface-container-lowest transition-colors border-b border-outline-variant bg-surface-bright">
                                <td class="p-4">#{{ $order->order_number }}</td>
                                <td class="p-4">{{ $order->restaurantTable?->label ?? 'Takeaway' }}</td>
                                <td class="p-4">{{ $order->created_at->format('H:i') }}</td>
                                <td class="p-4"><span class="bg-tertiary-container/10 text-tertiary-container font-label-sm text-label-sm px-2 py-1 rounded">{{ $statusLabel[$order->status] ?? $order->status }}</span></td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('kasir.orders.show', $order) }}" class="text-secondary hover:underline font-label-sm text-label-sm">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-6 text-center text-on-surface-variant">Tidak ada pesanan diproses.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="col-span-12 lg:col-span-4">
            <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm h-full">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6">Aktivitas Terbaru</h3>
                <div class="relative border-l-2 border-outline-variant ml-3 space-y-6">
                    @forelse ($activity as $order)
                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-secondary rounded-full -left-[7px] top-1.5 border-2 border-white"></div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">{{ $order->created_at->format('H:i') }}</p>
                        <p class="font-body-md text-body-md text-on-surface">Order <span class="font-semibold">#{{ $order->order_number }}</span> {{ $statusLabel[$order->status] ?? $order->status }}.</p>
                    </div>
                    @empty
                    <p class="font-body-md text-body-md text-on-surface-variant pl-6">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        var grid = document.getElementById('new-orders-grid');
        var empty = document.getElementById('new-orders-empty');
        var countEl = document.getElementById('new-orders-count');
        var indicator = document.getElementById('new-order-indicator');
        if (!grid || !countEl) return;
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var ids = new Set([].map.call(grid.querySelectorAll('[data-order-id]'), function(c) { return Number(c.getAttribute('data-order-id')); }));
        var hideTimer = null;

        function flash(card) {
            card.classList.add('ring-2', 'ring-secondary');
            setTimeout(function() { card.classList.remove('ring-2', 'ring-secondary'); }, 2500);
        }

        function showIndicator() {
            indicator.classList.remove('hidden');
            if (hideTimer) clearTimeout(hideTimer);
            hideTimer = setTimeout(function() { indicator.classList.add('hidden'); }, 4000);
        }

        function esc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function cardHtml(o) {
            return '<div class="flex justify-between items-start mb-3">'
                + '<div><span class="font-label-sm text-label-sm text-on-surface-variant block">#' + esc(o.order_number) + '</span>'
                + '<span class="font-body-md text-body-md text-on-surface font-semibold">' + esc(o.table) + '</span></div>'
                + '<span class="bg-secondary/10 text-secondary font-label-sm text-label-sm px-2 py-1 rounded">Baru</span></div>'
                + '<div class="font-body-md text-body-md text-on-surface-variant mb-4">' + o.items_html + '</div>'
                + '<div class="mt-auto"><form method="POST" action="' + esc(o.url) + '">'
                + '<input type="hidden" name="_token" value="' + csrf + '">'
                + '<input type="hidden" name="_method" value="PATCH">'
                + '<input type="hidden" name="status" value="confirmed">'
                + '<button type="submit" class="w-full bg-secondary text-white font-body-md text-body-md py-2 rounded-lg hover:bg-secondary/90 transition-colors">Terima Pesanan</button>'
                + '</form></div>';
        }

        function refresh() {
            fetch('{{ route('kasir.dashboard.orders-data') }}', { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var fetchedIds = new Set(d.orders.map(function(o) { return o.id; }));
                    var added = false;
                    d.orders.forEach(function(o) {
                        if (!ids.has(o.id)) {
                            ids.add(o.id);
                            var div = document.createElement('div');
                            div.className = 'border border-outline-variant rounded-lg p-4 bg-surface-bright flex flex-col';
                            div.setAttribute('data-order-id', o.id);
                            div.innerHTML = cardHtml(o);
                            grid.insertBefore(div, grid.firstChild);
                            flash(div);
                            added = true;
                        }
                    });
                    [].forEach.call(grid.querySelectorAll('[data-order-id]'), function(card) {
                        var id = Number(card.getAttribute('data-order-id'));
                        if (!fetchedIds.has(id)) { ids.delete(id); card.remove(); }
                    });
                    countEl.textContent = d.count;
                    if (empty) empty.classList.toggle('hidden', ids.size > 0);
                    if (added) showIndicator();
                })
                .catch(function() {});
        }

        setInterval(refresh, 4000);
    })();
</script>
@endpush
