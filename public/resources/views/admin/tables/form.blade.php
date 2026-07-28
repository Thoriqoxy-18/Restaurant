@extends('admin.layouts.app')
@section('title', $table->exists ? 'Edit Meja' : 'Tambah Meja')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ $table->exists ? route('admin.tables.update', $table) : route('admin.tables.store') }}">
        @csrf @if($table->exists) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja *</label>
                    <input type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}" placeholder="A1" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('table_number') border-red-300 @enderror">
                    @error('table_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="20" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary @error('capacity') border-red-300 @enderror">
                    @error('capacity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
                    <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>Terisi</option>
                    <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="maintenance" {{ old('status', $table->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="h-11 px-6 bg-primary text-white rounded-xl text-sm font-semibold">{{ $table->exists ? 'Simpan' : 'Tambah' }}</button>
            <a href="{{ route('admin.tables.index') }}" class="h-11 px-6 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center">Batal</a>
        </div>
    </form>
</div>
@endsection
