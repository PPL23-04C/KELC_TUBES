<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_device',
        'daya_watt',
        'jumlah_unit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monitoringLogs()
    {
        return $this->hasMany(MonitoringLog::class);
    }
}
