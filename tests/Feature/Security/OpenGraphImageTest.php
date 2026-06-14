<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OpenGraphImageTest extends TestCase
{
    private string $fixturePath;

    private ?string $cachePath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = storage_path('app/public/testing/open-graph.png');
        File::ensureDirectoryExists(dirname($this->fixturePath));
        File::put(
            $this->fixturePath,
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
                true
            )
        );

        $canonicalFixturePath = realpath($this->fixturePath);
        $this->cachePath = storage_path('app/public/og-cache/')
            .hash('sha256', $canonicalFixturePath.'|'.filemtime($canonicalFixturePath))
            .'.jpg';
    }

    protected function tearDown(): void
    {
        File::delete(array_filter([$this->fixturePath, $this->cachePath]));
        File::deleteDirectory(storage_path('app/public/testing'));

        parent::tearDown();
    }

    public function test_it_redirects_to_the_default_image_when_path_is_missing(): void
    {
        $this->get('/og-image')
            ->assertRedirect(asset('images/og-image.png'));
    }

    public function test_it_rejects_directory_traversal_attempts(): void
    {
        $this->get('/og-image?path=../../.env.png')
            ->assertRedirect(asset('images/og-image.png'));
    }

    public function test_it_rejects_non_image_files_even_with_an_allowed_extension(): void
    {
        $invalidPath = storage_path('app/public/testing/not-an-image.png');
        File::put($invalidPath, '<?php echo "not an image";');

        try {
            $this->get('/og-image?path=testing/not-an-image.png')
                ->assertRedirect(asset('images/og-image.png'));
        } finally {
            File::delete($invalidPath);
        }
    }

    public function test_it_serves_a_cached_social_image_for_a_valid_public_storage_image(): void
    {
        $this->get('/og-image?path=storage/testing/open-graph.png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertFileExists($this->cachePath);
    }
}
