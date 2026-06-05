<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\CO2Impact;
use App\Models\Device;
use App\Models\MonitoringLog;
use App\Services\CalculatorService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(private CalculatorService $calculatorService)
    {
    }

    public function index()
    {
        $devices = Device::where('user_id', auth()->id())->get();
        return view('reminder.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'jam_pemakaian' => 'required|numeric|min:0.01',
        ]);

        $device = Device::where('user_id', auth()->id())
            ->where('id', $request->input('device_id'))
            ->firstOrFail();

        $kwh = $this->calculatorService->calculateKwh(
            (float) $device->daya_watt,
            (float) $request->input('jam_pemakaian'),
            (int) $device->jumlah_unit
        );

        // Record the usage as today
        $log = MonitoringLog::create([
            'user_id' => auth()->id(),
            'device_id' => $device->id,
            'tanggal' => now()->toDateString(),
            'jam_pemakaian' => $request->input('jam_pemakaian'),
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

        return response()->json([
            'status' => 'success',
            'message' => 'Penggunaan alat ' . $device->nama_device . ' selama ' . $request->input('jam_pemakaian') . ' jam telah dicatat ke riwayat.'
        ]);
    }
}
