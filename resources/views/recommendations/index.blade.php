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
        <div class="text-right bg-slate-50 px-4 py-3 rounded-lg border border-slate-200">
            <div class="text-sm font-semibold text-slate-900">{{ $dayaVa }} VA</div>
            <div class="text-xs text-slate-600 mt-1">Tarif: Rp {{ number_format($tariff, 2) }}/kWh</div>
            <div class="text-xs text-slate-600">Batas: {{ number_format($hematMax, 1) }}–{{ number_format($sedangMax, 1) }} kWh/bulan</div>
        </div>
    </div>

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

                <div class="flex items-center gap-3 shrink-0">
                    <div class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white {{ $barClass }}">
                        {{ $device['kwh_bulan'] }} kWh
                    </div>
                    <div class="px-2 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $device['persen'] }}%
                    </div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 bg-slate-50 transition-transform duration-200" :class="{ 'rotate-180 bg-slate-100 text-slate-600': open }" aria-hidden="true">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        <span class="sr-only">Toggle detail</span>
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


                    <!-- Tips List -->
                    <ul class="space-y-3">
                        @foreach($device['tips'] as $j => $tip)
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold border {{ $bgClass }}">{{ $j + 1 }}</span>
                            <span class="text-sm text-slate-700 leading-relaxed pt-0.5">{{ $tip }}</span>
                        </li>
                        @endforeach
                    </ul>
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
@endpush
@endsection