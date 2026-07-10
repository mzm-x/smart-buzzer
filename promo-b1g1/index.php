<?php $__wa=$_SERVER["DOCUMENT_ROOT"]."/wa-config.php"; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER="628979133204";} if(empty($SB_WA_DISPLAY)){$SB_WA_DISPLAY="+62 897-9133-204";} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Buy Google Reviews + Free Ratings | B1G1 Offer</title>
    <meta name="description" content="Buy authentic Google reviews from local users. Trusted by 1200+ businesses. B1G1 offer: buy reviews and get free ratings included. Safe, gradual posting.">
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
<!-- End Google Tag Manager -->

    <!-- Meta Pixel: managed via GTM (tag: FB - Pageview, All Pages) -->

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

    <!-- Analytics Tracking Variables -->
    <script>
let sessionId = null;
let pageLoadTime = Date.now();
let scrollDepths = {25: false, 50: false, 75: false, 100: false};
let returnVisitor = false;

// BUG FIX: Safe localStorage wrapper (moved before usage to prevent ReferenceError)
function safeLocalStorage(action, key, value) {
    try {
        if (action === 'get') return localStorage.getItem(key);
        if (action === 'set') return localStorage.setItem(key, value);
        return null;
    } catch (e) {
        console.warn('localStorage not available:', e);
        return null;
    }
}

function getSessionId() {
    let sid = sessionStorage.getItem('sb_session_id');
    if (!sid) {
        sid = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('sb_session_id', sid);
    }
    return sid;
}

function checkReturnVisitor() {
    // BUG FIX: Use safeLocalStorage wrapper to handle incognito mode
    const visited = safeLocalStorage('get', 'sb_visited');
    if (visited) {
        returnVisitor = true;
        const count = parseInt(safeLocalStorage('get', 'sb_visit_count') || '0') + 1;
        safeLocalStorage('set', 'sb_visit_count', count.toString());
    } else {
        safeLocalStorage('set', 'sb_visited', 'true');
        safeLocalStorage('set', 'sb_visit_count', '1');
    }
}

sessionId = getSessionId();
checkReturnVisitor();

// Package metadata for GTM dataLayer (B1G1 V2 packages)
var sbPkgMeta = {
    'booster':   {id: 'pkg_booster_65',    name: 'Buy Google Reviews - 65 Local (B1G1)',  item_category: 'Google Reviews', price: 380.00, reviews: 65},
    'dominator': {id: 'pkg_dominator_120', name: 'Buy Google Reviews - 120 Local (B1G1)', item_category: 'Google Reviews', price: 680.00, reviews: 120}
};

