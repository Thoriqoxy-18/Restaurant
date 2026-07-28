@php
    $detailData = \Illuminate\Support\Js::from([
        'id' => (int) $item['id'],
        'name' => $item['name'],
        'price' => (float) $item['price'],
        'category' => $item->category?->slug ?? 'food',
        'image' => $image,
    ]);
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group cursor-pointer hover:-translate-y-1 hover:shadow-md transition-all duration-300 menu-card" data-name="{{ strtolower($item['name']) }}" data-category="{{ $item->category?->slug ?? '' }}" @click="$store.ui.openDetail({{ $detailData }})">
    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-primary/5 to-secondary/5">
        <img
            src="{{ $image }}"
            alt="{{ $item['name'] }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            loading="lazy"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
        @if ($item['is_signature'])
            <div class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">⭐ Signature</div>
        @elseif ($item['is_best_seller'])
            <div class="absolute top-2 left-2 bg-secondary text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">🔥 Best Seller</div>
        @endif
    </div>
    <div class="p-3 flex flex-col flex-1">
        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 mb-1.5">{{ $item['name'] }}</h3>
        <div class="flex items-center gap-1 mb-2.5">
            <span class="material-symbols-outlined text-xs text-tertiary" style="font-variation-settings: 'FILL' 1;">star</span>
            <span class="text-xs font-medium text-gray-400">{{ $item['rating'] }}</span>
        </div>
        <div class="mt-auto flex items-center justify-between pt-1">
            <span class="text-sm font-bold text-primary">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
            <button class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 hover:bg-primary/90 active:scale-90 transition-all" @click.stop="$store.ui.openDetail({{ $detailData }})">
                <span class="material-symbols-outlined text-base">add</span>
            </button>
        </div>
    </div>
</div>
