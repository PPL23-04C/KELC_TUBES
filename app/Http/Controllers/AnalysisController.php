<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalysisRequest;
use App\Models\Billing;
use App\Models\CO2Impact;
use App\Models\Device;
use App\Models\MonitoringLog;
use App\Services\CalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function __construct(private CalculatorService $calculatorService)
    {
    }

    public function create(): View
    {
        $devices = Device::where('user_id', auth()->id())->get();

        return view('analysis.input', compact('devices'));
    }

    public function store(AnalysisRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $device = Device::where('user_id', auth()->id())
            ->where('id', $data['device_id'])
            ->firstOrFail();

        $kwh = $this->calculatorService->calculateKwh(
            (float) $device->daya_watt,
            (float) $data['jam_pemakaian'],
            (int) $device->jumlah_unit
        );

        $log = MonitoringLog::create([
            'user_id' => auth()->id(),
            'device_id' => $device->id,
            'tanggal' => $data['tanggal'],
            'jam_pemakaian' => $data['jam_pemakaian'],
            'total_kwh' => $kwh,
        ]);

        $tariff = auth()->user()->tariff_per_kwh;
        $cost = $this->calculatorService->calculateCost($kwh, $tariff);
        $co2 = $this->calculatorService->calculateCo2($kwh);

        Billing::create([
            'log_id' => $log->id,
            'estimasi_biaya' => $cost,
            'tarif_per_kwh' => $tariff,
        ]);

        CO2Impact::create([
            'log_id' => $log->id,
            'emisi_co2' => $co2,
            'faktor_emisi' => (float) config('constants.co2_factor'),
        ]);

        return redirect()->route('dashboard')->with('success', 'Analisis berhasil disimpan.');
    }
}
