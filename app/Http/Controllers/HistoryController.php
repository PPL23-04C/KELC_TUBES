<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = MonitoringLog::with(['device', 'billing', 'co2Impact'])
            ->where('user_id', auth()->id());

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->input('end_date'));
        }

        $logs = $query->orderByDesc('tanggal')->get();

        return view('history.index', compact('logs'));
    }
}
