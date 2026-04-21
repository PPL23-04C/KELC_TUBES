<aside class="sidebar">
    <div>
        <h1>⚡ WattCare</h1>
        <div class="tagline">Monitoring konsumsi listrik rumah</div>
    </div>

    <nav class="nav-group">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
        <a class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}" href="{{ route('devices.index') }}">Perangkat</a>
        <a class="nav-link {{ request()->routeIs('analysis.*') ? 'active' : '' }}" href="{{ route('analysis.input') }}">Input Analisis</a>
        <a class="nav-link {{ request()->routeIs('history.index') ? 'active' : '' }}" href="{{ route('history.index') }}">Riwayat</a>
        <a class="nav-link {{ request()->routeIs('recommendations.index') ? 'active' : '' }}" href="{{ route('recommendations.index') }}">Rekomendasi</a>
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Profil</a>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn secondary" type="submit">Logout</button>
    </form>
</aside>
