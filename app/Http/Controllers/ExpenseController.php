<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\ExpenseService;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{
    protected ExpenseService $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }
    public function store(StoreExpenseRequest $request, Trip $trip)
    {
        $data = $request->validated();

        $expenses = $this->expenseService->addExpense($trip, $data);

        return response()->json([
            'message' => 'Expenses added successfully',
            'data' => $expenses
        ], 201);
    }

    public function settle(Trip $trip, int $expenseId)
    {
        $expense = $this->expenseService->settleExpense($trip, $expenseId);

        return response()->json([
            'message' => 'Expense settled successfully',
            'data' => $expense
        ]);
    }

    public function report(Request $request, Trip $trip)
    {
        $currency = strtoupper($request->query('currency', 'INR'));
        $expenses = $this->expenseService->getTripExpenses($trip->id, $currency);

        return response()->json([
            'data' => $expenses
        ]);
    }
}
