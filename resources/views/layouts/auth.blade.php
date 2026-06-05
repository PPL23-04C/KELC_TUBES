<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WattCare')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-page">

    <!-- Background Glow -->
    <div class="auth-bg-glow-wrapper">
        <div class="bg-glow one"></div>
        <div class="bg-glow two"></div>
    </div>

    <!-- AUTH -->
    <div class="auth-container">
        <div class="auth-card-modern @yield('form-size')">

            <!-- Logo -->
            <div class="auth-logo">
                <div class="auth-logo-icon">⚡</div>
            </div>

            <!-- Title -->
            <h1 class="auth-title">@yield('auth-title')</h1>

            <!-- Subtitle -->
            <p class="auth-subtitle">@yield('auth-subtitle')</p>

            <!-- Error Alert -->
            @if($errors->any())
                <div class="auth-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Content -->
            @yield('form-content')

            <!-- Footer -->
            <div class="auth-footer">
                @yield('auth-footer')
            </div>

        </div>
    </div>

</body>

</html>
