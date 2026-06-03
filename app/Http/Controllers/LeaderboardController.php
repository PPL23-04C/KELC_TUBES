<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $monthlyLogs = MonitoringLog::query()
            ->select('user_id', DB::raw('SUM(total_kwh) as total_kwh'))
            ->with('user:id,name,daya_va')
            ->groupBy('user_id')
            ->orderBy('total_kwh', 'asc')
            ->orderBy('user_id', 'asc')
            ->get();

        $leaderboard = $monthlyLogs
            ->values()
            ->map(function ($entry, $index) {
                return [
                    'rank'      => $index + 1,
                    'user_id'   => (int) $entry->user_id,
                    'user'      => $entry->user ? [
                        'name'    => $entry->user->name,
                        'daya_va' => $entry->user->daya_va,
                    ] : [],
                    'total_kwh' => round((float) $entry->total_kwh, 2),
                    'label'     => $entry->user?->daya_va
                        ? $entry->user->daya_va . ' VA'
                        : 'VA belum diatur',
                ];
            });

        return view('leaderboards.index', compact('leaderboard'));
    }
}