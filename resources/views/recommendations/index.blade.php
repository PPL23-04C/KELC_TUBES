@extends('layouts.app')

@section('title', 'Rekomendasi')
@section('page-title', 'Rekomendasi Hemat Listrik')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Rekomendasi Hemat Listrik</h1>
            <p class="text-slate-500 mt-2">Tips hemat listrik berdasarkan analisis perangkat Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- ===== ACHIEVEMENT ICON BUTTON ===== --}}
            <button id="achievement-btn" onclick="openAchievementModal()"
                class="relative flex flex-col items-center justify-center gap-0.5 w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-md hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none overflow-hidden"
                title="Lihat Achievement">
                {{-- Shimmer --}}
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-200%] achievement-btn-shimmer pointer-events-none"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span class="text-white/90 text-[9px] font-bold tracking-wide uppercase leading-none">Badge</span>
                {{-- Notif badge: angka achievement baru yg belum dilihat --}}
                <span id="achievement-badge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full items-center justify-center hidden shadow-sm border border-white">0</span>
            </button>
            <div class="text-right bg-slate-50 px-4 py-3 rounded-lg border border-slate-200">
                <div class="text-sm font-semibold text-slate-900">{{ $dayaVa }} VA</div>
                <div class="text-xs text-slate-600 mt-1">Tarif: Rp {{ number_format($tariff, 2) }}/kWh</div>
                <div class="text-xs text-slate-600">Batas: {{ number_format($hematMax, 1) }}–{{ number_format($sedangMax, 1) }} kWh/bulan</div>
            </div>
        </div>
    </div>

    {{-- ===== ACHIEVEMENT MODAL ===== --}}
    <div id="achievement-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true" aria-labelledby="achievement-modal-title">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAchievementModal()"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[88vh] flex flex-col overflow-hidden achievement-modal-panel">

            {{-- Header --}}
            <div class="bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 px-6 pt-6 pb-5 shrink-0 relative overflow-hidden">
                {{-- decorative circles --}}
                <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute top-8 -right-2 w-12 h-12 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-2xl shadow-inner">🏆</div>
                        <div>
                            <h2 id="achievement-modal-title" class="text-white font-bold text-lg leading-tight">Koleksi Achievement</h2>
                            <p class="text-amber-100 text-xs mt-0.5">Selesaikan tips untuk membuka penghargaan</p>
                        </div>
                    </div>
                    <button onclick="closeAchievementModal()" class="w-8 h-8 bg-white/20 hover:bg-white/35 rounded-full flex items-center justify-center text-white transition-colors focus:outline-none shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Stats row --}}
                <div class="flex gap-3 mt-4 relative z-10">
                    <div class="flex-1 bg-white/15 backdrop-blur rounded-xl px-3 py-2 text-center">
                        <div id="ach-stat-unlocked" class="text-white font-bold text-lg leading-none">0</div>
                        <div class="text-amber-100 text-[10px] mt-0.5">Terbuka</div>
                    </div>
                    <div class="flex-1 bg-white/15 backdrop-blur rounded-xl px-3 py-2 text-center">
                        <div id="ach-stat-total" class="text-white font-bold text-lg leading-none">0</div>
                        <div class="text-amber-100 text-[10px] mt-0.5">Total</div>
                    </div>
                    <div class="flex-1 bg-white/15 backdrop-blur rounded-xl px-3 py-2 text-center">
                        <div id="ach-stat-pct" class="text-white font-bold text-lg leading-none">0%</div>
                        <div class="text-amber-100 text-[10px] mt-0.5">Progress</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="mt-3 relative z-10">
                    <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                        <div id="ach-progress-bar" class="h-full bg-white rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            {{-- Filter tabs --}}
            <div class="flex gap-1 px-4 py-2.5 border-b border-slate-100 bg-slate-50 shrink-0 overflow-x-auto">
                <button onclick="filterAch('all')" data-filter="all" class="ach-filter-btn active shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150">Semua</button>
                <button onclick="filterAch('unlocked')" data-filter="unlocked" class="ach-filter-btn shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150">✅ Terbuka</button>
                <button onclick="filterAch('locked')" data-filter="locked" class="ach-filter-btn shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150">🔒 Terkunci</button>
                <button onclick="filterAch('new')" data-filter="new" class="ach-filter-btn shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 relative" id="filter-new-btn">
                    🆕 Baru
                    <span id="filter-new-dot" class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full hidden"></span>
                </button>
            </div>

            {{-- Achievement list --}}
            <div class="overflow-y-auto flex-1 p-4">
                <div id="achievement-list" class="space-y-2.5">
                    {{-- Diisi oleh JavaScript --}}
                </div>
                <div id="ach-empty" class="hidden text-center py-10 text-slate-400 text-sm">Tidak ada achievement di kategori ini</div>
            </div>
        </div>
    </div>
    {{-- ===== END ACHIEVEMENT MODAL ===== --}}

    {{-- ===== BANNER BELUM ADA PERANGKAT ===== --}}
    @if(!$hasDevice)
    <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 mb-8 text-white shadow-lg">
        <div class="w-16 h-16 shrink-0 bg-white/10 rounded-full flex items-center justify-center">
            <i data-lucide="plug-zap" class="w-8 h-8 text-white"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-bold mb-1">Belum Ada Perangkat Terdaftar</h2>
            <p class="text-blue-100 mb-4 leading-relaxed">Tambahkan perangkat listrik Anda agar WattCare dapat menganalisis penggunaan dan memberikan tips hemat yang relevan.</p>
            <a href="{{ route('devices.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-blue-700 font-semibold rounded-xl hover:bg-white/90 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Perangkat
            </a>
        </div>
    </div>
    @else

    {{-- ===== BANNER STATUS BULANAN ===== --}}
    @if($usageStatus === 'hemat')
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 shrink-0 bg-white/10 rounded-full flex items-center justify-center">
                <i data-lucide="trending-down" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">Luar Biasa! Konsumsi Listrik Anda Sangat Hemat 🎉</h2>
                <p class="text-emerald-50 text-sm mt-1">Pertahankan kebiasaan baik ini. Anda telah berkontribusi besar terhadap penghematan energi.</p>
            </div>
            <div class="ml-auto inline-flex items-center gap-3 bg-white/10 px-3 py-2 rounded-lg text-sm font-medium">
                <span>Total: {{ number_format($monthUsage, 2) }} kWh</span>
                <span class="text-slate-200">•</span>
                <span>Batas Hemat: < {{ number_format($hematMax, 1) }} kWh</span>
            </div>
        </div>
    </div>
    @elseif($usageStatus === 'sedang')
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 shrink-0 bg-white/10 rounded-full flex items-center justify-center">
                <i data-lucide="minus" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">Konsumsi Listrik Anda Cukup, Masih Bisa Lebih Hemat!</h2>
                <p class="text-amber-50 text-sm mt-1">Penggunaan Anda dalam kategori sedang. Lihat tips per perangkat di bawah untuk mengurangi konsumsi.</p>
            </div>
            <div class="ml-auto inline-flex items-center gap-3 bg-white/10 px-3 py-2 rounded-lg text-sm font-medium">
                <span>Total: {{ number_format($monthUsage, 2) }} kWh</span>
                <span class="text-slate-200">•</span>
                <span>Batas Sedang: ≤ {{ number_format($sedangMax, 1) }} kWh</span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 shrink-0 bg-white/10 rounded-full flex items-center justify-center">
                <i data-lucide="trending-up" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">Peringatan! Konsumsi Listrik Anda Terlalu Boros</h2>
                <p class="text-red-100 text-sm mt-1">Penggunaan bulan ini melebihi batas wajar. Segera terapkan tips per perangkat di bawah ini.</p>
            </div>
            <div class="ml-auto inline-flex items-center gap-3 bg-white/10 px-3 py-2 rounded-lg text-sm font-medium">
                <span>Total: {{ number_format($monthUsage, 2) }} kWh</span>
                <span class="text-slate-200">•</span>
                <span>Batas Wajar: ≤ {{ number_format($sedangMax, 1) }} kWh</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== TIPS PER PERANGKAT ===== --}}
    @if($deviceTips->isEmpty())
    <div class="bg-slate-50 border border-slate-200 border-dashed rounded-3xl p-8 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 text-slate-400 shadow-sm">
            <i data-lucide="pie-chart" class="w-8 h-8"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada data kalkulasi bulan ini</h3>
        <p class="text-slate-500 max-w-md mb-6">Pergi ke halaman Analisis dan masukkan data pemakaian perangkat Anda terlebih dahulu agar tips dapat ditampilkan.</p>
            <a href="{{ route('analysis.input') }}" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-500 transition-colors">
            Input Analisis
        </a>
    </div>
    @else

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-slate-900">Tips Hemat per Perangkat</h3>
        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $deviceTips->count() }} Perangkat</span>
    </div>

    <div class="space-y-4">
        @foreach($deviceTips as $i => $device)
        @php
            if ($device['persen'] >= 40)      { $color = 'red'; }
            elseif ($device['persen'] >= 25)  { $color = 'orange'; }
            elseif ($device['persen'] >= 15)  { $color = 'yellow'; }
            elseif ($device['persen'] >= 5)   { $color = 'blue'; }
            else                              { $color = 'green'; }

            $bgClass = [
                'red' => 'bg-red-50 text-red-600 border-red-100',
                'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
                'yellow' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
                'green' => 'bg-emerald-50 text-emerald-600 border-emerald-100'
            ][$color];

            $barClass = [
                'red' => 'bg-red-500',
                'orange' => 'bg-orange-500',
                'yellow' => 'bg-yellow-500',
                'blue' => 'bg-blue-500',
                'green' => 'bg-emerald-500'
            ][$color];
        @endphp
        
        <div x-data="{
                open: {{ $i === 0 ? 'true' : 'false' }},
                simulatorOpen: false,
                currentHours: {{ $device['avg_jam'] }},
                simulatedHours: {{ $device['avg_jam'] }},
                watt: {{ $device['daya_watt'] }},
                tariff: {{ $tariff }},
                co2Factor: {{ $co2Factor }},
                currentKwh() { return (this.watt * this.currentHours * 30) / 1000; },
                simulatedKwh() { return (this.watt * this.simulatedHours * 30) / 1000; },
                currentCost() { return this.currentKwh() * this.tariff; },
                simulatedCost() { return this.simulatedKwh() * this.tariff; },
                currentCo2() { return this.currentKwh() * this.co2Factor; },
                simulatedCo2() { return this.simulatedKwh() * this.co2Factor; },
                diffKwh() { return this.currentKwh() - this.simulatedKwh(); },
                diffCost() { return this.currentCost() - this.simulatedCost(); },
                diffCo2() { return this.currentCo2() - this.simulatedCo2(); },
                percentChange() { return this.currentKwh() ? (this.diffKwh() / this.currentKwh()) * 100 : 0; },
                formatNumber(value, decimals = 1) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(value); },
                formatCurrency(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value); },
            }" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-[0_2px_10px_rgb(0,0,0,0.02)] hover:border-slate-300 transition-colors">
            <!-- Header -->
            <button @click="open = !open" class="w-full flex items-center gap-4 p-4 md:p-5 text-left bg-white hover:bg-slate-50 transition-colors focus:outline-none">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0 border {{ $bgClass }}">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-slate-900 truncate mb-0">{{ $device['nama'] }}</h4>
                    <p class="text-xs text-slate-500 truncate mt-1">
                        {{ $device['daya_watt'] }}W @if($device['avg_jam'] > 0) • Avg {{ $device['avg_jam'] }} jam/hari @endif • {{ $device['jumlah_unit'] }} Unit
                    </p>
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white {{ $barClass }}">
                            {{ $device['kwh_bulan'] }} kWh
                        </div>
                        <div class="px-2 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                            {{ $device['persen'] }}%
                        </div>
                        <div class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 tips-counter" data-device-id="{{ $device['device_id'] }}">
                            <span class="tips-completed">0</span>/<span class="tips-total">{{ count($device['tips']) }}</span>
                        </div>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 bg-slate-50 transition-transform duration-200" :class="{ 'rotate-180 bg-slate-100 text-slate-600': open }" aria-hidden="true">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            <span class="sr-only">Toggle detail</span>
                        </div>
                    </div>
                </div>
            </button>

            <!-- Body (Accordion) -->
            <div x-show="open" x-collapse>
                <div class="p-5 border-t border-slate-100 bg-slate-50/50">
                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between text-xs text-slate-500 mb-2 font-medium">
                            <span>Kontribusi Konsumsi Bulanan</span>
                            <span>{{ $device['persen'] }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $barClass }}" style="width: {{ min($device['persen'], 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Simulasi Penghematan -->
                    <div class="mb-6">
                        <button
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition-all shadow-sm shadow-blue-200 hover:shadow-md active:scale-95"
                            type="button"
                            @click="simulatorOpen = !simulatorOpen"
                            :aria-expanded="simulatorOpen.toString()"
                            aria-controls="simulator-{{ $i }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            Simulasi Penghematan
                        </button>

                        <div x-show="simulatorOpen" x-collapse x-cloak class="mt-4" id="simulator-{{ $i }}">
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 md:p-6 shadow-sm">
                                
                                <!-- Slider Section (Atas) -->
                                <div class="space-y-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-800">Simulasikan Durasi Pemakaian Harian</h4>
                                            <p class="text-xs text-slate-500 mt-0.5">Pemakaian saat ini: <span class="font-bold text-slate-700" x-text="formatNumber(currentHours, 1) + ' jam/hari'"></span></p>
                                        </div>
                                        <div class="flex items-baseline gap-1 bg-white px-3 py-1.5 rounded-xl border border-slate-200/60 shadow-sm self-start">
                                            <span class="text-lg font-bold text-blue-600" x-text="formatNumber(simulatedHours, 1)"></span>
                                            <span class="text-xs text-slate-500 font-semibold">jam/hari</span>
                                        </div>
                                    </div>

                                    <!-- Range Slider & Ruler -->
                                    <div class="relative pt-2">
                                        <input
                                            type="range"
                                            class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            min="0"
                                            max="24"
                                            step="0.5"
                                            x-model.number="simulatedHours"
                                        >
                                        <!-- Ruler/Ticks -->
                                        <div class="flex justify-between text-[10px] text-slate-400 font-bold px-1 mt-2">
                                            <span>0 jam</span>
                                            <span>6 jam</span>
                                            <span>12 jam</span>
                                            <span>18 jam</span>
                                            <span>24 jam</span>
                                        </div>
                                    </div>

                                    <!-- Persentase & Ringkasan di Bawah Slider -->
                                    <div class="flex items-center gap-2 bg-white rounded-xl p-3.5 border border-slate-200/60 text-xs">
                                        <template x-if="percentChange() > 0">
                                            <span class="inline-flex items-center gap-1 font-bold text-emerald-600 shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7-7-7 7"/>
                                                </svg>
                                                <span x-text="formatNumber(percentChange(), 1) + '%'"></span> Hemat
                                            </span>
                                        </template>
                                        <template x-if="percentChange() < 0">
                                            <span class="inline-flex items-center gap-1 font-bold text-red-600 shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 10l-7 7-7-7"/>
                                                </svg>
                                                <span x-text="formatNumber(Math.abs(percentChange()), 1) + '%'"></span> Lebih Boros
                                            </span>
                                        </template>
                                        <template x-if="percentChange() == 0">
                                            <span class="font-bold text-slate-500 shrink-0">Tidak ada perubahan</span>
                                        </template>
                                        <span class="text-slate-300 font-medium shrink-0">|</span>
                                        <span class="text-slate-500" x-text="percentChange() > 0 ? 'Mengurangi durasi pemakaian akan memotong konsumsi bulanan Anda.' : (percentChange() < 0 ? 'Menambah durasi pemakaian meningkatkan konsumsi listrik Anda.' : 'Geser slider ke kiri atau kanan untuk menyimulasikan pemakaian baru.')"></span>
                                    </div>
                                </div>

                                <!-- Cards Section (Bawah) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                                    
                                    <!-- Card 1: Hemat kWh/bulan -->
                                    <div class="bg-white rounded-2xl p-4 border shadow-sm flex flex-col justify-between min-h-[90px] transition-all"
                                         :class="diffKwh() >= 0 ? 'border-emerald-100 bg-emerald-50/5' : 'border-red-100 bg-red-50/5'">
                                        <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Hemat kWh/bulan</span>
                                        <div class="flex items-baseline gap-1 mt-2">
                                            <span class="text-lg font-bold transition-colors"
                                                  :class="diffKwh() >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                                  x-text="formatNumber(diffKwh(), 1) + ' kWh'"></span>
                                        </div>
                                    </div>

                                    <!-- Card 2: Hemat biaya/bulan -->
                                    <div class="bg-white rounded-2xl p-4 border shadow-sm flex flex-col justify-between min-h-[90px] transition-all"
                                         :class="diffCost() >= 0 ? 'border-emerald-100 bg-emerald-50/5' : 'border-red-100 bg-red-50/5'">
                                        <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Hemat biaya/bulan</span>
                                        <div class="mt-2">
                                            <span class="text-lg font-bold transition-colors"
                                                  :class="diffCost() >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                                  x-text="formatCurrency(diffCost())"></span>
                                        </div>
                                    </div>

                                    <!-- Card 3: CO2 berkurang -->
                                    <div class="bg-white rounded-2xl p-4 border shadow-sm flex flex-col justify-between min-h-[90px] transition-all"
                                         :class="diffCo2() >= 0 ? 'border-emerald-100 bg-emerald-50/5' : 'border-red-100 bg-red-50/5'">
                                        <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">CO₂ berkurang</span>
                                        <div class="flex items-baseline gap-1 mt-2">
                                            <span class="text-lg font-bold transition-colors"
                                                  :class="diffCo2() >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                                  x-text="formatNumber(diffCo2(), 1) + ' kg'"></span>
                                        </div>
                                    </div>

                                    <!-- Card 4: Penurunan konsumsi -->
                                    <div class="bg-white rounded-2xl p-4 border shadow-sm flex flex-col justify-between min-h-[90px] transition-all"
                                         :class="percentChange() >= 0 ? 'border-emerald-100 bg-emerald-50/5' : 'border-red-100 bg-red-50/5'">
                                        <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Penurunan konsumsi</span>
                                        <div class="flex items-baseline gap-1 mt-2">
                                            <span class="text-lg font-bold transition-colors"
                                                  :class="percentChange() >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                                  x-text="formatNumber(percentChange(), 1) + '%'"></span>
                                        </div>
                                    </div>

                                </div>

                                <!-- Progress bar visualisasi sebelum & sesudah -->
                                <div class="mt-6 space-y-3 pt-4 border-t border-slate-200/60">
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                                            <span>Konsumsi Awal</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-slate-400 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                                            <span>Konsumsi Simulasi</span>
                                            <span :class="percentChange() >= 0 ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold'"
                                                  x-text="formatNumber(Math.max(0, 100 - percentChange()), 1) + '%'"></span>
                                        </div>
                                        <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300"
                                                 :class="percentChange() >= 0 ? 'bg-emerald-500' : 'bg-red-500'"
                                                 :style="`width: ${Math.max(0, Math.min(100, 100 - percentChange()))}%`"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Tips List -->
                    <div class="mt-6">
                        <h5 class="text-sm font-semibold text-slate-900 mb-4">Tips Hemat Listrik</h5>
                        <ul class="space-y-3">
                            @foreach($device['tips'] as $j => $tip)
                            @php
                                $checklist_key = "{$device['device_id']}_{$j}";
                                $is_completed = $tipChecklists[$checklist_key] ?? false;
                            @endphp

                            {{-- Wrapper baris: checkbox DI LUAR card (kiri), card di kanan --}}
                            <li class="tip-item group flex items-center gap-3 {{ $is_completed ? 'completed' : '' }}"
                                data-device-id="{{ $device['device_id'] }}"
                                data-tip-index="{{ $j }}">

                                {{-- KIRI: Checkbox (di luar card) --}}
                                <div class="shrink-0">
                                    <div class="relative w-6 h-6 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            class="tip-checkbox absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                                            data-device-id="{{ $device['device_id'] }}"
                                            data-tip-index="{{ $j }}"
                                            {{ $is_completed ? 'checked' : '' }}
                                        >
                                        {{-- Kotak checkbox --}}
                                        <div class="checkbox-box absolute inset-0 rounded-md border-2 transition-all duration-300 {{ $is_completed ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 bg-white group-hover:border-emerald-400' }}"></div>
                                        {{-- Checkmark --}}
                                        <svg class="checkbox-icon absolute inset-0 w-6 h-6 p-0.5 text-white pointer-events-none transition-all duration-300 {{ $is_completed ? 'opacity-100 scale-100' : 'opacity-0 scale-50' }}"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- KANAN: Card tip (nomor di dalam, kiri teks) --}}
                                <div class="tip-card flex-1 min-w-0 flex items-center gap-3 px-4 py-3 rounded-xl border transition-all duration-300 cursor-pointer
                                    {{ $is_completed
                                        ? 'bg-emerald-50 border-emerald-200'
                                        : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm' }}">

                                    {{-- Nomor (di dalam card) --}}
                                    <span class="tip-number shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold transition-all duration-300 {{ $is_completed ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $j + 1 }}
                                    </span>

                                    {{-- Teks tip --}}
                                    <span class="tip-text block text-sm font-medium leading-relaxed {{ $is_completed ? 'text-slate-400' : 'text-slate-700' }}">
                                        <span class="tip-line-wrap">
                                            <span class="tip-line">{{ $tip }}</span>
                                            <span class="tip-done-badge">
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5l2.5 2.5 4.5-4" stroke="#059669" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                Selesai
                                            </span>
                                        </span>
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif {{-- end deviceTips isEmpty --}}
    @endif {{-- end hasDevice --}}
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* ============================================================
   ACHIEVEMENT SYSTEM — Styles
   ============================================================ */

/* Modal pop animation */
.achievement-modal-panel {
    animation: modal-pop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes modal-pop {
    from { opacity: 0; transform: scale(0.88) translateY(24px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* Shimmer on button */
.achievement-btn-shimmer {
    animation: shimmer-slide 2.5s ease-in-out infinite;
}
@keyframes shimmer-slide {
    0%   { transform: translateX(-200%) skewX(-12deg); }
    60%  { transform: translateX(400%) skewX(-12deg); }
    100% { transform: translateX(400%) skewX(-12deg); }
}

/* Filter tab */
.ach-filter-btn {
    color: #64748b;
    background: transparent;
    border: none;
}
.ach-filter-btn.active {
    background: #fff7ed;
    color: #ea580c;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.ach-filter-btn:hover:not(.active) {
    background: #f1f5f9;
    color: #334155;
}

/* Achievement card */
.ach-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 16px;
    border: 1.5px solid;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}
.ach-item.unlocked {
    border-color: transparent;
    background: linear-gradient(135deg, #fffbeb, #fff7ed);
    box-shadow: 0 2px 10px rgba(245,158,11,0.1);
}
.ach-item.unlocked:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(245,158,11,0.18);
}
.ach-item.locked {
    border-color: #f1f5f9;
    background: #f8fafc;
}
.ach-item.is-new {
    border-color: #fcd34d !important;
    background: linear-gradient(135deg, #fefce8, #fff7ed) !important;
}
/* "NEW" ribbon */
.ach-item.is-new::before {
    content: 'BARU!';
    position: absolute;
    top: 8px;
    right: -18px;
    background: #ef4444;
    color: white;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .06em;
    padding: 2px 20px;
    transform: rotate(35deg);
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}

/* Icon wrap */
.ach-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    flex-shrink: 0;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.ach-item.unlocked .ach-icon-wrap {
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}
.ach-item.locked .ach-icon-wrap {
    background: #f1f5f9 !important;
    filter: grayscale(1);
    opacity: 0.5;
}
.ach-item.unlocked:hover .ach-icon-wrap {
    transform: rotate(-8deg) scale(1.08);
}

/* Pop animation on new unlock */
@keyframes ach-pop {
    0%   { transform: scale(0.4) rotate(-15deg); opacity: 0; }
    65%  { transform: scale(1.15) rotate(4deg);  opacity: 1; }
    100% { transform: scale(1) rotate(0);         opacity: 1; }
}
.ach-item.just-unlocked .ach-icon-wrap {
    animation: ach-pop 0.55s cubic-bezier(0.34,1.56,0.64,1) forwards;
}

/* Rarity badge */
.rarity-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    letter-spacing: .03em;
    white-space: nowrap;
}

/* Achievement button pulse */
@keyframes btn-pulse {
    0%,100% { box-shadow: 0 4px 14px rgba(245,158,11,0.4); }
    50%      { box-shadow: 0 4px 28px rgba(245,158,11,0.8), 0 0 0 6px rgba(245,158,11,0.15); }
}
#achievement-btn.has-new {
    animation: btn-pulse 1.6s ease-in-out infinite;
}

/* Tip checklist styles */
.tip-item { transition: all 0.25s ease; }

@keyframes checkmark-pop {
    0%   { opacity: 0; transform: scale(0.3) rotate(-10deg); }
    60%  { opacity: 1; transform: scale(1.2) rotate(2deg); }
    100% { opacity: 1; transform: scale(1) rotate(0deg); }
}
.checkbox-icon.animate-check {
    animation: checkmark-pop 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
}
@keyframes number-flip {
    0%   { transform: scale(0.7); opacity: 0.5; }
    100% { transform: scale(1);   opacity: 1; }
}
.tip-number.animate-flip {
    animation: number-flip 0.3s cubic-bezier(0.34,1.56,0.64,1) forwards;
}

.tip-text { display:block; transition: color 0.35s ease, opacity 0.35s ease; }
.tip-item.completed .tip-text { color:#94a3b8; opacity:0.65; }
.tip-line-wrap { display:block; }
.tip-line { display:block; }
.tip-done-badge {
    display:inline-flex; align-items:center; gap:4px; margin-top:6px;
    padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600;
    color:#059669; background:#d1fae5;
    opacity:0; transform:translateY(4px);
    transition: opacity 0.3s ease 0.1s, transform 0.3s ease 0.1s;
    pointer-events:none;
}
.tip-item.completed .tip-done-badge { opacity:1; transform:translateY(0); }
.tip-card { transition: all 0.25s ease; }
.checkbox-box { transition: background-color 0.25s ease, border-color 0.25s ease; }
.checkbox-icon { transition: opacity 0.2s ease, transform 0.2s ease; }
</style>

<script>
/* ================================================================
   ACHIEVEMENT SYSTEM — WattCare (Enhanced)
   ================================================================ */

const ACHIEVEMENTS = [
    /* ── Tier 1: Tips Total ── */
    { id:'first_tip',    icon:'⚡', title:'Percikan Pertama',   desc:'Selesaikan 1 tips pertamamu',          rarity:'Biasa',   rfrom:'#6b7280',rto:'#4b5563', type:'total_tips', req:1  },
    { id:'three_tips',   icon:'🔋', title:'Mulai Berhemat',     desc:'Selesaikan 3 tips hemat listrik',       rarity:'Biasa',   rfrom:'#6b7280',rto:'#4b5563', type:'total_tips', req:3  },
    { id:'five_tips',    icon:'💡', title:'Sadar Energi',       desc:'Selesaikan 5 tips hemat listrik',       rarity:'Pemula',  rfrom:'#3b82f6',rto:'#2563eb', type:'total_tips', req:5  },
    { id:'ten_tips',     icon:'🌱', title:'Bijak Listrik',      desc:'Selesaikan 10 tips hemat listrik',      rarity:'Pemula',  rfrom:'#10b981',rto:'#059669', type:'total_tips', req:10 },
    { id:'fifteen_tips', icon:'🌿', title:'Pejuang Hijau',      desc:'Selesaikan 15 tips hemat listrik',      rarity:'Menengah',rfrom:'#14b8a6',rto:'#0f9488', type:'total_tips', req:15 },
    { id:'twenty_tips',  icon:'🔌', title:'Hemat Sejati',       desc:'Selesaikan 20 tips hemat listrik',      rarity:'Menengah',rfrom:'#8b5cf6',rto:'#7c3aed', type:'total_tips', req:20 },
    { id:'thirty_tips',  icon:'🌍', title:'Eco Champion',       desc:'Selesaikan 30 tips hemat listrik',      rarity:'Langka',  rfrom:'#f59e0b',rto:'#d97706', type:'total_tips', req:30 },
    /* ── Tier 2: Perangkat 100% ── */
    { id:'device_1',     icon:'🥉', title:'Perangkat Pertama',  desc:'Selesaikan semua tips 1 perangkat',     rarity:'Pemula',  rfrom:'#3b82f6',rto:'#2563eb', type:'full_device',req:1  },
    { id:'device_3',     icon:'🥈', title:'Watt Warrior',       desc:'Selesaikan semua tips 3 perangkat',     rarity:'Menengah',rfrom:'#6366f1',rto:'#4f46e5', type:'full_device',req:3  },
    { id:'device_5',     icon:'🥇', title:'Energy Master',      desc:'Selesaikan semua tips 5 perangkat',     rarity:'Langka',  rfrom:'#f59e0b',rto:'#ea580c', type:'full_device',req:5  },
    /* ── Tier 3: Spesial ── */
    { id:'all_done',     icon:'🏆', title:'WattCare Master',    desc:'Selesaikan SEMUA tips semua perangkat', rarity:'Legendaris',rfrom:'#fbbf24',rto:'#f97316', type:'all_done',   req:0  },
    { id:'perfectionist',icon:'🌟', title:'Perfeksionis Hijau', desc:'Selesaikan ≥ 40 tips hemat listrik',    rarity:'Legendaris',rfrom:'#ec4899',rto:'#a855f7', type:'total_tips', req:40 },
];

const RARITY_STYLE = {
    'Biasa':      { bg:'#f1f5f9', text:'#64748b' },
    'Pemula':     { bg:'#dbeafe', text:'#1e40af' },
    'Menengah':   { bg:'#ede9fe', text:'#5b21b6' },
    'Langka':     { bg:'#fef3c7', text:'#92400e' },
    'Legendaris': { bg:'#fce7f3', text:'#9d174d' },
};

/* localStorage keys */
const LS_UNLOCKED = 'wattcare_achievements_v2';
const LS_SEEN     = 'wattcare_achievements_seen_v2';

let unlockedIds = new Set(JSON.parse(localStorage.getItem(LS_UNLOCKED) || '[]'));
let seenIds     = new Set(JSON.parse(localStorage.getItem(LS_SEEN)     || '[]'));
let justUnlockedIds = new Set(); // baru unlock di sesi ini, belum diseen
let activeFilter = 'all';

/* ── Stats ── */
function getStats() {
    const devices = {};
    document.querySelectorAll('.tip-checkbox').forEach(cb => {
        const id = cb.dataset.deviceId;
        if (!devices[id]) devices[id] = { total:0, done:0 };
        devices[id].total++;
        if (cb.checked) devices[id].done++;
    });
    const vals = Object.values(devices);
    return {
        totalTips:      document.querySelectorAll('.tip-checkbox:checked').length,
        fullDevice:     vals.filter(d => d.total > 0 && d.done === d.total).length,
        totalDevice:    vals.length,
    };
}

/* ── Evaluate which achievements are earned ── */
function evaluateUnlocked(stats) {
    const earned = new Set();
    ACHIEVEMENTS.forEach(a => {
        if (a.type === 'total_tips'  && stats.totalTips  >= a.req)                               earned.add(a.id);
        if (a.type === 'full_device' && stats.totalDevice > 0 && stats.fullDevice >= a.req)      earned.add(a.id);
        if (a.type === 'all_done'    && stats.totalDevice > 0 && stats.fullDevice >= stats.totalDevice) earned.add(a.id);
    });
    return earned;
}

/* ── Main check (call after every tip toggle) ── */
function checkAchievements(showToast = false) {
    const stats  = getStats();
    const earned = evaluateUnlocked(stats);

    // Find newly unlocked
    const newOnes = [...earned].filter(id => !unlockedIds.has(id));
    newOnes.forEach(id => {
        unlockedIds.add(id);
        justUnlockedIds.add(id);
    });

    if (newOnes.length > 0) {
        localStorage.setItem(LS_UNLOCKED, JSON.stringify([...unlockedIds]));
        if (showToast) {
            // show toast for each new one (with slight delay between)
            newOnes.forEach((id, i) => {
                setTimeout(() => showAchievementToast(id), i * 600);
            });
        }
        // Pulse button
        const btn = document.getElementById('achievement-btn');
        if (btn) btn.classList.add('has-new');
    }

    updateButtonBadge();
    updateProgressUI(stats);

    // Re-render if modal open
    if (!document.getElementById('achievement-modal').classList.contains('hidden')) {
        renderAchievementList();
    }
}

/* ── Unseen count = unlocked - seen ── */
function getUnseenCount() {
    return [...unlockedIds].filter(id => !seenIds.has(id)).length;
}

/* ── Button badge = unseen count ── */
function updateButtonBadge() {
    const badge = document.getElementById('achievement-badge');
    const btn   = document.getElementById('achievement-btn');
    if (!badge) return;
    const count = getUnseenCount();
    if (count > 0) {
        badge.textContent = count > 9 ? '9+' : count;
        badge.classList.remove('hidden');
        badge.classList.add('flex');
        if (btn) btn.classList.add('has-new');
    } else {
        badge.classList.add('hidden');
        badge.classList.remove('flex');
        if (btn) btn.classList.remove('has-new');
    }
    // filter-new dot
    const dot = document.getElementById('filter-new-dot');
    if (dot) {
        if (count > 0) dot.classList.remove('hidden');
        else dot.classList.add('hidden');
    }
}

/* ── Progress UI (stats in modal header) ── */
function updateProgressUI(stats) {
    if (!stats) stats = getStats();
    const count   = unlockedIds.size;
    const total   = ACHIEVEMENTS.length;
    const pct     = total ? Math.round((count / total) * 100) : 0;

    const bar  = document.getElementById('ach-progress-bar');
    const su   = document.getElementById('ach-stat-unlocked');
    const st   = document.getElementById('ach-stat-total');
    const sp   = document.getElementById('ach-stat-pct');
    const pt   = document.getElementById('ach-progress-text');

    if (bar) bar.style.width = pct + '%';
    if (su)  su.textContent  = count;
    if (st)  st.textContent  = total;
    if (sp)  sp.textContent  = pct + '%';
    if (pt)  pt.textContent  = `${count} / ${total} terbuka`;
}

/* ── Render achievement list ── */
function renderAchievementList() {
    const container = document.getElementById('achievement-list');
    const empty     = document.getElementById('ach-empty');
    if (!container) return;

    const stats = getStats();

    // Filter
    let list = ACHIEVEMENTS;
    if (activeFilter === 'unlocked') list = ACHIEVEMENTS.filter(a => unlockedIds.has(a.id));
    if (activeFilter === 'locked')   list = ACHIEVEMENTS.filter(a => !unlockedIds.has(a.id));
    if (activeFilter === 'new')      list = ACHIEVEMENTS.filter(a => !seenIds.has(a.id) && unlockedIds.has(a.id));

    if (list.length === 0) {
        container.innerHTML = '';
        if (empty) empty.classList.remove('hidden');
        return;
    }
    if (empty) empty.classList.add('hidden');

    // Sort: new first, then unlocked, then locked
    list = [...list].sort((a, b) => {
        const aNew  = justUnlockedIds.has(a.id) ? 0 : 1;
        const bNew  = justUnlockedIds.has(b.id) ? 0 : 1;
        const aUnl  = unlockedIds.has(a.id)     ? 0 : 1;
        const bUnl  = unlockedIds.has(b.id)     ? 0 : 1;
        return (aNew - bNew) || (aUnl - bUnl);
    });

    container.innerHTML = list.map(ach => {
        const unlocked  = unlockedIds.has(ach.id);
        const isNew     = justUnlockedIds.has(ach.id);
        const rs        = RARITY_STYLE[ach.rarity] || RARITY_STYLE['Biasa'];

        // Hint for locked
        let hint = '';
        if (!unlocked) {
            if (ach.type === 'total_tips')  hint = `📌 Butuh ${ach.req} tips selesai (sekarang: ${stats.totalTips})`;
            if (ach.type === 'full_device') hint = `📌 Butuh ${ach.req} perangkat 100% (sekarang: ${stats.fullDevice})`;
            if (ach.type === 'all_done')    hint = `📌 Selesaikan semua ${stats.totalDevice} perangkat`;
        }

        return `
        <div class="ach-item ${unlocked ? 'unlocked' : 'locked'} ${isNew ? 'is-new just-unlocked' : ''}">
            <div class="ach-icon-wrap" style="${unlocked ? `background:linear-gradient(135deg,${ach.rfrom},${ach.rto})` : ''}">
                <span style="${unlocked ? '' : 'filter:grayscale(1);opacity:0.45'}">${ach.icon}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <span class="font-bold text-sm ${unlocked ? 'text-slate-900' : 'text-slate-400'}">${ach.title}</span>
                    <span class="rarity-badge" style="background:${unlocked ? rs.bg : '#f1f5f9'};color:${unlocked ? rs.text : '#94a3b8'};">${ach.rarity}</span>
                    ${isNew ? '<span class="rarity-badge" style="background:#fee2e2;color:#b91c1c;animation:badge-blink 1s ease infinite;">✨ Baru!</span>' : ''}
                </div>
                <div class="text-xs mt-0.5 ${unlocked ? 'text-slate-500' : 'text-slate-400'}">${ach.desc}</div>
                ${hint ? `<div class="text-xs mt-1 text-slate-400">${hint}</div>` : ''}
            </div>
            <div class="shrink-0 text-xl">${unlocked ? '✅' : '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'}</div>
        </div>`;
    }).join('');

    // Add blink keyframe once
    if (!document.getElementById('ach-blink-style')) {
        const s = document.createElement('style');
        s.id = 'ach-blink-style';
        s.textContent = '@keyframes badge-blink{0%,100%{opacity:1}50%{opacity:0.4}}';
        document.head.appendChild(s);
    }
}

/* ── Filter tabs ── */
function filterAch(filter) {
    activeFilter = filter;
    document.querySelectorAll('.ach-filter-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.filter === filter);
    });
    renderAchievementList();
}

/* ── Toast notification ── */
function showAchievementToast(achId) {
    const ach = ACHIEVEMENTS.find(a => a.id === achId);
    if (!ach) return;

    const existing = document.getElementById('ach-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'ach-toast';
    toast.style.cssText = `
        position:fixed;bottom:24px;right:24px;z-index:9999;
        background:white;border-radius:20px;padding:14px 18px;
        box-shadow:0 10px 40px rgba(0,0,0,0.15),0 2px 8px rgba(0,0,0,0.08);
        border:1.5px solid #fde68a;max-width:300px;
        animation:toast-in 0.45s cubic-bezier(0.34,1.56,0.64,1) forwards;
    `;
    toast.innerHTML = `
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,${ach.rfrom},${ach.rto});display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,0.15);">${ach.icon}</div>
            <div>
                <div style="font-size:10px;font-weight:800;color:#f59e0b;text-transform:uppercase;letter-spacing:.06em;">🎉 Achievement Terbuka!</div>
                <div style="font-size:14px;font-weight:700;color:#1e293b;margin-top:2px;">${ach.title}</div>
                <div style="font-size:11px;color:#64748b;margin-top:1px;">${ach.desc}</div>
            </div>
        </div>
    `;
    if (!document.getElementById('toast-style')) {
        const s = document.createElement('style');
        s.id = 'toast-style';
        s.textContent = '@keyframes toast-in{from{opacity:0;transform:translateY(30px) scale(0.9)}to{opacity:1;transform:translateY(0) scale(1)}} @keyframes toast-out{from{opacity:1}to{opacity:0;transform:translateY(20px)}}';
        document.head.appendChild(s);
    }
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'toast-out 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}

/* ── Open modal: mark all unlocked as seen ── */
function openAchievementModal() {
    const modal = document.getElementById('achievement-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    renderAchievementList();
    updateProgressUI();

    // Mark semua yang sudah unlock sebagai seen → hapus badge merah
    unlockedIds.forEach(id => seenIds.add(id));
    localStorage.setItem(LS_SEEN, JSON.stringify([...seenIds]));
    justUnlockedIds.clear(); // sudah dilihat
    updateButtonBadge(); // badge merah hilang
}

function closeAchievementModal() {
    const modal = document.getElementById('achievement-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAchievementModal(); });

/* ================================================================
   TIP CHECKLIST SYSTEM
   ================================================================ */
document.addEventListener('DOMContentLoaded', function() {
    initializeCounters();
    initializeCompletedStates();
    setupEventListeners();
    checkAchievements(false); // restore state on load, no toast

    function initializeCompletedStates() {
        document.querySelectorAll('.tip-item.completed').forEach(item => {
            applyCompletedUI(item, true, false);
        });
    }

    function setupEventListeners() {
        document.querySelectorAll('.tip-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', async function(e) {
                e.stopPropagation();
                await toggleTip(this);
            });
        });
        document.querySelectorAll('.tip-card').forEach(card => {
            card.addEventListener('click', async function() {
                const item     = this.closest('.tip-item');
                const checkbox = item.querySelector('.tip-checkbox');
                checkbox.checked = !checkbox.checked;
                await toggleTip(checkbox);
            });
        });
    }

    async function toggleTip(checkbox) {
        const deviceId  = checkbox.dataset.deviceId;
        const tipIndex  = checkbox.dataset.tipIndex;
        const item      = checkbox.closest('.tip-item');
        const willCheck = checkbox.checked;
        try {
            const response = await fetch('{{ route("recommendations.toggleTipChecklist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ device_id: deviceId, tip_index: tipIndex })
            });
            const data = await response.json();
            if (data.success) {
                checkbox.checked = data.is_completed;
                applyCompletedUI(item, data.is_completed, true);
                updateCounter(deviceId);
                checkAchievements(true); // ← check & toast
            } else {
                checkbox.checked = !willCheck;
            }
        } catch (err) {
            console.error('Toggle gagal:', err);
            checkbox.checked = !willCheck;
        }
    }

    function applyCompletedUI(item, isCompleted, animate) {
        const textSpan    = item.querySelector('.tip-text');
        const numberBadge = item.querySelector('.tip-number');
        const checkBox    = item.querySelector('.checkbox-box');
        const checkIcon   = item.querySelector('.checkbox-icon');
        const tipCard     = item.querySelector('.tip-card');

        if (isCompleted) {
            item.classList.add('completed');
            tipCard.classList.add('bg-emerald-50','border-emerald-200');
            tipCard.classList.remove('bg-white','border-slate-200','hover:border-slate-300','hover:shadow-sm');
            numberBadge.classList.add('bg-emerald-500','text-white');
            numberBadge.classList.remove('bg-slate-100','text-slate-500');
            checkBox.classList.add('border-emerald-500','bg-emerald-500');
            checkBox.classList.remove('border-slate-300','bg-white');
            checkIcon.classList.add('opacity-100','scale-100');
            checkIcon.classList.remove('opacity-0','scale-50');
            if (animate) {
                checkIcon.classList.add('animate-check');
                numberBadge.classList.add('animate-flip');
                setTimeout(() => {
                    checkIcon.classList.remove('animate-check');
                    numberBadge.classList.remove('animate-flip');
                }, 400);
            }
        } else {
            item.classList.remove('completed');
            tipCard.classList.remove('bg-emerald-50','border-emerald-200');
            tipCard.classList.add('bg-white','border-slate-200','hover:border-slate-300','hover:shadow-sm');
            numberBadge.classList.remove('bg-emerald-500','text-white');
            numberBadge.classList.add('bg-slate-100','text-slate-500');
            checkBox.classList.remove('border-emerald-500','bg-emerald-500');
            checkBox.classList.add('border-slate-300','bg-white');
            checkIcon.classList.remove('opacity-100','scale-100');
            checkIcon.classList.add('opacity-0','scale-50');
        }
    }

    function initializeCounters() {
        document.querySelectorAll('.tips-counter').forEach(counter => {
            const deviceId  = counter.dataset.deviceId;
            const completed = document.querySelectorAll(`.tip-checkbox[data-device-id="${deviceId}"]:checked`).length;
            counter.querySelector('.tips-completed').textContent = completed;
        });
    }

    function updateCounter(deviceId) {
        const counter = document.querySelector(`.tips-counter[data-device-id="${deviceId}"]`);
        if (counter) {
            const completed = document.querySelectorAll(`.tip-checkbox[data-device-id="${deviceId}"]:checked`).length;
            counter.querySelector('.tips-completed').textContent = completed;
        }
    }
});
</script>
@endpush
@endsection