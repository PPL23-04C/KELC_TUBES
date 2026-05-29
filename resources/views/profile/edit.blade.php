@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Profil Pengguna')

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nama</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group">
                <label for="daya_va">Daya Listrik (VA)</label>
                <select id="daya_va" name="daya_va" required>
                    @foreach([450, 900, 1300, 2200, 3500] as $va)
                        <option value="{{ $va }}" {{ (int) old('daya_va', $user->daya_va) === $va ? 'selected' : '' }}>
                            {{ $va }} VA{{ $va === 3500 ? '+' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password Baru (opsional)</label>
                <input id="password" type="password" name="password">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation">
            </div>
            <button class="btn" type="submit">Simpan Perubahan</button>
        </form>
    </div>
@endsection
