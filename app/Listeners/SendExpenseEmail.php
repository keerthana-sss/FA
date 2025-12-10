<?php

namespace App\Listeners;

use App\Events\ExpenseCreated;
use App\Mail\ExpenseAddedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExpenseEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseCreated $event): void
    {
        $expense = $event->expense;

        $expense->load('trip', 'payer', 'payee');

        $trip = $expense->trip;
        $payer = $expense->payer;
        $payee = $expense->payee;

        // Only payee receives the notification
        Mail::send('emails.expense_added', [
            'trip' => $trip,
            'payer' => $payer,
            'payee' => $payee,
            'amount' => $expense->amount,
            'description' => $expense->description,
        ], function ($message) use ($payee) {
            $message->to($payee->email)
                ->subject('New Expense Added');
        });
    }
}
