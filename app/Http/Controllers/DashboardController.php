<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Services\AlertService;
use App\Services\RecommendationService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private AlertService $alertService,
        private RecommendationService $recommendationService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $now = Carbon::today();

        $totalKwh = MonitoringLog::where('user_id', $user->id)->sum('total_kwh');
        $totalCost = MonitoringLog::where('user_id', $user->id)
            ->with('billing')
            ->get()
            ->sum(function ($log) {
                return $log->billing ? (float) $log->billing->estimasi_biaya : 0;
            });
        $totalCo2 = MonitoringLog::where('user_id', $user->id)
            ->with('co2Impact')
            ->get()
            ->sum(function ($log) {
                return $log->co2Impact ? (float) $log->co2Impact->emisi_co2 : 0;
            });

        $todayKwh = MonitoringLog::where('user_id', $user->id)
            ->whereDate('tanggal', $now)
            ->sum('total_kwh');

        $weekStart = $now->copy()->subDays(6);
        $weekKwh = MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$weekStart, $now])
            ->sum('total_kwh');

        $chartDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartDays->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
            ]);
        }

        $chartData = $chartDays->map(function ($day) use ($user) {
            $value = MonitoringLog::where('user_id', $user->id)
                ->whereDate('tanggal', $day['date'])
                ->sum('total_kwh');

            return [
                'label' => $day['label'],
                'value' => (float) $value,
            ];
        });

        $previousStart = $now->copy()->subDays(28);
        $previousEnd = $now->copy()->subDay();
        $previousTotal = MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$previousStart, $previousEnd])
            ->sum('total_kwh');

        $alert = $this->alertService->checkDailyLimit((float) $todayKwh, (int) $user->daya_va);

        $tips = $this->recommendationService->buildRecommendations((float) $weekKwh, (float) ($previousTotal / 4));

        return view('dashboard.index', [
            'totalKwh' => $totalKwh,
            'totalCost' => $totalCost,
            'totalCo2' => $totalCo2,
            'todayKwh' => $todayKwh,
            'weekKwh' => $weekKwh,
            'chartData' => $chartData,
            'alert' => $alert,
            'tips' => $tips,
        ]);
    }
}