// Track last selected package for WhatsApp / floating CTA tracking
var sbLastSelectedPkg = null;
</script>

    <style>
        /* === RESET === */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* === CSS VARIABLES === */
        :root {
            --blue: #2563EB;
            --blue-hover: #1D4ED8;
            --blue-light: #EFF6FF;
            --orange: #F97316;
            --orange-hover: #EA580C;
            --orange-light: #FFF7ED;
            --green: #059669;
            --green-light: #ECFDF5;
            --red: #DC2626;
            --dark: #0F172A;
            --text: #1E293B;
            --muted: #64748B;
            --subtle: #94A3B8;
            --bg: #FFFFFF;
            --bg-alt: #F1F5F9;
            --card: #FFFFFF;
            --border: #E2E8F0;
            --radius: 12px;
            --shadow-sm: 0 1px 4px rgba(0,0,0,0.07);
            --shadow-md: 0 4px 18px rgba(0,0,0,0.10);
            --shadow-lg: 0 16px 48px rgba(0,0,0,0.13);
        }

        /* === ANIMATIONS === */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes zoomIn { from { transform: scale(0.8); } to { transform: scale(1); } }
        @keyframes badgePulse { 0%,100% { box-shadow: 0 0 0 0 rgba(249,115,22,0.4); } 50% { box-shadow: 0 0 0 10px rgba(249,115,22,0); } }
        @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
        @keyframes pulseRing { 0% { box-shadow: 0 4px 14px rgba(37,99,235,0.35), 0 0 0 0 rgba(37,99,235,0.4); } 70% { box-shadow: 0 4px 14px rgba(37,99,235,0.35), 0 0 0 10px rgba(37,99,235,0); } 100% { box-shadow: 0 4px 14px rgba(37,99,235,0.35), 0 0 0 0 rgba(37,99,235,0); } }
        @keyframes pulseRingOrange { 0% { box-shadow: 0 4px 14px rgba(249,115,22,0.35), 0 0 0 0 rgba(249,115,22,0.4); } 70% { box-shadow: 0 4px 14px rgba(249,115,22,0.35), 0 0 0 10px rgba(249,115,22,0); } 100% { box-shadow: 0 4px 14px rgba(249,115,22,0.35), 0 0 0 0 rgba(249,115,22,0); } }
        @keyframes dotBlink { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }
        @keyframes heroFadeIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideShimmer { 0% { left: -100%; } 100% { left: 100%; } }

        /* === SCROLL REVEAL === */
        .fade-up { opacity: 0; transform: translateY(28px); transition: opacity 0.65s ease, transform 0.65s ease; }
        .fade-up.is-visible { opacity: 1; transform: translateY(0); }
        .fade-up.delay-1 { transition-delay: 0.1s; }
        .fade-up.delay-2 { transition-delay: 0.2s; }
        .fade-up.delay-3 { transition-delay: 0.3s; }

        /* === BASE === */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
        }

        section { padding: 80px 0; }

        /* === TYPOGRAPHY === */
        h1, h2, h3 {
            font-family: 'Instrument Serif', Georgia, 'Times New Roman', serif;
            line-height: 1.25;
            color: var(--dark);
            font-weight: 400;
        }

        h1 em {
            font-style: italic;
            color: var(--orange);
        }

        /* === SECTION HEADER === */
        .section-header {
            text-align: center;
            margin-bottom: 52px;
        }

        .section-label {
            display: inline-block;
            background: var(--orange-light);
            color: var(--orange-hover);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 12px;
            font-family: 'Inter', sans-serif;
        }

        .section-header h2 {
            font-size: clamp(2.2rem, 4.5vw, 3.5rem);
            color: var(--dark);
            line-height: 1.15;
            letter-spacing: -0.4px;
        }

        .section-header p {
            color: var(--muted);
            margin-top: 12px;
            font-size: 16px;
        }

        /* === HEADER === */
        header {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 20px rgba(0,0,0,0.06);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            display: inline-block;
            text-decoration: none;
            line-height: 0;
        }

        .logo img { height: 36px; width: auto; }

        nav { display: flex; align-items: center; }

        nav a {
            margin-left: 24px;
            text-decoration: none;
            color: var(--muted);
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
            position: relative;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: var(--blue);
            transition: width 0.2s ease;
            border-radius: 2px;
        }

        nav a:hover { color: var(--blue); }
        nav a:hover::after { width: 100%; }

        /* === IMAGE ZOOM === */
        .image-zoom-wrapper {
            overflow: hidden;
            border-radius: var(--radius);
        }

        .image-zoom-wrapper img {
            transition: transform 0.4s ease;
            cursor: pointer;
        }

        .image-zoom-wrapper:hover img { transform: scale(1.04); }

        img[data-preview] { cursor: pointer; }

        /* === IMAGE MODAL === */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.95);
            animation: fadeIn 0.3s ease;
        }

        .image-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            animation: zoomIn 0.3s ease;
        }

        .modal-close {
            position: absolute;
            top: 20px; right: 20px;
            color: white;
            font-size: 36px;
            font-weight: 300;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10000;
            width: 44px; height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(90deg);
        }

        /* === BUTTONS === */
        .btn {
            display: inline-block;
            padding: 14px 32px;
            border-radius: 100px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
            font-family: 'Inter', sans-serif;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }

        .btn-primary:hover {
            background: var(--blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(37,99,235,0.35);
        }

        .btn-orange {
            background: var(--orange);
            color: white;
            box-shadow: 0 4px 14px rgba(249,115,22,0.25);
        }

        .btn-orange:hover {
            background: var(--orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(249,115,22,0.35);
        }

        .btn-ghost {
            background: transparent;
            color: var(--muted);
            border: 1.5px solid var(--border);
            box-shadow: none;
        }

        .btn-ghost:hover {
            border-color: var(--blue);
            color: var(--blue);
            background: var(--blue-light);
            transform: translateY(-1px);
        }

        .btn-primary.btn-pricing { animation: pulseRing 2.5s ease-in-out infinite; }
        .btn-orange.btn-pricing { animation: pulseRingOrange 2.5s ease-in-out infinite; }

        /* === HERO === */
        .hero {
            background: var(--card);
            padding: 96px 0 88px;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .hero::before, .hero::after { display: none; }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--orange);
            color: white;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 7px 16px;
            border-radius: 100px;
            margin-bottom: 20px;
            font-family: 'Inter', sans-serif;
            animation: badgePulse 2.5s ease-in-out infinite;
            box-shadow: 0 4px 14px rgba(249,115,22,0.2);
        }

        .hero-badge-dot {
            width: 7px; height: 7px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            animation: dotBlink 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }

        .hero-text { animation: heroFadeIn 0.7s ease both; }

        .hero-text h1 {
            font-size: 58px;
            margin-bottom: 20px;
            letter-spacing: -1px;
            line-height: 1.1;
            animation: heroFadeIn 0.7s ease 0.1s both;
        }

        .hero-text > p {
            font-size: 17px;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.75;
            animation: heroFadeIn 0.7s ease 0.2s both;
        }

        .hero-stats {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 32px;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.9);
            box-shadow: 0 4px 24px rgba(0,0,0,0.07), 0 0 0 1px rgba(37,99,235,0.06);
            overflow: hidden;
            animation: heroFadeIn 0.7s ease 0.3s both;
        }

        .stat-item { text-align: center; flex: 1; padding: 18px 12px; }

        .stat-item strong {
            display: block;
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            font-family: 'Inter', sans-serif;
            line-height: 1.2;
        }

        .stat-item span {
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 500;
        }

        .stat-divider {
            width: 1px;
            height: 40px;
            background: var(--border);
            flex-shrink: 0;
        }

        .hero-buttons {
            display: flex;
            gap: 12px;
            animation: heroFadeIn 0.7s ease 0.4s both;
        }

        .hero-image {
            position: relative;
            animation: heroFadeIn 0.9s ease 0.2s both;
        }

        .hero-image img {
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 32px 64px rgba(37,99,235,0.18), 0 0 0 1px rgba(37,99,235,0.08);
        }

        .hero-image-badge {
            position: absolute;
            bottom: -16px; left: 20px;
            background: white;
            border-radius: 14px;
            padding: 10px 16px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.12);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            border: 1px solid var(--border);
        }

        .hero-image-badge-icon {
            width: 28px; height: 28px;
            background: var(--green-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--green);
            font-size: 14px;
        }

        /* === HOW IT WORKS === */
        .how-section {
            background: var(--bg-alt);
            position: relative;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 38px;
            left: calc(16.66% + 12px);
            right: calc(16.66% + 12px);
            height: 2px;
            background: var(--border);
            opacity: 0.18;
            pointer-events: none;
        }

        .step-card {
            background: var(--card);
            border-radius: 16px;
            padding: 36px 24px;
            text-align: center;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(37,99,235,0.12);
            border-color: var(--blue);
        }

        .step-number {
            width: 52px; height: 52px;
            background: var(--blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            margin: 0 auto 20px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }

        .step-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .step-card p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.65;
        }

        /* === PRICING === */
        .pricing { background: var(--bg); }

        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
            max-width: 900px;
            margin: 0 auto;
        }

        .pricing-card {
            background: var(--card);
            border-radius: 18px;
            padding: 36px;
            border: 1.5px solid var(--border);
            border-top: 4px solid var(--blue);
            position: relative;
            transition: all 0.3s ease;
        }

        .pricing-card.popular {
            border: 1.5px solid rgba(249,115,22,0.25);
            border-top: 4px solid var(--orange);
            box-shadow: 0 0 0 4px rgba(249,115,22,0.07), 0 24px 64px rgba(249,115,22,0.13);
            background: var(--card);
            transform: scale(1.01);
        }

        .pricing-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 56px rgba(0,0,0,0.11);
        }

        .pricing-card.popular:hover {
            transform: scale(1.01) translateY(-6px);
            box-shadow: 0 0 0 4px rgba(249,115,22,0.1), 0 28px 64px rgba(249,115,22,0.18);
        }

        .popular-badge {
            position: absolute;
            top: -1px; right: 24px;
            background: var(--orange);
            color: white;
            padding: 5px 14px;
            border-radius: 0 0 10px 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        }

        .pricing-header h3 {
            font-size: 24px;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .pricing-price {
            font-size: 52px;
            font-weight: 800;
            color: var(--blue);
            font-family: 'Inter', sans-serif;
            line-height: 1;
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
        }

        .pricing-card.popular .pricing-price { color: var(--orange); }

        .pricing-original {
            font-size: 14px;
            color: var(--subtle);
            text-decoration: line-through;
            margin-bottom: 10px;
        }

        .pricing-reviews {
            display: inline-block;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 13px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 10px;
            font-family: 'Inter', sans-serif;
        }

        .pricing-card.popular .pricing-reviews {
            background: var(--orange-light);
            color: var(--orange-hover);
        }

        .discount-badge {
            display: inline-block;
            background: var(--green-light);
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 16px;
            font-family: 'Inter', sans-serif;
        }

        .pricing-bonus {
            background: var(--orange-light);
            border: 1px solid #FED7AA;
            border-radius: 10px;
            padding: 13px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: var(--orange-hover);
            font-family: 'Inter', sans-serif;
        }

        .pricing-bonus .bonus-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 24px;
        }

        .pricing-features li {
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            transition: color 0.2s;
        }

        .pricing-features li:hover { color: var(--text); }
        .pricing-features li:last-child { border-bottom: none; }

        .pricing-features li:before {
            content: "\2713";
            color: white;
            background: var(--green);
            font-size: 11px;
            font-weight: 700;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* === COUNTDOWN === */
        .countdown-container {
            background: var(--red);
            padding: 22px 32px;
            border-radius: 16px;
            text-align: center;
            margin: 0 auto 48px;
            max-width: 540px;
            box-shadow: 0 8px 24px rgba(220,38,38,0.2);
            position: relative;
            overflow: hidden;
        }

        .countdown-label {
            color: rgba(255,255,255,0.95);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .countdown-label-dot {
            width: 6px; height: 6px;
            background: #FCA5A5;
            border-radius: 50%;
            animation: dotBlink 1s ease-in-out infinite;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .countdown-block {
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(4px);
            padding: 12px 20px;
            border-radius: 10px;
            min-width: 76px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .countdown-number {
            font-size: 32px;
            font-weight: 800;
            color: white;
            display: block;
            line-height: 1;
            font-family: 'Inter', sans-serif;
            font-variant-numeric: tabular-nums;
        }

        .countdown-text {
            font-size: 10px;
            color: rgba(255,255,255,0.75);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 5px;
            display: block;
        }

        /* === UVP INFO ICON === */
        .info-icon-b1g1 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: var(--blue);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            cursor: pointer;
            margin-left: auto;
            flex-shrink: 0;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .info-icon-b1g1:hover {
            background: var(--blue-hover);
            transform: scale(1.1);
        }

        /* === UVP MODAL === */
        .uvp-modal-b1g1 {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(15,23,42,0.82);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .uvp-modal-b1g1.active { display: flex; }

        .uvp-modal-content-b1g1 {
            background: var(--card);
            border-radius: var(--radius);
            max-width: 480px;
            width: 100%;
            padding: 28px;
            position: relative;
            box-shadow: var(--shadow-lg);
            animation: modalSlideInB1g1 0.3s ease;
            max-height: 85vh;
            overflow-y: auto;
        }

        .uvp-modal-content-b1g1::-webkit-scrollbar { width: 5px; }
        .uvp-modal-content-b1g1::-webkit-scrollbar-track { background: var(--bg); border-radius: 3px; }
        .uvp-modal-content-b1g1::-webkit-scrollbar-thumb { background: var(--blue); border-radius: 3px; }

        @keyframes modalSlideInB1g1 {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .uvp-modal-close-b1g1 {
            position: absolute;
            top: 16px; right: 16px;
            background: var(--bg-alt);
            border: 1px solid var(--border);
            color: var(--muted);
            width: 32px; height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .uvp-modal-close-b1g1:hover {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
        }

        .uvp-modal-title-b1g1 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: var(--dark);
            padding-right: 40px;
        }

        .uvp-modal-desc-b1g1 {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 18px;
        }

        .uvp-modal-list-b1g1 {
            list-style: none;
            margin-bottom: 18px;
            padding: 0;
        }

        .uvp-modal-list-b1g1 li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            border-top: 1px solid var(--border);
            font-size: 0.88rem;
            color: var(--muted);
        }

        .uvp-modal-list-b1g1 li:first-child { border-top: none; }
        .uvp-modal-list-b1g1 li:before { display: none; }
        .uvp-modal-list-b1g1 .check-b1g1 { color: var(--green); font-weight: 700; }
        .uvp-modal-list-b1g1 strong { color: var(--dark); }

        .uvp-modal-warning-b1g1 {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 14px;
            color: var(--red);
            font-size: 0.83rem;
            line-height: 1.6;
        }

        .uvp-modal-note-b1g1 {
            background: var(--green-light);
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 12px 14px;
            color: var(--green);
            font-size: 0.83rem;
            line-height: 1.6;
        }

        .uvp-modal-image-b1g1 {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 14px;
        }

        /* === ORDER FORM === */
        .order-form-section { background: var(--bg-alt); }

        .order-form-wrapper {
            max-width: 580px;
            margin: 0 auto;
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            padding: 40px;
        }

        .of-group { margin-bottom: 18px; }

        .of-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .of-req { color: var(--red); }

        .of-input {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }

        .of-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        .of-package-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text);
            margin-bottom: 10px;
            display: block;
        }

        .of-packages {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
        }

        .of-pkg {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .of-pkg:hover { border-color: var(--blue); }

        .of-pkg.selected {
            border-color: var(--blue);
            background: var(--blue-light);
        }

        .of-pkg-radio { display: none; }
        .of-pkg-info { display: flex; flex-direction: column; }

        .of-pkg-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--dark);
            font-family: 'Inter', sans-serif;
        }

        .of-pkg-detail {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
        }

        .of-pkg-popular {
            font-size: 10px;
            font-weight: 700;
            background: var(--orange);
            color: white;
            padding: 2px 8px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 8px;
            vertical-align: middle;
            font-family: 'Inter', sans-serif;
        }

        .of-pkg-price {
            font-size: 20px;
            font-weight: 800;
            color: var(--blue);
            font-family: 'Inter', sans-serif;
        }

        .of-social-proof {
            background: var(--green-light);
            border: 1px solid #A7F3D0;
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--green);
            font-family: 'Inter', sans-serif;
        }

        .of-social-proof-dot {
            width: 8px; height: 8px;
            background: var(--green);
            border-radius: 50%;
            animation: dotBlink 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }

        .of-trust-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 10px;
            background: var(--bg-alt);
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
        }

        .of-trust-bar-divider {
            width: 1px; height: 14px;
            background: var(--border);
        }

        .of-submit {
            width: 100%;
            padding: 18px;
            background: var(--blue);
            color: white;
            border: none;
            border-radius: 100px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }

        .of-submit:hover {
            background: var(--blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(37,99,235,0.35);
        }

        /* === REVIEWS / PROOF === */
        .reviews-section { background: var(--card); }

        .proof-grid {
            max-width: 800px;
            margin: 0 auto;
        }

        .proof-card {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .proof-card img { width: 100%; height: auto; display: block; }

        /* === CONTENT SECTION === */
        .content-section { background: var(--bg-alt); }

        .content-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 52px;
            align-items: center;
        }

        .content-image {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .content-image img {
            width: 100%;
            transition: transform 0.4s ease;
            display: block;
        }

        .content-image:hover img { transform: scale(1.04); }

        .content-text h3 {
            font-size: 26px;
            margin-bottom: 16px;
            color: var(--dark);
        }

        .content-text p {
            font-size: 15px;
            color: var(--muted);
            margin-bottom: 14px;
            line-height: 1.75;
        }

        .content-highlight {
            background: var(--green-light);
            border-left: 3px solid var(--green);
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin-top: 16px;
            font-size: 14px;
            color: var(--text);
            line-height: 1.65;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* === WHY SECTION === */
        .why-section { background: var(--card); }

        .why-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 52px;
            align-items: center;
        }

        .why-image {
            border-radius: var(--radius);
            overflow: hidden;
        }

        .why-image img {
            width: 100%;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            transition: transform 0.4s ease;
            display: block;
        }

        .why-image:hover img { transform: scale(1.04); }

        .why-features { list-style: none; }

        .why-features li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
            padding: 16px;
            background: var(--bg);
            border-radius: 10px;
            border-left: 3px solid var(--border);
            transition: all 0.2s ease;
        }

        .why-features li:hover {
            border-left-color: var(--blue);
            background: var(--blue-light);
            transform: translateX(4px);
        }

        .check-icon {
            width: 20px; height: 20px;
            background: var(--green);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* === DASHBOARD SECTION === */
        .dashboard-section { background: var(--bg-alt); }

        .browser-chrome {
            background: var(--card);
            border-radius: 14px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.12), 0 0 0 1px var(--border);
            overflow: hidden;
        }

        .browser-chrome-bar {
            background: #F1F3F4;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .browser-dots {
            display: flex;
            gap: 6px;
        }

        .browser-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
        }

        .browser-dot-red { background: #FF5F57; }
        .browser-dot-yellow { background: #FFBD2E; }
        .browser-dot-green { background: #28CA41; }

        .browser-url-bar {
            flex: 1;
            background: white;
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 4px 14px;
            font-size: 11px;
            color: var(--muted);
            font-family: 'Inter', sans-serif;
        }

        .dashboard-preview {
            padding: 0;
            overflow: hidden;
        }

        .dashboard-preview img {
            width: 100%;
            display: block;
            transition: transform 0.5s ease;
        }

        .browser-chrome:hover .dashboard-preview img { transform: scale(1.01); }

        /* === EXPERIENCE SECTION === */
        .experience-section { background: var(--card); }

        /* === CLIENTS === */
        .clients-section { background: var(--bg-alt); }

        .clients-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .client-logo {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 24px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .client-logo:hover {
            border-color: var(--blue);
            box-shadow: var(--shadow-sm);
        }

        .client-logo img {
            max-width: 100%;
            height: auto;
            opacity: 0.7;
            filter: grayscale(60%);
            transition: all 0.3s ease;
        }

        .client-logo:hover img {
            opacity: 1;
            filter: grayscale(0%);
        }

        /* === TESTIMONIAL CARD === */
        .testimonial-card {
            background: var(--dark);
            border-radius: 18px;
            padding: 32px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .testimonial-card::before {
            content: '\201C';
            position: absolute;
            top: -10px; right: 24px;
            font-size: 120px;
            color: rgba(249,115,22,0.15);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-stars {
            color: #FBBF24;
            font-size: 18px;
            margin-bottom: 14px;
            letter-spacing: 2px;
        }

        .testimonial-text {
            font-size: 18px;
            color: rgba(255,255,255,0.92);
            line-height: 1.75;
            margin-bottom: 20px;
            font-family: 'Instrument Serif', Georgia, serif;
            font-style: italic;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .testimonial-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: var(--orange);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            flex-shrink: 0;
        }

        .testimonial-author-info strong {
            display: block;
            color: white;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }

        .testimonial-author-info span {
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            font-family: 'Inter', sans-serif;
        }

        /* === FAQ === */
        .faq-section { background: var(--bg); }

        .faq-list {
            max-width: 760px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }

        .faq-item:hover { box-shadow: var(--shadow-sm); }

        .faq-item.open { box-shadow: 0 4px 20px rgba(37,99,235,0.08); border-color: rgba(37,99,235,0.2); }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            padding: 18px 22px;
            text-align: left;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            font-family: 'Inter', sans-serif;
        }

        .faq-icon {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: var(--bg-alt);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.25s ease;
            color: var(--muted);
            font-size: 16px;
            font-weight: 300;
        }

        .faq-item.open .faq-icon {
            background: var(--blue);
            color: white;
            transform: rotate(45deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding 0.35s ease;
            padding: 0 22px;
        }

        .faq-answer-inner {
            padding-bottom: 18px;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.75;
            border-top: 1px solid var(--border);
            padding-top: 14px;
        }

        .faq-item.open .faq-answer { max-height: 300px; }

        /* === STICKY CTA (Mobile) === */
        .sticky-cta {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            padding: 14px 16px;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.12);
            z-index: 999;
            border-top: 1px solid var(--border);
        }

        .sticky-cta a {
            display: block;
            background: var(--orange);
            color: white;
            text-align: center;
            padding: 14px;
            border-radius: 100px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 14px rgba(249,115,22,0.25);
            animation: pulseRingOrange 2.5s ease-in-out infinite;
        }

        /* === FOOTER === */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.75);
            padding: 56px 0 28px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            font-family: 'Inter', sans-serif;
        }

        .footer-section ul { list-style: none; }

        .footer-section ul li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .footer-section a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-section a:hover { color: white; }

        .footer-section p {
            font-size: 14px;
            line-height: 1.7;
            color: rgba(255,255,255,0.65);
            margin-bottom: 8px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
            font-size: 13px;
        }

        .footer-bottom p + p { margin-top: 4px; }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            section { padding: 48px 0; }

            header { padding: 12px 0; }
            nav { display: none; }
            .logo img { height: 30px; }

            .hero { padding: 48px 0; }
            .hero-content { grid-template-columns: 1fr; gap: 28px; }
            .hero-text { order: 2; }
            .hero-image { order: 1; }
            .hero-text h1 { font-size: 30px; }
            .hero-stats { gap: 12px; padding: 14px 16px; flex-wrap: wrap; justify-content: center; }
            .stat-item strong { font-size: 17px; }
            .hero-buttons { flex-direction: column; gap: 10px; }
            .btn { width: 100%; padding: 14px 24px; }

            .section-header { margin-bottom: 36px; }

            .countdown-container { padding: 16px 20px; }
            .countdown-timer { gap: 8px; }
            .countdown-block { min-width: 64px; padding: 10px 14px; }
            .countdown-number { font-size: 24px; }
            .countdown-text { font-size: 10px; }

            .steps { grid-template-columns: 1fr; gap: 16px; }
            .step-card { padding: 24px 20px; }

            .pricing-cards { grid-template-columns: 1fr; gap: 16px; }
            .pricing-card { padding: 28px 22px; }
            .pricing-price { font-size: 40px; }

            .content-flex, .why-flex { grid-template-columns: 1fr; gap: 24px; }

            .clients-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 28px; }
            .client-logo { padding: 18px; }

            .footer-content { grid-template-columns: 1fr; gap: 28px; margin-bottom: 28px; }

            .order-form-wrapper { padding: 28px 20px; }

            .modal-close { top: 14px; right: 14px; font-size: 28px; width: 40px; height: 40px; }
            .modal-content { max-width: 95%; max-height: 85%; }

            .steps::before { display: none; }

            .hero-image-badge { display: none; }

            .testimonial-text { font-size: 15px; }

            .browser-url-bar { display: none; }

            .sticky-cta { display: block; }

            body { padding-bottom: 74px; }
        }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="https://smart-buzzer.com/" target="_blank">
                        <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                    </a>
                </div>
                <nav>
                    <a href="#pricing">Pricing</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#reviews">Our Reviews</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge"><span class="hero-badge-dot"></span>B1G1 Offer &mdash; Limited Time</div>
                    <h1>Buy Google Reviews & Get <em>Free Ratings</em> Included</h1>
                    <p>Smart Buzzer provides local reviews for 1,200+ businesses across USA and Canada. Zero account bans, guaranteed.</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <strong>1,200+</strong>
                            <span>Businesses Served</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <strong>0</strong>
                            <span>Account Bans</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <strong>USA &amp; CA</strong>
                            <span>Markets</span>
                        </div>
                    </div>
                    <div class="hero-buttons">
                        <a href="#pricing" class="btn btn-primary">View Packages</a>
                        <a href="https://wa.me/6285183081655?text=Hi%20Smart%20Buzzer%2C%20I%27m%20interested%20in%20the%20B1G1%20offer" class="btn btn-ghost" target="_blank"><i class="fa-brands fa-whatsapp" style="color:#25D366;"></i> Chat Us</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="image-zoom-wrapper">
                        <img src="https://smart-buzzer.com/google.webp" alt="Google Reviews Dashboard" data-preview="true">
                    </div>
                    <div class="hero-image-badge">
                        <div class="hero-image-badge-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <div>
                            <strong>Zero Account Bans</strong><br>
                            <span style="font-size:11px;font-weight:400;color:var(--muted);">1,200+ Campaigns Delivered</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Simple Process</span>
                <h2>How to Order</h2>
            </div>
            <div class="steps">
                <div class="step-card fade-up">
                    <div class="step-number">1</div>
                    <h3>Place Your Order</h3>
                    <p>Choose a package and submit your order effortlessly in under 2 minutes.</p>
                </div>
                <div class="step-card fade-up delay-1">
                    <div class="step-number">2</div>
                    <h3>Connect With Our Team</h3>
                    <p>Our account manager will guide you through the details to ensure everything is perfect.</p>
                </div>
                <div class="step-card fade-up delay-2">
                    <div class="step-number">3</div>
                    <h3>Track Your Progress</h3>
                    <p>Once your brief is confirmed, we handle the rest and start delivering your reviews.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="pricing">
        <div class="container">
            <div class="section-header">
                <span class="section-label">B1G1 Exclusive</span>
                <h2>Today's Available Discount</h2>
            </div>

            <div class="countdown-container">
                <div class="countdown-label"><span class="countdown-label-dot"></span>Limited Time Offer Ends In:</div>
                <div class="countdown-timer">
                    <div class="countdown-block">
                        <span class="countdown-number" id="hours">23</span>
                        <span class="countdown-text">Hours</span>
                    </div>
                    <div class="countdown-block">
                        <span class="countdown-number" id="minutes">59</span>
                        <span class="countdown-text">Minutes</span>
                    </div>
                    <div class="countdown-block">
                        <span class="countdown-number" id="seconds">59</span>
                        <span class="countdown-text">Seconds</span>
                    </div>
                </div>
            </div>

            <div class="pricing-cards">
                <!-- Booster Package -->
                <div class="pricing-card fade-up">
                    <div class="pricing-header">
                        <h3>Booster</h3>
                    </div>
                    <div class="pricing-price">$380</div>
                    <div class="pricing-original">Was $422.50</div>
                    <div class="pricing-reviews">65 Local Reviews</div><br>
                    <div class="discount-badge">Save $42 &mdash; 10% OFF</div>
                    <div class="pricing-bonus">
                        <span class="bonus-icon">&#10003;</span>
                        <span>FREE 5 Ratings Bonus Included</span>
                    </div>
                    <ul class="pricing-features">
                        <li>(20%) 4-Star + (80%) 5-Star Ratings<span class="info-icon-b1g1" onclick="openUvpModalB1g1('ratingsModalB1g1')">i</span></li>
                        <li>(70%) Local + (30%) Global Names<span class="info-icon-b1g1" onclick="openUvpModalB1g1('namesModalB1g1')">i</span></li>
                        <li>Human-Written Custom Content</li>
                        <li>5-10 Reviews Submitted Daily, ~3 Stick</li>
                        <li>Detailed Delivery Report</li>
                        <li>For 2 Business Links</li>
                    </ul>
                    <a href="#order-form" class="btn btn-primary btn-pricing" data-package="booster" onclick="preSelectPkg('booster')" style="width: 100%; display: block;">ORDER NOW</a>
                </div>

                <!-- Dominator Package -->
                <div class="pricing-card popular fade-up delay-1">
                    <div class="popular-badge">POPULAR</div>
                    <div class="pricing-header">
                        <h3>Dominator</h3>
                    </div>
                    <div class="pricing-price">$680</div>
                    <div class="pricing-original">Was $780.00</div>
                    <div class="pricing-reviews">120 Local Reviews</div><br>
                    <div class="discount-badge">Save $100 &mdash; 13% OFF</div>
                    <div class="pricing-bonus">
                        <span class="bonus-icon">&#10003;</span>
                        <span>FREE 15 Ratings Bonus Included</span>
                    </div>
                    <ul class="pricing-features">
                        <li>(20%) 4-Star + (80%) 5-Star Ratings<span class="info-icon-b1g1" onclick="openUvpModalB1g1('ratingsModalB1g1')">i</span></li>
                        <li>(70%) Local + (30%) Global Names<span class="info-icon-b1g1" onclick="openUvpModalB1g1('namesModalB1g1')">i</span></li>
                        <li>Human-Written Custom Content</li>
                        <li>5-10 Reviews Submitted Daily, ~3 Stick</li>
                        <li>Detailed Delivery Report</li>
                        <li>For 3 Business Links</li>
                    </ul>
                    <a href="#order-form" class="btn btn-orange btn-pricing" data-package="dominator" onclick="preSelectPkg('dominator')" style="width: 100%; display: block;">ORDER NOW &mdash; Save $100</a>
                </div>
            </div>
        </div>
    </section>

    <section class="order-form-section" id="order-form">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Get Started</span>
                <h2>Place Your Order</h2>
                <p>Complete your details below to get started</p>
            </div>
            <div class="order-form-wrapper">
                <div class="of-social-proof">
                    <span class="of-social-proof-dot"></span>
                    <span>12 businesses ordered in the last 24 hours</span>
                </div>
                <div class="of-group">
                    <label class="of-label">Google Business Name + Location<span class="of-req">*</span></label>
                    <input type="text" class="of-input" id="ofBizName" placeholder="Example: John's Burgers in New York" required>
                </div>
                <div class="of-group">
                    <label class="of-label">WhatsApp Number (for order updates)<span class="of-req">*</span></label>
                    <input type="tel" class="of-input" id="ofWhatsapp" placeholder="+1 Enter your WhatsApp number" required>
                </div>
                <div class="of-group">
                    <label class="of-label">Email Address</label>
                    <input type="email" class="of-input" id="ofEmail" placeholder="Enter your email address (optional)">
                </div>
                <span class="of-package-label">Select Your Package:</span>
                <div class="of-packages">
                    <label class="of-pkg" data-pkg="booster" onclick="selectPkg(this)">
                        <input type="radio" name="package" value="booster" class="of-pkg-radio">
                        <div class="of-pkg-info">
                            <span class="of-pkg-name">Booster &mdash; 65 Reviews</span>
                            <span class="of-pkg-detail"><s>$422.50</s> &mdash; Save $42 (10% OFF) + FREE 5 Ratings</span>
                        </div>
                        <span class="of-pkg-price">$380</span>
                    </label>
                    <label class="of-pkg selected" data-pkg="dominator" onclick="selectPkg(this)">
                        <input type="radio" name="package" value="dominator" class="of-pkg-radio" checked>
                        <div class="of-pkg-info">
                            <span class="of-pkg-name">Dominator &mdash; 120 Reviews <span class="of-pkg-popular">POPULAR</span></span>
                            <span class="of-pkg-detail"><s>$780.00</s> &mdash; Save $100 (13% OFF) + FREE 15 Ratings</span>
                        </div>
                        <span class="of-pkg-price">$680</span>
                    </label>
                </div>
                <div class="of-trust-bar">
                    <i class="fa-solid fa-lock" style="color:var(--green);font-size:11px;"></i>
                    <span>Secure &amp; Encrypted</span>
                    <span class="of-trust-bar-divider"></span>
                    <i class="fa-solid fa-shield-halved" style="color:var(--blue);font-size:11px;"></i>
                    <span>Account Safety Guaranteed</span>
                    <span class="of-trust-bar-divider"></span>
                    <i class="fa-solid fa-circle-check" style="color:var(--green);font-size:11px;"></i>
                    <span>Zero Bans</span>
                </div>
                <button class="of-submit" onclick="submitOrder()">COMPLETE ORDER &#8594;</button>
            </div>
        </div>
    </section>

    <section id="reviews" class="reviews-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Proof of Quality</span>
                <h2>Real Local Reviews</h2>
            </div>
            <div class="proof-grid">
                <div class="proof-card">
                    <div class="image-zoom-wrapper">
                        <img src="https://smart-buzzer.com/wp-content/uploads/2025/04/slide-3.jpg" alt="Review Example" data-preview="true">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Custom Content</span>
                <h2>Choose Your Own Sentences</h2>
            </div>
            <div class="content-flex">
                <div class="content-image">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review Sentences" data-preview="true">
                </div>
                <div class="content-text">
                    <h3>Reviews Written Around Your Business</h3>
                    <p>You choose the content, or let us create comprehensive variety for you. Every review is completely unique with zero repetition.</p>
                    <p>Our writers tailor each review to match your specific services, tone, and location — making them indistinguishable from genuine customer feedback.</p>
                    <div class="content-highlight">
                        <i class="fa-solid fa-circle-check" style="color:var(--green); flex-shrink:0;"></i>
                        Human-written content tailored to your business
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Why Choose Us</span>
                <h2>Why People Use Our Services</h2>
            </div>
            <div class="why-flex">
                <div class="why-image">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.36.44.png" alt="Review Example" data-preview="true">
                </div>
                <div class="why-list">
                    <ul class="why-features">
                        <li style="border-left-color: var(--green);">
                            <div class="check-icon">&#10003;</div>
                            <div>
                                <strong style="display: block; font-size: 15px; color: var(--dark); margin-bottom: 3px;">70% Local + 30% Global Names</strong>
                                <span style="font-size: 13px; color: var(--muted);">Natural mix of local and global reviewers for authenticity</span>
                            </div>
                        </li>
                        <li style="border-left-color: var(--blue);">
                            <div class="check-icon">&#10003;</div>
                            <div>
                                <strong style="display: block; font-size: 15px; color: var(--dark); margin-bottom: 3px;">Unique users, IPs, devices, and aged accounts</strong>
                                <span style="font-size: 13px; color: var(--muted);">Complete technical authenticity guaranteed</span>
                            </div>
                        </li>
                        <li style="border-left-color: #8B5CF6;">
                            <div class="check-icon">&#10003;</div>
                            <div>
                                <strong style="display: block; font-size: 15px; color: var(--dark); margin-bottom: 3px;">Tailored reviews for your business</strong>
                                <span style="font-size: 13px; color: var(--muted);">Custom content that matches your services</span>
                            </div>
                        </li>
                        <li style="border-left-color: var(--orange);">
                            <div class="check-icon">&#10003;</div>
                            <div>
                                <strong style="display: block; font-size: 15px; color: var(--dark); margin-bottom: 3px;">Gradual posting (5-10 daily, ~3 stick)</strong>
                                <span style="font-size: 13px; color: var(--muted);">Natural pacing prevents algorithm detection</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Full Transparency</span>
                <h2>Track Your Order Every Day</h2>
                <p>100% transparency with a real-time campaign progress dashboard</p>
            </div>
            <div class="browser-chrome">
                <div class="browser-chrome-bar">
                    <div class="browser-dots">
                        <div class="browser-dot browser-dot-red"></div>
                        <div class="browser-dot browser-dot-yellow"></div>
                        <div class="browser-dot browser-dot-green"></div>
                    </div>
                    <div class="browser-url-bar">smart-buzzer.com/tracker</div>
                </div>
                <div class="dashboard-preview">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Progress Dashboard" data-preview="true">
                </div>
            </div>
        </div>
    </section>

    <section class="experience-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Track Record</span>
                <h2>Experienced in Serving Over 1,200+ Businesses<br>Across USA and Canada</h2>
            </div>
            <div class="browser-chrome">
                <div class="browser-chrome-bar">
                    <div class="browser-dots">
                        <div class="browser-dot browser-dot-red"></div>
                        <div class="browser-dot browser-dot-yellow"></div>
                        <div class="browser-dot browser-dot-green"></div>
                    </div>
                    <div class="browser-url-bar">smart-buzzer.com/campaigns</div>
                </div>
                <div class="dashboard-preview">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="Trello Board" data-preview="true">
                </div>
            </div>
        </div>
    </section>

    <section class="clients-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Trusted By</span>
                <h2>Our Clients</h2>
            </div>
            <div class="testimonial-card fade-up">
                <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="testimonial-text">"We went from 12 reviews to 80+ in just 6 weeks. The reviews are indistinguishable from real customers and our ranking jumped from page 3 to the top 3 results. Zero issues with Google whatsoever."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">M</div>
                    <div class="testimonial-author-info">
                        <strong>Mike T.</strong>
                        <span>HVAC Business Owner, Houston TX</span>
                    </div>
                </div>
            </div>
            <div class="clients-grid">
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client Logo">
                </div>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="#pricing" class="btn btn-primary">
                    View Packages &uarr;
                </a>
            </div>
        </div>
    </section>

    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Questions</span>
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Is this safe for my Google Business Profile?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">Yes. Our method uses unique users, IPs, devices, and aged accounts — the same pattern as organic reviews. Combined with our gradual delivery (5-10 reviews submitted daily, ~3 stick per day), your profile stays safe. We've completed 1,200+ campaigns with our compliant, gradual approach.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        How long does delivery take?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">We submit 5-10 reviews daily (around 3 stick per day) to mimic natural review patterns. A Booster package (65 reviews) typically takes 2-3 months. A Dominator package (120 reviews) takes 4-5 months. Gradual pacing is essential for long-term safety.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        What is the B1G1 offer exactly?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">Buy 1 Get 1 means you get free Google Ratings (star ratings without text) included with your review package — at no extra cost. Booster includes 5 free ratings, Dominator includes 15 free ratings. This adds extra social proof to your profile.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Can I choose what the reviews say?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">Yes. After your order, our account manager will contact you via WhatsApp to gather your business details, services, and any specific keywords you want mentioned. All reviews are human-written and tailored to your business.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        What if a review gets removed?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">We offer a 7-day replacement guarantee. If any review is removed within 7 days of posting, we replace it at no charge. Our mixed rating strategy (80% 5-star, 20% 4-star) significantly reduces the likelihood of removal.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2021/10/REV-COLOR-Smart-Buzzer-10.png.webp" alt="Smart Buzzer" style="height: 50px; width: auto; margin-bottom: 18px; display: block;">
                    <p>Specialized in social media engagement, product reviews, and online reputation services.</p>
                    <p style="font-style: italic; opacity: 0.7;">A subsidiary of Pintarnya.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="https://smart-buzzer.com/tracker">Track Campaign</a></li>
                        <li><a href="https://smart-buzzer.com/report">Report Issue</a></li>
                        <li><a href="https://smart-buzzer.com/service-tnc">Terms &amp; Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <ul>
                        <li>&#128222; WhatsApp: <a href="https://wa.me/<?php echo $SB_WA_NUMBER; ?>?text=Hi%20Smart%20Buzzer%2C%20I%20want%20to%20order%20ggl%20rvw"><?php echo $SB_WA_DISPLAY; ?></a></li>
                        <li>&#128231; Email: <a href="mailto:contact@smart-buzzer.com">contact@smart-buzzer.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Payment security: Charges appear as "Smart Buzzer"</p>
                <p>&copy; 2025 Smart Buzzer. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Sticky Mobile CTA -->
    <div class="sticky-cta" id="stickyCta">
        <a href="#order-form" onclick="return stickyCTAClick()">&#9889; Claim B1G1 Offer &mdash; Order Now</a>
    </div>

    <div class="image-modal" id="imageModal">
        <span class="modal-close" id="modalClose">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <!-- UVP Modal: Ratings -->
    <div class="uvp-modal-b1g1" id="ratingsModalB1g1">
        <div class="uvp-modal-content-b1g1">
            <button class="uvp-modal-close-b1g1" onclick="closeUvpModalB1g1('ratingsModalB1g1')">&times;</button>
            <h3 class="uvp-modal-title-b1g1">Why (20%) 4-Star + (80%) 5-Star?</h3>
            <p class="uvp-modal-desc-b1g1">4-Star reviews are not bad. In fact, they make your profile look more authentic and trustworthy.</p>
            <ul class="uvp-modal-list-b1g1">
                <li><span class="check-b1g1">&#10003;</span><div><strong>More Sticky</strong> - Reviews stay longer</div></li>
                <li><span class="check-b1g1">&#10003;</span><div><strong>More Permanent</strong> - Less likely to be removed</div></li>
                <li><span class="check-b1g1">&#10003;</span><div><strong>Less Drop</strong> - Lower chance of review loss</div></li>
            </ul>
            <div class="uvp-modal-warning-b1g1">Warning: If all 5-star reviews are posted to your Google Maps, even with gradual posting, Google may flag your business for suspicious engagement activity.</div>
            <img src="https://smart-buzzer.com/photos/hr.webp" alt="Review Example" class="uvp-modal-image-b1g1">
            <div class="uvp-modal-note-b1g1">We highly recommend this balanced approach to keep your Google Maps profile safe from being flagged or banned.</div>
        </div>
    </div>

    <!-- UVP Modal: Names -->
    <div class="uvp-modal-b1g1" id="namesModalB1g1">
        <div class="uvp-modal-content-b1g1">
            <button class="uvp-modal-close-b1g1" onclick="closeUvpModalB1g1('namesModalB1g1')">&times;</button>
            <h3 class="uvp-modal-title-b1g1">Why Mix Local and Global Names?</h3>
            <p class="uvp-modal-desc-b1g1">A natural mix of reviewer origins creates a more authentic and believable review profile for your business.</p>
            <ul class="uvp-modal-list-b1g1">
                <li><span class="check-b1g1">&#10003;</span><div><strong>More Genuine</strong> - Mimics real customer patterns</div></li>
                <li><span class="check-b1g1">&#10003;</span><div><strong>More Natural</strong> - Avoids detection algorithms</div></li>
                <li><span class="check-b1g1">&#10003;</span><div><strong>More Trustworthy</strong> - Builds customer confidence</div></li>
            </ul>
            <p class="uvp-modal-desc-b1g1">Real businesses naturally receive reviews from both local customers and visitors from other areas. Our approach mirrors this organic pattern perfectly.</p>
            <div class="uvp-modal-note-b1g1">This balanced mix helps your Google Maps profile appear natural to both potential customers and Google's review algorithms.</div>
        </div>
    </div>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>


// ===== ANALYTICS TRACKING SYSTEM =====

function logAnalyticsEvent(eventType, data = {}) {
    const analyticsData = {
        event_type: eventType,
        page_url: window.location.href,
        data: JSON.stringify(data),
        session_id: sessionId
    };

    fetch('analytics.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(analyticsData)
    }).catch(err => console.log('Analytics error:', err));
}


