<?php
$dirs = ['resources/views', 'app', 'config', 'database', 'routes'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Replace SAHIGADI with SAHI GADI, but ignore if followed by .com or .in or if preceded by @
            // Also ignore if part of a variable or property like $sahigadi
            $newContent = preg_replace('/(?<!@)SAHIGADI(?!\.com|\.in|_|\-)/', 'SAHI GADI', $content);
            $newContent = preg_replace('/(?<!@)Sahigadi(?!\.com|\.in|_|\-)/', 'Sahi Gadi', $newContent);
            
            if ($content !== $newContent) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Updated: " . $file->getPathname() . "\n";
            }
        }
    }
}
echo "Done.\n";
