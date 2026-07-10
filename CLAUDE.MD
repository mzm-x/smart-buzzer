# Smart Buzzer Landing Page - Project Knowledge Document (v4.0)

## CONFIDENCE-GATED EXECUTION RULE (CRITICAL)

**ALWAYS state confidence % before executing ANY code change.**

- If confidence ≥ 90% → execute the change immediately, no approval needed.
- If confidence < 90% → STOP. Do NOT execute. Present:
  1. The exact plan (what will change, where, in what order)
  2. Confidence % per item + overall
  3. What's causing the < 90% (unclear copy, ambiguous spec, layout uncertainty, dependency unknown, etc.)
  4. Specific clarifying questions OR options to choose from
  Then wait for user approval before any Edit/Write tool call.

**How to calibrate confidence honestly:**
- 95-100%: Trivial mechanical change (text swap, color change, removing element). No layout questions.
- 90-94%: Clear scope, single-file edit, no copy ambiguity, mobile/desktop both straightforward.
- 80-89%: Multiple sub-decisions (copy needs interpretation, mobile layout has tradeoffs, new section requires structure choices).
- < 80%: Spec is genuinely ambiguous, multiple valid interpretations, or risk of overwriting user intent.

**Do not pad confidence to skip approval.** If unsure whether something is 88% or 92%, treat it as 88% and ask. The user prefers a 30-second confirmation over a 30-minute redo.

**Do not chain low-confidence changes together hoping average ≥ 90%.** Each item in a multi-step plan needs ≥ 90% individually, OR the plan needs approval.

**Apply this rule globally** — to every project, every file, every conversation in this repo. Not just landing-page work.

---

## CHANGED-FILES CHECKLIST RULE (ALWAYS)

**End EVERY response that creates, edits, or deletes files with a "File yang diubah:" list** — each file path + a short note of what changed (dibuat / diedit bagian apa / dihapus). Scope = that turn's changes only; give a full-session recap only when explicitly asked. Do this automatically, every single time, without being asked.

**Why:** The user deploys to production manually (cPanel, file-by-file). A missing list means a changed file gets missed on upload. This has been re-requested many times — treat as high priority.

---

## Project Overview
**Product:** Smart Buzzer Google Review Service
**Target Market:** US/Canada/Australia small-medium businesses (HVAC, dental, restaurants, professional services)
**Core Value Prop:** Authentic local Google reviews with gradual delivery, natural posting patterns, across 1,200+ completed campaigns

**Main Site:** https://smart-buzzer.com/
**Primary LP:** https://smart-buzzer.com/promo/
**Payment Gateway:** Fanbasis (third-party - no GTM access on gateway pages)

**Always use this favicon:**  
```html
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
```

---

## OUR UNIQUE VALUE PROPOSITIONS (UVP)

| Feature | Description |
|---------|-------------|
| (20%) 4-Star + (80%) 5-Star Ratings | Natural rating mix that avoids Google's detection algorithms |
| (70%) Local + (30%) Global Names | Authentic reviewer diversity for credibility |
| Human-Written Custom Content | Tailored reviews that match your business services |
| 1-3 Reviews Per Week | Gradual posting prevents algorithm detection |
| Detailed Delivery Report | Full transparency on campaign progress |

---

## SERVICE DELIVERY KNOWLEDGE (Source of Truth for TNC + Customer Comms)

This is the canonical service-delivery knowledge that must be reflected in all customer-facing TNC, onboarding flows (`/submit/data/tnc.json`), landing page copy, support scripts, and dashboard messaging. **Always use this exact framing to avoid miscommunication.**

### 1. Daily Submission Cadence
**We submit 5-10 reviews daily.** Around 5 will show up and stick (roughly 3 per day). This gradual pace keeps the account safe and looks natural to the algorithm. **We only count reviews that stick** — if a review doesn't stick, it does NOT count toward the order.

### 2. Drops Are Normal — Per-Review 7-Day Replacement
Each review counts as **delivered the moment it shows up** on the listing (1 review = 1 delivered). Every delivered review carries its **own 7-day replacement window starting the day it appears** — NOT after the whole order is complete. If a review drops within those 7 days, **we replace it free, one replacement per review**. Reviews that stay live past 7 days are **final** — **if a review drops after the 7-day window, it is NOT replaced**. If a replacement later drops, it is **not replaced again** (one replacement per review). This is the canonical guarantee model — do NOT describe the guarantee as starting "after the full order completes".

### 3. Error Names (Asian-Server Accounts)
Since we use real people to post reviews, occasionally some reviews may come from Asian-server accounts. **These are not counted as valid orders** — we automatically replace them with new reviews until the full order is fulfilled.

### 4. Mistaken Reviews
Incorrect reviews (wrong language/rating) cannot be removed but **will not count toward the order**. We provide free replacements until resolved.

### 5. Google Wave Updates
Google rolls out algorithm updates ("waves") a few times a year — usually around mid-year, but timing is unpredictable. When a wave hits, a few reviews may get affected. The account stays safe, and **we replace any affected reviews until the order is complete**. As a seller, we have no advance notice of these waves.

### 6. Review Posting Timeline
Estimated timeline depends on the business, but **full delivery of 55 reviews typically takes around 3 months (default baseline)**. Larger orders take longer. **Once the campaign has started, the order is final and cannot be cancelled or disputed.**

### 7. Content Guidelines
Customer may provide input on review content, **but cannot select reviewer names**. All names follow local naming conventions for authenticity.

### Source of Truth
- TNC JSON file: `/submit/data/tnc.json` (8 points total — these 7 + Refund Policy + Guarantee & Claim Period)
- Onboarding form: `/submit/submit.php` reads from `data/tnc.json`
- DO NOT contradict these points in any landing page copy, modal, or marketing material
- When updating TNC text, update BOTH `data/tnc.json` AND this section in CLAUDE.md to keep them in sync

### Phrases to AVOID (compliance / clarity)
- "Zero account bans" / "0 account bans" / "no account bans" — compliance risk
- **"Zero drops" / "no drops" / "0 drops" / "zero ban" / "zero bans"** — compliance risk. We promise replacement, not zero drops. Some drops are normal Google behavior. Instead say: "7-day per-review replacement", "if a review drops within 7 days we replace it free (one replacement per review)", "drops within 7 days auto-replaced free".
- **"guarantee starts after the full order completes" / "7 days from completion" / "after campaign completion you have 7 days"** — outdated guarantee model. Replaced by per-review 7-day window: each review's 7 days starts the day it shows up. One replacement per review.
- "Reviews are posted 1-3 per week" — outdated, replaced by "5-10 submitted daily, ~3 stick per day"
- "1-2 months" / "2-3 months" hardcoded timeline — replaced by "around 3 months for 55 reviews (default), varies by business"

### Mobile section ordering rule (CRITICAL — applies to ALL LPs)
**On mobile (≤880px), the image/visual ALWAYS appears ABOVE text in any image+text split section.**
- Hero, case study, real reviews, track order, and any image+text two-column layout must reorder so the image renders FIRST on mobile, text BELOW
- Implementation: use CSS `order: 1` on the visual element and `order: 2` on the text element inside the mobile media query (`@media (max-width: 880px)`)
- Reason: users skim on mobile — visual proof captures attention first, text reinforces second. Pure text-first walls cause scroll-aways.
- Desktop layout stays as designed (e.g., text-left + image-right is fine for desktop)
- Default content order in conversation: when user describes a section as "image: ... text: ...", treat image as the lead element. Image should always come first in the visual hierarchy on mobile.

---

# UNIFIED GTM EVENT FLOW (v4.0)

## Core Principle
**Do NOT create duplicate events.** Use a single standardized set of GA4 + Meta events that fire across ALL pages (main site, landing pages). Differentiate page performance via `page_location` in GA4 — not by creating separate events per page.

## Complete Funnel: view_item → begin_checkout → add_payment_info → purchase

```
+-----------------------------------------------------------------------+
|                    UNIFIED GTM EVENT FUNNEL (v4.0)                    |
+-----------------------------------------------------------------------+
|                                                                       |
|  STEP 0: PAGE LOAD (All Pages)                                        |
|  +-- GTM: PageView (auto via GTM)                                     |
|  +-- FB Pixel: fbq('track', 'PageView')                              |
|  +-- TikTok: ttq.page()                                               |
|                                                                       |
|  STEP 1: PRICING SECTION VISIBLE (30% scroll or element visible)     |
|  +-- File: index.php / main site product page                         |
|  +-- Trigger: Element visibility or 30% scroll depth                 |
|  +-- GTM dataLayer: view_item (per package, GA4 standard)            |
|  +-- Analytics: VIEW_ITEM → page_analytics.log                        |
|                                                                       |
|  STEP 2: ORDER NOW CLICK (Pricing Cards)                              |
|  +-- File: index.php                                                  |
|  +-- Trigger: Click any ORDER NOW button in pricing section           |
|  +-- FB Pixel: fbq('track', 'Lead')                                  |
|  +-- TikTok: ttq.track('SubmitForm')                                  |
|  +-- GTM dataLayer: generate_lead (for WhatsApp CTA clicks)          |
|  +-- Analytics: ORDER_[PACKAGE]_CLICK → page_analytics.log           |
|  +-- Action: Smooth scroll to #order-form, pre-select package        |
|                                                                       |
|  STEP 2b: ADD TO CART (main site /product/ page only)                |
|  +-- File: WooCommerce product page                                   |
|  +-- Trigger: Click cart/buy button                                   |
|  +-- GTM dataLayer: add_to_cart (GA4 standard)                       |
|                                                                       |
|  STEP 3: COMPLETE ORDER (Form Submit + Redirect)                     |
|  +-- File: index.php                                                  |
|  +-- Trigger: Click COMPLETE ORDER after filling form                 |
|  +-- FB Pixel: fbq('track', 'InitiateCheckout')                      |
|  +-- TikTok: ttq.track('InitiateCheckout')                           |
|  +-- GTM dataLayer: begin_checkout (GA4 + user_data captured here)  |
|  +-- GTM dataLayer: add_payment_info (fires on same click/redirect)  |
|  +-- Analytics: ORDER_SUBMIT → page_analytics.log                    |
|  +-- Customer: businessName + whatsapp + email → customer_data.log   |
|  +-- LocalStorage: save email + phone for purchase event bridge      |
|  +-- Action: Redirect to Fanbasis payment URL                        |
|                                                                       |
|  STEP 4: THANK YOU PAGE (After Gateway Return)                       |
|  +-- File: thankyou.php                                               |
|  +-- Trigger: Page load (purchase confirmed)                          |
|  +-- FB Pixel: fbq('track', 'Purchase')                              |
|  +-- TikTok: ttq.track('CompletePayment')                            |
|  +-- GTM dataLayer: purchase (reads user_data from LocalStorage)     |
|  +-- Analytics: THANKYOU_PAGE_VIEW → page_analytics.log              |
|                                                                       |
|  STEP 4b: CLICK HERE TO CONTINUE (Thank You Page button)            |
|  +-- Trigger: Click CLICK HERE TO CONTINUE button                    |
|  +-- Analytics: PURCHASE_CLICK → page_analytics.log                  |
|  +-- Action: 400ms delay then redirect to Fanbasis                   |
|                                                                       |
+-----------------------------------------------------------------------+
```

**NOTE (v4.0):** GTM is the single source of truth for all tracking. Direct `fbq()` and `ttq()` calls are retained as fallback. GA4 standard ecommerce events replace the old `select_item` / `initiate_checkout` custom events. User data is captured at `begin_checkout` on LP and persisted to LocalStorage for the `purchase` event on thankyou.php.

### Unified Event Details

