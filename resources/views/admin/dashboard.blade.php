@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 mb-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -bottom-10 -right-6 text-slate-700/20">
            <i data-lucide="shield" class="w-56 h-56"></i>
        </div>
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 border border-white/10 rounded-full text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                Panel Admin
            </div>
            <h2 class="text-2xl font-bold mb-1">👋 Selamat datang, {{ auth()->user()->name }}</h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                Gunakan menu sidebar untuk mengelola tarif listrik, pengguna, dan memantau konsumsi.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-blue-400"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Pengguna</p>
                    <p class="text-4xl font-bold text-slate-900">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 mt-4 text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                Semua Role
            </span>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-500 to-amber-400"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Tarif Listrik</p>
                    <p class="text-4xl font-bold text-slate-900">{{ \App\Models\ElectricityRate::count() }}</p>
                </div>
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i data-lucide="zap" class="w-7 h-7"></i>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 mt-4 text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                Aktif
            </span>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-violet-600 to-violet-400"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Akun Admin</p>
                    <p class="text-4xl font-bold text-slate-900">{{ \App\Models\User::where('role','admin')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-600">
                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 mt-4 text-xs font-semibold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full">
                Role Admin
            </span>
        </div>
    </div>

    <!-- Quick Actions & Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                <i data-lucide="zap" class="w-5 h-5 text-blue-600"></i>
                Aksi Cepat
            </h3>
            <div class="space-y-3">
                <a href="{{ route('admin.users.create') }}"
                   class="flex items-center gap-3 p-4 rounded-2xl bg-blue-50 hover:bg-blue-100 transition-colors border border-blue-100 group">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-blue-900 text-sm">Tambah User Baru</p>
                        <p class="text-xs text-blue-600">Buat akun pengguna</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-blue-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('admin.electricity_rates.create') }}"
                   class="flex items-center gap-3 p-4 rounded-2xl bg-amber-50 hover:bg-amber-100 transition-colors border border-amber-100 group">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="plus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-amber-900 text-sm">Tambah Tarif Listrik</p>
                        <p class="text-xs text-amber-600">Atur harga per kWh</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-amber-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-blue-600"></i>
                Ringkasan Sistem
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                    <span class="text-sm text-slate-600 font-medium flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        User Biasa
                    </span>
                    <span class="font-bold text-slate-900">{{ \App\Models\User::where('role','user')->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                    <span class="text-sm text-slate-600 font-medium flex items-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4 text-slate-400"></i>
                        Tarif Aktif
                    </span>
                    <span class="font-bold text-slate-900">{{ \App\Models\ElectricityRate::count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                    <span class="text-sm text-slate-600 font-medium flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-slate-400"></i>
                        Total Admin
                    </span>
                    <span class="font-bold text-slate-900">{{ \App\Models\User::where('role','admin')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
