<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminHeroImageController extends Controller
{
    private const SLOTS      = [1, 2, 3, 4, 5];
    private const WIDTHS     = [480, 960, 1440];
    private const MAX_BYTES  = 10 * 1024 * 1024; // 10 MB

    public function index(): View
    {
        $slots = [];

        foreach (self::SLOTS as $slot) {
            $hasWebp   = Storage::disk('public')->exists("hero/{$slot}_480.webp");
            $previewUrl = $hasWebp
                ? Storage::disk('public')->url("hero/{$slot}_960.webp")
                : (file_exists(public_path("images/hero/{$slot}.png"))
                    ? asset("images/hero/{$slot}.png")
                    : null);

            $slots[$slot] = [
                'slot'       => $slot,
                'hasCustom'  => $hasWebp,
                'previewUrl' => $previewUrl,
            ];
        }

        return view('admin.hero-images.index', compact('slots'));
    }

    public function update(Request $request, int $slot): RedirectResponse
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $manager = new ImageManager(new Driver());
        $image   = $manager->read($request->file('image')->getRealPath());

        foreach (self::WIDTHS as $width) {
            $copy = clone $image;
            $copy->scaleDown(width: $width);
            Storage::disk('public')->put(
                "hero/{$slot}_{$width}.webp",
                (string) $copy->toWebp(quality: 82)
            );
        }

        // Save a PNG fallback at max width
        $fallback = clone $image;
        $fallback->scaleDown(width: 1440);
        Storage::disk('public')->put(
            "hero/{$slot}_fallback.png",
            (string) $fallback->toPng()
        );

        return redirect()->route('admin.hero-images.index')
            ->with('success', "Hero image {$slot} updated successfully.");
    }

    public function destroy(int $slot): RedirectResponse
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        foreach (self::WIDTHS as $width) {
            Storage::disk('public')->delete("hero/{$slot}_{$width}.webp");
        }
        Storage::disk('public')->delete("hero/{$slot}_fallback.png");

        return redirect()->route('admin.hero-images.index')
            ->with('success', "Hero image {$slot} reset to default.");
    }
}
