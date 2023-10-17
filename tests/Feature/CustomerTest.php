<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_a_customer(): void
    {
        
        // $user = User::factory()->create();

        $response = $this->get('admin');

        // $response = $this->get('/admin/customers');
        // $response->assertSeeText("Dashboard");
        
        // $response->dumpSession();

        $response->assertStatus(200);
    }
}
