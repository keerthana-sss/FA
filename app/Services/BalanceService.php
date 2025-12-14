<?php

namespace App\Services;

class BalanceService
{
    private function symbol(string $currency)
    {
        return match (strtoupper($currency)) {
            'USD' => '$',
            'EUR' => '€',
            'INR' => '₹',
            'GBP' => '£',
            default => strtoupper($currency) . ' ',
        };
    }

    public function computeNetBalances($expenses, string $toCurrency = "INR")
    {
        $baseCurrency = 'INR';
        $toCurrency   = strtoupper($toCurrency);
        $symbol = $this->symbol($toCurrency);
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

        $balances = collect($groups)->map(function ($row) use ($symbol) {
            $net = $row['amount_user1_to_user2'] - $row['amount_user2_to_user1'];
            if ($net === 0) return null;

            return [
                'payer_id' => $net > 0 ? $row['user1'] : $row['user2'],
                'payee_id' => $net > 0 ? $row['user2'] : $row['user1'],
                'amount'   => abs($net),
                'symbol'   => $symbol,
            ];
        })->filter()->values();

        if (strtoupper($toCurrency) !== 'INR') {

            $converter = app(CurrencyConversionService::class);

            return $balances->map(function ($row) use ($baseCurrency, $converter, $toCurrency, $symbol) {

                $convertedAmount = $converter->convert(
                    $row['amount'],
                    $baseCurrency,
                    $toCurrency
                );
                if ($convertedAmount === null) {
                    return [
                        'payer_id' => $row['payer_id'],
                        'payee_id' => $row['payee_id'],
                        'amount'   => round($row['amount'], 2),
                        'currency' => $baseCurrency,
                        'symbol'   => $this->symbol($baseCurrency),
                        'conversion_failed' => true,
                    ];
                }

                return [
                    'payer_id' => $row['payer_id'],
                    'payee_id' => $row['payee_id'],
                    'amount'   => $convertedAmount,
                    'symbol'   => $symbol,
                ];
            });
        }

        return $balances;
    }
}
