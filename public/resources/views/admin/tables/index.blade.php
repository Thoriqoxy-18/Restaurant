@extends('admin.layouts.app')
@section('title', 'Meja')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" placeholder="Cari meja..." value="{{ request('search') }}" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary w-48 sm:w-64">
        <select name="status" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Terisi</option>
            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
        </select>
        <button class="h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium">Cari</button>
    </form>
    <a href="{{ route('admin.tables.create') }}" class="h-10 px-5 bg-primary text-white rounded-xl text-sm font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">add</span> Tambah
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Nomor Meja</th>
                <th class="px-5 py-3 font-medium">Kapasitas</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tables as $table)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $table->table_number }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $table->capacity }} orang</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($table->status == 'available') bg-green-100 text-green-700
                            @elseif($table->status == 'occupied') bg-blue-100 text-blue-700
                            @elseif($table->status == 'reserved') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-500 @endif
                        ">
                            @if($table->status == 'available') Tersedia
                            @elseif($table->status == 'occupied') Terisi
                            @elseif($table->status == 'reserved') Reserved
                            @else Maintenance @endif
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.tables.edit', $table) }}" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" onsubmit="return confirm('Yakin?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada meja.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($tables->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $tables->links() }}</div>
    @endif
</div>
@endsection
