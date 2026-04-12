<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeHeroImages extends Command
{
    protected $signature   = 'images:optimize-hero';
    protected $description = 'Convert existing static hero PNGs to responsive WebP variants in storage.';

    private const WIDTHS = [480, 960, 1440];

    public function handle(): int
    {
        $manager = new ImageManager(new Driver());

        for ($slot = 1; $slot <= 5; $slot++) {
            $source = public_path("images/hero/{$slot}.png");

            if (! file_exists($source)) {
                $this->warn("Slot {$slot}: source not found, skipping.");
                continue;
            }

            $this->info("Slot {$slot}: processing…");

            foreach (self::WIDTHS as $width) {
                $image = $manager->read($source);
                $image->scaleDown(width: $width);
                $encoded = $image->toWebp(quality: 82);
                Storage::disk('public')->put("hero/{$slot}_{$width}.webp", (string) $encoded);
                $this->line("  → hero/{$slot}_{$width}.webp");
            }

            // Also copy original at max width for the <img> fallback
            $image = $manager->read($source);
            $image->scaleDown(width: 1440);
            Storage::disk('public')->put("hero/{$slot}_fallback.png", (string) $image->toPng());
            $this->line("  → hero/{$slot}_fallback.png");
        }

        $this->info('Done. Run php artisan storage:link if the symlink does not exist.');

        return self::SUCCESS;
    }
}
