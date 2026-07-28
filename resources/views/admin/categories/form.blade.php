@extends('admin.layouts.app')
@section('title', ($category ?? false) ? 'Edit Category - ' . config('app.name') : 'Create Category - ' . config('app.name'))
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-1">
            <span class="material-symbols-outlined text-base">arrow_back</span> Back to Categories
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ isset($category) ? 'Edit Category' : 'Create Category' }}</h2>
    </div>

    <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        @csrf
        @if (isset($category)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            <p class="text-xs text-gray-400 mt-1">URL-friendly name (e.g., "main-course").</p>
            @error('slug') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="h-11 px-6 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">{{ isset($category) ? 'Update Category' : 'Create Category' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="h-11 px-6 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
