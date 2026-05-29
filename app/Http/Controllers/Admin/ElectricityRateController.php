<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectricityRate;
use Illuminate\Http\Request;

class ElectricityRateController extends Controller
{
    public function index()
    {
        $rates = ElectricityRate::orderBy('daya_va')->paginate(20);
        return view('admin.electricity_rates.index', compact('rates'));
    }

    public function create()
    {
        return view('admin.electricity_rates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'daya_va' => 'required|integer|unique:electricity_rates,daya_va',
            'tarif_per_kwh' => 'required|numeric|min:0',
        ]);

        ElectricityRate::create($data);

        return redirect()->route('admin.electricity_rates.index')->with('success','Tarif listrik ditambahkan');
    }

    public function edit(ElectricityRate $electricity_rate)
    {
        return view('admin.electricity_rates.edit', ['rate' => $electricity_rate]);
    }

    public function update(Request $request, ElectricityRate $electricity_rate)
    {
        $data = $request->validate([
            'daya_va' => 'required|integer|unique:electricity_rates,daya_va,'.$electricity_rate->id,
            'tarif_per_kwh' => 'required|numeric|min:0',
        ]);

        $electricity_rate->update($data);

        return redirect()->route('admin.electricity_rates.index')->with('success','Tarif listrik diupdate');
    }

    public function destroy(ElectricityRate $electricity_rate)
    {
        $electricity_rate->delete();
        return redirect()->route('admin.electricity_rates.index')->with('success','Tarif listrik dihapus');
    }
}
