@extends('layouts.app')

@section('title', 'Edit Tarif Listrik')
@section('page-title', 'Edit Tarif Listrik')

@section('content')
    <div class="device-header">
        <div class="meta">Ubah data tarif listrik daya {{ $rate->daya_va }} VA</div>
        <a class="btn secondary" href="{{ route('admin.electricity_rates.index') }}">← Kembali</a>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('admin.electricity_rates.update', $rate) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="daya_va">Daya (VA)</label>
                <input id="daya_va" type="number" name="daya_va" value="{{ old('daya_va', $rate->daya_va) }}" required>
                @error('daya_va')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="tarif_per_kwh">Tarif per kWh (Rp)</label>
                <input id="tarif_per_kwh" type="text" name="tarif_per_kwh" value="{{ old('tarif_per_kwh', $rate->tarif_per_kwh) }}" required>
                @error('tarif_per_kwh')<div style="color: var(--danger); font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button class="btn" type="submit">💾 Simpan</button>
                <a class="btn secondary" href="{{ route('admin.electricity_rates.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
