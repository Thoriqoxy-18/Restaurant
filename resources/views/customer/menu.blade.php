@extends('customer.layouts.app')
@section('title', 'Menu - Verdant Bistro')

@section('content')
@php $sevenDaysAgo = now()->subDays(7); @endphp

{{-- Toast Notification --}}
<div x-data x-show="$store.toast.visible" x-cloak class="fixed top-4 right-4 z-[1200] max-w-sm w-full transition-all duration-300"
     x-transition:enter="transition-all duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
     x-transition:leave="transition-all duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center" :class="$store.toast.type === 'success' ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500'">
            <svg x-show="$store.toast.type === 'success'" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <svg x-show="$store.toast.type !== 'success'" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
        </div>
        <p class="text-sm text-gray-700" x-text="$store.toast.message"></p>
    </div>
</div>

<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-4 md:px-16 h-16 bg-surface border-b border-outline-variant/10">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container/10 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-primary-container" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h18M8 11V5m8 6V5M6 11v8h12v-8"/></svg>
        </div>
        <h1 class="font-headline-md text-headline-md font-bold text-primary text-lg">Verdant Bistro</h1>
    </div>
    <div class="px-3 py-1.5 bg-primary-container text-on-primary-container rounded-full font-label-md text-label-md text-xs shadow-sm">{{ $table->label }}</div>
</header>

