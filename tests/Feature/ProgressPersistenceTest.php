<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_mock_test_submission_is_saved_for_the_authenticated_user(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson(route('progress.mock'), [
                'day_number' => 1,
                'score' => 8,
            ])
            ->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('completed_day_numbers', [1]);

        $this->assertDatabaseHas('vocab_progress', [
            'user_id' => $user->id,
            'day_number' => 1,
            'score' => 8,
            'passed' => true,
            'is_completed' => true,
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('completedDays: [1]');
    }

    public function test_failed_mock_test_is_saved_without_completing_the_day(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson(route('progress.mock'), [
                'day_number' => 1,
                'score' => 6,
            ])
            ->assertOk()
            ->assertJsonPath('passed', false)
            ->assertJsonPath('completed_day_numbers', []);

        $this->assertDatabaseHas('vocab_progress', [
            'user_id' => $user->id,
            'day_number' => 1,
            'score' => 6,
            'passed' => false,
            'is_completed' => false,
        ]);
    }

    public function test_dashboard_uses_the_users_saved_language_preference(): void
    {
        $user = $this->createUser();
        $user->preferred_lang = 'jp';
        $user->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('lang: "ja"', false);
    }

    public function test_manual_completion_and_reset_are_persisted(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson(route('progress.complete'), [
                'day_number' => 2,
                'completed' => true,
            ])
            ->assertOk()
            ->assertJsonPath('completed_day_numbers', [2]);

        $this->assertDatabaseHas('vocab_progress', [
            'user_id' => $user->id,
            'day_number' => 2,
            'is_completed' => true,
        ]);

        $this->postJson(route('progress.reset'))
            ->assertOk()
            ->assertJsonPath('completed_day_numbers', []);

        $this->assertDatabaseMissing('vocab_progress', [
            'user_id' => $user->id,
            'day_number' => 2,
        ]);
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Progress Test',
            'phone' => fake()->unique()->numerify('01#########'),
            'email' => null,
            'password' => 'test-password',
            'preferred_lang' => 'bn',
        ]);
    }
}
