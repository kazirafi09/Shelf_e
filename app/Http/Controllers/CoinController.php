<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $ledger = $user->coinLedger()->latest()->paginate(20);

        return view('wallet.index', [
            'ledger'       => $ledger,
            'coin_balance' => $user->coin_balance,
        ]);
    }
}
