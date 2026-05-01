<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Optimize and resize all car images to save bandwidth';

    public function handle()
    {
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
                        
                        // If file is larger than 250KB, it's worth optimizing
                        if ($size > 250000) {
                            try {
                                $image = $manager->read($fullPath);
                                $width = $image->width();
                                
                                // Only resize if it's wider than 800px
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
    }
}
