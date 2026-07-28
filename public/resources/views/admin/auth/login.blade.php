<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Verdant Bistro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fffa; }
        input:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 shadow-lg shadow-primary/20">V</div>
            <h1 class="text-xl font-bold text-gray-800">Verdant Admin</h1>
            <p class="text-sm text-gray-400 mt-1">Masuk ke panel administrasi</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.authenticate') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm transition-all">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-300 text-primary">
                <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
            </div>
            <button type="submit" class="w-full h-11 bg-primary text-white rounded-xl text-sm font-semibold shadow-md hover:opacity-90 transition-all">Masuk</button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">Default: admin@verdantbistro.com / password</p>
    </div>
</body>
</html>
