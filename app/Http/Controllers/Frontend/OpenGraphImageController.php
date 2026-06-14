<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OpenGraphImageController extends Controller
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __invoke(Request $request): BinaryFileResponse|RedirectResponse|Response
    {
        $storagePath = $this->resolveStorageImage($request->string('path')->toString());

        if (! $storagePath) {
            return redirect(asset('images/og-image.png'));
        }

        $cacheDirectory = storage_path('app/public/og-cache');
        if (! is_dir($cacheDirectory) && ! mkdir($cacheDirectory, 0755, true) && ! is_dir($cacheDirectory)) {
            return response()->file($storagePath);
        }

        $cachePath = $cacheDirectory.DIRECTORY_SEPARATOR
            .hash('sha256', $storagePath.'|'.filemtime($storagePath)).'.jpg';

        if (! is_file($cachePath)) {
            try {
                $image = (new ImageManager(new Driver()))->read($storagePath);
                $image->cover(1200, 630);
                $image->toJpeg(80)->save($cachePath);
            } catch (\Throwable) {
                return response()->file($storagePath, [
                    'Cache-Control' => 'public, max-age=86400',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        return response()->file($cachePath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function resolveStorageImage(string $input): ?string
    {
        if ($input === '' || str_contains($input, "\0")) {
            return null;
        }

        if (filter_var($input, FILTER_VALIDATE_URL)) {
            $input = (string) parse_url($input, PHP_URL_PATH);
        }

        $relativePath = ltrim(rawurldecode($input), '/\\');
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, 8);
        }

        $relativePath = str_replace('\\', '/', $relativePath);
        if ($relativePath === '' || str_contains($relativePath, '../') || str_starts_with($relativePath, '..')) {
            return null;
        }

        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }

        $storageRoot = realpath(storage_path('app/public'));
        $candidate = realpath(storage_path('app/public/'.$relativePath));

        if (! $storageRoot || ! $candidate || ! is_file($candidate)) {
            return null;
        }

        $rootPrefix = rtrim($storageRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (! str_starts_with($candidate, $rootPrefix)) {
            return null;
        }

        $mimeType = mime_content_type($candidate);
        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return null;
        }

        return $candidate;
    }
}
