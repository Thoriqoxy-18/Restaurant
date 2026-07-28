@extends('admin.layouts.app')
@section('title', ($staff ?? false) ? 'Edit Staff - ' . config('app.name') : 'Create Staff - ' . config('app.name'))
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 mb-1">
            <span class="material-symbols-outlined text-base">arrow_back</span> Back to Staff
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ isset($staff) ? 'Edit Staff' : 'Create Staff' }}</h2>
    </div>

    <form method="POST" action="{{ isset($staff) ? route('admin.staff.update', $staff->id) : route('admin.staff.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        @csrf
        @if (isset($staff)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
            <input type="text" name="name" value="{{ old('name', $staff->name ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $staff->email ?? '') }}" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password{{ isset($staff) ? ' (leave blank to keep current)' : '' }}</label>
            <input type="password" name="password" {{ isset($staff) ? '' : 'required' }} class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation" {{ isset($staff) ? '' : 'required' }} class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
            <select name="role" class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                <option value="cashier" {{ old('role', $staff->role ?? '') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                <option value="owner" {{ old('role', $staff->role ?? '') == 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="staff" {{ old('role', $staff->role ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="h-11 px-6 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">{{ isset($staff) ? 'Update Staff' : 'Create Staff' }}</button>
            <a href="{{ route('admin.staff.index') }}" class="h-11 px-6 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
