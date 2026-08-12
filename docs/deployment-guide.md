# SahiGadi: Production Deployment Guide

Verified against the live environment on 2026-08-01, and again after the Laravel 13
upgrade. Supersedes the previous VPS/CloudPanel/PM2 guide, which described an
environment this project does not use.

---

## 1. The actual environment

| | |
|---|---|
| Host | Hostinger **shared** hosting (not a VPS, no root) |
| SSH user | `u587835185@in-mum-web2206` |
| **Live app root** | `~/domains/sahigadi.com/public_html` |
| **Document root** | `~/domains/sahigadi.com/public_html/public` |
| Framework | **Laravel 13** (requires PHP ^8.3; Symfony 8.1 components require **>= 8.4.1**) |
| Web PHP | **8.4.19** (set in hPanel per domain) |
| CLI PHP | `/opt/alt/php84/usr/bin/php` &rarr; 8.4.19 |
| Composer | `/usr/local/bin/composer` |
| Stack | MySQL, LiteSpeed |
| Frontend | Vite build output is **committed to git** |

Because the document root is Laravel's `public/` subdirectory, the application
files (`app/`, `config/`, `.env`, `storage/`) are **not** web-reachable. Verified:
`/composer.json`, `/artisan`, `/app/Models/Dealer.php` all return 404.

### ⚠️ The PHP version trap (this WILL break a deploy)

**hPanel's PHP selector only changes the WEB server. The SSH `php` binary stays on
an older version.** After switching the site to 8.4.19, the default CLI `php` was
still **8.3.30** — and because Laravel 13 pulls Symfony 8.1 components requiring
PHP >= 8.4.1, running `composer install` with the default binary fails the platform
check and can leave `vendor/` half-updated, taking the site down.

Always check both before deploying:

```bash
php -v | head -1                              # the CLI default - may be stale
/opt/alt/php84/usr/bin/php -v | head -1       # the 8.4 binary
```

If they differ, either use the explicit binary (see §3) or fix the CLI default:

```bash
mkdir -p ~/bin && ln -sf /opt/alt/php84/usr/bin/php ~/bin/php
printf 'export PATH="$HOME/bin:$PATH"\n' >> ~/.bashrc
# Login shells read the FIRST of .bash_profile / .bash_login / .profile and stop.
# Creating .bash_profile shadows Hostinger's .profile, so source both:
cat > ~/.bash_profile <<'EOF'
[ -f ~/.profile ] && . ~/.profile
[ -f ~/.bashrc ]  && . ~/.bashrc
EOF
```
Verify in a **new** SSH session — the current one already has the PATH exported.

### ⚠️ Composer resolves against the machine that runs it

`composer update` on a dev machine with a newer PHP will lock package versions that
the server cannot install. This upgrade was resolved on PHP 8.4 and locked Symfony
8.1 (needs >= 8.4.1), which would have failed on the then-8.3 server.

- On the server always use **`composer install`**, never `update` — the lock file is
  authoritative and gives exactly the versions that were tested.
- If dev and production PHP versions ever differ again, pin resolution to the
  server's version in `composer.json` before running `update`:
  `"config": { "platform": { "php": "8.3.30" } }`
- To check a lock file against a target version before deploying, look for packages
  whose `require.php` excludes it.

### Other host limitations

- **`exec()` and `symlink()` are disabled in PHP.** `php artisan storage:link`
  **fails** with `Call to undefined function Illuminate\Filesystem\exec()`.
  Create the symlink from the shell instead (see §4).
- **No Node.js build on the server.** Assets are built locally and committed,
  so `npm` is never run in production.
- **No PM2 / persistent processes**, therefore **no Inertia SSR**. The app falls
  back to client-side rendering. Do not follow SSR instructions from older docs.
- **LiteSpeed caches aggressively.** A file can keep returning 200 for minutes
  after deletion. Always cache-bust when verifying (`?v=$(date +%s)`) and check
  `content-type` — a 200 with `text/html` is an error page, not the real file.

### Directories that are NOT the live site

Two stale copies of the application exist. Neither is served, but both contain a
real `.env`. Do not deploy into them:

