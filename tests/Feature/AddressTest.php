<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_address_with_minimal_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/addresses', [
            // 'name' => 'Home', // Nullable
            'recipient_name' => 'John Doe',
            // 'address_line_1' => '123 Main St', // Nullable
            'city' => 'Anytown',
            'state' => 'State',
            // 'zip_code' => '12345', // Nullable
            // 'country' => 'Country', // Nullable
            'phone_number' => '1234567890',
            'backup_phone_number' => '0987654321', // Optional
            'is_default' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'recipient_name' => 'John Doe',
            'backup_phone_number' => '0987654321',
            'is_default' => true,
        ]);
        
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'address_line_1' => null, // verifying nullable
        ]);
    }

    public function test_user_can_list_addresses()
    {
        $user = User::factory()->create();
        Address::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/addresses');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }
}
