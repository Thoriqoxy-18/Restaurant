<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Verdant Bistro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fffa; margin: 0; }
        input:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
    </style>
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px; margin: 0;">
    <div style="width: 100%; max-width: 380px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #22c55e; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 4px 14px rgba(34,197,94,0.3);">
                <span style="color: white; font-size: 20px; font-weight: bold;">V</span>
            </div>
            <h1 style="font-size: 20px; font-weight: 700; color: #1f2937; margin: 0 0 4px;">Verdant Admin</h1>
            <p style="font-size: 14px; color: #9ca3af; margin: 0;">Masuk ke panel administrasi</p>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 16px; padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; font-size: 14px; color: #dc2626;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.authenticate') }}" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; padding: 24px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            @csrf
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus style="width: 100%; height: 44px; padding: 0 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; box-sizing: border-box; transition: all 0.2s;">
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Password</label>
                <input type="password" name="password" required style="width: 100%; height: 44px; padding: 0 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; box-sizing: border-box; transition: all 0.2s;">
            </div>
            <button type="submit" style="width: 100%; height: 44px; background: #22c55e; color: white; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(34,197,94,0.2);">Masuk</button>
        </form>
        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">Default: admin@verdantbistro.com / password</p>
    </div>
</body>
</html>
