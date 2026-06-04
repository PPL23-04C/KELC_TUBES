@extends('layouts.app')

@section('title', 'Perangkat')
@section('page-title', 'Manajemen Perangkat')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Perangkat Anda</h1>
            <p class="text-slate-500 mt-1 text-sm">Kelola daftar perangkat elektronik untuk analisis konsumsi yang akurat.</p>
        </div>
        <a href="{{ route('devices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Perangkat
        </a>
    </div>

    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Perangkat</th>
                        <th class="px-6 py-4 font-semibold">Daya (Watt)</th>
                        <th class="px-6 py-4 font-semibold">Jumlah Unit</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($devices as $device)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-slate-900">{{ $device->nama_device }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $device->daya_watt }}</span> W
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $device->jumlah_unit }} Unit
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('devices.edit', $device) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                        <i data-lucide="box" class="w-8 h-8"></i>
                                    </div>
                                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Belum ada perangkat</h3>
                                    <p class="text-sm text-slate-500 mb-4 max-w-sm">Anda belum menambahkan perangkat elektronik apa pun. Tambahkan perangkat sekarang untuk mulai analisis.</p>
                                    <a href="{{ route('devices.create') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                        + Buat perangkat baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
