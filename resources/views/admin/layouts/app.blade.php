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
<body class="bg-background text-on-background font-body-md min-h-screen flex overflow-x-hidden">
{{-- SideNavBar (drawer mobile, tetap di desktop) --}}
<nav id="sidebar" class="flex flex-col fixed left-0 top-0 min-h-screen w-[280px] bg-white dark:bg-inverse-surface border-r border-outline-variant dark:border-outline py-6 z-50 transition-transform duration-300 -translate-x-full md:translate-x-0">
    <div class="px-6 mb-8 flex items-center justify-between">
        <h1 class="font-display-brand text-display-brand text-primary dark:text-primary-fixed-dim">Verdant Bistro</h1>
        <button id="sidebar-close" class="md:hidden p-1 text-on-surface-variant hover:bg-surface-container rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>

    <ul class="flex-1 flex flex-col gap-1 px-2">
        @php
            $nav = [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'kasir.dashboard', 'active' => request()->routeIs('kasir.dashboard')],
                ['label' => 'Pesanan', 'icon' => 'receipt_long', 'route' => 'kasir.orders', 'active' => request()->routeIs('kasir.orders*')],
                ['label' => 'Pembayaran', 'icon' => 'payments', 'route' => 'kasir.payments', 'active' => request()->routeIs('kasir.payments')],
                ['label' => 'Notifikasi', 'icon' => 'notifications', 'route' => 'kasir.notifications', 'active' => request()->routeIs('kasir.notifications')],
            ];
        @endphp
        @foreach ($nav as $item)
        <li>
            <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ $item['active'] ? 'text-secondary dark:text-secondary-fixed-dim font-bold bg-surface-container-low border-r-4 border-secondary rounded-l-lg' : 'text-on-surface-variant dark:text-surface-variant hover:text-secondary dark:hover:text-secondary-fixed-dim hover:bg-surface-container-low dark:hover:bg-surface-container-highest' }}">
                <span class="material-symbols-outlined {{ $item['active'] ? 'fill' : '' }}">{{ $item['icon'] }}</span>
                <span class="font-body-md text-body-md">{{ $item['label'] }}</span>
            </a>
        </li>
        @endforeach
    </ul>

    <div class="px-2 mt-auto border-t border-outline-variant/50 pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-error hover:bg-error-container rounded-lg transition-colors duration-200">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-body-md text-body-md">Keluar</span>
            </button>
        </form>
    </div>
</nav>

