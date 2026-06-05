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
   }
}