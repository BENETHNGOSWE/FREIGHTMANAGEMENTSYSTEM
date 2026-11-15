# Cargo operations, storage and release control

Everything below is scoped to the logged-in user's **client** (`client_id`); nothing leaks between clients.
If any part of this add-on fails, the freight screens keep working — the accounting posts are wrapped so a
failure is logged, never thrown.

## Where it lives

| Menu (sidebar) | Page | Path |
|---|---|---|
| Cargo Operations → Cargo List | every cargo with status, China location, batch, storage owed | `/cargo` |
| Cargo Operations → Warehouse Verification | confirm weight / damage / missing / wrong item | `/verification` |
| Cargo Operations → Release Control | the release checklist and pickup notes | `/release-control` |
| Cargo Operations → Merge Payment | one payment for several invoices | `/merge-payment` |
| **Storage** → Cargo in Storage | storage per cargo, balances, "Add today's charges" | `/warehouse/storage` |
| Storage → Storage Payments | money received for storage | `/warehouse/storage/payments` |
| Storage → Storage Settings | default free days + daily fee, per-customer fee | `/warehouse/storage/settings` |
| Public (no login) | customer tracking by number or QR | `/track` |
| Public (no login) | warehouse scans the pickup note | `/pickup/{token}` |

> Storage uses `/warehouse/storage` **not** `/storage`, because `public/storage` is the upload symlink and
> the web server answers that path before Laravel ever sees it.

## 1. Storage, automatic and per day

* **Settings** (`storage_settings`, one row per client): `free_days`, `daily_rate`, `auto_accrue`.
* **Per-customer fee** (`customer_storage_rates`): when a customer has an agreement, their `daily_rate` /
  `free_days` win over the default. "Customer A pays 500" is just a customer rate of 500; everybody else
  falls back to the default.
* **Per cargo ledger** (`cargo_storages`): start date, the free days and rate copied at the moment the
  storage started (so later setting changes do not rewrite history), status `active|collected|waived`.
* **Daily charges** (`storage_charges`): one row per cargo per day, protected by a unique index on
  `(loadpackage_id, charge_date)` and written with `firstOrCreate` — running the job twice on the same day
  adds **nothing**.
* **Balance** = charged − waived − paid, never below zero.
* **When does it start?** When the cargo becomes `Arrived` (arrival date), or manually with
  "Start tracking cargo that is already in the warehouse". It stops when the cargo is collected.
* **The job**: `php artisan storage:accrue` (also `--date=YYYY-MM-DD`, `--client=ID`) runs daily at 00:05,
  scheduled in `app/Console/Kernel.php`. On a server you must have the scheduler running:
  `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.

### Accounting

`4400 Storage Income` is seeded by `php artisan accounting:setup`.
Daily accrual → **Dr 1200 Accounts Receivable / Cr 4400 Storage Income** (one journal per day, reference
`StorageAccrual:YYYYMMDD`). Storage payment → Dr cash/bank/mobile money / Cr 1200. Waiver → Dr 4400 / Cr 1200.

## 2. Cargo registration: photos, China location, batch

`loadpackages` gained `china_location`, `batch_no`, `qr_token`, `cargo_status`, `arrived_at`,
`original_weight_value`, `verified_weight_value`, `verified_at`, `has_damage`, `has_missing`,
`has_wrong_item`, `verification_notes`, `weight_price_impact`, `released_at`, `collected_at`, `released_by`.

On the cargo page you can add several photos at once (`cargo_photos`, stored on the `public` disk — run
`php artisan storage:link` once), plus edit China location and batch. The batch is what the timeline follows
when cargo is put together in a container / shipment.

## 3. QR code, public tracking, timeline

* `qr_token` is created once per cargo (`QrCodeService`, bacon/bacon-qr-code, inline SVG → data URI).
* `/track` — the customer types the tracking number.
* `/track/{token}` — that is what the QR points at. No money, no internal notes: status, weight, customer,
  and the journey steps (Received → In Transit → Arrived → Verified → Invoiced → Paid → Ready for Pickup →
  Collected). `Invoiced`, `Paid`, `Ready for Pickup` and `Collected` are also proven by the data itself
  (invoice exists / balance cleared / released_at / collected_at), so the timeline stays true even if
  nobody changed the status by hand.
* Status changes from the cargo page are written to `cargo_status_histories` with who and when.

## 4. Mark as arrived → verification

`/verification` lists everything that arrived and is not verified yet. The Dar warehouse confirms:

* actual weight — if it differs, the invoice is re-priced (`price × fx × total verified weight` when the
  invoice has a CBM/KG price, otherwise the difference is applied to the load price) and
  `weight_price_impact` records the money effect. **The China weight is kept** in `original_weight_value`.
* damaged / missing / wrong item flags and a free-text note, all written into the history.

## 5. Release control (storage is now part of the check)

A cargo can only be released when **all** of these pass — and the storage one is new:

1. invoice fully paid,
2. storage cleared (balance 0),
3. a valid (issued, not cancelled, not used) pickup note,
4. status allows release (verified / invoiced / paid / ready),
5. verified in Dar,
6. physically present (not yet collected).

The release desk shows each check as ✓ / ✕ with the reason, filters for *Ready*, *Blocked*, *Storage owing*,
and turns the cargo into `Ready for Pickup`; **Collected** then stops the storage clock and marks the note used.

## 6. Merge payment, 1 KG minimum on the combined weight

`/merge-payment` replaces the old "merge selected invoices → one PDF" flow:

1. pick a customer, tick the cargo,
2. **preview**: combined weight, billable weight, old/new total per invoice, re-pricing difference,
   balance after the merge,
3. if the combined weight of KG cargo is below **1 KG**, the billable weight is raised to 1 KG and the
   invoices are re-priced on that,
4. one payment is recorded and split over the invoices oldest-first (`merged_payments` +
   `merged_payment_invoices`), which also creates the normal `invoice_payments` rows — so the existing
   invoice screens, balances and accounting keep working untouched.

## Permissions (added to `PermissionsSeeder`)

`verify cargo`, `manage storage`, `manage storage settings`, `release cargo`, `merge payments`, plus
`cargo_operations`, `cargo_storage`, `pickup_notes` list/view/create/update. Roles updated: manager,
godownmanager (warehouse desk), Cashier (storage money + merged payments); admin/owner get everything.
The new pages render for any signed-in user and hide actions the role cannot perform, so no permission
mistake can lock somebody out of the freight screens.

## Install / update

```bash
git pull
composer install                     # bacon/bacon-qr-code for the QR codes
php artisan migrate
php artisan db:seed --class=PermissionsSeeder
php artisan accounting:setup         # adds 4400 Storage Income (safe to re-run)
php artisan storage:link             # cargo photos
php artisan optimize:clear
```

Server cron (automatic storage):

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Tests

```bash
php artisan test --filter="CargoOperationsTest|AccountsModuleTest|SmsNotificationSwitchTest"
# 34 passed / 141 assertions
```

`tests/Feature/CargoOperationsTest.php` covers: free period then daily charge, no duplicate on a second run,
per-customer rate overriding the default, payment + waiver clearing the balance, storage stopping on
collection, the daily command, weight re-pricing with the China weight kept, damage flags, the full release
checklist (blocked by unpaid invoice, by storage, by cancelled pickup note; released and collected after),
the 1 KG minimum on the combined weight, a merged payment settling several invoices, public tracking
(no money shown), the cargo pages, photo/details saving, status history, and tenant isolation.