{{-- Overlay mobile sidebar --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 md:hidden hidden"></div>

{{-- TopNavBar --}}
<header class="fixed top-0 right-0 w-full md:w-[calc(100%-280px)] h-16 bg-surface dark:bg-surface-container-lowest border-b border-outline-variant dark:border-outline z-30 flex justify-between items-center px-4 md:px-8">
    <div class="flex items-center gap-2">
        <button id="menu-btn" class="md:hidden p-2 -ml-1 text-on-surface-variant hover:bg-surface-container rounded-lg" aria-label="Menu">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <span class="md:hidden font-display-brand text-display-brand text-primary">Verdant Bistro</span>
    </div>
    <div class="hidden md:block"></div>
    <div class="flex items-center gap-4 text-on-surface-variant dark:text-surface-variant">
        <span class="font-label-sm text-label-sm hidden sm:block" id="clock">--</span>
        {{-- Notifikasi Kasir --}}
        <div class="relative" id="notif-wrap">
            <button id="notif-btn" class="relative p-2 hover:bg-surface-container dark:hover:bg-surface-container-high rounded-full transition-all" aria-label="Notifikasi">
                <span class="material-symbols-outlined">notifications</span>
                <span id="notif-badge" class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 bg-error text-on-error rounded-full text-[10px] font-bold flex items-center justify-center hidden">0</span>
            </button>
            <div id="notif-panel" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-inverse-surface rounded-xl shadow-xl border border-outline-variant dark:border-outline z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-outline-variant flex items-center justify-between bg-surface-bright">
                    <h4 class="font-label-sm text-label-sm font-semibold text-on-surface">Notifikasi</h4>
                    <button id="notif-read-all" class="text-secondary font-label-sm text-label-sm hover:underline">Tandai semua dibaca</button>
                </div>
                <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-outline-variant/50"></div>
            </div>
        </div>
        <button class="p-2 hover:bg-surface-container dark:hover:bg-surface-container-high rounded-full transition-all">
            <span class="material-symbols-outlined">schedule</span>
        </button>
    </div>
</header>

{{-- Content --}}
<main class="flex-1 md:ml-[280px] pt-16 min-h-screen w-full">
    @if (session('success'))
    <div class="mx-4 md:mx-8 mt-4 flex items-center gap-2 bg-secondary-container/40 border border-secondary/30 text-on-secondary-container rounded-lg px-4 py-3 font-body-md text-body-md">
        <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="mx-4 md:mx-8 mt-4 flex items-center gap-2 bg-error-container border border-error/30 text-on-error-container rounded-lg px-4 py-3 font-body-md text-body-md">
        <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
        {{ session('error') }}
    </div>
    @endif
    @yield('content')
</main>

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
    function tick() {
        const el = document.getElementById('clock');
        if (!el) return;
        const d = new Date();
        el.textContent = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' | ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    tick();
    setInterval(tick, 30000);

    // ===== Notifikasi Kasir =====
    (function() {
        const btn = document.getElementById('notif-btn');
        const badge = document.getElementById('notif-badge');
        const panel = document.getElementById('notif-panel');
        const list = document.getElementById('notif-list');
        const readAll = document.getElementById('notif-read-all');
        if (!btn) return;

        function render(items) {
            list.innerHTML = '';
            if (!items.length) {
                list.innerHTML = '<p class="p-6 text-center text-on-surface-variant font-label-sm text-label-sm">Belum ada notifikasi.</p>';
                return;
            }
            items.forEach(function(n) {
                const a = document.createElement('a');
                a.href = n.order_id ? '{{ url('/kasir/orders') }}/' + n.order_id : '#';
                a.className = 'block px-4 py-3 hover:bg-surface-container-low transition-colors' + (n.read ? ' opacity-60' : ' bg-surface-bright');
                a.innerHTML = '<div class="flex items-start gap-2">'
                    + '<span class="mt-0.5 shrink-0">' + (n.type === 'payment' ? '💳' : '🔔') + '</span>'
                    + '<div class="min-w-0 flex-1"><p class="font-label-sm text-label-sm font-semibold text-on-surface">' + n.title + '</p>'
                    + '<p class="text-xs text-on-surface-variant mt-0.5 break-words">' + n.message + '</p>'
                    + '<p class="text-[10px] text-outline mt-0.5">' + n.time + '</p></div>'
                    + (n.read ? '' : '<span class="w-2 h-2 rounded-full bg-error mt-1 shrink-0"></span>')
                    + '</div>';
                if (!n.read) {
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        fetch('{{ url('/kasir/notifications') }}/' + n.id + '/read', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).then(load);
                    });
                }
                list.appendChild(a);
            });
        }

        function load() {
            fetch('{{ route('kasir.notifications.data') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(function(d) {
                    badge.textContent = d.count;
                    badge.classList.toggle('hidden', d.count === 0);
                    if (!panel.classList.contains('hidden')) render(d.items);
                })
                .catch(() => {});
        }

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            panel.classList.toggle('hidden');
            load();
        });
        document.addEventListener('click', function(e) { if (!document.getElementById('notif-wrap').contains(e.target)) panel.classList.add('hidden'); });
        readAll.addEventListener('click', function() {
            fetch('{{ route('kasir.notifications.readall') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).then(load);
        });

        load();
        setInterval(load, 5000);
    })();
</script>
@stack('scripts')
</body>
</html>
