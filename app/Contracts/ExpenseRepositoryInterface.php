<?php

namespace App\Contracts;

interface ExpenseRepositoryInterface
{
    public function create(array $data);
    public function getById(int $tripId);
    public function settleExpense(int $id);
    public function getUnsettledByTrip($tripId);
}
