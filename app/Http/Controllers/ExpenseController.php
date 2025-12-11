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
    public function store(StoreExpenseRequest $request, $tripId)
    {
        $data = $request->validated();
        $data['trip_id'] = $tripId;

        $expenses = $this->expenseService->addExpense($data);

        return response()->json([
            'message' => 'Expenses added successfully',
            'data' => $expenses
        ], 201);
    }

    public function settle($id)
    {
        $expense = $this->expenseService->settleExpense($id);

        return response()->json([
            'message' => 'Expense settled successfully',
            'data' => $expense
        ]);
    }

    public function report($tripId)
    {
        $expenses = $this->expenseService->getTripExpenses($tripId);

        return response()->json([
            'data' => $expenses
        ]);
    }
}