<main class="pt-20 menu-pb min-h-screen px-4 lg:px-16" x-data="{ search: '', activeCat: 'all', ready: false }" x-init="setTimeout(() => ready = true, 200)">
    <div class="max-w-6xl mx-auto flex flex-col gap-5">
        {{-- Notifikasi status pesanan (live, compact, di bawah header) --}}
        <div id="order-banner" x-data x-show="$store.orderBanner.visible" x-cloak
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
        <div class="flex flex-col md:flex-row gap-6">
        <section class="flex-1 space-y-4">
            {{-- Search --}}
            <div class="relative">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-outline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" class="w-full h-12 pl-11 pr-4 rounded-2xl border border-outline-variant/30 bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition-all" placeholder="Cari menu...">
            </div>

            {{-- Categories --}}
            <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
                <button @click="activeCat = 'all'" :class="activeCat === 'all' ? 'bg-primary text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-primary/30'" class="whitespace-nowrap px-5 py-2 rounded-full text-sm font-medium active:scale-95 transition-all shrink-0">Semua</button>
                @foreach ($categories as $cat)
                <button @click="activeCat = '{{ $cat->slug }}'" :class="activeCat === '{{ $cat->slug }}' ? 'bg-primary text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-primary/30'" class="whitespace-nowrap px-5 py-2 rounded-full text-sm font-medium active:scale-95 transition-all shrink-0">{{ $cat->name }}</button>
                @endforeach
            </div>

            {{-- Skeleton --}}
            <template x-if="!ready">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <template x-for="i in 6">
                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100">
                            <div class="aspect-[4/3] bg-gray-100 animate-pulse"></div>
                            <div class="p-3 space-y-2.5"><div class="h-4 bg-gray-100 animate-pulse rounded-full w-3/4"></div><div class="h-3 bg-gray-100 animate-pulse rounded-full w-1/3"></div></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Menu Grid --}}
            <div x-show="ready" class="grid grid-cols-2 md:grid-cols-3 gap-3" id="menu-grid">
                @foreach ($menuItems as $item)
                @php
                    $isNew = $item->created_at && $item->created_at->gt($sevenDaysAgo);
                    // Determine badges: max 2, priority: Signature > Best Seller > Promo > New
                    $badges = [];
                    if ($item->is_signature) $badges[] = ['text' => 'Signature', 'class' => 'bg-violet-600 text-white'];
                    if ($item->is_bestseller) $badges[] = ['text' => 'Best Seller', 'class' => 'bg-orange-500 text-white shadow-sm shadow-orange-500/30'];
                    if ($item->old_price && count($badges) < 2) $badges[] = ['text' => 'Promo', 'class' => 'bg-red-500 text-white'];
                    if ($isNew && count($badges) < 2) $badges[] = ['text' => 'New', 'class' => 'bg-blue-500 text-white'];
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group cursor-pointer lg:hover:shadow-md lg:hover:-translate-y-0.5 transition-all duration-200 menu-card"
                     @click="if (!$event.target.closest('button')) $dispatch('open-detail', { id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, oldPrice: {{ $item->old_price ?? 'null' }}, image: '{{ $item->image_path ? asset($item->image_path) : asset('assets/images/default/no-image.svg') }}', desc: @js($item->description), prepTime: {{ $item->prep_time_minutes }}, vegan: {{ $item->is_vegan ? 'true' : 'false' }}, hasSpice: {{ $item->has_spice_level ? 'true' : 'false' }}, rating: '{{ $item->rating }}', variations: @js($item->variations), toppings: @js($item->toppings), sauces: @js($item->sauces) })"
                     x-show="activeCat === 'all' || activeCat === '{{ $item->category->slug }}'">
                    <div class="relative aspect-[4/3] bg-gray-50 overflow-hidden">
                        <img src="{{ $item->image_path ? asset($item->image_path) : asset('assets/images/default/no-image.svg') }}" alt="{{ $item->name }}" class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.src='{{ asset('assets/images/default/no-image.svg') }}'">
                        {{-- Badges --}}
                        @foreach ($badges as $i => $b)
                        <span class="absolute top-3 left-3 {{ $b['class'] }} px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-sm animate-badge-in" style="animation-delay:{{ $i * 0.08 }}s; z-index:{{ 2 - $i }}">{{ $b['text'] }}</span>
                        @endforeach
                        {{-- Add button / Qty control --}}
                        <template x-if="$store.cart.items.some(i => i.id === {{ $item->id }})">
                            <div class="absolute bottom-3 right-3 z-10 flex items-center bg-white rounded-full shadow-md" @click.stop>
                                <button type="button" @click.stop="$store.cart.decrementFirst({{ $item->id }})" aria-label="Kurangi jumlah" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-primary active:scale-90 transition-transform rounded-l-full">
                                    <svg class="w-5 h-5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                                </button>
                                <span class="w-9 text-center text-sm font-bold text-gray-800" x-text="$store.cart.countById({{ $item->id }})"></span>
                                <button type="button" @click.stop="$dispatch('open-detail', { id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, oldPrice: {{ $item->old_price ?? 'null' }}, image: '{{ $item->image_path ? asset($item->image_path) : asset('assets/images/default/no-image.svg') }}', desc: @js($item->description), prepTime: {{ $item->prep_time_minutes }}, vegan: {{ $item->is_vegan ? 'true' : 'false' }}, hasSpice: {{ $item->has_spice_level ? 'true' : 'false' }}, rating: '{{ $item->rating }}', variations: @js($item->variations), toppings: @js($item->toppings), sauces: @js($item->sauces) })" aria-label="Tambah konfigurasi baru" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-primary active:scale-90 transition-transform rounded-r-full">
                                    <svg class="w-5 h-5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="!$store.cart.items.some(i => i.id === {{ $item->id }})">
                            <button type="button" class="tap-btn pointer-events-auto absolute bottom-3 right-3 z-10 w-11 h-11 bg-primary rounded-full text-white flex items-center justify-center shadow-lg active:scale-90 transition-transform animate-pop"
                                    aria-label="Tambah {{ $item->name }}"
                                    @click.stop="$dispatch('open-detail', { id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, oldPrice: {{ $item->old_price ?? 'null' }}, image: '{{ $item->image_path ? asset($item->image_path) : asset('assets/images/default/no-image.svg') }}', desc: @js($item->description), prepTime: {{ $item->prep_time_minutes }}, vegan: {{ $item->is_vegan ? 'true' : 'false' }}, hasSpice: {{ $item->has_spice_level ? 'true' : 'false' }}, rating: '{{ $item->rating }}', variations: @js($item->variations), toppings: @js($item->toppings), sauces: @js($item->sauces) })">
                                <svg class="w-6 h-6 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            </button>
                        </template>
                    </div>
                    <div class="p-3 flex flex-col flex-1 gap-0.5">
                        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-tight">{{ $item->name }}</h3>
                        <div class="flex items-center justify-between mt-auto pt-2">
                            <span class="text-base font-bold text-primary">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                            @if ($item->rating)
                            <div class="flex items-center gap-1 text-amber-500">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <span class="text-xs font-medium">{{ $item->rating }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Empty State --}}
            <div id="search-empty" style="display:none">
                <div class="text-center py-16">
                    <svg class="w-14 h-14 text-gray-300 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <p class="text-sm text-gray-500 mt-3">Menu tidak ditemukan</p>
                </div>
            </div>

            <script>
            document.addEventListener('alpine:init', () => {
                Alpine.effect(() => {
                    const grid = document.getElementById('menu-grid');
                    const empty = document.getElementById('search-empty');
                    const ready = document.querySelector('[x-data]')?.__x?.$data?.ready;
                    if (!grid || !empty || !ready) return;
                    requestAnimationFrame(() => {
                        const visible = [...grid.children].filter(el => el.style.display !== 'none');
                        empty.style.display = visible.length === 0 ? '' : 'none';
                    });
                });
            });
            </script>
        </section>

        {{-- CART PANEL --}}
        <aside class="hidden lg:block w-80 shrink-0 sticky top-24 self-start bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-800">Pesanan</h2>
                <span class="bg-primary text-white px-3 py-1 rounded-full text-[11px] font-bold" x-text="$store.cart.itemsCount + ' ITEM'"></span>
            </div>
            <div x-show="$store.cart.items.length === 0" class="flex flex-col items-center justify-center py-10 text-gray-400">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL'1;">shopping_basket</span>
                <p class="text-sm mt-2">Keranjang kosong</p>
            </div>
            <div x-show="$store.cart.items.length > 0" class="space-y-3 max-h-[35vh] overflow-y-auto pr-1 custom-scrollbar">
                <template x-for="(item, idx) in $store.cart.items" :key="idx">
                    <div class="flex gap-3 pb-3 border-b border-gray-50 last:border-0">
                        <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-50 cursor-pointer" @click="$dispatch('open-detail', { id: item.id, name: item.name, price: item.price, image: item.image, _editItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })">
                            <img :src="item.image || '{{ asset('assets/images/default/no-image.svg') }}'" alt="" class="w-full h-full object-cover" onerror="this.src='{{ asset('assets/images/default/no-image.svg') }}'">
                        </div>
                        <div class="flex-1 min-w-0 cursor-pointer" @click="$dispatch('open-detail', { id: item.id, name: item.name, price: item.price, image: item.image, _editItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })">
                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-gray-400" x-show="item.variationName" x-text="item.variationName"></p>
                            <template x-for="oname in (item.toppingNames || [])" :key="oname">
                                <p class="text-xs text-gray-400">+ <span x-text="oname"></span></p>
                            </template>
                            <template x-for="sname in (item.sauceNames || [])" :key="sname">
                                <p class="text-xs text-gray-400">Saus: <span x-text="sname"></span></p>
                            </template>
                            <p class="text-xs text-gray-400 italic" x-show="item.notes" x-text="'Catatan: ' + item.notes"></p>
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-sm font-bold text-primary">Rp<span x-text="$store.cart.getItemTotal(item).toLocaleString('id-ID', {maximumFractionDigits:0})"></span></span>
                                <div class="flex items-center gap-1.5">
                                    <div class="flex items-center bg-gray-100 rounded-full">
                                        <button @click.stop="$store.cart.updateQty(item.id, item.variationId, -1)" class="w-7 h-7 flex items-center justify-center text-primary hover:bg-gray-200 rounded-l-full transition-colors text-sm">−</button>
                                        <span class="w-7 text-center text-xs font-bold text-gray-800" x-text="item.quantity"></span>
                                        <button @click.stop="$dispatch('open-detail', { _reorderItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })" class="w-7 h-7 flex items-center justify-center text-primary hover:bg-gray-200 rounded-r-full transition-colors text-sm" aria-label="Beli lagi dengan konfigurasi">+</button>
                                    </div>
                                    <button @click.stop="$store.cart.remove(item.id, item.variationId)" class="w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-400 transition-colors" aria-label="Hapus item"><span class="material-symbols-outlined text-sm">delete</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="$store.cart.items.length > 0" class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                <div class="flex justify-between text-xs text-gray-500"><span>Subtotal</span><span x-text="'Rp' + $store.cart.subtotal.toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
                <div class="flex justify-between text-xs text-gray-500"><span>Pajak & Service</span><span x-text="'Rp' + ($store.cart.tax + $store.cart.serviceCharge).toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
                <div class="flex justify-between text-sm font-bold text-gray-800 pt-2 border-t border-gray-100"><span>Total</span><span class="text-primary" x-text="'Rp' + $store.cart.total.toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
                <form method="POST" action="{{ route('order.payment', $table) }}" @submit.prevent="
                    const cartData = JSON.stringify($store.cart.toJSON());
                    $el.querySelector('[name=cart]').value = cartData;
                    $el.submit();
                ">
                    @csrf
                    <input type="hidden" name="cart">
                    <button type="submit" class="w-full mt-2 h-11 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">Pesan Sekarang</button>
                </form>
            </div>
        </aside>
        </div>
    </div>
