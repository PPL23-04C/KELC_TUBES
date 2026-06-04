<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $selectedMonth = (string) ($request->input('month') ?? now()->format('Y-m'));
        $selectedVa = (string) ($request->input('daya_va') ?? auth()->user()->daya_va ?? 'all');

        $month = Carbon::createFromFormat('Y-m', $selectedMonth);
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $monthlyLogs = MonitoringLog::query()
            ->whereBetween('tanggal', [$start, $end])
            ->when($selectedVa !== 'all', function ($query) use ($selectedVa) {
                $query->whereHas('user', function ($userQuery) use ($selectedVa) {
                    $userQuery->where('daya_va', (int) $selectedVa);
                });
            })
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
                    'label'     => $entry->user?->daya_va ? $entry->user->daya_va . ' VA' : 'VA belum diatur',
                ];
            });

        $currentUserEntry = $monthlyLogs->firstWhere('user_id', auth()->id());

        $currentUserTotal = $currentUserEntry
            ? [
                'rank'      => $monthlyLogs->search(fn ($entry) => (int) $entry->user_id === auth()->id()) + 1,
                'total_kwh' => round((float) $currentUserEntry->total_kwh, 2),
            ]
            : null;

        $availableMonths = MonitoringLog::query()
            ->orderBy('tanggal', 'desc')
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->values();

        if ($availableMonths->isEmpty()) {
            $availableMonths = collect([$selectedMonth]);
        }

        $availableVas = User::query()
            ->whereNotNull('daya_va')
            ->distinct()
            ->orderBy('daya_va')
            ->pluck('daya_va');

        $monthLabel      = Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y');
        $selectedVaLabel = $selectedVa === 'all' ? 'Semua VA' : $selectedVa . ' VA';

        return view('leaderboards.index', compact(
            'selectedMonth',
            'selectedVa',
            'selectedVaLabel',
            'monthLabel',
            'leaderboard',
            'availableMonths',
            'availableVas',
            'currentUserTotal'
        ));
    }
}