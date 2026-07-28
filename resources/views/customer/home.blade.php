@extends('customer.layouts.app')
@section('title', 'Menu - ' . config('app.name'))
@section('content')
{{-- Toast Notification --}}
<div x-show="$store.toast.visible" x-cloak class="fixed top-16 right-3 left-3 z-50 max-w-sm mx-auto sm:right-4 sm:left-auto transition-all duration-300"
     x-transition:enter="transition-all duration-300" x-transition:enter-start="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition-all duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-4 opacity-0">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-lg">check_circle</span></div>
        <p class="text-sm text-gray-700" x-text="$store.toast.message"></p>
    </div>
</div>

{{-- Mobile: Search (sticky) --}}
<div class="sticky top-14 z-30 bg-gray-50/95 backdrop-blur-md px-4 pt-3 pb-3 border-b border-gray-100 lg:border-0 lg:bg-transparent lg:relative lg:top-0 lg:px-0 lg:pt-0 lg:pb-0 lg:border-0">
    <div class="relative max-w-5xl mx-auto">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
        <input type="text" x-model="$store.ui.menuSearch" placeholder="Cari menu favoritmu..." class="w-full h-12 pl-11 pr-4 rounded-2xl border border-gray-200 bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition-all">
    </div>
</div>

{{-- Categories --}}
<div class="px-4 pt-3 pb-2 lg:px-0">
    <div class="flex gap-2 overflow-x-auto hide-scrollbar max-w-5xl mx-auto scroll-smooth snap-x snap-mandatory">
        <template x-for="cat in $store.categories" :key="cat.slug">
            <button @@click="Alpine.store('ui').activeCategory = cat.slug; Alpine.store('ui').filterMenu()"
                    :class="Alpine.store('ui').activeCategory === cat.slug ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-gray-500 border border-gray-200 hover:border-primary/30'"
                    class="px-5 py-2.5 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 shrink-0 snap-start active:scale-95" x-text="cat.name"></button>
        </template>
    </div>
</div>

{{-- Content: Menu + Desktop Cart --}}
<div class="px-4 pb-6 lg:px-0">
    <div class="flex flex-col lg:flex-row gap-6 max-w-5xl mx-auto">
        {{-- Menu Grid --}}
        <div class="flex-1 min-w-0">
            {{-- Skeleton --}}
            <template x-if="!$store.ui.loaded">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="i in 4">
                        <div class="bg-white rounded-2xl overflow-hidden">
                            <div class="aspect-[4/3] bg-gray-100 animate-pulse"></div>
                            <div class="p-4 space-y-2.5"><div class="h-4 bg-gray-100 animate-pulse rounded-full w-3/4"></div><div class="h-3 bg-gray-100 animate-pulse rounded-full w-1/3"></div><div class="flex justify-between pt-1"><div class="h-5 bg-gray-100 animate-pulse rounded-full w-1/3"></div><div class="w-10 h-10 bg-gray-100 animate-pulse rounded-xl"></div></div></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Menu Grid --}}
            <div x-show="$store.ui.loaded" class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="menu-grid">
                @foreach ($menuItems as $item)
                @php
                    $itemOptions = $item->options->map(fn($o) => ['id' => $o->id, 'group_name' => $o->group_name, 'type' => $o->type, 'choices' => $o->choices->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'price_modifier' => (float)$c->price_modifier])]);
                    $badges = [];
                    if ($item->is_signature) $badges[] = ['label' => 'Signature', 'class' => 'bg-amber-500'];
                    elseif ($item->is_bestseller) $badges[] = ['label' => 'Best Seller', 'class' => 'bg-secondary'];
                    $badge = $badges[0] ?? null;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group cursor-pointer hover:shadow-md active:scale-[0.99] transition-all duration-200 menu-card"
                     data-name="{{ strtolower($item->name) }}" data-category="{{ $item->category->slug }}">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-primary/5 to-secondary/5" @@click="$store.ui.openDetail({ id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, rating: '{{ $item->rating }}', image: '{{ $item->image_path ? asset($item->image_path) : '' }}', desc: @js($item->description), badge: @js($badge), _options: @js($itemOptions) })">
                        @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">@endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent pointer-events-none"></div>
                        @if ($badge)<div class="absolute top-2 left-2 {{ $badge['class'] }} text-white text-[10px] font-bold px-2 py-0.5 rounded-lg shadow-sm">{{ $badge['label'] }}</div>@endif
                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-lg px-1.5 py-0.5 text-xs font-semibold text-gray-700 shadow-sm flex items-center gap-0.5"><span class="text-amber-400 text-[11px]">⭐</span><span>{{ $item->rating ?? '-' }}</span></div>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-[15px] font-semibold text-gray-800 line-clamp-2 mb-1">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-400 mb-2">15-20 menit</p>
                        <div class="mt-auto flex items-center justify-between pt-1">
                            <span class="text-lg font-bold text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <button @@click.stop="$store.ui.openDetail({ id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, rating: '{{ $item->rating }}', image: '{{ $item->image_path ? asset($item->image_path) : '' }}', desc: @js($item->description), badge: @js($badge), _options: @js($itemOptions) })"
                                    class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-md hover:bg-primary/90 hover:shadow-lg active:scale-90 transition-all">
                                <span class="material-symbols-outlined text-lg">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Search Empty State --}}
            <div id="search-empty" class="text-center py-20 hidden" x-show="$store.ui.loaded">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4"><span class="material-symbols-outlined text-4xl text-gray-300">search_off</span></div>
                <h3 class="text-base font-semibold text-gray-600 mb-1">Tidak ada menu</h3>
                <p class="text-sm text-gray-400">Coba kata kunci lain atau ubah filter kategori.</p>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL MODAL (Mobile: full screen / Desktop: centered) --}}
