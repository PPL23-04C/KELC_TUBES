<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WattCare - Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Buat Akun WattCare</h2>
            <p class="meta">Mulai memonitor konsumsi listrik Anda.</p>

            @if($errors->any())
                <div class="alert">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="daya_va">Daya Listrik (VA)</label>
                    <select id="daya_va" name="daya_va" required>
                        <option value="450">450 VA</option>
                        <option value="900">900 VA</option>
                        <option value="1300" selected>1300 VA</option>
                        <option value="2200">2200 VA</option>
                        <option value="3500">3500 VA+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
                <button class="btn" type="submit">Daftar</button>
            </form>

            <p style="margin-top: 16px;">Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--primary);">Login</a></p>
        </div>
    </div>
</body>
</html>
