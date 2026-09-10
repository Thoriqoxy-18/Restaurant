<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Verdant Bistro - Masuk</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Literata:wght@700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
@vite(['resources/css/admin.css'])
</head>
<body class="bg-background min-h-screen flex items-center justify-center p-margin-mobile md:p-margin-desktop font-body-lg text-on-surface">
<main class="w-full max-w-md bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] p-8 flex flex-col gap-8 relative overflow-hidden">
<div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, #012d1d 1px, transparent 0); background-size: 24px 24px;"></div>

@if (session('success'))
<div class="relative z-10 flex items-center gap-2 bg-secondary-container/40 border border-secondary/30 text-on-secondary-container rounded-lg px-4 py-3 font-body-md text-body-md">
    <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
    {{ session('success') }}
</div>
@endif

<header class="flex flex-col items-center gap-2 relative z-10">
    <h1 class="font-display-brand text-display-brand text-primary">Verdant Bistro</h1>
    <h2 class="font-headline-md text-headline-md text-on-surface-variant">Selamat Datang</h2>
    <p class="font-body-md text-body-md text-outline">Masuk ke sistem manajemen restoran</p>
</header>

<form class="flex flex-col gap-6 relative z-10" id="loginForm" method="POST" action="{{ route('login.attempt') }}" onsubmit="showLoading()">
    @csrf
    <div class="flex flex-col gap-2">
        <label class="font-label-sm text-label-sm text-on-surface" for="email">Email atau Username</label>
        <div class="relative">
            <input class="w-full bg-surface-container-lowest border rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:ring-2 transition-all {{ $errors->has('email') ? 'border-error focus:ring-error-container focus:border-error' : 'border-outline-variant focus:ring-secondary-container focus:border-primary' }}"
                   id="email" name="email" placeholder="Masukkan email Anda" type="text" value="{{ old('email') }}" required autofocus/>
            @error('email')
            <span class="material-symbols-outlined absolute right-3 top-3 text-error pointer-events-none" style="font-variation-settings: 'FILL' 1;">error</span>
            @enderror
        </div>
        @error('email')
        <p class="font-label-sm text-label-sm text-error mt-1 flex items-center gap-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-2">
        <label class="font-label-sm text-label-sm text-on-surface" for="password">Password</label>
        <div class="relative">
            <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:ring-2 focus:ring-secondary-container focus:border-primary transition-all pr-10"
                   id="password" name="password" placeholder="Masukkan password Anda" type="password" required/>
            <button aria-label="Toggle password visibility" class="absolute right-3 top-3 text-outline hover:text-on-surface transition-colors focus:outline-none" onclick="togglePassword()" type="button">
                <span class="material-symbols-outlined" id="visibilityIcon">visibility_off</span>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer group">
            <div class="relative flex items-center justify-center w-5 h-5">
                <input class="peer appearance-none w-5 h-5 border border-outline-variant rounded bg-surface-container-lowest checked:bg-primary checked:border-primary focus:ring-2 focus:ring-secondary-container focus:ring-offset-1 focus:ring-offset-surface-container-lowest transition-colors cursor-pointer" type="checkbox" name="remember"/>
                <span class="material-symbols-outlined absolute text-on-primary text-[16px] pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" style="font-variation-settings: 'FILL' 1;">check</span>
            </div>
            <span class="font-body-md text-body-md text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat saya</span>
        </label>
        <a class="font-label-sm text-label-sm text-primary hover:text-secondary-fixed-dim transition-colors" href="#">Lupa Password?</a>
    </div>

    <button class="mt-2 w-full bg-primary hover:bg-tertiary-container text-on-primary font-label-sm text-label-sm py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2 relative overflow-hidden group" id="submitBtn" type="submit">
        <span id="btnText">Masuk</span>
        <span class="material-symbols-outlined animate-spin" id="spinnerIcon" style="display:none">progress_activity</span>
    </button>
</form>

<div class="text-center relative z-10">
    <p class="font-body-md text-body-md text-outline-variant text-[12px]">
        Butuh bantuan akses? <a class="text-primary hover:underline" href="#">Hubungi IT Support</a>
    </p>
</div>
</main>

<script>
    function togglePassword() {
        const pwdInput = document.getElementById('password');
        const icon = document.getElementById('visibilityIcon');
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            icon.textContent = 'visibility';
        } else {
            pwdInput.type = 'password';
            icon.textContent = 'visibility_off';
        }
    }

    function showLoading() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('spinnerIcon');
        btn.classList.add('opacity-90', 'cursor-not-allowed');
        btn.disabled = true;
        btnText.textContent = 'Memproses...';
        spinner.style.display = '';
    }
</script>
</body>
</html>
