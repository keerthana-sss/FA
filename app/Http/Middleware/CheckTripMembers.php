<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Trip;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Response\ApiResponse;

class CheckTripMembers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $trip = $request->route('trip');

        // Ensure $trip is a Trip model
        if (!$trip instanceof Trip) {
            $trip = Trip::findOrFail($trip);
        }

        $user = $request->user();

        // Check if user is owner or trip member
        $isMember = $trip->users()->where('user_id', $user->id)->exists();
        $isOwner  = $trip->owner_id == $user->id;

        if (! $isOwner && ! $isMember) {
            return ApiResponse::setMessage("You are not a member of this trip")
                ->response(Response::HTTP_FORBIDDEN);
        }



        // // Check payer and payee in request
        // $payer = $request->payer_id ?? null;
        // $payees = $request->payee_ids ?? [$request->payee_id];

        // $memberIds = $trip->users->pluck('id')->toArray();

        // if ($payer && !in_array($payer, $memberIds)) {
        //     abort(403, "Payer is not part of this trip");
        // }

        // foreach ($payees as $payee) {
        //     if ($payee && !in_array($payee, $memberIds)) {
        //         abort(403, "Payee {$payee} is not part of this trip");
        //     }
        // }


        return $next($request);
    }
}
