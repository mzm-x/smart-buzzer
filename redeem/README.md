# /redeem — Balance & Voucher Lookup

A **lookup-only** page for customers, plus an **admin page** for the team.
Customers enter their email and see the review balance (units remaining), the
dollar value of that balance, and a deterministic voucher code per listing.
There is no checkout and no payment — `index.php` only reads the pre-built
`redeem_data.json` snapshot.

## Files in this folder

| File | Role |
|------|------|
| `index.php` | Customer lookup page (reads `redeem_data.json`). |
| `submit.php` | **Admin page** — add/update/suppress balances, rebuild the snapshot. |
| `build_lib.php` | Shared build logic. Used by both `build_data.php` and `submit.php`. |
| `build_data.php` | CLI build script — turns the CSVs into `redeem_data.json`. |
| `redeem_data.json` | **Generated.** Do not hand-edit; rebuilt from the CSVs. |
| `data/single.csv` | Source — one-listing clients (legacy export). |
| `data/multi.csv` | Source — multi-listing clients (legacy export). |
| `data/tracker.csv` | Source — Late Debt Tracker export. |
| `data/manual.csv` | Source — written by `submit.php`. Never hand-edit while the admin page is open. |
| `data/.htaccess` | Blocks public HTTP access to the CSVs. **Must stay uploaded.** |

## Data pipeline

```
Google Sheet ──► data/single.csv ─┐
                data/multi.csv   ─┤
                data/tracker.csv ─┼──► build ──► redeem_data.json ──► index.php
submit.php   ──► data/manual.csv ─┘
```

Sources are read in that order and **a later source replaces an earlier one**
for the same `email + label`. So `manual.csv` always wins, and re-exporting the
tracker never double-counts orders already present in the legacy CSVs.

Every source is optional — the build processes whichever files exist.

---

## Admin page — `/redeem/submit.php`

Password: `smartbuzzer2025` (session-based).

| Action | What it does |
|--------|--------------|
| **Save & rebuild** | Writes/updates a row in `data/manual.csv`, then rebuilds `redeem_data.json` immediately. Live within seconds. |
| **Suppress from lookup** | Writes an `action=remove` row — that listing stops appearing for the customer even if it's still in a CSV export. |
| **Delete** | Removes the manual row. The value from the CSV export (if any) applies again. |
| **Rebuild JSON** | Re-runs the build without changing any data — use after uploading a fresh `tracker.csv`. |
| **Search** | Shows exactly what a given email/business/voucher code returns on the customer page. |

The admin page never touches `single.csv`, `multi.csv` or `tracker.csv`, so a
fresh Google Sheet export can't wipe manual corrections.

**Two emails on one order:** enter both in the email field separated by a space,
comma or semicolon. Each address becomes its own order object, so **either one**
pulls up that balance. Example: `demariajasob@yahoo.com jasondemaria2123@icloud.con`.

---

## Refreshing from the Google Sheet

### Legacy exports (`single.csv` / `multi.csv`)

1. **File ▸ Download ▸ Comma-separated values (.csv)**
2. Save as `redeem/data/single.csv` and `redeem/data/multi.csv`.
3. Run `php redeem/build_data.php`, or click **Rebuild JSON** in `submit.php`.

Expected headers (order-independent, extra columns tolerated):

```
data/single.csv   business,email,remaining          (label = business)
data/multi.csv    business,label,email,remaining
```

### Late Debt Tracker (`tracker.csv`)

1. Download the **Late Debt Tracker** tab as CSV.
2. Save as `redeem/data/tracker.csv` — no cleanup needed, the two-row header is
   handled automatically (the parser scans for the row containing `Mission Names`).
3. Run `php redeem/build_data.php`, or click **Rebuild JSON**.

Columns used, mapped **by header name**:

| Tracker column | Used as |
|----------------|---------|
| `Mission Names` | `label` |
| `Client Name` | `business` |
| `Email` | `email` (may hold two addresses) |
| `Units Remaining` | `remaining` |

**Rows deliberately skipped** — these clients must not be handed a voucher:

| Skip reason | Trigger |
|-------------|---------|
| `zero-or-negative` | `Units Remaining` ≤ 0 (nothing left, or over-delivered) |
| `refund-executed` | `Executed Refund` = `Yes` |
| `refund-approved` | `Approval` = `Approved` |
| `disputed` | `Overall Status (Biz)` contains `Disputed` |
| `clean-debt` | `Overall Status (Biz)` contains `Clean Debt` |
| `client-stopped` | `Overall Status (Biz)` or `Notes Biz` contains "request to stop" |
| `finished` | `Campaign Status` = `Finished` |
| `no-email` | No `@` anywhere in the email cell (blank, `No Email`, a name, `Crypto`, …) |

The build prints a per-reason count so you can sanity-check every import.

---

## How values are computed

- **`amount = remaining × $5`** — each remaining unit is worth $5.
- **`code`** is a deterministic voucher: `SB-` + the first 6 hex chars (uppercased)
  of `md5(email | label | remaining)`. The same `email + label + remaining`
  **always** yields the same code, so rebuilding never changes a code unless the
  underlying balance changes.

Each emitted order object:

```json
{
  "email": "owner@example.com",
  "business": "SafePro Roofing & Chimney",
  "label": "SafePro Roofing & Chimney Link 2",
  "remaining": 22,
  "amount": 110,
  "code": "SB-AB12CD"
}
```

And `redeem_data.json` wraps them:

```json
{
  "generated_at": "2026-07-20",
  "count": 620,
  "orders": [ ... ]
}
```

### Repeat orders on the same listing

A client can buy the same listing twice — the tracker then has two rows with the
same `Mission Names`. Both are kept as separate vouchers, and the customer sees
both cards with the totals added up. Only byte-identical rows (same email, label
**and** remaining) collapse into one.

---

## Build output

```
single.csv: 455 rows -> 464 orders | multi.csv: 89 rows -> 94 orders | tracker.csv: 812 rows -> 390 orders | manual.csv: 3 rows -> 3 orders | skipped: no-email 10, bad-remaining 0, zero-or-negative 96, ineligible 214 (refund-approved 31, disputed 12, client-stopped 40, finished 131) | 620 orders, 540 distinct emails, total $302,150 | 1 suppressed via manual remove
```

Malformed rows never crash the build — they are skipped and counted.

## Deployment

Upload after any change: `redeem_data.json`, plus whichever CSVs you refreshed.
On a first deploy also upload `submit.php`, `build_lib.php`, `build_data.php`
and **`data/.htaccess`** — without that file the CSVs (client emails + balances)
are publicly downloadable.

## Voucher expiry

A single global expiry is shown on every voucher, set at the top of `index.php`:

```php
$voucherValidUntil = '1 Sep 2026';
```
