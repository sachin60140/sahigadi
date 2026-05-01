<?php
$dirs = ['resources/views', 'app', 'config', 'database', 'routes'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            $newContent = preg_replace('/SAHI<span>GADI<\/span>/', 'SAHI <span>GADI</span>', $content);
            $newContent = preg_replace('/SAHI<span([^>]*)>GADI<\/span>/', 'SAHI <span$1>GADI</span>', $newContent);
            
            if ($content !== $newContent) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Updated: " . $file->getPathname() . "\n";
            }
        }
    }
}
echo "Done.\n";
