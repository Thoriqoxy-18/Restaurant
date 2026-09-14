<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Verdant Bistro')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Work+Sans:wght@400;500;600&family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-on-background font-body-md antialiased min-h-screen overflow-x-hidden">
    @if (session('error'))
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[2000] max-w-sm w-[calc(100%-2rem)] bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm font-medium shadow-lg" role="alert">{{ session('error') }}</div>
    @endif
    @if (session('success'))
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[2000] max-w-sm w-[calc(100%-2rem)] bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm font-medium shadow-lg" role="status">{{ session('success') }}</div>
    @endif
    @yield('content')
    @stack('scripts')
</body>
</html>
