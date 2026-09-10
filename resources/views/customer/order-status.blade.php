@extends('customer.layouts.app')
@section('title', 'Order Status - Verdant Bistro')

@section('content')
<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-4 md:px-16 h-16 bg-surface border-b border-outline-variant/20">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-2xl">restaurant_menu</span>
        <h1 class="font-headline-md text-headline-md font-bold text-primary text-base md:text-xl">Verdant Bistro</h1>
    </div>
    <div class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-md text-label-md text-xs">TABLE {{ $order->restaurantTable->code ?? $table->code }}</div>
</header>

<main class="pt-20 pb-28 px-4 md:px-16 max-w-2xl mx-auto">
    {{-- Notifikasi status pesanan (live, compact, di bawah header) --}}
    <div id="order-banner" x-data x-show="$store.orderBanner.visible" x-cloak class="mb-4"
         x-transition:enter="transition-all duration-300 ease-out"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition-all duration-200 ease-in"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2">
        <div class="relative flex items-center gap-3 rounded-2xl border bg-white px-4 py-3 shadow-sm overflow-hidden" :class="$store.orderBanner.cfg.box">
            <span class="absolute left-0 top-0 bottom-0 w-1.5" :class="$store.orderBanner.cfg.bar"></span>
            <span class="text-xl leading-none shrink-0" x-text="$store.orderBanner.cfg.icon"></span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold leading-tight" :class="$store.orderBanner.cfg.titleCls" x-text="$store.orderBanner.cfg.title"></p>
                <p class="text-xs mt-0.5 leading-snug text-gray-500" x-text="$store.orderBanner.cfg.desc"></p>
            </div>
            <button type="button" @click="$store.orderBanner.visible = false" aria-label="Tutup" class="shrink-0 -m-1 p-1 text-gray-400 hover:text-gray-600 transition-colors rounded-full">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </div>
    {{-- Pesanan Selesai / Sesi Berakhir --}}
    @if($order->status === 'completed')
    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 mb-6">
        <div class="w-9 h-9 bg-gray-500 text-white rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-gray-700">Sesi pesanan telah berakhir.</p>
            <p class="text-xs text-gray-500">Untuk memesan lagi, silakan scan ulang QR meja untuk memulai sesi baru.</p>
        </div>
    </div>
    @endif

    {{-- Pembayaran Berhasil (QRIS DEMO / paid) --}}
    @if(request('placed') && $order->payment_status === 'paid')
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-4 py-3 mb-6">
        <div class="w-9 h-9 bg-green-500 text-white rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-green-700">Pembayaran Berhasil</p>
            <p class="text-xs text-green-600">Pesanan berhasil dibuat · Status: Menunggu Diproses</p>
        </div>
    </div>
    @endif

    {{-- Success Confirmation --}}
    <section class="flex flex-col items-center text-center mb-8">
        <div class="relative mb-5">
            <div class="bg-primary-container/20 rounded-full blur-xl w-24 h-24 absolute -top-2 left-1/2 -translate-x-1/2 animate-pulse"></div>
            <div class="relative bg-primary-container text-white w-20 h-20 rounded-full flex items-center justify-center shadow-lg animate-float">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL'1;">check_circle</span>
            </div>
        </div>
        <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-1">Pesanan Diterima!</h2>
        <p class="text-body-md text-body-md text-on-surface-variant">Kami sedang menyiapkan pesananmu.</p>
    </section>

    {{-- Order Info Cards --}}
    <div class="flex gap-3 mb-8 overflow-x-auto no-scrollbar -mx-4 px-4">
        <div class="bg-surface-container px-5 py-3 rounded-xl border border-outline-variant/20 shrink-0 min-w-[140px]">
            <span class="block text-label-sm font-label-sm text-on-surface-variant uppercase tracking-tighter text-xs">No. Order</span>
            <span class="font-headline-sm text-headline-sm text-on-surface text-sm">#{{ $order->order_number }}</span>
        </div>
        <div class="bg-surface-container px-5 py-3 rounded-xl border border-outline-variant/20 shrink-0 min-w-[140px]">
            <span class="block text-label-sm font-label-sm text-on-surface-variant uppercase tracking-tighter text-xs">Estimasi</span>
            <span class="font-headline-sm text-headline-sm text-primary text-sm">{{ $order->orderItems->max('menuItem.prep_time_minutes') ?? 15 }} menit</span>
        </div>
        <div class="bg-surface-container px-5 py-3 rounded-xl border border-outline-variant/20 shrink-0 min-w-[140px]">
            <span class="block text-label-sm font-label-sm text-on-surface-variant uppercase tracking-tighter text-xs">Meja</span>
            <span class="font-headline-sm text-headline-sm text-on-surface text-sm">{{ $order->restaurantTable->code ?? $table->code }}</span>
        </div>
    </div>

    {{-- Progress Tracker --}}
    @php
        $steps = ['pending' => 0, 'confirmed' => 1, 'preparing' => 1, 'served' => 2, 'completed' => 3, 'cancelled' => -1];
        $currentStep = $steps[$order->status] ?? 0;
        $isCancelled = $order->status === 'cancelled';
        $stepLabels = ['Diterima', 'Diproses', 'Siap Diambil', 'Selesai'];
        $stepIcons = ['check', 'check', 'restaurant', 'room_service'];
    @endphp
    <section class="bg-surface-container-lowest rounded-2xl p-5 md:p-6 shadow-sm border border-outline-variant/20 mb-8">
        <div id="progress-tracker">
        <div class="relative pt-8 pb-4">
            <div class="absolute top-[38px] left-[12%] right-[12%] h-1 bg-secondary-container rounded-full overflow-hidden">
                @if ($isCancelled)
                    <div class="h-full bg-red-400 w-full"></div>
                @else
                    <div class="h-full bg-primary-container transition-all duration-1000 ease-out animate-progress" style="width: {{ ($currentStep / 3) * 100 }}%"></div>
                @endif
            </div>
            <div class="relative flex justify-between">
                @foreach ($stepLabels as $i => $label)
                <div class="flex flex-col items-center" style="width:25%">
                    @if ($isCancelled)
                        <div class="w-9 h-9 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-2 z-10 shadow-sm">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </div>
                        <span class="font-label-md text-label-md text-red-500 text-xs text-center">{{ $label }}</span>
                    @elseif ($i < $currentStep)
                        <div class="w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center mb-2 z-10 shadow-sm">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL'1;">{{ $stepIcons[$i] }}</span>
                        </div>
                        <span class="font-label-md text-label-md text-on-surface text-xs text-center">{{ $label }}</span>
                    @elseif ($i == $currentStep)
                        <div class="relative w-9 h-9 flex items-center justify-center mb-2 z-10">
                            <div class="absolute inset-0 bg-primary-container/20 rounded-full animate-pulse-ring"></div>
                            <div class="w-9 h-9 rounded-full bg-white border-2 border-primary-container text-primary-container flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-lg" style="animation: spin 3s linear infinite;">{{ $stepIcons[$i] }}</span>
                            </div>
                        </div>
                        <span class="font-label-md text-label-md text-primary font-bold text-xs text-center">{{ $label }}</span>
                    @else
                        <div class="w-9 h-9 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-2 z-10">
                            <span class="material-symbols-outlined text-sm">{{ $stepIcons[$i] }}</span>
                        </div>
                        <span class="font-label-md text-label-md text-on-surface-variant text-xs text-center">{{ $label }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        @if ($isCancelled)
        <div class="mt-4 pt-4 border-t border-outline-variant/20 text-center">
            <p class="text-sm text-red-500 font-medium">Pesanan dibatalkan</p>
        </div>
        @else
        <div class="mt-4 pt-4 border-t border-outline-variant/20 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-on-surface-variant">
                <span class="material-symbols-outlined text-lg">schedule</span>
                <span>Estimasi: <strong class="text-on-surface">{{ $order->orderItems->max('menuItem.prep_time_minutes') ?? 15 }} menit</strong> <span class="text-on-surface-variant">· Selesai ± {{ $order->created_at->copy()->addMinutes($order->orderItems->max('menuItem.prep_time_minutes') ?? 15)->format('H:i') }}</span></span>
            </div>
            <div class="text-right text-on-surface-variant">
                <span class="text-xs">Dipesan</span>
                <p class="font-semibold text-on-surface text-sm">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}</p>
            </div>
        </div>
        @endif
        </div>
    </section>

    {{-- Order Items --}}
    <section class="bg-surface-container-lowest rounded-2xl p-5 md:p-6 shadow-sm border border-outline-variant/20 mb-8">
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-5 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-primary text-lg">list_alt</span>
            Detail Pesanan
            <span class="ml-auto text-xs text-on-surface-variant font-normal">{{ $order->orderItems->count() }} item</span>
        </h3>
        <div class="space-y-3">
            @foreach ($order->orderItems as $orderItem)
            <div class="flex items-center gap-3 py-3 border-b border-secondary-container/30 last:border-0">
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-surface-container flex-shrink-0 shadow-sm">
                    @if ($orderItem->menuItem && $orderItem->menuItem->image_path)
                    <img src="{{ asset($orderItem->menuItem->image_path) }}" alt="" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-outline"><span class="material-symbols-outlined text-lg">restaurant</span></div>
                    @endif
                </div>
                <div class="flex-grow min-w-0">
                    <h4 class="font-body-md text-body-md font-semibold text-on-surface truncate">{{ $orderItem->menuItem->name ?? 'Menu' }}</h4>
                    @foreach ($orderItem->options as $opt)
                    <p class="font-label-sm text-label-sm text-on-surface-variant truncate">+ {{ $opt->name }}</p>
                    @endforeach
                    @if ($orderItem->notes)
                    <p class="font-label-sm text-label-sm text-on-surface-variant italic truncate">Catatan: {{ $orderItem->notes }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <p class="font-body-md text-body-md font-bold text-on-surface">{{ $orderItem->quantity }}x</p>
                    <p class="font-label-md text-label-md text-primary">Rp{{ number_format($orderItem->price, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 pt-5 border-t-2 border-dashed border-secondary-container/50 space-y-1.5">
            <div class="flex justify-between text-sm text-on-surface-variant"><span>Subtotal</span><span class="text-on-surface">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm text-on-surface-variant"><span>Pajak & Service</span><span class="text-on-surface">Rp{{ number_format($order->tax + $order->service_charge, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-bold text-base pt-2 border-t border-outline-variant/20 mt-2"><span>Total</span><span class="text-primary">Rp{{ number_format($order->total, 0, ',', '.') }}</span></div>
        </div>
    </section>

    {{-- Payment Info --}}
    <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-outline-variant/20 mb-8">
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-4 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-primary text-lg">payments</span>
            Pembayaran
        </h3>
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-on-surface-variant">Metode</span>
            <span class="text-sm font-semibold text-on-surface">{{ match($order->payment_method) { 'qris' => 'QRIS', 'cash' => 'Tunai', default => ucfirst($order->payment_method ?? 'Tunai') } }}</span>
        </div>
        @if($order->payment_provider)
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-on-surface-variant">Aplikasi</span>
            <span class="text-sm font-semibold text-on-surface">{{ match($order->payment_provider) { 'dana' => 'DANA', 'gopay' => 'GoPay', 'ovo' => 'OVO', 'shopeepay' => 'ShopeePay', 'mobilebanking' => 'Mobile Banking', default => ucfirst($order->payment_provider) } }}</span>
        </div>
        @endif
        <div class="flex items-center justify-between">
            <span class="text-sm text-on-surface-variant">Status</span>
            @php
                $paymentLabel = $order->payment_status === 'paid' ? 'Pembayaran Diterima' : 'Menunggu Pembayaran';
                $paymentColor = $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700';
            @endphp
            <span id="payment-status-badge" class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentColor }}">{{ $paymentLabel }}</span>
        </div>
    </section>

    {{-- CTA --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('menu', $table) }}" class="flex-1 py-4 rounded-2xl bg-primary text-white font-label-md text-label-md shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali ke Menu
        </a>
        <button class="flex-1 py-4 rounded-2xl bg-secondary-container text-on-secondary-container font-label-md text-label-md hover:bg-secondary-container/80 active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">help_outline</span>
            Butuh Bantuan?
        </button>
    </div>
</main>

@push('styles')
<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script>
@if(request('placed'))
// Clear cart from localStorage after successful order
try { localStorage.removeItem('vb_cart'); } catch(e) {}
@endif
(function() {
    var STEPS = ['Diterima', 'Diproses', 'Siap Diambil', 'Selesai'];
    var ICONS = ['check', 'check', 'restaurant', 'room_service'];
    var STEP_MAP = { pending: 0, confirmed: 1, preparing: 1, served: 2, completed: 3, cancelled: -1 };
    var CONFIG = {
        prepTime: {{ $order->orderItems->max('menuItem.prep_time_minutes') ?? 15 }},
        orderedAt: '{{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}',
        doneAt: '{{ $order->created_at->copy()->addMinutes($order->orderItems->max('menuItem.prep_time_minutes') ?? 15)->format('H:i') }}'
    };
    var currentStatus = '{{ $order->status }}';
    var currentPayment = '{{ $order->payment_status }}';

    function stepHTML(icon, type) {
        if (type === 'done') {
            return '<div class="w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center mb-2 z-10 shadow-sm"><span class="material-symbols-outlined text-sm" style="font-variation-settings:\'FILL\'1;">' + icon + '</span></div>';
        }
        if (type === 'current') {
            return '<div class="relative w-9 h-9 flex items-center justify-center mb-2 z-10"><div class="absolute inset-0 bg-primary-container/20 rounded-full animate-pulse-ring"></div><div class="w-9 h-9 rounded-full bg-white border-2 border-primary-container text-primary-container flex items-center justify-center shadow-lg"><span class="material-symbols-outlined text-lg" style="animation:spin 3s linear infinite;">' + icon + '</span></div></div>';
        }
        if (type === 'cancelled') {
            return '<div class="w-9 h-9 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-2 z-10 shadow-sm"><span class="material-symbols-outlined text-sm">close</span></div>';
        }
        return '<div class="w-9 h-9 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-2 z-10"><span class="material-symbols-outlined text-sm">' + icon + '</span></div>';
    }

    function renderTracker(status) {
        var box = document.getElementById('progress-tracker');
        if (!box) return;
        var cancelled = status === 'cancelled';
        var step = STEP_MAP[status] !== undefined ? STEP_MAP[status] : 0;
        var width = cancelled ? 100 : Math.max(0, step) / 3 * 100;
        var fillStyle = cancelled ? 'background:#f87171;width:100%' : 'width:' + width + '%';
        var line = '<div class="absolute top-[38px] left-[12%] right-[12%] h-1 bg-secondary-container rounded-full overflow-hidden"><div class="h-full bg-primary-container transition-all duration-700 ease-out" style="' + fillStyle + '"></div></div>';
        var html = '<div class="relative pt-8 pb-4">' + line + '<div class="relative flex justify-between">';
        STEPS.forEach(function(label, i) {
            var node, lbl;
            if (cancelled) { node = stepHTML(ICONS[i], 'cancelled'); lbl = 'text-red-500'; }
            else if (i < step) { node = stepHTML(ICONS[i], 'done'); lbl = 'text-on-surface'; }
            else if (i === step) { node = stepHTML(ICONS[i], 'current'); lbl = 'text-primary font-bold'; }
            else { node = stepHTML(ICONS[i], 'todo'); lbl = 'text-on-surface-variant'; }
            html += '<div class="flex flex-col items-center" style="width:25%">' + node + '<span class="font-label-md text-label-md ' + lbl + ' text-xs text-center">' + label + '</span></div>';
        });
        html += '</div></div>';
        if (cancelled) {
            html += '<div class="mt-4 pt-4 border-t border-outline-variant/20 text-center"><p class="text-sm text-red-500 font-medium">Pesanan dibatalkan</p></div>';
        } else {
            html += '<div class="mt-4 pt-4 border-t border-outline-variant/20 flex items-center justify-between text-sm">'
                 + '<div class="flex items-center gap-2 text-on-surface-variant"><span class="material-symbols-outlined text-lg">schedule</span><span>Estimasi: <strong class="text-on-surface">' + CONFIG.prepTime + ' menit</strong> <span class="text-on-surface-variant">· Selesai ± ' + CONFIG.doneAt + '</span></span></div>'
                 + '<div class="text-right text-on-surface-variant"><span class="text-xs">Dipesan</span><p class="font-semibold text-on-surface text-sm">' + CONFIG.orderedAt + '</p></div></div>';
        }
        box.innerHTML = html;
    }

    renderTracker(currentStatus);

    setInterval(function() {
        fetch('{{ route('order.poll', [$table, $order]) }}')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.status && d.status !== currentStatus) {
                    currentStatus = d.status;
                    renderTracker(currentStatus);
                    Alpine.store('orderBanner').show(currentStatus);
                }
                if (d.payment_status && d.payment_status !== currentPayment) {
                    currentPayment = d.payment_status;
                    if (d.payment_status === 'paid') {
                        var badge = document.getElementById('payment-status-badge');
                        if (badge) {
                            badge.textContent = 'Pembayaran Diterima';
                            badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                        }
                        Alpine.store('orderBanner').showCustom('✅', 'Pembayaran Diterima', 'Pembayaran untuk order #{{ $order->order_number }} sudah dikonfirmasi oleh kasir.', { bar: 'bg-green-500', box: 'bg-white border-green-200', titleCls: 'text-green-700' });
                    }
                }
            })
            .catch(function() {});
    }, 4000);
})();
</script>
@endpush
@endsection
