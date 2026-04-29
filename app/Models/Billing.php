<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_id',
        'estimasi_biaya',
        'tarif_per_kwh',
    ];

    protected $casts = [
        'estimasi_biaya' => 'decimal:2',
        'tarif_per_kwh' => 'decimal:2',
    ];

    public function monitoringLog()
    {
        return $this->belongsTo(MonitoringLog::class, 'log_id');
    }
}
