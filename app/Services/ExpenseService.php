<?php

namespace App\Services;

use Exception;
use App\Models\Trip;
use App\Models\Expense;
use App\Events\ExpenseCreated;
use App\Contracts\ExpenseRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ExpenseService
{
    protected $expenseRepo;
    protected $balanceService;

    public function __construct(ExpenseRepositoryInterface $expenseRepo, BalanceService $balanceService)
    {
        $this->expenseRepo = $expenseRepo;
        $this->balanceService = $balanceService;
    }

    public function addExpense(Trip $trip, array $data)
    {
        $expenses = [];

        $data['trip_user_ids'] = $trip->users->pluck('id')->toArray();
        if ($data['split_type'] === 'single') {
            $expense = $this->createExpense($trip->id, $data['payer_id'], $data['payee_id'], $data);

            event(new ExpenseCreated($expense));

            $expenses[] = $expense;
        }

        if ($data['split_type'] === 'equal') {
            $perPayeeAmount = $data['amount'] / count($data['payee_ids']);
            foreach ($data['payee_ids'] as $payeeId) {
                $expense = $this->createExpense($trip->id, $data['payer_id'], $payeeId, array_merge($data, ['amount' => $perPayeeAmount]));

                event(new ExpenseCreated($expense));

                $expenses[] = $expense;
            }
        }

        return $expenses;
    }

    private function createExpense(int $tripId, int $payerId, int $payeeId, array $data)
    {
        // 1. Validate payer
        if (!in_array($payerId, $data['trip_user_ids'])) {
            throw ValidationException::withMessages([
                'payer_id' => 'Payer must be a member of this trip.'
            ]);
        }

        // 2. Validate payee
        if (!in_array($payeeId, $data['trip_user_ids'])) {
            throw ValidationException::withMessages([
                'payee_id' => "Payee must be a member of this trip."
            ]);
        }

        $expenseData = [
            'trip_id'    => $tripId,
            'payer_id'   => $payerId,
            'payee_id'   => $payeeId,
            'amount'     => $data['amount'],
            'currency'   => $data['currency'] ?? 'INR',
            'description' => $data['description'] ?? null,
            'is_settled' => 0
        ];

        $expense = $this->expenseRepo->create($expenseData);

        return $expense;
    }

    public function settleExpense(Trip $trip, int $expenseId)
    {
        $expense = $this->expenseRepo->getById($expenseId);

        if (!$expense || $expense->trip_id !== $trip->id) {
            throw new Exception('Expense does not belong to this trip.');
        }
        if ($expense->is_settled) {
            return [
                'status' => 'already_settled',
                'message' => 'This expense has already been settled.',
                'expense' => $expense
            ];
        }

        return $this->expenseRepo->settleExpense($expenseId);
    }

    public function getTripExpenses(int $tripId, string $toCurrency = 'INR')
    {
        $expenses = $this->expenseRepo->getUnsettledByTrip($tripId);

        $balances = $this->balanceService->computeNetBalances($expenses,$toCurrency);

        return $balances->map(fn($row) => [
            'payer_id' => $row['payer_id'],
            'payee_id' => $row['payee_id'],
            'amount'   => $row['amount'],
            'message'  => "User {$row['payee_id']} needs to pay User {$row['payer_id']} {$row['symbol']}{$row['amount']}"
        ]);
    }
}
