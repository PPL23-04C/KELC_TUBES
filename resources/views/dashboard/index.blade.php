@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if(!empty($alert['has_spike']))
        <div class="alert">{{ $alert['message'] }}</div>
    @endif

    <div class="grid grid-3" style="margin-top: 20px;">
        <div class="card">
            <div class="chip">Total</div>
            <h3>Total Penggunaan/bulan</h3>
            <div class="value">{{ number_format($totalKwh, 2) }} kWh</div>
        </div>
        <div class="card">
            <div class="chip">Estimasi</div>
            <h3>Estimasi Biaya/bulan</h3>
            <div class="value">Rp {{ number_format($totalCost, 2, ',', '.') }}</div>
        </div>
        
        
    </div>

    <div class="grid grid-2" style="margin-top: 20px;">
        <div class="card">
            <h3>Total Penggunaan Hari Ini</h3>
            <div class="value">{{ number_format($todayKwh, 2) }} kWh</div>
            @php($level = $alert['level'] ?? 'hemat')
            <div style="margin-top: 10px;">
                <span class="status-pill {{ $level }}">
                    Status: {{ strtoupper($level) }}
                </span>
            </div>
        </div>
        <div class="card">
            <h3>Total Penggunaan Minggu Ini</h3>
            <div class="value">{{ number_format($weekKwh, 2) }} kWh</div>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h3>Grafik Penggunaan (7 Hari)</h3>
        <canvas id="usageChart" height="120"></canvas>
    </div>

@endsection
