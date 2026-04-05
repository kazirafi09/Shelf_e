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
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'announcement_text' => ['required', 'string', 'max:200'],
            'fomo_ends_at'      => ['required', 'date'],
        ]);

        Setting::set('announcement_text', $data['announcement_text']);
        Setting::set('fomo_ends_at', \Carbon\Carbon::parse($data['fomo_ends_at'])->toIso8601String());

        return back()->with('success', 'Store settings saved.');
    }
}
