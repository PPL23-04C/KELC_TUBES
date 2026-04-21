<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WattCare - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h2>@yield('page-title')</h2>
                    <div class="meta">Pantau penggunaan listrik Anda secara real-time.</div>
                </div>
                <div class="meta">{{ now()->format('d M Y') }}</div>
            </div>

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
