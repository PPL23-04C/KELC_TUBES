@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Profile Header Card -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 mb-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -bottom-10 -right-6 text-slate-700/30">
            <i data-lucide="user-circle" class="w-48 h-48"></i>
        </div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 flex items-center justify-center text-3xl font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ auth()->user()->name }}</h2>
                <p class="text-slate-300 text-sm mt-0.5">{{ auth()->user()->email }}</p>
                <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-blue-500/20 border border-blue-400/20 text-blue-300 rounded-full text-xs font-semibold uppercase tracking-wider">
                    <i data-lucide="zap" class="w-3 h-3"></i>
                    {{ auth()->user()->daya_va }} VA
                </span>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Edit Informasi Profil</h3>
                <p class="text-xs text-slate-500">Perbarui data akun dan preferensi daya Anda</p>
            </div>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl mb-6">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                <p class="text-sm text-emerald-700 font-medium">Profil berhasil diperbarui.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="daya_va" class="block text-sm font-semibold text-slate-700">Daya Listrik Rumah (VA)</label>
                <div class="relative">
                        <select id="daya_va" name="daya_va" required
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        @foreach([450, 900, 1300, 2200, 3500] as $va)
                            <option value="{{ $va }}" {{ (int) old('daya_va', $user->daya_va) === $va ? 'selected' : '' }}>
                                {{ $va }} VA{{ $va === 3500 ? '+' : '' }}
                            </option>
                        @endforeach
                    </select>
                    
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5 space-y-5">
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">Ganti Password <span class="text-slate-400 font-normal">(kosongkan jika tidak ingin mengubah)</span></h4>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password Baru</label>
                            <input id="password" type="password" name="password"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('password') border-red-400 @enderror"
                                   placeholder="Minimal 6 karakter">
                            @error('password')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium"
                                   placeholder="Ketik ulang password baru">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="flex-1 py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
