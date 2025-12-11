<?php

namespace App\Services;

class BalanceService
{
    public function computeNetBalances($expenses)
    {
        $groups = $expenses->reduce(function ($carry, $exp) {
            $u1 = min($exp->payer_id, $exp->payee_id);
            $u2 = max($exp->payer_id, $exp->payee_id);
            $key = "{$u1}_{$u2}";

            if (!isset($carry[$key])) {
                $carry[$key] = [
                    'user1' => $u1,
                    'user2' => $u2,
                    'amount_user1_to_user2' => 0,
                    'amount_user2_to_user1' => 0,
                ];
            }

            if ($exp->payer_id === $u1) {
                $carry[$key]['amount_user1_to_user2'] += $exp->amount;
            } else {
                $carry[$key]['amount_user2_to_user1'] += $exp->amount;
            }

            return $carry;
        }, []);

        return collect($groups)->map(function ($row) {
            $net = $row['amount_user1_to_user2'] - $row['amount_user2_to_user1'];
            if ($net === 0) return null;

            return [
                'payer_id' => $net > 0 ? $row['user1'] : $row['user2'],
                'payee_id' => $net > 0 ? $row['user2'] : $row['user1'],
                'amount'   => abs($net),
            ];
        })->filter()->values();
    }
}
