<?php

namespace Database\Seeders;

use App\Models\ElectricityRate;
use Illuminate\Database\Seeder;

class ElectricityRateSeeder extends Seeder
{
    /**
     * Seed the electricity rates table.
     */
    public function run(): void
    {
        $rates = [
            450 => 1444.70,
            900 => 1444.70,
            1300 => 1444.70,
            2200 => 1444.70,
            3500 => 1444.70,
            5500 => 1444.70,
            6600 => 1444.70,
        ];

        foreach ($rates as $dayaVa => $tarif) {
            ElectricityRate::updateOrCreate(
                ['daya_va' => $dayaVa],
                ['tarif_per_kwh' => $tarif]
            );
        }
    }
}
