@extends('admin.layouts.app')
@section('title', $menu->exists ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ $menu->exists ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" enctype="multipart/form-data">
        @csrf @if($menu->exists) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('name') border-red-300 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                    <select name="category_id" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('category_id') border-red-300 @enderror">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">{{ old('description', $menu->description) }}</textarea>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga *</label>
                    <input type="number" name="price" value="{{ old('price', $menu->price) }}" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('price') border-red-300 @enderror">
                    @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <input type="number" step="0.1" max="5" name="rating" value="{{ old('rating', $menu->rating) }}" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge</label>
                    <input type="text" name="badge" value="{{ old('badge', $menu->badge) }}" placeholder="Best Seller" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Menu</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                @if ($menu->image)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ asset($menu->image) }}" class="w-16 h-16 rounded-lg object-cover">
                        <span class="text-xs text-gray-400">Gambar saat ini</span>
                    </div>
                @endif
                @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary">
                    <span class="text-sm text-gray-700">Tersedia</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $menu->is_popular) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary">
                    <span class="text-sm text-gray-700">Popular</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="h-11 px-6 bg-primary text-white rounded-xl text-sm font-semibold">{{ $menu->exists ? 'Simpan' : 'Tambah' }}</button>
            <a href="{{ route('admin.menus.index') }}" class="h-11 px-6 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center">Batal</a>
        </div>
    </form>
</div>
@endsection
