<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // public function test_that_sign_in_page_contains_sign_in(): void
    // {
    //     $response = $this->get('/admin/login');

    //     $response->assertSee('Sign in');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase;

    // public function test_user_can_login()
    // {
    //     // Create a user for testing
    //     $user = User::factory()->create([
    //         'name' => 'test',
    //         'email' => 'test@example.com',
    //         'password' => Hash::make('password123'),
    //     ]);

    //     // Simulate a login request
    //     $response = $this->post('/admin/login', [
    //         'email' => 'test@example.com',
    //         'password' => 'password123',
    //     ]);

    //     // Assert that the user is redirected after login
    //     $response->assertRedirect('/admin');

    //     // Assert that the user is authenticated
    //     $this->assertAuthenticatedAs($user);
    // }
}