| Step | Trigger | GA4 dataLayer Event | FB Pixel Event | TikTok Event | Pages |
|------|---------|-------------------|---------------|-------------|-------|
| Pricing visible | 30% scroll / element visible | `view_item` | - | - | All LPs + main site |
| ORDER NOW click | Pricing button click | `generate_lead` | `Lead` | `SubmitForm` | All LPs |
| Add to Cart | Product page cart click | `add_to_cart` | - | - | /product/ only |
| COMPLETE ORDER | Form submit | `begin_checkout` | `InitiateCheckout` | `InitiateCheckout` | All LPs |
| COMPLETE ORDER | Form submit (same click) | `add_payment_info` | - | - | All LPs |
| Thank you page load | Page load | `purchase` | `Purchase` | `CompletePayment` | thankyou.php |
| WhatsApp button | WA CTA click | `generate_lead` | - | - | All pages |

---

# GTM DATALAYER SYSTEM (v4.0)

## GTM Container ID
**GTM ID:** `GTM-WJ6ZK3MR`

```html
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
<!-- End Google Tag Manager -->
```

**Required on:** index.php AND thankyou.php (in `<head>` before FB/TT pixels)

## Package Metadata (Used by dataLayer)

**IMPORTANT: Each LP uses its own `sbPkgMeta`. Do NOT copy /promo/ packages into /promo-b1g1/ or vice versa.**

### /promo/ — Standard Packages
```javascript
var sbPkgMeta = {
    'starter':     {id: 'pkg_starter_55',     name: 'Buy Google Reviews - 55 Local',  item_category: 'Google Reviews', price: 360.00, reviews: 55},
    'growth':      {id: 'pkg_growth_88',      name: 'Buy Google Reviews - 88 Local',  item_category: 'Google Reviews', price: 550.00, reviews: 88},
    'performance': {id: 'pkg_performance_110', name: 'Buy Google Reviews - 110 Local', item_category: 'Google Reviews', price: 660.00, reviews: 110}
};
```

### /promo-b1g1/ — B1G1 Packages
```javascript
var sbPkgMeta = {
    'booster':   {id: 'pkg_booster_65',    name: 'Buy Google Reviews - 65 Local (B1G1)',  item_category: 'Google Reviews', price: 380.00, reviews: 65},
    'dominator': {id: 'pkg_dominator_120', name: 'Buy Google Reviews - 120 Local (B1G1)', item_category: 'Google Reviews', price: 680.00, reviews: 120}
};
```

---

## Fanbasis Payment URLs (Per LP)

### /promo/ — Fanbasis Links
| Package Key | Reviews | Price | Fanbasis URL |
|-------------|---------|-------|-------------|
| `starter` | 55 | $360.00 | `https://www.fanbasis.com/agency-checkout/smartbuzzer/Rgl3R` |
| `growth` | 88 | $550 | `https://www.fanbasis.com/agency-checkout/smartbuzzer/Vmv31` |
| `performance` | 110 | $660 | `https://www.fanbasis.com/agency-checkout/smartbuzzer/WnP3g` |

### /promo-b1g1/ — Fanbasis Links
| Package Key | Reviews | Price | Fanbasis URL |
|-------------|---------|-------|-------------|
| `booster` | 65 | $380 | `https://www.fanbasis.com/agency-checkout/smartbuzzer/Rgl3R` |
| `dominator` | 120 | $680 | `https://www.fanbasis.com/agency-checkout/smartbuzzer/wpk38` |

---

## thankyou.php Package Mapping (Per LP)

### /promo/thankyou.php
| Key | Name | Reviews | Price |
|-----|------|---------|-------|
| `starter` | Starter | 55 | 360.00 |
| `growth` | Growth | 88 | 550.00 |
| `performance` | Performance | 110 | 660.00 |

### /promo-b1g1/thankyou.php
| Key | Name | Reviews | Price |
|-----|------|---------|-------|
| `booster` | Booster | 65 | 380.00 |
| `dominator` | Dominator | 120 | 680.00 |

**Default fallback package when `pkg` param is missing:**
- `/promo/` → `growth`
- `/promo-b1g1/` → `dominator`

---

## Transaction ID Format
```
SB_{timestamp}_{random6char}
Example: SB_1772349856835_6jdgy0
```

```javascript
var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
```

---

## Event 1 - view_item (Pricing Section Visible)

Fires when pricing section becomes visible (30% scroll depth or element visibility trigger in GTM).

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ecommerce: null});
window.dataLayer.push({
    event: 'view_item',
    ecommerce: {
        currency: 'USD',
        value: meta.price,
        items: [{
            item_id: meta.id,
            item_name: meta.name,
            item_category: 'Google Reviews',
            price: meta.price,
            quantity: 1
        }]
    }
});
```

**Trigger:** Element visibility (pricing section) or 30% scroll depth — whichever fires first.
**Where:** All LPs + main site (`/`, `/product/google-reviews/`)

---

## Event 2 - add_to_cart (Main Site Product Page Only)

Fires when user clicks cart/buy button on WooCommerce product page.

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ecommerce: null});
window.dataLayer.push({
    event: 'add_to_cart',
    ecommerce: {
        currency: 'USD',
        value: meta.price,
        items: [{
            item_id: meta.id,
            item_name: meta.name,
            item_category: 'Google Reviews',
            price: meta.price,
            quantity: 1
        }]
    }
});
```

**Where:** `/product/google-reviews/` only.

---

## Event 3 - begin_checkout (COMPLETE ORDER Click)

Fires on click of COMPLETE ORDER button BEFORE redirect. Captures user_data here — this is the "Capture-at-Source" strategy to bridge the Fanbasis tracking gap.

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ecommerce: null});

// Parse name for user_data
var nameParts = biz.split(' ');
var firstName = nameParts[0] || '';
var lastName = nameParts.slice(1).join(' ') || '';

window.dataLayer.push({
    event: 'begin_checkout',
    user_data: {
        email: email,              // From ofEmail field (optional)
        phone_number: wa,          // From ofWhatsapp field
        first_name: firstName,     // Parsed from business name
        last_name: lastName        // Parsed from business name
    },
    ecommerce: {
        currency: 'USD',
        value: meta.price,
        items: [{
            item_id: meta.id,
            item_name: meta.name,
            item_category: 'Google Reviews',
            price: meta.price,
            quantity: 1
        }]
    }
});

// Bridge: persist user_data to LocalStorage for purchase event on thankyou.php
localStorage.setItem('sb_user_email', email || '');
localStorage.setItem('sb_user_phone', wa || '');
localStorage.setItem('sb_user_fname', firstName || '');
localStorage.setItem('sb_user_lname', lastName || '');
localStorage.setItem('sb_txn_id', txnId);
localStorage.setItem('sb_pkg', pkgValue);
```

**Where:** All LPs (`/promo/`, `/promo-b1g1/`, `/seasonal/`, etc.)

---

## Event 4 - add_payment_info (Same Click as begin_checkout)

Fires on the same COMPLETE ORDER click, immediately after begin_checkout, before redirect to Fanbasis.

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ecommerce: null});
window.dataLayer.push({
    event: 'add_payment_info',
    ecommerce: {
        currency: 'USD',
        value: meta.price,
        payment_type: 'Credit Card',
        items: [{
            item_id: meta.id,
            item_name: meta.name,
            item_category: 'Google Reviews',
            price: meta.price,
            quantity: 1
        }]
    }
});
```

**Where:** All LPs. Fires before window.location redirect.

---

## Event 5 - purchase (thankyou.php Page Load)

Fires on thankyou.php page load. Reads user_data from LocalStorage (set at begin_checkout).

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ecommerce: null});

// Read persisted user_data from LocalStorage
var sbEmail  = localStorage.getItem('sb_user_email') || '';
var sbPhone  = localStorage.getItem('sb_user_phone') || '';
var sbFname  = localStorage.getItem('sb_user_fname') || '';
var sbLname  = localStorage.getItem('sb_user_lname') || '';
var sbTxnId  = localStorage.getItem('sb_txn_id') || ('SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6));

window.dataLayer.push({
    event: 'purchase',
    user_data: {
        email: sbEmail,
        phone_number: sbPhone,
        first_name: sbFname,
        last_name: sbLname
    },
    ecommerce: {
        transaction_id: sbTxnId,
        value: parseFloat(pkgPrice),
        currency: 'USD',
        items: [{
            item_id: pkg,
            item_name: pkgName
        }]
    }
});

// Clear LocalStorage after firing
localStorage.removeItem('sb_user_email');
localStorage.removeItem('sb_user_phone');
localStorage.removeItem('sb_user_fname');
localStorage.removeItem('sb_user_lname');
localStorage.removeItem('sb_txn_id');
localStorage.removeItem('sb_pkg');
```

**Where:** thankyou.php (on page load, before handlePurchaseClick).

---

## Event 6 - generate_lead (WhatsApp Button Click)

Fires when user clicks any WhatsApp CTA button.

```javascript
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    event: 'generate_lead',
    method: 'WhatsApp',
    value: meta ? meta.price : 0,
    currency: 'USD'
});
```

**Where:** All pages with WhatsApp CTA.

---

## GTM Variable Setup (Enhanced Conversions)

### Step 1 - Data Layer Variables (create in GTM)

| Variable Name | Data Layer Path | Purpose |
|--------------|----------------|---------|
| `DLV - User Email` | `user_data.email` | Enhanced Conversions |
| `DLV - User Phone` | `user_data.phone_number` | Enhanced Conversions |
| `DLV - User First Name` | `user_data.first_name` | Enhanced Conversions |
| `DLV - User Last Name` | `user_data.last_name` | Enhanced Conversions |
| `DLV - Ecommerce Value` | `ecommerce.value` | GA4 / Meta |
| `DLV - Transaction ID` | `ecommerce.transaction_id` | Purchase dedup |
| `DLV - Currency` | `ecommerce.currency` | GA4 / Meta |
| `DLV - Items` | `ecommerce.items` | GA4 ecommerce |

### Step 2 - User-Provided Data Variable (Google Ads Enhanced Conversions)

Create variable type: **User-Provided Data**, set to **Manual Configuration**:
- Email: `{{DLV - User Email}}`
- Phone: `{{DLV - User Phone}}`
- Name: `{{DLV - User First Name}} {{DLV - User Last Name}}`

Variable name: `UPD - Manual User Data`

In Google Ads Conversion tag: enable "Include user-provided data", select `{{UPD - Manual User Data}}`.

### Step 3 - Meta Pixel Advanced Matching

In Meta Pixel GTM tag (community template), enable **Advanced Matching**:
- `em`: `{{DLV - User Email}}`
- `ph`: `{{DLV - User Phone}}`

GTM hashes these automatically before sending to Meta.

---

## GTM Tags & Triggers (Live in GTM Container)

| Tag Name | Type | Firing Triggers |
|----------|------|----------------|
| Conversion Linker | Conversion Linker | All Pages |
| FB - Add Payment Info | Facebook Pixel | event `add_payment_info` |
| FB - Add To Cart | Facebook Pixel | Click ATC button, Click Text Order Now, event `add_to_cart`, event `view_cart` |
| FB - Initiate Checkout | Facebook Pixel | event `begin_checkout`, event `event_checkout` |
| FB - Pageview | Facebook Pixel | All Pages |
| FB - Purchase | Facebook Pixel | event `purchase` |
| FB - View Content | Facebook Pixel | Element Visibility Pricing 30%, event `view_item` |

**NOTE:** FB Pixel is managed via GTM. Do NOT add direct `fbq('init', ...)` on landing pages — GTM already fires all FB events above.

## dataLayer Events Implemented in index.php (All LPs)

### 1. view_item — Pricing Section Visible
- **Trigger:** Pricing section (`#pricing`) enters viewport (scroll/visibility)
- **Fires once** per page load
- **Payload:** All packages as items array
```javascript
dataLayer.push({ event: 'view_item', ecommerce: { currency: 'USD', value: defaultPrice, items: [{item_id, item_name, item_category: 'Google Reviews', price, quantity: 1}, ...] } });
```

