<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('co2_impacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('monitoring_logs')->cascadeOnDelete();
            $table->decimal('emisi_co2', 10, 2);
            $table->decimal('faktor_emisi', 5, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('co2_impacts');
    }
};
