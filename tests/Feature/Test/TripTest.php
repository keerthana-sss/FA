<?php

namespace Tests\Feature\Test;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        // create a user for authentication
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /** @test */
    public function user_can_create_trip()
    {
        $this->actingAs($this->user, 'api');

        $payload = [
            'title' => 'Test Trip',
            'destination' => 'Test Destination',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ];

        $response = $this->postJson('/api/trips', $payload);
        $this->assertDatabaseHas('trips', [
            'title' => 'Test Trip',
            'owner_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_see_trips_in_database()
    {
        $this->actingAs($this->user, 'api');

        $trip = Trip::factory()->create([
            'owner_id'   => $this->user->id,
            'title'      => 'Sample Trip 1',
            'destination' => 'Paris',
        ]);

        $this->getJson('/api/trips');

        // foreach ($trips as $trip) {
            $this->assertDatabaseHas('trips', [
                'id' => $trip->id,
                'owner_id' => $this->user->id,
            ]);
        // }
    }

    /** @test */
    public function owner_can_update_own_trip()
    {
        $trip = Trip::factory()->create([
            'owner_id' => $this->user->id,
            'title' => 'Original Trip',
            'destination' => 'Destination',
        ]);

        $this->actingAs($this->user, 'api');

        $payload = [
            'title' => 'Updated Trip',
            'destination' => 'Updated Destination',
        ];

        $response = $this->putJson("/api/trips/{$trip->id}", $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'title' => 'Updated Trip',
        ]);
    }

    /** @test */
    public function non_owner_cannot_update_trip()
    {
        $trip = Trip::factory()->create([
            'owner_id' => $this->otherUser->id,
            'title' => 'Other Trip',
            'destination' => 'Destination',
        ]);

        $this->actingAs($this->user, 'api');

        $payload = [
            'title' => 'Hacked Trip',
        ];

        $response = $this->putJson("/api/trips/{$trip->id}", $payload);
        $response->assertStatus(403);
    }

     /** @test */
    public function owner_can_add_member()
    {
        $trip = Trip::factory()->create([
            'owner_id' => $this->user->id,
            'title' => 'Other Trip',
            'destination' => 'Destination',
        ]);

        Passport::actingAs($this->user);

        $response = $this->postJson("/api/trips/{$trip->id}/add-member", [
            'user_id' => $this->otherUser->id,
            'role' => 'traveler',
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('trip_user', [
            'trip_id' => $trip->id,
            'user_id' => $this->otherUser->id,
            'role' => 'traveler',
        ]);

    }
}
