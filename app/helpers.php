<?php

use App\Models\ElectricityRate;

if (! function_exists('getTarifListrik')) {
    function getTarifListrik($va): float
    {
        // try database first
        try {
            if ($va) {
                $rate = ElectricityRate::where('daya_va', $va)->first();
                if ($rate) {
                    return (float) $rate->tarif_per_kwh;
                }
            }
        } catch (\Throwable $e) {
            // ignore DB errors and fallback to config
        }

        // fallback to config default
        $default = config('constants.tariffs.default', 1444.70);
        return (float) $default;
    }
}
