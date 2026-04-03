# Shelf-e v2.0 — Implementation Plan

**Architect:** Lead AI  
**Date:** 2026-04-03  
**Stack:** Laravel 12, Tailwind CSS 4, Alpine.js 3, SQLite, PHPUnit

---

## Feature Index

| ID | Feature               | Description                                              |
|----|-----------------------|----------------------------------------------------------|
| F1 | Coin System           | Virtual wallet: earn, spend, and redeem coins at checkout |
| F2 | Review System         | Admin-moderated reviews with "Verified Purchase" badge   |
| F3 | Peek Inside           | Admin-uploaded image/video previews per product          |
| F4 | Hover Zoom            | Alpine.js magnifier on product card book covers          |
| F5 | Book Scraper          | Admin UI to fetch book data from Open Library API        |
| F6 | Author Management     | Full Admin CRUD for authors, linked to products          |
| F7 | Admin Auto-Search     | Alpine.js live-search for assigning authors to books     |

---

## Phase 1 — Database Migrations

> One file per migration. No model or controller work here.

### F6 — Author Management
- **1.1** `database/migrations/..._create_authors_table.php`
  - Columns: `id`, `name`, `slug` (unique), `bio` (nullable text), `photo_path` (nullable string), `timestamps`

- **1.2** `database/migrations/..._create_author_product_table.php`
  - Pivot: `author_id` (FK → authors), `product_id` (FK → products). No timestamps.

### F1 — Coin System
- **1.3** `database/migrations/..._add_coin_balance_to_users_table.php`
  - `ALTER TABLE users ADD coin_balance UNSIGNED INTEGER DEFAULT 0`

- **1.4** `database/migrations/..._create_coin_ledger_table.php`
  - Columns: `id`, `user_id` (FK → users, cascade delete), `type` (enum: `credit`/`debit`), `amount` (unsigned integer), `description` (string), `balance_after` (unsigned integer), `timestamps`

### F2 — Review System
- **1.5** `database/migrations/..._create_reviews_table.php`
  - Columns: `id`, `user_id` (FK → users, set null on delete, nullable), `product_id` (FK → products, cascade delete), `rating` (tinyint 1–5), `title` (nullable string), `body` (text), `status` (enum: `pending`/`approved`/`rejected`, default `pending`), `is_verified_purchase` (boolean, default false), `timestamps`

### F3 — Peek Inside
- **1.6** `database/migrations/..._create_product_previews_table.php`
  - Columns: `id`, `product_id` (FK → products, cascade delete), `type` (enum: `image`/`video`), `path` (string), `sort_order` (unsigned tinyint, default 0), `timestamps`

---

## Phase 2 — Eloquent Models

> One task per model file. Add `HasFactory`, `$fillable`, casts, and relationships only. Zero business logic.

### F6
- **2.1** `app/Models/Author.php`
  - `HasFactory`, fillable: `[name, slug, bio, photo_path]`
  - Relationship: `products()` → `BelongsToMany(Product::class)`

- **2.2** Update `app/Models/Product.php`
  - Add relationship: `authors()` → `BelongsToMany(Author::class)`

### F1
- **2.3** `app/Models/CoinLedger.php`
  - `HasFactory`, fillable: `[user_id, type, amount, description, balance_after]`
  - Relationship: `user()` → `BelongsTo(User::class)`

- **2.4** Update `app/Models/User.php`
  - Add `coin_balance` to `$fillable` and cast to `integer`
  - Add relationship: `coinLedger()` → `HasMany(CoinLedger::class)`

### F2
- **2.5** `app/Models/Review.php`
  - `HasFactory`, fillable: `[user_id, product_id, rating, title, body, status, is_verified_purchase]`
  - Cast `is_verified_purchase` → boolean, `rating` → integer
  - Scope: `scopeApproved($query)` → `where('status', 'approved')`
  - Relationships: `user()` → `BelongsTo`, `product()` → `BelongsTo`

- **2.6** Update `app/Models/User.php`
  - Add relationship: `reviews()` → `HasMany(Review::class)`

- **2.7** Update `app/Models/Product.php`
  - Add relationship: `reviews()` → `HasMany(Review::class)`
  - Add relationship: `approvedReviews()` → `HasMany(Review::class)->approved()`

