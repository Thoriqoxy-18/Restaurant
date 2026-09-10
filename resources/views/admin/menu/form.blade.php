@extends('admin.layouts.admin')
@section('title', $menu ? 'Edit Menu - Verdant Bistro' : 'Tambah Menu - Verdant Bistro')

@section('content')
@php
    $optionRows = fn ($rel) => $menu ? $menu->$rel->map(fn ($o) => ['name' => $o->name, 'extra_price' => $o->extra_price])->toArray() : [];
@endphp
<div class="max-w-3xl mx-auto" x-data="{
    variations: {{ Js::from($optionRows('variations')) }},
    toppings: {{ Js::from($optionRows('toppings')) }},
    sauces: {{ Js::from($optionRows('sauces')) }},
    add(field) { this[field].push({ name: '', extra_price: 0 }); },
    remove(field, i) { this[field].splice(i, 1); }
}">
    <div class="flex items-center gap-4 border-b border-outline-variant pb-6 mb-8">
        <a href="{{ route('admin.menu') }}" class="p-2 bg-surface-container-lowest rounded-full hover:bg-surface-container border border-outline-variant transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $menu ? 'Edit Menu' : 'Tambah Menu Baru' }}</h1>
    </div>

    <form method="POST" action="{{ $menu ? route('admin.menu.update', $menu) : route('admin.menu.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($menu) @method('PUT') @endif

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 border-b border-outline-variant pb-4">Informasi Dasar</h2>
            <div class="space-y-5">
                <div>
                    <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Nama Menu</label>
                    <input name="name" value="{{ old('name', $menu->name ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-all font-body-md text-body-md" placeholder="Contoh: Risoles Mayo"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Kategori</label>
                        <select name="category_id" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md">
                            @foreach ($categories as $c)
                            <option value="{{ $c->id }}" {{ (string) old('category_id', $menu->category_id ?? '') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Waktu Persiapan (menit)</label>
                        <input name="prep_time_minutes" type="number" min="1" value="{{ old('prep_time_minutes', $menu->prep_time_minutes ?? 15) }}" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Harga (Rp)</label>
                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $menu->price ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md"/>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Harga Coret / Promo (Rp, opsional)</label>
                        <input name="old_price" type="number" step="0.01" min="0" value="{{ old('old_price', $menu->old_price ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary outline-none font-body-md text-body-md"/>
                    </div>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface focus:border-primary focus:ring-1 focus:ring-secondary-fixed-dim outline-none font-body-md text-body-md">{{ old('description', $menu->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm font-medium text-on-surface mb-1.5">Foto Menu</label>
                    <input name="image" type="file" accept="image/*" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    @if ($menu && $menu->image_path)
                    <img src="{{ asset($menu->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-outline-variant">
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 border-b border-outline-variant pb-4">Varian</h2>
            <template x-for="(v, i) in variations" :key="i">
                <div class="flex gap-3 mb-3">
                    <input x-model="variations[i].name" :name="'variations['+i+'][name]'" placeholder="Nama varian (mis. Large)" class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <input x-model="variations[i].extra_price" :name="'variations['+i+'][extra_price]'" type="number" step="0.01" min="0" placeholder="+Harga" class="w-32 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <button type="button" @click="remove('variations', i)" class="text-error p-2"><span class="material-symbols-outlined">delete</span></button>
                </div>
            </template>
            <button type="button" @click="add('variations')" class="text-primary font-label-sm text-label-sm flex items-center gap-1 hover:underline"><span class="material-symbols-outlined text-[18px]">add</span> Tambah Varian</button>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 border-b border-outline-variant pb-4">Topping</h2>
            <template x-for="(t, i) in toppings" :key="i">
                <div class="flex gap-3 mb-3">
                    <input x-model="toppings[i].name" :name="'toppings['+i+'][name]'" placeholder="Nama topping" class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <input x-model="toppings[i].extra_price" :name="'toppings['+i+'][extra_price]'" type="number" step="0.01" min="0" placeholder="+Harga" class="w-32 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <button type="button" @click="remove('toppings', i)" class="text-error p-2"><span class="material-symbols-outlined">delete</span></button>
                </div>
            </template>
            <button type="button" @click="add('toppings')" class="text-primary font-label-sm text-label-sm flex items-center gap-1 hover:underline"><span class="material-symbols-outlined text-[18px]">add</span> Tambah Topping</button>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 border-b border-outline-variant pb-4">Saus</h2>
            <template x-for="(s, i) in sauces" :key="i">
                <div class="flex gap-3 mb-3">
                    <input x-model="sauces[i].name" :name="'sauces['+i+'][name]'" placeholder="Nama saus" class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <input x-model="sauces[i].extra_price" :name="'sauces['+i+'][extra_price]'" type="number" step="0.01" min="0" placeholder="+Harga" class="w-32 px-4 py-2.5 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md"/>
                    <button type="button" @click="remove('sauces', i)" class="text-error p-2"><span class="material-symbols-outlined">delete</span></button>
                </div>
            </template>
            <button type="button" @click="add('sauces')" class="text-primary font-label-sm text-label-sm flex items-center gap-1 hover:underline"><span class="material-symbols-outlined text-[18px]">add</span> Tambah Saus</button>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 border-b border-outline-variant pb-4">Pengaturan</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach ([
                    'is_available' => 'Tersedia', 'is_signature' => 'Signature', 'is_bestseller' => 'Best Seller', 'is_vegan' => 'Vegan', 'has_spice_level' => 'Level Pedas',
                ] as $field => $label)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary" {{ old($field, $menu->$field ?? true) ? 'checked' : '' }}/>
                    <span class="font-body-md text-body-md text-on-surface">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.menu') }}" class="px-5 py-2.5 border border-outline-variant text-on-surface-variant rounded-lg font-label-sm text-label-sm hover:bg-surface-container transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm font-semibold hover:bg-tertiary transition-colors shadow-sm">{{ $menu ? 'Simpan Perubahan' : 'Simpan Menu' }}</button>
        </div>
    </form>
</div>
@endsection
