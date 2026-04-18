<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $newsletterVoucher = Voucher::where('code', 'FIRST15')->first();

        return view('admin.settings.index', [
            'announcementText'          => Setting::get('announcement_text', ''),
            'shippingInsideDhaka'       => Setting::get('shipping_inside_dhaka', 60),
            'shippingOutsideDhaka'      => Setting::get('shipping_outside_dhaka', 150),
            'bkashNumber'               => Setting::get('bkash_number', ''),
            'faqContent'                => Setting::get('faq_content', ''),
            'aboutUs'                   => Setting::get('about_us', ''),
            'newsletterDiscountPercent' => $newsletterVoucher ? (int) $newsletterVoucher->discount_value : 15,
            'newsletterDiscountCap'     => $newsletterVoucher ? (int) $newsletterVoucher->max_discount_amount : 100,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'announcement_text'           => ['required', 'string', 'max:200'],
            'shipping_inside_dhaka'       => ['required', 'integer', 'min:0'],
            'shipping_outside_dhaka'      => ['required', 'integer', 'min:0'],
            'bkash_number'                => ['nullable', 'string', 'max:20'],
            'faq_content'                 => ['nullable', 'string', 'max:10000'],
            'about_us'                    => ['nullable', 'string', 'max:2000'],
            'newsletter_discount_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'newsletter_discount_cap'     => ['required', 'integer', 'min:0'],
        ]);

        Setting::set('announcement_text', $data['announcement_text']);
        Setting::set('shipping_inside_dhaka', (int) $data['shipping_inside_dhaka']);
        Setting::set('shipping_outside_dhaka', (int) $data['shipping_outside_dhaka']);
        Setting::set('bkash_number', $data['bkash_number'] ?? '');
        Setting::set('faq_content', $data['faq_content'] ?? '');
        Setting::set('about_us', $data['about_us'] ?? '');

        $cap = (int) $data['newsletter_discount_cap'];
        $percent = (int) $data['newsletter_discount_percent'];
        $capLabel = $cap > 0 ? " (up to ৳{$cap})" : '';
        Voucher::where('code', 'FIRST15')->update([
            'discount_value'      => $percent,
            'max_discount_amount' => $cap > 0 ? $cap : null,
            'description'         => "Newsletter subscriber welcome discount — {$percent}% off{$capLabel} your first order.",
        ]);

        return back()->with('success', 'Store settings saved.');
    }
}
