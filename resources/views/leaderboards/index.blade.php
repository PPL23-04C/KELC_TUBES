@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard Hemat Energi')

@section('content')
<div class="card">
    <h3>Top pengguna paling hemat</h3>

    @if($leaderboard->isEmpty())
        <div style="margin-top:20px; color:var(--muted);">
            Belum ada data konsumsi.
        </div>
    @else
        <div style="overflow-x:auto; margin-top:20px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #dbe4f2;">
                        <th style="padding:12px 10px;">Peringkat</th>
                        <th style="padding:12px 10px;">Nama</th>
                        <th style="padding:12px 10px;">Jenis VA</th>
                        <th style="padding:12px 10px;">Total kWh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderboard as $entry)
                        <tr style="border-bottom:1px solid #eef3fb;">
                            <td style="padding:12px 10px; font-weight:700;">#{{ $entry['rank'] }}</td>
                            <td style="padding:12px 10px;">{{ $entry['user']['name'] ?? 'Pengguna' }}</td>
                            <td style="padding:12px 10px;">{{ $entry['label'] }}</td>
                            <td style="padding:12px 10px;">{{ number_format($entry['total_kwh'], 2) }} kWh</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection