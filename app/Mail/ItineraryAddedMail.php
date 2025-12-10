<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItineraryAddedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $itinarary;

    /**
     * Create a new message instance.
     */
    public function __construct($itinarary)
    {
        $this->itinarary = $itinarary;
    }

    public function build()
    {
        return $this->markdown('emails.itinerary_added')
            ->subject('A new itinarary was added to your trip!');
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
