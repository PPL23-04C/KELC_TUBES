@extends('layouts.app')

@section('title', 'Perangkat')
@section('page-title', 'Perangkat')

@section('content')
    <div class="device-header">
        <div class="meta">Kelola daftar perangkat elektronik Anda.</div>
        <a class="btn" href="{{ route('devices.create') }}">+ Tambah Perangkat</a>
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
                        <td class="action-cell">
                            <a class="btn btn-sm" href="{{ route('devices.edit', $device) }}">Ubah</a>
                            <form method="POST" action="{{ route('devices.destroy', $device) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm danger" type="submit" onclick="return confirm('Hapus perangkat ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">Belum ada perangkat. <a href="{{ route('devices.create') }}">Buat perangkat baru</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
