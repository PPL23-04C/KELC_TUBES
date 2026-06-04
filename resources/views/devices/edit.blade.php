@extends('layouts.app')

@section('title', 'Ubah Perangkat')
@section('page-title', 'Ubah Perangkat')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Data Perangkat</h1>
                <p class="text-slate-500 mt-1 text-sm">Perbarui informasi perangkat elektronik Anda.</p>
            </div>
            <a href="{{ route('devices.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </a>
        </div>

        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <form method="POST" action="{{ route('devices.update', $device) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="nama_device" class="block text-sm font-semibold text-slate-700">Nama Perangkat</label>
                    <input id="nama_device" name="nama_device" value="{{ old('nama_device', $device->nama_device) }}" placeholder="Contoh: AC Kamar Tidur, Kulkas..." required
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="daya_watt" class="block text-sm font-semibold text-slate-700">Daya (Watt)</label>
                        <div class="relative">
                            <input id="daya_watt" type="number" name="daya_watt" min="1" value="{{ old('daya_watt', $device->daya_watt) }}" required
                                   aria-describedby="daya_watt_help_edit"
                                   title="Daya perangkat dalam Watt (W)"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400">
                            <span id="daya_watt_help_edit" class="sr-only">Satuan: Watt (W)</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="jumlah_unit" class="block text-sm font-semibold text-slate-700">Jumlah Unit</label>
                        <input id="jumlah_unit" type="number" name="jumlah_unit" min="1" value="{{ old('jumlah_unit', $device->jumlah_unit) }}" required
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('devices.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-600/20 rounded-xl transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
