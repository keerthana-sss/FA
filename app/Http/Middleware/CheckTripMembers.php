<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Trip;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        // Check payer and payee in request
        $payer = $request->payer_id ?? null;
        $payees = $request->payee_ids ?? [$request->payee_id];

        $memberIds = $trip->users->pluck('id')->toArray();

        if ($payer && !in_array($payer, $memberIds)) {
            abort(403, "Payer is not part of this trip");
        }

        foreach ($payees as $payee) {
            if ($payee && !in_array($payee, $memberIds)) {
                abort(403, "Payee {$payee} is not part of this trip");
            }
        }


        return $next($request);
    }
}