### 2. add_to_cart — ORDER NOW Button Click
- **Trigger:** Click on any `[data-package]` pricing button
- **Payload:** Single package item
```javascript
dataLayer.push({ event: 'add_to_cart', ecommerce: { currency: 'USD', value: meta.price, items: [{item_id, item_name, item_category: 'Google Reviews', price, quantity: 1}] } });
```
- Also fires: `fbq('track', 'Lead')`, `ttq.track('SubmitForm')`, `generate_lead`

### 3. begin_checkout — COMPLETE ORDER Click
- **Trigger:** Click COMPLETE ORDER button (after form validation)
- **Payload:** user_data + ecommerce
```javascript
dataLayer.push({ event: 'begin_checkout', user_data: { email, phone_number, first_name, last_name }, ecommerce: { currency: 'USD', value, items: [{...}] } });
```
- Immediately followed by `add_payment_info` event
- Saves user_data to LocalStorage (`sb_user_email`, `sb_user_phone`, `sb_user_fname`, `sb_user_lname`, `sb_txn_id`, `sb_pkg`)
- Also fires: `fbq('track', 'InitiateCheckout')`, `ttq.track('InitiateCheckout')`

### 4. add_payment_info — Same Click as begin_checkout
```javascript
dataLayer.push({ event: 'add_payment_info', ecommerce: { currency: 'USD', value, payment_type: 'Credit Card', items: [{...}] } });
```

### 5. generate_lead — WhatsApp CTA Click
```javascript
dataLayer.push({ event: 'generate_lead', method: 'WhatsApp', value, currency: 'USD' });
```

### 6. purchase — thankyou.php Page Load
- Reads user_data from LocalStorage, fires purchase event, clears LocalStorage
- See thankyou.php section for implementation details

---

## GTM Tag Configuration Reference

### view_item
| Variable | dataLayer Path | Example |
|----------|---------------|---------|
| Event | `event` | `view_item` |
| Value | `ecommerce.value` | `360.00` |
| Currency | `ecommerce.currency` | `USD` |
| Item ID | `ecommerce.items.0.item_id` | `pkg_starter_55` |
| Item Name | `ecommerce.items.0.item_name` | `Buy Google Reviews - 55 Local` |
| Item Category | `ecommerce.items.0.item_category` | `Google Reviews` |

### begin_checkout
| Variable | dataLayer Path | Example |
|----------|---------------|---------|
| Event | `event` | `begin_checkout` |
| Email | `user_data.email` | `john@example.com` |
| Phone | `user_data.phone_number` | `+12345678` |
| First Name | `user_data.first_name` | `John` |
| Last Name | `user_data.last_name` | `Doe` |
| Value | `ecommerce.value` | `360.00` |
| Currency | `ecommerce.currency` | `USD` |
| Item ID | `ecommerce.items.0.item_id` | `pkg_starter_55` |

### purchase
| Variable | dataLayer Path | Example |
|----------|---------------|---------|
| Event | `event` | `purchase` |
| Transaction ID | `ecommerce.transaction_id` | `SB_1772349856835_6jdgy0` |
| Value | `ecommerce.value` | `360.00` |
| Currency | `ecommerce.currency` | `USD` |
| Email | `user_data.email` | `john@example.com` |
| Phone | `user_data.phone_number` | `+12345678` |

---

### Direct Pixel Event Code (Fallback — GTM is primary)

**ORDER NOW click (index.php, pricing button):**
```javascript
// Inside [data-package] click handler
if (typeof fbq !== 'undefined') {
    fbq('track', 'Lead', {content_name: pkg});
}
if (typeof ttq !== 'undefined') {
    ttq.track('SubmitForm', {content_name: pkg});
}
```

**COMPLETE ORDER click (index.php, submitOrder):**
```javascript
// Inside submitOrder() after validation, before redirect
if (typeof fbq !== 'undefined') {
    fbq('track', 'InitiateCheckout', {
        value: meta.price,
        currency: 'USD',
        content_name: pkgName,
        content_type: 'product',
        content_ids: [pkgValue]
    });
}
if (typeof ttq !== 'undefined') {
    ttq.track('InitiateCheckout', {
        value: meta.price,
        currency: 'USD',
        content_name: pkgName
    });
}
```

**thankyou.php page load (Purchase):**
```javascript
// On page load — fires automatically, not on button click
if (typeof fbq !== 'undefined') {
    fbq('track', 'Purchase', {
        value: parseFloat(pkgPrice),
        currency: 'USD',
        content_name: pkgName + ' - ' + pkgReviews + ' Reviews',
        content_type: 'product',
        content_ids: [pkg]
    });
}
if (typeof ttq !== 'undefined') {
    ttq.track('CompletePayment', {
        value: parseFloat(pkgPrice),
        currency: 'USD',
        content_name: pkgName + ' - ' + pkgReviews + ' Reviews',
        content_type: 'product'
    });
}
```

### Pixel Events in FB Events Manager

| FB Events Manager | Pixel Event | Setup Method | Trigger |
|-------------------|-------------|--------------|---------|
| PageView | `PageView` | Manual Setup | Auto on page load (all pages) |
| Lead | `Lead` | Manual Setup | ORDER NOW pricing button click |
| InitiateCheckout | `InitiateCheckout` | Manual Setup | COMPLETE ORDER form submit |
| Purchase | `Purchase` | Manual Setup | thankyou.php page load |
| SubscribedButtonClick | Auto-logged | Automatic | FB auto-detects button clicks |

### Full Redirect Flow

```
Page load → PageView fires (FB + TikTok + GTM)
          ↓
Pricing visible (30% scroll) → view_item fires (GTM dataLayer)
          ↓
User clicks ORDER NOW → Lead pixel + generate_lead dataLayer
          ↓
Scrolls to form, fills business name + email + WhatsApp
          ↓
User clicks COMPLETE ORDER → begin_checkout + add_payment_info fire (GTM)
                           → InitiateCheckout pixel fires (FB + TikTok)
                           → user_data saved to LocalStorage
          ↓
Redirect: Fanbasis payment URL (GTM stops here — third party)
          ↓
User returns to thankyou.php → purchase fires (GTM reads LocalStorage)
                             → Purchase pixel fires (FB + TikTok)
                             → LocalStorage cleared
```

---

# FOUR-FILE SYSTEM ARCHITECTURE (Per Landing Page) — v3.1

## System Components Overview

```
+-------------------------------------------------------------+
|                    SMART BUZZER SYSTEM                      |
+-------------------------------------------------------------+
|                                                             |
|  +--------------+      +--------------+      +----------+   |
|  |  index.php   |--+-->| analytics.php|----->| page_    |   |
|  |  (Landing)   |  |   |  (Backend)   |      |analytics |   |
|  |              |  |   +--------------+      |  .log    |   |
|  |  - Hero      |  |                         +----------+   |
|  |  - Pricing   |  |                                        |
|  |  - FAQ       |  |   +--------------+      +----------+   |
|  |  - Footer    |  +-->|   log.php    |----->|customer_ |   |
|  +--------------+      | (Dashboard)  |      |data.log  |   |
|        |               |              |      +----------+   |
|        |               |  - Customers |                     |
|        |               |  - Analytics |                     |
|        |               |  - Campaign  |                     |
|        |               +--------------+                     |
|        |                                                    |
|        |  +----------------+                                |
|        +->| thankyou.php   |                                |
|           | (Thank You)    |                                |
|           |                |                                |
|           | - Purchase     |                                |
|           |   Pixel        |                                |
|           | - Redirect to  |                                |
|           |   Fanbasis     |                                |
|           +----------------+                                |
+-------------------------------------------------------------+
```

### 1. **index.php** - Landing Page (CUSTOMIZABLE)
**Purpose:** User-facing landing page  
**Customizable:** YES

**What You Can Change:**
- Pricing amounts (Starter/Growth/Performance)
- Checkout URLs (Fanbasis links)
- Hero section content
- FAQ questions/answers
- Images and branding
- Package names

**What You CANNOT Change:**
- Analytics tracking code in `<head>`
- GTM container code and dataLayer pushes
- UTM tracking logic (`window.location.href`)
- Facebook Pixel code
- TikTok Pixel code
- `data-package` attributes on buttons
- GA4 event sequence (view_item → begin_checkout → add_payment_info → purchase)
- LocalStorage keys for user_data bridge (`sb_user_email`, `sb_user_phone`, etc.)

**Key Features:**
```javascript
// Tracking System (v4.0)
1. Analytics tracking -> ALL clicks -> page_analytics.log
2. Customer tracking -> FORM SUBMISSIONS ONLY -> customer_data.log
3. GTM dataLayer -> GA4 standard ecommerce events -> GA4/Meta CAPI

// Pixel Events Fired (direct fallback):
- PageView (auto on load)
- Lead (ORDER NOW pricing click)
- InitiateCheckout (COMPLETE ORDER form submit)
- Purchase (thankyou.php page load)

// dataLayer Events Fired (GA4 standard):
- view_item (pricing section visible / 30% scroll)
- begin_checkout (COMPLETE ORDER click) — ecommerce + user_data
- add_payment_info (same COMPLETE ORDER click) — ecommerce
- purchase (thankyou.php load) — reads from LocalStorage
- generate_lead (WhatsApp CTA click)

// Analytics Events Tracked:
- PAGE_VIEW
- VIEW_ITEM
- SCROLL_DEPTH_25/50/75/100
- ORDER_[PACKAGE]_CLICK
- ORDER_SUBMIT
- CLICK_HEATMAP
- TIME_ON_PAGE
- EXIT_PAGE
- RETURN_VISITOR
- EXTERNAL_LINK_CLICK
```

---

### 2. **analytics.php** - Backend API (NEVER MODIFY)
**Purpose:** Receive & log analytics events  
**Line Count:** 119 lines (13-column format)  
**Customizable:** NO - Copy exact as-is

**What It Does:**
```php
1. Receives POST request from index.php AND thankyou.php
   - event_type (PAGE_VIEW, SCROLL_DEPTH_25, PURCHASE_CLICK, etc)
   - page_url (full URL with UTM params)
   - data (JSON metadata)
   - session_id (user session)

2. Processes data
   - Detect device (Desktop/Mobile/Tablet)
   - Extract UTM params from page_url (6 params)
   - Get IP address & country
   - Allow ALL traffic (no geo-blocking)

3. Writes to page_analytics.log (13 columns TSV)
   Timestamp | Event | Page URL | Data | Device | Session | Campaign | Source | Medium | UTM_Content | Placement | IP | Country
```

**Critical Features:**
- Input sanitization
- IP validation (prevent spoofing)
- Timeout 3 seconds
- UTM parsing from page_url (6 params including utm_content, utm_term & utm_placement)

---

### 3. **log.php** - Dashboard (SHARED ACROSS PAGES)
**Purpose:** Unified dashboard for all landing pages  
**Line Count:** ~3505 lines (v3.1)  
**Customizable:** NO - Single shared file

**What It Does:**
```php
1. Reads customer_data.log -> Customer Tab
   - Shows form submissions (FORM_SUBMIT - with business data)
   - Full UTM attribution (6 params)
   - Follow-up functionality (preserves original status on toggle)
   - CSV export

2. Reads page_analytics.log -> Analytics Tab
   - Page views & engagement
   - Conversion funnel: Page Views → Pricing Clicks (CTR%) → Form Submits → Conversion Rate
   - Conversion Rate = Form Submits (ORDER_SUBMIT) / Page Views (NOT pricing clicks / page views)
   - Scroll depth
   - Device breakdown
   - Time metrics

3. Processes hierarchical data -> Campaign Breakdown Tab
   - Campaign (UTM Campaign) -> Ad (UTM Content)
   - Side-by-side layout
   - Top 5 winning adsets & ads
   - Click-to-select navigation
```