### F3
- **2.8** `app/Models/ProductPreview.php`
  - `HasFactory`, fillable: `[product_id, type, path, sort_order]`
  - Cast `sort_order` → integer
  - Relationship: `product()` → `BelongsTo(Product::class)`

- **2.9** Update `app/Models/Product.php`
  - Add relationship: `previews()` → `HasMany(ProductPreview::class)->orderBy('sort_order')`

---

## Phase 3 — Service Classes

> Pure PHP classes. No HTTP layer. All business logic lives here per Rule #4.

### F1
- **3.1** `app/Services/CoinService.php`
  - `credit(User $user, int $amount, string $description): CoinLedger`
    - Wraps in `DB::transaction`: increments `user.coin_balance`, inserts ledger row.
  - `debit(User $user, int $amount, string $description): CoinLedger`
    - Throws `InsufficientCoinsException` if balance < amount.
    - Wraps in `DB::transaction`: decrements balance, inserts ledger row.
  - `getBalance(User $user): int`

- **3.2** `app/Exceptions/InsufficientCoinsException.php`
  - Simple domain exception extending `\RuntimeException`.

### F2
- **3.3** `app/Services/ReviewService.php`
  - `isVerifiedPurchase(User $user, Product $product): bool`
    - Checks if an `Order` exists for `$user` containing `$product->id` with `status = completed`.
  - `approve(Review $review): void` — sets `status = approved`.
  - `reject(Review $review): void` — sets `status = rejected`.

### F5
- **3.4** `app/Services/BookScraperService.php`
  - `searchByTitle(string $title): array` — HTTP GET to Open Library search API, returns normalized array of `[title, author, isbn, cover_url, description, published_year]`.
  - `fetchByISBN(string $isbn): ?array` — fetches a single book by ISBN from Open Library, same normalized shape.
  - Uses Laravel `Http` facade. Throws `\Exception` on non-200 responses.

---

## Phase 4 — Factories

> One factory file per model. Follow existing conventions (`UserFactory`, `ProductFactory`).

- **4.1** `database/factories/AuthorFactory.php`
- **4.2** `database/factories/CoinLedgerFactory.php`
- **4.3** `database/factories/ReviewFactory.php`
- **4.4** `database/factories/ProductPreviewFactory.php`

---

## Phase 5 — Admin Controllers & Routes

> Skinny controllers — validate input, call service/model, return response.

### F6 — Author Management
- **5.1** `app/Http/Controllers/Admin/AuthorController.php`
  - Resource controller: `index`, `create`, `store`, `edit`, `update`, `destroy`
  - `store`/`update`: handle optional `photo_path` file upload to `public/authors`

### F2 — Review Moderation
- **5.2** `app/Http/Controllers/Admin/ReviewController.php`
  - `index`: paginated list filtered by `status` (default `pending`)
  - `approve(Review $review)`: delegates to `ReviewService::approve()`, redirects back
  - `reject(Review $review)`: delegates to `ReviewService::reject()`, redirects back

### F1 — Coin Management
- **5.3** `app/Http/Controllers/Admin/CoinController.php`
  - `index`: paginated user list with `coin_balance`
  - `adjust(Request $request, User $user)`: POST — validate `type` (credit/debit), `amount`, `description`; call `CoinService`

### F3 — Peek Inside Uploads
- **5.4** `app/Http/Controllers/Admin/ProductPreviewController.php`
  - `store(Request $request, Product $product)`: validate file (image or video), store to `public/previews`, create `ProductPreview` record
  - `destroy(ProductPreview $preview)`: delete file from disk, delete record

### F5 — Book Scraper
- **5.5** `app/Http/Controllers/Admin/BookScraperController.php`
  - `index`: return scraper search view
  - `search(Request $request)`: POST — validate `query`, call `BookScraperService::searchByTitle()`, return JSON results
  - `import(Request $request)`: POST — validate scraped fields, create or update `Product` record, redirect to edit page

