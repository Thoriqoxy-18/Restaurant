@extends('admin.layouts.app')
@section('title', 'Staff - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Staff</h2>
            <p class="text-sm text-gray-500">Manage your restaurant staff accounts.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="h-10 px-4 rounded-xl bg-primary text-white text-sm font-semibold flex items-center gap-1.5 hover:bg-primary/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-base">add</span> Add Staff
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">{{ session('success') }}</div>
    @endif

    @if ($staff->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <span class="material-symbols-outlined text-4xl text-gray-300">badge</span>
            <h3 class="text-base font-semibold text-gray-700 mt-2">No staff</h3>
            <p class="text-sm text-gray-400 mt-1">Add staff members to help manage orders.</p>
            <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90">Add Staff</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($staff as $member)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $member->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                    @if($member->role == 'owner') bg-purple-100 text-purple-700
                                    @elseif($member->role == 'cashier') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-600 @endif
                                ">{{ $member->role ?? 'staff' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.staff.edit', $member->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary hover:bg-primary/5 transition-colors">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.staff.destroy', $member->id) }}" onsubmit="return confirm('Delete this staff member?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