<div x-show="$store.ui.detailOpen" x-cloak class="fixed inset-0 z-[100] flex flex-col sm:items-center sm:justify-center sm:p-4"
     x-transition:enter="transition-all duration-300 ease-out" x-transition:leave="transition-all duration-200 ease-in">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @@click="$store.ui.closeDetail()" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-200"></div>
    <div class="relative bg-white w-full sm:max-w-lg sm:rounded-3xl shadow-2xl flex flex-col flex-1 sm:flex-none sm:max-h-[90vh] overflow-hidden"
         x-transition:enter="transition-all duration-300" x-transition:enter-start="translate-y-8 sm:translate-y-0 sm:scale-95 opacity-0" x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
         x-transition:leave="transition-all duration-200" x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100" x-transition:leave-end="translate-y-8 sm:translate-y-0 sm:scale-95 opacity-0">
        {{-- Image --}}
        <div class="shrink-0 relative h-56 sm:h-64 bg-gray-100 overflow-hidden">
            <img :src="$store.ui.detailItem?.image || '{{ asset('assets/images/default/no-image.svg') }}'" :alt="$store.ui.detailItem?.name" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            <button @@click="$store.ui.closeDetail()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/90 backdrop-blur-sm shadow-md flex items-center justify-center text-gray-600 active:scale-90"><span class="material-symbols-outlined text-lg">close</span></button>
            <div class="absolute bottom-4 left-5 right-5 text-white">
                <h2 class="text-xl font-bold drop-shadow-sm" x-text="$store.ui.detailItem?.name"></h2>
                <div class="flex items-center gap-2 mt-1"><span class="text-lg font-bold drop-shadow-sm" x-text="'Rp ' + ($store.ui.detailItem?.price || 0).toLocaleString('id-ID')"></span><span class="text-sm text-white/80">· 15-20 menit</span></div>
            </div>
        </div>
        {{-- Content --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar px-5 py-4 space-y-5">
            <div class="flex items-center gap-3">
                <template x-if="$store.ui.detailItem?.rating"><div class="flex items-center gap-1 text-sm"><span class="text-amber-400">⭐</span><span class="font-medium text-gray-700" x-text="$store.ui.detailItem?.rating"></span></div></template>
                <template x-if="$store.ui.detailItem?.badge"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white" :class="$store.ui.detailItem.badge.class || 'bg-primary'" x-text="$store.ui.detailItem.badge.label"></span></template>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed" x-text="$store.ui.detailItem?.desc || '—'"></p>
            {{-- Dynamic Options --}}
            <template x-for="(og, gi) in $store.ui.currentOptions" :key="og.id">
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3" x-text="og.group_name"></h3>
                    <template x-if="og.type === 'radio'">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="ch in og.choices" :key="ch.id">
                                <button @@click="$store.ui.selectRadio(gi, ch.id)" :class="ch.selected ? 'border-primary bg-primary/5 text-primary ring-2 ring-primary/15' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'" class="px-4 py-2.5 rounded-xl text-sm font-medium border transition-all"><span class="block" x-text="ch.name"></span><span x-show="ch.price_modifier > 0" class="block text-[11px] mt-0.5 text-gray-400" x-text="'+Rp ' + ch.price_modifier.toLocaleString('id-ID')"></span><span x-show="ch.price_modifier == 0" class="block text-[11px] mt-0.5 text-gray-400">Free</span></button>
                            </template>
                        </div>
                    </template>
                    <template x-if="og.type === 'checkbox'">
                        <div class="space-y-1.5">
                            <template x-for="ch in og.choices" :key="ch.id">
                                <label class="flex items-center justify-between px-3.5 py-3 rounded-xl hover:bg-gray-50 cursor-pointer border border-transparent transition-colors">
                                    <div class="flex items-center gap-3"><input type="checkbox" :checked="ch.selected" @@change="$store.ui.toggleCheckbox(ch.id)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30"><span class="text-sm text-gray-700" x-text="ch.name"></span></div>
                                    <span x-show="ch.price_modifier > 0" class="text-xs text-gray-400 font-medium" x-text="'+Rp ' + ch.price_modifier.toLocaleString('id-ID')"></span><span x-show="ch.price_modifier == 0" class="text-xs text-gray-400">Free</span>
                                </label>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            {{-- Notes --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Catatan</h3>
                <textarea x-model="$store.ui.note" class="w-full bg-gray-50 border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-primary/20 resize-none h-20" placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="h-2"></div>
        </div>
        {{-- Footer --}}
        <div class="shrink-0 bg-white border-t border-gray-100 px-5 py-4 space-y-3">
            <template x-if="$store.ui.optionSummary"><p class="text-xs text-gray-500" x-text="$store.ui.optionSummary"></p></template>
            <div class="flex items-center justify-between">
                <div class="flex items-center bg-gray-100 rounded-xl p-0.5">
                    <button @@click="$store.ui.qty = Math.max(1, $store.ui.qty - 1)" class="w-10 h-10 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-600 active:scale-90 text-lg font-semibold">−</button>
                    <span class="w-10 text-center font-bold text-lg text-gray-800" x-text="$store.ui.qty"></span>
                    <button @@click="$store.ui.qty++" class="w-10 h-10 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-600 active:scale-90 text-lg font-semibold">+</button>
                </div>
                <div class="text-right"><p class="text-[11px] text-gray-400">Total</p><span class="text-xl font-bold text-primary" x-text="'Rp ' + $store.ui.liveTotal.toLocaleString('id-ID')"></span></div>
            </div>
            <button @@click="$store.ui.addToCart()" class="w-full h-12 rounded-xl gradient-button text-white text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/25">
                <span x-text="$store.ui.editMode ? 'Simpan' : 'Masukkan ke Keranjang'"></span><span class="material-symbols-outlined text-lg">shopping_basket</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('categories', @json($categories->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name])));
    Alpine.store('ui').loaded = true;
    Alpine.store('ui').filterMenu = function() {
        const q = (Alpine.store('ui').menuSearch || '').toLowerCase().trim();
        const cat = Alpine.store('ui').activeCategory || 'all';
        const grid = document.getElementById('menu-grid');
        if (!grid) return;
        let v = 0;
        grid.querySelectorAll('.menu-card').forEach(c => {
            const n = c.dataset.name || '', cc = c.dataset.category || '';
            const show = (q === '' || n.includes(q)) && (cat === 'all' || cc === cat);
            c.style.display = show ? '' : 'none';
            if (show) v++;
        });
        const e = document.getElementById('search-empty');
        if (e) e.classList.toggle('hidden', v > 0 || q === '');
    };
    Alpine.effect(() => { Alpine.store('ui').menuSearch; Alpine.store('ui').activeCategory; Alpine.store('ui').filterMenu(); });
});
</script>
@endsection
