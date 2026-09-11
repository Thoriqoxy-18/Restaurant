@extends('admin.layouts.app')
@section('title', 'Detail Pesanan - Verdant Bistro')

@section('content')
@php
    $statusLabel = ['pending' => 'Menunggu', 'confirmed' => 'Diterima', 'preparing' => 'Diproses', 'served' => 'Siap Diambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $payLabel = ['qris' => 'QRIS', 'cash' => 'Tunai'];
    $paymentLabel = ['paid' => 'LUNAS', 'unpaid' => 'Menunggu Pembayaran'];
    $optionType = ['variation' => 'Varian', 'topping' => 'Topping', 'sauce' => 'Saus'];
@endphp

<div class="max-w-container-max mx-auto p-margin-mobile md:p-margin-desktop space-y-8" x-data="{ confirmOpen: false, completeOpen: false }">
    <div class="flex items-center gap-4 border-b border-outline-variant pb-6">
        <a href="{{ route('kasir.orders') }}" class="p-2 bg-surface-container-lowest rounded-full hover:bg-surface-container border border-outline-variant transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Detail Pesanan #{{ $order->order_number }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $order->restaurantTable?->label ?? 'Takeaway' }} • {{ $statusLabel[$order->status] ?? $order->status }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6 border-b border-outline-variant pb-4">Daftar Item</h2>
                <div class="space-y-4">
                    @foreach ($order->orderItems as $oi)
                    <div class="flex justify-between items-start p-4 bg-surface rounded-lg border border-outline-variant">
                        <div class="flex gap-4 min-w-0">
                            <div class="w-16 h-16 bg-surface-container rounded flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-3xl">restaurant</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-headline-md text-headline-md text-on-surface text-base">{{ $oi->menuItem->name ?? 'Menu' }}</h3>
                                <ul class="font-body-md text-body-md text-on-surface-variant text-sm mt-1">
                                    @foreach ($oi->options as $opt)
                                    <li>{{ $optionType[$opt->type] ?? $opt->type }}: {{ $opt->name }}</li>
                                    @endforeach
                                    @if ($oi->notes)
                                    <li class="italic">Catatan: {{ $oi->notes }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-headline-md text-headline-md text-on-surface text-base">Rp{{ number_format($oi->price, 0, ',', '.') }}</p>
                            <p class="font-body-md text-body-md text-on-surface-variant text-sm mt-1">Qty: {{ $oi->quantity }} × Rp{{ number_format($oi->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6 border-b border-outline-variant pb-4">Ringkasan Pembayaran</h2>
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between"><span class="font-body-md text-body-md text-on-surface-variant">Subtotal</span><span class="font-body-md text-body-md text-on-surface">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="font-body-md text-body-md text-on-surface-variant">Pajak & Service (10%)</span><span class="font-body-md text-body-md text-on-surface">Rp{{ number_format($order->tax + $order->service_charge, 0, ',', '.') }}</span></div>
                    <div class="pt-3 border-t border-outline-variant flex justify-between"><span class="font-headline-md text-headline-md text-on-surface text-base">Total</span><span class="font-headline-md text-headline-md text-primary text-base">Rp{{ number_format($order->total, 0, ',', '.') }}</span></div>
                </div>
                <div class="bg-surface p-4 rounded-lg border border-outline-variant space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Metode Pembayaran</span>
                        <span class="font-label-sm text-label-sm text-on-surface bg-surface-container-highest px-2 py-1 rounded">{{ $payLabel[$order->payment_method] ?? ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Status</span>
                        <span class="font-label-sm text-label-sm px-2 py-1 rounded font-semibold {{ $order->payment_status === 'paid' ? 'bg-secondary/20 text-secondary' : 'bg-[#FDE68A] text-[#92400E]' }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $order->payment_status === 'paid' ? 'check_circle' : 'schedule' }}</span> {{ $paymentLabel[$order->payment_status] ?? ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    @if ($order->payment_status === 'paid' && $order->paid_at)
                    <div class="flex justify-between items-center">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Dibayar</span>
                        <span class="font-label-sm text-label-sm text-on-surface">{{ $order->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    @if ($order->payment_provider)
                    <div class="flex justify-between items-center">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Aplikasi</span>
                        <span class="font-label-sm text-label-sm text-on-surface">{{ ucfirst($order->payment_provider) }}</span>
                    </div>
                    @endif
                </div>

                @if ($order->payment_status === 'unpaid')
                <button type="button" @click="confirmOpen = true"
                        class="mt-4 w-full bg-secondary text-on-secondary font-label-sm text-label-sm py-3 rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">payments</span>
                    {{ $order->payment_method === 'cash' ? 'Konfirmasi Pembayaran Tunai' : 'Konfirmasi Pembayaran (Simulasi)' }}
                </button>
                @endif
            </div>

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm space-y-3">
                @if ($order->status === 'pending')
                <form method="POST" action="{{ route('kasir.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="w-full bg-secondary text-on-secondary font-label-sm text-label-sm py-3 rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">check_circle</span> Terima Pesanan
                    </button>
                </form>
                @endif
                @if ($order->status === 'confirmed')
                <form method="POST" action="{{ route('kasir.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="preparing">
                    <button type="submit" class="w-full bg-secondary text-on-secondary font-label-sm text-label-sm py-3 rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">local_dining</span> Mulai Diproses
                    </button>
                </form>
                @endif
                @if ($order->status === 'preparing')
                <form method="POST" action="{{ route('kasir.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="served">
                    <button type="submit" class="w-full bg-secondary text-on-secondary font-label-sm text-label-sm py-3 rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">room_service</span> Siap Diambil
                    </button>
                </form>
                @endif
                @if ($order->status === 'served')
                <button type="button" @click="completeOpen = true"
                        class="w-full bg-secondary text-on-secondary font-label-sm text-label-sm py-3 rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span> Selesaikan Pesanan
                </button>
                @endif
                @if (in_array($order->status, ['pending', 'confirmed', 'preparing']))
                <form method="POST" action="{{ route('kasir.orders.status', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="w-full text-error font-label-sm text-label-sm py-3 rounded-lg hover:bg-error-container hover:text-on-error-container transition-colors mt-2">Batalkan Pesanan</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Selesaikan Pesanan --}}
    <div class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm" :class="completeOpen ? 'flex' : 'hidden'">
        <div class="bg-white dark:bg-inverse-surface rounded-xl shadow-xl border border-outline-variant w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-secondary/15 text-secondary flex items-center justify-center shrink-0"><span class="material-symbols-outlined">check_circle</span></div>
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Pesanan selesai?</h3>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">Order #{{ $order->order_number }}</p>
                </div>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6">Pastikan customer sudah menerima pesanannya sebelum menyelesaikan pesanan.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="completeOpen = false" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-sm text-label-sm hover:bg-surface-container transition-colors">Batal</button>
                <form method="POST" action="{{ route('kasir.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="px-5 py-2 bg-secondary text-on-secondary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-opacity-90 transition-colors">Selesaikan Pesanan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Pembayaran --}}
    <div class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm" :class="confirmOpen ? 'flex' : 'hidden'">
        <div class="bg-white dark:bg-inverse-surface rounded-xl shadow-xl border border-outline-variant w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-secondary/15 text-secondary flex items-center justify-center shrink-0"><span class="material-symbols-outlined">payments</span></div>
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Konfirmasi Pembayaran</h3>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">Order #{{ $order->order_number }}</p>
                </div>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-1">
                @if ($order->payment_method === 'cash')
                    Pastikan uang tunai sebesar <strong class="text-on-surface">Rp{{ number_format($order->total, 0, ',', '.') }}</strong> sudah diterima dari customer.
                @else
                    Simulasi: konfirmasi pembayaran <strong class="text-on-surface">QRIS</strong> untuk kebutuhan testing/demo. Sistem tidak memverifikasi pembayaran sungguhan.
                @endif
            </p>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6">Klik "Ya, Pembayaran Diterima" untuk menandai pembayaran sebagai <strong class="text-secondary">LUNAS</strong>.</p>

            <div class="flex justify-end gap-3">
                <button type="button" @click="confirmOpen = false" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-sm text-label-sm hover:bg-surface-container transition-colors">Batal</button>
                <form method="POST" action="{{ route('kasir.payment.confirm', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-5 py-2 bg-secondary text-on-secondary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-opacity-90 transition-colors">Ya, Pembayaran Diterima</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