// Wait for DOM to be ready before tracking (BUG FIX: Moved from immediate execution)
document.addEventListener('DOMContentLoaded', function() {
    // 1. PAGE VIEW TRACKING
    logAnalyticsEvent('PAGE_VIEW', {});

    // 2. RETURN VISITOR TRACKING
    if (returnVisitor) {
        logAnalyticsEvent('RETURN_VISITOR', {
            is_return: true,
            visit_count: parseInt(safeLocalStorage('get', 'sb_visit_count') || '1')  // FIXED: Use safe wrapper
        });
    }
});

// 3. SCROLL DEPTH TRACKING - BUG FIX: Added debounce for performance
let scrollTimeout;
function checkScrollDepth() {
    const scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;

    if (scrollPercent >= 25 && !scrollDepths[25]) {
        scrollDepths[25] = true;
        logAnalyticsEvent('SCROLL_DEPTH_25', {depth: 25});
    }
    if (scrollPercent >= 50 && !scrollDepths[50]) {
        scrollDepths[50] = true;
        logAnalyticsEvent('SCROLL_DEPTH_50', {depth: 50});
    }
    if (scrollPercent >= 75 && !scrollDepths[75]) {
        scrollDepths[75] = true;
        logAnalyticsEvent('SCROLL_DEPTH_75', {depth: 75});
    }
    if (scrollPercent >= 100 && !scrollDepths[100]) {
        scrollDepths[100] = true;
        logAnalyticsEvent('SCROLL_DEPTH_100', {depth: 100});
    }
}

