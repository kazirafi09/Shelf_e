# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Core Tech Stack
* **Backend:** Laravel 12 (PHP 8.2+)
* **Frontend:** Tailwind CSS 4, Alpine.js 3 (`@alpinejs/collapse` included)
* **Build Tool:** Vite 7
* **Database:** SQLite (in-memory SQLite for tests)
* **Auth:** Laravel Breeze (session-based) + Laravel Socialite (Google OAuth)
* **Testing:** PHPUnit, Mockery

## Common Commands

```bash
# Start all dev servers concurrently (PHP server, queue, logs, Vite)
composer dev

# Run all tests
composer test
php artisan test

# Run a single test file
php artisan test tests/Feature/CheckoutTest.php

# Run a specific test method
php artisan test --filter=test_method_name

# Build frontend assets
npm run build

# Dev Vite only
npm run dev

# Code formatting (Laravel Pint)
./vendor/bin/pint

# Database: reset and reseed
php artisan migrate:fresh --seed
```

## Architecture Overview

Shelf-e is an online bookstore with a public catalog, session-based cart, checkout, and a full admin panel.

### Key Directories
- `app/Http/Controllers/` — thin controllers; validation + delegate to models/services
- `app/Http/Controllers/Admin/` — admin CRUD (books, orders, authors, reviews, vouchers, coins, settings, scraper)
- `app/Models/` — all business logic lives here or in Services
- `app/Services/` — `CoinService` (credit/debit/balance with DB transactions), `BookScraperService`, `ReviewService`
- `app/Http/Middleware/IsAdmin.php` — gates all `/admin/*` routes
- `app/Providers/AppServiceProvider.php` — global category View::composer with 1-hour cache; HTTPS enforcement in production
- `resources/views/` — Blade templates (auth/, admin/, account/, products/, orders/, checkout/, pages/)
- `routes/web.php` — all routes (public, auth, admin); rate-limited cart (30/min), checkout (5/min), search (60/min)

### Data Model Highlights
- **Product** — `paperback_price`, `hardcover_price`, `sale_price`; belongs to Category, belongs to many Authors
- **Order** — supports guest checkout (`user_id` nullable); stores full shipping snapshot; statuses managed by admin
- **Voucher** — percentage or fixed discount; per-user usage limits; `isUsable()` / `calculateDiscount()` on the model
- **CoinLedger** — append-only ledger; `CoinService` is the only entry point for mutations (uses DB transactions)
- **Setting** — key-value store for admin-configurable shipping rates, FAQ, returns policy, etc.
- **Review** — requires admin approval before display

### Caching
- Homepage data: 5 minutes
- Global navigation categories: 1 hour (invalidate manually or via cache:clear if categories change)

### Testing Conventions
- PHPUnit test suites: `Unit` (`tests/Unit/`) and `Feature` (`tests/Feature/`)
- Tests use SQLite in-memory; queue/cache/session are all array drivers
- Unit tests for Services are in `tests/Unit/Services/`
- Feature tests cover checkout flows, cart, auth, and admin operations

## AI Development Rules (Strict)
1. **Atomic Execution:** Only implement the specific task requested. Do not attempt to build out the entire feature if the prompt only asks for a single controller or view.
2. **Alpine.js over jQuery/Vanilla:** All frontend reactivity must be handled natively with Alpine.js (`x-data`, `x-on`, etc.).
3. **Tailwind Utility Classes:** Utilize Tailwind for all styling. Do not write custom CSS unless absolutely necessary for animations or complex layouts not supported by utilities.
4. **Fat Models, Skinny Controllers:** Keep business logic inside Eloquent models or dedicated Service classes. Controllers should primarily handle request validation and response formatting.
5. **Testing First:** When modifying business logic or checkout flows, provide the accompanying PHPUnit tests covering edge cases.
