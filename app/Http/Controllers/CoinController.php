<?php

namespace App\Http\Controllers;

use App\Models\CoinShippingReward;
use App\Services\CoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoinController extends Controller
{
    public function __construct(private readonly CoinService $coinService) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $ledger = $user->coinLedger()->latest()->paginate(20);

        $activeRewards = CoinShippingReward::where('user_id', $user->id)
            ->where('used', false)
            ->orderByDesc('shipping_discount')
            ->get();

        return view('wallet.index', [
            'ledger'        => $ledger,
            'coin_balance'  => $user->coin_balance,
            'tiers'         => CoinShippingReward::TIERS,
            'activeRewards' => $activeRewards,
        ]);
    }

    public function redeem(Request $request)
    {
        $coins = (int) $request->input('coins');
        $tier  = collect(CoinShippingReward::TIERS)->firstWhere('coins', $coins);

        if (! $tier) {
            return back()->with('error', 'Invalid reward tier.');
        }

        $user = $request->user();

        if ($user->coin_balance < $coins) {
            return back()->with('error', 'You do not have enough coins for this reward.');
        }

        DB::transaction(function () use ($user, $tier) {
            $this->coinService->debit(
                $user,
                $tier['coins'],
                "Redeemed ৳{$tier['discount']} shipping discount reward"
            );

            CoinShippingReward::create([
                'user_id'           => $user->id,
                'coins_spent'       => $tier['coins'],
                'shipping_discount' => $tier['discount'],
            ]);
        });

        return back()->with('success', "Success! You've unlocked a ৳{$tier['discount']} shipping discount. Use it at checkout.");
    }
}
