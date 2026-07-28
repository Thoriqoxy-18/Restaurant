@extends('admin.layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" placeholder="Cari kategori..." value="{{ request('search') }}" class="h-10 px-4 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-primary w-64">
        <button class="h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium">Cari</button>
    </form>
    <a href="{{ route('admin.categories.create') }}" class="h-10 px-5 bg-primary text-white rounded-xl text-sm font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">add</span> Tambah
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Nama</th>
                <th class="px-5 py-3 font-medium">Slug</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Menu</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $category->slug }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $category->menuItems()->count() }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-medium hover:bg-primary/20">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($categories->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
