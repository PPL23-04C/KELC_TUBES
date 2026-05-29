@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <div class="admin-welcome-card card" style="margin-bottom: 24px;">
        <h3 style="color: var(--text); font-size: 20px; margin-bottom: 8px;">👋 Selamat datang, {{ auth()->user()->name }}</h3>
        <p style="color: var(--muted); margin: 0; line-height: 1.6;">
            Gunakan menu sidebar untuk mengelola tarif listrik, pengguna, dan memantau konsumsi.
        </p>
    </div>

    <div class="grid grid-3" style="margin-bottom: 24px;">
        <div class="card">
            <h3>Total Users</h3>
            <div class="value">{{ \App\Models\User::count() }}</div>
            <span class="chip">Semua</span>
        </div>
        <div class="card">
            <h3>Total Tarif Listrik</h3>
            <div class="value">{{ \App\Models\ElectricityRate::count() }}</div>
            <span class="chip">Aktif</span>
        </div>
        <div class="card">
            <h3>Admin</h3>
            <div class="value">{{ \App\Models\User::where('role','admin')->count() }}</div>
            <span class="chip">Role</span>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h3>⚡ Aksi Cepat</h3>
            <div style="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;">
                <a class="btn" href="{{ route('admin.users.create') }}">+ Tambah User</a>
                <a class="btn secondary" href="{{ route('admin.electricity_rates.create') }}">+ Tambah Tarif</a>
            </div>
        </div>
        <div class="card">
            <h3>📊 Ringkasan</h3>
            <p style="color: var(--muted); margin: 12px 0 0; line-height: 1.7;">
                User biasa: <strong>{{ \App\Models\User::where('role','user')->count() }}</strong> •
                Tarif aktif: <strong>{{ \App\Models\ElectricityRate::count() }}</strong>
            </p>
        </div>
    </div>
@endsection