**customer_data.log Status Values (v3.1):**
| Status | Meaning | Source |
|--------|---------|--------|
| `FORM_SUBMIT` | User submitted order form with business data | Order form handler |
| `FOLLOWED_UP` | Admin marked as followed up | Dashboard toggle |

**NOTE (v3.1):** `CLICK_ONLY` status is no longer generated. Existing `CLICK_ONLY` entries in logs remain readable by the dashboard for backward compatibility.

**UTM Default Values (aligned with analytics.php):**
| Parameter | Default (no UTM) |
|-----------|-----------------|
| utm_source | `direct` |
| utm_medium | `none` |
| utm_campaign | `direct` |
| utm_content | `-` |
| utm_term | `-` |
| utm_placement | `-` |

---

### 4. **thankyou.php** - Thank You Page (NEW in v2.9)
**Purpose:** Post-checkout confirmation page that fires Purchase pixel  
**Line Count:** ~522 lines  
**Customizable:** YES (content/design only)

**What It Does:**
```
1. Receives URL params from index.php:
   - pkg (starter/growth/performance)
   - dest (Fanbasis payment URL)
   - biz (business name, URL-encoded)

2. Displays thank you confirmation card:
   - Check icon + Thank You header
   - CLICK HERE TO CONTINUE button (pulsing animation)
   - Smart Buzzer branding footer

3. On button click (handlePurchaseClick):
   - Fires fbq('track', 'Purchase') with value/currency
   - Fires ttq.track('CompletePayment') with value/currency
   - Logs PURCHASE_CLICK to analytics.php
   - 400ms delay for pixel to fire
   - Redirects to Fanbasis payment URL

4. Analytics events tracked:
   - THANKYOU_PAGE_VIEW (on load)
   - PURCHASE_CLICK (on button click)
   - THANKYOU_EXIT (on page unload)
```

**URL Format:**
```
thankyou.php?pkg=growth&dest=https://www.fanbasis.com/agency-checkout/smartbuzzer/Vmv31&biz=My%20Business
```

**URL Parameters:**
| Param | Required | Description | Example |
|-------|----------|-------------|---------|
| `pkg` | Yes | Package key | `starter`, `growth`, `performance` |
| `dest` | Yes | Fanbasis payment URL | `https://www.fanbasis.com/agency-checkout/smartbuzzer/Vmv31` |
| `biz` | No | Business name (URL-encoded) | `My%20Business` |

**Package Mapping (inside thankyou.php):**
| Key | Name | Reviews | Price | Display |
|-----|------|---------|-------|---------|
| `starter` | Starter | 55 | 360.00 | $360.00 |
| `growth` | Growth | 88 | 550.00 | $550 |
| `performance` | Performance | 110 | 660.00 | $660 |

**What You Can Change:**
- Thank you message text
- Button text and styling
- Card design/layout
- Footer branding

**What You CANNOT Change:**
- Facebook Pixel code and ID
- TikTok Pixel code and ID
- Purchase event firing logic
- handlePurchaseClick function
- 400ms redirect delay (required for pixels to fire)
- analytics.php integration
- URL parameter parsing logic

**Security:**
- `pkg` sanitized with `htmlspecialchars()`
- `dest` validated with `filter_var(FILTER_VALIDATE_URL)`
- `biz` decoded then sanitized with `htmlspecialchars()`
- Invalid `dest` falls back to `https://smart-buzzer.com/promo/`
- `handlePurchaseClick` has one-time guard (`tyClickHandled`) to prevent double-fire

---

# ORDER FORM FIELDS (v3.0)

## Form Fields

| Field | ID | Type | Required | Placeholder |
|-------|----|------|----------|-------------|
| Google Business Name | `ofBizName` | text | Yes | Enter your business name |
| WhatsApp Number | `ofWhatsapp` | tel | Yes | +1 Enter your WhatsApp number |
| Email Address | `ofEmail` | email | No | Enter your email address (optional) |
| Package Selection | `of-pkg` radio | radio | Yes | Pre-selected: Growth |

## Form Auto-Save Keys (localStorage)

| Key | Field |
|-----|-------|
| `sb_form_biz` | Business Name |
| `sb_form_wa` | WhatsApp Number |
| `sb_form_email` | Email Address |
| `sb_form_pkg` | Selected Package |

All form fields auto-save on input and restore on page load. Cleared after successful order submission.

---

# CENTRALIZED ANALYTICS SYSTEM (v1.3)

## Overview

```
+-----------------------------------------------------------------------+
|                    CENTRALIZED ANALYTICS                              |
+-----------------------------------------------------------------------+
|                                                                       |
|  /analytics/                                                          |
|  +-- index.php         <- Centralized Dashboard (filesystem fetch)    |
|  +-- sources/          <- Source configs folder (auto-created)        |
|      +-- promo.json    <- {"name":"promo","path":"/home/.../promo"}   |
|      +-- xmas.json     <- {"name":"xmas","path":"/home/.../xmas"}     |
|                                                                       |
|  Reads directly from filesystem (same cPanel):                        |
|  +-- /promo/customer_data.log                                         |
|  +-- /xmas/customer_data.log                                          |
|  +-- /blackfriday/customer_data.log                                   |
|                                                                       |
+-----------------------------------------------------------------------+
```

## Centralized Dashboard Features

| Feature | Description |
|---------|-------------|
| Multi-Source | Aggregate data from 7 default landing pages |
| Filesystem Fetch | Direct file read (no HTTP, no 403 issues) |
| Source Management | Add/Remove/Toggle sources via UI |
| Date Filtering | Default: Last 30 Days |
| Campaign Breakdown | Hierarchical: Campaign (UTM Campaign) -> Ad (UTM Content) |
| Source Filter | View specific LP or all combined |
| Landing Page Table | Breakdown by LP performance |
| Full AJAX | No page refresh needed |
| Password Protection | Secure access |
| Backward Compatible | Supports 14-18 column formats |

## Configuration

```php
// Filesystem base path (same cPanel - no HTTP needed)
$BASE_PATH = '/home/u387681977/domains/smart-buzzer.com/public_html';

// Default landing pages (auto-created on first load)
$DEFAULT_LANDING_PAGES = [
    'promo',
    'promo-2',
    'promo-outbound',
    'promo-b1g1',
    'promo-rating',
    'promo-australia',
    'seasonal',
    'xmas',
    'blackfriday'
];
```

## File Structure

```
/home/u387681977/domains/smart-buzzer.com/public_html/
+-- analytics/
|   +-- index.php              <- Centralized Dashboard (2068 lines)
|   +-- sources/               <- Auto-created folder
|       +-- promo.json         <- Source config with path
|       +-- promo-2.json
|       +-- promo-outbound.json
|       +-- promo-b1g1.json
|       +-- promo-rating.json
|       +-- promo-australia.json
|       +-- xmas.json
|       +-- blackfriday.json
|
+-- promo/
|   +-- index.php              <- Landing page
|   +-- thankyou.php           <- Thank you page
|   +-- analytics.php          <- Analytics backend (13-col)
|   +-- log.php                <- Local dashboard
|   +-- customer_data.log      <- 18 columns (form submissions only)
|   +-- page_analytics.log     <- 13 columns
|   +-- order_clicks.log       <- 7 columns (legacy)
```

## Source Config Format (JSON)

```json
{
    "name": "promo",
    "path": "/home/u387681977/domains/smart-buzzer.com/public_html/promo",
    "url": "https://smart-buzzer.com/promo/",
    "enabled": true
}
```

## How Centralized Fetching Works

```php
// Filesystem fetch (faster, no 403 issues)
$basePath = $source['path'];  // /home/u387681977/.../promo
$customerDataPath = $basePath . '/customer_data.log';

if (file_exists($customerDataPath)) {
    $content = file_get_contents($customerDataPath);
    // Parse 14-18 column format (backward compatible)
}
```

---

## FILE STRUCTURE TEMPLATE (Per Landing Page)

```
/landing-page-folder/
+-- index.php           <- Landing page (customize content)
+-- thankyou.php        <- Thank you page (Purchase pixel)
+-- analytics.php       <- Analytics backend (13-col, copy exact)
+-- log.php             <- Dashboard (copy to same folder)
+-- customer_data.log   <- Auto-created on first form submission (18 cols)
+-- page_analytics.log  <- Auto-created on first analytics event (13 cols)
+-- order_clicks.log    <- Legacy simple click tracking (7 cols)
```

---

## UVP (Unique Value Propositions) - v2.8

### Pricing Card Features (with info icons [i]):
| Feature | Info Icon | Modal Content |
|---------|-----------|---------------|
| (20%) 4-Star + (80%) 5-Star Ratings | Yes | Explains why mixed ratings are safer |
| (70%) Local + (30%) Global Names | Yes | Explains reviewer diversity benefits |
| Human-Written Custom Content | No | - |
| 1-3 Reviews Per Week | No | - |
| Detailed Delivery Report | No | - |
| For X Business Links | No | - |

### WHY PEOPLE USE OUR SERVICES Section:

| # | Title | Description | Border Color |
|---|-------|-------------|--------------|
| 1 | 70% Local + 30% Global Names | Natural mix of local and global reviewers for authenticity | Green |
| 2 | Unique users, IPs, devices, and aged accounts | Complete technical authenticity guaranteed | Blue |
| 3 | Tailored reviews for your business | Custom content that matches your services | Purple |
| 4 | Gradual posting (1-3 Reviews Per Week) | Natural pacing prevents algorithm detection | Orange |

### UVP Modal System (per landing page):

**CSS Classes (unique per page to avoid conflicts):**
- `.info-icon-{suffix}` - Blue/Gold circular [i] button
- `.uvp-modal-{suffix}` - Full-screen overlay
- `.uvp-modal-content-{suffix}` - White/dark card with scrollbar
- `.uvp-modal-close-{suffix}` - Close button

**Suffixes by Landing Page:**
| Landing Page | CSS Suffix | Theme Colors |
|--------------|------------|--------------|
| /promo/ | (none) | Blue |
| /promo-2/ | (none) | Blue |
| /promo-b1g1/ | `-b1g1` | Blue |
| /promo-rating/ | `-rating` | Blue |
| /promo-australia/ | `-australia` | Blue |
| /xmas/ | `-xmas` | Christmas (Emerald/Gold) |
| /blackfriday/ | `-bf` | Black Friday (Black/Gold) |

**Modal IDs:**
- `ratingsModal{Suffix}` - Explains (20%) 4-Star + (80%) 5-Star
- `namesModal{Suffix}` - Explains (70%) Local + (30%) Global Names

**JavaScript Functions:**
```javascript
openUvpModal{Suffix}(modalId)   // Opens modal, locks body scroll
closeUvpModal{Suffix}(modalId)  // Closes modal, restores scroll
// ESC key closes all modals
// Backdrop click closes modal
```

**Ratings Modal Content:**
```
Title: Why (20%) 4-Star + (80%) 5-Star?
- Warning about 100% 5-star being flagged by Google
- Image: https://smart-buzzer.com/photos/hr.webp
- Benefits: More Sticky, More Permanent, Less Drop
```

**Names Modal Content:**
```
Title: Why (70%) Local + (30%) Global Names?
- Explains authentic reviewer diversity
- Benefits: More Genuine, More Natural, More Trustworthy
```

---

## COMPLETE FEATURES LIST

