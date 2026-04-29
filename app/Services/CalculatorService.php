<?php

namespace App\Services;

class CalculatorService
{


    public function calculateCost(float $totalKwh, float $tariffPerKwh): float
    {
        return $totalKwh * $tariffPerKwh;
    }

    public function calculateCo2(float $totalKwh): float
    {
        $emissionFactor = (float) config('constants.co2_factor');

        return $totalKwh * $emissionFactor;
    }
}
