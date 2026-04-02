# Shelf-e: Complete & Polish Implementation Plan

## Context

Shelf-e is a Laravel 12 e-commerce bookstore that is **feature-complete** (9 models, 22 controllers, 41 Blade templates) but needs bug fixes, hardening, test coverage, missing small features, and CI/CD. The app already has: catalog browsing with filters, session-based cart, COD checkout, wishlist, admin panel (books, orders, hero slides, dashboard), live search, newsletter, and random quotes. This plan brings it to production quality.

**Decisions:** COD only (no Stripe), content-only admin (no user management), full CI/CD with automated deploy.

---

## Architecture Overview

```
Tech Stack: Laravel 12 / PHP 8.2+ / Tailwind CSS 3 / Alpine.js 3 / Vite 7 / SQLite / Breeze / PHPUnit + Mockery

Modules:
  Frontend  -- Blade templates + Alpine.js interactivity + Tailwind styling, bundled by Vite
  Backend   -- Laravel MVC: Controllers, Models, Middleware, Policies, Notifications, Mailables
  Database  -- SQLite with 9 tables: users, products, categories, orders, order_items, wishlists, hero_slides, quotes, subscribers
  Auth      -- Laravel Breeze (email/password, verification, password reset) + IsAdmin middleware

Folder Structure:
  app/Http/Controllers/         -- 22 controllers (public, auth, admin)
  app/Http/Controllers/Admin/   -- AdminDashboard, AdminBook, AdminOrder, HeroSlide (+new: AdminQuote, AdminSubscriber)
  app/Http/Middleware/           -- IsAdmin, PreventBackHistory
  app/Models/                    -- User, Product, Category, Order, OrderItem, Wishlist, HeroSlide, Quote, Subscriber
  app/Mail/                      -- (new) ContactFormMail, WelcomeSubscriberMail, OrderPlacedMail
  app/Notifications/             -- (new) OrderStatusNotification
  app/Policies/                  -- OrderPolicy
  database/factories/            -- UserFactory, ProductFactory (+new: CategoryFactory, OrderFactory)
  database/seeders/              -- DatabaseSeeder, CategorySeeder, ProductSeeder, QuoteSeeder
  resources/views/               -- 41+ Blade templates across admin/, auth/, categories/, checkout/, components/, layouts/, etc.
  routes/web.php                 -- All routes (public, cart, checkout, auth, admin)
  tests/Feature/                 -- Auth tests (+new: Catalog, Cart, Checkout, Wishlist, Search, Contact, Newsletter, Admin/*)
  tests/Unit/                    -- (+new: ProductTest, OrderTest)
```

---

## Phase 1: Bug Fixes & Hardening (15 tasks)

### Critical Bugs

- [ ] **1.1** Fix ContactController method name mismatch: route calls `store()` but controller defines `submit()` -- rename method to `store()` so `POST /contact` works.
  - `app/Http/Controllers/ContactController.php` (line 10: rename `submit` to `store`)

- [ ] **1.2** Fix DatabaseSeeder referencing removed `price` column -- use `paperback_price`/`hardcover_price` matching ProductFactory pattern.
  - `database/seeders/DatabaseSeeder.php` (lines 41-42)

- [ ] **1.3** Remove duplicate `/random-quote` route at line 62 of web.php (keep line 29).
  - `routes/web.php` (delete lines 62-64)

- [ ] **1.4** Remove `@tailwindcss/vite` v4 from package.json (unused, conflicts with working Tailwind v3 setup).
  - `package.json`

- [ ] **1.5** Fix DashboardController duplicate queries -- remove redundant `$orders` query, keep only `$recentOrders`.
  - `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard.blade.php`

### Security & Hardening

- [ ] **1.6** Add stock validation to `CartController::add()` -- reject if `stock_quantity` is 0 or requested qty exceeds stock.
  - `app/Http/Controllers/CartController.php`

- [ ] **1.7** Add stock validation to `CartController::increment()` -- prevent incrementing beyond available stock.
  - `app/Http/Controllers/CartController.php`

- [ ] **1.8** Add server-side stock re-verification inside `OrderController::store()` DB transaction -- abort if any item went out of stock between cart-add and checkout.
  - `app/Http/Controllers/OrderController.php`

- [ ] **1.9** Add format validation to `CartController::add()` -- reject if chosen format (paperback/hardcover) has null price for the product.
  - `app/Http/Controllers/CartController.php`

- [ ] **1.10** Add rate limiting to `/api/search-books` (60/min) and search term length limit (max 100 chars) in `CatalogController::liveSearch()`.
  - `routes/web.php`, `app/Http/Controllers/CatalogController.php`

