@extends('layouts.app')

@section('title', 'Tambah Perangkat')
@section('page-title', 'Tambah Perangkat')

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('devices.store') }}">
            @csrf
            <div class="form-group">
                <label for="nama_device">Nama Perangkat</label>
                <input id="nama_device" name="nama_device" value="{{ old('nama_device') }}" required>
            </div>
            <div class="form-group">
                <label for="daya_watt">Daya (Watt)</label>
                <input id="daya_watt" type="number" name="daya_watt" min="1" value="{{ old('daya_watt') }}" required>
            </div>
            <div class="form-group">
                <label for="jumlah_unit">Jumlah Unit</label>
                <input id="jumlah_unit" type="number" name="jumlah_unit" min="1" value="{{ old('jumlah_unit', 1) }}" required>
            </div>
            <button class="btn" type="submit">Simpan</button>
            <a class="btn secondary" href="{{ route('devices.index') }}">Batal</a>
        </form>
    </div>
@endsection
