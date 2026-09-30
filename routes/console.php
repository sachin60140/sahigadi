<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:optimize', function () {
    $this->info('Starting image optimization...');
    
    $directories = ['customer-listings', 'dealer-listings', 'cars', 'customer-cars'];
    $manager = new ImageManager(new Driver());
    
    $count = 0;
    foreach ($directories as $dir) {
        if (Storage::disk('public')->exists($dir)) {
            $files = Storage::disk('public')->files($dir);
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
                    $fullPath = Storage::disk('public')->path($file);
                    $size = filesize($fullPath);
                    
                    if ($size > 250000) {
                        try {
                            $image = $manager->read($fullPath);
                            $width = $image->width();
                            
                            if ($width > 800) {
                                $image->scaleDown(width: 800);
                                $image->save($fullPath, quality: 75);
                                $this->line("Optimized: {$file}");
                                $count++;
                            }
                        } catch (\Exception $e) {
                            $this->error("Failed to optimize {$file}: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }
    
    $this->info("Successfully optimized {$count} images!");
})->purpose('Optimize and resize all car images to save bandwidth');

// Account deletion is two-stage: DELETE /api/account hides the account and
// revokes its tokens immediately, and this pass scrambles the personal fields
// once the grace period has run out. It needs the server cron to be calling
// `php artisan schedule:run` every minute - without that, deleted accounts stay
// hidden but are never anonymised.
Schedule::command('customers:anonymise-deleted')
    ->dailyAt('03:20')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->onOneServer();
