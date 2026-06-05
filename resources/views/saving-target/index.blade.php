@extends('layouts.app')

@section('title', 'Target Penghematan')
@section('page-title', 'Target Penghematan Listrik')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    {{-- ===== KARTU KIRI: FORM / HASIL TARGET ===== --}}
    @if($user->target_hemat === null)
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-blue-400"></div>
            
            <div class="flex items-center gap-3 mb-4">
                <i data-lucide="target" class="w-6 h-6 text-blue-600"></i>
                <h2 class="text-xl font-bold text-slate-900">Atur Target Penghematan</h2>
            </div>
            
            <p class="text-sm text-slate-500 leading-relaxed mb-6">
                Pilih persentase penghematan listrik bulanan yang ingin dicapai. Sistem akan menghitung batas maksimal pemakaian baru berdasarkan daya listrik (VA) Anda, yang diperoleh dari pengurangan kategori boros.
            </p>

            <form method="POST" action="{{ route('saving-target.store') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="target_hemat" class="block text-sm font-semibold text-slate-700">Persentase Penghematan (%)</label>
                    <div class="relative">
                           <input type="number" id="target_hemat" name="target_hemat" min="1" max="50" placeholder="Contoh: 15" required 
                               aria-describedby="target_hemat_help"
                               title="Persentase penghematan (%)"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold">
                           <span id="target_hemat_help" class="sr-only">Persentase penghematan dalam persen (%)</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Batas penghematan minimal 1% dan maksimal 50% per bulan.</p>
                </div>

                <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100 mb-6">
                    <span class="text-sm font-medium text-slate-600">Daya Rumah Saat Ini</span>
                    <span class="font-bold text-slate-900">{{ $user->daya_va }} VA</span>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm shadow-blue-600/20 transition-all">
                    Aktifkan Target Penghematan
                </button>
            </form>
        </div>
    @else
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r {{ $savingStatus === 'tercapai' ? 'from-emerald-500 to-emerald-400' : 'from-red-500 to-red-400' }}"></div>
            
            <div class="flex items-center gap-3 mb-4">
                <i data-lucide="check-circle" class="w-6 h-6 {{ $savingStatus === 'tercapai' ? 'text-emerald-500' : 'text-red-500' }}"></i>
                <h2 class="text-xl font-bold text-slate-900">Target Penghematan Aktif</h2>
            </div>
            
            <p class="text-sm text-slate-500 leading-relaxed mb-5">
                Berikut adalah detail batas penggunaan listrik ideal Anda bulan ini berdasarkan target penghematan yang telah diatur.
            </p>

            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide mb-6 {{ $savingStatus === 'tercapai' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                Status: {{ $savingStatus === 'tercapai' ? 'Target Tercapai' : 'Melebihi Target' }}
            </div>

            <div class="grid gap-3 mb-6">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <span class="text-sm font-medium text-slate-500">VA Rumah</span>
                    <span class="font-bold text-slate-900">{{ $user->daya_va }} VA</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <span class="text-sm font-medium text-slate-500">Kategori Boros</span>
                    <span class="font-bold text-slate-900">{{ round($batasBoros) }} kWh</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-blue-50/50 border border-blue-100 border-dashed rounded-xl">
                    <span class="text-sm font-bold text-blue-600">Target Hemat</span>
                    <span class="font-bold text-blue-600">{{ $user->target_hemat }}%</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-blue-50 rounded-xl">
                    <span class="text-sm font-bold text-slate-900">Target Maksimal Pemakaian</span>
                    <span class="text-lg font-bold text-blue-600">{{ round($targetKwh) }} kWh/bln</span>
                </div>
            </div>

            {{-- Form edit cepat persentase --}}
            <form method="POST" action="{{ route('saving-target.store') }}" class="pt-5 border-t border-slate-100">
                @csrf
                <label for="target_hemat" class="block text-sm font-semibold text-slate-700 mb-2">Ubah Target Persentase (%)</label>
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <input type="number" id="target_hemat" name="target_hemat" min="1" max="50" value="{{ $user->target_hemat }}" required 
                               aria-describedby="target_hemat_quick_help"
                               title="Ubah persentase penghematan (%)"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold">
                        <span id="target_hemat_quick_help" class="sr-only">Persentase penghematan dalam persen (%)</span>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl transition-all whitespace-nowrap">
                        Perbarui
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('saving-target.destroy') }}" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-all" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan target penghematan bulanan?')">
                    Nonaktifkan Fitur Penghematan
                </button>
            </form>
        </div>
    @endif

    {{-- ===== KARTU KANAN: STATUS KONSUMSI / REKOMENDASI ===== --}}
    @if($user->target_hemat !== null)
        <div class="bg-slate-900 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden flex flex-col justify-between shadow-[0_4px_20px_rgb(0,0,0,0.08)]">
            <div class="absolute -bottom-8 -right-4 text-slate-800/30">
                <i data-lucide="zap" class="w-48 h-48"></i>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-xl font-bold mb-3">Pantau Pemakaian Energi</h2>
                <p class="text-sm text-slate-300 leading-relaxed mb-6">
                    Berikut adalah gambaran realisasi konsumsi listrik rumah Anda pada bulan berjalan terhadap target penghematan maksimal.
                </p>

                <div class="mb-6">
                    <div class="flex justify-between text-sm font-medium text-slate-300 mb-2">
                        <span>Penggunaan Bulan Ini</span>
                        <span>{{ number_format($monthUsage, 1) }} / {{ round($targetKwh) }} kWh</span>
                    </div>
                    <div class="h-2.5 w-full bg-slate-800 rounded-full overflow-hidden">
                        @php
                            $percentage = $targetKwh > 0 ? min(($monthUsage / $targetKwh) * 100, 100) : 0;
                            $barClass = 'bg-emerald-500';
                            if ($percentage > 100) {
                                $barClass = 'bg-red-500';
                            } elseif ($percentage > 80) {
                                $barClass = 'bg-amber-500';
                            }
                        @endphp
                        <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $barClass }}" style="width: {{ $percentage }}%;"></div>
                    </div>
                    <div class="text-right text-xs text-slate-400 mt-2 font-medium">
                        Persentase Target: {{ round($percentage) }}%
                    </div>
                </div>

                @if($savingStatus === 'tercapai')
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-8">
                        <p class="text-sm text-emerald-200 leading-relaxed">
                            <span class="font-bold text-emerald-400">💡 Hebat!</span> Konsumsi listrik Anda sejauh ini masih berada di bawah target maksimal pemakaian Anda ({{ round($targetKwh) }} kWh). Pertahankan kinerja ini untuk memastikan target penghematan tercapai di akhir bulan.
                        </p>
                    </div>
                @else
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-8">
                        <p class="text-sm text-red-200 leading-relaxed">
                            <span class="font-bold text-red-400">⚠️ Peringatan!</span> Pemakaian listrik Anda bulan ini telah melebihi batas target penghematan Anda ({{ round($targetKwh) }} kWh). Harap kurangi penggunaan peralatan elektronik yang tidak mendesak sesegera mungkin.
                        </p>
                    </div>
                @endif
            </div>

            <a href="{{ route('recommendations.index') }}" class="relative z-10 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-900/20 self-start">
                Lihat Rekomendasi Hemat Listrik
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    @else
        <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden flex flex-col justify-between shadow-[0_4px_20px_rgb(0,0,0,0.08)]">
            <div class="absolute -bottom-8 -right-4 text-blue-950/20">
                <i data-lucide="lightbulb" class="w-48 h-48"></i>
            </div>
            
            <div class="relative z-10 mb-8">
                <h2 class="text-xl font-bold mb-3">Mengapa Mengatur Target?</h2>
                <div class="text-sm text-blue-100 leading-relaxed space-y-4">
                    <p>Mengatur target penghematan membantu Anda:</p>
                    <ul class="list-disc pl-5 space-y-2 marker:text-blue-400">
                        <li>Mendapatkan batas pemakaian ideal secara personal.</li>
                        <li>Memantau efisiensi energi bulanan secara visual melalui grafik.</li>
                        <li>Mengontrol pengeluaran biaya listrik sebelum terjadi lonjakan.</li>
                        <li>Mendukung kelestarian lingkungan dengan mengurangi emisi CO2.</li>
                    </ul>
                </div>
            </div>

            <a href="{{ route('recommendations.index') }}" class="relative z-10 inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all self-start border border-white/10">
                Baca Tips Efisiensi
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    @endif
</div>
@endsection