</main>

{{-- ITEM DETAIL MODAL --}}
<div x-data="{ open: false, item: null, qty: 1, selVar: null, selTop: [], selSauce: [], note: '', basePrice: 0, editingKey: null, reordering: false }"
     x-on:open-detail.window="
        const d = $event.detail;
        if (d._editItem) {
            const ei = d._editItem;
            open = true; item = { id: ei.id, name: ei.name, price: ei.price, image: ei.image, desc: d.desc || '', prepTime: d.prepTime, vegan: d.vegan, hasSpice: d.hasSpice, rating: d.rating, badge: d.badge, variations: d.variations || [], toppings: d.toppings || [], sauces: d.sauces || [] };
            qty = ei.quantity; selVar = ei.variationId || null; selTop = [...(ei.toppingIds || [])]; selSauce = [...(ei.sauceIds || [])]; note = ei.notes || ''; basePrice = ei.price || 0;
            editingKey = $store.cart._key(ei);
            reordering = false;
            lockBody();
            $store.cart.modalOpen = true;
            __overlayOpen('detail');
        } else if (d._reorderItem) {
            const ri = d._reorderItem;
            open = true; item = { id: ri.id, name: ri.name, price: ri.price, image: ri.image, desc: ri.desc || '', prepTime: ri.prepTime, vegan: ri.vegan, hasSpice: ri.hasSpice, rating: ri.rating, badge: ri.badge, variations: d.variations || [], toppings: d.toppings || [], sauces: d.sauces || [] };
            qty = ri.quantity || 1; selVar = ri.variationId || null; selTop = [...(ri.toppingIds || [])]; selSauce = [...(ri.sauceIds || [])]; note = ri.notes || ''; basePrice = ri.price || 0;
            editingKey = null;
            reordering = true;
            lockBody();
            $store.cart.modalOpen = true;
            __overlayOpen('detail');
        } else {
            open = true; item = d; qty = 1; selVar = null; selTop = []; selSauce = []; note = ''; basePrice = d.price || 0;
            editingKey = null;
            reordering = false;
            lockBody();
            $store.cart.modalOpen = true;
            __overlayOpen('detail');
        }"
     x-on:overlay-back.window="if ($event.detail === 'detail') { open = false; unlockBody(); $store.cart.modalOpen = false; }"
     x-show="open" x-cloak class="fixed inset-0 z-[1100] flex items-end md:items-center justify-center p-0 md:p-4"
     style="background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);"
     x-transition:enter="transition-all duration-300" x-transition:leave="transition-all duration-200">
    <div class="bg-white w-full md:max-w-lg max-h-[92vh] md:max-h-[85vh] rounded-t-2xl md:rounded-2xl overflow-hidden flex flex-col shadow-xl"
         x-transition:enter="transition-all duration-300" x-transition:enter-start="translate-y-full md:translate-y-0 md:scale-95" x-transition:enter-end="translate-y-0 md:scale-100"
         x-transition:leave="transition-all duration-200" x-transition:leave-start="translate-y-0 md:scale-100" x-transition:leave-end="translate-y-full md:translate-y-0 md:scale-95">
        <div class="absolute top-3 left-3 z-20">
            <button type="button" aria-label="Tutup" class="tap-btn pointer-events-auto bg-white/90 w-11 h-11 rounded-full flex items-center justify-center shadow-sm active:scale-90 transition-transform" @click="open = false; __overlayClose('detail'); $store.cart.modalOpen = false; unlockBody()">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
        <div class="overflow-y-auto no-scrollbar flex-grow pb-36">
            <div class="relative w-full aspect-video bg-gray-100">
                <img :src="item?.image || '{{ asset('assets/images/default/no-image.svg') }}'" :alt="item?.name" class="w-full h-full object-cover" onerror="this.src='{{ asset('assets/images/default/no-image.svg') }}'">
            </div>
            <div class="px-5 pt-4 space-y-4">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800" x-text="item?.name"></h2>
                        <p class="text-xs text-gray-400 mt-0.5" x-show="item?.prepTime">
                            <svg class="w-3.5 h-3.5 inline-block align-middle mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            <span x-text="item?.prepTime + ' menit'"></span>
                        </p>
                    </div>
                    <template x-if="item?.vegan"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0">🌱 Vegan</span></template>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed" x-show="item?.desc" x-text="item?.desc"></p>

                <div x-show="item?.variations?.length">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pilih Varian</h3>
                    <div class="space-y-2">
                        <template x-for="v in (item?.variations || [])" :key="v.id">
                            <div class="relative">
                                <button type="button" @click="selVar = (selVar === v.id) ? null : v.id"
                                        class="flex justify-between items-center p-3 rounded-xl border transition-all cursor-pointer w-full text-left"
                                        :class="selVar === v.id ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-gray-200 bg-white hover:border-gray-300'">
                                    <span class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors" :class="selVar === v.id ? 'border-primary' : 'border-gray-300'">
                                            <span class="w-2.5 h-2.5 rounded-full transition-all" :class="selVar === v.id ? 'bg-primary scale-100' : 'scale-0'"></span>
                                        </span>
                                        <span class="text-sm text-gray-700" x-text="v.name"></span>
                                    </span>
                                    <span class="text-sm font-bold text-primary" x-text="'+Rp' + parseFloat(v.extra_price).toLocaleString('id-ID', {maximumFractionDigits:0})"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="item?.toppings?.length">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Topping Tambahan</h3>
                    <div class="space-y-1.5">
                        <template x-for="t in (item?.toppings || [])" :key="t.id">
                            <div class="relative">
                                <div @click="selTop = selTop.includes(t.id) ? selTop.filter(id => id !== t.id) : [...selTop, t.id]"
                                     class="flex justify-between items-center p-3 rounded-xl border transition-all cursor-pointer"
                                     :class="selTop.includes(t.id) ? 'border-primary bg-primary/5' : 'border-gray-200 bg-white hover:border-gray-300'">
                                    <span class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded flex items-center justify-center transition-all shrink-0 border-2" :class="selTop.includes(t.id) ? 'bg-primary border-primary' : 'border-gray-300 bg-white'">
                                            <svg x-show="selTop.includes(t.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700" x-text="t.name"></span>
                                    </span>
                                    <span class="text-xs font-medium text-gray-500" x-text="'+Rp' + parseFloat(t.extra_price).toLocaleString('id-ID', {maximumFractionDigits:0})"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="item?.sauces?.length">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Saus (Maks 2)</h3>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="s in (item?.sauces || [])" :key="s.id">
                            <span>
                                <button type="button" @click="if(selSauce.includes(s.id)){selSauce = selSauce.filter(id => id !== s.id)}else{if(selSauce.length < 2){selSauce = [...selSauce, s.id]}}"
                                        class="px-4 py-1.5 rounded-full border text-sm transition-all"
                                        :class="selSauce.includes(s.id) ? 'bg-primary text-white border-primary' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                        x-text="s.name"></button>
                            </span>
                        </template>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Catatan</h3>
                    <textarea x-model="note" class="w-full bg-gray-50 border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-primary/20 resize-none h-20" placeholder="Ada permintaan khusus?"></textarea>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full z-10 bg-white border-t border-gray-100 px-5 py-4 flex items-center gap-3" style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 16px)">
            <div class="flex items-center bg-gray-100 rounded-xl p-0.5">
                <button type="button" aria-label="Kurangi jumlah" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-600 active:scale-90 transition-all" @click="qty = Math.max(1, qty - 1)">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                </button>
                <span class="w-9 text-center font-bold text-gray-800" x-text="qty"></span>
                <button type="button" aria-label="Tambah jumlah" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-600 active:scale-90 transition-all" @click="qty++">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>
            <button class="flex-1 h-12 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20 tap-btn pointer-events-auto"
                    @click="
                        if (editingKey) { $store.cart.items = $store.cart.items.filter(i => $store.cart._key(i) !== editingKey); editingKey = null; }
                        let vPrice = parseFloat(item?.variations?.find(v => v.id == selVar)?.extra_price || 0);
                        let tPrices = (selTop || []).map(id => parseFloat(item?.toppings?.find(t => t.id == id)?.extra_price || 0));
                        let sPrices = (selSauce || []).map(id => parseFloat(item?.sauces?.find(s => s.id == id)?.extra_price || 0));
                        $store.cart.add({ id: item.id, name: item.name, price: basePrice, image: item.image, quantity: qty, notes: note,
                            variationId: selVar, variationName: item?.variations?.find(v => v.id == selVar)?.name || null, _variationPrice: vPrice,
                            toppingIds: selTop, toppingNames: (selTop || []).map(id => item?.toppings?.find(t => t.id == id)?.name || ''),
                            _toppingPrices: tPrices,
                            sauceIds: selSauce, sauceNames: (selSauce || []).map(id => item?.sauces?.find(s => s.id == id)?.name || ''),
                            _saucePrices: sPrices,
                            _variations: item?.variations || [], _toppings: item?.toppings || [], _sauces: item?.sauces || [] });
                        open = false; __overlayClose('detail'); $store.cart.modalOpen = false; unlockBody();">
                <span x-text="editingKey ? 'Simpan Perubahan' : (reordering ? 'Tambahkan ke Pesanan' : 'Tambah')"></span>
                <span class="mx-1 opacity-30">|</span>
                <span x-text="'Rp' + (() => { let v = parseFloat(item?.variations?.find(v => v.id == selVar)?.extra_price || 0); let t = (selTop || []).reduce((s, id) => s + parseFloat(item?.toppings?.find(x => x.id == id)?.extra_price || 0), 0); let s = (selSauce || []).reduce((s, id) => s + parseFloat(item?.sauces?.find(x => x.id == id)?.extra_price || 0), 0); return Math.round((basePrice + v + t + s) * qty).toLocaleString('id-ID'); })()"></span>
            </button>
        </div>
    </div>