window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(checkScrollDepth, 100);  // Debounce 100ms
});

// === view_item: fires when pricing section becomes visible ===
(function() {
    var viewItemFired = false;
    function fireViewItem() {
        if (viewItemFired) return;
        var pricingEl = document.getElementById('pricing');
        if (!pricingEl) return;
        var rect = pricingEl.getBoundingClientRect();
        var windowH = window.innerHeight || document.documentElement.clientHeight;
        if (rect.top < windowH && rect.bottom > 0) {
            viewItemFired = true;
            // Push view_item for each package
            var pkgKeys = Object.keys(sbPkgMeta);
            var items = pkgKeys.map(function(k) {
                var m = sbPkgMeta[k];
                return { item_id: m.id, item_name: m.name, item_category: m.item_category, price: m.price, quantity: 1 };
            });
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'view_item',
                ecommerce: {
                    currency: 'USD',
                    value: sbPkgMeta['dominator'].price,
                    items: items
                }
            });
            logAnalyticsEvent('VIEW_ITEM', {location: 'pricing'});
        }
    }
    window.addEventListener('scroll', function() { setTimeout(fireViewItem, 150); });
    document.addEventListener('DOMContentLoaded', function() { setTimeout(fireViewItem, 500); });
})();

// 4. PRICING BUTTON CLICK TRACKING (UNIQUE PER DAY)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-package]').forEach(btn => {
        btn.addEventListener('click', function() {
            const pkg = this.getAttribute('data-package');
            var meta = sbPkgMeta[pkg];
            if (!meta) return;

            // Track last selected package for WhatsApp / floating CTA
            sbLastSelectedPkg = pkg;

            // Facebook Pixel - Lead (use readable meta.name, not key)
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {content_name: meta.name});
            }
            // TikTok Pixel - SubmitForm
            if (typeof ttq !== 'undefined') {
                ttq.track('SubmitForm', {content_name: meta.name});
            }

            // dataLayer: add_to_cart (GA4 standard)
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
                        item_category: meta.item_category,
                        price: meta.price,
                        quantity: 1
                    }]
                }
            });

            // dataLayer: generate_lead (clear ecommerce first to avoid stale data)
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'Pricing Click',
                value: meta.price,
                currency: 'USD'
            });

            // Track clicks in analytics.php
            logAnalyticsEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {
                package: pkg,
                location: 'pricing'
            });
        });
    });

    // === generate_lead: WhatsApp CTA clicks ===
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"], a[href*="api.whatsapp"]').forEach(function(waBtn) {
        waBtn.addEventListener('click', function() {
            // Use last selected package if available; otherwise value 0 (user hasn't picked yet)
            var waMeta = sbLastSelectedPkg ? sbPkgMeta[sbLastSelectedPkg] : null;
            var waValue = waMeta ? waMeta.price : 0;
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'WhatsApp',
                value: waValue,
                currency: 'USD',
                content_name: waMeta ? waMeta.name : 'No package selected'
            });
        });
    });
});

