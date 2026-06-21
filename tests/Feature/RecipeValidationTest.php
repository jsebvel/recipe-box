<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecipeValidationTest extends TestCase {
    public function test_prep_minutes_must_be_between_1_and_600(): void
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post('/recipes', [
            'title'=> 'Test',
            'body' => 'Body',
            'prep_minutes' => 0,
        ]);

        $response->assertSessionHasErrors('prep_minutes');
    }
    public function test_prep_minutes_accept_valid_range(): void
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post('/recipes', [
            'title'=> 'Test',
            'body' => 'Body',
            'prep_minutes' => 30,
        ]);

        $response->assertRedirect();
    }
}
