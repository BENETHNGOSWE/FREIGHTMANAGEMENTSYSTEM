# Client spec vs the system — coverage report

Source: `cargo_management_system_how_it_works.md` (China → Tanzania → Warehouse → Payment → Pickup).
Checked against the code in this repository on 2026-11-15. "Covered" means the screens,
data and the automatic rules exist; "Partial" means the data is there but a rule is missing;
"Missing" means nothing exists yet.

## Summary

| # | Requirement | Status | Where it lives / what is missing |
|---|---|---|---|
| 1 | Cargo registration: customer, tracking no, product, weight, package count, photos, China location | **Partial** | `loadpackages` (load_no, load_name, load_weight_value, loadpackagetype/unit type, price, dates) + `LoadpackageController`. **Missing: photos per cargo, explicit China location/batch field.** |
| 1 | Cargo QR code per package | **Missing** | No QR library, no QR field. Tracking number exists, QR does not. |
| 1 | Public tracking by number, customer sees only their own cargo | **Partial** | `loadtrackingpage` / `loadtracking` exist but are **behind `auth` + `multiple` middleware** — the customer cannot use them without a login. Needs a public route plus a status timeline. |
| 1 | Status timeline: Received → In Transit → Arrived → Verified → Invoice → Paid → Ready → Collected | **Partial** | Status exists only on the **container** (`Warehouse, OnVessel, Arrived_TZ-Port, ICD, Warehouse-HQ`) and on the **invoice** (Pending / Partial / Full). There is no per-cargo status field or history table. |
| 2 | China warehouse department: receives, photographs, registers, assigns to batch; no pricing | **Partial** | Roles exist (`chiefoperation`, `godownmanager`, `Cashier`, `manager`, `salesperson`, `receptionist`, `owner`, `admin`) but **no China-warehouse role**, and photos are not captured on a cargo. |
| 3 | Shipment / batch grouping before departure | **Partial** | `containers` + `container_loadings` group cargo and carry a voyage status; a container is the batch. No separate "shipment" entity with ETD/ETA/agent. |
| 4 | Mark as Arrived → verification of weight, packages, condition, damage, missing, wrong item | **Missing** | Only the container can be marked `Arrived_TZ-Port`. There is no verification screen, no condition/damage/missing flags. |
| 4 | Weight changed by Dar warehouse → price recalculated automatically, old weight kept as history | **Partial** | `LoadpackageController::update()` recalculates the invoice line and adjusts `total_amount` / `total_balance` by the price difference. **Missing: keeping the original weight, a "KG changed — price affected" record, and re-pricing of an already fully-paid invoice.** |
| 5 | Finance confirms final price, invoice reflects weight × rate | **Covered** | Invoice with `cbm_price`, `exchange_rate`, `total_amount`, `total_transportfee`; the accounts module now posts it to the ledger. |
| 5 | Changed amount flows to Invoice → Amount Due → Payment → Balance → Ledger → Reports | **Partial** | Invoice/balance/payment flow does; the **ledger re-prices in place** (covered by `LedgerService::upsertReference`). Missing: a report that shows *why* the amount changed. |
| 6 | Payment: amount due, paid, method, account, reference, balance, over/under payment | **Partial** | `invoice_payments` (amount, date, method CASH/BANK/MOBILE_MONEY/CHEQUE/OTHER, reference, received by, notes) + balance checks that refuse over-payment. **Missing: explicit "account" per payment and an under-payment/over-payment flag.** |
| 7 | Pickup note with its own QR, separate from the cargo QR | **Partial** | `derivery_orders` exist (driver name, number, vehicle, linked invoice, printable) but there is **no pickup-note document and no QR**. |
| 8 | Automatic daily storage after free period, accumulating without duplicates | **Missing** | Nothing exists. Needs a daily job, a storage ledger per cargo, and a free-days/storage-rate setting. |
| 9 | Paid but not collected → storage outstanding, still blocked | **Missing** | Depends on #8. |
| 10 | Warehouse release control: checks payment, storage, pickup note, status; storage blocks release | **Partial** | Release order is refused unless the invoice is **fully paid** (`InvoiceController::generatereleasePDF`). **Missing: storage and pickup-note checks.** |
| 11 | Final pickup: scan pickup QR + cargo QR → Collected, storage stops | **Missing** | No scanning, no `collected_at`, no final-pickup screen. |
| 12 | Investigations: missing, damaged, wrong item/batch, short-landed, customs/TRA hold, resolution history | **Missing** | No investigation entity. |
| 13 | Merge payment across several cargo, 1 KG minimum applied to the **combined** weight | **Partial** | "Merge Selected Invoices" produces one PDF of several invoices (originals stay separate). **Missing: a single payment that settles several invoices, and the 1 KG minimum rule.** |
| 14 | Loan / personal funding: amount, expenses paid, repayments, balance | **Missing** | Only a "Loans" ledger account exists now (chart of accounts); no loan records. |
| 15 | Mobile money channels combined into one accounting account (e.g. LIPA) | **Partial** | Payments carry `MOBILE_MONEY` and the accounts module maps them to one "Mobile Money" account. **Missing: per-channel instructions (M-Pesa / Tigo / Airtel) kept while posting to the combined account.** |
| — | Departments: China Warehouse, Dar Warehouse, Finance, Support, Manager, Admin | **Partial** | Dar warehouse, finance (cashier), manager, admin exist. **Missing: China Warehouse and Customer Support roles.** |

