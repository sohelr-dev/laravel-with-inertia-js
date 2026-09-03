# Laravel + Inertia.js + Vue 3 — Retail / POS Management System

A Laravel 12 backend paired with Vue 3 through Inertia.js — no separate REST/JSON API layer, no separate SPA build. Controllers return page components directly and Inertia handles routing, data passing and navigation between them.

## Tech Stack

| Layer      | Technology                                   |
|------------|-----------------------------------------------|
| Backend    | Laravel 12 (PHP 8.2+)                         |
| Frontend   | Vue 3 (Composition API, `<script setup>`)     |
| Bridge     | Inertia.js v3 (`inertiajs/inertia-laravel` + `@inertiajs/vue3`) |
| UI         | Bootstrap 5                                   |
| Build tool | Vite (via `laravel-vite-plugin`)              |
| Database   | MySQL (default `.env`; SQLite also works)     |

## How This Project Connects Laravel + Inertia + Vue (From Scratch)

This is the exact sequence used to wire this repo up — follow it in order to reproduce the same connection in a brand-new project. Each step is a real commit in this repo's history if you want to see the literal diff (`git log --oneline --reverse | head -6`, then `git show <hash>`).

### 1. Create a fresh Laravel project

```bash
composer create-project laravel/laravel project-name
cd project-name
```

### 2. Install Inertia's server-side adapter

```bash
composer require inertiajs/inertia-laravel
```

### 3. Generate and register the Inertia middleware

```bash
php artisan inertia:middleware
```

This creates `app/Http/Middleware/HandleInertiaRequests.php` — it decides the root Blade view and the props shared with every page:

```php
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app'; // renders resources/views/app.blade.php

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'auth' => ['user' => $request->user()],
        ];
    }
}
```

Register it in the `web` middleware group, in `bootstrap/app.php`:

```php
use App\Http\Middleware\HandleInertiaRequests;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        HandleInertiaRequests::class,
    ]);
})
```

### 4. Install Vue and Inertia's client-side adapter

```bash
npm install vue @inertiajs/vue3 @vitejs/plugin-vue
```

### 5. Add the Vue plugin to Vite

In `vite.config.js`, add `@vitejs/plugin-vue` and point `laravel-vite-plugin`'s `input` only at the JS entry (no more separate CSS entry needed — the JS file imports its own CSS):

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: { base: null, includeAbsolute: false },
            },
        }),
    ],
});
```

### 6. Create the Inertia root view

Delete (or stop using) the default `resources/views/welcome.blade.php`, and create `resources/views/app.blade.php` — this is the *one* HTML shell every page loads into:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

### 7. Bootstrap the Vue app

Replace the contents of `resources/js/app.js`:

```js
import { createApp, h } from 'vue';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
```

### 8. Create your first page component

```bash
mkdir -p resources/js/Pages
```

`resources/js/Pages/Home.vue`:

```vue
<script setup>
defineProps({ message: String });
</script>

<template>
    <h1>{{ message }}</h1>
</template>
```

### 9. Render it from a route/controller — instead of `view()`, use `Inertia::render()`

```php
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', ['message' => 'Hello from Inertia + Vue']);
});
```

The first argument (`'Home'`) must match the file path under `resources/js/Pages/` (so `'Products/Index'` → `resources/js/Pages/Products/Index.vue`).

### 10. Run it

```bash
php artisan serve   # terminal 1
npm run dev          # terminal 2
```

Visit `http://127.0.0.1:8000` — that request hits the Laravel route, `Inertia::render()` returns the `Home` component with its props, `app.blade.php` loads the compiled `app.js`, and `createInertiaApp` mounts `Home.vue` into the page. Every later click on an Inertia `<Link>` re-fetches only the new page's data as JSON and swaps the component — no full reload.

### Optional: swap the default Tailwind for Bootstrap (as this project does)

```bash
npm uninstall tailwindcss @tailwindcss/vite
npm install bootstrap
```

Remove the `tailwindcss()` plugin from `vite.config.js`, then in `resources/js/app.js`:

```js
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';
```

### How it behaves day-to-day (once wired up)

