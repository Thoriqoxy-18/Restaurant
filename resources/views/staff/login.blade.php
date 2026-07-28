<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Login - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center mx-auto mb-3 shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-white text-2xl">restaurant</span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">{{ config('app.name') }}</h1>
            <p class="text-sm text-gray-400 mt-1">Staff &mdash; sign in to continue</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">error</span>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('staff.login.authenticate') }}" class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all">
            </div>
            <button type="submit" class="w-full h-11 rounded-xl bg-primary text-white font-semibold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 active:scale-[0.98] transition-all">Sign In</button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">Default: staff@restaurant.com / password</p>
    </div>
</body>
</html>
