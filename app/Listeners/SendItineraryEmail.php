<?php

namespace App\Listeners;

use App\Events\ItineraryCreated;
use App\Mail\ItineraryAddedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendItineraryEmail
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
    public function handle(ItineraryCreated $event): void
    {
        // $itinerary = $event->itinerary;

        // // Make sure trip and user are loaded
        // $itinerary->load('trip', 'creator', 'updater');

        // $trip = $itinerary->trip;
        // $user = $itinerary->updater ?? $itinerary->creator;

        // foreach ($trip->users as $member) {
        //     Mail::send('emails.itinerary_added', [
        //         'trip' => $trip,
        //         'title' => $itinerary->title,
        //         'description' => $itinerary->description,
        //         'day_number' => $itinerary->day_number,
        //         'start_time' => $itinerary->start_time?->format('H:i'),
        //         'end_time' => $itinerary->end_time?->format('H:i'),
        //         'user' => $user,
        //     ], function ($message) use ($member) {
        //         $message->to($member->email)
        //             ->subject('New Itinerary Added');
        //     });
        // }
        $itinerary = $event->itinerary;

        // Make sure trip and user relationships are loaded
        $itinerary->load('trip', 'creator', 'updater');

        $trip = $itinerary->trip;
        $user = $itinerary->updater ?? $itinerary->creator;

        // Collect all trip member emails
        $emails = $trip->users->pluck('email')->toArray();

        // Send ONE email to all members
        Mail::send('emails.itinerary_added', [
            'trip'        => $trip,
            'title'       => $itinerary->title,
            'description' => $itinerary->description,
            'day_number'  => $itinerary->day_number,
            'start_time'  => $itinerary->start_time?->format('H:i'),
            'end_time'    => $itinerary->end_time?->format('H:i'),
            'user'        => $user,
        ], function ($message) use ($emails) {
            $message->to($emails)
                ->subject('New Itinerary Added');
        });
    }
}