- `~/sahigadi.com/public_html` — old copy (note Hostinger's `DO_NOT_UPLOAD_HERE` marker)
- `~/public_html` — primary-domain copy, has `APP_URL=http://localhost`

Confirm which directory is live before any risky operation:

```bash
echo "SERVED_FROM_DOMAINS" > ~/domains/sahigadi.com/public_html/public/whoami.txt
curl -s "https://sahigadi.com/whoami.txt?v=$(date +%s)"    # expect SERVED_FROM_DOMAINS
rm -f ~/domains/sahigadi.com/public_html/public/whoami.txt
```

---

## 2. Pre-flight: check `.env` BEFORE deploying

**This step is mandatory.** Skipping it caused a production login outage on
2026-08-01.

Several credentials have **no hardcoded fallback** in `config/services.php` (they
were removed deliberately — the values were leaking in git history). If a key is
absent from `.env`, the feature fails **silently**: no exception, no log entry.
Missing `SMARTPING_*` means OTP SMS never sends, which takes down **customer
login entirely** (customer auth is OTP-only) plus dealer registration and
forgot-password.

```bash
cd ~/domains/sahigadi.com/public_html
for k in SMARTPING_USERNAME SMARTPING_PASSWORD SMARTPING_SENDER_ID \
         SMARTPING_DLT_CONTENT_ID SMARTPING_DLT_PRINCIPAL_ID \
         SERVICE_HISTORY_SECRET_KEY SERVICE_HISTORY_CLIENT_ID \
         VEHICLE_API_KEY RAZORPAY_KEY RAZORPAY_SECRET \
         PHONEPE_CLIENT_ID PHONEPE_CLIENT_SECRET PHONEPE_WEBHOOK_USER PHONEPE_WEBHOOK_PASS; do
  grep -qaE "^$k=.+" .env && echo "OK      $k" || echo "MISSING $k"
done
```

Also confirm these production values:

```bash
grep -aE "^(APP_ENV|APP_DEBUG|APP_URL)=" .env
```

Required: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://sahigadi.com`.

> `APP_URL` must be **https** in production — `PhonePeService::assertRedirectUrl()`
> throws if the callback URL is not HTTPS when `APP_ENV=production`, which breaks
> all PhonePe payments.

Fix anything missing **before** continuing. A stale `bootstrap/cache/config.php`
can mask a missing key (the cached file has old values baked in) until something
clears it — so never rely on "it works right now".

---

## 3. Deploy

Use the explicit 8.4 binary. It is correct regardless of shell config, and removes
any chance of running under a stale CLI PHP:

```bash
cd ~/domains/sahigadi.com/public_html
PHP84=/opt/alt/php84/usr/bin/php
$PHP84 -v | head -1                                       # must be >= 8.4.1

$PHP84 artisan down                                       # optional maintenance window
git pull origin main
$PHP84 /usr/local/bin/composer install --no-dev --optimize-autoloader
$PHP84 artisan migrate --force
$PHP84 artisan optimize:clear && $PHP84 artisan optimize
$PHP84 artisan up
```

Verify:

```bash
$PHP84 artisan --version                                  # Laravel Framework 13.x
$PHP84 artisan migrate:status | tail -3                   # newest migrations "Ran"
curl -s -o /dev/null -w "home %{http_code}  (200)\n" https://sahigadi.com
curl -s -o /dev/null -w "api  %{http_code}  (401)\n" -H "Accept: application/json" https://sahigadi.com/api/v1/account/balance
```

If `composer install` fails, **stop and read the error** rather than retrying — a
half-updated `vendor/` takes the site down. Rollback:

```bash
git checkout <previous-commit>
$PHP84 /usr/local/bin/composer install --no-dev --optimize-autoloader
$PHP84 artisan optimize:clear
```

<details>
<summary>Legacy form (only if the CLI default is already 8.4.1+)</summary>

```bash
cd ~/domains/sahigadi.com/public_html
php artisan down                       # optional; brief maintenance window
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan up
```
</details>

No `npm` step — `public/build` ships in the repository.

---

## 4. Storage symlink (only if missing or broken)

`php artisan storage:link` **does not work on this host.** Use the shell:

```bash
cd ~/domains/sahigadi.com/public_html
ls -ld public/storage      # must show: public/storage -> ../storage/app/public
```

If it is missing, or is a **real directory** rather than a symlink:

```bash
cd ~/domains/sahigadi.com/public_html
mkdir -p storage/app/public
cp -a public/storage/. storage/app/public/    # copy first - never move
rm -rf public/storage
ln -s ../storage/app/public public/storage
ls -ld public/storage
```

A real directory here is a genuine bug: uploads are written to
`storage/app/public/…` but the web only serves `public/storage/…`, so **newly
uploaded car images and profile photos silently do not display**.

### Private documents must never live under `public/`

Dealer KYC / PAN / GST files belong on the **private** disk:

```
storage/app/private/dealers/{kyc,pan,gst}/     ← correct (not web-reachable)
public/storage/dealers/{kyc,pan,gst}/          ← WRONG (publicly downloadable)
```

They are served only through authenticated routes
(`admin.dealers.document`, `dealer.profile.document`). Verify after any deploy
that touches storage:

```bash
cd ~/domains/sahigadi.com/public_html
ls storage/app/private/dealers/ 2>/dev/null     # expect: gst kyc pan
ls public/storage/dealers/ 2>/dev/null          # expect: profiles ONLY
```

Only `dealers/profiles` (profile photos) is meant to be public.

---

## 5. Post-deploy verification

```bash
cd ~/domains/sahigadi.com/public_html
php artisan migrate:status | tail -5
curl -s -o /dev/null -w "home            %{http_code}  (200)\n" https://sahigadi.com
curl -s -o /dev/null -w "app file hidden %{http_code}  (404)\n" https://sahigadi.com/composer.json
curl -s -o /dev/null -w "api unauth      %{http_code}  (401)\n" -H "Accept: application/json" https://sahigadi.com/api/v1/account/balance
php artisan tinker --execute="echo config('services.smartping.username') ? 'SMS creds OK' : 'SMS CREDS EMPTY - LOGIN BROKEN';"
```

Then confirm by hand — these exercise the paths that broke before:

1. **Send a real OTP** at `https://sahigadi.com/customer/login` (proves SMS works).
2. **Open any car listing** and confirm images render (proves the symlink works).
3. **Open a dealer in admin** and view a KYC document (proves private-disk reads work).

---

## 6. Rules learned from real incidents

**A fix that spans code + server state is not finished until the server state is
verified.** Both production incidents on 2026-08-01 came from shipping the code
half and leaving the server half as a checklist note:

- Pulling code whose migration had not been run → **500 on the wallet receipt
  page**, because the new code queried an `invoices` table that did not exist.
- Removing the credential fallbacks without adding the `.env` keys → **customer
  login outage**.
- Moving KYC storage to the private disk in code without moving the **files** →
  dealer Aadhaar/PAN documents stayed **publicly downloadable in production**
  for months while being reported as fixed.

Practical consequences:

- Any commit that removes a config fallback must ship **together** with the
  `.env` update, as a blocking pre-deploy step.
- Any commit that changes where files are stored must ship with the
  corresponding **file migration on the server**, verified with a live HTTP
  request.
- When verifying that something is no longer public, **cache-bust and inspect
  `content-type`** — LiteSpeed will happily serve a deleted file from cache.

---

## 7. Credential rotation

Because credentials are read from `.env` with no code fallback, rotation needs
**no code change and no redeploy**:

```bash
cd ~/domains/sahigadi.com/public_html
nano .env                                   # update the key(s)
php artisan config:clear && php artisan optimize
php artisan tinker --execute="echo config('services.smartping.username') ? 'OK' : 'EMPTY';"
```

Update the local `.env` to match. Rotate whenever a value may have been exposed —
several current values appeared in git history and must be rotated.

---

## 8. Rollback

```bash
cd ~/domains/sahigadi.com/public_html
git log --oneline -5
git checkout <previous-commit>
php artisan optimize:clear && php artisan optimize
```

Assets are committed, so checking out an older commit restores the matching
frontend build automatically.

**Migrations do not roll back automatically.** Check whether the bad deploy ran
any (`php artisan migrate:status`) and roll them back deliberately with
`php artisan migrate:rollback --step=1` only if the migration is genuinely
reversible.