### Landing Page (index.php)
| Feature | Description |
|---------|-------------|
| Facebook Pixel | Auto-track PageView (ID: 938738044322271) |
| TikTok Pixel | Auto-track page (ID: D25JHKBC77UF6R3NPOGG) |
| GTM Container | GTM-WJ6ZK3MR with dataLayer pushes (primary) |
| Lead Pixel | Fires on ORDER NOW pricing button click |
| InitiateCheckout Pixel | Fires on COMPLETE ORDER form submit |
| dataLayer view_item | GA4 standard — pricing section visible / 30% scroll |
| dataLayer begin_checkout | GA4 standard — COMPLETE ORDER click + user_data |
| dataLayer add_payment_info | GA4 standard — same COMPLETE ORDER click |
| dataLayer generate_lead | GA4 — WhatsApp CTA clicks |
| LocalStorage Bridge | Persist user_data for purchase event on thankyou.php |
| Customer Logging | Form submissions only to customer_data.log (v3.1+) |
| UTM Capture | 6 params: source, medium, campaign, content, term, placement |
| Email Field | Optional email field in order form |
| Form Auto-Save | Business name, WhatsApp, email, package auto-saved |
| Scroll Tracking | 25%, 50%, 75%, 100% depth |
| Click Heatmap | X,Y coordinates on interactive elements |
| Time Tracking | Duration on page |
| Return Visitor | Cookie-based detection |
| Exit Tracking | Last clicked link URL and time spent |
| Smooth Scroll | Anchor link navigation (with history.pushState for back button) |
| Image Modal | Click-to-zoom preview |
| Countdown Timer | Daily reset urgency |
| UVP Modals | Info popups for ratings & names (scrollable) |
| Mobile Responsive | Optimized for all devices |

### Thank You Page (thankyou.php)
| Feature | Description |
|---------|-------------|
| Facebook Pixel | PageView (auto) + Purchase (page load) |
| TikTok Pixel | page (auto) + CompletePayment (page load) |
| GTM Container | GTM-WJ6ZK3MR — fires purchase dataLayer on load |
| dataLayer purchase | Reads user_data from LocalStorage, fires GA4 purchase |
| LocalStorage Read | Gets user_data set at begin_checkout, clears after use |
| Purchase Tracking | Fires on page load (not button click) |
| Analytics Events | THANKYOU_PAGE_VIEW, PURCHASE_CLICK, THANKYOU_EXIT |
| One-time Guard | Prevents double-fire on rapid clicks |
| Redirect Delay | 400ms delay ensures pixels fire before redirect |
| URL Param Parsing | pkg, dest, biz from index.php redirect |
| Input Sanitization | htmlspecialchars + FILTER_VALIDATE_URL |
| Session Continuity | Shares session ID with index.php via sessionStorage |

### Local Dashboard (log.php)
| Feature | Description |
|---------|-------------|
| Customer Tab | View form submissions with full UTM attribution |
| Analytics Tab | Page views, scroll depth, device breakdown |
| Campaign Breakdown | Hierarchical: Campaign (UTM Campaign) -> Ad (UTM Content) |
| Side-by-Side View | Campaign list + winning breakdown |
| Top 5 Rankings | Best adsets & ads per campaign |
| Date Filter | Filter by date range |
| AJAX Search | Live search across all fields |
| CSV Export | Export filtered data |
| Follow-up Toggle | Mark customers as followed up (preserves original status) |
| Delete Function | Remove selected entries |
| Format Detection | Supports 14-18 column formats |
| Status Tracking | FORM_SUBMIT / FOLLOWED_UP (legacy CLICK_ONLY still readable) |

### Analytics Backend (analytics.php)
| Feature | Description |
|---------|-------------|
| Event Logging | All events to page_analytics.log (13 cols) |
| Device Detection | Desktop/Mobile/Tablet |
| UTM Parsing | Extract 6 params from page_url |
| IP Validation | Prevent spoofing |
| Geo Detection | Country via ip-api.com |
| Input Sanitization | XSS prevention + TSV corruption prevention |
| Allow All Traffic | No geo-blocking (all countries pass through) |

### Centralized Dashboard (/analytics/index.php)
| Feature | Description |
|---------|-------------|
| Multi-Source | Aggregate from 7 default LPs |
| Filesystem Fetch | Direct file read (no HTTP/403) |
| Source Management | Add/Remove/Toggle via UI |
| Default 30 Days | Auto-load Last 30 Days on open |
| Source Filtering | View specific LP or all |
| Landing Page Table | Breakdown by LP performance |
| Campaign Breakdown | Combined data from all sources |
| Password Protection | Secure access |
| Full AJAX | No page refresh needed |
| 18-Column Support | Compatible with new format |
| Backward Compatible | Supports 14-15 column format |

---

## LOG FILE FORMATS

### customer_data.log (18 columns)
```
Timestamp | Business | Location | Email | WhatsApp | Package | Page URL | Reviews Qty | UTM_Source | UTM_Medium | UTM_Campaign | UTM_Content | Placement | State | Zip | Country | Status | UTM_Term
```

| Index | Field | Example |
|-------|-------|---------|
| 0 | Timestamp | 2025-12-30 14:23:45 |
| 1 | Business | My Business Name |
| 2 | Location | - |
| 3 | Email | john@example.com |
| 4 | WhatsApp | +1234567890 |
| 5 | Package | Growth |
| 6 | Page URL | https://site.com/promo/?utm_... |
| 7 | Reviews Qty | 88 |
| 8 | UTM_Source | meta |
| 9 | UTM_Medium | paid_social |
| 10 | UTM_Campaign | Aul_US_LP_PROMO_V1 |
| 11 | UTM_Content | kv_localized-copy |
| 12 | Placement | Facebook_Mobile_Reels |
| 13 | State | CA |
| 14 | Zip | 90210 |
| 15 | Country | US |
| 16 | Status | FORM_SUBMIT / FOLLOWED_UP |
| 17 | UTM_Term | LAA_ICO_L150D |

### Status Column Values (Index 16) - v3.1

| Value | Meaning | When Set |
|-------|---------|----------|
| `FORM_SUBMIT` | Full form submission with business name + WhatsApp | COMPLETE ORDER click |
| `FOLLOWED_UP` | Admin toggled follow-up in dashboard | Dashboard toggle |

**NOTE:** `CLICK_ONLY` is no longer generated as of v3.1. Legacy entries with `CLICK_ONLY` status remain in existing logs and are still readable by the dashboard.

**Status Transition Rules:**
```
FORM_SUBMIT → FOLLOWED_UP (admin toggles ON)
FOLLOWED_UP → FORM_SUBMIT (admin toggles OFF, restores original)
```
Detection logic for legacy data: If business name (col 1) is `-`, original status was `CLICK_ONLY`. Otherwise `FORM_SUBMIT`.

### page_analytics.log (13 columns)
```
Timestamp | Event | Page URL | Data | Device | Session | Campaign | Source | Medium | UTM_Content | Placement | IP | Country
```

| Index | Field | Example |
|-------|-------|---------|
| 0 | Timestamp | 2025-12-30 14:23:45 |
| 1 | Event | PAGE_VIEW |
| 2 | Page URL | https://site.com/promo/?utm_... |
| 3 | Data | {} |
| 4 | Device | Desktop |
| 5 | Session | session_123456 |
| 6 | Campaign | LAA |
| 7 | Source | meta_under_SB |
| 8 | Medium | Aul_US_LP_BAU |
| 9 | UTM_Content | video_winning |
| 10 | Placement | Facebook_Mobile_Reels |
| 11 | IP | 192.168.1.1 |
| 12 | Country | US |

### Analytics Event Types (page_analytics.log)

| Event | Source File | Description |
|-------|-----------|-------------|
| `PAGE_VIEW` | index.php | Landing page loaded |
| `RETURN_VISITOR` | index.php | Return visit detected |
| `SCROLL_DEPTH_25` | index.php | Scrolled 25% |
| `SCROLL_DEPTH_50` | index.php | Scrolled 50% |
| `SCROLL_DEPTH_75` | index.php | Scrolled 75% |
| `SCROLL_DEPTH_100` | index.php | Scrolled 100% |
| `ORDER_STARTER_CLICK` | index.php | Starter pricing button click |
| `ORDER_GROWTH_CLICK` | index.php | Growth pricing button click |
| `ORDER_PERFORMANCE_CLICK` | index.php | Performance pricing button click |
| `ORDER_SUBMIT` | index.php | Order form submitted |
| `CLICK_HEATMAP` | index.php | Interactive element clicked |
| `TIME_ON_PAGE` | index.php | Page unload with duration |
| `EXIT_PAGE` | index.php | Page exit with last clicked URL |
| `EXTERNAL_LINK_CLICK` | index.php | Header/footer external link |
| `THANKYOU_PAGE_VIEW` | thankyou.php | Thank you page loaded |
| `PURCHASE_CLICK` | thankyou.php | Continue button clicked |
| `THANKYOU_EXIT` | thankyou.php | Thank you page unload |

### order_clicks.log (7 columns) - Legacy
```
Timestamp | Page URL | Action | UTM_Campaign | UTM_Source/Medium | Country | IP
```

---

## TRAFFIC POLICY

**All traffic is allowed.** There is no geo-blocking on any file. All countries including Indonesia pass through analytics.php, log.php, and thankyou.php without filtering.

---

## CRITICAL IMPLEMENTATION RULES

### !! ABSOLUTE RULE — NO CLICK TRACKING IN customer_data.log (ALL LPs) !!
**customer_data.log records FORM SUBMISSIONS ONLY.**
- NEVER write a row to `customer_data.log` when user clicks a pricing button (ORDER NOW)
- NEVER write a row to `customer_data.log` when there is no business name + WhatsApp submitted
- Any row with Business Name = `-` and Status = `CLICK_ONLY` is a legacy bug — do not reproduce
- This applies to ALL landing pages: `/promo/`, `/promo-b1g1/`, `/seasonal/`, `/promo-2/`, `/promo-outbound/`, `/promo-australia/`, `/promo-california/`, `/promo-rating/`, `/xmas/`, `/blackfriday/`, and any future LP
- Pricing button clicks are tracked ONLY via: FB `Lead` pixel, TikTok `SubmitForm`, GTM `generate_lead` dataLayer, and `ORDER_*_CLICK` event to `page_analytics.log`
- If any LP still has `log_pricing_click` fetch call to `log.php` — REMOVE IT immediately

### DO NOT:
1. Use `window.location.pathname` for analytics (use `.href`)
2. Parse UTM from `HTTP_REFERER` (use `page_url` from frontend)
3. Add validation alert for WhatsApp number
4. Use auto-refresh (`setTimeout` reload)
5. Use emojis in Follow Up button
6. Modify `log.php` when creating new landing pages
7. Hard-code state/zip code (must auto-detect)
8. Forget Facebook Pixel on new pages
9. Use different Pixel ID (must be 938738044322271)
10. Modify log files manually
11. Edit `analytics.php` (copy exact as-is)
12. Change `data-package` attributes on ORDER buttons
13. Use checkmark emoji directly (use `&#10003;` HTML entity)
14. Mix CSS class names across landing pages (use unique suffixes)
15. Skip thankyou.php when creating new landing pages
16. Remove the 400ms delay in thankyou.php (pixels need time to fire)
17. Triple-decode UTM params (use `parse_url` + `parse_str` only, no extra `urldecode`)
18. Write user input to TSV without sanitizing tabs/newlines
19. Remove or modify GTM container code (`GTM-WJ6ZK3MR`)
20. Remove `dataLayer.push({ecommerce: null})` before ecommerce pushes
21. Change package metadata IDs — `/promo/`: `pkg_starter_55`, `pkg_growth_88`, `pkg_performance_110` | `/promo-b1g1/`: `pkg_booster_65`, `pkg_dominator_120`
22. Remove `typeof fbq !== 'undefined'` or `typeof ttq !== 'undefined'` guards
29. Add direct `fbq('init', ...)` Meta Pixel code on new landing pages — GTM already fires FB PageView and all pixel events. Direct init causes **duplicate events**. Only add a comment: `<!-- Meta Pixel: managed via GTM -->`
23. Log pricing button clicks to customer_data.log
24. Create duplicate/redundant GTM events per page — use one unified event set, differentiate by `page_location`
25. Fire Purchase pixel on button click on thankyou.php — fire on page load instead
26. Rely on third-party gateway (Fanbasis/Xendit) for user_data — capture upstream on LP
27. Push `dataLayer.push` inside events that fail form validation (no ghost conversions)
28. Use old custom event names (`select_item`, `initiate_checkout`) — use GA4 standard names
30. Use the phrases "Zero account bans", "ZERO account bans", "0 account bans", "no account bans", or any variation claiming guaranteed zero bans — this is a compliance risk. Instead say: "gradual delivery built to last", "natural posting patterns", "designed to stay compliant", or "1-3 reviews per week for natural growth"

