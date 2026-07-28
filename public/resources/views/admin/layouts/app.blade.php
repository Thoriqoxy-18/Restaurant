<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Verdant Bistro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r border-gray-200 min-h-screen p-5 hidden md:block">
            <div class="flex items-center gap-2 mb-8">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white text-sm font-bold">V</div>
                <span class="font-bold text-gray-800">Verdant Admin</span>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-lg">dashboard</span> Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-lg">category</span> Kategori
                </a>
                <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.menus.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-lg">restaurant_menu</span> Menu
                </a>
                <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.tables.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-lg">table_restaurant</span> Meja
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-lg">receipt_long</span> Pesanan
                </a>
                <hr class="my-4 border-gray-100">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
                    <span class="material-symbols-outlined text-lg">arrow_back</span> Kembali ke Website
                </a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">← Website</a>
            </header>

            <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
