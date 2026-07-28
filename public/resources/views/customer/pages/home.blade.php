@extends('customer.layouts.app')

@section('title', 'Menu - Verdant Bistro')

@section('content')
{{-- Search Bar --}}
<div id="search-bar" class="mb-6">
    <div class="relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
        <input
            type="text"
            x-model="$store.ui.menuSearch"
            @input="$store.ui.filterMenu()"
            class="w-full h-12 pl-11 pr-4 rounded-full border border-gray-200 bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm"
            placeholder="Search menu..."
        >
    </div>
</div>

{{-- Category Chips --}}
    <section class="mb-6">
        <div class="flex gap-3 overflow-x-auto hide-scrollbar">
            <button
                @click="$store.ui.setCategory('all')"
                :class="$store.ui.activeCategory === 'all' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'"
                class="px-5 py-2.5 rounded-full text-sm font-medium whitespace-nowrap transition-all active:scale-95"
            >All</button>
            @foreach ($categories as $cat)
                <button
                    @click="$store.ui.setCategory('{{ $cat->slug }}')"
                    :class="$store.ui.activeCategory === '{{ $cat->slug }}' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'"
                    class="px-5 py-2.5 rounded-full text-sm font-medium whitespace-nowrap transition-all active:scale-95"
                >{{ $cat->name }}</button>
            @endforeach
        </div>
    </section>

{{-- Menu Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="menu-grid">
    @foreach ($menuItems as $item)
        @include('customer.components.menu-card', ['item' => $item, 'image' => $item->image ? asset($item->image) : asset('assets/images/default/no-image.svg')])
    @endforeach
</div>

{{-- Empty State (search + category) --}}
<div id="search-empty" class="text-center py-16 hidden">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-3xl text-gray-300">search_off</span>
    </div>
    <h3 class="text-base font-semibold text-gray-800 mb-1">Menu tidak ditemukan</h3>
    <p class="text-sm text-gray-400" id="empty-message">Tidak ada menu yang sesuai dengan filter saat ini.</p>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.effect(() => {
        Alpine.store('ui').menuSearch;
        Alpine.store('ui').activeCategory;
        Alpine.store('ui').filterMenu();
    });
    Alpine.store('ui').filterMenu();
});
</script>
@endsection
