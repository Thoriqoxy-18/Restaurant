@extends('admin.layouts.app')
@section('title', 'Menu')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" placeholder="Cari menu..." value="{{ request('search') }}" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary w-48 sm:w-64">
        <select name="category_id" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium">Cari</button>
    </form>
    <a href="{{ route('admin.menus.create') }}" class="h-10 px-5 bg-primary text-white rounded-xl text-sm font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">add</span> Tambah
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Gambar</th>
                <th class="px-5 py-3 font-medium">Nama</th>
                <th class="px-5 py-3 font-medium">Kategori</th>
                <th class="px-5 py-3 font-medium">Harga</th>
                <th class="px-5 py-3 font-medium">Rating</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($menus as $menu)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-5 py-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100">
                            @if ($menu->image)
                                <img src="{{ asset($menu->image) }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $menu->name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $menu->category?->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-800">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $menu->rating }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $menu->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Yakin?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Belum ada menu.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($menus->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $menus->links() }}</div>
    @endif
</div>
@endsection
