<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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