### Routes
- **5.6** Update `routes/web.php`
  - Add `Route::resource('admin/authors', Admin\AuthorController::class)` under admin middleware
  - Add `Route::resource('admin/reviews', Admin\ReviewController::class, ['only' => ['index']])` + explicit approve/reject PUT routes
  - Add `Route::get/post('admin/coins', ...)` routes for coin management
  - Add `Route::post('admin/products/{product}/previews', ...)` and `Route::delete('admin/previews/{preview}', ...)` for peek inside
  - Add `Route::get/post('admin/scraper', ...)` routes for book scraper
  - Add author search JSON endpoint: `Route::get('admin/authors/search', [Admin\AuthorController::class, 'search'])` (used by F7 Alpine component)

---

## Phase 6 — Frontend Controllers & Routes

- **6.1** `app/Http/Controllers/ReviewController.php`
  - `store(Request $request, Product $product)`: validate `rating`, `title`, `body`; call `ReviewService::isVerifiedPurchase()`; create `Review` with `status = pending`; redirect back with flash

- **6.2** `app/Http/Controllers/CoinController.php`
  - `index`: return wallet view with paginated `coinLedger` for `Auth::user()`

- **6.3** Update `app/Http/Controllers/CheckoutController.php` (or equivalent)
  - Add coin redemption logic: if `redeem_coins = true` in request, call `CoinService::debit()` and subtract from `total_amount`

- **6.4** Update `routes/web.php` (auth-protected)
  - `POST /products/{product}/reviews` → `ReviewController@store`
  - `GET /wallet` → `CoinController@index`

---

## Phase 7 — Admin Views (Blade + Alpine.js)

> One task per distinct view file. All interactivity via Alpine.js. All styling via Tailwind 4.

### F6 — Author Management
- **7.1** `resources/views/admin/authors/index.blade.php`
  - Table: name, photo thumbnail, action links (edit/delete). Delete uses an Alpine `x-data` confirm dialog, no JS alerts.

- **7.2** `resources/views/admin/authors/create.blade.php`
  - Form: name, bio (textarea), photo upload input. Reuses `@include('admin.partials._form-errors')`.

- **7.3** `resources/views/admin/authors/edit.blade.php`
  - Same form as create, pre-filled. Shows current photo thumbnail.

### F2 — Review Moderation
- **7.4** `resources/views/admin/reviews/index.blade.php`
  - Status filter tabs (Pending / Approved / Rejected) using Alpine `x-data` to switch active tab without page reload.
  - Each row: rating stars, review body, verified badge, approve/reject buttons (POST forms).

### F1 — Coin Management
- **7.5** `resources/views/admin/coins/index.blade.php`
  - Table of users with `coin_balance`. Each row has an inline "Adjust" form (Alpine `x-show` toggle) with `type` select, `amount`, `description`.

