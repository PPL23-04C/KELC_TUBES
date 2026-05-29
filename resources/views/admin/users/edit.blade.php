@extends('layouts.app')

@section('title', 'Ubah User')
@section('page-title', 'Ubah User')

@section('content')
    <div class="device-header">
        <div class="meta">Edit data user: {{ $user->name }}</div>
        <a class="btn secondary" href="{{ route('admin.users.index') }}">← Kembali</a>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password">Password (kosongkan jika tidak berubah)</label>
                <input id="password" type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengganti">
                @error('password')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation">
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User Biasa</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button class="btn" type="submit">💾 Simpan</button>
                <a class="btn secondary" href="{{ route('admin.users.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
