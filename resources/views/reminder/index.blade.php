@extends('layouts.app')

@section('title', 'Pengingat Alat')
@section('page-title', 'Pengingat Penggunaan Alat Elektronik')

@section('content')
<style>
    @keyframes modalPop {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="max-w-5xl mx-auto space-y-8">
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Mulai Timer Baru</h3>
        </div>
        <p class="text-slate-500 text-sm mb-6 ml-13">Atur pengingat untuk alat yang sedang Anda gunakan. Anda dapat menjalankan banyak pengingat sekaligus.</p>

        <form id="reminder-form" class="ml-13 space-y-5">
            <div class="space-y-2">
                <label for="device_id" class="block text-sm font-semibold text-slate-700">Pilih Alat Elektronik</label>
                <div class="relative">
                        <select name="device_id" id="device_id" required aria-describedby="device_select_help"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" data-name="{{ $device->nama_device }}">{{ $device->nama_device }} ({{ $device->daya_watt }} W)</option>
                        @endforeach
                    </select>
                    
                    <span id="device_select_help" class="sr-only">Pilih perangkat elektronik</span>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="space-y-2 flex-2">
                    <label for="duration" class="block text-sm font-semibold text-slate-700">Durasi Waktu</label>
                    <input type="number" id="duration" min="1" step="1" required placeholder="Contoh: 30" 
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                </div>
                <div class="space-y-2 flex-1">
                    <label for="unit" class="block text-sm font-semibold text-slate-700">Satuan</label>
                    <div class="relative">
                        <select id="unit" aria-describedby="unit_select_help" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                            <option value="minutes">Menit</option>
                            <option value="hours">Jam</option>
                        </select>
                        
                        <span id="unit_select_help" class="sr-only">Pilih satuan waktu (menit atau jam)</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                <i data-lucide="rocket" class="w-5 h-5"></i>
                Tambahkan Timer
            </button>
        </form>
    </div>

    <!-- Daftar Timer Aktif -->
    <div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                Daftar Pengingat Aktif
            </h3>
        </div>
        
        <div id="empty-state" class="bg-slate-50 border border-slate-200 border-dashed rounded-3xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-sm">
                <i data-lucide="timer" class="w-8 h-8"></i>
            </div>
            <p class="text-slate-500 text-lg font-medium">Belum ada timer yang berjalan saat ini.</p>
        </div>

        <div id="timers-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Timer cards will be injected here by JS -->
        </div>
    </div>
</div>
