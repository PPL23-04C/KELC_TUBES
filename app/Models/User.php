<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\Recommendation;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'daya_va',
        'role',
        'target_hemat',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function monitoringLogs()
    {
        return $this->hasMany(MonitoringLog::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function getTariffPerKwhAttribute(): float
    {
        $dayaVa = (int) $this->daya_va;
        return (float) getTarifListrik($dayaVa);
    }

    public function getBatasBorosKwh(): float
    {
        $thresholds = config('constants.daily_usage_thresholds', []);
        $dayaVa = (int) $this->daya_va;
        $resolvedTier = null;
        foreach ($thresholds as $tier) {
            if ($dayaVa <= (int) $tier['max_va']) {
                $resolvedTier = $tier;
                break;
            }
        }
        if (!$resolvedTier) {
            $resolvedTier = end($thresholds);
        }
        $sedangLimit = isset($resolvedTier['sedang']) ? (float) $resolvedTier['sedang'] : 7.0;
        return $sedangLimit * 30;
    }
}
