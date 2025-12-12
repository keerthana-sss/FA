<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripFileTest extends TestCase
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
            'owner_id' => $this->user->id
        ]);


        $this->trip->users()->attach($this->user->id, ['role' => 'admin']);
    }

    /** @test */
    public function user_can_upload_file_to_trip()
    {
        Storage::fake('public');

        $this->actingAs($this->user, 'api');

        $file = UploadedFile::fake()->create('receipt.pdf', 100);

        $response = $this->postJson("/api/trips/{$this->trip->id}/files", [
            'file' => $file,
            'type' => 0
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'File uploaded successfully']);


        Storage::disk('public')->assertExists("trip_files/{$this->trip->id}/" . $file->hashName());


        $this->assertDatabaseHas('trip_files', [
            'trip_id' => $this->trip->id,
            'uploaded_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_delete_own_file()
    {
        Storage::fake('public');

        $this->actingAs($this->user, 'api');

        $file = UploadedFile::fake()->create('ticket.jpg', 50);
        $uploadedFile = $this->trip->files()->create([
            'uploaded_by' => $this->user->id,
            'path' => $file->store("trip_files/{$this->trip->id}", 'public'),
            'type' => 2
        ]);

        $response = $this->deleteJson("/api/trips/{$this->trip->id}/files/{$uploadedFile->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'File deleted successfully']);

        Storage::disk('public')->assertMissing($uploadedFile->path);

        $this->assertSoftDeleted('trip_files', [
            'id' => $uploadedFile->id
        ]);
    }

    /** @test */
    public function non_trip_members_cannot_upload_or_access_files()
    {
        Storage::fake('public');

        $this->actingAs($this->otherUser, 'api');

        $file = UploadedFile::fake()->create('forbidden.pdf', 20);

        $response = $this->postJson("/api/trips/{$this->trip->id}/files", [
            'file' => $file,
            'type' => 0
        ]);

        $response->assertStatus(403);


        $listResponse = $this->getJson("/api/trips/{$this->trip->id}/files/user-files");
        $listResponse->assertStatus(403)
            ->assertJson(['message' => 'You are not a member of this trip']);
    }

    /** @test */
    public function user_can_only_see_their_own_files()
    {
        Storage::fake('public');

        $this->actingAs($this->user, 'api');


        $userFile = $this->trip->files()->create([
            'uploaded_by' => $this->user->id,
            'path' => 'trip_files/' . $this->trip->id . '/user_file.pdf',
            'type' => 0
        ]);

        $otherFile = $this->trip->files()->create([
            'uploaded_by' => $this->otherUser->id,
            'path' => 'trip_files/' . $this->trip->id . '/other_user_file.pdf',
            'type' => 0
        ]);

        $response = $this->getJson("/api/trips/{$this->trip->id}/files/user-files");

        $response->assertStatus(200)
            ->assertJsonMissing([
                'id' => $otherFile->id
            ])
            ->assertJsonFragment([
                'id' => $userFile->id
            ]);
    }
}