</div>

{{-- FLOATING ACTIVE ORDER INDICATOR (menu customer) --}}
@php
    $ao = $activeOrders->first();
    $aoCount = $activeOrders->count();
    $aoItems = $ao ? $ao->orderItems->sum('quantity') : 0;
@endphp
@if ($ao)
<div x-data="orderIndicator({{ $ao->id }}, '{{ $ao->status }}', '{{ $ao->payment_status }}', {{ $aoItems }}, {{ $aoCount }}, '{{ route('order.status', [$table, $ao]) }}', '{{ route('order.poll', [$table, $ao]) }}', '{{ $ao->order_number }}')"
     x-show="visible && !$store.cart.cartOpen" x-cloak
     class="fixed left-0 right-0 z-[8000] px-4"
     :style="'bottom:' + bottomStyle"
     x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="translate-y-6 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition-all duration-200 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-6 opacity-0">
    <a :href="statusUrl"
       class="tap-btn pointer-events-auto w-full flex items-center gap-3 bg-white rounded-2xl shadow-lg border border-gray-100 px-4 py-3 active:scale-[0.99] transition-transform">
        <span class="text-lg shrink-0">🍽️</span>
        <span class="flex-1 min-w-0">
            <span class="block text-sm font-bold text-gray-800 truncate"><span x-text="title"></span> <span class="text-gray-400 font-normal">· <span x-text="itemCount"></span> Item</span></span>
            <span class="block text-xs text-gray-500 mt-0.5" x-show="paymentLine" x-cloak>💰 <span x-text="paymentLine"></span></span>
        </span>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap" :class="meta.cls" x-text="meta.dot + ' ' + meta.label"></span>
        <span class="text-gray-400 shrink-0">›</span>
    </a>
