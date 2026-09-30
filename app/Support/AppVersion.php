<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * What is actually running right now.
 *
 * Deploys here are a `git pull` on the server, so .git is present and is the
 * only thing that always tells the truth about which code is live. Working out
 * whether a deploy had landed has cost real time on this project - once because
 * there are two checkouts of the app on the box and only one is served - so the
 * answer belongs somewhere you can just look at.
 *
 * Everything degrades to null rather than throwing: a missing or unreadable
 * .git must never take a page down.
 */
class AppVersion
{
    /** Memoised for the request; deliberately not cached across requests, so a deploy shows up at once. */
    private static ?array $resolved = null;

    public static function all(): array
    {
        return self::$resolved ??= [
            'version' => config('app.version'),
            'commit' => self::commit(),
            'deployedAt' => self::deployedAt()?->toIso8601String(),
            'deployedAtLabel' => self::deployedAt()?->timezone('Asia/Kolkata')->format('d M Y, g:i A'),
        ];
    }

    /** Short commit hash of the checked-out code, or null. */
    public static function commit(): ?string
    {
        $sha = self::readSha();

        return $sha ? substr($sha, 0, 7) : null;
    }

    /**
     * When this code arrived on the server. The ref file is rewritten by the
     * pull, so its mtime is the deploy time - which is the question people
     * actually ask, rather than when the commit was authored.
     */
    public static function deployedAt(): ?Carbon
    {
        $path = self::refPath() ?? self::gitPath('HEAD');

        if (! $path || ! is_readable($path)) {
            return null;
        }

        $mtime = @filemtime($path);

        return $mtime ? Carbon::createFromTimestamp($mtime) : null;
    }

    private static function readSha(): ?string
    {
        $ref = self::refPath();

        if ($ref && is_readable($ref)) {
            $sha = trim((string) @file_get_contents($ref));

            if (preg_match('/^[0-9a-f]{40}$/i', $sha)) {
                return $sha;
            }
        }

        // A repository that has been `git gc`'d keeps its refs in one file.
        $packed = self::gitPath('packed-refs');
        $branch = self::branch();

        if ($branch && $packed && is_readable($packed)) {
            foreach (preg_split('/\R/', (string) @file_get_contents($packed)) ?: [] as $line) {
                if (preg_match('/^([0-9a-f]{40})\s+refs\/heads\/'.preg_quote($branch, '/').'$/i', trim($line), $m)) {
                    return $m[1];
                }
            }
        }

        // A detached HEAD holds the hash directly.
        $head = self::gitPath('HEAD');

        if ($head && is_readable($head)) {
            $contents = trim((string) @file_get_contents($head));

            if (preg_match('/^[0-9a-f]{40}$/i', $contents)) {
                return $contents;
            }
        }

        return null;
    }

    private static function branch(): ?string
    {
        $head = self::gitPath('HEAD');

        if (! $head || ! is_readable($head)) {
            return null;
        }

        $contents = trim((string) @file_get_contents($head));

        return preg_match('/^ref:\s*refs\/heads\/(.+)$/', $contents, $m) ? $m[1] : null;
    }

    private static function refPath(): ?string
    {
        $branch = self::branch();

        return $branch ? self::gitPath('refs/heads/'.$branch) : null;
    }

    private static function gitPath(string $relative): ?string
    {
        $git = base_path('.git');

        if (! is_dir($git)) {
            return null;
        }

        return $git.'/'.$relative;
    }
}
