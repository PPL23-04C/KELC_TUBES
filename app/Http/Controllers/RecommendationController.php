<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Models\MonitoringLog;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationService $recommendationService)
    {
    }

    public function index(): View
    {
        $user = auth()->user();
        $now = Carbon::today();
        $weekStart = $now->copy()->subDays(6);

        $weekUsage = MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$weekStart, $now])
            ->sum('total_kwh');

        $previousStart = $weekStart->copy()->subDays(28);
        $previousEnd = $weekStart->copy()->subDay();
        $previousTotal = MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$previousStart, $previousEnd])
            ->sum('total_kwh');
        $averageWeekly = $previousTotal / 4;

        $tips = $this->recommendationService->buildRecommendations((float) $weekUsage, (float) $averageWeekly);

        return view('recommendations.index', compact('tips', 'weekUsage'));
    }
}
