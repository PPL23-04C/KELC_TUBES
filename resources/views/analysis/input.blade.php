@extends('layouts.app')

@section('title', 'Input Analisis')
@section('page-title', 'Input Analisis')

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('analysis.store') }}">
            @csrf
            <div class="form-group">
                <label for="device_id">Pilih Perangkat</label>
                <select id="device_id" name="device_id" required>
                    <option value="">-- Pilih --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                            {{ $device->nama_device }} ({{ $device->daya_watt }}W)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="jam_pemakaian">Jam Pemakaian (jam)</label>
                <input id="jam_pemakaian" type="number" step="0.1" name="jam_pemakaian" value="{{ old('jam_pemakaian') }}" required>
            </div>
            <button class="btn" type="submit">Simpan Analisis</button>
        </form>
    </div>
@endsection
