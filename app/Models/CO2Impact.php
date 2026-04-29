<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CO2Impact extends Model
{
    use HasFactory;

    protected $table = 'co2_impacts';

    protected $fillable = [
        'log_id',
        'emisi_co2',
        'faktor_emisi',
    ];

    protected $casts = [
        'emisi_co2' => 'decimal:2',
        'faktor_emisi' => 'decimal:3',
    ];

    public function monitoringLog()
    {
        return $this->belongsTo(MonitoringLog::class, 'log_id');
    }
}
