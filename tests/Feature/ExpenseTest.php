<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;
    protected $user2;
    protected $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $this->trip = Trip::factory()->create();
        $this->trip->users()->attach([$this->user1->id, $this->user2->id]);
    }

    /** @test */
    public function valid_trip_members_can_store_expenses()
    {
        $this->actingAs($this->user1, 'api'); // 👈 OLD STYLE

        $payload = [
            'payer_id'   => $this->user1->id,
            'payee_id'   => $this->user2->id,
            'amount'     => 300,
            'currency'   => 'INR',
            'split_type' => 'single'
        ];

        $response = $this->postJson("/api/trips/{$this->trip->id}/expenses", $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expenses', [
            'trip_id'  => $this->trip->id,
            'payer_id' => $this->user1->id,
            'payee_id' => $this->user2->id,
            'amount'   => 300
        ]);
    }

    /** @test */
    public function unsettled_expenses_can_be_settled()
    {
        $this->actingAs($this->user1, 'api');

        $expense = Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'payer_id' => $this->user1->id,
            'payee_id' => $this->user2->id,
            'amount' => 500,
            'is_settled' => 0
        ]);

        $response = $this->patchJson("/api/trips/{$this->trip->id}/expenses/settle/{$expense->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'is_settled' => 1
        ]);
    }

    /** @test */
    public function report_shows_correct_balances()
    {
        $this->actingAs($this->user1, 'api');

        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'payer_id' => $this->user1->id,
            'payee_id' => $this->user2->id,
            'amount' => 200,
            'is_settled' => 0,
        ]);

        $response = $this->getJson("/api/trips/{$this->trip->id}/expenses/report");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'payer_id' => $this->user1->id,
                     'payee_id' => $this->user2->id,
                     'amount' => 200,
                     'message' => "User {$this->user2->id} needs to pay User {$this->user1->id} ₹200"
                 ]);
    }
}
