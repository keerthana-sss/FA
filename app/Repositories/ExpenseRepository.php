<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Contracts\ExpenseRepositoryInterface;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(array $data)
    {
        return Expense::create($data);
    }

    public function getByTrip(int $tripId)
    {
        // return Expense::where('trip_id', $tripId)
        //     ->where('is_settled', 0)
        //     ->get();
    }

    public function settleExpense(int $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->is_settled = 1;
        $expense->save();

        return $expense;
    }

    public function getUnsettledByTrip($tripId)
    {
        return Expense::where('trip_id', $tripId)
            ->where('is_settled', 0)
            ->get();
    }
}
