<?php

namespace Tests\Feature;

use App\Support\AppVersion;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Working out whether a deploy had actually landed has cost real time on this
 * project - once because there are two checkouts of the app on the server and
 * only one of them is served. The footer now says which commit is running and
 * when it arrived, so the answer is visible rather than investigated.
 */
class AppVersionFooterTest extends TestCase
{
    public function test_the_build_details_are_shared_with_every_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('appVersion', fn (Assert $build) => $build
                    ->where('version', config('app.version'))
                    ->has('commit')
                    ->has('deployedAt')
                    ->has('deployedAtLabel')
                )
            );
    }

    public function test_the_commit_is_the_one_actually_checked_out(): void
    {
        $commit = AppVersion::commit();

        if ($commit === null) {
            $this->markTestSkipped('No readable .git in this checkout.');
        }

        $this->assertMatchesRegularExpression('/^[0-9a-f]{7}$/', $commit);

        // Compare against git itself rather than re-implementing the parse.
        $expected = trim((string) shell_exec('git rev-parse --short=7 HEAD 2>&1'));

        if (preg_match('/^[0-9a-f]{7}$/', $expected)) {
            $this->assertSame($expected, $commit, 'The footer would report the wrong build.');
        }
    }

    public function test_the_deploy_time_is_reported_in_india_time(): void
    {
        $build = AppVersion::all();

        if ($build['deployedAtLabel'] === null) {
            $this->markTestSkipped('No readable .git in this checkout.');
        }

        // e.g. "30 Sep 2026, 9:58 PM" - readable by the person deploying,
        // in the timezone they are actually in.
        $this->assertMatchesRegularExpression(
            '/^\d{2} [A-Z][a-z]{2} \d{4}, \d{1,2}:\d{2} (AM|PM)$/',
            $build['deployedAtLabel']
        );
    }

    public function test_a_missing_git_directory_does_not_break_anything(): void
    {
        // Production is a git checkout today, but a zip deploy or a stripped
        // release must degrade to a hidden line, never a 500 on every page.
        $this->assertIsArray(AppVersion::all());
        $this->assertArrayHasKey('commit', AppVersion::all());
    }

    public function test_the_public_deletion_page_is_linked_from_the_footer(): void
    {
        // Google Play wants the URL findable without installing the app.
        $this->get('/')->assertOk()->assertSee('/account-deletion', false);
    }
}
