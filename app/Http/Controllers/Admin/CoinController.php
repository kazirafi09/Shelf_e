<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientCoinsException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CoinService;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function __construct(private readonly CoinService $coinService) {}

    public function index()
    {
        $users = User::orderBy('name')->paginate(20);

        return view('admin.coins.index', compact('users'));
    }

    public function adjust(Request $request, User $user)
    {
        $validated = $request->validate([
            'type'        => 'required|in:credit,debit',
            'amount'      => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($validated['type'] === 'credit') {
                $this->coinService->credit($user, $validated['amount'], $validated['description']);
            } else {
                $this->coinService->debit($user, $validated['amount'], $validated['description']);
            }
        } catch (InsufficientCoinsException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return back()->with('success', "Coin balance for {$user->name} updated successfully.");
    }
}