// 5. CLICK HEATMAP TRACKING - BUG FIX: Filter to only interactive elements
document.addEventListener('click', function(e) {
    const target = e.target;
    const tagName = target.tagName.toLowerCase();
    const interactiveElements = ['a', 'button', 'input', 'select', 'textarea'];

    // Only track clicks on interactive elements or elements with onclick handlers
    if (interactiveElements.includes(tagName) || target.onclick || target.closest('a') || target.closest('button')) {
        logAnalyticsEvent('CLICK_HEATMAP', {
            x: e.clientX,
            y: e.clientY,
            element: tagName
        });
    }
});

// 6. TIME ON PAGE & EXIT TRACKING
window.addEventListener('beforeunload', function() {
    const timeSpent = Math.floor((Date.now() - pageLoadTime) / 1000);
    logAnalyticsEvent('TIME_ON_PAGE', {duration: timeSpent});
    logAnalyticsEvent('EXIT_PAGE', {
        exit_url: window.location.href,  // FIXED BUG: Use href not pathname for UTM tracking
        time_spent: timeSpent
    });
});

// 7. EXTERNAL LINK TRACKING (Header & Footer)
document.addEventListener('DOMContentLoaded', function() {
    // Track header navigation clicks
    document.querySelectorAll('header a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            // Only track external links (not # anchor links)
            if (href && !href.startsWith('#')) {
                logAnalyticsEvent('EXTERNAL_LINK_CLICK', {
                    location: 'header',
                    url: href,
                    text: this.textContent.trim()
                });
            }
        });
    });

    // Track footer link clicks
    document.querySelectorAll('footer a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            logAnalyticsEvent('EXTERNAL_LINK_CLICK', {
                location: 'footer',
                url: href,
                text: this.textContent.trim()
            });
        });
    });
});

