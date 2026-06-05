<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingTargetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_saving_target_page(): void
    {
        $response = $this->get(route('saving-target.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_saving_target_page_and_default_is_null(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => null,
        ]);

        $response = $this->actingAs($user)->get(route('saving-target.index'));

        $response->assertOk();
        $response->assertSee('Atur Target Penghematan');
        $response->assertViewHas('user');
        $response->assertViewHas('batasBoros', 210.0); // 7.0 * 30 = 210
        $response->assertViewHas('targetKwh', null);
    }

    public function test_user_can_set_valid_saving_target(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => null,
        ]);

        $response = $this->actingAs($user)->post(route('saving-target.store'), [
            'target_hemat' => 20
        ]);

        $response->assertRedirect(route('saving-target.index'));
        $response->assertSessionHas('success');
        $this->assertEquals(20, $user->fresh()->target_hemat);
    }

    public function test_user_cannot_set_invalid_saving_target(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => null,
        ]);

        // Too high
        $response = $this->actingAs($user)->post(route('saving-target.store'), [
            'target_hemat' => 51
        ]);
        $response->assertSessionHasErrors('target_hemat');
        $this->assertNull($user->fresh()->target_hemat);

        // Too low
        $response = $this->actingAs($user)->post(route('saving-target.store'), [
            'target_hemat' => 0
        ]);
        $response->assertSessionHasErrors('target_hemat');
        $this->assertNull($user->fresh()->target_hemat);
    }

    public function test_user_can_delete_saving_target(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => 20,
        ]);

        $response = $this->actingAs($user)->delete(route('saving-target.destroy'));

        $response->assertRedirect(route('saving-target.index'));
        $response->assertSessionHas('success');
        $this->assertNull($user->fresh()->target_hemat);
    }

    public function test_dashboard_displays_promotional_banner_when_target_is_null(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Atur Target Penghematan Bulanan Anda!');
        $response->assertDontSee('id="w2s0ed"', false);
    }

    public function test_dashboard_displays_saving_metrics_when_target_is_active(): void
    {
        $user = User::factory()->create([
            'daya_va' => 1300,
            'target_hemat' => 20,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('id="w2s0ed"', false);
        $response->assertSee('VA Rumah: 1300 VA', false);
        $response->assertSee('Kategori Boros: 210 kWh', false);
        $response->assertSee('Target Hemat: 20%', false);
        $response->assertSee('Target Maksimal Pemakaian: 168 kWh/bulan', false);
        $response->assertSee(route('recommendations.index'));
    }
}