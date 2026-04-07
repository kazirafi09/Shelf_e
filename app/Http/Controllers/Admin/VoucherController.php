<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->get();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'               => 'required|string|max:50|uppercase|unique:vouchers,code',
            'description'        => 'nullable|string|max:255',
            'discount_type'      => 'required|in:percentage,fixed',
            'discount_value'     => 'required|numeric|min:0.01|max:100',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'required|integer|min:1',
            'expires_at'         => 'nullable|date|after:now',
            'is_active'          => 'boolean',
            'is_announced'       => 'boolean',
        ]);

        // If type is fixed, discount_value cannot exceed a sane ceiling
        if ($validated['discount_type'] === 'fixed') {
            $request->validate(['discount_value' => 'max:99999']);
        }

        Voucher::create([
            'code'              => strtoupper($validated['code']),
            'description'       => $validated['description'] ?? null,
            'discount_type'     => $validated['discount_type'],
            'discount_value'    => $validated['discount_value'],
            'min_order_amount'  => $validated['min_order_amount'] ?? 0,
            'max_uses'          => $validated['max_uses'] ?? null,
            'max_uses_per_user' => $validated['max_uses_per_user'],
            'expires_at'        => $validated['expires_at'] ?? null,
            'is_active'         => $request->boolean('is_active', true),
            'is_announced'      => $request->boolean('is_announced', false),
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher created successfully.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code'               => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'description'        => 'nullable|string|max:255',
            'discount_type'      => 'required|in:percentage,fixed',
            'discount_value'     => 'required|numeric|min:0.01',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'required|integer|min:1',
            'expires_at'         => 'nullable|date',
            'is_active'          => 'boolean',
            'is_announced'       => 'boolean',
        ]);

        $voucher->update([
            'code'              => strtoupper($validated['code']),
            'description'       => $validated['description'] ?? null,
            'discount_type'     => $validated['discount_type'],
            'discount_value'    => $validated['discount_value'],
            'min_order_amount'  => $validated['min_order_amount'] ?? 0,
            'max_uses'          => $validated['max_uses'] ?? null,
            'max_uses_per_user' => $validated['max_uses_per_user'],
            'expires_at'        => $validated['expires_at'] ?? null,
            'is_active'         => $request->boolean('is_active'),
            'is_announced'      => $request->boolean('is_announced'),
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher '{$voucher->code}' updated.");
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return back()->with('success', "Voucher '{$voucher->code}' deleted.");
    }
}
