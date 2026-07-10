# /redeem — Balance & Voucher Lookup

A **lookup-only** page. Customers enter their email and see the review balance
(units remaining), the dollar value of that balance, and a deterministic voucher
code per listing. There is no checkout, no payment, and no data written back —
`index.php` only reads the pre-built `redeem_data.json` snapshot.

## Files in this folder

| File | Role |
|------|------|
| `index.php` | The lookup page (reads `redeem_data.json`). |
| `build_data.php` | CLI build script — turns the CSVs into `redeem_data.json`. |
| `redeem_data.json` | **Generated.** Do not hand-edit; rebuilt by `build_data.php`. |
| `data/single.csv` | Source — one-listing clients. |
| `data/multi.csv` | Source — multi-listing clients. |

## Data pipeline

```
Google Sheet
   │  File ▸ Download ▸ Comma-separated values (.csv)
   ▼
redeem/data/single.csv   +   redeem/data/multi.csv
   │  php build_data.php
   ▼
redeem/redeem_data.json  ──►  index.php (lookup)
```

1. In the Google Sheet, choose **File ▸ Download ▸ Comma-separated values (.csv)**.
2. Save the downloaded files as `redeem/data/single.csv` and `redeem/data/multi.csv`.
3. From the repo root run `php redeem/build_data.php` (or `php build_data.php`
   from inside `redeem/`). This regenerates `redeem_data.json`.

Both CSVs are optional — the script processes whichever exist and skips the rest.

### Expected CSV headers (exact)

**`data/single.csv`** — one listing per client. The order **label = business**.

```
business,email,remaining
```

**`data/multi.csv`** — a client with several listings/links; each row is one listing.

```
business,label,email,remaining
```

Column meanings (both files):

- `business` — client / display name (for `multi.csv` this is the group name).
- `label` — the specific listing/link name (`multi.csv` only; single uses `business`).
- `email` — may be blank, may be `No Email`, or may contain **two emails**
  separated by a space/comma/semicolon (e.g. `a@x.com b@y.com`). Each valid email
  produces its own order object, so **either** address can redeem that order.
- `remaining` — integer units remaining. Rows without a valid non-negative
  integer here are skipped.

Column **order and extra columns are tolerant** — the script maps by header name,
not position. Blanks, `No Email`, and stray notes without an `@` are dropped
automatically.

## How values are computed

- **`amount = remaining × $5`** — each remaining unit is worth $5.
- **`code`** is a deterministic voucher: `SB-` + the first 6 hex chars (uppercased)
  of `md5(email | label | remaining)`. The same `email + label + remaining`
  **always** yields the same code, so re-running the build never changes a code
  unless the underlying balance changes.

Each emitted order object looks like:

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
  "generated_at": "2026-07-09",
  "count": 620,
  "orders": [ ... ]
}
```

## Refreshing when balances change

Balances live in the Google Sheet. To publish new numbers:

1. Re-download both tabs as CSV (**File ▸ Download ▸ .csv**).
2. Overwrite `data/single.csv` and `data/multi.csv`.
3. Re-run `php build_data.php`.

That's it — `redeem_data.json` is rewritten and `index.php` serves the new
balances immediately. No database, no deploy step beyond uploading the refreshed
`redeem_data.json` (and the CSVs, if you keep them on the server).

## Build output

The script prints a one-line summary, e.g.:

```
single.csv: 455 rows -> 470 orders | multi.csv: 89 rows -> 150 orders | skipped 12 (no-email 9, bad-remaining 3) | 620 orders, 540 distinct emails, total $302,150
```

- **rows** — data rows read per file (header excluded).
- **orders** — order objects emitted per file (a two-email row emits two).
- **skipped** — rows that produced nothing, bucketed by reason
  (`no-email` = no `@` found, `bad-remaining` = not a non-negative integer).
- **distinct emails** and **total $** — across everything emitted.

Malformed rows never crash the build — they are skipped and counted.
