<?php
/**
 * ============================================================================
 * File: /stories/index.php
 * Smart Buzzer — Client Case Study (single page, light mode only)
 *
 * ONE page, deliberately minimal: [text] -> [picture], repeated. Short copy.
 * No routing, no story index, no metric strip, no package table, no FAQ.
 * If you are about to add a section here, add it to /promo/ instead — this page
 * exists to carry the proof and hand off, nothing more.
 *
 * Text-before-picture is an explicit product decision, not an oversight. The
 * CLAUDE.md mobile rule (visual above text) covers two-column split sections;
 * these are full-width stacked blocks where the sentence sets up the screenshot.
 *
 * Light mode only on purpose: the screenshots are dark WhatsApp captures and a
 * dark page made them disappear. Do NOT add a prefers-color-scheme block back in.
 *
 * NO order form. This page never writes customer_data.log — that file is form
 * submissions only (CLAUDE.md absolute rule). Checkout lives on /promo/.
 * Deliberately no view_item / begin_checkout either: those would skew the
 * /promo/ funnel conversion rate.
 *
 * REDACTION RULE: every screenshot must have the client's name, business name,
 * phone number, avatar, email, the Google Sheets URL and the reviewer account
 * names removed before upload.
 * ============================================================================
 */

// WhatsApp: pre-purchase marketing, so this uses the public sales line.
$__wa = ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/wa-config.php';
if (!is_readable($__wa)) { $__wa = dirname(__DIR__) . '/wa-config.php'; }
if (is_readable($__wa)) { include $__wa; }
if (empty($SB_WA_NUMBER))  { $SB_WA_NUMBER  = '6285121563813'; }
if (empty($SB_WA_DISPLAY)) { $SB_WA_DISPLAY = '+62 851-2156-3813'; }

$SB_WA_TEXT = rawurlencode("Hi Smart Buzzer, I just read the case study about review drops. I'd like to order Google reviews.");
$SB_PRICING = 'https://smart-buzzer.com/promo/#pricing';

$e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };

$pageTitle = 'Case study: he asked about review drops. We showed him every single one. | Smart Buzzer';
$pageDesc  = 'A client asked what happens when reviews drop. We sent the full tracking report and the 7-day per-review refill. He moved to a retainer, ordered the $530 package and added four more profiles.';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<title><?php echo $e($pageTitle); ?></title>
<meta name="description" content="<?php echo $e($pageDesc); ?>">
<link rel="canonical" href="https://smart-buzzer.com/stories/">
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<meta property="og:title" content="He asked about review drops. We showed him every single one.">
<meta property="og:description" content="<?php echo $e($pageDesc); ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="https://smart-buzzer.com/stories/">
<meta property="og:image" content="https://smart-buzzer.com/stories/img/story-1.webp">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
<!-- End Google Tag Manager -->

