@extends('layouts.auth')

@section('title', 'WattCare - Login')
@section('form-size', '')
@section('auth-title', 'Masuk ke WattCare')
@section('auth-subtitle', 'Pantau konsumsi listrik rumah Anda dengan sistem monitoring modern dan AI.')

@section('form-content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-form-group">
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email Anda"
                required
                autocomplete="email"
            >
        </div>

        <div class="auth-form-group">
            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="Masukkan password"
                minlength="8"
                required
                autocomplete="current-password"
            >
            <small id="password-warn" style="color: #fca5a5; font-size: 12px; display: none; margin-top: 4px;">⚠️ Password harus minimal 8 karakter</small>
        </div>

        <button type="submit" class="auth-btn">
            🔐 Login Sekarang
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordWarn = document.getElementById('password-warn');
            
            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
                    passwordWarn.style.display = 'block';
                } else {
                    passwordWarn.style.display = 'none';
                }
            });
        });
    </script>
@endsection

@section('auth-footer')
    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
@endsection
```
