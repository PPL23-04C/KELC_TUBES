<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_recommendation_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
        ]);
        $response = $this->actingAs($user)->get(route('recommendations.index'));
        $response->assertOk();
    }

    public function test_user_can_toggle_recommendation_checklist(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
        ]);
        $device = \App\Models\Device::create([
            'user_id' => $user->id,
            'nama_device' => 'AC Panasonic',
            'daya_watt' => 1000,
            'jumlah_unit' => 1,
        ]);
        
        // Assert checklist is empty initially
        $this->assertEquals(0, $user->recommendations()->count());

        // Perform toggle POST request
        $response = $this->actingAs($user)->postJson(route('recommendations.toggleTipChecklist'), [
            'device_id' => $device->id,
            'tip_index' => 0,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_completed' => true,
        ]);

        // Check database
        $this->assertEquals(1, $user->recommendations()->count());
        $this->assertEquals('checklist', $user->recommendations()->first()->tipe);
        $this->assertEquals("{$device->id}_0", $user->recommendations()->first()->pesan);

        // Toggle again to uncheck
        $response = $this->actingAs($user)->postJson(route('recommendations.toggleTipChecklist'), [
            'device_id' => $device->id,
            'tip_index' => 0,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_completed' => false,
        ]);

        $this->assertEquals(0, $user->recommendations()->count());
    }
}

