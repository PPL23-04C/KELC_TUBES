@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-medium text-sm mb-6 transition-colors group">
        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center group-hover:bg-slate-50 transition-colors shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </div>
        Kembali ke Daftar User
    </a>

    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Buat Akun Pengguna Baru</h3>
                <p class="text-xs text-slate-500">Isi form di bawah untuk membuat akun baru</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Masukkan nama lengkap"
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       placeholder="contoh@email.com"
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" type="password" name="password" required
                           placeholder="Minimal 8 karakter"
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           placeholder="Ketik ulang password"
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                </div>
            </div>

            <div class="space-y-2">
                <label for="role" class="block text-sm font-semibold text-slate-700">Role Pengguna</label>
                <div class="relative">
                        <select id="role" name="role" required
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User Biasa</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    
                </div>
            </div>

            <div id="va_select_group" class="space-y-2 mt-2" style="{{ old('role') === 'user' ? 'display:block;' : 'display:none;' }}">
                <label for="daya_va" class="block text-sm font-semibold text-slate-700">Daya Listrik Rumah (VA)</label>
                <select id="daya_va" name="daya_va" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                    <option value="450" {{ old('daya_va') == '450' ? 'selected' : '' }}>450 VA</option>
                    <option value="900" {{ old('daya_va') == '900' ? 'selected' : '' }}>900 VA</option>
                    <option value="1300" {{ old('daya_va', '1300') == '1300' ? 'selected' : '' }}>1300 VA</option>
                    <option value="2200" {{ old('daya_va') == '2200' ? 'selected' : '' }}>2200 VA</option>
                    <option value="3500" {{ old('daya_va') == '3500' ? 'selected' : '' }}>3500 VA+</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100 mt-6">
                <button type="submit"
                    class="flex-1 py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan User
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
