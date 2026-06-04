@extends('layouts.app')

@section('title', 'Tarif Listrik')
@section('page-title', 'Tarif Listrik')

@php
    // Intentionally no header-actions here — primary action belongs inside page content
@endphp

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <p class="text-slate-500 text-sm">Kelola paket tarif listrik berdasarkan daya (VA).</p>
        <a href="{{ route('admin.electricity_rates.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm rounded-full transition-all shadow-sm whitespace-nowrap"
           title="Tambah tarif listrik baru">
            <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
            Tambah Tarif
        </a>
    </div>

    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-5 font-semibold">Daya (VA)</th>
                        <th class="px-6 py-5 font-semibold">Tarif per kWh</th>
                        <th class="px-6 py-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rates as $rate)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                                        <i data-lucide="zap" class="w-4.5 h-4.5"></i>
                                    </div>
                                    <span class="font-bold text-slate-900">{{ number_format($rate->daya_va, 0, ',', '.') }} <span class="text-slate-500 font-medium">VA</span></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">Rp {{ number_format($rate->tarif_per_kwh, 2, ',', '.') }}</span>
                                <span class="text-slate-400 text-xs ml-1">/ kWh</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.electricity_rates.edit', $rate) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-lg transition-colors">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        Ubah
                                    </a>
                                    <form method="POST" action="{{ route('admin.electricity_rates.destroy', $rate) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Hapus tarif ini? Tindakan ini tidak dapat dibatalkan.')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-xs rounded-lg transition-colors">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i data-lucide="zap" class="w-12 h-12 mb-3 text-slate-300"></i>
                                    <p class="font-semibold text-slate-600">Belum ada data tarif</p>
                                    <a href="{{ route('admin.electricity_rates.create') }}" class="mt-3 text-blue-600 hover:underline text-sm font-medium">Tambah tarif baru</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($rates->hasPages())
        <div class="mt-6">{{ $rates->links() }}</div>
    @endif
@endsection
