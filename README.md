<h1 align="center">
  <br>
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel">
  <br><br>
  📚 Shelf-E — Online Bookstore
  <br>
</h1>

<p align="center">
  A full-featured online bookstore built with Laravel 12, Alpine.js, and Tailwind CSS 4.
  <br>
  Supports guest checkout, a loyalty coin wallet, vouchers, admin panel, and more.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind 4">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js 3">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
</p>

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Data Model](#data-model)
- [Route Map](#route-map)
- [Getting Started](#getting-started)
- [Development Commands](#development-commands)
- [Testing](#testing)
- [Admin Panel](#admin-panel)
- [Key Design Decisions](#key-design-decisions)

---

## Features

### Storefront
| Feature | Details |
|---|---|
| **Catalog & Search** | Browse by category, author, genre, price range, rating; live search with debounced AJAX dropdown |
| **Product Page** | Image magnifier, preview gallery, star ratings, approved-review display |
| **Shopping Cart** | Session-based; persists across pages; rate-limited (30 req/min) |
| **Checkout** | Works for guests and logged-in users; shipping address snapshot; Bkash & COD payment options |
| **Voucher System** | Percentage or fixed-amount discount codes; per-user usage limits; AJAX validation at checkout |
| **Coin Wallet** | Loyalty coins earned on orders; redeemable at checkout; append-only ledger via `CoinService` |
| **Wishlist** | Toggle-to-save books; accessible from account dashboard |
| **Reviews** | Customers submit reviews; displayed only after admin approval |
| **Newsletter** | One-click subscribe (auth required); rewards with a 15% discount voucher |
| **Authors Page** | Author profiles with photo, bio, and book listings |
| **Bestsellers Page** | Ranked by total units sold |
| **Deal Cards** | Live countdown timers for sale-priced books |
| **Rotating Quotes** | Auto-refreshing literary quote bar with a progress indicator |

### Admin Panel (`/admin/*`)
| Module | Capabilities |
|---|---|
| **Dashboard** | Revenue, order & user stats at a glance |
| **Books** | Create / edit / delete; image upload via Intervention Image; sale pricing; scraper integration |
| **Orders** | List, detail, status updates, printable invoice |
| **Authors** | CRUD with photo upload |
| **Categories** | Inline CRUD |
| **Reviews** | Approve / reject queue |
| **Vouchers** | Full CRUD; announce voucher shown in site-wide banner |
| **Coins** | Manual credit / debit per user |
| **Hero Images** | Upload slot-based hero banner images |
| **Featured Books** | Curate the homepage "Featured Books" carousel |
| **Settings** | Announcement text, shipping rates, Bkash number, FAQ, About Us |
| **Contacts** | Read and manage contact form submissions |

---

## Tech Stack

```
Backend          Laravel 12 (PHP 8.2+)
Frontend         Tailwind CSS 4 · Alpine.js 3 (@alpinejs/collapse)
Build Tool       Vite 7
Database         SQLite  (in-memory SQLite for tests)
Auth             Laravel Breeze (sessions) + Laravel Socialite (Google OAuth)
Image Processing Intervention Image 3
Testing          PHPUnit 11 · Mockery
Code Style       Laravel Pint
Dev DX           Laravel Pail (log tailing) · Concurrently
```

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                          Browser                                │
│   Blade + Alpine.js + Tailwind CSS (served via Vite)            │
└────────────────────────────┬────────────────────────────────────┘
                             │  HTTP
┌────────────────────────────▼────────────────────────────────────┐
│                       Laravel Router                            │
│  Public  │  Auth (Breeze/Socialite)  │  Admin (IsAdmin MW)      │
└───┬──────┴──────────────────┬────────┴──────────────────┬───────┘
    │                         │                           │
┌───▼──────────┐   ┌──────────▼──────────┐   ┌───────────▼───────┐
│  Controllers │   │     Controllers     │   │ Admin Controllers  │
│  (Catalog,   │   │  (Dashboard, Cart,  │   │ (Books, Orders,    │
│  Order,      │   │   Order, Profile,   │   │  Authors, Reviews, │
│  Contact…)   │   │   Wishlist, Coins…) │   │  Settings…)        │
└───┬──────────┘   └──────────┬──────────┘   └───────────┬───────┘
    │                         │                           │
┌───▼─────────────────────────▼───────────────────────────▼───────┐
│                    Eloquent Models + Services                    │
│                                                                  │
│  Product · Order · Voucher · CoinLedger · Review · Setting …    │
│                                                                  │
│  CoinService (ledger mutations)  ·  ReviewService               │
└─────────────────────────────────────────────────────────────────┘
                             │
               ┌─────────────▼─────────────┐
               │        SQLite DB           │
               └───────────────────────────┘
```

### Directory Layout

```
app/
├── Http/
│   ├── Controllers/          # Thin controllers — validate + delegate
│   │   └── Admin/            # Admin CRUD controllers
│   └── Middleware/
│       ├── IsAdmin.php        # Guards all /admin/* routes
│       └── PreventBackHistory.php
├── Models/                   # Business logic lives here
└── Services/
    ├── CoinService.php        # Only entry-point for coin ledger mutations
    └── ReviewService.php

resources/views/
├── layouts/                  # app.blade.php · admin.blade.php · guest.blade.php
├── components/               # Reusable Blade components (hero, deal-card, …)
├── admin/                    # Admin panel views
├── products/ · categories/   # Storefront catalog views
├── checkout/ · orders/       # Transactional views
└── pages/                    # Static-ish pages (FAQ, Contact, Newsletter)
```

---

## Data Model

```
┌─────────────┐       ┌──────────────┐      ┌───────────────┐
│   User      │──────▶│    Order     │─────▶│  OrderItem    │
│  (nullable  │       │  (shipping   │      │  (snapshot    │
│  for guest  │       │   snapshot)  │      │   of price)   │
│  checkout)  │       └──────────────┘      └───────────────┘
└──────┬──────┘
       │
       ├──────▶ Wishlist ──────▶ Product ◀──── Category
       │                            │
       ├──────▶ CoinLedger          ├──────▶ Author (many-to-many)
       │        (append-only)       │
       ├──────▶ VoucherUsage        ├──────▶ Review (approved_reviews)
       │                            │
       └──────▶ UserAddress         └──────▶ ProductPreview
                                             (image gallery)

┌─────────────┐    ┌────────────────┐    ┌──────────────────┐
│   Voucher   │    │    Setting     │    │   HeroSlide      │
│ (% or fixed │    │  (key-value    │    │  (featured book  │
│  discount)  │    │   store)       │    │   carousel)      │
└─────────────┘    └────────────────┘    └──────────────────┘
```

**Key model notes:**
- `Product` — carries `paperback_price`, `hardcover_price`, `sale_price`, and a computed `display_price`
- `Order` — `user_id` is nullable (guest checkout); stores a full shipping address snapshot
- `Voucher` — `isUsable()` and `calculateDiscount()` methods encapsulate all discount logic
- `CoinLedger` — append-only; `CoinService` is the **only** place that writes to it (DB transaction-wrapped)
- `Setting` — admin-configurable key-value pairs (shipping rates, FAQ, About Us, Bkash number, etc.)

---

## Route Map

```
GET  /                              Home (featured, top-rated, bestsellers, deals)
GET  /categories                    Catalog with filters (genre, author, price, rating)
GET  /product/{slug}                Product detail page
GET  /authors                       Authors listing
GET  /bestsellers                   Bestsellers ranked by sales
GET  /series                        Series listing
GET  /checkout                      Checkout (guest + auth)
POST /checkout                      Place order  [throttle: 5/min]

POST /cart/add/{id}                 Add to cart  [throttle: 30/min]
POST /cart/remove|increment|decrement/{id}

── Authenticated ──────────────────────────────────────────────────
GET  /dashboard                     Order history + account overview
GET  /wishlist                      Saved books
GET  /wallet                        Coin balance + ledger
GET  /addresses                     Saved shipping addresses
GET  /account/settings              Profile & preferences

── Admin (/admin/*) ───────────────────────────────────────────────
GET  /admin/dashboard
CRUD /admin/books
CRUD /admin/orders          (+ status patch + invoice PDF)
CRUD /admin/authors
CRUD /admin/categories
CRUD /admin/vouchers
CRUD /admin/reviews         (approve / reject)
     /admin/coins/{user}/adjust
     /admin/hero-images
     /admin/hero-books
     /admin/settings
     /admin/contacts

── API ────────────────────────────────────────────────────────────
GET  /api/search-books?q=           Live search  [throttle: 60/min]
GET  /api/voucher/validate?code=    Voucher check [throttle: 30/min]
GET  /random-quote                  Random literary quote (JSON)
```

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+ and npm

### One-command setup

```bash
git clone <repo-url> shelf-e
cd shelf-e
composer setup       # installs deps, copies .env, generates key, migrates, builds assets
```

### Manual setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite — no server needed)
touch database/database.sqlite
php artisan migrate --seed

# 4. Storage symlink
php artisan storage:link

# 5. Frontend
npm install
npm run build
```

### Google OAuth (optional)

Add to your `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## Development Commands

```bash
# Start everything concurrently (PHP server + queue + log tail + Vite HMR)
composer dev

# Build production assets
npm run build

# Watch frontend assets
npm run dev

# Format code (Laravel Pint)
./vendor/bin/pint

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset and reseed database
php artisan migrate:fresh --seed
```

> `composer dev` starts four processes in parallel using **concurrently**:
> `php artisan serve` · `queue:listen` · `pail` (log tail) · `vite dev`

---

## Testing

```bash
# Run full test suite
composer test
# or
php artisan test

# Run a single test file
php artisan test tests/Feature/CheckoutTest.php

# Run a specific test method
php artisan test --filter=test_method_name
```

**Test conventions:**
- All tests use an **in-memory SQLite** database — no setup required
- Queue, cache, and session drivers are set to `array` in the test environment
- `tests/Unit/Services/` — unit tests for `CoinService`, `ReviewService`, etc.
- `tests/Feature/` — integration tests covering checkout flows, cart, auth, and admin operations

---

## Admin Panel

Access the admin panel at `/admin/dashboard`. A user must have `role = 'admin'` in the `users` table (enforced by `IsAdmin` middleware).

```
Admin Panel Modules
│
├── Dashboard          Revenue charts, order & user counts
├── Books              Full CRUD + image upload + sale pricing + scraper
├── Orders             Status management + printable invoices
├── Authors            Profiles with photos
├── Categories         Inline add/edit/delete
├── Vouchers           Create codes, set type/value/expiry, announce site-wide
├── Reviews            Approve or reject customer reviews
├── Coins              Manually credit or debit user coin balances
├── Hero Images        Upload banner images for the hero section
├── Featured Books     Curate the homepage "Handpicked For You" carousel
├── Settings           Announcement text · Shipping rates · Bkash number
│                      FAQ content · About Us text
└── Contacts           Inbox for contact form submissions
```

### Caching

| Data | Cache TTL | Invalidation |
|---|---|---|
| Homepage sections | 5 minutes | Automatic expiry |
| Global nav categories | 1 hour | `php artisan cache:clear` |

---

## Key Design Decisions

**Fat Models, Skinny Controllers**
Business logic lives in Eloquent models or service classes. Controllers handle request validation and response formatting only.

**CoinService as the single ledger entry-point**
All coin credits and debits go through `CoinService`, which wraps every mutation in a DB transaction. This prevents double-spend and keeps the ledger append-only.

**Guest checkout**
`Order.user_id` is nullable. Shipping information is snapshotted into the order row at the time of purchase — not linked to a live user address — so historical orders are always accurate.

**Alpine.js over jQuery**
All frontend reactivity (modals, dropdowns, live search, cart counters, countdown timers, filter auto-submit) is handled with Alpine.js directives. No jQuery dependency.

**Rate limiting at the route layer**
Cart (30/min), checkout (5/min), search (60/min), and voucher validation (30/min) are throttled at the route level using `RateLimiter::for()`, keeping controllers clean.

**Voucher safety**
`Voucher::isUsable()` checks expiry, active status, and global usage limits. `hasBeenUsedByUser()` checks per-user usage. Both are enforced both server-side at checkout and via the AJAX validation endpoint.

---

## License & Ownership

**Copyright © 2025 Kazi Rafiul Kader. All rights reserved.**

This project and all of its source code, assets, and documentation are the exclusive property of Kazi Rafiul Kader. No part of this codebase may be copied, modified, distributed, sublicensed, or used in any form — in whole or in part — without the express written permission of the owner.

This is **not** an open-source project. Unauthorized use, reproduction, or distribution of any portion of this codebase is strictly prohibited.