// Smooth Scroll with history.pushState (preserves back button behavior)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({behavior: 'smooth', block: 'start'});
            history.pushState(null, '', href);
        }
    });
});

// ===== ORDER FORM FUNCTIONS =====
function selectPkg(el) {
    document.querySelectorAll('.of-pkg').forEach(function(p) {
        p.classList.remove('selected');
        p.querySelector('input').checked = false;
    });
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    safeLocalStorage('set', 'sb_form_pkg', el.getAttribute('data-pkg'));
}

function preSelectPkg(pkg) {
    setTimeout(function() {
        var card = document.querySelector('.of-pkg[data-pkg="' + pkg + '"]');
        if (card) selectPkg(card);
    }, 300);
}

// ===== AUTO-SAVE & RESTORE FORM =====
(function() {
    function saveFormField(key, value) {
        safeLocalStorage('set', 'sb_form_' + key, value);
    }
    function restoreForm() {
        var biz = safeLocalStorage('get', 'sb_form_biz');
        var wa = safeLocalStorage('get', 'sb_form_wa');
        var email = safeLocalStorage('get', 'sb_form_email');
        var pkg = safeLocalStorage('get', 'sb_form_pkg');
        if (biz) { var el = document.getElementById('ofBizName'); if (el) el.value = biz; }
        if (wa)  { var el = document.getElementById('ofWhatsapp'); if (el) el.value = wa; }
        if (email) { var el = document.getElementById('ofEmail'); if (el) el.value = email; }
        if (pkg) {
            var card = document.querySelector('.of-pkg[data-pkg="' + pkg + '"]');
            if (card) selectPkg(card);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        restoreForm();
        var bizInput = document.getElementById('ofBizName');
        var waInput = document.getElementById('ofWhatsapp');
        var emailInput = document.getElementById('ofEmail');
        if (bizInput) bizInput.addEventListener('input', function() { saveFormField('biz', this.value); });
        if (waInput) waInput.addEventListener('input', function() { saveFormField('wa', this.value); });
        if (emailInput) emailInput.addEventListener('input', function() { saveFormField('email', this.value); });
    });
})();

