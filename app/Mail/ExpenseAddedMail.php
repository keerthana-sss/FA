<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseAddedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $expense;

    /**
     * Create a new message instance.
     */
    public function __construct($expense)
    {
        $this->expense = $expense;
    }

    public function build()
    {
        return $this->markdown('emails.expense_added')
            ->subject('A new expense was added to your trip!');
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