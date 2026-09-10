@extends('admin.layouts.admin')
@section('title', 'Manajemen Menu - Verdant Bistro')

@section('content')
<div class="max-w-container-max mx-auto" x-data="{ cat: 'all', q: '' }">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Manajemen Menu</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola daftar hidangan dan minuman Anda.</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" class="bg-primary hover:bg-tertiary-container text-on-primary px-4 py-2 rounded-lg font-body-md text-body-md flex items-center justify-center gap-2 transition-colors">
            <span class="material-symbols-outlined">add</span>
            <span>Tambah Menu</span>
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="relative w-full sm:w-72">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input x-model="q" class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-shadow" placeholder="Cari nama menu..."/>
        </div>
        <div class="flex overflow-x-auto gap-2 hide-scrollbar">
            <button @click="cat = 'all'" :class="cat === 'all' ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant bg-surface text-on-surface-variant hover:bg-surface-container hover:text-on-surface'"
                    class="px-4 py-1.5 rounded-full border font-label-sm text-label-sm whitespace-nowrap transition-colors">Semua</button>
            @foreach ($categories as $c)
            <button @click="cat = '{{ $c->slug }}'" :class="cat === '{{ $c->slug }}' ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant bg-surface text-on-surface-variant hover:bg-surface-container hover:text-on-surface'"
                    class="px-4 py-1.5 rounded-full border font-label-sm text-label-sm whitespace-nowrap transition-colors">{{ $c->name }}</button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($menus as $menu)
        @php
            $badge = null;
            if ($menu->is_signature) $badge = ['text' => 'Signature', 'cls' => 'bg-secondary text-on-secondary'];
            elseif ($menu->is_bestseller) $badge = ['text' => 'Best Seller', 'cls' => 'bg-tertiary-container text-on-tertiary'];
            elseif ($menu->old_price) $badge = ['text' => 'Promo', 'cls' => 'bg-error text-on-error'];
        @endphp
        <div x-show="(cat === 'all' || cat === '{{ $menu->category->slug }}') && (q === '' || '{{ strtolower($menu->name) }}'.includes(q.toLowerCase()))"
             class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="relative h-44 bg-surface-container-high">
                @if ($menu->image_path)
                <img class="w-full h-full object-cover" src="{{ asset($menu->image_path) }}" alt="{{ $menu->name }}">
                @else
                <div class="w-full h-full flex items-center justify-center text-on-surface-variant"><span class="material-symbols-outlined text-5xl">restaurant</span></div>
                @endif
                @if ($badge)
                <div class="absolute top-2 left-2 {{ $badge['cls'] }} px-2 py-1 rounded font-label-sm text-label-sm flex items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined text-[14px]">{{ $menu->is_signature ? 'star' : ($menu->is_bestseller ? 'trending_up' : 'sell') }}</span>
                    <span>{{ $badge['text'] }}</span>
                </div>
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2 gap-2">
                    <div class="min-w-0">
                        <h3 class="font-headline-md text-headline-md text-on-surface line-clamp-1">{{ $menu->name }}</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">{{ $menu->category->name }}</p>
                    </div>
                    <span class="font-headline-md text-headline-md text-primary shrink-0">Rp{{ number_format($menu->price, 0, ',', '.') }}</span>
                </div>
                <div class="mt-auto pt-4 flex items-center justify-between border-t border-outline-variant/30">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $menu->is_available ? 'bg-secondary' : 'bg-error' }}"></div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}</span>
                    </div>
                    <div class="flex gap-1">
                        <a href="{{ route('admin.menu.edit', $menu) }}" class="text-outline hover:text-primary transition-colors p-1" title="Edit"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                        <form method="POST" action="{{ route('admin.menu.destroy', $menu) }}" onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-outline hover:text-error transition-colors p-1" title="Hapus"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-on-surface-variant py-10">Belum ada menu. Tambahkan menu baru.</p>
        @endforelse
    </div>
</div>
@endsection