**Score: 3 covered, 9 partial, 6 missing** of the 18 requirements listed above.
The core money path (cargo → invoice → payment → release order → ledger → reports) is in
place; the warehouse-physical path (QR, verification, storage, pickup, investigations) is not.

## What is worth building next, in this order

1. **Cargo status + history** (item 1/3/4) — a `cargo_status` field and a `cargo_status_histories`
   table written on every change: Received → In Transit → Arrived → Verified → Invoiced →
   Paid → Ready → Collected. Everything else in the document hangs off this timeline.
2. **Public tracking page** — a route outside `auth` that takes only the tracking number and
   shows the timeline, hiding internal costs and notes.
3. **Verification screen + weight change audit** (item 4) — Dar warehouse enters the real
   weight, the system stores old and new, marks "KG changed — price affected", and re-prices
   the invoice (which already re-prices its ledger entry automatically).
4. **QR codes** (items 1, 7, 11) — one QR per cargo and one per pickup note, both printable and
   scannable with a phone camera; scanning a pickup note checks payment + storage first.
5. **Automatic storage** (items 8, 9, 10) — settings for free days and daily rate, a daily
   scheduler command, a storage balance per cargo that only ever grows, and a release-order
   block while storage is outstanding.
6. **Investigations** (item 12) and **loans** (item 14) — both are simple entities with history.
7. **Merge payment with the 1 KG minimum** (item 13) — one payment settling several invoices,
   billable weight computed on the combined weight.

Each of these is a self-contained module; none requires rewriting what exists.

---

## Build log — 16 Oct 2026

The six missing/partial items from the spec are now implemented and tested (see `docs/CARGO_STORAGE.md`):

| Spec item | Status | Where |
|---|---|---|
| Cargo photos, China location, batch | done | cargo page `/cargo/{id}` |
| QR code per cargo + public tracking + timeline | done | `/track`, `/track/{token}` |
| Mark as arrived → warehouse verification | done | `/verification` |
| Automatic daily storage, default + per-customer fee, no duplicates | done | `/warehouse/storage`, `storage:accrue` (00:05) |
| Release control with the storage check | done | `/release-control` |
| Merge payment across cargo, 1 KG minimum on combined weight | done | `/merge-payment` |

`tests/Feature/CargoOperationsTest.php` — 18 tests / 73 assertions.
