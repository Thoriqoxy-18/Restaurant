@extends('admin.layouts.app')
@section('title', 'Menu Items - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Menu Items</h2>
            <p class="text-sm text-gray-500">Manage your restaurant menu.</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="h-10 px-4 rounded-xl bg-primary text-white text-sm font-semibold flex items-center gap-1.5 hover:bg-primary/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-base">add</span> Add Menu
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">{{ session('success') }}</div>
    @endif

    @if ($menus->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <span class="material-symbols-outlined text-4xl text-gray-300">menu_book</span>
            <h3 class="text-base font-semibold text-gray-700 mt-2">No menu items</h3>
            <p class="text-sm text-gray-400 mt-1">Add your first menu item to get started.</p>
            <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90">Add Menu Item</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($menus as $menu)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-video bg-gradient-to-br from-primary/5 to-secondary/5 relative">
                        <img src="{{ $menu->image ? asset($menu->image) : asset('assets/images/default/no-image.svg') }}" alt="{{ $menu->name }}" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute top-2 right-2 flex gap-1">
                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur-sm flex items-center justify-center text-gray-600 hover:text-primary shadow-sm transition-colors">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" onsubmit="return confirm('Delete this menu item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur-sm flex items-center justify-center text-gray-600 hover:text-red-500 shadow-sm transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</h3>
                            <span class="text-sm font-bold text-primary whitespace-nowrap ml-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">{{ $menu->category->name ?? '-' }}</p>
                        <div class="flex items-center gap-2">
                            @if ($menu->is_signature) <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Signature</span> @endif
                            @if ($menu->is_bestseller) <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-secondary/10 text-secondary">Best Seller</span> @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
