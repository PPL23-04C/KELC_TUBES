@extends('layouts.auth')

@section('title', 'WattCare - Register')
@section('form-size', 'register')
@section('auth-title', 'Buat Akun WattCare')
@section('auth-subtitle', 'Mulai monitoring penggunaan listrik, analisis energi, dan prediksi konsumsi dengan sistem AI modern.')

@section('form-content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-group">
            <label for="name">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Masukkan nama lengkap"
                required
            >
        </div>

        <div class="auth-form-group">
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
            >
        </div>

        <div class="auth-form-group">
            <label for="daya_va">Daya Listrik Rumah</label>
            <select id="daya_va" name="daya_va" required>
                <option value="450">450 VA</option>
                <option value="900">900 VA</option>
                <option value="1300" selected>1300 VA</option>
                <option value="2200">2200 VA</option>
                <option value="3500">3500 VA+</option>
            </select>
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
            >
            <small id="password-warn" style="color: #fca5a5; font-size: 12px; display: none; margin-top: 4px;">⚠️ Password harus minimal 8 karakter</small>
        </div>

        <div class="auth-form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="Ulangi password"
                minlength="8"
                required
            >
        </div>

        <button type="submit" class="auth-btn">
            🚀 Buat Akun
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
    Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
@endsection