<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'tanggal',
        'jam_pemakaian',
        'total_kwh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_pemakaian' => 'string',
        'total_kwh' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function billing()
    {
        return $this->hasOne(Billing::class, 'log_id');
    }

    public function co2Impact()
    {
        return $this->hasOne(CO2Impact::class, 'log_id');
    }
}
