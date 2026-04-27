<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WattCare - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Masuk ke WattCare</h2>
            <p class="meta">Pantau konsumsi listrik rumah Anda.</p>

            @if($errors->any())
                <div class="alert">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <button class="btn" type="submit">Login</button>
            </form>

            <p style="margin-top: 16px;">Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary);">Daftar sekarang</a></p>
        </div>
    </div>
</body>
</html>
