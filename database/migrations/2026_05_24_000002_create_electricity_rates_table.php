<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('electricity_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('daya_va')->unique();
            $table->decimal('tarif_per_kwh', 10, 2);
            $table->timestamps();
        });

        // Seed initial rates from config/constants.php if present
        try {
            $tariffs = config('constants.tariffs', []);
            foreach ($tariffs as $key => $value) {
                if ($key === 'default') continue;
                if (is_numeric($key)) {
                    \DB::table('electricity_rates')->insert([
                        'daya_va' => (int) $key,
                        'tarif_per_kwh' => (float) $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // ignore during env where config not available
        }
    }

    public function down()
    {
        Schema::dropIfExists('electricity_rates');
    }
};
