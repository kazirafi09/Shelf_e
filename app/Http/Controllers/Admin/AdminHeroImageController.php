<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminHeroImageController extends Controller
{
    private const SLOTS = [1, 2, 3, 4, 5];

    public function index(): View
    {
        $slots = [];

        foreach (self::SLOTS as $slot) {
            $hasWebp    = Storage::disk('public')->exists("hero/{$slot}_480.webp");
            $previewUrl = $hasWebp
                ? Storage::disk('public')->url("hero/{$slot}_960.webp")
                : asset("images/hero/{$slot}.png");

            $slots[$slot] = [
                'slot'       => $slot,
                'hasCustom'  => $hasWebp,
                'previewUrl' => $previewUrl,
            ];
        }

        return view('admin.hero-images.index', compact('slots'));
    }
}
