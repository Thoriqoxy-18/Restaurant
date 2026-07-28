@extends('customer.layouts.app')

@section('title', $menuItem->name . ' - Verdant Bistro')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto">
    {{-- Image --}}
    <div class="relative aspect-[4/3] bg-gradient-to-br from-primary/5 to-secondary/5 overflow-hidden">
        <img
            src="{{ $menuItem->image ? asset($menuItem->image) : asset('assets/images/default/no-image.svg') }}"
            alt="{{ $menuItem->name }}"
            class="w-full h-full object-cover"
            loading="lazy"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
        <div class="absolute top-4 right-4">
            <a href="{{ route('home') }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm shadow-md text-gray-600 hover:text-gray-800 active:scale-90 transition-all">
                <span class="material-symbols-outlined text-lg">close</span>
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-4 sm:px-6 py-5">
        <div class="flex justify-between items-start gap-4 mb-2">
            <h1 class="text-lg sm:text-xl font-semibold text-gray-800">{{ $menuItem->name }}</h1>
            <span class="text-lg font-bold text-primary whitespace-nowrap">Rp {{ number_format($menuItem->price, 0, ',', '.') }}</span>
        </div>

        <div class="flex items-center gap-1 mb-3">
            <span class="material-symbols-outlined text-sm text-tertiary" style="font-variation-settings: 'FILL' 1;">star</span>
            <span class="text-xs font-medium text-gray-500">{{ $menuItem->rating }}</span>
        </div>

        <p class="text-sm text-gray-500 leading-relaxed mb-6">
            {{ $menuItem->description }}
        </p>

        {{-- Spicy Level --}}
        <div class="py-4 border-t border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Spicy Level</h3>
            <div class="flex gap-2">
                <button class="px-5 py-2 rounded-full text-sm font-medium border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">Low</button>
                <button class="px-5 py-2 rounded-full text-sm font-medium border border-primary bg-primary/5 text-primary transition-colors">Medium</button>
                <button class="px-5 py-2 rounded-full text-sm font-medium border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">High</button>
            </div>
        </div>

        {{-- Toppings --}}
        <div class="py-4 border-t border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Extra Toppings</h3>
            <div class="space-y-2">
                <label class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                        <span class="text-sm text-gray-700">Caramelized Onion</span>
                    </div>
                    <span class="text-xs font-medium text-gray-400">+Rp 5.000</span>
                </label>
                <label class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                        <span class="text-sm text-gray-700">Extra Cheese</span>
                    </div>
                    <span class="text-xs font-medium text-gray-400">+Rp 10.000</span>
                </label>
            </div>
        </div>

        {{-- Note --}}
        <div class="py-4 border-t border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Special Note</h3>
            <textarea class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-primary/20 resize-none h-24" placeholder="Add a note..."></textarea>
        </div>

        {{-- Action --}}
        <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
            <div class="flex items-center bg-gray-100 rounded-full p-0.5">
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-lg">remove</span>
                </button>
                <span class="w-10 text-center font-semibold text-gray-800">1</span>
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-lg">add</span>
                </button>
            </div>
            <button class="flex-1 h-12 rounded-full gradient-button text-white font-semibold text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                Add to Cart
                <span class="material-symbols-outlined text-lg">shopping_basket</span>
            </button>
        </div>
    </div>
</div>
@endsection
