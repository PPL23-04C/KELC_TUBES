<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricityRate extends Model
{
    protected $table = 'electricity_rates';

    protected $fillable = [
        'daya_va',
        'tarif_per_kwh',
    ];

    protected $casts = [
        'daya_va' => 'integer',
        'tarif_per_kwh' => 'decimal:2',
    ];
}
