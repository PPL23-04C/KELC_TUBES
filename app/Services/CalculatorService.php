<?php

namespace App\Services;

class CalculatorService
{
    public function calculateKwh(float $watt, float $hours, int $unit): float
    {
        return ($watt * $hours * $unit) / 1000;
    }

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
