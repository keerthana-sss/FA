<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Expense;
use App\Models\Itinerary;
use Illuminate\Console\Command;
use App\Services\BalanceService;
use App\Mail\WeeklyTripSummaryMail;
use Illuminate\Support\Facades\Mail;

class SendWeeklyTripSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip:weekly-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly summary email for active trips';

    private BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        parent::__construct();
        $this->balanceService = $balanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Get active trips
        $trips = Trip::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();
        info("Found " . $trips->count() . " active trips for weekly summary.");

        foreach ($trips as $trip) {

            $weekStart = now()->startOfWeek();
            $weekEnd   = now()->endOfWeek();

            // Fetch this week's expenses
            $expenses = Expense::where('trip_id', $trip->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->with(['payer'])
                ->get();

            // Fetch this week's itinerary updates
            $itineraries = Itinerary::where('trip_id', $trip->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get();

            $balances = $this->balanceService->computeNetBalances($expenses);

            $emails = $trip->users->pluck('email')->toArray();

            if (!empty($emails)) {
                Mail::to($emails)->send(
                    new WeeklyTripSummaryMail($trip, $expenses, $itineraries, $balances->values()->all())
                );
            }
        }

        return Command::SUCCESS;
    }
}
