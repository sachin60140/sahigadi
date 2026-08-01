# SahiGadi: Production Deployment Guide

Verified against the live environment on 2026-08-01. Supersedes the previous
VPS/CloudPanel/PM2 guide, which described an environment this project does not use.

---

## 1. The actual environment

| | |
|---|---|
| Host | Hostinger **shared** hosting (not a VPS, no root) |
| SSH user | `u587835185@in-mum-web2206` |
| **Live app root** | `~/domains/sahigadi.com/public_html` |
| **Document root** | `~/domains/sahigadi.com/public_html/public` |
| Stack | PHP 8.2+, MySQL, LiteSpeed |
| Frontend | Vite build output is **committed to git** |

Because the document root is Laravel's `public/` subdirectory, the application
files (`app/`, `config/`, `.env`, `storage/`) are **not** web-reachable. Verified:
`/composer.json`, `/artisan`, `/app/Models/Dealer.php` all return 404.

### Host limitations that affect deployment

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
