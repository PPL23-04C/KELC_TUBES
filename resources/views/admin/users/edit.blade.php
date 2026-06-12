@extends('layouts.app')

@section('title', 'Ubah User')
@section('page-title', 'Edit Data Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-medium text-sm mb-6 transition-colors group">
        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center group-hover:bg-slate-50 transition-colors shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </div>
        Kembali ke Daftar User
    </a>

    <!-- User Info Banner -->
    <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-slate-200 flex items-center justify-center text-slate-700 font-bold text-lg uppercase shrink-0">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div>
            <p class="font-bold text-slate-900">{{ $user->name }}</p>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
        </div>
        <div class="ml-auto">
            @if($user->role === 'admin')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-xs font-bold">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    Admin
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    User
                </span>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <i data-lucide="pencil" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Edit Data Pengguna</h3>
                <p class="text-xs text-slate-500">Ubah informasi akun pengguna ini</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
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

            <div class="border-t border-slate-100 pt-5 space-y-4">
                <h4 class="text-sm font-semibold text-slate-700">Ganti Password <span class="text-slate-400 font-normal">(kosongkan jika tidak diubah)</span></h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password Baru</label>
                        <input id="password" type="password" name="password"
                               placeholder="Biarkan kosong jika tidak diganti"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               placeholder="Ketik ulang password baru"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label for="role" class="block text-sm font-semibold text-slate-700">Role Pengguna</label>
                <div class="relative">
                        <select id="role" name="role" required
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User Biasa</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    
                </div>
            </div>

            <div id="va_select_group" class="space-y-2 mt-2" style="{{ $user->role === 'user' ? 'display:block;' : 'display:none;' }}">
                <label for="daya_va" class="block text-sm font-semibold text-slate-700">Daya Listrik Rumah (VA)</label>
                <select id="daya_va" name="daya_va" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                    <option value="450" {{ (int) old('daya_va', $user->daya_va) === 450 ? 'selected' : '' }}>450 VA</option>
                    <option value="900" {{ (int) old('daya_va', $user->daya_va) === 900 ? 'selected' : '' }}>900 VA</option>
                    <option value="1300" {{ (int) old('daya_va', $user->daya_va) === 1300 ? 'selected' : '' }}>1300 VA</option>
                    <option value="2200" {{ (int) old('daya_va', $user->daya_va) === 2200 ? 'selected' : '' }}>2200 VA</option>
                    <option value="3500" {{ (int) old('daya_va', $user->daya_va) === 3500 ? 'selected' : '' }}>3500 VA+</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100 mt-6">
                <button type="submit"
                        class="flex-1 py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="flex-1 py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const vaGroup = document.getElementById('va_select_group');
    function toggleVa() {
        if (!roleSelect || !vaGroup) return;
        if (roleSelect.value === 'user') {
            vaGroup.style.display = 'block';
            vaGroup.querySelector('select').setAttribute('required','required');
        } else {
            vaGroup.style.display = 'none';
            vaGroup.querySelector('select').removeAttribute('required');
        }
    }
    toggleVa();
    roleSelect.addEventListener('change', toggleVa);
});
</script>
@endpush
