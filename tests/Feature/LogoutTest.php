<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_a_logout_form_and_posting_it_logs_out_the_user(): void
    {
        $user = User::create([
            'name' => 'Logout Test',
            'phone' => '01987654321',
            'email' => null,
            'password' => 'test-password',
            'preferred_lang' => 'bn',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Logout Test')
            ->assertSee('লগআউট')
            ->assertSee('action="'.route('logout').'"', false);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