function submitOrder() {
    var biz = document.getElementById('ofBizName').value.trim();
    var wa = document.getElementById('ofWhatsapp').value.trim();
    var email = document.getElementById('ofEmail').value.trim();
    var pkg = document.querySelector('.of-pkg.selected');

    if (!biz) { alert('Please enter your Google Business name.'); return; }
    if (!wa)  { alert('Please enter your WhatsApp number.'); return; }
    if (!pkg) { alert('Please select a package.'); return; }

    var pkgValue = pkg.getAttribute('data-pkg');
    var pkgName  = pkg.querySelector('.of-pkg-name').textContent.replace(/POPULAR/g, '').trim();
    var meta = sbPkgMeta[pkgValue] || sbPkgMeta['dominator'];
    var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

    // Parse first/last name from business name
    var nameParts = biz.split(' ');
    var firstName = nameParts[0] || '';
    var lastName = nameParts.slice(1).join(' ') || '';

    // === dataLayer: begin_checkout (GA4 standard) + user_data ===
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'begin_checkout',
        user_data: {
            email: email || '',
            phone_number: wa,
            first_name: firstName,
            last_name: lastName
        },
        ecommerce: {
            currency: 'USD',
            value: meta.price,
            items: [{
                item_id: meta.id,
                item_name: meta.name,
                item_category: meta.item_category,
                price: meta.price,
                quantity: 1
            }]
        }
    });

    // === dataLayer: add_payment_info (GA4 standard, same click) ===
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
                item_category: meta.item_category,
                price: meta.price,
                quantity: 1
            }]
        }
    });

    // === LocalStorage bridge: persist user_data for purchase event on thankyou.php ===
    localStorage.setItem('sb_user_email', email || '');
    localStorage.setItem('sb_user_phone', wa || '');
    localStorage.setItem('sb_user_fname', firstName || '');
    localStorage.setItem('sb_user_lname', lastName || '');
    localStorage.setItem('sb_txn_id', txnId);
    localStorage.setItem('sb_pkg', pkgValue);

    // Facebook Pixel - InitiateCheckout (fallback)
    if (typeof fbq !== 'undefined') {
        fbq('track', 'InitiateCheckout', {
            value: meta.price,
            currency: 'USD',
            content_name: pkgName,
            content_type: 'product',
            content_ids: [pkgValue]
        });
    }
    // TikTok Pixel - InitiateCheckout (fallback)
    if (typeof ttq !== 'undefined') {
        ttq.track('InitiateCheckout', {
            value: meta.price,
            currency: 'USD',
            content_name: pkgName
        });
    }

    // Track order submission in analytics
    logAnalyticsEvent('ORDER_SUBMIT', {
        package: pkgName,
        price: meta.price,
        business: biz,
        location: 'order_form'
    });

    // Log customer data to customer_data.log via log.php
    fetch('log.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            businessName: biz,
            whatsapp: wa,
            businessEmail: email,
            action: pkgValue.toUpperCase(),
            pageUrl: window.location.href
        })
    }).catch(function(err) { console.log('Customer log error:', err); });

    // Fanbasis payment links per package
    var fanbasisLinks = {
        'booster':   'https://www.fanbasis.com/agency-checkout/smartbuzzer/Rgl3R',
        'dominator': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/wpk38'
    };

    var paymentUrl = fanbasisLinks[pkgValue];
    if (paymentUrl) {
        // Clear saved form data
        safeLocalStorage('set', 'sb_form_biz', '');
        safeLocalStorage('set', 'sb_form_wa', '');
        safeLocalStorage('set', 'sb_form_email', '');
        safeLocalStorage('set', 'sb_form_pkg', '');
        // Redirect directly to Fanbasis payment gateway
        // Fanbasis will redirect back to thankyou.php after payment
        window.location.href = paymentUrl;
    }
}

