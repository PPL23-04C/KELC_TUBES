<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SavingTargetController extends Controller
{
    public function index(): View
    {
        $user       = auth()->user();
        $now        = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();

        $monthUsage = (float) MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->sum('total_kwh');

        $batasBoros = $user->getBatasBorosKwh();

        $targetKwh   = null;
        $savingStatus = null;

        if ($user->target_hemat !== null) {
            $targetKwh    = $batasBoros * (1 - ($user->target_hemat / 100));
            $savingStatus = $monthUsage <= $targetKwh ? 'tercapai' : 'melebihi';
        }

        return view('saving-target.index', compact(
            'user',
            'monthUsage',
            'batasBoros',
            'targetKwh',
            'savingStatus'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_hemat' => ['required', 'integer', 'min:1', 'max:50'],
        ], [
            'target_hemat.required' => 'Persentase target penghematan wajib diisi.',
            'target_hemat.integer'  => 'Persentase target penghematan harus berupa angka bulat.',
            'target_hemat.min'      => 'Target penghematan minimal adalah 1%.',
            'target_hemat.max'      => 'Target penghematan maksimal adalah 50%.',
        ]);

        auth()->user()->update([
            'target_hemat' => $data['target_hemat'],
        ]);

        return redirect()
            ->route('saving-target.index')
            ->with('success', 'Target penghematan listrik bulanan berhasil disimpan.');
    }
}