@extends('layouts.app')

@section('title', 'Riwayat')
@section('page-title', 'Riwayat Analisis')

@section('content')
    <div class="form-card" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('history.index') }}" 
              style="display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); align-items: end;">
            
            <div class="form-group">
                <label for="start_date">Dari</label>
                <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}">
            </div>

            <div class="form-group">
                <label for="end_date">Sampai</label>
                <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <button class="btn" type="submit">Filter</button>

            @if(request('start_date') || request('end_date'))
                <a href="{{ route('history.index') }}" class="btn" style="background: #ccc;">
                    Reset
                </a>
            @endif

        </form>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Perangkat</th>
                    <th>Jam</th>
                    <th>Total kWh</th>
                    <th>Estimasi Biaya</th>
                    <th>Emisi CO2</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->tanggal->format('d M Y') }}</td>
                        <td>{{ $log->device?->nama_device }}</td>
                        <td>{{ number_format($log->jam_pemakaian, 2) }}</td>
                        <td>{{ number_format($log->total_kwh, 2) }} kWh</td>
                        <td>Rp {{ number_format(optional($log->billing)->estimasi_biaya ?? 0, 2, ',', '.') }}</td>
                        <td>{{ number_format(optional($log->co2Impact)->emisi_co2 ?? 0, 2) }} kg</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection