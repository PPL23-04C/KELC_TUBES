@extends('layouts.app')

@section('title', 'Riwayat')
@section('page-title', 'Riwayat Analisis')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Riwayat Penggunaan</h1>
            <p class="text-slate-500 mt-1 text-sm">Lihat log historis pencatatan pemakaian listrik Anda.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 mb-8">
        <form method="GET" action="{{ route('history.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
            
            <div class="flex-1 space-y-2">
                <label for="start_date" class="block text-sm font-semibold text-slate-700">Dari Tanggal</label>
                <div class="relative">
                          <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}"
                              aria-describedby="start_date_help"
                              title="Pilih tanggal mulai"
                              class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    <span id="start_date_help" class="sr-only">Dari tanggal (mulai)</span>
                </div>
            </div>

            <div class="flex-1 space-y-2">
                <label for="end_date" class="block text-sm font-semibold text-slate-700">Sampai Tanggal</label>
                <div class="relative">
                          <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}"
                              aria-describedby="end_date_help"
                              title="Pilih tanggal akhir"
                              class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    <span id="end_date_help" class="sr-only">Sampai tanggal (akhir)</span>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-4 md:mt-0">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 shadow-sm shadow-blue-600/20 transition-all flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Terapkan
                </button>
                
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('history.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-200 transition-all" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Perangkat</th>
                        <th class="px-6 py-4 font-semibold">Durasi (Jam)</th>
                        <th class="px-6 py-4 font-semibold">Konsumsi (kWh)</th>
                        <th class="px-6 py-4 font-semibold">Estimasi Biaya</th>
                        <th class="px-6 py-4 font-semibold">Emisi CO2</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    <span class="font-medium text-slate-700">{{ $log->tanggal->format('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                {{ $log->device?->nama_device ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ number_format($log->jam_pemakaian) }} <span class="text-xs"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg font-semibold text-xs">
                                    <i data-lucide="zap" class="w-3 h-3"></i>
                                    {{ number_format($log->total_kwh, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-emerald-600 font-semibold">
                                Rp {{ number_format(optional($log->billing)->estimasi_biaya ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg font-semibold text-xs">
                                    <i data-lucide="cloud" class="w-3 h-3"></i>
                                    {{ number_format(optional($log->co2Impact)->emisi_co2 ?? 0, 2) }} kg
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                        <i data-lucide="history" class="w-8 h-8"></i>
                                    </div>
                                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Belum ada riwayat</h3>
                                    <p class="text-sm text-slate-500 max-w-sm">Anda belum memiliki catatan analisis penggunaan atau filter yang Anda cari tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection