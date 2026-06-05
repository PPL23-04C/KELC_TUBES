<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_leaderboard_filtered_by_month_and_va(): void
    {
        $currentUser = User::factory()->create([
            'name' => 'Rani',
            'daya_va' => 1300,
        ]);

        $otherUserA = User::factory()->create([
            'name' => 'Ari',
            'daya_va' => 1300,
        ]);

        $otherUserB = User::factory()->create([
            'name' => 'Bima',
            'daya_va' => 2200,
        ]);

        $currentDevice = Device::create([
            'user_id' => $currentUser->id,
            'nama_device' => 'AC',
            'daya_watt' => 600,
            'jumlah_unit' => 1,
        ]);

        $deviceA = Device::create([
            'user_id' => $otherUserA->id,
            'nama_device' => 'Lampu',
            'daya_watt' => 50,
            'jumlah_unit' => 1,
        ]);

        $deviceB = Device::create([
            'user_id' => $otherUserB->id,
            'nama_device' => 'Kulkas',
            'daya_watt' => 200,
            'jumlah_unit' => 1,
        ]);

        MonitoringLog::create([
            'user_id' => $currentUser->id,
            'device_id' => $currentDevice->id,
            'tanggal' => '2026-05-03',
            'jam_pemakaian' => 10,
            'total_kwh' => 6.00,
        ]);

        MonitoringLog::create([
            'user_id' => $otherUserA->id,
            'device_id' => $deviceA->id,
            'tanggal' => '2026-05-07',
            'jam_pemakaian' => 10,
            'total_kwh' => 3.00,
        ]);

        MonitoringLog::create([
            'user_id' => $otherUserB->id,
            'device_id' => $deviceB->id,
            'tanggal' => '2026-05-09',
            'jam_pemakaian' => 10,
            'total_kwh' => 8.00,
        ]);

        $response = $this->actingAs($currentUser)->get(route('leaderboards.index', [
            'month' => '2026-05',
            'daya_va' => 1300,
        ]));

        $response->assertOk();
        $response->assertViewHas('selectedVa', '1300');
        $response->assertViewHas('selectedMonth', '2026-05');
        $response->assertViewHas('leaderboard', function ($leaderboard) {
            return $leaderboard->count() === 2
                && $leaderboard->first()['user']['name'] === 'Ari'
                && $leaderboard->first()['total_kwh'] === 3.0
                && $leaderboard->last()['user']['name'] === 'Rani'
                && $leaderboard->last()['total_kwh'] === 6.0;
        });
    }
}