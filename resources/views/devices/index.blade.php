@extends('layouts.app')

@section('title', 'Perangkat')
@section('page-title', 'Perangkat')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div class="meta">Kelola daftar perangkat elektronik Anda.</div>
        <a class="btn" href="{{ route('devices.create') }}">Tambah Perangkat</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Daya (Watt)</th>
                    <th>Jumlah Unit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr>
                        <td>{{ $device->nama_device }}</td>
                        <td>{{ $device->daya_watt }}</td>
                        <td>{{ $device->jumlah_unit }}</td>
                        <td>
                            <a class="btn secondary" href="{{ route('devices.edit', $device) }}">Ubah</a>
                            <form method="POST" action="{{ route('devices.destroy', $device) }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit" onclick="return confirm('Hapus perangkat ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada perangkat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
