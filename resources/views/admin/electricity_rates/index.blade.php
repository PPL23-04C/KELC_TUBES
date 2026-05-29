@extends('layouts.app')

@section('title', 'Tarif Listrik')
@section('page-title', 'Tarif Listrik')

@section('content')
    <div class="device-header">
        <div class="meta">Kelola paket tarif listrik berdasarkan daya (VA)</div>
        <a href="{{ route('admin.electricity_rates.create') }}" class="btn">+ Tambah Tarif</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Daya (VA)</th>
                    <th>Tarif per kWh (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rates as $rate)
                    <tr>
                        <td><strong>{{ number_format($rate->daya_va, 0, ',', '.') }}</strong> VA</td>
                        <td>Rp {{ number_format($rate->tarif_per_kwh, 2, ',', '.') }}</td>
                        <td class="action-cell">
                            <a class="btn btn-sm secondary" href="{{ route('admin.electricity_rates.edit', $rate) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.electricity_rates.destroy', $rate) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm danger" type="submit" onclick="return confirm('Hapus tarif ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">Belum ada tarif. <a href="{{ route('admin.electricity_rates.create') }}">Tambah tarif baru</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rates->hasPages())
        <div style="margin-top: 20px;">{{ $rates->links() }}</div>
    @endif
@endsection