</div>
@endif

{{-- MOBILE FLOATING BOTTOM CART (GoFood style) --}}
<div x-data x-show="$store.cart.items.length > 0 && !$store.cart.modalOpen" x-cloak class="lg:hidden fixed bottom-0 left-0 right-0 z-[900]"
     x-transition:enter="transition-all duration-300 ease-out"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition-all duration-200 ease-in"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0">
    <div class="bg-gray-900 rounded-t-2xl shadow-[0_-8px_30px_rgba(0,0,0,0.25)] px-4 pt-3 flex items-center justify-between gap-3"
         style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 12px);">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="bg-primary text-white w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0" x-text="$store.cart.count"></span>
            <div class="min-w-0">
                <p class="text-[11px] text-gray-400 leading-tight" x-text="$store.cart.count + ' Item'"></p>
                <p class="text-sm font-bold text-white truncate" x-text="'Rp' + $store.cart.subtotal.toLocaleString('id-ID', {maximumFractionDigits:0})"></p>
            </div>
        </div>
        <button type="button" @click="$dispatch('open-sheet')"
                class="tap-btn pointer-events-auto flex items-center gap-1 bg-primary text-white text-sm font-semibold rounded-xl h-11 px-4 shrink-0 active:scale-[0.97] transition-transform">Lihat Pesanan</button>
    </div>
