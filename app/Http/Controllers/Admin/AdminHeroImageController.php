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
            $key        = "hero/{$slot}_480.webp";
            $hasCustom  = Storage::disk('public')->exists($key);
            $previewUrl = $hasCustom
                ? asset("storage/hero/{$slot}_960.webp") . '?v=' . Storage::disk('public')->lastModified($key)
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
        $disk    = Storage::disk('public');

        try {
            $manager = new ImageManager(new Driver());

            foreach (self::WIDTHS as $width) {
                $webp = $manager->read($file->getRealPath())
                    ->scaleDown(width: $width)
                    ->toWebp(quality: 85);

                $path = "hero/{$slot}_{$width}.webp";
                if (! $disk->put($path, $webp->toString())) {
                    return back()->with('error', "Failed to save {$path}. Check storage permissions.");
                }
            }

            $png = $manager->read($file->getRealPath())
                ->scaleDown(width: 960)
                ->toPng();

            $path = "hero/{$slot}_fallback.png";
            if (! $disk->put($path, $png->toString())) {
                return back()->with('error', "Failed to save {$path}. Check storage permissions.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Image processing failed: ' . $e->getMessage());
        }

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
