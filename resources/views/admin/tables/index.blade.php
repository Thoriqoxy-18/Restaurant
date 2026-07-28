@extends('admin.layouts.app')
@section('title', 'Tables - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tables</h2>
            <p class="text-sm text-gray-500">Manage restaurant tables and QR codes.</p>
        </div>
        <a href="{{ route('admin.tables.create') }}" class="h-10 px-4 rounded-xl bg-primary text-white text-sm font-semibold flex items-center gap-1.5 hover:bg-primary/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-base">add</span> Add Table
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">{{ session('success') }}</div>
    @endif

    @if ($tables->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <span class="material-symbols-outlined text-4xl text-gray-300">table_restaurant</span>
            <h3 class="text-base font-semibold text-gray-700 mt-2">No tables</h3>
            <p class="text-sm text-gray-400 mt-1">Add tables to start accepting orders.</p>
            <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90">Add Table</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($tables as $table)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.tables.edit', $table->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary hover:bg-primary/5 transition-colors">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.tables.destroy', $table->id) }}" onsubmit="return confirm('Delete this table?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">{{ $table->table_number }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Capacity: {{ $table->capacity ?? '-' }} guests</p>
                    <p class="text-xs mt-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $table->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $table->is_available ? 'Available' : 'Occupied' }}
                        </span>
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
