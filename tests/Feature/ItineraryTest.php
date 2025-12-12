<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use App\Models\Itinerary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->trip = Trip::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        // Attach user as trip member
        $this->trip->users()->attach($this->user->id);
    }

    /** @test */
    public function only_trip_member_can_create_itinerary()
    {
        $this->actingAs($this->user, 'api');

        $payload = [
            'day_number' => 1,
            'title' => 'Day 1 - Sightseeing',
            'description' => 'Visit museums',
            'start_time' => now()->format('Y-m-d H:i'),
            'end_time' => now()->addHours(3)->format('Y-m-d H:i'),
        ];

        $response = $this->postJson("/api/trips/{$this->trip->id}/itineraries", $payload);
        $response->assertStatus(201);

        // Non-member attempt
        $this->actingAs($this->otherUser, 'api');
        $response = $this->postJson("/api/trips/{$this->trip->id}/itineraries", $payload);
        $response->assertStatus(403);
    }

    /** @test */
    public function start_time_and_end_time_validation_works()
    {
        $this->actingAs($this->user, 'api');

        $payload = [
            'day_number' => 1,
            'title' => 'Invalid timing',
            'description' => 'Wrong times',
            'start_time' => now()->addHour()->format('Y-m-d H:i'),
            'end_time' => now()->format('Y-m-d H:i'),
        ];

        $response = $this->postJson("/api/trips/{$this->trip->id}/itineraries", $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_time']);
    }

    /** @test */
    public function member_can_update_itinerary()
    {
        $this->actingAs($this->user, 'api');

        $itinerary = Itinerary::factory()->create([
            'trip_id' => $this->trip->id,
            'created_by' => $this->user->id,
            'day_number' => 1,
            'title' => 'Original',
            'description' => 'Desc',
        ]);

        $payload = [
            'title' => 'Updated title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/trips/{$this->trip->id}/itineraries/{$itinerary->id}", $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('itineraries', [
            'id' => $itinerary->id,
            'title' => 'Updated title',
        ]);

        // Non-member cannot update
        $this->actingAs($this->otherUser, 'api');
        $response = $this->putJson("/api/trips/{$this->trip->id}/itineraries/{$itinerary->id}", $payload);
        $response->assertStatus(403);
    }

    /** @test */
    public function member_can_delete_itinerary()
    {
        $this->actingAs($this->user, 'api');

        $itinerary = Itinerary::factory()->create([
            'trip_id' => $this->trip->id,
            'created_by' => $this->user->id,
            'day_number' => 1,
            'title' => 'To delete',
        ]);

        $response = $this->deleteJson("/api/trips/{$this->trip->id}/itineraries/{$itinerary->id}");
        $response->assertStatus(200);

        $this->assertSoftDeleted('itineraries', ['id' => $itinerary->id]);

        // Non-member cannot delete
        $itinerary = Itinerary::factory()->create([
            'trip_id' => $this->trip->id,
            'created_by' => $this->user->id,
            'title' => 'Non-member itinerary',
            'day_number' => 1,
        ]);

        $this->actingAs($this->otherUser, 'api');
        $response = $this->deleteJson("/api/trips/{$this->trip->id}/itineraries/{$itinerary->id}");
        $response->assertStatus(403);
    }
}
