<?php

namespace App\Mail;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyTripSummaryMail extends Mailable
{
    use Queueable, SerializesModels;
    public Trip $trip;
    public $expenses;
    public $itineraries;
    public $balances;

    /**
     * Create a new message instance.
     */
    public function __construct(Trip $trip, $expenses, $itineraries, $balances)
    {
        $this->trip = $trip;
        $this->expenses = $expenses;
        $this->itineraries = $itineraries;
        $this->balances = $balances;
    }

    public function build()
    {
        return $this->subject('Weekly Trip Summary - ' . $this->trip->name)
                    ->markdown('emails.weekly_summary');
    }
}
