@extends('layouts.app')

@section('title', 'Target Penghematan')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-1">Target Penghematan</h4>
    <p class="text-muted small mb-4">Tetapkan persentase penghematan listrik bulanan Anda.</p>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Info Batas Boros ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">Informasi Konsumsi Bulan Ini</h6>
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <div class="p-3 rounded bg-light text-center">
                        <div class="fs-4 fw-bold text-primary">{{ number_format($monthUsage, 2) }}</div>
                        <div class="small text-muted">kWh terpakai</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-3 rounded bg-light text-center">
                        <div class="fs-4 fw-bold text-danger">{{ number_format($batasBoros, 2) }}</div>
                        <div class="small text-muted">Batas boros (kWh/bln)</div>
                    </div>
                </div>
                @if ($user->target_hemat !== null)
                <div class="col-6 col-md-4">
                    <div class="p-3 rounded bg-light text-center">
                        <div class="fs-4 fw-bold {{ $savingStatus === 'tercapai' ? 'text-success' : 'text-warning' }}">
                            {{ number_format($targetKwh, 2) }}
                        </div>
                        <div class="small text-muted">Target kWh ({{ $user->target_hemat }}% hemat)</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Status target saat ini --}}
            @if ($user->target_hemat !== null)
                <div class="mt-3 alert {{ $savingStatus === 'tercapai' ? 'alert-success' : 'alert-warning' }} mb-0">
                    @if ($savingStatus === 'tercapai')
                        ✅ Konsumsi bulan ini <strong>{{ number_format($monthUsage, 2) }} kWh</strong>
                        — target tercapai! (target ≤ {{ number_format($targetKwh, 2) }} kWh)
                    @else
                        ⚠️ Konsumsi bulan ini <strong>{{ number_format($monthUsage, 2) }} kWh</strong>
                        — melebihi target {{ number_format($targetKwh, 2) }} kWh.
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── Form Set Target ── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">
                {{ $user->target_hemat !== null ? 'Ubah Target Penghematan' : 'Tetapkan Target Penghematan' }}
            </h6>

            <form action="{{ route('saving-target.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="target_hemat" class="form-label">
                        Persentase Target Penghematan
                        <span class="text-muted small">(1% – 50%)</span>
                    </label>
                    <div class="input-group" style="max-width: 280px;">
                        <input
                            type="number"
                            id="target_hemat"
                            name="target_hemat"
                            class="form-control @error('target_hemat') is-invalid @enderror"
                            min="1"
                            max="50"
                            value="{{ old('target_hemat', $user->target_hemat ?? '') }}"
                            placeholder="Contoh: 20"
                            required
                        >
                        <span class="input-group-text">%</span>
                        @error('target_hemat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">
                        Sistem akan menghitung batas konsumsi ideal:
                        <strong>Batas boros × (1 − persentase / 100)</strong>.
                    </div>
                </div>

                {{-- Preview kalkulasi (dinamis via JS) --}}
                <div id="preview-target" class="alert alert-info small mb-3 d-none">
                    Target kWh Anda: <strong id="preview-kwh">-</strong> kWh/bulan
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-floppy me-1"></i> Simpan Target
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