<!-- Meta Pixel: managed via GTM -->

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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                border: 'hsl(var(--border))',
                ring: 'hsl(var(--ring))',
                background: 'hsl(var(--background))',
                foreground: 'hsl(var(--foreground))',
                primary: { DEFAULT: 'hsl(var(--primary))', foreground: 'hsl(var(--primary-foreground))' },
                secondary: { DEFAULT: 'hsl(var(--secondary))', foreground: 'hsl(var(--secondary-foreground))' },
                muted: { DEFAULT: 'hsl(var(--muted))', foreground: 'hsl(var(--muted-foreground))' },
                accent: { DEFAULT: 'hsl(var(--accent))', foreground: 'hsl(var(--accent-foreground))' },
                card: { DEFAULT: 'hsl(var(--card))', foreground: 'hsl(var(--card-foreground))' }
            },
            borderRadius: { lg: 'var(--radius)', md: 'calc(var(--radius) - 2px)', sm: 'calc(var(--radius) - 4px)' },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'monospace']
            }
        }
    }
};
</script>
<style>
    /* shadcn/ui token set — LIGHT ONLY. No dark block. */
    :root {
        color-scheme: light;
        --background: 0 0% 100%;
        --foreground: 240 10% 3.9%;
        --card: 0 0% 100%;
        --card-foreground: 240 10% 3.9%;
        --primary: 240 5.9% 10%;
        --primary-foreground: 0 0% 98%;
        --secondary: 240 4.8% 95.9%;
        --secondary-foreground: 240 5.9% 10%;
        --muted: 240 4.8% 95.9%;
        --muted-foreground: 240 3.8% 46.1%;
        --accent: 240 4.8% 95.9%;
        --accent-foreground: 240 5.9% 10%;
        --border: 240 5.9% 90%;
        --ring: 240 5% 64.9%;
        --radius: 0.75rem;
    }
    * { -webkit-font-smoothing: antialiased; }
    body {
        background: hsl(var(--background));
        color: hsl(var(--foreground));
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    h1, h2 { text-wrap: balance; letter-spacing: -0.022em; }
    .eyebrow {
        font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 500;
        letter-spacing: 0.14em; text-transform: uppercase; color: hsl(var(--muted-foreground));
    }

    /* Screenshot frame — placeholder while the file is not uploaded yet */
    .shot { position: relative; overflow: hidden; background: hsl(var(--muted)); }
    .shot img { display: block; width: 100%; height: auto; cursor: zoom-in; }
    .shot.is-missing img { display: none; }
    .shot.is-missing::after {
        content: 'Screenshot pending upload';
        display: flex; align-items: center; justify-content: center;
        min-height: 240px;
        font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em;
        text-transform: uppercase; color: hsl(var(--muted-foreground));
        background: repeating-linear-gradient(45deg, hsl(var(--muted)) 0 10px, #fff 10px 20px);
    }

    /* Lightbox */
    #lb { display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(9,9,11,.92); padding: 24px; }
    #lb.open { display: flex; align-items: center; justify-content: center; }
    #lb img { max-width: 100%; max-height: 92vh; border-radius: 10px; }
    #lb button {
        position: absolute; top: 16px; right: 16px; width: 40px; height: 40px; border: 0;
        border-radius: 999px; background: rgba(255,255,255,.16); color: #fff; font-size: 22px; cursor: pointer;
    }
</style>
</head>
<body class="min-h-screen">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<header class="border-b border-border">
    <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-5">
        <a href="https://smart-buzzer.com/" class="flex items-center">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" class="h-7 w-auto">
        </a>
        <a href="<?php echo $e($SB_PRICING); ?>" data-sb-cta="packages" data-sb-loc="header"
           class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition hover:opacity-90">
            See the packages
        </a>
    </div>
</header>

<main class="mx-auto max-w-3xl px-5">

    <!-- ===== [ TULISAN ] — satu blok deskriptif, tanpa judul =====
         Sudutnya sengaja MANFAAT, bukan proses. Yang bikin klien beli bukan
         "kami transparan", tapi kalimatnya sendiri: reviewnya bakal drop dan
         dia gak masalah, karena profil yang keliatan aktif itu yang bikin dia
         dapet duit. Jangan diputer balik jadi cerita soal kerapian laporan. -->
    <section class="pt-14 sm:pt-20">
        <p class="eyebrow">Case study &middot; August 2026 &middot; Plumbing, United States</p>

        <p class="mt-5 text-xl leading-relaxed sm:text-2xl">
            Reviews drop. He ordered more anyway &mdash; because
            <strong class="font-semibold">a listing that gets fresh reviews every day is the one customers
            call.</strong>
        </p>

        <p class="mt-5 text-lg leading-relaxed text-muted-foreground">
            That is the whole reason he does not mind the drops: the value is in the flow, not the stock.
            So when we sent him the full tracking report &mdash; every review, every drop, and which drops
            were still inside their 7-day free refill window &mdash; he did not ask for a refund. He put us
            on retainer the same morning, ordered the $530 package and added four more profiles.
        </p>
    </section>

    <!-- ===== [ PICTURE ] ===== -->
    <figure class="mt-10">
        <div class="shot overflow-hidden rounded-xl border border-border">
            <img src="img/story-2.webp" loading="lazy"
                 alt="The tracking report we sent, showing every review with its status and the drop-eligible list"
                 onerror="this.parentNode.classList.add('is-missing')" onclick="sbZoom(this.src)">
        </div>
        <figcaption class="mt-3 text-sm leading-relaxed text-muted-foreground">
            The report we sent him: every review with its status, the drops eligible for a free refill, and
            the running totals.
        </figcaption>
    </figure>

    <!-- ===== [ PICTURE ] ===== -->
    <figure class="mt-8">
        <div class="shot overflow-hidden rounded-xl border border-border">
            <img src="img/story-1.webp" loading="lazy"
                 alt="Client message moving to a retainer after reading the tracking report"
                 onerror="this.parentNode.classList.add('is-missing')" onclick="sbZoom(this.src)">
        </div>
        <figcaption class="mt-3 text-sm leading-relaxed text-muted-foreground">
            His answer: &ldquo;I don&rsquo;t mind because they make me money, so this will be a long term
            partnership.&rdquo;
        </figcaption>
    </figure>

    <p class="mt-6 text-xs leading-relaxed text-muted-foreground">
        Screenshots from the real WhatsApp thread. Client name, number, business name, avatar, report URL
        and reviewer account names removed.
    </p>

    <!-- ===== [ CTA ] ===== -->
    <section class="my-14 sm:my-20">
        <div class="rounded-xl border border-border bg-secondary/60 p-8 text-center">
            <h2 class="text-2xl font-bold">Want the same report on your listing?</h2>
            <p class="mx-auto mt-3 max-w-md leading-relaxed text-muted-foreground">
                Packages start at $360. Pick one and we start this week.
            </p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="<?php echo $e($SB_PRICING); ?>" data-sb-cta="packages" data-sb-loc="footer"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-7 py-3.5 text-base font-semibold text-primary-foreground transition hover:opacity-90">
                    See the packages <span aria-hidden="true">&rarr;</span>
                </a>
                <a href="https://wa.me/<?php echo $e($SB_WA_NUMBER); ?>?text=<?php echo $SB_WA_TEXT; ?>" target="_blank" rel="noopener"
                   data-sb-cta="whatsapp" data-sb-loc="footer"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-card px-7 py-3.5 text-base font-semibold transition hover:bg-accent">
                    Ask on WhatsApp
                </a>
            </div>
        </div>
    </section>

</main>

<footer class="border-t border-border">
    <div class="mx-auto max-w-3xl px-5 py-10 text-sm text-muted-foreground">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <span>&copy; <?php echo date('Y'); ?> Smart Buzzer &middot; A subsidiary of Pintarnya.</span>
            <span class="flex flex-wrap gap-x-4 gap-y-1">
                <a href="https://smart-buzzer.com/service-tnc" class="transition hover:text-foreground">Terms &amp; Conditions</a>
                <a href="https://wa.me/<?php echo $e($SB_WA_NUMBER); ?>?text=<?php echo $SB_WA_TEXT; ?>" target="_blank" rel="noopener"
                   data-sb-cta="whatsapp" data-sb-loc="footer_link"
                   class="transition hover:text-foreground"><?php echo $e($SB_WA_DISPLAY); ?></a>
            </span>
        </div>
    </div>
</footer>

<!-- Lightbox -->
<div id="lb" onclick="if(event.target===this)sbZoomClose()">
    <button type="button" onclick="sbZoomClose()" aria-label="Close">&times;</button>
    <img id="lbImg" src="" alt="">
</div>

<script>
// ===== Lightbox =====
function sbZoom(src) {
    document.getElementById('lbImg').src = src;
    document.getElementById('lb').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function sbZoomClose() {
    document.getElementById('lb').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') sbZoomClose(); });

// ===== Analytics (same backend as the landing pages) =====
var sessionId = (function () {
    try {
        var sid = sessionStorage.getItem('sb_session_id');
        if (!sid) {
            sid = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('sb_session_id', sid);
        }
        return sid;
    } catch (e) { return 'session_' + Date.now(); }
})();

function logAnalyticsEvent(eventType, data) {
    fetch('analytics.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            event_type: eventType,
            page_url: window.location.href,   // .href, never .pathname — UTM lives here
            data: JSON.stringify(data || {}),
            session_id: sessionId
        })
    }).catch(function () {});
}

