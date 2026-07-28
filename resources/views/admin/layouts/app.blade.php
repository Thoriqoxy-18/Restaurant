<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . config('app.name'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 hidden lg:flex">
            <div class="h-16 flex items-center gap-2 px-5 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm">
                    <span class="material-symbols-outlined text-lg">restaurant</span>
                </div>
                <span class="font-bold text-gray-800">{{ config('app.name') }}</span>
                <span class="text-[10px] font-medium text-gray-400 ml-auto uppercase tracking-wider">Admin</span>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">dashboard</span> Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">category</span> Categories
                </a>
                <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.menus.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">menu_book</span> Menu Items
                </a>
                <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.tables.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">table_restaurant</span> Tables
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">receipt_long</span> Orders
                </a>
                <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.staff.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <span class="material-symbols-outlined text-base">badge</span> Staff
                </a>
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 w-full transition-colors">
                            <span class="material-symbols-outlined text-base">logout</span> Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-outlined text-gray-600">menu</span>
                    </button>
                    <h1 class="text-base font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ auth()->user()?->name ?? 'Admin' }}</span>
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-semibold">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</div>
                </div>
            </header>

            <div id="mobile-menu" class="hidden fixed inset-0 z-50 lg:hidden">
                <div class="absolute inset-0 bg-black/40" onclick="this.parentElement.classList.add('hidden')"></div>
                <aside class="relative w-72 max-w-[85%] h-full bg-white shadow-xl flex flex-col">
                    <div class="h-16 flex items-center gap-2 px-5 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm">
                            <span class="material-symbols-outlined text-lg">restaurant</span>
                        </div>
                        <span class="font-bold text-gray-800">{{ config('app.name') }}</span>
                        <button type="button" onclick="this.closest('#mobile-menu').classList.add('hidden')" class="ml-auto w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                            <span class="material-symbols-outlined text-gray-500">close</span>
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Dashboard</a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Categories</a>
                        <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Menu Items</a>
                        <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Tables</a>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Orders</a>
                        <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Staff</a>
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 w-full transition-colors">Logout</button>
                            </form>
                        </div>
                    </nav>
                </aside>
            </div>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