### F3 — Peek Inside (Admin side)
- **7.6** `resources/views/admin/products/partials/_preview-upload.blade.php`
  - File input (accepts image/*, video/*). Lists existing previews with thumbnail and a delete button (DELETE form). `@include`d from the product edit view.

- **7.7** Update `resources/views/admin/products/edit.blade.php`
  - `@include` the `_preview-upload` partial in a new "Peek Inside" section.

### F7 — Admin Auto-Search (Author Assignment)
- **7.8** Update `resources/views/admin/products/create.blade.php` and `edit.blade.php`
  - Add Alpine.js `x-data` author search component:
    - Text input triggers `fetch('/admin/authors/search?q=...')` on `@input` (debounced 300ms)
    - Dropdown shows results; selecting one adds the author to a local array
    - Hidden `<input type="hidden" name="author_ids[]">` for each selected author
    - Selected authors shown as removable chips/tags

### F5 — Book Scraper
- **7.9** `resources/views/admin/books/scraper.blade.php`
  - Alpine `x-data` search component:
    - Input + Search button → `fetch('/admin/scraper/search')` → renders results table
    - Each result row has an "Import" button that POSTs to `/admin/scraper/import`
    - Loading spinner while fetching

---

## Phase 8 — Frontend Views & Alpine.js Components

### F2 — Reviews (User-facing)
- **8.1** Update `resources/views/products/show.blade.php`
  - Add "Customer Reviews" section below product details
  - If user is authenticated: show "Write a Review" form (collapsible with Alpine `x-show`)
  - Display approved reviews with star ratings and "Verified Purchase" badge

### F1 — Coin Wallet (User-facing)
- **8.2** `resources/views/coins/index.blade.php`
  - Balance card at top. Paginated transaction history table (date, type, description, amount, running balance).

- **8.3** Update checkout view (find existing blade file)
  - Add a "Redeem Coins" toggle using Alpine `x-data`:
    - Shows available balance
    - Toggle applies discount and updates displayed total reactively (client-side preview)
    - Adds hidden `redeem_coins` input when toggled on

### F3 — Peek Inside (Frontend)
- **8.4** Update `resources/views/products/show.blade.php`
  - Add "Peek Inside" button (visible only if `$product->previews->isNotEmpty()`)
  - Alpine `x-data` lightbox/modal: clicking button opens overlay, cycles through image/video previews

### F4 — Hover Zoom
- **8.5** Update the product card partial (find file containing book cover `<img>`)
  - Wrap cover image in an Alpine `x-data="{ zoomed: false }"` container
  - On `@mouseenter` set `zoomed = true`; on `@mouseleave` set `zoomed = false`
  - Apply Tailwind `transition-transform duration-300` + `:class="{ 'scale-125 z-10': zoomed }"` to the image
  - Parent container needs `overflow-hidden relative`; no custom CSS required

---

## Phase 9 — PHPUnit Tests

> Per Rule #5: tests required for all service/business logic and checkout-adjacent changes.

- **9.1** `tests/Unit/Services/CoinServiceTest.php`
  - `test_credit_increases_balance_and_creates_ledger_entry`
  - `test_debit_decreases_balance_and_creates_ledger_entry`
  - `test_debit_throws_exception_when_balance_insufficient`
  - `test_debit_is_atomic_on_failure` (DB::transaction rollback)

- **9.2** `tests/Unit/Services/ReviewServiceTest.php`
  - `test_is_verified_purchase_returns_true_when_completed_order_exists`
  - `test_is_verified_purchase_returns_false_for_pending_order`
  - `test_approve_sets_status_to_approved`
  - `test_reject_sets_status_to_rejected`

- **9.3** `tests/Unit/Services/BookScraperServiceTest.php`
  - Uses `Http::fake()` to mock Open Library responses
  - `test_search_by_title_returns_normalized_array`
  - `test_fetch_by_isbn_returns_null_on_404`
  - `test_throws_exception_on_server_error`

- **9.4** `tests/Feature/Admin/AuthorControllerTest.php`
  - `test_admin_can_list_authors`
  - `test_admin_can_create_author`
  - `test_admin_can_update_author`
  - `test_admin_can_delete_author`
  - `test_guest_cannot_access_author_routes` (403/redirect)

- **9.5** `tests/Feature/Admin/ReviewControllerTest.php`
  - `test_admin_can_view_pending_reviews`
  - `test_admin_can_approve_review`
  - `test_admin_can_reject_review`

- **9.6** `tests/Feature/CheckoutCoinRedemptionTest.php`
  - `test_coins_are_debited_on_checkout_when_redemption_enabled`
  - `test_total_is_reduced_by_coin_value_on_redemption`
  - `test_coins_are_not_debited_when_redemption_disabled`

---

## Dependency Graph

```
F6 (Author Mgmt)  ──────────────────────────► F7 (Auto-Search)
                                                    │
F1 (Coin System)  ──► CoinService ──► Checkout ◄───┘
                                          ▲
F2 (Review System) ──► ReviewService ─────┘

F5 (Book Scraper) ── independent (admin-only, no frontend deps)
F3 (Peek Inside)  ── independent per product
F4 (Hover Zoom)   ── independent, pure frontend
```

## Implementation Order (Recommended)

1. Phase 1 → Phase 2 (schema first, models second)
2. Phase 4 (factories — needed by tests)
3. Phase 3 (services — testable in isolation)
4. Phase 9 (write tests before controllers per Rule #5)
5. Phase 5 → Phase 6 (admin then frontend controllers)
6. Phase 7 → Phase 8 (admin then frontend views)

> **Rule reminder:** Each prompt should target exactly one task from this list (e.g., "Implement task 5.1 — AuthorController"). Do not combine tasks.
