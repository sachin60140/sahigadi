# Technical Audit Report: SahiGadi Upgrade

## 1. Current State Overview
**Framework:** Laravel 12
**Frontend UI:** Bootstrap 5 (Blade templates)
**Build Tool:** Vite (configured with Tailwind v4 in `package.json`, but currently relies on Blade/Bootstrap)
**Database:** MySQL (Existing, with substantial models & migrations)
**Auth System:** Custom Multi-Auth (Admin, Dealer, Customer, Web/User)

## 2. Directory & Structure Analysis
- **Controllers (`app/Http/Controllers/`):** 
  Contains subdirectories for Admin, Dealer, Customer, and Frontend. Uses traditional MVC with Blade responses.
- **Models (`app/Models/`):**
  Robust schema including `Car`, `Dealer`, `Customer`, `Enquiry`, `Payment`, `ServiceHistory`, `ChallanSearch`, `Wallet`, `Subscription`, `Plan`, `FeaturedListing`.
- **Routes (`routes/web.php`):**
  Extensive routing map covering all 4 auth guards. Contains temporary utility routes for caching/migrations/images. Needs careful refactoring to swap `view()` responses to `Inertia::render()`.
- **Views (`resources/views/`):**
  Relies on `app.blade.php`, `dealer.blade.php`, `admin.blade.php` for layouts.

## 3. Security & Validation Assessment
- OTP Logic: Exists via API routes (`/api/enquiry/send-otp`, `/api/enquiry/verify-otp`) and within `Frontend/SellCarController`.
- **Risk Identified:** Currently, full seller contact info might be passed to the frontend view or available in DOM before OTP. This is explicitly violating the redesign constraint.
- Request Validation: Existing controllers seem to rely on basic validation. Need to ensure `FormRequests` are strictly utilized.

## 4. Performance Assessment
- Images are being optimized via a temporary Artisan route. We need to implement proper image processing during upload (Intervention Image v3 is in `composer.json`).
- Pagination and Eager Loading must be audited at the controller level during the Inertia migration.

## 5. Upgrade Path to Vue 3 + Inertia + Tailwind
- **Vue & Inertia:** Needs installation (`vue`, `@vitejs/plugin-vue`, `@inertiajs/vue3`, `inertiajs/core`).
- **Tailwind CSS:** `tailwindcss` v4 is present in `package.json`, needs verification and setup with PostCSS.
- **TypeScript:** Needs `typescript`, `vue-tsc` installed, `tsconfig.json` generated, and Vite configured.
- **Hybrid Approach:** We can incrementally replace `app.blade.php` pages with Inertia pages using `Route::inertia()` or controller returns without breaking `admin.blade.php`.

## 6. Recommendations
1. Install and configure Vue 3 + Inertia + TypeScript without altering existing Blade views immediately.
2. Setup an `app.blade.php` root file for Inertia separate from the legacy bootstrap layout.
3. Migrate Frontend routes module by module.
4. Implement strict API resource filtering so phone numbers are never returned in page props unless explicitly verified via OTP.
