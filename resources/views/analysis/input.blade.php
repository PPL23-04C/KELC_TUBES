@extends('layouts.app')

@section('title', 'Input Analisis')
@section('page-title', 'Input Analisis Harian')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Catat Penggunaan</h1>
                <p class="text-slate-500 mt-1 text-sm">Masukkan durasi penggunaan perangkat Anda untuk hari ini.</p>
            </div>
            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <form method="POST" action="{{ route('analysis.store') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="device_id" class="block text-sm font-semibold text-slate-700">Pilih Perangkat</label>
                    <div class="relative">
                        <select id="device_id" name="device_id" required
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                            <option value="">-- Pilih Perangkat Anda --</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                    {{ $device->nama_device }} ({{ $device->daya_watt }}W)
                                </option>
                            @endforeach
                        </select>
                        
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="tanggal" class="block text-sm font-semibold text-slate-700">Tanggal Pencatatan</label>
                        <div class="relative">
                            <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                                   aria-describedby="tanggal_help"
                                   title="Pilih tanggal pencatatan"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <span id="tanggal_help" class="sr-only">Tanggal pencatatan</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="jam_pemakaian" class="block text-sm font-semibold text-slate-700">Durasi (Jam)</label>
                        <div class="relative">
                            <input id="jam_pemakaian" type="number" step="0.1" name="jam_pemakaian" value="{{ old('jam_pemakaian') }}" placeholder="Contoh: 5.5" required
                                   aria-describedby="jam_help"
                                   title="Durasi pemakaian dalam jam"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400">
                            <span id="jam_help" class="sr-only">Satuan: jam</span>
                            
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-600/20 rounded-xl transition-all flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Analisis
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