### ALWAYS DO:
1. Use `window.location.href` for analytics (captures UTM)
2. Parse UTM from `$data['page_url']` in analytics.php
3. Use exact Facebook Pixel code provided (ID: 938738044322271)
4. Use exact TikTok Pixel code provided (ID: D25JHKBC77UF6R3NPOGG)
5. Auto-detect state/zip from IP
6. Accept any WhatsApp input (no validation)
7. Use proper button spacing (8px 16px)
8. Remove emojis from buttons
9. Test form submission logs to file
10. Add `data-package` attribute to ORDER buttons
11. Include analytics tracking code
12. Copy `analytics.php` exact (no changes)
13. Use unique CSS classes per landing page (suffixes)
14. Use HTML entities for special characters (`&#10003;` not checkmark)
15. Match CSS variables to page theme (check `:root` variables)
16. Include thankyou.php in every landing page folder
17. Fire Lead pixel on ORDER NOW click
18. Fire InitiateCheckout pixel on COMPLETE ORDER submit
19. Fire Purchase pixel on thankyou.php PAGE LOAD (not button click)
20. Use `typeof fbq !== 'undefined'` guard before firing pixels
21. Use `typeof ttq !== 'undefined'` guard before firing TikTok pixels
22. Sanitize TSV inputs with `str_replace(["\t", "\n", "\r"], '', $value)`
23. Use consistent UTM defaults: `direct`/`none`/`direct`/`-`/`-`
24. Always write 18 columns to customer_data.log (include Status + UTM_Term columns)
25. Push `dataLayer view_item` when pricing section becomes visible (30% scroll or element visibility)
26. Push `dataLayer begin_checkout` on COMPLETE ORDER click with ecommerce + user_data
27. Push `dataLayer add_payment_info` on same COMPLETE ORDER click (after begin_checkout)
28. Save user_data to LocalStorage at begin_checkout (bridge for purchase event)
29. Push `dataLayer purchase` on thankyou.php page load — read user_data from LocalStorage
30. Clear LocalStorage after purchase event fires
31. Clear ecommerce with `dataLayer.push({ecommerce: null})` before each ecommerce push
32. Generate unique `transaction_id` per session (`SB_{timestamp}_{random6}`)
33. Include email field (`ofEmail`) in order form (optional, no validation)
34. Auto-save and restore all form fields including email
35. Only log form submissions (FORM_SUBMIT) to customer_data.log, not pricing clicks
36. Add `item_category: 'Google Reviews'` to all ecommerce items
37. Install GTM on BOTH index.php and thankyou.php
38. Conversion Rate in dashboard = Form Submits (ORDER_SUBMIT) / Page Views — NOT pricing clicks / page views
39. Funnel in dashboard must show 4 steps: Page Views → Pricing Clicks (CTR%) → Form Submits (COMPLETE ORDER) → Conversion Rate

---

## FACEBOOK PIXEL (MANDATORY)

**Pixel ID:** `938738044322271`

```html
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '938738044322271');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=938738044322271&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
```

**Required on:** index.php, thankyou.php

---

## TIKTOK PIXEL (MANDATORY)

**Pixel ID:** `D25JHKBC77UF6R3NPOGG`

```html
<!-- TikTok Pixel Code Start -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(
var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script")
;n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
  ttq.load('D25JHKBC77UF6R3NPOGG');
  ttq.page();
}(window, document, 'ttq');
</script>
<!-- TikTok Pixel Code End -->
```

**Required on:** index.php, thankyou.php

---

## UTM TRACKING SYSTEM

### UTM Parameters Captured (6 params) — Standard Convention:
```
utm_source    -> Platform (e.g., meta)
utm_medium    -> Medium (e.g., paid_social)
utm_campaign  -> Campaign Name (e.g., Aul_US_LP_PROMO_V1)
utm_content   -> Ad Content (e.g., kv_localized-copy)
utm_term      -> Adset Name (e.g., LAA_ICO_L150D)
utm_placement -> Placement (e.g., Facebook_Mobile_Reels)
```

### UTM to FB Ads Mapping:
| UTM Parameter | FB Ads Equivalent | Example |
|---------------|-------------------|---------|
| utm_source | Platform | meta |
| utm_medium | Medium Type | paid_social |
| utm_campaign | Campaign Name | Aul_US_LP_PROMO_V1 |
| utm_content | Ad Content | kv_localized-copy |
| utm_term | Adset Name | LAA_ICO_L150D |
| utm_placement | Placement | Facebook_Mobile_Reels |

### Standard FB Ads UTM Template:
```
utm_source=meta&utm_medium=paid_social&utm_campaign={{campaign.name}}&utm_content={{ad.name}}&utm_term={{adset.name}}&utm_placement={{placement}}
```

### UTM Default Values (consistent across all files):
| Parameter | Default Value | Meaning |
|-----------|--------------|---------|
| utm_source | `direct` | No UTM source detected |
| utm_medium | `none` | No medium detected |
| utm_campaign | `direct` | No campaign detected |
| utm_content | `-` | No ad content |
| utm_term | `-` | No adset name |
| utm_placement | `-` | No placement data |

### UTM Parsing Method (consistent across all files):
```php
// CORRECT - parse_url + parse_str only (parse_str auto-decodes)
$parsed = parse_url($pageUrl);
if (isset($parsed['query'])) {
    parse_str($parsed['query'], $params);
    $utmSource = isset($params['utm_source']) ? trim($params['utm_source']) : 'direct';
}

// WRONG - triple decode corrupts encoded values
$decodedUrl = urldecode($pageUrl);          // decode #1
parse_str($parsed['query'], $params);        // decode #2 (auto)
$utmSource = urldecode($params['utm_source']); // decode #3 - WRONG!
```

### CRITICAL - Always use:
```javascript
// CORRECT
page_url: window.location.href  // Full URL with UTM params

// WRONG - UTM will be lost!
page_url: window.location.pathname  // Just "/promo/"
```

---

## HEADER & NAVIGATION

**Logo:** Clickable ke `https://smart-buzzer.com/`

**Navigation Links:**
- Pricing -> `#pricing`
- How It Works -> `#how-it-works`
- Our Reviews -> `#reviews`

---

## FOOTER STRUCTURE

### Section 1 - Brand:
```html
<img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" height="50px">
<p>Specialized in social media engagement, product reviews, and online reputation services.</p>
<p><i>A subsidiary of Pintarnya.</i></p>
```

### Section 2 - Quick Links (3 links only):
- Track Campaign -> `https://smart-buzzer.com/tracker`
- Report Issue -> `https://smart-buzzer.com/report`
- Terms & Conditions -> `https://smart-buzzer.com/service-tnc`

### Section 3 - Contact:
- **WhatsApp: +6285-7731-16557**
- Email: contact@smart-buzzer.com

---

## DEPLOYMENT CHECKLIST

### Per Landing Page (/promo/, /promo-b1g1/, /seasonal/, etc.):
```
[ ] Facebook Pixel installed (ID: 938738044322271)
[ ] TikTok Pixel installed (ID: D25JHKBC77UF6R3NPOGG)
[ ] GTM container installed (ID: GTM-WJ6ZK3MR) - on index.php AND thankyou.php
[ ] Lead pixel fires on ORDER NOW click
[ ] dataLayer view_item fires on pricing section visible / 30% scroll
[ ] dataLayer begin_checkout fires on COMPLETE ORDER click (with user_data)
[ ] dataLayer add_payment_info fires on COMPLETE ORDER click (same click, after begin_checkout)
[ ] user_data saved to LocalStorage at begin_checkout
[ ] InitiateCheckout pixel fires on COMPLETE ORDER submit
[ ] Form submission logs to customer_data.log (FORM_SUBMIT only)
[ ] analytics.php uploaded (13-col format, exact copy)
[ ] log.php uploaded to same folder
[ ] thankyou.php uploaded to same folder
[ ] GTM installed on thankyou.php
[ ] dataLayer purchase fires on thankyou.php PAGE LOAD (reads LocalStorage)
[ ] Purchase pixel fires on thankyou.php page load
[ ] LocalStorage cleared after purchase event fires
[ ] data-package attributes on ORDER buttons
[ ] UTM tracking uses window.location.href
[ ] State & Zip auto-detect working
[ ] All 6 UTM parameters captured
[ ] Dashboard shows all 3 tabs (Customers, Analytics, Campaign Breakdown)
[ ] Email field present in order form (optional)
[ ] Form auto-save working (biz, wa, email, pkg)
[ ] Mobile responsive tested
[ ] UVP modals working (ratings & names)
[ ] Info icons [i] visible and clickable
[ ] Unique CSS class suffix used (avoid conflicts)
[ ] HTML entities used for checkmarks (&#10003;)
[ ] COMPLETE ORDER redirects to thankyou.php?pkg=...&dest=...&biz=...
[ ] Fanbasis dest links are correct per package
[ ] generate_lead fires on WhatsApp CTA clicks
[ ] item_category: 'Google Reviews' included in all ecommerce items
```

### Centralized Analytics (/analytics/):
```
[ ] index.php uploaded (v1.3 filesystem version)
[ ] sources/ folder will auto-create with 7 defaults
[ ] BASE_PATH configured correctly
[ ] Password set correctly
[ ] Default 30 Days loads on open
[ ] Test button shows clicks or Not found
[ ] Campaign breakdown displaying
[ ] Landing Page table displaying
[ ] Full AJAX working (no page refresh)
```

---

## ACTIVE LANDING PAGES

