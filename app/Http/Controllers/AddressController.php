<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = UserAddress::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get();

        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'division'    => ['required', 'string', 'max:50'],
            'district'    => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone'       => ['required', 'regex:/^[0-9]{10}$/'],
        ]);

        $data['user_id'] = auth()->id();

        DB::transaction(function () use ($data, $request) {
            // If first address or explicitly set as default, clear others first
            $isFirst = UserAddress::where('user_id', auth()->id())->doesntExist();

            if ($isFirst || $request->boolean('is_default')) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
                $data['is_default'] = true;
            } else {
                $data['is_default'] = false;
            }

            UserAddress::create($data);
        });

        return redirect()->route('addresses.index')->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, UserAddress $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'label'       => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'division'    => ['required', 'string', 'max:50'],
            'district'    => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone'       => ['required', 'regex:/^[0-9]{10}$/'],
        ]);

        if ($request->boolean('is_default')) {
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        return redirect()->route('addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(UserAddress $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        $wasDefault = $address->is_default;
        $address->delete();

        // Promote the oldest remaining address to default
        if ($wasDefault) {
            UserAddress::where('user_id', auth()->id())
                ->oldest()
                ->first()
                ?->update(['is_default' => true]);
        }

        return redirect()->route('addresses.index')->with('success', 'Address removed.');
    }

    public function setDefault(UserAddress $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')->with('success', 'Default address updated.');
    }
}
