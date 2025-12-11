<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Enums\TripFileType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showTripDashboard(Request $request, $tripId)
    {
        $trip = Trip::with(['users', 'expenses', 'files'])->findOrFail($tripId);

        $totalMembers = $trip->users->count();

        $totalExpense = $trip->expenses->sum('amount');

        $payerData = $trip->expenses
            ->groupBy('payer_id')
            ->map(fn($expenses, $payerId) => [
                'name' => $trip->users->where('id', $payerId)->first()?->name ?? 'Unknown',
                'amount' => $expenses->sum('amount')
            ])->values();

        // File uploads count per type
        $fileData = [];
        // Loop over enum values (integers)
        foreach (TripFileType::getValues() as $value) {
            $desc = TripFileType::description($value); // e.g. 'Receipt'
            // Count files with matching type
            $fileData[$desc] = $trip->files->filter(fn($file) => $file->type->value === $value)->count();
        }

        return view('dashboard.trip', [
            'trip' => $trip,
            'totalMembers' => $totalMembers,
            'totalExpense' => $totalExpense,
            'payerData' => $payerData,
            'fileData' => $fileData,
        ]);
    }
}
