# SahiGadi: Production Deployment Guide

This guide details the process of deploying the newly refactored SahiGadi platform (Laravel 12 + Vue 3 + Inertia.js + Tailwind CSS) onto a Hostinger VPS managed via CloudPanel.

## Prerequisites
- **Server:** Hostinger VPS
- **Management Panel:** CloudPanel
- **Stack:** PHP 8.2+, Node.js 18+, MySQL, Nginx
- **Tools:** Git, PM2 (for SSR)

---

## 1. Preparing the Server (CloudPanel)

1. **SSH into the Server:**
   ```bash
   ssh root@<your-vps-ip>
   ```

2. **Install Node.js & PM2:**
   CloudPanel usually runs PHP by default. You will need Node.js to build the assets and run the SSR server.
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
   sudo apt-get install -y nodejs
   sudo npm install -g pm2
   ```

3. **Navigate to the Application Directory:**
   ```bash
   cd /htdocs/sahigadi.com/
   ```

---

## 2. Deploying the Application

1. **Pull the Latest Code:**
   Fetch the `redesign/vue-inertia` branch.
   ```bash
   git pull origin redesign/vue-inertia
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Install Node Dependencies:**
   ```bash
   npm ci
   ```

4. **Build Frontend Assets:**
   Compile the Vue components and Tailwind styles for production.
   ```bash
   npm run build
   ```

5. **Optimize Laravel:**
   Clear and cache configurations, routes, and views.
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 3. Configuring Server-Side Rendering (SSR)

To ensure maximum SEO for the Car Listings and Home Page, Inertia SSR must run continuously in the background.

1. **Test the SSR Build:**
   ```bash
   php artisan inertia:start-ssr
   ```
   *If successful, press `Ctrl+C` to stop.*

2. **Run SSR via PM2:**
   Instead of running the command manually, we will daemonize it using PM2 so it automatically restarts on failure or server reboot.
   ```bash
   pm2 start "php artisan inertia:start-ssr" --name "sahigadi-ssr"
   pm2 save
   pm2 startup
   ```

3. **Verify PM2 Status:**
   ```bash
   pm2 status
   pm2 logs sahigadi-ssr
   ```

---

## 4. Rollback Strategy (The "Kill-Switch")

Because we kept the legacy Blade views intact, rolling back to the old UI in case of an emergency is instantaneous.

### Option A: Controller-Level Reversion (Recommended)
If you only need to revert a specific page (e.g., the Homepage):
1. Open `app/Http/Controllers/Frontend/HomeController.php`.
2. Change:
   ```php
   return \Inertia\Inertia::render('Public/Home', compact(...));
   ```
   Back to:
   ```php
   return view('frontend.home', compact(...));
   ```
3. Run `php artisan optimize:clear`.

### Option B: Branch Reversion (Full Rollback)
If you need to instantly restore the entire application to the Bootstrap 5 version:
1. SSH into the server.
2. Checkout the `main` or production branch:
   ```bash
   git checkout main
   ```
3. Clear caches:
   ```bash
   php artisan optimize:clear
   ```
4. Stop the SSR server (optional, to save RAM):
   ```bash
   pm2 stop sahigadi-ssr
   ```

---

## 5. SEO Verification Post-Deployment
Once live, ensure the following checklist is completed:
1. **Source Code Check:** Right-click the live page -> "View Page Source". You should see fully rendered HTML (not an empty `<div id="app"></div>`) proving that SSR is working.
2. **Schema Testing:** Run a car detail URL through the [Google Rich Results Test](https://search.google.com/test/rich-results) to ensure the Breadcrumb and Car JSON-LD schemas are detected.
3. **Contact Security:** Inspect the page payload/network requests on the Car Detail page to ensure `phone` and `email` variables do NOT exist until an OTP is verified.
