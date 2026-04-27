<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WattCare - Monitoring Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="landing">
        <div class="hero">
            <h1>WattCare</h1>
            <p>Kelola konsumsi listrik rumah Anda dengan insight harian, estimasi biaya, dan dampak emisi CO2. Catat perangkat, analisis pemakaian, dan dapatkan rekomendasi hemat energi.</p>
            <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a class="btn" href="{{ route('register') }}">Mulai Sekarang</a>
                <a class="btn secondary" href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
