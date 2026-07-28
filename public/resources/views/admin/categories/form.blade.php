@extends('admin.layouts.app')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf @if($category->exists) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('name') border-red-300 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary">
                <label for="is_active" class="text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="h-11 px-6 bg-primary text-white rounded-xl text-sm font-semibold">{{ $category->exists ? 'Simpan' : 'Tambah' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="h-11 px-6 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center">Batal</a>
        </div>
    </form>
</div>
@endsection
