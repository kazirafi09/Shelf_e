<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminHeroImageController extends Controller
{
    private const SLOTS  = [1, 2, 3, 4, 5];
    private const WIDTHS = [480, 960, 1440];

    public function index(): View
    {
        $slots = [];

        foreach (self::SLOTS as $slot) {
            $hasCustom  = Storage::disk('public')->exists("hero/{$slot}_480.webp");
            $previewUrl = $hasCustom
                ? Storage::disk('public')->url("hero/{$slot}_960.webp")
                : asset("images/hero/{$slot}.png");

            $slots[$slot] = [
                'slot'       => $slot,
                'hasCustom'  => $hasCustom,
                'previewUrl' => $previewUrl,
            ];
        }

        return view('admin.hero-images.index', compact('slots'));
    }

    public function store(Request $request, int $slot): RedirectResponse
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $file    = $request->file('image');
        $manager = new ImageManager(new Driver());

        foreach (self::WIDTHS as $width) {
            $webp = $manager->read($file->getRealPath())->scaleDown(width: $width)->toWebp(quality: 85);
            Storage::disk('public')->put("hero/{$slot}_{$width}.webp", $webp->toString());
        }

        $png = $manager->read($file->getRealPath())->scaleDown(width: 960)->toPng();
        Storage::disk('public')->put("hero/{$slot}_fallback.png", $png->toString());

        return back()->with('success', "Hero image {$slot} updated.");
    }

    public function destroy(int $slot): RedirectResponse
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        foreach (self::WIDTHS as $width) {
            Storage::disk('public')->delete("hero/{$slot}_{$width}.webp");
        }
        Storage::disk('public')->delete("hero/{$slot}_fallback.png");

        return back()->with('success', "Hero image {$slot} reverted to default.");
    }
}
