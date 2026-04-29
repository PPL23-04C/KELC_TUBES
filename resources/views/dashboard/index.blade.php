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
        <div class="card">
            <div class="chip">Emisi</div>
            <h3>Emisi CO2/bulan</h3>
            <div class="value">{{ number_format($totalCo2, 2) }} kg</div>
        </div>
    </div>

 
    <div class="card" style="margin-top: 24px;">
        <h3>Grafik Penggunaan (7 Hari)</h3>
        <canvas id="usageChart" height="120"></canvas>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h3>Tips Hemat Energi</h3>
        <ul style="margin: 0; padding-left: 18px; color: var(--muted);">
            @foreach($tips as $tip)
                <li>{{ $tip }}</li>
            @endforeach
        </ul>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartPayload = @json($chartData);
        const labels = chartPayload.map(item => item.label);
        const values = chartPayload.map(item => item.value);

        const ctx = document.getElementById('usageChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'kWh',
                    data: values,
                    backgroundColor: '#2d6cdf',
                    borderRadius: 8,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 2 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endpush
