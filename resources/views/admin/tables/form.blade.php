@extends('admin.layouts.app')
@section('title', ($table ?? false) ? 'Edit Table - ' . config('app.name') : 'Create Table - ' . config('app.name'))
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-1">
            <span class="material-symbols-outlined text-base">arrow_back</span> Back to Tables
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ isset($table) ? 'Edit Table' : 'Create Table' }}</h2>
    </div>

    <form method="POST" action="{{ isset($table) ? route('admin.tables.update', $table->id) : route('admin.tables.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        @csrf
        @if (isset($table)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Table Number</label>
            <input type="text" name="table_number" value="{{ old('table_number', $table->table_number ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('table_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Capacity (guests)</label>
            <input type="number" name="capacity" value="{{ old('capacity', $table->capacity ?? '') }}" min="1" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('capacity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', $table->is_available ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                <span class="text-sm text-gray-700">Available</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="h-11 px-6 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">{{ isset($table) ? 'Update Table' : 'Create Table' }}</button>
            <a href="{{ route('admin.tables.index') }}" class="h-11 px-6 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
