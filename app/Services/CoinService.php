<?php

namespace App\Services;

use App\Exceptions\InsufficientCoinsException;
use App\Models\CoinLedger;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CoinService
{
    public function credit(User $user, int $amount, string $description): CoinLedger
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $user->increment('coin_balance', $amount);
            $user->refresh();

            return CoinLedger::create([
                'user_id'      => $user->id,
                'type'         => 'credit',
                'amount'       => $amount,
                'description'  => $description,
                'balance_after' => $user->coin_balance,
            ]);
        });
    }

    public function debit(User $user, int $amount, string $description): CoinLedger
    {
        if ($user->coin_balance < $amount) {
            throw new InsufficientCoinsException(
                "Insufficient coins: balance {$user->coin_balance}, required {$amount}."
            );
        }

        return DB::transaction(function () use ($user, $amount, $description) {
            $user->decrement('coin_balance', $amount);
            $user->refresh();

            return CoinLedger::create([
                'user_id'      => $user->id,
                'type'         => 'debit',
                'amount'       => $amount,
                'description'  => $description,
                'balance_after' => $user->coin_balance,
            ]);
        });
    }

    public function getBalance(User $user): int
    {
        return $user->coin_balance;
    }
}