1. **Controllers render pages, not Blade views.** `Inertia::render('Products/Index', [...props])` instead of `view(...)`.
2. **Pages resolve by name.** The string passed to `Inertia::render()` must exactly match a file under `resources/js/Pages/`.
3. **Shared data on every request.** `HandleInertiaRequests::share()` — the logged-in user, app name, flash messages (`flash.success`, `flash.receipt`) are available as props on every page automatically.
4. **Layouts wrap pages.** `resources/js/Layouts/AppLayout.vue` (public site) and `AdminLayout.vue` (authenticated sidebar shell) — a page imports the layout it needs and wraps its content in it.
5. **Navigation without full reloads.** Use `<Link href="...">` instead of `<a>`, and `router.get/post(...)` for programmatic navigation — normal URLs and browser history still work.
6. **Forms via `useForm()`.** Two-way-bound fields, `form.processing`, CSRF handled automatically, `form.errors.<field>` populated straight from Laravel validation — no manual axios wiring for standard forms.
7. **Vite compiles everything.** `npm run dev` for hot-reload during development; `npm run build` outputs hashed production assets to `public/build/`.

## Requirements

- PHP >= 8.2 with the extensions Laravel needs (`pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, ...)
- Composer
- Node.js >= 18 and npm
- MySQL (or switch `DB_CONNECTION` in `.env` to `sqlite`)

## First-Time Setup

**Option A — one command** (installs dependencies, creates `.env`, generates the app key, runs migrations, builds frontend assets):

```bash
composer setup
```

**Option B — manual, step by step:**

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database connection (defaults to MySQL in this project):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
php artisan migrate
npm install
```

## Running the App

**Option A — one command** (runs the PHP server, queue listener, log tailer and Vite dev server together):

```bash
composer dev
```

**Option B — separate terminals:**

```bash
php artisan serve   # backend — http://127.0.0.1:8000
npm run dev          # Vite dev server with hot reload
```

Visit `http://127.0.0.1:8000`.

## Production Build

```bash
npm run build
```

Compiles and hashes frontend assets into `public/build/` (no Vite dev server needed at runtime after this).

## Useful Commands

```bash
php artisan migrate              # run pending migrations
php artisan migrate:status       # see which migrations have run
php artisan tinker               # interactive PHP shell against the app
php artisan route:list           # list all registered routes
composer test                    # run the PHPUnit test suite
```

## What's Been Built So Far

### Authentication
- Register, login, logout — session-based, using Laravel's built-in `Auth` facade (`app/Http/Controllers/AuthController.php`)
- Route groups split by `guest` middleware (register/login) and `auth` middleware (everything else) in `routes/web.php`

### Dashboard (`/dashboard`)
- `DashboardController` computes live stats on every load: active product count, low-stock count (stock ≤ 10), today's sale count, today's revenue
- Recent sales list (last 5, with cashier name and item count) and a low-stock alert list (bottom 5 by stock)

### Products (`/products`, `/products/create`)
- `products` table: name, sku, barcode, category, description, price, cost, stock, unit, is_active
- `ProductController`: searchable + paginated list (`index`), create form (`create`), validated store (`store`)
- Stock badge coloring on the list (red = out of stock, amber = low stock, neutral = healthy)

### Point of Sale — POS (`/pos`)
- `sales` and `sale_items` tables — each sale snapshots product name/price at time of sale so later price changes don't rewrite history
- `PosController@index` loads active products, categories, and the customer list for the picker
- `PosController@store` runs the whole checkout in one DB transaction: row-locks the products (and customer, if any) to prevent overselling under concurrent sales, checks stock per line, computes subtotal/discount/total, decrements stock, generates an invoice number, and creates the `Sale` + `SaleItem` records
- POS UI: product search + category filter, tap-to-add cart, quantity +/-, discount, payment method (cash/card/mobile), amount paid with live change calculation, and a post-sale receipt banner

### Customers & Due/Credit Tracking (`/customers`, `/customers/{id}`)
- `customers` table (name, phone, email, address, running `total_due` balance) and `customer_payments` table (payment history against that due)
- `sales.customer_id` + `sales.due_amount` — a sale can optionally be attached to a customer
- Business rule enforced server-side in `PosController@store`: a walk-in sale (no customer selected) must always be paid in full; a sale with a customer attached can be underpaid, and the shortfall is added to that customer's `total_due`
- `CustomerController`: searchable + paginated list (`index`), create (`create`/`store`), detail page (`show` — due balance, sales history, payment history), and `storePayment` to record a payment against the due balance (rejects payments larger than the outstanding due)
- POS customer picker (search by name/phone, shows existing due before attaching) and a "Record Payment" form on the customer detail page

### UI / Design
- `AdminLayout.vue` — sidebar + topbar app shell (Bangladesh flag green/red theme) used by Dashboard, POS, Products and Customers
- `AppLayout.vue` — redesigned public-site navbar and footer (used by Home/About)
- `Home.vue` — landing page with a hero section, a 4-feature grid, and a call-to-action band
- `resources/css/app.css` — the shared design system: stat cards, POS terminal styling, category pills, hero/feature/CTA sections
