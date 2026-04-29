@extends('layouts.app')

@section('title', 'Rekomendasi')
@section('page-title', 'Rekomendasi Hemat Energi')

@section('content')
    <div class="card">
        <h3>Total penggunaan minggu ini</h3>
        <div class="value">{{ number_format($weekUsage, 2) }} kWh</div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Tips & Saran</h3>
        <ul style="margin: 0; padding-left: 18px; color: var(--muted);">
            @foreach($tips as $tip)
                <li>{{ $tip }}</li>
            @endforeach
        </ul>
    </div>
@endsection