<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Expense;
use App\Events\ExpenseCreated;
use App\Contracts\ExpenseRepositoryInterface;

class ExpenseService
{
    protected $expenseRepo;

    public function __construct(ExpenseRepositoryInterface $expenseRepo)
    {
        $this->expenseRepo = $expenseRepo;
    }

    public function addExpense(array $data)
    {
        $expenses = [];

        if ($data['split_type'] === 'single') {
            $expense = $this->expenseRepo->create([
                'trip_id' => $data['trip_id'],
                'payer_id' => $data['payer_id'],
                'payee_id' => $data['payee_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'INR',
                'description' => $data['description'] ?? null,
                'is_settled' => 0
            ]);

            event(new ExpenseCreated($expense));

            $expenses[] = $expense;
        }

        if ($data['split_type'] === 'equal') {
            $perPayeeAmount = $data['amount'] / count($data['payee_ids']);
            foreach ($data['payee_ids'] as $payeeId) {
                $expense = $this->expenseRepo->create([
                    'trip_id' => $data['trip_id'],
                    'payer_id' => $data['payer_id'],
                    'payee_id' => $payeeId,
                    'amount' => $perPayeeAmount,
                    'currency' => $data['currency'] ?? 'INR',
                    'description' => $data['description'] ?? null,
                    'is_settled' => 0
                ]);

                event(new ExpenseCreated($expense));

                $expenses[] = $expense;
            }
        }

        return $expenses;
    }

    public function settleExpense(int $id)
    {
        return $this->expenseRepo->settleExpense($id);
    }

    public function getTripExpenses(int $tripId)
    {
        $expenses = $this->expenseRepo->getUnsettledByTrip($tripId);

        $summary = [];

        foreach ($expenses as $exp) {
            $key = $exp->payer_id . '_' . $exp->payee_id;

            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'payer_id' => $exp->payer_id,
                    'payee_id' => $exp->payee_id,
                    'amount'   => 0
                ];
            }

            $summary[$key]['amount'] += $exp->amount;
        }
        return array_values(array_map(function ($row) {
            return [
                'payer_id' => $row['payer_id'],
                'payee_id' => $row['payee_id'],
                'amount'   => $row['amount'],
                'message'  => "User {$row['payee_id']} needs to pay User {$row['payer_id']} ₹{$row['amount']}"
            ];
        }, $summary));
    }
}