| Landing Page | URL | Status | Theme | Packages | Default Pkg | Thank You Page |
|--------------|-----|--------|-------|----------|-------------|----------------|
| Main Site | / | Active | WordPress | - | - | - |
| Product Page | /product/google-reviews/ | Active | WordPress | starter/growth/performance | - | - |
| promo | /promo/ | Active | Blue | starter ($360.00) / growth ($550) / performance ($660) | growth | /promo/thankyou.php |
| promo-2 | /promo-2/ | Active | Blue | starter ($360.00) / growth ($550) / performance ($660) | growth | /promo-2/thankyou.php |
| promo-outbound | /promo-outbound/ | Active | Blue (Outbound Email) | starter ($360.00) / growth ($550) / performance ($660) | growth | /promo-outbound/thankyou.php |
| promo-b1g1 | /promo-b1g1/ | Active | Blue (B1G1) | booster 65r ($380) / dominator 120r ($680) | dominator | /promo-b1g1/thankyou.php |
| promo-rating | /promo-rating/ | Active | Blue (Rating only) | starter/growth/performance | growth | /promo-rating/thankyou.php |
| promo-australia | /promo-australia/ | Active | Blue (AU market) | starter/growth/performance | growth | /promo-australia/thankyou.php |
| promo-california | /promo-california/ | Active | Coastal Blue + Sunset Gold (CA market) | starter ($360.00) / growth ($550) / performance ($660) | growth | /promo-california/thankyou.php |
| promo-tripadvisor | /promo-tripadvisor/ | Active | TripAdvisor Green (#00AA6C) | starter 28r ($250) / growth 35r ($300) / performance 50r ($400) | growth | /promo-tripadvisor/thankyou.php |
| promo-trustpilot | /promo-trustpilot/ | Active | Trustpilot Green (#05B67A) | starter ($357.50) / growth ($550) / performance ($660) | growth | /promo-trustpilot/thankyou.php |
| seasonal | /seasonal/ | Planned | TBD | TBD | TBD | /seasonal/thankyou.php |
| xmas | /xmas/ | Active | Christmas (Emerald/Gold) | starter/growth/performance | growth | /xmas/thankyou.php |
| blackfriday | /blackfriday/ | Active | Black Friday (Black/Gold) | starter/growth/performance | growth | /blackfriday/thankyou.php |

**Primary LP:** https://smart-buzzer.com/promo/
**Main Site:** https://smart-buzzer.com/

### CSS Suffix Reference (updated)
| Landing Page | CSS Suffix |
|--------------|------------|
| /promo/ | (none) |
| /promo-2/ | (none) |
| /promo-outbound/ | (none) — 1:1 clone of /promo/ |
| /promo-b1g1/ | `-b1g1` |
| /promo-rating/ | `-rating` |
| /promo-australia/ | `-australia` |
| /promo-california/ | `-california` (uses `.ca-*` prefixed classes for new sections) |
| /seasonal/ | `-seasonal` |
| /xmas/ | `-xmas` |
| /blackfriday/ | `-bf` |

---

## PRODUCT DESCRIPTION (for SKU/Google Shopping)

```
Trusted review provider for over 1200+ businesses.

Our service includes:
- (20%) 4-Star + (80%) 5-Star Ratings - Natural mix for authenticity
- (70%) Local + (30%) Global Names - Authentic reviewer diversity
- Human-written custom content tailored to your business
- Gradual posting (1-3 Reviews/Week) - Natural pacing, not bulk
- Unique users, IPs, devices, and aged accounts
- 7-day replacement guarantee
- Detailed delivery report provided

We ensure high quality and help keep your business safe.

NOTE: Our minimum purchase is $350. This is not limited to Google Reviews - you can mix and match with all our products.
If you want to buy only Google Reviews, a minimum of 55 reviews is required.

ORDER NOW: https://smart-buzzer.com/promo/
CONTACT: +6285-7731-16557 (WhatsApp)
```

---

## HELPER SCRIPTS

### fix_sources.php
**Purpose:** Regenerate sources with correct filesystem paths  
**Location:** Upload to `/analytics/` temporarily  
**Usage:** Run once, then DELETE

### check_paths.php
**Purpose:** Verify log file locations across all landing pages  
**Location:** Upload to `/analytics/` temporarily  
**Usage:** Run once to check paths, then DELETE

---

## FILE LINE COUNTS (v3.1)

```
/promo/
  index.php:              ~4081 lines
  thankyou.php:           ~522 lines
  log.php:                ~3505 lines
  analytics.php:          119 lines (13-col format)

/analytics/
  index.php:              2068 lines (v1.3 filesystem)
```

---

## QUICK REFERENCE

| Item | Value |
|------|-------|
| **Main Site** | https://smart-buzzer.com/ |
| **Primary LP** | https://smart-buzzer.com/promo/ |
| **B1G1 LP** | https://smart-buzzer.com/promo-b1g1/ |
| **WhatsApp** | +6285-7731-16557 |
| **Email** | contact@smart-buzzer.com |
| **Facebook Pixel ID** | 938738044322271 |
| **TikTok Pixel ID** | D25JHKBC77UF6R3NPOGG |
| **GTM Container ID** | GTM-WJ6ZK3MR |
| **Logo URL** | https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png |

### Package ID Quick Reference
| LP | Package Key | Item ID | Reviews | Price |
|----|-------------|---------|---------|-------|
| /promo/ | `starter` | `pkg_starter_55` | 55 | $360.00 |
| /promo/ | `growth` | `pkg_growth_88` | 88 | $550 |
| /promo/ | `performance` | `pkg_performance_110` | 110 | $660 |
| /promo-b1g1/ | `booster` | `pkg_booster_65` | 65 | $380 |
| /promo-b1g1/ | `dominator` | `pkg_dominator_120` | 120 | $680 |

### Fanbasis URL Quick Reference
| LP | Package | Fanbasis URL Slug |
|----|---------|------------------|
| /promo/ | starter | `Rgl3R` |
| /promo/ | growth | `Vmv31` |
| /promo/ | performance | `WnP3g` |
| /promo-b1g1/ | booster | `Rgl3R` |
| /promo-b1g1/ | dominator | `wpk38` |

---

## OUTBOUND LEAD SCRAPER (/outbound/)

Internal B2B lead-generation tool that scrapes Google Maps via Apify. Completely separate from the customer-facing landing pages — does NOT use GTM, pixels, customer_data.log, or the standard LP analytics stack.

### Overview
| Item | Value |
|------|-------|
| **URL** | https://smart-buzzer.com/outbound/ |
| **Password** | `smartbuzzer2025` (session-based, stored in `$_SESSION['ob_auth']`) |
| **Target market** | US small-medium businesses (HVAC, dental, restaurants, contractors, etc.) |
| **Use case** | Find prospect leads (business name, email, phone, website) for cold outreach campaigns |
| **Apify Actor** | Google Maps Scraper (`nwua9Gu5YrADL7ZDj`) |
| **Plan budget** | $199/mo (live usage shown in header badge) |

### Apify Credentials (server-side only — never expose to frontend)
| Constant | Value |
|----------|-------|
| `APIFY_TOKEN` | `apify_api_S8VBUZeyfA9ehMNHZNtduhKiy34j0X14FMf7` |
| `APIFY_ORG_ID` | `2HSXfO6L1LvcRjPML` |
| `ACTOR_ID` | `nwua9Gu5YrADL7ZDj` |
| `WEBHOOK_URL` | `https://smart-buzzer.com/outbound/webhook.php` |

### File Structure
```
/outbound/
├── index.php       # Dashboard UI (3 tabs) — ~628 lines, password-protected
├── api.php         # Backend (9 actions) — ~454 lines
├── webhook.php     # Apify webhook receiver — 50 lines
└── data/           # Auto-created, .htaccess protected
    ├── leads_db.json         # Master deduplicated lead database (placeId-keyed)
    ├── run_<runId>.json      # Per-run metadata (status, cost, counts)
    └── webhook_<runId>.json  # Raw webhook payloads from Apify
```

### Three-File Architecture
- **index.php** — Frontend dashboard. Login screen + 3 tabs (New Scrape, Leads Database, Run History). All AJAX to `api.php`. Polls `?action=status` every 5s during active runs. Theme toggle (light/dark) persisted in localStorage.
- **api.php** — REST-ish backend. Single entry point with `?action=` switch. Talks to Apify API, manages local JSON files, handles dedup.
- **webhook.php** — One-way receiver for Apify run completion events. Writes raw payload to `data/webhook_<runId>.json`. Webhook is auto-registered per run by `setupWebhook()` in api.php.

### API Actions (api.php `?action=`)
| Action | Method | Purpose |
|--------|--------|---------|
| `start` | POST | Launch new Apify scrape — body: `{searchTerms, maxResults, language, country, state, city, location}` |
| `status` | GET | Poll Apify run status, update local run file with cost — `?runId=` |
| `results` | GET | Fetch dataset items, extract lead schema, **auto-save to leads_db.json** — `?datasetId=` or `?runId=` |
| `runs` | GET | List last 50 runs from `data/run_*.json` (sorted desc by `startedAt`) |
| `limits` | GET | Apify plan + monthly USD usage (for header badge + progress bar) |
| `leads` | GET | Read leads DB with optional filters: `?state=`, `?category=`, `?search=` |
| `stats` | GET | Aggregated counters: totalRuns, totalLeads, totalEmails, totalCostUsd, avgCostPerRun |
| `save_leads` | POST | Manual save batch to DB (dedup by placeId) — body: `{leads, runId}` |
| `clear_leads` | POST | Wipe `leads_db.json` (writes `[]`) |

### Apify Actor Input Mapping
What `handleStart()` sends to Apify:
```json
{
  "searchStringsArray": ["HVAC contractor", "..."],
  "maxCrawledPlacesPerSearch": 100,
  "language": "en",
  "deeperCityScrape": true,
  "includeWebResults": false,
  "scrapeContacts": true,
  "scrapeDirectories": false,
  "geolocation": {"country": "United States", "state": "California", "city": "..."},
  "locationQuery": "California, United States"
}
```
- If `location` field contains a Google Maps URL (`google.com/maps` or `maps.app.goo.gl`), it becomes `startUrls` instead of `locationQuery`.
- `maxResults` capped at 1000 per run (cost control).

### Lead Schema (extracted by `extractLead()`)
Each lead row contains: `title`, `categoryName`, `categories[]`, `address`, `neighborhood`, `city`, `state`, `zipCode`, `countryCode`, `phone`, `phoneUnformatted`, `website`, `email`, `totalScore`, `reviewsCount`, `url` (Maps URL), `placeId`, `price`, `permanentlyClosed`, `temporarilyClosed`, `facebook`, `instagram`, `twitter`, `linkedin`, `youtube`, `tiktok`, `claimThisBusiness`, plus auto-added `savedAt` and `fromRunId`.

**Email extraction (`extractEmail()`)** — checks in order: `item.email` → `item.contacts[].value` (where type=email) → `item.emails[0]`.

### Dedup Strategy
- **Primary key:** `placeId` (unique Google Maps identifier)
- **Fallback key:** `title + '_' + phone` (when placeId missing)
- **Where applied:** `autoSaveLeads()` (after every `results` call) AND `handleSaveLeads()` (manual save)
- **Concurrency:** All writes to `leads_db.json` use `flock(LOCK_EX)` to prevent race conditions

### UI Tabs (index.php)
1. **New Scrape** — Industry templates (16 verticals) + state pills + city + max results + language. Clicking "Start Scraping" calls `?action=start` and begins 5s polling. Live results table renders on completion.
2. **Leads Database** — Master view of `leads_db.json` with industry/state/search filters. CSV export (all columns or emails-only).
3. **Run History** — Tabular list of all runs with totals row (results, emails, cost). Click "View" jumps to Tab 0 and loads that run's results.

### Industry Templates (16 verticals — `TPL` constant in index.php)
`hvac`, `construction`, `restaurant`, `auto_repair`, `roofing`, `auto_dealer`, `accounting`, `healthcare`, `dental`, `fnb`, `locksmith`, `salon`, `landscaping`, `plumber`, `lawyer`, `realtor`

Each template pre-fills 3-5 search terms. Used templates persist in localStorage (`sb_tpl`) and show a green checkmark.

### Auto-Save Flow (the critical path)
Every successful `?action=results` call:
1. Fetches Apify dataset items (max 5000 per call, paginated by offset)
2. Maps each item → lead schema via `extractLead()`
3. Updates `run_<runId>.json` with `resultsCount` + `emailsCount`
4. Calls `autoSaveLeads($leads, $runId)` — appends NEW leads only (placeId dedup)
5. Each new lead gets `savedAt` (timestamp) + `fromRunId` (provenance)

### Webhook Flow
- `setupWebhook($runId)` is called inside `handleStart()` — registers a webhook scoped to that specific runId
- Events watched: `ACTOR.RUN.SUCCEEDED`, `ACTOR.RUN.FAILED`, `ACTOR.RUN.ABORTED`
- Payload arrives at `webhook.php` → saved to `data/webhook_<runId>.json` as raw archive
- Webhook is **passive only** — does NOT trigger result fetching. The dashboard's 5s polling loop is what actually pulls results.

### CRITICAL RULES (/outbound/ specific)
1. **NEVER** expose `APIFY_TOKEN` to frontend — keep it server-side in api.php constants only
2. **NEVER** change the dedup key from `placeId` (would corrupt all existing dedup state in leads_db.json)
3. **NEVER** remove `flock(LOCK_EX)` on leads_db.json writes (concurrent runs will corrupt the JSON)
4. **NEVER** skip the `setupWebhook()` call in `handleStart()` (loses run completion signal)
5. **NEVER** treat /outbound/ as a customer-facing landing page — it does NOT use GTM, FB Pixel, TikTok Pixel, customer_data.log, page_analytics.log, or thankyou.php
6. **NEVER** commit `data/` folder contents to git (contains scraped lead data + run metadata)
7. **DO NOT** raise `maxResults` cap above 1000 without budget review (each run bills against $199/mo Apify plan)
8. **DO NOT** modify `extractLead()` field names — the dashboard frontend depends on them
9. **DO NOT** add geo-blocking — the scraper is for US/CA/AU markets but the operator can be anywhere
10. **ALWAYS** preserve `data/.htaccess` (denies public HTTP access to the JSON files)

### Cost Reference
- Avg ~$1.67 per run (varies by `maxResults` and result count per search term)
- Header badge shows live monthly usage vs $199 plan limit
- Color thresholds: green <70%, amber 70-90%, red >90%

### CSV Export Formats
- **Export Emails** (selected only) — `Business, Email, Phone, Category, City, State` (or extended for current-run export)
- **Export CSV** (all rows) — full lead schema including socials, address, placeId

### Differences From Landing Pages
| Landing Pages (`/promo/` etc.) | /outbound/ |
|--------------------------------|------------|
| Customer-facing | Internal team tool (password-protected) |
| Logs `customer_data.log` (TSV, 18 cols) | Logs `leads_db.json` (JSON array) |
| Uses GTM + FB + TikTok pixels | NO tracking pixels (internal use) |
| UTM attribution required | No UTM concept |
| Has thankyou.php + Fanbasis flow | No payment flow |
| Geo-blocking? No | Geo-blocking? No |

---

## NEW SEASONAL CAMPAIGN WORKFLOW

When asked to create a new seasonal campaign (e.g., "mid-year sale", "back to school", "black friday"), **always create a new dedicated folder** — never overwrite or reuse `/seasonal/`.

### Folder Naming Convention
Use lowercase, hyphenated, theme-based names:
| Campaign | Folder |
|----------|--------|
| Mid-Year Sale | `/midyear/` |
| Back to School | `/backtoschool/` |
| Black Friday | `/blackfriday/` |
| Christmas | `/xmas/` |
| Valentine | `/seasonal/` (legacy) |

### Required Files Per New Campaign Folder
```
/new-campaign-folder/
+-- index.php          ← Landing page (new campaign design + tracking)
+-- analytics.php      ← Copy exact from any existing LP (13-col, no modifications)
+-- log.php            ← Copy exact shared dashboard
```

### thankyou.php
- **Do NOT create a `thankyou.php` inside the campaign folder.**
- All campaigns use the **root `thankyou.php`** at `https://smart-buzzer.com/thankyou.php`.
- When creating a new campaign, add the new packages + Fanbasis ref codes to the root `thankyou.php` (`$packages` array and `$refMap`).
- Also add the new landing page to `/analytics/` sources (update `$DEFAULT_LANDING_PAGES` or add a source JSON).

### Checklist for New Campaign Folder
```
[ ] New folder created with correct name
[ ] index.php — new design with correct packages, prices, Fanbasis URLs
[ ] analytics.php — exact copy from existing LP (no changes)
[ ] log.php — exact copy from existing LP (no changes)
[ ] GTM (GTM-WJ6ZK3MR) installed in index.php <head> + noscript
[ ] Meta Pixel: DO NOT add direct fbq() init code — GTM handles FB Pixel (add comment: <!-- Meta Pixel: managed via GTM -->)
[ ] TikTok Pixel (D25JHKBC77UF6R3NPOGG) installed directly (not via GTM)
[ ] sbPkgMeta defined with correct item IDs, prices, reviews
[ ] view_item dataLayer fires on pricing section visible
[ ] ORDER NOW buttons: Lead pixel + SubmitForm TikTok + begin_checkout + add_payment_info + LocalStorage bridge + redirect
[ ] generate_lead dataLayer on WhatsApp CTA clicks
[ ] trackEvent uses application/json format
[ ] New packages added to root thankyou.php $packages
[ ] New refMap entries added to root thankyou.php $refMap
[ ] New LP added to /analytics/ source list
[ ] UTM package.md updated with new Fanbasis Ref → Package Key → Reviews → Price entries
```

---

## /seasonal/ CAMPAIGN ROTATION WORKFLOW

The `/seasonal/` folder is a **rotating campaign slot** — the same URL is reused for different seasonal promotions (Valentine, Mid-Year Sale, Black Friday, Christmas, etc.).

**Live URL:** `https://smart-buzzer.com/seasonal/`
**Server path:** `/home/u387681977/domains/smart-buzzer.com/public_html/seasonal/`

### VSCode Folder ↔ Server Path Mapping

**IMPORTANT:** In VSCode, each campaign gets its own folder for organization. On the server, they ALL deploy to the same `/seasonal/` path.

| VSCode Folder | Server Deploy Path | Campaign |
|---------------|-------------------|----------|
| `seasonal/` | `/seasonal/` | Valentine (legacy/backup) |
| `midyear/` | `/seasonal/` | Mid-Year Sale (current) |
| *(future folder)* | `/seasonal/` | Next campaign |

**How it works:**
- In VSCode, create a new folder per campaign (e.g., `midyear/`, `backtoschool/`) for version control
- When deploying, upload the contents to `/seasonal/` on the server (replacing the previous campaign)
- The `analytics/index.php` centralized dashboard reads from server path `/seasonal/customer_data.log` — it does NOT know about VSCode folder names
- Each VSCode campaign folder has its own `log.php`, `analytics.php`, `index.php` — all get uploaded to `/seasonal/`

### Rule: ALWAYS backup before replacing

When asked to update `/seasonal/index.php` with a new campaign:

1. **First, rename/backup the current `index.php`** to a descriptive filename reflecting its content.
   - Format: `index-{theme}.php`
   - Examples: `index-valentine.php`, `index-midyear.php`, `index-blackfriday.php`, `index-xmas.php`
2. **Then place the new campaign file** as `index.php`.

```
Before:  seasonal/index.php  (e.g., Valentine campaign)
After:   seasonal/index-valentine.php  (backup)
         seasonal/index.php            (new campaign, e.g., Mid-Year Sale)
```

### DO NOT:
- Overwrite `index.php` without backing up the old version first
- Name the backup generically (e.g., `index-old.php`, `index-backup.php`) — always use the campaign theme name
- Assume VSCode folder name = server path (e.g., `midyear/` in VSCode ≠ `/midyear/` on server — it deploys to `/seasonal/`)

### Active Campaign History (update this when rotating)
| File | Campaign | Period | VSCode Folder |
|------|----------|--------|---------------|
| `index-valentine.php` | Valentine Special | Feb 2026 | `seasonal/` |
| `index.php` | Mid-Year Sale (current) | Mar 2026 | `midyear/` |

---

## CHANGELOG

### v4.5 (2026-04-28)
- Added `/promo-california/` landing page — California-targeted clone of `/promo/` with UI/UX redesign for the CA market. Same packages/tracking/Fanbasis URLs as `/promo/`. UI changes: new Coastal Blue (#0EA5E9) + Sunset Gold (#F59E0B) palette, inline SVG California state map vector with city dots (Sacramento/SF/SJ/Fresno/LA/SD) replacing dashboard hero image, new Trust Bar section with city pills, new Problem→Solution comparison section, CA-localized testimonials with initial-letter avatars (no photos), 2 new CCPA + CA-reviewer FAQs, sticky mobile CTA bar, CCPA notice on order form. All tracking code (GTM, FB Pixel, TikTok Pixel, dataLayer events, LocalStorage bridge) preserved 100% identical to /promo/. Only `thankyou.php` diff: line 56 fallback URL → `/promo-california/`. New CSS uses `.ca-*` prefix to avoid conflicts. Added `promo-california` (and missing `promo-outbound`) to $DEFAULT_LANDING_PAGES in /analytics/.

### v4.4 (2026-04-16)
- Added `/promo-outbound/` landing page — 1:1 clone of `/promo/` dedicated to outbound email campaigns. Enables distinct UTM attribution (`utm_source=email&utm_medium=outbound`) and separate customer_data.log / page_analytics.log for cold-email conversions. Same packages/Fanbasis URLs as `/promo/`. Only code diff: `thankyou.php` fallback URL points to `/promo-outbound/` instead of `/promo/`. Added to $DEFAULT_LANDING_PAGES in /analytics/ (will auto-create source config on next dashboard load).

### v4.3 (2026-04-15)
- Added OUTBOUND LEAD SCRAPER (/outbound/) section: full product knowledge for the internal Apify-powered Google Maps lead scraper. Documents the 3-file architecture (index.php / api.php / webhook.php), all 9 API actions, lead schema, dedup strategy (placeId), Apify integration credentials, industry templates, auto-save flow, webhook flow, cost reference, CSV export formats, and 10 critical rules. Clarifies that /outbound/ is an internal tool — does NOT use GTM, pixels, customer_data.log, or any LP analytics stack.

### v4.2 (2026-03-11)
- analytics/index.php: fixed all UI label bugs — "Total Clicks" → "Total Submissions", "Daily Clicks" chart → "Daily Submissions", "LP Clicks Trend" → "LP Submissions Trend", all table column headers "Clicks" → "Submissions" (Source, Placement, Landing Page tables). Fixed "Form Submissions" stat card (was hardcoded "coming soon"/0, now populated from total_clicks). Removed hardcoded "0 submits" duplicate line in campaign detail stats. Password updated to `smartbuzzer2025` (removed trailing `!`). Per-LP CLAUDE.md files deprecated with slim quick-reference notices.

### v4.1 (2026-03-11)
- Added per-LP package metadata section covering /promo/ (starter/growth/performance) and /promo-b1g1/ (booster/dominator). Added Fanbasis payment URL tables per LP. Added thankyou.php package mapping per LP. Updated rule 21 to include B1G1 package IDs. Updated Active Landing Pages table with packages column and default package per LP. analytics/index.php updated: CLICK_ONLY entries now filtered from dashboard counts, labels updated from "clicks" to "submissions".

### v4.0 (2026-03-11)
- Migrated to unified GA4 standard ecommerce events: replaced `select_item`/`initiate_checkout` with `view_item`, `begin_checkout`, `add_payment_info`, `purchase`, `generate_lead`. Added LocalStorage bridge to pass user_data across Fanbasis third-party gateway gap. GTM now installed on both index.php and thankyou.php. Purchase pixel/dataLayer fires on thankyou.php page load (not button click). Added Enhanced Conversion GTM variable setup (DLV, UPD, Meta Advanced Matching). Added main site (/) and /seasonal/ to active LP list. Added `item_category: 'Google Reviews'` to all ecommerce items. Single unified event set across all pages — differentiate by page_location, not duplicate events.

### v3.1 (2025-03-11)
- Removed CLICK_ONLY tracking; only FORM_SUBMIT logged to customer_data.log. Legacy entries still readable.

### v3.0 (2025-03-01)
- Added GTM dataLayer system, GA4 ecommerce, user_data, Lead/InitiateCheckout pixels, email field, form auto-save.

### v2.9 (2025-02-15)
- Added thankyou.php, Purchase pixel, TikTok Pixel, 4-file architecture.

### v2.8 (2025-01-10)
- UVP Modal System, Centralized Analytics v1.3, 18-column customer_data.log.

---

**Document Version:** 4.5
**Last Updated:** 2026-04-28
**Status:** Production Ready