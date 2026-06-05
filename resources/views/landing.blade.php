<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WattCare - Smart Energy Monitoring</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="landing-page">

    <div class="landing-wrapper">

        <!-- Background Glow -->
        <div class="bg-glow one"></div>
        <div class="bg-glow two"></div>

        <!-- Navbar -->
        <nav class="navbar">
            <div class="navbar-logo">
                <div class="navbar-logo-icon">⚡</div>
                WattCare
            </div>

            <div class="navbar-cta">
                <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn-primary">Mulai Sekarang</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-badge">
                    ⚡ Smart Energy Monitoring Platform
                </div>

                <h1 class="hero-title">
                    Monitoring Listrik
                    <span>Lebih Pintar</span>
                    dan Modern
                </h1>

                <p class="hero-subtitle">
                    Kelola konsumsi listrik rumah Anda dengan AI,
                    analisis penggunaan perangkat elektronik,
                    estimasi biaya otomatis, pengingat pintar,
                    dan insight energi yang lebih efisien.
                </p>

                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-landing btn-primary">🚀 Mulai Gratis</a>
                    <a href="{{ route('login') }}" class="btn-landing btn-secondary">🔐 Login</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Monitoring Real-time</h3>
                    <p>Pantau penggunaan listrik harian dengan dashboard modern dan data konsumsi yang mudah dipahami.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3>AI Prediksi Listrik</h3>
                    <p>Hitung estimasi penggunaan kWh hanya dengan mengetik aktivitas perangkat elektronik Anda.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⏰</div>
                    <h3>Smart Reminder</h3>
                    <p>Atur timer alat elektronik dengan popup notifikasi dan suara pengingat otomatis.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🌱</div>
                    <h3>Analisis Emisi CO₂</h3>
                    <p>Ketahui dampak penggunaan listrik terhadap lingkungan dan mulai kebiasaan hemat energi.</p>
                </div>
            </div>
        </section>

    </div>

</body>
</html>