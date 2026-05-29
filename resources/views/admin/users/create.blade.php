@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
    <div class="device-header">
        <div class="meta">Buat akun user baru</div>
        <a class="btn secondary" href="{{ route('admin.users.index') }}">← Kembali</a>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                @error('name')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com">
                @error('email')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required placeholder="Minimal 6 karakter">
                @error('password')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ketik ulang password">
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User Biasa</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button class="btn" type="submit">💾 Simpan</button>
                <a class="btn secondary" href="{{ route('admin.users.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
