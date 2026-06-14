# Phased Migration Plan: SahiGadi Redesign

## Phase 1: Audit + Setup Vue/Inertia (No Live Disruptions)
- [ ] Create `/docs/upgrade-audit.md` (Completed)
- [ ] Create `/docs/redesign-plan.md` (Completed)
- [ ] Install dependencies: `vue`, `@inertiajs/vue3`, `@vitejs/plugin-vue`, `typescript`, `@inertiajs/core`.
- [ ] Configure `vite.config.js` with Vue and Inertia plugins.
- [ ] Setup `tsconfig.json`.
- [ ] Initialize Tailwind CSS properly in `resources/css/app.css`.
- [ ] Create root `resources/views/app.blade.php` specifically for Inertia (keep legacy layout as `legacy-app.blade.php` if needed to prevent breaking).
- [ ] Setup `HandleInertiaRequests` middleware.

## Phase 2: Redesign Homepage and Browse Cars (`/cars`)
- [ ] **Components:** `AppLayout.vue`, `Navbar.vue`, `Footer.vue`, `HeroSearch.vue`, `CarCard.vue`, `CarGrid.vue`, `CarFilters.vue`.
- [ ] **Homepage:** Convert `HomeController@index` to return `Inertia::render('Public/Home')`.
- [ ] **Browse Cars:** Convert `CarController@index` to return `Inertia::render('Public/Cars/Index')`. Implement Left sidebar / Mobile bottom drawer filters.
- [ ] Add SSR support for SEO rendering on these pages.

## Phase 3: Car Detail Page & Security Fixes (`/car/{slug}`)
- [ ] **Components:** `ImageGallery.vue`, `SellerCard.vue`, `OtpUnlockModal.vue`.
- [ ] **Security:** Refactor `CarController@show` to return an API Resource that strictly **omits** the seller's phone number.
- [ ] Implement the Contact Unlock API flow (Send OTP, Verify OTP -> Return Phone Number).
- [ ] Render Open Graph meta tags server-side for the specific car.

## Phase 4: Sell Your Car, Dealer Registration & Auth
- [ ] **Sell Car:** Convert `SellCarController` to render `Public/SellCar.vue`. Add multi-step form with Vue reactive state.
- [ ] **Dealer Registration:** Convert `AuthController@showRegister` to render `Dealer/Register.vue`.
- [ ] **Auth Pages:** Convert Customer and Dealer login pages to Inertia (`Auth/CustomerLogin.vue`, `Auth/DealerLogin.vue`).
- [ ] Ensure strict route protection (Guest vs Auth middleware).

## Phase 5: Dealer & Customer Dashboards
- [ ] **Layouts:** Create `DealerLayout.vue` and `CustomerLayout.vue`.
- [ ] **Dealer Dashboard:** Implement `Dealer/Dashboard.vue`, `Dealer/Cars/Index.vue`, `Dealer/Cars/Create.vue`. 
- [ ] Replace legacy views with robust Vue forms for listing management.
- [ ] **Customer Dashboard:** Implement `Customer/Dashboard.vue` with saved cars, recent enquiries.

## Phase 6: SEO, Performance, Mobile Testing & Deployment
- [ ] Setup Dynamic Meta Tags via Inertia Head component.
- [ ] Ensure `sitemap.xml` includes all dynamic car links.
- [ ] Audit Core Web Vitals (Skeleton loaders, lazy loading images).
- [ ] Verify Mobile Responsiveness across all new pages.
- [ ] Build production assets (`npm run build`).
- [ ] Run full QA sweep on active routing and API rate limits.
