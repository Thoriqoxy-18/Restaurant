@extends('admin.layouts.admin')
@section('title', 'Manajemen Pengguna - Verdant Bistro')

@section('content')
<div class="max-w-container-max mx-auto" x-data="{
    modal: false, editing: null, q: '',
    openAdd() { this.editing = null; this.modal = true; },
    openEdit(u) { this.editing = u; this.modal = true; },
    close() { this.modal = false; this.editing = null; }
}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-1">Manajemen Pengguna</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Kelola akun staf, kasir, dan administrator sistem.</p>
        </div>
        <button @click="openAdd()" class="bg-primary hover:bg-tertiary text-on-primary rounded-lg py-2.5 px-5 font-label-sm text-label-sm font-semibold transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm whitespace-nowrap self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Pengguna
        </button>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="p-4 border-b border-outline-variant bg-surface">
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input x-model="q" class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-shadow" placeholder="Cari nama atau email..."/>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-surface-container sticky top-0 z-10 border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold">Nama</th>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold">Email</th>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold">Role</th>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold">Status</th>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold">Dibuat</th>
                        <th class="py-3 px-4 font-label-sm text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50 bg-surface-container-lowest">
                    @forelse ($users as $user)
                    <tr x-show="q === '' || '{{ $user->name }} {{ $user->email }}'.toLowerCase().includes(q.toLowerCase())" class="hover:bg-surface transition-colors group {{ $loop->even ? 'bg-surface-bright' : '' }}">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm font-bold text-[14px] uppercase shrink-0">{{ substr($user->name, 0, 1) }}</div>
                                <p class="font-body-md text-body-md font-medium text-on-surface {{ $user->is_active ? '' : 'opacity-60' }}">{{ $user->name }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-body-md text-body-md text-on-surface-variant {{ $user->is_active ? '' : 'opacity-60' }}">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold tracking-wide uppercase {{ $user->role === 'admin' ? 'bg-secondary-container text-on-secondary-container' : 'border border-outline-variant text-on-surface-variant' }}">{{ $user->role }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-secondary' : 'bg-outline' }}"></div>
                                <span class="font-label-sm text-label-sm text-on-surface {{ $user->is_active ? '' : 'opacity-60' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-body-md text-[13px] text-on-surface-variant">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEdit({ id: {{ $user->id }}, name: '{{ $user->name }}', email: '{{ $user->email }}', role: '{{ $user->role }}', is_active: {{ $user->is_active ? 'true' : 'false' }}, isSelf: {{ $user->id === Auth::id() ? 'true' : 'false' }}, canToggle: {{ $user->id === Auth::id() ? 'false' : 'true' }}, canDelete: {{ $user->id === Auth::id() ? 'false' : 'true' }} })" class="p-1.5 rounded hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors" title="Edit"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="p-1.5 rounded hover:bg-surface-container text-on-surface-variant {{ $user->is_active ? 'hover:text-error' : 'hover:text-secondary' }} transition-colors" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                        <span class="material-symbols-outlined text-[20px]">{{ $user->is_active ? 'block' : 'check_circle' }}</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-1.5 rounded hover:bg-error-container text-error transition-colors" title="Hapus" {{ $user->id === Auth::id() ? 'disabled' : '' }}><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-on-surface-variant">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-outline-variant bg-surface text-on-surface-variant font-label-sm text-label-sm">
            Menampilkan {{ $users->count() }} pengguna
        </div>
    </div>

    {{-- Modal Tambah/Edit Pengguna --}}
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-on-background/40 backdrop-blur-sm transition-all duration-200" :class="modal ? 'flex' : 'hidden'">
        <div class="bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant w-full max-w-lg mx-4 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between bg-surface">
                <h3 class="font-headline-md text-headline-md text-on-surface" x-text="editing ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                <button @click="close()" class="text-on-surface-variant hover:text-on-surface p-1 rounded-md hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto">
                <form class="space-y-5"
                      method="POST"
                      :action="editing ? '/admin/users/' + editing.id : '{{ route('admin.users.store') }}'">
                    @csrf
                    <input type="hidden" name="_method" :value="editing ? 'PATCH' : 'POST'">
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Nama Lengkap</label>
                        <input name="name" required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none font-body-md text-body-md" placeholder="Masukkan nama lengkap" :value="editing ? editing.name : ''"/>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Email</label>
                        <input name="email" type="email" required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none font-body-md text-body-md" placeholder="contoh@verdant.com" :value="editing ? editing.email : ''"/>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5" x-text="editing ? 'Password (kosongkan jika tetap)' : 'Password'"></label>
                            <input name="password" type="password" :required="!editing" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md" placeholder="Minimal 8 karakter"/>
                        </div>
                        <div>
                            <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Role</label>
                            <select name="role" required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md">
                                <option value="kasir" :selected="editing && editing.role === 'kasir'">Kasir</option>
                                <option value="admin" :selected="editing && editing.role === 'admin'">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <label class="block font-label-sm text-label-sm font-medium text-on-surface">Status Akun</label>
                            <p class="text-[12px] text-on-surface-variant mt-0.5">Pengguna dapat langsung login.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input name="is_active" type="checkbox" value="1" class="sr-only peer" :checked="!editing || editing.is_active"/>
                            <div class="w-11 h-6 bg-surface-variant rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-outline-variant after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2 border-t border-outline-variant">
                        <button type="button" @click="close()" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-sm text-label-sm hover:bg-surface-container transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm font-medium hover:bg-tertiary transition-colors shadow-sm" x-text="editing ? 'Simpan Perubahan' : 'Simpan Pengguna'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
