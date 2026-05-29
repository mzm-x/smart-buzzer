# Smart Buzzer

Landing pages, conversion tracking, and internal dashboards for the **Smart Buzzer** Google/Tripadvisor/Trustpilot review service.

- **Main site:** https://smart-buzzer.com/
- **Primary LP:** https://smart-buzzer.com/promo/
- **Market:** US / Canada / Australia small–medium businesses (HVAC, dental, restaurants, hospitality, professional services)
- **Payment gateway:** Fanbasis (third-party)

> ⚠️ **Private repo.** Contains internal marketing logic and product knowledge. Do not make public.

---

## What's in here

Each landing page is a self-contained folder following a **4-file architecture**:

| File | Role | Editable? |
|------|------|-----------|
| `index.php` | The landing page (hero, pricing, FAQ, order form) | ✅ content/design |
| `thankyou.php` | Post-checkout page that fires the Purchase pixel | ✅ content only |
| `analytics.php` | Backend that logs events to `page_analytics.log` (13-col TSV) | ❌ copy exact |
| `log.php` | Per-LP dashboard (customers, analytics, campaign breakdown) | ❌ shared file |

The root [`thankyou.php`](thankyou.php) is the shared thank-you page for seasonal campaigns (package + Fanbasis ref mapping lives here).

### Landing pages

| Folder | Theme | Notes |
|--------|-------|-------|
| `promo/` | Blue | Primary LP — Google Reviews (starter/growth/performance) |
| `promo-b1g1/` | Blue (B1G1) | Buy-1-Get-1 packages |
| `promo-california/` | Coastal Blue + Sunset Gold | CA-targeted clone of `/promo/` |
| `promo-outbound/` | Blue | Clone for outbound email campaigns |
| `promo-industry/` | Blue | Industry-targeted variant |
| `promo-tripadvisor/` | Tripadvisor Green | Tripadvisor reviews (28 / 35 / 50) |
| `promo-trustpilot/` | Trustpilot Green | Trustpilot reviews |
| `midyear/` · `seasonal/` | Seasonal | Rotating seasonal campaign slot |

### Supporting tools

| Folder | Purpose |
|--------|---------|
| `analytics/` | Centralized dashboard — aggregates `customer_data.log` across all LPs (filesystem read) |
| `outbound/` | Internal Apify-powered Google Maps lead scraper (password-protected, not customer-facing) |
| `submit/` | Account Manager onboarding + order management app |
| `survey/`, `demography/`, `links/`, `lifegrid/` | Misc internal utilities |

---

## Tracking stack (unified)

GTM is the single source of truth. GA4 standard ecommerce funnel:

```
view_item → begin_checkout → add_payment_info → purchase
```

| Service | ID |
|---------|-----|
| GTM container | `GTM-WJ6ZK3MR` |
| Facebook Pixel | `938738044322271` |
| TikTok Pixel | `D25JHKBC77UF6R3NPOGG` |

User data is captured at `begin_checkout` and persisted to `localStorage` to bridge the Fanbasis third-party gateway, then read back for the `purchase` event on `thankyou.php`.

See [`CLAUDE.MD`](CLAUDE.MD) for the full event spec, log formats, UTM convention, and per-LP package metadata.

---

## Conventions

- **`customer_data.log` records form submissions only** — never pricing-button clicks.
- All traffic allowed (no geo-blocking).
- Use `window.location.href` (not `pathname`) so UTM params are captured.
- 6 UTM params tracked: `source`, `medium`, `campaign`, `content`, `term`, `placement`.
- Mobile rule: in any image+text split section, the **image renders above the text** (`@media max-width: 880px`).
- Each LP uses a **unique CSS class suffix** to avoid cross-page conflicts.

---

## Security notes

The following are **excluded from this repo** via [`.gitignore`](.gitignore):

- `*.log` — runtime logs containing customer PII (email, WhatsApp, business name)
- `outbound/data/`, `survey/data/` — scraped leads & survey responses
- `submit/config.php` — DB credentials, Account Manager accounts, password hashes

When cloning to a new server, re-create `submit/config.php` from the live environment (it is intentionally untracked).

---

*Internal project. See [`CLAUDE.MD`](CLAUDE.MD) for the complete knowledge document.*