document.addEventListener('DOMContentLoaded', function () {
    logAnalyticsEvent('PAGE_VIEW', { page: 'case_study' });

    // CTA clicks -> analytics + generate_lead. No view_item / begin_checkout here:
    // this page has no form and no checkout, so it must not pollute the /promo/ funnel.
    document.querySelectorAll('[data-sb-cta]').forEach(function (el) {
        el.addEventListener('click', function () {
            var kind = this.getAttribute('data-sb-cta');
            logAnalyticsEvent('STORY_CTA_' + kind.toUpperCase(), {
                location: this.getAttribute('data-sb-loc') || '-',
                href: this.getAttribute('href')
            });
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'generate_lead',
                method: kind === 'whatsapp' ? 'WhatsApp' : 'Case Study Packages',
                value: 0,
                currency: 'USD'
            });
        });
    });

    // Screenshot zoom = proof engagement
    document.querySelectorAll('.shot img').forEach(function (img) {
        img.addEventListener('click', function () { logAnalyticsEvent('STORY_SHOT_ZOOM', { src: this.getAttribute('src') }); });
    });
});

// ===== Scroll depth =====
(function () {
    var hit = { 25: false, 50: false, 75: false, 100: false }, t;
    window.addEventListener('scroll', function () {
        clearTimeout(t);
        t = setTimeout(function () {
            var pct = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
            [25, 50, 75, 100].forEach(function (d) {
                if (pct >= d && !hit[d]) { hit[d] = true; logAnalyticsEvent('SCROLL_DEPTH_' + d, { depth: d }); }
            });
        }, 120);
    }, { passive: true });
})();

// ===== Time on page =====
(function () {
    var start = Date.now();
    window.addEventListener('beforeunload', function () {
        var payload = JSON.stringify({
            event_type: 'TIME_ON_PAGE',
            page_url: window.location.href,
            data: JSON.stringify({ duration: Math.floor((Date.now() - start) / 1000) }),
            session_id: sessionId
        });
        if (navigator.sendBeacon) {
            navigator.sendBeacon('analytics.php', new Blob([payload], { type: 'application/json' }));
        }
    });
})();
</script>
</body>
</html>
