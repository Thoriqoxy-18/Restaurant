<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Verdant Bistro')</title>
@vite(['resources/css/admin.css', 'resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Literata:wght@700&display=swap" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex antialiased overflow-x-hidden">
{{-- SideNavBar (Admin, dark) — full height, drawer mobile, tetap di desktop --}}
<nav id="sidebar" class="flex flex-col bg-primary dark:bg-primary-container text-primary-fixed fixed top-0 left-0 min-h-screen w-[280px] py-6 z-50 transition-transform duration-300 -translate-x-full md:translate-x-0">
    {{-- Logo --}}
    <div class="px-6 mb-8 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary-fixed-dim flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined fill text-primary">restaurant</span>
            </div>
            <div class="min-w-0">
                <h1 class="font-display-brand text-display-brand text-primary-fixed text-xl tracking-tight leading-tight">Verdant Bistro</h1>
                <p class="font-label-sm text-label-sm opacity-80 mt-1">Admin Dashboard</p>
            </div>
        </div>
    </div>

    {{-- Navigasi --}}
    <div class="flex-1 min-h-0 px-3">
        @php
            $adminNav = [
                ['label' => 'Beranda', 'icon' => 'dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Menu', 'icon' => 'restaurant_menu', 'route' => 'admin.menu'],
                ['label' => 'Meja & QR', 'icon' => 'grid_view', 'route' => 'admin.tables'],
                ['label' => 'Pengguna', 'icon' => 'group', 'route' => 'admin.users'],
            ];
            $active = request()->routeIs('admin.dashboard') ? 'admin.dashboard' : (request()->routeIs('admin.menu*') ? 'admin.menu' : (request()->routeIs('admin.users*') ? 'admin.users' : (request()->routeIs('admin.tables*') ? 'admin.tables' : '')));
        @endphp
        <nav class="flex flex-col gap-1">
        @foreach ($adminNav as $item)
        <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ $active === $item['route'] ? 'bg-primary-container text-primary-fixed font-bold' : 'text-on-primary-container hover:bg-primary-container/40' }}">
            <span class="material-symbols-outlined {{ $active === $item['route'] ? 'fill' : '' }}">{{ $item['icon'] }}</span>
            <span class="font-label-sm text-label-sm">{{ $item['label'] }}</span>
        </a>
        @endforeach
        </nav>
    </div>

    {{-- Logout --}}
    <div class="px-3 mt-auto shrink-0 pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-error hover:bg-error-container hover:text-on-error-container rounded-lg transition-colors">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-sm text-label-sm">Keluar</span>
            </button>
        </form>
    </div>
</nav>

{{-- Overlay mobile sidebar --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 md:hidden hidden"></div>

{{-- Main Content --}}
<div class="flex-1 flex flex-col md:ml-[280px] w-full min-h-screen">
    <header class="bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline flex justify-between items-center h-16 px-4 md:px-8 sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <button id="menu-btn" class="md:hidden p-2 -ml-1 text-on-surface-variant hover:bg-surface-container rounded-lg" aria-label="Menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="font-headline-md text-headline-md text-primary md:hidden">Verdant Bistro</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-label-sm font-bold text-[14px] uppercase shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="hidden lg:block text-left">
                    <p class="font-label-sm text-label-sm text-on-surface font-semibold leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-on-surface-variant mt-0.5">Administrator</p>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 p-margin-mobile md:p-margin-desktop bg-background overflow-y-auto pb-16 md:pb-8">
        @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-secondary-container/40 border border-secondary/30 text-on-secondary-container rounded-lg px-4 py-3 font-body-md text-body-md">
            <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="mb-4 flex items-center gap-2 bg-error-container border border-error/30 text-on-error-container rounded-lg px-4 py-3 font-body-md text-body-md">
            <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
            {{ session('error') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="mb-4 flex items-start gap-2 bg-error-container border border-error/30 text-on-error-container rounded-lg px-4 py-3 font-body-md text-body-md">
            <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif
        @yield('content')
    </main>
</div>

<script>
    (function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const open = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
        const close = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };
        document.getElementById('menu-btn')?.addEventListener('click', open);
        document.getElementById('sidebar-close')?.addEventListener('click', close);
        overlay?.addEventListener('click', close);
        window.addEventListener('resize', () => { if (window.innerWidth >= 768) close(); });
    })();
</script>
</body>
</html>
