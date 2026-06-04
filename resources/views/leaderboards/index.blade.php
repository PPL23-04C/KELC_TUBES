@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard Hemat Energi')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Top Pengguna Hemat Energi</h1>
            <p class="text-slate-500 mt-1 text-sm">Bandingkan tingkat penghematan listrik Anda dengan pengguna lainnya.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 mb-8">
        <form method="GET" action="{{ route('leaderboards.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1 space-y-2">
                <label for="month" class="block text-sm font-semibold text-slate-700">Pilih Bulan</label>
                <div class="relative">
                    <select id="month" name="month" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-2.5 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}" {{ $selectedMonth === $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                    
                </div>
            </div>

            <div class="flex-1 space-y-2">
                <label for="daya_va" class="block text-sm font-semibold text-slate-700">Jenis VA</label>
                <div class="relative">
                    <select id="daya_va" name="daya_va" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-2.5 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        <option value="all" {{ $selectedVa === 'all' ? 'selected' : '' }}>Semua VA</option>
                        @foreach($availableVas as $va)
                            <option value="{{ $va }}" {{ (string) $selectedVa === (string) $va ? 'selected' : '' }}>{{ $va }} VA</option>
                        @endforeach
                    </select>
                    
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Leaderboard Header & User Position -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                {{ $monthLabel }}
            </div>
            <h3 class="text-xl font-bold text-slate-900">Top 10 Pengguna Paling Hemat</h3>
            <p class="text-sm text-slate-500 mt-1">Filter aktif: <span class="font-semibold text-slate-700">{{ $selectedVaLabel }}</span></p>
        </div>

        <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20 min-w-[280px] flex items-center gap-5">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <div class="text-blue-100 text-xs font-semibold uppercase tracking-wider mb-1">Posisi Anda Saat Ini</div>
                @if($currentUserTotal)
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold">#{{ $currentUserTotal['rank'] }}</span>
                        <span class="text-blue-50 font-medium text-sm border-l border-white/20 pl-2">{{ number_format($currentUserTotal['total_kwh'], 2) }} kWh</span>
                    </div>
                @else
                    <div class="text-sm font-medium mt-1">Belum ada data untuk filter ini.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    @if($leaderboard->isEmpty())
        <div class="bg-slate-50 border border-slate-200 border-dashed rounded-3xl p-12 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-sm">
                <i data-lucide="trophy" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Data</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">Belum ada data konsumsi pada bulan ini untuk filter yang Anda pilih.</p>
        </div>
    @else
        <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100 tracking-wider">
                        <tr>
                            <th class="px-6 py-5 font-semibold w-24 text-center">Peringkat</th>
                            <th class="px-6 py-5 font-semibold">Nama Pengguna</th>
                            <th class="px-6 py-5 font-semibold">Jenis VA</th>
                            <th class="px-6 py-5 font-semibold text-right">Total Konsumsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($leaderboard as $entry)
                            @php
                                $isCurrentUser = $entry['user_id'] === auth()->id();
                                $rank = $entry['rank'];
                                
                                $rankStyle = '';
                                $medalIcon = '';
                                
                                if ($rank == 1) {
                                    $rankStyle = 'text-amber-500 bg-amber-50';
                                    $medalIcon = '🥇';
                                } elseif ($rank == 2) {
                                    $rankStyle = 'text-slate-500 bg-slate-100';
                                    $medalIcon = '🥈';
                                } elseif ($rank == 3) {
                                    $rankStyle = 'text-orange-700 bg-orange-50';
                                    $medalIcon = '🥉';
                                } else {
                                    $rankStyle = 'text-slate-600 bg-slate-50';
                                }
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/50 {{ $isCurrentUser ? 'bg-blue-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-base {{ $rankStyle }}">
                                            @if($rank <= 3)
                                                <span class="text-lg">{{ $medalIcon }}</span>
                                            @else
                                                {{ $rank }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold uppercase shrink-0">
                                            {{ substr($entry['user']['name'] ?? 'P', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                                {{ $entry['user']['name'] ?? 'Pengguna' }}
                                                @if($isCurrentUser)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold">
                                        {{ $entry['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-slate-900 text-base">{{ number_format($entry['total_kwh'], 2) }} <span class="text-xs text-slate-500 font-medium">kWh</span></div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection