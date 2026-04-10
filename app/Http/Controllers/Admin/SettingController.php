<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'announcementText' => Setting::get('announcement_text', ''),
            'fomoEndsAt'       => Setting::get('fomo_ends_at', now()->addDay()->toIso8601String()),
            'shippingInsideDhaka'  => Setting::get('shipping_inside_dhaka', 60),
            'shippingOutsideDhaka' => Setting::get('shipping_outside_dhaka', 150),
            'bkashNumber'      => Setting::get('bkash_number', ''),
            'returnsPolicy'    => Setting::get('returns_policy', ''),
            'faqContent'       => Setting::get('faq_content', ''),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'announcement_text' => ['required', 'string', 'max:200'],
            'fomo_ends_at'      => ['required', 'date'],
            'shipping_inside_dhaka'  => ['required', 'integer', 'min:0'],
            'shipping_outside_dhaka' => ['required', 'integer', 'min:0'],
            'bkash_number'      => ['nullable', 'string', 'max:20'],
            'returns_policy'    => ['nullable', 'string', 'max:5000'],
            'faq_content'       => ['nullable', 'string', 'max:10000'],
        ]);

        Setting::set('announcement_text', $data['announcement_text']);
        Setting::set('fomo_ends_at', \Carbon\Carbon::parse($data['fomo_ends_at'])->toIso8601String());
        Setting::set('shipping_inside_dhaka', (int) $data['shipping_inside_dhaka']);
        Setting::set('shipping_outside_dhaka', (int) $data['shipping_outside_dhaka']);
        Setting::set('bkash_number', $data['bkash_number'] ?? '');
        Setting::set('returns_policy', $data['returns_policy'] ?? '');
        Setting::set('faq_content', $data['faq_content'] ?? '');

        return back()->with('success', 'Store settings saved.');
    }
}
