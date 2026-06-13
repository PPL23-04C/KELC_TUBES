@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-slate-500 mt-1 text-sm">Berikut adalah ringkasan konsumsi energi Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('devices.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Perangkat
            </a>
            <a href="{{ route('analysis.input') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20">
                <i data-lucide="bar-chart" class="w-4 h-4"></i>
                Input Analisis
            </a>
        </div>
    </div>

    <!-- Spike Alert -->
    @if(!empty($alert['has_spike']))
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3 shadow-sm">
            <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-semibold text-amber-800 text-sm">Peringatan Lonjakan Konsumsi</h4>
                <p class="text-amber-700 mt-1 text-sm">{!! $alert['message'] !!}</p>
            </div>
        </div>
    @endif

    <!-- Hero Saving Banner -->
    @if(auth()->user()->target_hemat !== null)
        <div id="w2s0ed" class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 mb-8 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
                <i data-lucide="leaf" class="w-40 h-40 text-emerald-600"></i>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 relative z-10">
                <!-- Left: Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                            <i data-lucide="target" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Target Penghematan Bulan Ini</h3>
                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider {{ $savingStatus === 'tercapai' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $savingStatus === 'tercapai' ? 'On Track' : 'Melebihi' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Target Maksimal</div>
                            <div class="text-xl font-bold text-slate-800">{{ round($targetKwh) }} <span class="text-sm font-medium text-slate-500">kWh</span></div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Batas Boros</div>
                            <div class="text-xl font-bold text-slate-800">{{ round($batasBoros) }} <span class="text-sm font-medium text-slate-500">kWh</span></div>
                        </div>
                    </div>
                </div>

                <!-- Right: Progress -->
                <div class="flex-[1.2] flex flex-col justify-center bg-slate-50/50 rounded-3xl p-6 border border-slate-100">
                    <div class="flex justify-between text-sm font-medium mb-3">
                        <span class="text-slate-500">Realisasi Pemakaian</span>
                        <span class="text-slate-800 font-bold">{{ number_format($monthUsage, 1) }} / {{ round($targetKwh) }} kWh</span>
                    </div>

                    @php
                        $percentage = $targetKwh > 0 ? min(($monthUsage / $targetKwh) * 100, 100) : 0;
                        $barColor = $percentage > 100 ? 'bg-red-500' : ($percentage > 80 ? 'bg-amber-500' : 'bg-emerald-500');
                        $glowColor = $percentage > 100 ? 'shadow-red-500/50' : ($percentage > 80 ? 'shadow-amber-500/50' : 'shadow-emerald-500/50');
                    @endphp

                    <div class="h-4 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full {{ $barColor }} rounded-full transition-all duration-1000 ease-out {{ $glowColor }} shadow-[0_0_10px_rgba(0,0,0,0.2)]" style="width: {{ $percentage }}%;"></div>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <div class="text-xs font-semibold text-slate-400">
                            {{ round($percentage) }}% dari target
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('saving-target.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">Kelola Target</a>
                            <a href="{{ route('recommendations.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                Lihat Tips <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-8 border border-blue-100/50 shadow-sm mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h3 class="text-xl font-bold text-blue-900 flex items-center gap-2 mb-2">
                    <i data-lucide="crosshair" class="w-6 h-6 text-blue-600"></i>
                    Mulai Hemat Energi Hari Ini!
                </h3>
                <p class="text-blue-700/80 text-sm max-w-xl">
                    Tetapkan target pemakaian listrik bulanan Anda (1% - 50%) untuk mengendalikan pengeluaran dan memantau status efisiensi energi.
                </p>
            </div>
            <a href="{{ route('saving-target.index') }}" class="whitespace-nowrap px-6 py-3 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                Mulai Berhemat
            </a>
        </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Hari ini -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                    <i data-lucide="zap" class="w-6 h-6"></i>
                </div>
                @php($level = $alert['level'] ?? 'hemat')
                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider {{ $level == 'boros' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                    {{ $level }}
                </span>
            </div>
            <div class="text-sm font-medium text-slate-500 mb-1">Penggunaan Hari Ini</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($todayKwh, 2) }} <span class="text-sm font-medium text-slate-500">kWh</span></div>
        </div>

        <!-- Bulan ini -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="text-sm font-medium text-slate-500 mb-1">Total Bulan Ini</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalKwh, 2) }} <span class="text-sm font-medium text-slate-500">kWh</span></div>
        </div>

        <!-- Estimasi Biaya -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="text-sm font-medium text-slate-500 mb-1">Estimasi Biaya</div>
            <div class="text-2xl font-bold text-slate-800 text-emerald-600"><span class="text-sm font-medium">Rp</span> {{ number_format($totalCost, 0, ',', '.') }}</div>
        </div>

        <!-- Emisi CO2 -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-slate-50 text-slate-600 rounded-2xl">
                    <i data-lucide="cloud" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="text-sm font-medium text-slate-500 mb-1">Emisi CO2</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalCo2, 2) }} <span class="text-sm font-medium text-slate-500">kg</span></div>
        </div>
    </div>

    <!-- Secondary Section: Graph & Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-3 bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Grafik Penggunaan (7 Hari)</h3>
                <div class="px-3 py-1 bg-slate-50 text-slate-500 text-xs font-semibold rounded-lg border border-slate-100">Minggu Ini</div>
            </div>

            @if(count($chartData) > 0)
                <div class="relative h-[280px] w-full">
                    <canvas id="usageChart"></canvas>
                </div>
            @else
                <div class="h-[280px] w-full flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <i data-lucide="bar-chart-2" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Belum ada data konsumsi minggu ini ⚡</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">Mulai tambahkan perangkat atau input analisis untuk melihat grafik.</p>
                </div>
            @endif
        </div>

        <!-- Tips / Insight removed per request -->
    </div>

@endsection

@push('scripts')
    @if(count($chartData) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartPayload = @json($chartData);
        const labels = chartPayload.map(item => item.label);
        const values = chartPayload.map(item => item.value);

        const ctx = document.getElementById('usageChart');

        // Gradient for bars
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, '#3b82f6'); // blue-500
        gradient.addColorStop(1, '#60a5fa'); // blue-400

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Konsumsi (kWh)',
                    data: values,
                    backgroundColor: gradient,
                    borderRadius: 8,
                    barThickness: 'flex',
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9', // slate-100
                            drawBorder: false,
                        },
                        ticks: {
                            stepSize: 2,
                            color: '#94a3b8', // slate-400
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#64748b', // slate-500
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '500'
                            }
                        },
                        border: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { family: "'Inter', sans-serif", size: 13, weight: '600' },
                        bodyFont: { family: "'Inter', sans-serif", size: 12 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' kWh';
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    </script>
    @endif
@endpush
