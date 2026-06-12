@extends('layouts.app')

@section('title', 'Edit Tarif Listrik')
@section('page-title', 'Edit Tarif Listrik')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.electricity_rates.index') }}"
       class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-medium text-sm mb-6 transition-colors group">
        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center group-hover:bg-slate-50 transition-colors shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </div>
        Kembali ke Daftar Tarif
    </a>

    <!-- Current Rate Info -->
    <div class="flex items-center gap-4 bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
            <i data-lucide="zap" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-0.5">Tarif Saat Ini</p>
            <p class="font-bold text-slate-900">{{ number_format($rate->daya_va, 0, ',', '.') }} VA — Rp {{ number_format($rate->tarif_per_kwh, 2, ',', '.') }} / kWh</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <i data-lucide="pencil" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Edit Tarif Daya {{ $rate->daya_va }} VA</h3>
                <p class="text-xs text-slate-500">Perbarui nilai tarif listrik</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.electricity_rates.update', $rate) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="daya_va" class="block text-sm font-semibold text-slate-700">Daya Listrik (VA)</label>
                <div class="relative">
                    <input id="daya_va" type="number" name="daya_va" value="{{ old('daya_va', $rate->daya_va) }}" required
                           aria-describedby="daya_va_help_edit"
                           title="Daya listrik dalam Volt-Ampere (VA)"
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('daya_va') border-red-400 @enderror">
                    <span id="daya_va_help_edit" class="sr-only">Satuan: VA (Volt-Ampere)</span>
                </div>
                @error('daya_va')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                    <p class="text-xs text-slate-400">Umumnya: 450, 900, 1300, 2200, 3500 VA</p>
            </div>

            <div class="space-y-2">
                <label for="tarif_per_kwh" class="block text-sm font-semibold text-slate-700">Tarif per kWh (Rp)</label>
                <div class="relative">
                          <input id="tarif_per_kwh" type="text" name="tarif_per_kwh" value="{{ old('tarif_per_kwh', $rate->tarif_per_kwh) }}" required
                              aria-describedby="tarif_per_kwh_help_edit"
                              title="Masukkan tarif per kWh dalam Rupiah (Rp)"
                              class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium @error('tarif_per_kwh') border-red-400 @enderror">
                          <span id="tarif_per_kwh_help_edit" class="sr-only">Mata uang: Rupiah (Rp). Contoh: 1352.00</span>
                </div>
                @error('tarif_per_kwh')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100 mt-6">
                <button type="submit"
                    class="flex-1 py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.electricity_rates.index') }}"
                   class="flex-1 py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