// Countdown Timer
function updateCountdown() {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);

    const diff = tomorrow - now;

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
}

updateCountdown();
setInterval(updateCountdown, 1000);

// Image Modal Preview
const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const modalClose = document.getElementById('modalClose');

document.querySelectorAll('img[data-preview]').forEach(img => {
    img.addEventListener('click', function() {
        modal.classList.add('active');
        modalImg.src = this.src;
        document.body.style.overflow = 'hidden';
    });
});

modalClose.addEventListener('click', function() {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
});

modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});

// ===== DIRECT CHECKOUT - NO FORM MODAL =====
// Simple click tracking without form submission

function logOrderClick(action) {
    const currentUrl = window.location.href;

    const data = {
        url: currentUrl,
        action: action
    };

    fetch('log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Click logged:', data);
    })
    .catch(error => {
        console.error('Error logging:', error);
    });
}

// === DUPLICATE TRACKING COMMENTED OUT - BUG FIX ===
// This section was causing double tracking. Already handled in lines 1439-1475
/*
// Track Order Button Clicks - Direct redirect without form
document.addEventListener('DOMContentLoaded', function() {
    const orderButtons = document.querySelectorAll('a[href*="fanbasis.com"]');

    orderButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Don't prevent default - let it go directly to checkout

            const href = this.getAttribute('href');
            let action = 'ORDER_BUTTON_CLICK';

            if (href.includes('Rgl3R')) {
                action = 'ORDER_STARTER_PACKAGE';
            } else if (href.includes('Vmv31')) {
                action = 'ORDER_GROWTH_PACKAGE';
            } else if (href.includes('WnP3g')) {
                action = 'ORDER_PERFORMANCE_PACKAGE';
            }

            // Log the click (async - won't block redirect)
            logOrderClick(action);
        });
    });

    // Track hero LEARN MORE clicks
    document.querySelectorAll('a[href="#pricing"]').forEach(button => {
        if (button.textContent.includes('LEARN MORE')) {
            button.addEventListener('click', function() {
                logOrderClick('LEARN_MORE_HERO');
            });
        }
    });

    // Track WhatsApp clicks
    const whatsappButtons = document.querySelectorAll('a[href*="wa.me"]');

    whatsappButtons.forEach(button => {
        button.addEventListener('click', function() {
            logOrderClick('WHATSAPP_CLICK');
        });
    });
});
*/

// ===== SCROLL REVEAL (IntersectionObserver) =====
(function() {
    var fadeEls = document.querySelectorAll('.fade-up');
    if (!fadeEls.length) return;
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    fadeEls.forEach(function(el) { observer.observe(el); });
})();

// ===== FAQ ACCORDION =====
function toggleFaq(btn) {
    var item = btn.parentElement;
    var isOpen = item.classList.contains('open');
    // Close all
    document.querySelectorAll('.faq-item.open').forEach(function(el) {
        el.classList.remove('open');
    });
    // Open clicked if it was closed
    if (!isOpen) {
        item.classList.add('open');
    }
}

// ===== STICKY CTA =====
function stickyCTAClick() {
    var target = document.getElementById('order-form');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    return false;
}

(function() {
    var cta = document.getElementById('stickyCta');
    var orderForm = document.getElementById('order-form');
    if (!cta || !orderForm) return;
    window.addEventListener('scroll', function() {
        var rect = orderForm.getBoundingClientRect();
        // Hide sticky CTA when order form is visible
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            cta.style.display = 'none';
        } else {
            cta.style.display = 'block';
        }
    }, { passive: true });
})();

// UVP Modal Functions
function openUvpModalB1g1(modalId) {
    const uvpModal = document.getElementById(modalId);
    if (uvpModal) {
        uvpModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeUvpModalB1g1(modalId) {
    const uvpModal = document.getElementById(modalId);
    if (uvpModal) {
        uvpModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Close UVP modals on backdrop click
document.querySelectorAll('.uvp-modal-b1g1').forEach(function(uvpModal) {
    uvpModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
});

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUvpModalB1g1('ratingsModalB1g1');
        closeUvpModalB1g1('namesModalB1g1');
    }
});
</script>
</body>
</html>