- [ ] **1.11** Add MIME type validation to `HeroSlideController::store()` and `update()` (matching AdminBookController's security pattern).
  - `app/Http/Controllers/Admin/HeroSlideController.php`

- [ ] **1.12** Move inline checkout GET closure (web.php lines 83-95) into `OrderController::index()` method.
  - `routes/web.php`, `app/Http/Controllers/OrderController.php`

- [ ] **1.13** Remove unused `App\Models\Book` import from AdminBookController (line 10).
  - `app/Http/Controllers/Admin/AdminBookController.php`

- [ ] **1.14** Add `with('category')` eager loading to `CatalogController::show()` to prevent N+1 on product detail page.
  - `app/Http/Controllers/CatalogController.php`

- [ ] **1.15** Run Laravel Pint on entire codebase to normalize code style; create `pint.json` with PSR-12 preset.
  - `pint.json` (new), all PHP files

---

## Phase 2: Missing Features (8 tasks)

- [ ] **2.1** Create dedicated cart view: add `CartController::index()`, `GET /cart` route, and `resources/views/cart/index.blade.php` showing items with qty controls and "Proceed to Checkout" button. Update navbar cart icon to link to `/cart`.
  - `app/Http/Controllers/CartController.php`, `routes/web.php`, `resources/views/cart/index.blade.php` (new), `resources/views/layouts/app.blade.php`

- [ ] **2.2** Create `ContactFormMail` Mailable and email template; update `ContactController::store()` to dispatch it to `env('CONTACT_MAIL_TO')`.
  - `app/Mail/ContactFormMail.php` (new), `resources/views/emails/contact.blade.php` (new), `app/Http/Controllers/ContactController.php`

- [ ] **2.3** Create `WelcomeSubscriberMail` Mailable; update `NewsletterController::subscribe()` to queue it to the subscriber.
  - `app/Mail/WelcomeSubscriberMail.php` (new), `resources/views/emails/newsletter-welcome.blade.php` (new), `app/Http/Controllers/NewsletterController.php`

- [ ] **2.4** Create `AdminQuoteController` with full CRUD (index, create, store, edit, update, destroy) + Blade views + admin routes.
  - `app/Http/Controllers/Admin/AdminQuoteController.php` (new), `resources/views/admin/quotes/index.blade.php` (new), `resources/views/admin/quotes/form.blade.php` (new), `routes/web.php`

- [ ] **2.5** Create `AdminSubscriberController` with index and destroy + Blade view + admin routes.
  - `app/Http/Controllers/Admin/AdminSubscriberController.php` (new), `resources/views/admin/subscribers/index.blade.php` (new), `routes/web.php`

- [ ] **2.6** Create `OrderStatusNotification` (Laravel Notification); dispatch from `AdminOrderController::updateStatus()` when status changes.
  - `app/Notifications/OrderStatusNotification.php` (new), `app/Http/Controllers/Admin/AdminOrderController.php`, `resources/views/emails/order-status.blade.php` (new)

- [ ] **2.7** Create `OrderPlacedMail` Mailable; dispatch from `OrderController::store()` after successful order creation.
  - `app/Mail/OrderPlacedMail.php` (new), `resources/views/emails/order-placed.blade.php` (new), `app/Http/Controllers/OrderController.php`

- [ ] **2.8** Add "Quotes" and "Subscribers" links to admin sidebar navigation.
  - `resources/views/layouts/admin.blade.php`

---

## Phase 3: Testing (22 tasks)

All tests use `RefreshDatabase` trait, in-memory SQLite, and existing factories.

### Setup

- [ ] **3.1** Create `CategoryFactory` and `OrderFactory` so tests can create categories and orders without seeders.
  - `database/factories/CategoryFactory.php` (new), `database/factories/OrderFactory.php` (new)

### Unit Tests

- [ ] **3.2** Test `Product::display_price` accessor: returns paperback-only, hardcover-only, min of both, and 0 when neither.
  - `tests/Unit/ProductTest.php` (new)

- [ ] **3.3** Test Product and Order model relationships (belongsTo, hasMany).
  - `tests/Unit/ProductTest.php`, `tests/Unit/OrderTest.php` (new)

### Feature Tests -- Catalog & Search

- [ ] **3.4** Test homepage (200), categories page (200 + products), category filter, search filter, pagination (24/page).
  - `tests/Feature/CatalogTest.php` (new)

- [ ] **3.5** Test product detail page (200 for valid slug, 404 for invalid).
  - `tests/Feature/CatalogTest.php`

- [ ] **3.6** Test live search API: returns JSON, respects min query length, limits to 5 results.
  - `tests/Feature/LiveSearchTest.php` (new)

### Feature Tests -- Cart

- [ ] **3.7** Test cart add (creates session entry), add duplicate (increments qty), add non-existent product (404).
  - `tests/Feature/CartTest.php` (new)

- [ ] **3.8** Test cart remove, increment, decrement, and decrement-to-zero removes item.
  - `tests/Feature/CartTest.php`

- [ ] **3.9** Test cart view page (200, shows items, shows empty state).
  - `tests/Feature/CartTest.php`

### Feature Tests -- Checkout

- [ ] **3.10** Test checkout validation (missing fields -> errors), empty cart -> redirect, valid data -> creates Order + OrderItems.
  - `tests/Feature/CheckoutTest.php` (new)

- [ ] **3.11** Test checkout decrements `stock_quantity` and clears session cart.
  - `tests/Feature/CheckoutTest.php`

### Feature Tests -- Wishlist

- [ ] **3.12** Test wishlist: guest -> redirect to login, auth user -> toggle adds/removes, index shows items.
  - `tests/Feature/WishlistTest.php` (new)

### Feature Tests -- Contact & Newsletter

- [ ] **3.13** Test contact form: valid data -> success flash + `Mail::assertSent`, invalid data -> validation errors.
  - `tests/Feature/ContactTest.php` (new)

- [ ] **3.14** Test newsletter: valid email -> creates Subscriber + sends welcome mail, duplicate email -> no duplicate record.
  - `tests/Feature/NewsletterTest.php` (new)

### Feature Tests -- Admin

- [ ] **3.15** Test admin dashboard: admin sees stats, non-admin gets 403/redirect, guest gets redirect.
  - `tests/Feature/Admin/AdminDashboardTest.php` (new)

- [ ] **3.16** Test admin book CRUD: list, create, store (with image), edit, update, delete -- all reject non-admin.
  - `tests/Feature/Admin/AdminBookTest.php` (new)

- [ ] **3.17** Test admin order management: list, show, update status (+ notification sent), reject non-admin.
  - `tests/Feature/Admin/AdminOrderTest.php` (new)

- [ ] **3.18** Test admin hero slides CRUD: list, create, store (with image upload), update, delete.
  - `tests/Feature/Admin/AdminHeroSlideTest.php` (new)

- [ ] **3.19** Test admin quotes CRUD: list, create, store, edit, update, delete.
  - `tests/Feature/Admin/AdminQuoteTest.php` (new)

- [ ] **3.20** Test admin subscribers: list, delete subscriber.
  - `tests/Feature/Admin/AdminSubscriberTest.php` (new)

### Feature Tests -- Rate Limiting

- [ ] **3.21** Test cart rate limit (429 after 30 requests/min) and checkout rate limit (429 after 5 requests/min).
  - `tests/Feature/RateLimitTest.php` (new)

### Final Test Verification

- [ ] **3.22** Run full test suite (`php artisan test`) and verify all tests pass with zero failures.

---

## Phase 4: CI/CD & Deployment (8 tasks)

- [ ] **4.1** Create `.github/workflows/ci.yml` with 3 jobs: Lint (Pint), Test (PHPUnit on PHP 8.2 + SQLite), Build (Vite).
  - `.github/workflows/ci.yml` (new)

- [ ] **4.2** Create `.env.production.example` with all required env vars documented (APP_KEY, DB, MAIL, QUEUE, SESSION, CACHE).
  - `.env.production.example` (new)

- [ ] **4.3** Verify Vite production build outputs hashed assets to `public/build/manifest.json`.
  - `vite.config.js`

- [ ] **4.4** Add `GET /health` endpoint returning `{"status":"ok","timestamp":"..."}` for deployment health checks.
  - `routes/web.php`

- [ ] **4.5** Create `deploy.sh` script: composer install --no-dev, config/route/view cache, migrate --force, npm ci + build, storage:link.
  - `deploy.sh` (new)

- [ ] **4.6** Create `.github/workflows/deploy.yml` for automated deploy to hosting platform on push to `main` after CI passes.
  - `.github/workflows/deploy.yml` (new)

- [ ] **4.7** Add `.env.production` to `.gitignore` and verify no secrets are committed.
  - `.gitignore`

- [ ] **4.8** End-to-end verification: fresh clone -> `composer setup` -> seed -> browse all pages -> add to cart -> checkout -> admin panel -> confirm everything works.

---

## Verification Plan

1. **After Phase 1:** Run `php artisan serve`, manually test contact form, seeder (`php artisan db:seed`), and product detail page. Run `npm run build` to verify no Tailwind conflicts.
2. **After Phase 2:** Test each new feature: visit `/cart`, subscribe to newsletter (check email in log), create/edit/delete quotes and subscribers in admin, place order and verify confirmation email.
3. **After Phase 3:** Run `php artisan test` -- expect 70+ tests, 0 failures. Check coverage of all critical paths.
4. **After Phase 4:** Push to GitHub, verify CI pipeline passes (lint + test + build), trigger deploy, hit `/health` endpoint on production.

---

## Execution Order Summary

| Order | Tasks | Dependencies |
|-------|-------|-------------|
| 1 | 1.1-1.5 (bug fixes) | None -- can parallelize |
| 2 | 1.6-1.15 (hardening) | None -- can parallelize |
| 3 | 3.1 (factories) | Must precede all tests |
| 4 | 2.1-2.8 (features) + 3.2-3.3 (unit tests) | Factories ready |
| 5 | 3.4-3.21 (feature tests) | Features complete |
| 6 | 3.22 (full suite run) | All tests written |
| 7 | 4.1-4.8 (CI/CD) | Tests passing |

**Total: 53 atomic tasks across 4 phases.**
