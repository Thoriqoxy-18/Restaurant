@extends('admin.layouts.app')
@section('title', ($menu ?? false) ? 'Edit Menu - ' . config('app.name') : 'Create Menu - ' . config('app.name'))
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-1">
            <span class="material-symbols-outlined text-base">arrow_back</span> Back to Menu Items
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ isset($menu) ? 'Edit Menu Item' : 'Create Menu Item' }}</h2>
    </div>

    <form method="POST" action="{{ isset($menu) ? route('admin.menus.update', $menu->id) : route('admin.menus.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        @csrf
        @if (isset($menu)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                <input type="text" name="name" value="{{ old('name', $menu->name ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                <input type="number" name="price" value="{{ old('price', $menu->price ?? '') }}" required min="0" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
            <select name="category_id" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                <option value="">Select category</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $menu->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none resize-none">{{ old('description', $menu->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Image</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/5 file:text-primary hover:file:bg-primary/10">
            @if (isset($menu) && $menu->image)
                <div class="mt-2 flex items-center gap-2">
                    <img src="{{ asset($menu->image) }}" alt="" class="w-16 h-16 rounded-lg object-cover">
                    <span class="text-xs text-gray-400">Current image</span>
                </div>
            @endif
            @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Rating</label>
            <input type="number" name="rating" value="{{ old('rating', $menu->rating ?? '') }}" step="0.1" min="0" max="5" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_bestseller" value="1" {{ old('is_bestseller', $menu->is_bestseller ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                <span class="text-sm text-gray-700">Best Seller</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_signature" value="1" {{ old('is_signature', $menu->is_signature ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                <span class="text-sm text-gray-700">Signature</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="has_spice_level" value="1" {{ old('has_spice_level', $menu->has_spice_level ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                <span class="text-sm text-gray-700">Has Spice Level</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="h-11 px-6 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">{{ isset($menu) ? 'Update Menu' : 'Create Menu' }}</button>
            <a href="{{ route('admin.menus.index') }}" class="h-11 px-6 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
