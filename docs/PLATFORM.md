# Platform layer — clients, packages, subscriptions, tokens, SMS allowance

This is the owner's side of the product. It answers one business flow:

```
create client → assign package (KodaFreight or KodaSMS) → subscribe → share token to unlock
   expired = locked → assign message allowance → sending stops when it is finished
   → assign sender name
```

Everything below is enforced in code, not just on screen.

## 1. Products and packages

| Product | Key | What a client gets |
|---|---|---|
| KodaFreight (cargo & freight) | `freight` | containers, customers, loads, invoices, cargo operations, storage, accounts |
| KodaSMS (bulk SMS) | `sms` | message, sender name, SMS contacts, SMS reports, own customers |
| KodaFreight + KodaSMS | `both` | everything above |

Module keys live in `app/Services/Platform/ProductCatalog.php`; what a package actually
contains is stored in `package_modules` and edited on **Products → Modules**.

A client has `clients.account_type` (`freight|sms|both`) and one row in `subscriptions`
with `product`, `package_id`, `start_date`, `expire_date`, `duration_in_month`,
`sms_quota`, `amount`.

## 2. The owner flow (menu: Clients / Subscriptions / Products / Access)

1. **Clients → Add client** (`list.clients` / `create.clients`) creates the client company.
2. **Clients & subscriptions** (`/platform/clients`) shows every client with product,
   package, expiry, status, SMS left and the actions: **Lock / Unlock**, **Renew**,
   **Token**.
3. **Subscriptions** (`/platform/subscriptions`) assigns a package: client, product,
   package, SMS included, start date, months, amount (blank = package price). It creates
   the subscription *and* a `subscription_invoices` row, and flips the client to active.
4. **Unlock tokens** (`/platform/tokens`) issues a token per subscription with the date it
   unlocks until, and revokes one if it was sent to the wrong person.
5. **Products → Modules** decides what each package includes (this is the switch that
   hides whole sections from a client).
6. **Platform settings** (`/platform/settings`) sets the grace period and whether the
   subscription is enforced for **every** client (grandfathering off).

## 3. Status and locking

`SubscriptionService::status()` returns `active`, `grace`, `expired`, `locked` or `none`.

* `expire_date` today or later → **active**
* expired but still inside the grace period → **grace** (still usable)
* expired after grace → **expired** and the client is redirected to the unlock screen
* locked by the owner → **locked**, same unlock screen
* no subscription on file → **none**: the client keeps working (grandfathered) unless
  *Enforce for every client* is switched on in Platform settings

A locked or expired client sees only **Manage Profile** and **Settings** plus the unlock
box; `/cargo`, `/storage`, `/accounts`, `/message` all answer **403** for them.

## 4. Unlock token

`subscription_tokens` holds `token_`, `expire_date`, `status` (1 active / 0 off),
`used_at`. A token works once, for the client it belongs to, and only before its own
expiry date. Redeeming it moves the subscription's `expire_date` forward (never
backwards), unlocks the client and marks the token used. Screen: `/subscription`.

## 5. SMS allowance

* Balance = `subscriptions.sms_quota` − the client's live message count.
* The client sees it on the dashboard, in the header badge and on **Message → SMS
  Balance** (`/sms-balance`).
* Sending is blocked when the balance is finished or the account is expired/locked —
  single, multiple and lead sends all pass through the same guard
  (`MessageController::guardSmsAllowance()`). The send is refused, nothing is deleted,
  and the client is told to ask the owner for more SMS.
* Freight, cargo, storage and accounting never depend on this: an SMS failure never
  blocks them.

## 6. Sender name

Assigned under **Owner → Sender Name → Assign Name** (per client, `clientactivenames`).
The SMS package includes the `sms_sender` module; a freight-only package does not see it.

## 7. Module enforcement

`EnsureModule` (`module:cargo`, `module:storage`, `module:accounts`, `module:sms_bulk`)
guards the routes themselves, so closing a menu item is not the only protection. The
sidebar filters by module **and** spatie permission; the owner bypasses both.

## 8. If an owner screen reports a missing column

`SQLSTATE[42703] ... column "amount" does not exist` (PostgreSQL) means the database was
built before the platform columns existed. Two things protect you now:

* the owner overview sums subscription value in PHP, so an empty table or a missing
  column renders as `0` instead of a 500;
* `2026_11_26_000001_repair_subscription_platform_columns` adds `product`, `sms_quota`,
  `sms_used`, `amount`, `locked_at` and `notes` to `subscriptions` one column at a time
  (PostgreSQL refuses several `ADD COLUMN`s in a single `ALTER`, which is why the batch
  migration could silently stop halfway).

Fix an affected database with:

```bash
php artisan migrate --force
php artisan tinker --execute="var_dump(App\Models\Subscription::platformColumnsReady());"
```

Check the other platform tables in the same breath:

```sql
select table_name, column_name from information_schema.columns
where table_name in ('subscriptions','subscription_tokens','platform_settings','platform_modules','package_modules')
order by table_name, ordinal_position;
```

## 9. Deploy

```bash
git pull
composer install
php artisan migrate            # fresh installs now build all 74 tables in one pass
php artisan db:seed --class=PermissionsSeeder
php artisan db:seed --class=PlatformSeeder
php artisan accounting:setup
php artisan storage:link
php artisan optimize:clear
```

Then, once: **Platform settings** → set the grace period, and **Products → Modules** →
confirm KodaFreight / KodaSMS include what you sell.

Want demo data to click through the flow? `php artisan db:seed --class=PlatformDemoSeeder`
creates a freight client, a bulk-SMS reseller and an expired client, and prints its unlock
token. Logins: `owner@fms.com`/`12345678`, `freight@demo.test`, `sms@demo.test`,
`expired@demo.test` (password `secret123`).

Tests: `php artisan test --filter="PlatformSubscriptionTest|CargoOperationsTest|AccountsModuleTest|SmsNotificationSwitchTest"`
(54 passed, 248 assertions).