</div>

{{-- MOBILE CART BOTTOM SHEET (auto-height, max 85vh, scrollable list) --}}
<div x-data="{
        openSheet() {
            __overlayOpen('cart');
            $store.cart.cartOpen = true;
            lockBody();
        },
        close() {
            __overlayClose('cart');
            $store.cart.cartOpen = false;
            unlockBody();
        }
     }"
     x-show="$store.cart.cartOpen" x-cloak
     x-on:open-sheet.window="openSheet()"
     x-on:overlay-back.window="if ($event.detail === 'cart') close()"
     class="fixed inset-0 z-[1000] lg:hidden"
     x-transition:enter="transition-opacity duration-250 ease-out" x-transition:leave="transition-opacity duration-200 ease-in">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"></div>
    <div class="absolute bottom-0 left-0 right-0 max-h-[85vh] bg-white rounded-t-3xl shadow-2xl flex flex-col overflow-hidden"
         x-transition:enter="transition-transform duration-300 ease-out" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-transform duration-250 ease-in" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
        <div class="shrink-0 flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 bg-gray-200 rounded-full"></div></div>
        <div class="shrink-0 px-5 pb-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Pesanan</h2>
            <div class="flex items-center gap-1">
                <span class="bg-primary text-white px-3 py-0.5 rounded-full text-[11px] font-bold" x-text="$store.cart.itemsCount + ' ITEM'"></span>
                <button type="button" aria-label="Tutup keranjang" @click="close()" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-gray-400 active:text-gray-600 transition-colors rounded-full">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar px-5 py-3 space-y-4 min-h-0 overscroll-contain">
            <template x-if="$store.cart.items.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <p class="text-sm mt-3">Keranjang kosong</p>
                </div>
            </template>
            <template x-for="(item, idx) in $store.cart.items" :key="idx">
                <div class="flex gap-3 pb-4 border-b border-gray-100 last:border-0"
                     x-transition:enter="transition-all duration-200 ease-out" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-50 cursor-pointer" @click="$dispatch('open-detail', { id: item.id, name: item.name, price: item.price, image: item.image, _editItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })">
                        <img :src="item.image || '{{ asset('assets/images/default/no-image.svg') }}'" alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0 cursor-pointer" @click="$dispatch('open-detail', { id: item.id, name: item.name, price: item.price, image: item.image, _editItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })">
                        <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></p>
                        <p class="text-xs text-gray-500" x-show="item.variationName" x-text="item.variationName"></p>
                        <template x-for="oname in (item.toppingNames || [])" :key="oname">
                            <p class="text-xs text-gray-400">+ <span x-text="oname"></span></p>
                        </template>
                        <template x-for="sname in (item.sauceNames || [])" :key="sname">
                            <p class="text-xs text-gray-400">Saus: <span x-text="sname"></span></p>
                        </template>
                        <p class="text-xs text-gray-400 italic" x-show="item.notes" x-text="'Catatan: ' + item.notes"></p>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-sm font-bold text-primary">Rp<span x-text="$store.cart.getItemTotal(item).toLocaleString('id-ID', {maximumFractionDigits:0})"></span></span>
                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center bg-gray-100 rounded-full">
                                    <button type="button" aria-label="Kurangi jumlah" @click.stop="$store.cart.updateQty(item.id, item.variationId, -1)" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-primary active:scale-90 transition-transform rounded-l-full">
                                        <svg class="w-4.5 h-4.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-gray-800" x-text="item.quantity"></span>
                                    <button type="button" aria-label="Beli lagi dengan konfigurasi" @click.stop="$dispatch('open-detail', { _reorderItem: item, variations: item._variations || [], toppings: item._toppings || [], sauces: item._sauces || [] })" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-primary active:scale-90 transition-transform rounded-r-full">
                                        <svg class="w-4.5 h-4.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                </div>
                                <button type="button" aria-label="Hapus item" @click.stop="$store.cart.remove(item.id, item.variationId)" class="tap-btn pointer-events-auto w-11 h-11 flex items-center justify-center text-gray-300 active:text-red-400 transition-colors">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="shrink-0 px-5 pt-3 border-t border-gray-100 space-y-2 bg-white" style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 12px)">
            <div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span x-text="'Rp' + $store.cart.subtotal.toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
            <div class="flex justify-between text-sm text-gray-500"><span>Pajak & Service</span><span x-text="'Rp' + ($store.cart.tax + $store.cart.serviceCharge).toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
            <div class="flex justify-between text-base font-bold text-gray-800 pt-2 border-t border-gray-100"><span>Total</span><span class="text-primary" x-text="'Rp' + $store.cart.total.toLocaleString('id-ID', {maximumFractionDigits:0})"></span></div>
            <form method="POST" action="{{ route('order.payment', $table) }}" @submit.prevent="
                const cartData = JSON.stringify($store.cart.toJSON());
                $el.querySelector('[name=cart]').value = cartData;
                $el.submit();
            ">
                @csrf
                <input type="hidden" name="cart">
                <button type="submit" class="w-full h-12 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20 tap-btn pointer-events-auto">Pesan Sekarang</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes badgeIn { from { opacity: 0; transform: scale(0.85); } to { opacity: 1; transform: scale(1); } }
.animate-badge-in { animation: badgeIn 0.25s ease-out both; }

@keyframes popIn { from { opacity: 0; transform: scale(0.6); } to { opacity: 1; transform: scale(1); } }
.animate-pop { animation: popIn 0.15s ease-out both; }

.sheet-transition { transition: transform 300ms cubic-bezier(0.32, 0.72, 0, 1); }

.menu-pb { padding-bottom: calc(140px + env(safe-area-inset-bottom, 0px)); }
@media (min-width: 1024px) { .menu-pb { padding-bottom: 2rem; } }
</style>
@endpush
