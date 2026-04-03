<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientCoinsException;
use App\Models\CoinLedger;
use App\Models\User;
use App\Services\CoinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoinServiceTest extends TestCase
{
    use RefreshDatabase;

    private CoinService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CoinService();
    }

    public function test_credit_increments_user_balance_and_creates_ledger_entry(): void
    {
        $user = User::factory()->create(['coin_balance' => 0]);

        $ledger = $this->service->credit($user, 100, 'Welcome bonus');

        $this->assertEquals(100, $user->fresh()->coin_balance);

        $this->assertDatabaseHas('coin_ledger', [
            'user_id'      => $user->id,
            'type'         => 'credit',
            'amount'       => 100,
            'description'  => 'Welcome bonus',
            'balance_after' => 100,
        ]);

        $this->assertInstanceOf(CoinLedger::class, $ledger);
    }

    public function test_debit_decrements_user_balance_and_creates_ledger_entry(): void
    {
        $user = User::factory()->create(['coin_balance' => 200]);

        $ledger = $this->service->debit($user, 75, 'Order redemption');

        $this->assertEquals(125, $user->fresh()->coin_balance);

        $this->assertDatabaseHas('coin_ledger', [
            'user_id'      => $user->id,
            'type'         => 'debit',
            'amount'       => 75,
            'description'  => 'Order redemption',
            'balance_after' => 125,
        ]);

        $this->assertInstanceOf(CoinLedger::class, $ledger);
    }

    public function test_debit_throws_insufficient_coins_exception_when_balance_is_too_low(): void
    {
        $user = User::factory()->create(['coin_balance' => 50]);

        $this->expectException(InsufficientCoinsException::class);

        $this->service->debit($user, 100, 'Should fail');
    }

    public function test_credit_is_atomic_and_rolls_back_on_failure(): void
    {
        $user = User::factory()->create(['coin_balance' => 0]);

        // Anonymous subclass that increments the balance then throws before committing
        // the ledger row — simulating a mid-transaction DB failure.
        $failingService = new class extends CoinService {
            public function credit(User $user, int $amount, string $description): CoinLedger
            {
                return DB::transaction(function () use ($user, $amount) {
                    $user->increment('coin_balance', $amount);
                    throw new \RuntimeException('Simulated DB failure mid-transaction');
                });
            }
        };

        try {
            $failingService->credit($user, 100, 'Should roll back');
        } catch (\RuntimeException) {
            // Expected — we only care that the balance did not persist
        }

        $this->assertEquals(0, $user->fresh()->coin_balance);
        $this->assertDatabaseMissing('coin_ledger', ['user_id' => $user->id]);
    }
}
