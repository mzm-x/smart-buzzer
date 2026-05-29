<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Industry-Tailored Google Reviews | HVAC, Restaurants, Construction, Healthcare & 12+ More</title>
    <meta name="description" content="Real Google reviews crafted for your industry. Trusted by 1,200+ SMBs across HVAC, restaurants, construction, healthcare, auto, plumbing, roofing, accounting & more. Gradual delivery, real local accounts.">
    <meta property="og:title" content="Smart Buzzer - Industry-Tailored Google Reviews">
    <meta property="og:description" content="Real Google reviews customized for your industry — Construction, Restaurants, Healthcare, Auto, and more.">
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    <!-- Analytics Tracking Variables -->
    <script>
    let sessionId = null;
    let pageLoadTime = Date.now();
    let scrollDepths = {25: false, 50: false, 75: false, 100: false};
    let returnVisitor = false;

    function safeLocalStorage(action, key, value) {
        try {
            if (action === 'get') return localStorage.getItem(key);
            if (action === 'set') return localStorage.setItem(key, value);
            return null;
        } catch (e) { return null; }
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

    function logAnalyticsEvent(eventType, data) {
        data = data || {};
        const payload = JSON.stringify({
            event_type: eventType,
            page_url: window.location.href,
            data: JSON.stringify(data),
            session_id: sessionId
        });
        fetch('analytics.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: payload
        }).catch(function(err) { console.log('Analytics error:', err); });
    }

    function beaconAnalyticsEvent(eventType, data) {
        data = data || {};
        const payload = JSON.stringify({
            event_type: eventType,
            page_url: window.location.href,
            data: JSON.stringify(data),
            session_id: sessionId
        });
        if (navigator.sendBeacon) {
            navigator.sendBeacon('analytics.php', new Blob([payload], {type: 'application/json'}));
        }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --navy-deep: #0F172A;
            --navy: #1E293B;
            --navy-mid: #334155;
            --amber: #F59E0B;
            --amber-deep: #D97706;
            --amber-light: #FEF3C7;
            --amber-soft: #FFFBEB;
            --emerald: #10B981;
            --emerald-deep: #059669;
            --emerald-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --paper: #FFFFFF;
            --paper-soft: #F8FAFC;
            --paper-mid: #F1F5F9;
            --border: #E2E8F0;
            --border-strong: #CBD5E1;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-sm: 0 1px 2px rgba(15,23,42,0.06), 0 1px 1px rgba(15,23,42,0.04);
            --shadow-md: 0 4px 12px rgba(15,23,42,0.08), 0 2px 4px rgba(15,23,42,0.04);
            --shadow-lg: 0 12px 36px rgba(15,23,42,0.10), 0 4px 12px rgba(15,23,42,0.06);
            --shadow-xl: 0 24px 56px rgba(15,23,42,0.12), 0 8px 20px rgba(15,23,42,0.06);
            --shadow-amber: 0 8px 24px rgba(245,158,11,0.35);
            --font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: var(--font);
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--paper);
            -webkit-font-smoothing: antialiased;
            padding-top: 76px;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .container-narrow { max-width: 920px; margin: 0 auto; padding: 0 20px; }

        /* ===== HEADER ===== */
        header {
            background: var(--paper);
            border-bottom: 1px solid var(--border);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            height: 76px;
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            gap: 16px;
        }
        .logo-wrap { display: flex; align-items: center; gap: 12px; }
        .logo img { height: 42px; display: block; }
        .logo-tag {
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 600;
            line-height: 1.3;
            border-left: 2px solid var(--amber);
            padding-left: 10px;
        }
        .logo-tag strong { color: var(--navy-deep); display: block; }
        nav ul { list-style: none; display: flex; gap: 28px; align-items: center; }
        nav a {
            color: var(--navy-mid);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.2s;
        }
        nav a:hover { color: var(--navy-deep); }
        .nav-cta {
            background: var(--amber);
            color: var(--navy-deep) !important;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            font-weight: 800 !important;
            font-size: 14px !important;
        }
        .nav-cta:hover { background: var(--amber-deep); color: white !important; }
        .header-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--navy-deep);
            background: var(--paper-mid);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
        }
        .header-rating .stars { color: var(--amber); }
        @media (max-width: 980px) {
            .logo-tag { display: none; }
            nav ul li:not(:last-child):not(.header-rating-item) { display: none; }
        }
        @media (max-width: 768px) {
            .header-rating { display: none; }
        }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, var(--navy-deep) 0%, var(--navy) 100%);
            color: white;
            padding: 64px 0 88px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -20%; right: -15%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(245,158,11,0.18) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 56px;
            align-items: center;
            position: relative;
        }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.35);
            color: var(--amber);
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }
        .hero-eyebrow .stars-mini { color: var(--amber); letter-spacing: 1px; }
        .hero h1 {
            font-size: 52px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.025em;
            margin-bottom: 16px;
        }
        .hero h1 .accent { color: var(--amber); }
        .hero h1 .strike {
            color: rgba(255,255,255,0.4);
            text-decoration: line-through;
            font-weight: 700;
        }
        .hero p.lead {
            font-size: 18px;
            color: rgba(255,255,255,0.85);
            margin-bottom: 28px;
            line-height: 1.6;
            max-width: 540px;
        }
        .hero-bullets {
            list-style: none;
            margin-bottom: 32px;
        }
        .hero-bullets li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 15px;
            color: rgba(255,255,255,0.9);
            padding: 6px 0;
            text-align: left;
            line-height: 1.5;
        }
        .hero-bullets li > span { flex: 1; }
        .hero-bullets li strong { display: inline; }
        .hero-bullets i {
            color: var(--emerald);
            font-size: 14px;
            background: rgba(16,185,129,0.15);
            border-radius: 50%;
            width: 22px; height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 3px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 28px;
            border-radius: var(--radius-sm);
            font-weight: 800;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.25s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--amber);
            color: var(--navy-deep);
            box-shadow: var(--shadow-amber);
        }
        .btn-primary:hover {
            background: var(--amber-deep);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(245,158,11,0.45);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.08);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); }
        .hero-ctas { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
        .hero-microtrust {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            flex-wrap: wrap;
        }
        .hero-microtrust .live-dot {
            width: 8px; height: 8px;
            background: #22C55E;
            border-radius: 50%;
            animation: pulseDot 1.5s infinite;
            display: inline-block;
            margin-right: 6px;
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
            50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
        }

        /* Hero Visual: Google Business Profile mockup */
        .gbp-mockup {
            position: relative;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
            padding: 24px;
            color: var(--text-primary);
        }
        .gbp-mockup::before {
            content: 'LIVE PREVIEW';
            position: absolute;
            top: -12px; right: 16px;
            background: var(--amber);
            color: var(--navy-deep);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
        }
        .gbp-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 14px;
        }
        .gbp-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4285F4 0%, #34A853 50%, #FBBC04 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 16px;
        }
        .gbp-bizname {
            font-weight: 800;
            font-size: 15px;
            color: var(--navy-deep);
        }
        .gbp-bizmeta {
            font-size: 12px;
            color: var(--text-muted);
        }
        .gbp-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .gbp-rating-num {
            font-size: 28px;
            font-weight: 800;
            color: var(--navy-deep);
        }
        .gbp-rating-stars { color: var(--amber); font-size: 14px; }
        .gbp-rating-count {
            font-size: 13px;
            color: var(--text-secondary);
        }
        .gbp-counter {
            background: linear-gradient(90deg, var(--emerald-light) 0%, var(--amber-light) 100%);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 12px;
            color: var(--navy-deep);
            font-weight: 600;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .gbp-counter-num {
            font-size: 16px;
            font-weight: 800;
            color: var(--emerald-deep);
        }
        .gbp-review-mini {
            background: var(--paper-soft);
            border-left: 3px solid var(--amber);
            padding: 10px 12px;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            margin-bottom: 8px;
        }
        .gbp-review-mini .gbp-rev-stars { color: var(--amber); font-size: 11px; margin-bottom: 4px; }
        .gbp-review-mini .gbp-rev-text {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.45;
            margin-bottom: 4px;
        }
        .gbp-review-mini .gbp-rev-author {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .gbp-trend-arrow {
            position: absolute;
            top: 80px;
            right: -32px;
            background: var(--emerald);
            color: white;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
            transform: rotate(8deg);
        }

        .hero-photo-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: var(--radius-xl);
            padding: 20px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
            max-width: 580px;
            margin: 0 auto;
        }
        .hero-photo {
            width: 100%;
            max-width: 540px;
            height: auto;
            display: block;
            filter: drop-shadow(0 24px 48px rgba(0,0,0,0.35));
        }

        @media (max-width: 980px) {
            .hero { padding: 32px 0 56px; }
            .hero-grid { grid-template-columns: 1fr; gap: 28px; }
            .hero-text-col { order: 2; }
            .hero-photo-wrap { order: 1; max-width: 480px; padding: 14px; }
            .hero h1 { font-size: 32px; }
            .hero p.lead { font-size: 15px; max-width: 100%; }
            .hero-photo { max-width: 100%; }
        }
        @media (max-width: 480px) {
            .hero h1 { font-size: 26px; line-height: 1.1; }
            .hero p.lead { font-size: 14px; line-height: 1.55; }
            .hero-bullets li { font-size: 13.5px; }
            .hero-photo-wrap { padding: 12px; border-radius: var(--radius-lg); }
        }

        /* ===== TRUST BAR ===== */
        .trust-bar {
            background: var(--paper-mid);
            padding: 18px 0;
            border-bottom: 1px solid var(--border);
            overflow: hidden;
        }
        .trust-bar-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .trust-bar-inner i { color: var(--emerald); margin-right: 6px; }
        .trust-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--emerald-light);
            color: var(--emerald-deep);
            font-weight: 700;
        }
        .trust-live .live-dot-sm {
            width: 7px; height: 7px;
            background: var(--emerald);
            border-radius: 50%;
            animation: pulseDot 1.5s infinite;
        }
        @media (max-width: 768px) {
            .trust-bar-inner { gap: 14px; font-size: 11px; }
        }

        /* ===== SECTION HEADER ===== */
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .section-eyebrow {
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            color: var(--amber-deep);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 10px;
        }
        .section-header h2 {
            font-size: 40px;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 14px;
            color: var(--navy-deep);
        }
        .section-header h2 .accent { color: var(--amber-deep); }
        .section-header p {
            font-size: 17px;
            color: var(--text-secondary);
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .section-header h2 { font-size: 28px; }
            .section-header p { font-size: 15px; }
        }

        /* ===== INDUSTRY SELECTOR ===== */
        .industry-selector {
            padding: 80px 0;
            background: var(--paper-soft);
        }
        .industry-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }
        .industry-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 18px;
            background: white;
            border: 2px solid var(--border);
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }
        .industry-pill i { font-size: 14px; color: var(--text-muted); }
        .industry-pill:hover {
            border-color: var(--amber);
            transform: translateY(-1px);
        }
        .industry-pill.active {
            background: var(--navy-deep);
            border-color: var(--navy-deep);
            color: white;
        }
        .industry-pill.active i { color: var(--amber); }
        .industry-more-wrap {
            position: relative;
            display: inline-block;
        }
        .industry-more-btn {
            background: white;
            border: 2px solid var(--border);
            border-radius: 999px;
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            cursor: pointer;
            font-family: inherit;
        }
        .industry-more-btn:hover { border-color: var(--amber); }
        .industry-more-list {
            display: none;
            position: absolute;
            top: 110%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 50;
            min-width: 220px;
            max-height: 280px;
            overflow-y: auto;
        }
        .industry-more-list.show { display: block; }
        .industry-more-list button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 10px 12px;
            font-size: 14px;
            color: var(--navy);
            cursor: pointer;
            border-radius: var(--radius-sm);
            text-align: left;
            font-family: inherit;
            font-weight: 500;
        }
        .industry-more-list button:hover { background: var(--paper-soft); }

        .industry-content {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .industry-content-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 0;
        }
        .industry-content-left {
            padding: 44px 40px;
            background: linear-gradient(135deg, var(--navy-deep) 0%, var(--navy) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .industry-content-left::before {
            content: '';
            position: absolute;
            bottom: -40%; right: -20%;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .ic-icon {
            width: 64px; height: 64px;
            border-radius: var(--radius-md);
            background: transparent;
            border: 2px solid var(--amber);
            color: var(--amber);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 22px;
            position: relative;
        }
        .industry-content-left h3 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 12px;
            letter-spacing: -0.015em;
            position: relative;
        }
        .ic-subtitle {
            color: rgba(255,255,255,0.82);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 28px;
            position: relative;
        }
        .ic-checklist {
            list-style: none;
            position: relative;
            margin-top: 4px;
        }
        .ic-checklist li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            font-size: 14px;
            color: rgba(255,255,255,0.88);
            line-height: 1.5;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .ic-checklist li:last-child { border-bottom: none; }
        .ic-checklist li i {
            color: var(--navy-deep);
            background: var(--amber);
            border-radius: 50%;
            width: 20px; height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .ic-checklist li strong { color: white; font-weight: 800; }

        .ic-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            position: relative;
        }
        .ic-stat {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-left: 3px solid var(--amber);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            display: flex;
            align-items: baseline;
            gap: 12px;
        }
        .ic-stat-num {
            color: var(--amber);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .ic-stat-label {
            color: rgba(255,255,255,0.78);
            font-size: 13px;
            font-weight: 500;
            line-height: 1.35;
        }
        .industry-content-right { padding: 44px 40px; }
        .industry-content-right h4 {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 14px;
        }
        .ic-painpoints {
            list-style: none;
            margin-bottom: 24px;
        }
        .ic-painpoints li {
            padding: 6px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: var(--text-primary);
        }
        .ic-painpoints i {
            color: var(--red);
            margin-top: 5px;
            flex-shrink: 0;
            font-size: 11px;
        }
        /* Sample review card — KILLER FEATURE */
        .ic-sample-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--amber-light);
            color: var(--amber-deep);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }
        .ic-sample-card {
            background: var(--paper-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            margin-bottom: 18px;
        }
        .ic-sample-stars { color: var(--amber); font-size: 13px; margin-bottom: 8px; }
        .ic-sample-text {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .ic-sample-author {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }
        .ic-sample-avatar {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: var(--navy-mid);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }
        .ic-sample-photo {
            width: 28px; height: 28px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-color: var(--paper-mid);
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }
        .ic-testimonial-row {
            display: flex;
            gap: 12px;
            align-items: center;
            background: linear-gradient(135deg, var(--amber-soft) 0%, white 100%);
            border-left: 4px solid var(--amber);
            padding: 14px 16px;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        }
        .ic-test-photo {
            width: 48px; height: 48px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-color: var(--paper-mid);
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }
        .ic-test-quote {
            font-size: 13px;
            font-style: italic;
            color: var(--text-secondary);
            line-height: 1.55;
            margin-bottom: 4px;
        }
        .ic-test-author {
            font-size: 12px;
            font-weight: 700;
            color: var(--navy-deep);
        }
        @media (max-width: 880px) {
            .industry-content-grid { grid-template-columns: 1fr; }
            .industry-content-left, .industry-content-right { padding: 32px 24px; }
            .industry-content-left h3 { font-size: 22px; }
        }

        /* ===== HOW IT WORKS — TIMELINE ===== */
        .how-it-works {
            padding: 80px 0;
            background: var(--paper);
        }
        .timeline-wrap {
            position: relative;
            margin-top: 40px;
        }
        .timeline-track {
            position: absolute;
            top: 28px;
            left: 16%;
            right: 16%;
            height: 4px;
            background: linear-gradient(90deg, var(--amber) 0%, var(--emerald) 100%);
            border-radius: 999px;
            z-index: 1;
        }
        .timeline-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            position: relative;
            z-index: 2;
        }
        .timeline-step {
            text-align: center;
            padding: 0 8px;
        }
        .timeline-dot {
            width: 56px; height: 56px;
            background: var(--navy-deep);
            color: var(--amber);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 22px;
            margin-bottom: 16px;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
            position: relative;
        }
        .timeline-day {
            display: inline-block;
            background: var(--amber-light);
            color: var(--amber-deep);
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .timeline-step h4 {
            font-size: 17px;
            font-weight: 800;
            color: var(--navy-deep);
            margin-bottom: 8px;
        }
        .timeline-step p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.55;
        }
        @media (max-width: 880px) {
            .timeline-grid { grid-template-columns: 1fr; gap: 32px; }
            .timeline-track { display: none; }
            .timeline-step { padding: 0; }
        }

        /* ===== WHY US ===== */
        .why-us {
            padding: 80px 0;
            background: var(--paper-soft);
        }
        .why-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .why-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: all 0.25s;
        }
        .why-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--amber);
            transform: translateY(-2px);
        }
        .why-icon {
            width: 56px; height: 56px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .why-icon-1 { background: var(--amber-light); color: var(--amber-deep); }
        .why-icon-2 { background: var(--emerald-light); color: var(--emerald-deep); }
        .why-icon-3 { background: #DBEAFE; color: #1D4ED8; }
        .why-icon-4 { background: #F3E8FF; color: #7C3AED; }
        .why-card h4 {
            font-size: 18px;
            font-weight: 800;
            color: var(--navy-deep);
            margin-bottom: 8px;
        }
        .why-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .why-grid { grid-template-columns: 1fr; }
        }

        /* ===== COMPARISON TABLE ===== */
        .comparison {
            padding: 80px 0;
            background: var(--paper);
        }
        .compare-table-wrap {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-top: 32px;
        }
        .compare-table {
            width: 100%;
            border-collapse: collapse;
        }
        .compare-table thead th {
            background: var(--paper-mid);
            padding: 18px 16px;
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            color: var(--navy-deep);
            border-bottom: 2px solid var(--border);
        }
        .compare-table thead th.compare-us {
            background: linear-gradient(180deg, var(--navy-deep) 0%, var(--navy) 100%);
            color: var(--amber);
            position: relative;
        }
        .compare-table thead th.compare-us::after { display: none; }
        .compare-table tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            text-align: center;
            color: var(--text-primary);
        }
        .compare-table tbody td:first-child {
            text-align: left;
            font-weight: 700;
            color: var(--navy-deep);
            background: var(--paper-soft);
            font-size: 13px;
        }
        .compare-table tbody td.us {
            background: var(--amber-soft);
            color: var(--navy-deep);
            font-weight: 700;
        }
        .compare-yes { color: var(--emerald-deep); font-size: 18px; }
        .compare-no { color: var(--red); font-size: 18px; }
        .compare-meh { color: var(--amber-deep); font-size: 14px; font-weight: 700; }
        @media (max-width: 768px) {
            .compare-table thead th, .compare-table tbody td { padding: 10px 6px; font-size: 11px; }
            .compare-table tbody td:first-child { font-size: 11px; padding: 10px 8px; }
            .compare-yes, .compare-no { font-size: 14px; }
            .compare-table thead th.compare-us::after { font-size: 9px; padding: 2px 6px; }
        }
        @media (max-width: 480px) {
            .compare-table thead th { padding: 14px 4px; font-size: 10px; line-height: 1.3; }
            .compare-table tbody td { padding: 10px 4px; font-size: 10.5px; }
            .compare-table tbody td:first-child { font-size: 10px; padding: 10px 6px; }
        }

        /* ===== PRICING ===== */
        .pricing {
            padding: 80px 0;
            background: var(--paper-soft);
        }
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 40px;
        }
        .price-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 32px 28px;
            position: relative;
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
        }
        .price-card.featured {
            border-color: var(--amber);
            box-shadow: var(--shadow-lg);
            transform: scale(1.04);
            background: linear-gradient(180deg, white 0%, var(--amber-soft) 100%);
        }
        .price-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        .price-card.featured:hover {
            transform: scale(1.04) translateY(-4px);
        }
        .price-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--amber);
            color: var(--navy-deep);
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            white-space: nowrap;
            box-shadow: var(--shadow-md);
        }
        .price-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }
        .price-fit {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            min-height: 38px;
            line-height: 1.5;
        }
        .price-amount {
            font-size: 48px;
            font-weight: 900;
            color: var(--navy-deep);
            line-height: 1;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }
        .price-amount .price-old {
            font-size: 17px;
            color: var(--text-muted);
            text-decoration: line-through;
            font-weight: 600;
            margin-right: 8px;
        }
        .price-reviews {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 6px;
        }
        .price-reviews strong { color: var(--amber-deep); font-weight: 800; }
        .price-perreview {
            display: inline-block;
            align-self: flex-start;
            background: var(--emerald-light);
            color: var(--emerald-deep);
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 22px;
            width: auto;
        }
        .price-features {
            list-style: none;
            flex: 1;
            margin-bottom: 24px;
        }
        .price-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
        }
        .price-features li:last-child { border-bottom: none; }
        .price-features i.feat-icon {
            color: var(--emerald);
            margin-top: 4px;
            flex-shrink: 0;
        }
        .price-features .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px; height: 16px;
            border-radius: 50%;
            background: var(--navy-deep);
            color: var(--amber);
            font-size: 10px;
            font-weight: 700;
            margin-left: 6px;
            cursor: pointer;
            font-style: normal;
            transition: all 0.2s;
        }
        .price-features .info-icon:hover { background: var(--amber); color: var(--navy-deep); }
        .price-cta {
            display: block;
            width: 100%;
            padding: 14px 20px;
            text-align: center;
            background: var(--navy-deep);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-weight: 800;
            transition: all 0.2s;
        }
        .price-cta:hover { background: var(--amber); color: var(--navy-deep); }
        .price-card.featured .price-cta { background: var(--amber); color: var(--navy-deep); }
        .price-card.featured .price-cta:hover { background: var(--amber-deep); color: white; }
        .price-scarcity {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--red);
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .price-trust-strip {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 36px;
            padding: 18px 24px;
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .price-trust-strip span {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .price-trust-strip i { font-size: 18px; }
        @media (max-width: 880px) {
            .pricing-grid { grid-template-columns: 1fr; gap: 20px; }
            .price-card.featured { transform: none; }
            .price-card.featured:hover { transform: translateY(-4px); }
        }

        /* ===== ORDER FORM (split layout) ===== */
        .order-form-section {
            padding: 80px 0;
            background: linear-gradient(180deg, var(--paper-soft) 0%, var(--paper-mid) 100%);
        }
        .order-form-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            margin-top: 32px;
            align-items: flex-start;
        }
        .order-form-wrapper {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            padding: 36px;
        }
        .order-form-countdown {
            background: var(--red-light);
            border: 1px solid var(--red);
            color: var(--red);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            text-align: center;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .order-form-countdown i { color: var(--red); }
        .of-group { margin-bottom: 18px; }
        .of-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--navy-deep);
            margin-bottom: 6px;
        }
        .of-req { color: var(--red); }
        .of-input, select.of-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-family: inherit;
            background: var(--paper-soft);
            transition: all 0.2s;
            color: var(--navy-deep);
        }
        .of-input:focus, select.of-input:focus {
            outline: none;
            border-color: var(--amber);
            background: white;
            box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
        }
        .of-package-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--navy-deep);
            margin: 24px 0 10px;
        }
        .of-packages { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
        .of-pkg {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: var(--paper-soft);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
        }
        .of-pkg:hover { border-color: var(--amber); }
        .of-pkg.selected {
            background: var(--amber-soft);
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        .of-pkg-radio { margin: 0; cursor: pointer; accent-color: var(--amber); }
        .of-pkg-info { flex: 1; }
        .of-pkg-name { font-weight: 800; font-size: 14px; color: var(--navy-deep); display: block; }
        .of-pkg-detail { font-size: 12px; color: var(--text-secondary); }
        .of-pkg-detail s { color: var(--text-muted); }
        .of-pkg-popular {
            display: inline-block;
            background: var(--amber);
            color: var(--navy-deep);
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            margin-left: 6px;
            letter-spacing: 0.04em;
        }
        .of-pkg-price { font-weight: 900; color: var(--navy-deep); font-size: 17px; }
        .of-submit {
            width: 100%;
            padding: 16px 24px;
            background: var(--amber);
            color: var(--navy-deep);
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: var(--shadow-amber);
            font-family: inherit;
            letter-spacing: 0.02em;
        }
        .of-submit:hover {
            background: var(--amber-deep);
            color: white;
            transform: translateY(-2px);
        }
        .of-trust {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 16px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* What happens next sidebar */
        .next-side {
            background: var(--navy-deep);
            color: white;
            border-radius: var(--radius-xl);
            padding: 36px 32px;
            position: sticky;
            top: 96px;
        }
        .next-side h3 {
            color: var(--amber);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }
        .next-side h4 {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 24px;
            line-height: 1.3;
        }
        .next-step {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .next-step:last-of-type { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .next-num {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--amber);
            color: var(--navy-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
            flex-shrink: 0;
        }
        .next-text-h {
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 4px;
            color: white;
        }
        .next-text-p {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            line-height: 1.55;
        }
        .next-guarantee {
            margin-top: 24px;
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: var(--radius-sm);
            padding: 14px;
            font-size: 13px;
            color: rgba(255,255,255,0.95);
            line-height: 1.5;
        }
        .next-guarantee i { color: var(--emerald); margin-right: 6px; }
        @media (max-width: 880px) {
            .order-form-grid { grid-template-columns: 1fr; }
            .next-side { position: static; }
        }

        /* ===== CASE STUDY ===== */
        .case-study {
            padding: 80px 0;
            background: var(--navy-deep);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .case-study::before {
            content: '';
            position: absolute;
            top: -30%; left: -10%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .case-study .section-header h2 { color: white; }
        .case-study .section-header h2 .accent { color: var(--amber); }
        .case-study .section-header p { color: rgba(255,255,255,0.7); }
        .case-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 40px;
            align-items: center;
            position: relative;
        }
        .case-narrative h3 {
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        .case-narrative .case-meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.08);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: var(--amber);
            margin-bottom: 14px;
        }
        .case-narrative p {
            color: rgba(255,255,255,0.8);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .case-numbers {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 24px;
            max-width: 320px;
        }
        .case-number {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-md);
            padding: 16px;
        }
        .case-number-from {
            font-size: 11px;
            color: var(--red);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }
        .case-number-from-val {
            font-size: 22px;
            font-weight: 800;
            color: rgba(255,255,255,0.5);
            text-decoration: line-through;
            margin-bottom: 6px;
        }
        .case-number-to {
            font-size: 11px;
            color: var(--emerald);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }
        .case-number-to-val {
            font-size: 30px;
            font-weight: 900;
            color: var(--amber);
        }
        .case-number-label {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }
        /* Case study visual */
        .case-visual {
            background: white;
            border-radius: var(--radius-xl);
            padding: 12px;
            color: var(--navy-deep);
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
            position: relative;
        }
        .case-visual-header {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }
        .case-chart {
            height: 180px;
            background: linear-gradient(180deg, var(--paper-soft) 0%, white 100%);
            border-radius: var(--radius-md);
            position: relative;
            margin-bottom: 18px;
            padding: 16px;
            overflow: hidden;
        }
        .case-chart-bars {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 100%;
            gap: 4px;
        }
        .case-bar {
            flex: 1;
            background: linear-gradient(180deg, var(--amber) 0%, var(--amber-deep) 100%);
            border-radius: 4px 4px 0 0;
            position: relative;
        }
        .case-bar.low { background: linear-gradient(180deg, var(--text-muted) 0%, var(--border-strong) 100%); }
        .case-chart-label {
            position: absolute;
            bottom: -2px;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .case-rating-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px;
            background: var(--paper-soft);
            border-radius: var(--radius-md);
        }
        .case-rating-row + .case-rating-row { margin-top: 8px; }
        .case-rating-period {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .case-rating-stars {
            color: var(--amber);
            font-size: 14px;
        }
        .case-rating-num {
            font-size: 18px;
            font-weight: 900;
            color: var(--navy-deep);
        }
        @media (max-width: 880px) {
            .case-grid { grid-template-columns: 1fr; gap: 32px; }
            .case-narrative { order: 2; }
            .case-visual { order: 1; }
            .case-narrative h3 { font-size: 22px; }
        }

        /* ===== TESTIMONIALS — MIXED MEDIA ===== */
        .testimonials {
            padding: 80px 0;
            background: var(--paper);
        }
        .testimonial-mosaic {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }
        .test-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
        }
        .test-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        /* Video tile */
        .test-video {
            position: relative;
            aspect-ratio: 16/10;
            background-size: cover;
            background-position: center;
            background-color: var(--paper-mid);
            cursor: pointer;
        }
        .test-video::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.6) 100%);
        }
        .test-video-play {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
        }
        .test-video-play i {
            color: var(--navy-deep);
            font-size: 22px;
            margin-left: 4px;
        }
        .test-video-label {
            position: absolute;
            bottom: 14px;
            left: 14px;
            background: var(--amber);
            color: var(--navy-deep);
            font-size: 10px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 4px;
            letter-spacing: 0.04em;
            z-index: 2;
        }
        /* Screenshot tile (Google Business Profile mockup) */
        .test-screenshot {
            background: var(--paper-soft);
            padding: 18px;
            position: relative;
            min-height: 220px;
        }
        .test-screen-label {
            position: absolute;
            top: 12px; right: 12px;
            background: var(--emerald);
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.04em;
            z-index: 2;
        }
        .test-gbp {
            background: white;
            border-radius: var(--radius-sm);
            padding: 14px;
            box-shadow: var(--shadow-sm);
        }
        .test-gbp-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--navy-deep);
            margin-bottom: 6px;
        }
        .test-gbp-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        .test-gbp-rating-num {
            font-size: 20px;
            font-weight: 900;
            color: var(--navy-deep);
        }
        .test-gbp-stars { color: var(--amber); font-size: 12px; }
        .test-gbp-count { font-size: 11px; color: var(--text-muted); }
        .test-gbp-bar {
            display: grid;
            grid-template-columns: 16px 1fr 24px;
            gap: 8px;
            align-items: center;
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .test-gbp-bar-fill {
            height: 6px;
            background: var(--paper-mid);
            border-radius: 999px;
            overflow: hidden;
        }
        .test-gbp-bar-fill > div {
            height: 100%;
            background: var(--amber);
            border-radius: 999px;
        }
        /* Card content */
        .test-content {
            padding: 18px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .test-quote {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.55;
            margin-bottom: 14px;
            flex: 1;
        }
        .test-author-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .test-photo {
            width: 44px; height: 44px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-color: var(--paper-mid);
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }
        .test-name {
            font-weight: 800;
            font-size: 14px;
            color: var(--navy-deep);
        }
        .test-meta {
            font-size: 12px;
            color: var(--text-muted);
        }
        @media (max-width: 880px) {
            .testimonial-mosaic { grid-template-columns: 1fr; }
        }

        /* ===== REAL LOCAL REVIEWS ===== */
        .real-reviews {
            padding: 80px 0;
            background: var(--paper);
        }
        .rr-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
            align-items: center;
            margin-top: 32px;
        }
        .rr-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 28px 28px 26px;
            box-shadow: var(--shadow-md);
            position: relative;
        }
        .rr-tag {
            display: inline-block;
            background: var(--amber-light);
            color: var(--amber-deep);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            margin-bottom: 14px;
        }
        .rr-stars {
            color: var(--amber);
            font-size: 16px;
            margin-bottom: 10px;
        }
        .rr-text {
            font-size: 15px;
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 18px;
        }
        .rr-author {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        .rr-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--navy-deep);
            color: var(--amber);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }
        .rr-name { font-weight: 800; font-size: 14px; color: var(--navy-deep); }
        .rr-meta { font-size: 12px; color: var(--text-muted); }
        .rr-features h3 {
            font-size: 26px;
            font-weight: 900;
            color: var(--navy-deep);
            margin-bottom: 14px;
            line-height: 1.25;
        }
        .rr-features-lead {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.65;
            margin-bottom: 20px;
        }
        .rr-features-lead strong { color: var(--amber-deep); font-weight: 800; }
        .rr-features-list { list-style: none; }
        .rr-features-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
            text-align: left;
        }
        .rr-features-list li:last-child { border-bottom: none; }
        .rr-features-list i { color: var(--emerald); margin-top: 4px; flex-shrink: 0; }
        .rr-features-list li > div { flex: 1; text-align: left; }
        .rr-features-list li strong {
            display: block;
            color: var(--navy-deep);
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 2px;
        }
        .rr-features-list li span {
            display: block;
            color: var(--text-secondary);
            font-size: 13.5px;
            line-height: 1.5;
        }
        @media (max-width: 880px) {
            .rr-grid { grid-template-columns: 1fr; gap: 28px; }
            .rr-grid .rr-image-only { order: 1; }
            .rr-grid .rr-features { order: 2; }
            .rr-features h3 { font-size: 22px; }
        }
        /* Single image wrapper (replaces rr-card text version) */
        .rr-image-only {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        .rr-image-only img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: var(--radius-xl);
        }
        /* Single-section image wrapper (full-width centered image) */
        .single-image-wrap {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        .single-image-wrap img {
            width: 100%;
            height: auto;
            display: block;
        }
        /* Trust-strip-big image addition */
        .ts-image-wrap {
            margin-top: 36px;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
            overflow: hidden;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .ts-image-wrap img {
            width: 100%;
            height: auto;
            display: block;
        }
        /* Clients logo grid */
        .clients-section {
            padding: 80px 0;
            background: var(--paper);
        }
        .ic-clients-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 32px;
            align-items: center;
        }
        .ic-client-logo {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 24px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 96px;
            transition: all 0.2s;
        }
        .ic-client-logo:hover {
            border-color: var(--amber);
            box-shadow: var(--shadow-sm);
        }
        .ic-client-logo img {
            max-width: 100%;
            max-height: 56px;
            object-fit: contain;
            filter: grayscale(40%);
            opacity: 0.85;
            transition: all 0.2s;
        }
        .ic-client-logo:hover img {
            filter: grayscale(0%);
            opacity: 1;
        }
        @media (max-width: 880px) {
            .ic-clients-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
        }
        /* Testimonial photo (replaces letter avatars) */
        .cs-photo {
            width: 44px; height: 44px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-color: var(--paper-mid);
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        /* ===== WHY PEOPLE USE OUR SERVICES ===== */
        .why-services {
            padding: 80px 0;
            background: var(--paper-soft);
        }
        .ws-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .ws-card {
            background: white;
            border: 1px solid var(--border);
            border-left: 4px solid var(--emerald);
            border-radius: var(--radius-lg);
            padding: 24px 26px;
            transition: all 0.25s;
        }
        .ws-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .ws-check {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--emerald-light);
            color: var(--emerald-deep);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-bottom: 14px;
        }
        .ws-card h4 {
            font-size: 17px;
            font-weight: 800;
            color: var(--navy-deep);
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .ws-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .ws-grid { grid-template-columns: 1fr; }
        }

        /* ===== TRACK ORDER ===== */
        .track-order {
            padding: 80px 0;
            background: var(--paper);
        }
        .track-grid {
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            gap: 48px;
            align-items: center;
        }
        .track-text h2 {
            font-size: 36px;
            font-weight: 900;
            color: var(--navy-deep);
            margin: 10px 0 16px;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        .track-text h2 .accent { color: var(--amber-deep); }
        .track-text p {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.65;
            margin-bottom: 22px;
        }
        .track-list { list-style: none; }
        .track-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 7px 0;
            font-size: 14px;
            color: var(--text-primary);
        }
        .track-list i { color: var(--emerald); margin-top: 4px; flex-shrink: 0; }
        .track-mockup {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: 0 24px 60px rgba(15,23,42,0.15);
            overflow: hidden;
        }
        .track-mockup-header {
            background: var(--navy-deep);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .track-dots { display: flex; gap: 6px; }
        .track-dots span {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
        }
        .track-dots span:first-child { background: #FF5F57; }
        .track-dots span:nth-child(2) { background: #FEBC2E; }
        .track-dots span:nth-child(3) { background: #28C840; }
        .track-mockup-title {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
        }
        .track-mockup-body { padding: 22px; }
        .track-progress-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }
        .track-progress-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .track-progress-num {
            font-size: 22px;
            font-weight: 900;
            color: var(--navy-deep);
        }
        .track-progress-bar {
            height: 10px;
            background: var(--paper-mid);
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .track-progress-bar > div {
            height: 100%;
            background: linear-gradient(90deg, var(--amber) 0%, var(--emerald) 100%);
            border-radius: 999px;
        }
        .track-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 18px;
        }
        .track-stat {
            background: var(--paper-soft);
            border-radius: var(--radius-sm);
            padding: 10px;
            text-align: center;
        }
        .track-stat-num {
            display: block;
            font-size: 20px;
            font-weight: 900;
            color: var(--navy-deep);
        }
        .track-stat-lbl {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .track-recent { border-top: 1px solid var(--border); padding-top: 14px; }
        .track-recent-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }
        .track-recent-stars { color: var(--amber); flex-shrink: 0; }
        .track-recent-name {
            color: var(--text-secondary);
            flex: 1;
            margin: 0 12px;
        }
        .track-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 999px;
            letter-spacing: 0.04em;
        }
        .track-badge.live {
            background: var(--emerald-light);
            color: var(--emerald-deep);
        }
        @media (max-width: 880px) {
            .track-grid { grid-template-columns: 1fr; gap: 32px; }
            .track-text h2 { font-size: 26px; }
        }

        /* ===== TRUST STRIP BIG ===== */
        .trust-strip-big {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--navy-deep) 0%, var(--navy) 100%);
            color: white;
        }
        .ts-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }
        .ts-num {
            font-size: 72px;
            font-weight: 900;
            color: var(--amber);
            line-height: 1;
            letter-spacing: -0.03em;
            flex-shrink: 0;
        }
        .ts-text h3 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
            line-height: 1.25;
        }
        .ts-text p {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            line-height: 1.55;
        }
        @media (max-width: 768px) {
            .ts-inner { flex-direction: column; gap: 16px; text-align: center; }
            .ts-num { font-size: 56px; }
            .ts-text h3 { font-size: 20px; }
        }

        /* ===== CLIENTS SAY (CAROUSEL) ===== */
        .clients-say {
            padding: 80px 0;
            background: var(--paper-soft);
            overflow: hidden;
        }
        .cs-carousel-wrap {
            position: relative;
            margin-top: 32px;
        }
        .cs-grid {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 8px 4px 24px;
            scrollbar-width: none;
        }
        .cs-grid::-webkit-scrollbar { display: none; }
        .cs-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 26px;
            display: flex;
            flex-direction: column;
            transition: all 0.25s;
            flex: 0 0 calc(33.333% - 14px);
            min-width: 320px;
            scroll-snap-align: start;
        }
        .cs-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--amber);
            transform: translateY(-2px);
        }
        .cs-nav {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }
        .cs-nav button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border);
            color: var(--navy-deep);
            font-size: 16px;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .cs-nav button:hover {
            background: var(--amber);
            border-color: var(--amber);
            color: var(--navy-deep);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .cs-nav button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        .cs-stars {
            color: var(--amber);
            font-size: 16px;
            margin-bottom: 14px;
        }
        .cs-quote {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 18px;
            flex: 1;
        }
        .cs-author {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        .cs-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--amber) 0%, var(--amber-deep) 100%);
            color: var(--navy-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 15px;
        }
        .cs-name { font-weight: 800; font-size: 14px; color: var(--navy-deep); }
        .cs-meta { font-size: 12px; color: var(--text-muted); }
        @media (max-width: 880px) {
            .cs-card { flex: 0 0 calc(100% - 8px); min-width: 280px; }
        }

        /* ===== FAQ ===== */
        .faq {
            padding: 80px 0;
            background: var(--paper-soft);
        }
        .faq-list { max-width: 760px; margin: 32px auto 0; }
        .faq-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .faq-item.active { border-color: var(--amber); box-shadow: var(--shadow-md); }
        .faq-q {
            padding: 18px 22px;
            font-weight: 700;
            color: var(--navy-deep);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }
        .faq-q i { color: var(--amber); transition: transform 0.25s; }
        .faq-item.active .faq-q i { transform: rotate(45deg); }
        .faq-a {
            padding: 0 22px;
            max-height: 0;
            overflow: hidden;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.7;
            transition: all 0.3s ease;
        }
        .faq-item.active .faq-a {
            padding: 0 22px 18px;
            max-height: 500px;
        }
        .faq-cta {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        .faq-cta a {
            color: var(--amber-deep);
            font-weight: 700;
            text-decoration: none;
            border-bottom: 2px solid var(--amber);
        }
        .faq-cta a:hover { color: var(--navy-deep); }

        /* ===== FOOTER ===== */
        footer {
            background: var(--navy-deep);
            color: rgba(255,255,255,0.85);
            padding: 56px 0 32px;
        }
        .footer-trust-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
            padding: 18px;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius-md);
            margin-bottom: 40px;
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
        }
        .footer-trust-row i { font-size: 22px; margin-right: 4px; }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .footer-brand img { height: 40px; margin-bottom: 16px; filter: brightness(0) invert(1); }
        .footer-brand p {
            font-size: 14px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .footer-brand .footer-sub {
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            font-style: italic;
        }
        .footer-col h5 {
            font-size: 13px;
            font-weight: 800;
            color: var(--amber);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 10px; }
        .footer-col a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
            cursor: pointer;
        }
        .footer-col a:hover { color: var(--amber); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 24px;
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.5);
        }
        @media (max-width: 880px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 28px; }
            .footer-brand { grid-column: 1 / -1; }
        }
        @media (max-width: 540px) {
            .footer-grid { grid-template-columns: 1fr; }
        }

        /* ===== UVP MODAL ===== */
        .uvp-modal-ind {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .uvp-modal-ind.show { display: flex; }
        .uvp-modal-content-ind {
            background: white;
            border-radius: var(--radius-xl);
            max-width: 500px;
            width: 100%;
            padding: 32px;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
        }
        .uvp-modal-close-ind {
            position: absolute;
            top: 16px; right: 16px;
            background: var(--paper-mid);
            border: none;
            width: 32px; height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-secondary);
        }
        .uvp-modal-content-ind h3 {
            color: var(--navy-deep);
            font-size: 22px;
            margin-bottom: 16px;
            font-weight: 900;
        }
        .uvp-modal-content-ind p {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 14px;
            font-size: 15px;
        }
        .uvp-modal-content-ind .uvp-benefits {
            background: var(--amber-light);
            border-left: 4px solid var(--amber);
            padding: 16px;
            border-radius: var(--radius-sm);
            margin-top: 16px;
        }
        .uvp-modal-content-ind .uvp-benefits strong { color: var(--navy-deep); }

        /* Sticky mobile CTA */
        .sticky-cta {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            border-top: 1px solid var(--border);
            padding: 12px 16px;
            box-shadow: 0 -4px 12px rgba(15,23,42,0.08);
            z-index: 998;
        }
        .sticky-cta a {
            display: block;
            padding: 14px;
            background: var(--amber);
            color: var(--navy-deep);
            text-align: center;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-weight: 900;
            font-size: 15px;
        }
        @media (max-width: 768px) {
            .sticky-cta { display: block; }
            body { padding-bottom: 76px; }
        }

        /* ===== FLOATING WHATSAPP WIDGET ===== */
        .wa-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #25D366;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 30px;
            box-shadow: 0 8px 28px rgba(37,211,102,0.5), 0 4px 12px rgba(0,0,0,0.15);
            z-index: 999;
            transition: transform 0.25s, box-shadow 0.25s;
            animation: waPulse 2.4s infinite;
        }
        .wa-float:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 36px rgba(37,211,102,0.6), 0 6px 16px rgba(0,0,0,0.2);
            color: white;
        }
        .wa-float i {
            line-height: 1;
        }
        .wa-float-label {
            position: absolute;
            right: 72px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--navy-deep);
            color: white;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: var(--shadow-md);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .wa-float-label::after {
            content: '';
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 6px 0 6px 6px;
            border-color: transparent transparent transparent var(--navy-deep);
        }
        .wa-float:hover .wa-float-label { opacity: 1; }
        @keyframes waPulse {
            0%, 100% { box-shadow: 0 8px 28px rgba(37,211,102,0.5), 0 4px 12px rgba(0,0,0,0.15), 0 0 0 0 rgba(37,211,102,0.5); }
            50% { box-shadow: 0 8px 28px rgba(37,211,102,0.5), 0 4px 12px rgba(0,0,0,0.15), 0 0 0 14px rgba(37,211,102,0); }
        }
        @media (max-width: 768px) {
            .wa-float {
                bottom: 84px;
                right: 16px;
                width: 54px;
                height: 54px;
                font-size: 26px;
            }
            .wa-float-label { display: none; }
        }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<header>
    <div class="header-inner">
        <a href="https://smart-buzzer.com/" class="logo-wrap">
            <span class="logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer"></span>
            <span class="logo-tag"><strong>Smart Buzzer</strong>Trusted by 2,000+ SMBs</span>
        </a>
        <nav>
            <ul>
                <li><a href="#industries">Industries</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#order-form" class="nav-cta">Get My Reviews</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-text-col">
                <span class="hero-eyebrow"><span class="stars-mini">★★★★★</span> Trusted by 2,000+ SMBs</span>
                <h1>From "Who?" to <span class="accent">Top 3 on Google</span></h1>
                <p class="lead">Real Google reviews — built for <strong>HVAC, dentists, restaurants, contractors</strong> &amp; 12+ more verticals.</p>
                <ul class="hero-bullets">
                    <li><i class="fa-solid fa-check"></i><span><strong>100% real local reviewers</strong> — no bots, no recycled accounts</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Industry-aware copy</strong> — HVAC mentions furnaces, dental mentions cleanings</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Free replacements</strong> — order isn't done until it's complete</span></li>
                </ul>
                <div class="hero-ctas">
                    <a href="#industries" class="btn btn-primary">See My Industry First <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="hero-microtrust">
                    <span><span class="live-dot"></span>47 reviews posted in the last 24h</span>
                </div>
            </div>
            <!-- Hero photo -->
            <div class="hero-photo-wrap">
                <img src="https://smart-buzzer.com/photos/2.png" alt="Stand out your business with Google reviews" class="hero-photo">
            </div>
        </div>
    </div>
</section>

<!-- ===== TRUST BAR ===== -->
<div class="trust-bar">
    <div class="container">
        <div class="trust-bar-inner">
            <span><i class="fa-solid fa-circle-check"></i> 100% Local Reviewers</span>
            <span><i class="fa-solid fa-circle-check"></i> Industry-Tailored Content</span>
            <span class="trust-live"><span class="live-dot-sm"></span> LIVE: 47 posted today</span>
            <span><i class="fa-solid fa-circle-check"></i> 7-Day Replacement Guarantee</span>
        </div>
    </div>
</div>

<!-- ===== INDUSTRY SELECTOR ===== -->
<section class="industry-selector" id="industries">
    <div class="container">
        <div class="section-header">
            <h2>Pick <span class="accent">Your Industry</span></h2>
            <p>We craft reviews that match how real customers talk in your line of work — see a sample below.</p>
        </div>

        <div class="industry-pills" id="industryPills">
            <button class="industry-pill active" data-industry="construction" onclick="selectIndustry('construction')"><i class="fa-solid fa-helmet-safety"></i> Construction</button>
            <button class="industry-pill" data-industry="restaurant" onclick="selectIndustry('restaurant')"><i class="fa-solid fa-utensils"></i> Restaurant</button>
            <button class="industry-pill" data-industry="roofing" onclick="selectIndustry('roofing')"><i class="fa-solid fa-house-chimney"></i> Roofing</button>
            <button class="industry-pill" data-industry="auto_repair" onclick="selectIndustry('auto_repair')"><i class="fa-solid fa-car-burst"></i> Auto Repair</button>
            <button class="industry-pill" data-industry="plumbing" onclick="selectIndustry('plumbing')"><i class="fa-solid fa-faucet-drip"></i> Plumbing</button>
            <button class="industry-pill" data-industry="hvac" onclick="selectIndustry('hvac')"><i class="fa-solid fa-temperature-half"></i> HVAC</button>
            <button class="industry-pill" data-industry="dental" onclick="selectIndustry('dental')"><i class="fa-solid fa-tooth"></i> Dental</button>
            <button class="industry-pill" data-industry="accounting" onclick="selectIndustry('accounting')"><i class="fa-solid fa-calculator"></i> Accounting</button>
            <button class="industry-pill" data-industry="healthcare" onclick="selectIndustry('healthcare')"><i class="fa-solid fa-stethoscope"></i> Healthcare</button>
            <div class="industry-more-wrap">
                <button class="industry-more-btn" onclick="toggleMoreIndustries(event)">More 7+ <i class="fa-solid fa-chevron-down" style="font-size:11px;margin-left:4px;"></i></button>
                <div class="industry-more-list" id="industryMoreList">
                    <button onclick="selectIndustry('auto_dealer')"><i class="fa-solid fa-car"></i> Auto Dealer</button>
                    <button onclick="selectIndustry('lawyer')"><i class="fa-solid fa-scale-balanced"></i> Lawyer</button>
                    <button onclick="selectIndustry('realtor')"><i class="fa-solid fa-house"></i> Realtor</button>
                    <button onclick="selectIndustry('salon')"><i class="fa-solid fa-scissors"></i> Salon / Spa</button>
                    <button onclick="selectIndustry('locksmith')"><i class="fa-solid fa-key"></i> Locksmith</button>
                    <button onclick="selectIndustry('landscaping')"><i class="fa-solid fa-tree"></i> Landscaping</button>
                    <button onclick="selectIndustry('other')"><i class="fa-solid fa-store"></i> Other</button>
                </div>
            </div>
        </div>

        <div class="industry-content" id="industryContent">
            <!-- Content populated by JS -->
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS — TIMELINE ===== -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">⏱ Your Timeline</span>
            <h2>From Order to <span class="accent">Top 3 on Google</span></h2>
            <p>Here's exactly what happens — day-by-day, no guesswork.</p>
        </div>
        <div class="timeline-wrap">
            <div class="timeline-track"></div>
            <div class="timeline-grid">
                <div class="timeline-step">
                    <div class="timeline-dot">1</div>
                    <h4>Order &amp; Brief</h4>
                    <p>Pick your package, fill the form. WhatsApp onboarding within 24h to gather your Google profile + service brief.</p>
                </div>
                <div class="timeline-step">
                    <div class="timeline-dot">2</div>
                    <h4>First Reviews Live</h4>
                    <p>Industry-tailored reviews start appearing on your profile — posted by real local accounts at a natural pace.</p>
                </div>
                <div class="timeline-step">
                    <div class="timeline-dot">3</div>
                    <h4>Goal Hit</h4>
                    <p>Full delivery + detailed report. Drops auto-replaced free until your count is complete.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== COMPARISON TABLE ===== -->
<section class="comparison">
    <div class="container">
        <div class="section-header">
            <h2>Smart Buzzer vs <span class="accent">The Alternatives</span></h2>
            <p>Why $360 with us beats $50 on Fiverr — same delivery promise, real accounts, lasting results.</p>
        </div>
        <div class="compare-table-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th></th>
                        <th class="compare-us">Smart Buzzer</th>
                        <th>Cheap Providers<br><span style="font-weight:500;font-size:11px;color:var(--text-muted);">($50-200)</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Real local accounts</td>
                        <td class="us"><span class="compare-yes"><i class="fa-solid fa-circle-check"></i></span></td>
                        <td><span class="compare-no"><i class="fa-solid fa-circle-xmark"></i></span> Bot/farmed</td>
                    </tr>
                    <tr>
                        <td>Industry-specific copy</td>
                        <td class="us"><span class="compare-yes"><i class="fa-solid fa-circle-check"></i></span></td>
                        <td><span class="compare-no"><i class="fa-solid fa-circle-xmark"></i></span> Templates</td>
                    </tr>
                    <tr>
                        <td>Stick rate</td>
                        <td class="us">93%</td>
                        <td>20-40%</td>
                    </tr>
                    <tr>
                        <td>Account safety</td>
                        <td class="us"><span class="compare-yes"><i class="fa-solid fa-circle-check"></i></span> Designed safe</td>
                        <td><span class="compare-no"><i class="fa-solid fa-circle-xmark"></i></span> Suspension risk</td>
                    </tr>
                    <tr>
                        <td>Speed to top 3</td>
                        <td class="us">2-3 months</td>
                        <td><span class="compare-meh">1 week (then drops)</span></td>
                    </tr>
                    <tr>
                        <td>Free replacements</td>
                        <td class="us"><span class="compare-yes"><i class="fa-solid fa-circle-check"></i></span></td>
                        <td><span class="compare-no"><i class="fa-solid fa-circle-xmark"></i></span></td>
                    </tr>
                    <tr>
                        <td>Delivery report</td>
                        <td class="us"><span class="compare-yes"><i class="fa-solid fa-circle-check"></i></span> Detailed</td>
                        <td><span class="compare-no"><i class="fa-solid fa-circle-xmark"></i></span></td>
                    </tr>
                    <tr>
                        <td>True cost</td>
                        <td class="us"><strong>$360-660</strong></td>
                        <td>$50-200 + lost account</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ===== PRICING ===== -->
<section class="pricing" id="pricing">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">💰 Pick Your Package</span>
            <h2>Pick Your Plan. Get on <span class="accent">Top 3 of Google.</span></h2>
            <p>Every plan: real local reviewers · industry-tailored copy · free replacements until your full count is delivered.</p>
        </div>
        <div class="pricing-grid">
            <!-- STARTER -->
            <div class="price-card">
                <div class="price-name">Starter</div>
                <div class="price-amount"><span class="price-old">$450</span>$360</div>
                <div class="price-reviews"><strong>55 Local Reviews</strong> · Save $90</div>
                <span class="price-perreview">$6.55/review</span>
                <ul class="price-features">
                    <li><i class="fa-solid fa-check feat-icon"></i> (20%) 4-Star + (80%) 5-Star Ratings <i class="info-icon" onclick="openUvpModalInd('ratingsModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Local Names <i class="info-icon" onclick="openUvpModalInd('namesModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Human-Written Custom Content</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Detailed Delivery Report</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> For 2 Business Links</li>
                </ul>
                <a href="#order-form" class="price-cta" data-package="starter" onclick="preSelectPkg('starter')">ORDER NOW</a>
                <div class="price-scarcity">🔥 Only 5 left at this price</div>
            </div>
            <!-- GROWTH -->
            <div class="price-card featured">
                <div class="price-badge">★ MOST POPULAR</div>
                <div class="price-name">Growth</div>
                <div class="price-amount"><span class="price-old">$700</span>$550</div>
                <div class="price-reviews"><strong>88 Local Reviews</strong> · Save $150</div>
                <span class="price-perreview">$6.25/review · Best value</span>
                <ul class="price-features">
                    <li><i class="fa-solid fa-check feat-icon"></i> (20%) 4-Star + (80%) 5-Star Ratings <i class="info-icon" onclick="openUvpModalInd('ratingsModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Local Names <i class="info-icon" onclick="openUvpModalInd('namesModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Human-Written Custom Content</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Detailed Delivery Report</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> For 3 Business Links</li>
                </ul>
                <a href="#order-form" class="price-cta" data-package="growth" onclick="preSelectPkg('growth')">ORDER NOW - Save $150</a>
                <div class="price-scarcity">🔥 Only 3 left at this price</div>
            </div>
            <!-- PERFORMANCE -->
            <div class="price-card">
                <div class="price-name">Performance</div>
                <div class="price-amount"><span class="price-old">$880</span>$660</div>
                <div class="price-reviews"><strong>110 Local Reviews</strong> · Save $220</div>
                <span class="price-perreview">$6.00/review · Lowest per-review</span>
                <ul class="price-features">
                    <li><i class="fa-solid fa-check feat-icon"></i> (20%) 4-Star + (80%) 5-Star Ratings <i class="info-icon" onclick="openUvpModalInd('ratingsModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Local Names <i class="info-icon" onclick="openUvpModalInd('namesModalInd')">i</i></li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Human-Written Custom Content</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> Detailed Delivery Report</li>
                    <li><i class="fa-solid fa-check feat-icon"></i> For 4 Business Links</li>
                </ul>
                <a href="#order-form" class="price-cta" data-package="performance" onclick="preSelectPkg('performance')">ORDER NOW - Save $220</a>
                <div class="price-scarcity">🔥 Only 7 left at this price</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== ORDER FORM (split) ===== -->
<section class="order-form-section" id="order-form">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">📝 Place Your Order</span>
            <h2>Start Your <span class="accent">Campaign</span></h2>
            <p>Complete your details — we open WhatsApp onboarding within 24 hours.</p>
        </div>
        <div class="order-form-grid">
            <div class="order-form-wrapper">
                <div class="order-form-countdown">
                    <i class="fa-solid fa-clock"></i> Today's pricing expires in: <span id="ofH">00</span>:<span id="ofM">00</span>:<span id="ofS">00</span>
                </div>
                <div class="of-group">
                    <label class="of-label">Google Business Name + Location <span class="of-req">*</span></label>
                    <input type="text" class="of-input" id="ofBizName" placeholder="Example: Joe's HVAC Service in Dallas" required>
                </div>
                <div class="of-group">
                    <label class="of-label">Industry</label>
                    <select class="of-input" id="ofIndustry">
                        <option value="">Select your industry</option>
                        <option value="construction">Construction</option>
                        <option value="restaurant">Restaurant / Café</option>
                        <option value="roofing">Roofing</option>
                        <option value="auto_repair">Auto Repair</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="hvac">HVAC</option>
                        <option value="dental">Dental</option>
                        <option value="accounting">Accounting</option>
                        <option value="healthcare">Healthcare / Clinic</option>
                        <option value="auto_dealer">Auto Dealer</option>
                        <option value="lawyer">Lawyer</option>
                        <option value="realtor">Realtor</option>
                        <option value="salon">Salon / Spa</option>
                        <option value="locksmith">Locksmith</option>
                        <option value="landscaping">Landscaping</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="of-group">
                    <label class="of-label">WhatsApp Number (for order updates) <span class="of-req">*</span></label>
                    <input type="tel" class="of-input" id="ofWhatsapp" placeholder="+1 Enter your WhatsApp number" required>
                </div>
                <div class="of-group">
                    <label class="of-label">Email Address</label>
                    <input type="email" class="of-input" id="ofEmail" placeholder="Enter your email address (optional)">
                </div>

                <span class="of-package-label">Select Your Package:</span>
                <div class="of-packages">
                    <label class="of-pkg" data-pkg="starter" onclick="selectPkg(this)">
                        <input type="radio" name="package" value="starter" class="of-pkg-radio">
                        <div class="of-pkg-info">
                            <span class="of-pkg-name">Starter — 55 Reviews</span>
                            <span class="of-pkg-detail"><s>$450</s> · Save $90</span>
                        </div>
                        <span class="of-pkg-price">$360</span>
                    </label>
                    <label class="of-pkg selected" data-pkg="growth" onclick="selectPkg(this)">
                        <input type="radio" name="package" value="growth" class="of-pkg-radio" checked>
                        <div class="of-pkg-info">
                            <span class="of-pkg-name">Growth — 88 Reviews <span class="of-pkg-popular">POPULAR</span></span>
                            <span class="of-pkg-detail"><s>$700</s> · Save $150</span>
                        </div>
                        <span class="of-pkg-price">$550</span>
                    </label>
                    <label class="of-pkg" data-pkg="performance" onclick="selectPkg(this)">
                        <input type="radio" name="package" value="performance" class="of-pkg-radio">
                        <div class="of-pkg-info">
                            <span class="of-pkg-name">Performance — 110 Reviews</span>
                            <span class="of-pkg-detail"><s>$880</s> · Save $220</span>
                        </div>
                        <span class="of-pkg-price">$660</span>
                    </label>
                </div>

                <button class="of-submit" onclick="submitOrder()">COMPLETE ORDER →</button>

                <div class="of-trust">
                    <span><i class="fa-solid fa-lock" style="color: var(--emerald);"></i> Secure Checkout</span>
                    <span><i class="fa-solid fa-shield-halved" style="color: var(--amber);"></i> SSL Protected</span>
                    <span><i class="fa-solid fa-circle-check" style="color: var(--emerald);"></i> Money-Back Guarantee</span>
                </div>
            </div>

            <!-- What happens next sidebar -->
            <div class="next-side">
                <h3>What Happens Next</h3>
                <h4>From submit to first live review in 5 days</h4>
                <div class="next-step">
                    <div class="next-num">1</div>
                    <div>
                        <div class="next-text-h">Pay Securely</div>
                        <div class="next-text-p">Fanbasis checkout. Visa, MC, Apple Pay, Google Pay. SSL-encrypted, 256-bit.</div>
                    </div>
                </div>
                <div class="next-step">
                    <div class="next-num">2</div>
                    <div>
                        <div class="next-text-h">Onboarding Within 24h</div>
                        <div class="next-text-p">We open WhatsApp to gather your Google profile + service brief. Quick 5-min chat.</div>
                    </div>
                </div>
                <div class="next-step">
                    <div class="next-num">3</div>
                    <div>
                        <div class="next-text-h">Delivery + Live Reviews</div>
                        <div class="next-text-p">First reviews go live Day 3-5. Full delivery with detailed report. Replacements until your full count is delivered.</div>
                    </div>
                </div>
                <div class="next-guarantee">
                    <i class="fa-solid fa-shield-halved"></i> <strong>Our promise:</strong> Reviews that don't stick don't count. We replace until your full order is complete — or refund the difference.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CASE STUDY ===== -->
<section class="case-study">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow" style="color: var(--amber);">📊 Real Results</span>
            <h2>Case Study: <span class="accent">Tilton Equipment Rentals</span></h2>
            <p>How a Dallas equipment rental business went from a 2.5★ rating to a perfect 5.0★ in 14 weeks.</p>
        </div>
        <div class="case-grid">
            <div class="case-narrative">
                <span class="case-meta"><i class="fa-solid fa-helmet-safety"></i> Construction Machine Rentals · Dallas, TX</span>
                <h3>2.5★ → 5.0★ in 14 weeks</h3>
                <p>Tilton's Dallas equipment rental shop was stuck at 2.5★. Old bad reviews killed the phone.</p>
                <p>We delivered 105 industry-tailored reviews from real local accounts. Now it's a 5.0★ profile — and the phone's ringing again.</p>
                <div class="case-numbers">
                    <div class="case-number">
                        <div class="case-number-from">Before</div>
                        <div class="case-number-from-val">2.5 ★ rating</div>
                        <div class="case-number-to">After 14 weeks</div>
                        <div class="case-number-to-val">5.0 ★</div>
                        <div class="case-number-label">Average star rating</div>
                    </div>
                </div>
                <a href="#order-form" class="btn btn-primary">START MY CAMPAIGN <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="case-visual">
                <img src="https://smart-buzzer.com/photos/ba.jpg" alt="Before / After Google Maps profile" style="width:100%; height:auto; display:block; border-radius:var(--radius-md);">
            </div>
        </div>
    </div>
</section>

<!-- ===== REAL LOCAL REVIEWS ===== -->
<section class="real-reviews" id="reviews">
    <div class="container">
        <div class="section-header">
            <h2>Real <span class="accent">Local Reviews</span></h2>
        </div>
        <div class="single-image-wrap">
            <img src="https://smart-buzzer.com/wp-content/uploads/2025/04/slide-3.jpg" alt="Real Local Reviews Example">
        </div>
    </div>
</section>

<!-- ===== CHOOSE YOUR OWN SENTENCES ===== -->
<section class="real-reviews">
    <div class="container">
        <div class="section-header">
            <h2>Choose Your <span class="accent">Own Sentences</span></h2>
        </div>
        <div class="rr-grid">
            <div class="rr-image-only">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review Sentences Variety">
            </div>
            <div class="rr-features">
                <p class="rr-features-lead">You choose the content, or let us create comprehensive variety for you. For every 55 reviews ordered, we prepare <strong>440+ unique sentences</strong> to ensure no repetition.</p>
                <ul class="rr-features-list">
                    <li><i class="fa-solid fa-circle-check"></i> Human-written content tailored to your business</li>
                    <li><i class="fa-solid fa-circle-check"></i> Preview &amp; approve all content before posting</li>
                    <li><i class="fa-solid fa-circle-check"></i> Up to 2 revision rounds included</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== WHY PEOPLE USE OUR SERVICES ===== -->
<section class="why-services">
    <div class="container">
        <div class="section-header">
            <h2>Why People Use <span class="accent">Our Services</span></h2>
        </div>
        <div class="rr-grid">
            <div class="rr-image-only">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.36.44.png" alt="Real Local Review Example">
            </div>
            <div class="rr-features">
                <ul class="rr-features-list">
                    <li><i class="fa-solid fa-circle-check"></i><div><strong>Local Names</strong><span>Reviewer names match your geographic area for maximum credibility</span></div></li>
                    <li><i class="fa-solid fa-circle-check"></i><div><strong>Unique users, IPs, devices, and aged accounts</strong><span>Complete technical authenticity guaranteed</span></div></li>
                    <li><i class="fa-solid fa-circle-check"></i><div><strong>Tailored reviews for your business</strong><span>Custom content that matches your services</span></div></li>
                    <li><i class="fa-solid fa-circle-check"></i><div><strong>Gradual posting (5-10 daily, ~3 stick)</strong><span>Natural pacing prevents algorithm detection</span></div></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== TRACK YOUR ORDER ===== -->
<section class="track-order">
    <div class="container">
        <div class="section-header">
            <h2>Track Your Order <span class="accent">Every Day</span></h2>
            <p>100% transparency with real-time campaign dashboard.</p>
        </div>
        <div class="single-image-wrap">
            <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Progress Dashboard">
        </div>
    </div>
</section>

<!-- ===== TRUST STRIP: 2,000+ BUSINESSES ===== -->
<section class="trust-strip-big">
    <div class="container">
        <div class="ts-inner">
            <div class="ts-num">2,000+</div>
            <div class="ts-text">
                <h3>Serving Over 2,000+ Businesses Across the USA</h3>
                <p>From solo HVAC techs to multi-location restaurants — every vertical, every state.</p>
            </div>
        </div>
        <div class="ts-image-wrap">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="2,000+ businesses served">
        </div>
    </div>
</section>

<!-- ===== WHAT OUR CLIENTS SAY ===== -->
<section class="clients-say">
    <div class="container">
        <div class="section-header">
            <h2>What Our <span class="accent">Clients Say</span></h2>
            <p>Trusted by 2,000+ businesses across the USA.</p>
        </div>
        <div class="cs-carousel-wrap">
        <div class="cs-grid" id="csGrid">
            <div class="cs-card">
                <div class="cs-stars">★★★★★</div>
                <p class="cs-quote">"Went from 2.3 to 4.6 stars in about 5 weeks. The reviews look completely natural and my phone has been ringing nonstop since then. Best investment for my roofing business."</p>
                <div class="cs-author">
                    <div class="cs-photo" style="background-image: url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&amp;h=80&amp;fit=crop&amp;crop=face');"></div>
                    <div>
                        <div class="cs-name">Mike R.</div>
                        <div class="cs-meta">Roofing Company · Texas</div>
                    </div>
                </div>
            </div>
            <div class="cs-card">
                <div class="cs-stars">★★★★★</div>
                <p class="cs-quote">"I was skeptical at first but the results speak for themselves. 88 reviews delivered exactly as promised. The tracking dashboard is a nice touch too. Will order again for my other location."</p>
                <div class="cs-author">
                    <div class="cs-photo" style="background-image: url('https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&amp;h=80&amp;fit=crop&amp;crop=face');"></div>
                    <div>
                        <div class="cs-name">Sarah L.</div>
                        <div class="cs-meta">Dental Clinic · California</div>
                    </div>
                </div>
            </div>
            <div class="cs-card">
                <div class="cs-stars">★★★★★</div>
                <p class="cs-quote">"As a digital agency, I resell Smart Buzzer to my clients. The quality is consistent and every order has delivered as promised. Their team is responsive and professional."</p>
                <div class="cs-author">
                    <div class="cs-photo" style="background-image: url('https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&amp;h=80&amp;fit=crop&amp;crop=face');"></div>
                    <div>
                        <div class="cs-name">James W.</div>
                        <div class="cs-meta">Marketing Agency · Florida</div>
                    </div>
                </div>
            </div>
            <div class="cs-card">
                <div class="cs-stars">★★★★★</div>
                <p class="cs-quote">"My locksmith business jumped from page 3 to the top of Google Maps within 2 months. The local reviewer names really made the difference. Account stayed clean throughout."</p>
                <div class="cs-author">
                    <div class="cs-photo" style="background-image: url('https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&amp;h=80&amp;fit=crop&amp;crop=face');"></div>
                    <div>
                        <div class="cs-name">David K.</div>
                        <div class="cs-meta">Locksmith · New York</div>
                    </div>
                </div>
            </div>
            <div class="cs-card">
                <div class="cs-stars">★★★★★</div>
                <p class="cs-quote">"Ordered the Performance package for 3 of my restaurant locations. All delivered on time with unique content for each. My Google Maps ranking improved significantly."</p>
                <div class="cs-author">
                    <div class="cs-photo" style="background-image: url('https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&amp;h=80&amp;fit=crop&amp;crop=face');"></div>
                    <div>
                        <div class="cs-name">Anna P.</div>
                        <div class="cs-meta">Restaurant Owner · Illinois</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cs-nav">
            <button onclick="scrollTesti(-1)" aria-label="Previous testimonials"><i class="fa-solid fa-arrow-left"></i></button>
            <button onclick="scrollTesti(1)" aria-label="Next testimonials"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        </div>
    </div>
</section>

<!-- ===== OUR CLIENTS (LOGO GRID) ===== -->
<section class="clients-section">
    <div class="container">
        <div class="section-header">
            <h2>Our <span class="accent">Clients</span></h2>
        </div>
        <div class="ic-clients-grid">
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client Logo"></div>
            <div class="ic-client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client Logo"></div>
        </div>
    </div>
</section>

<!-- ===== FAQ ===== -->
<section class="faq" id="faq">
    <div class="container-narrow">
        <div class="section-header">
            <h2>Real <span class="accent">Answers</span></h2>
        </div>
        <div class="faq-list">
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Will Google penalize my account? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">No. Our gradual pace (1-3 reviews/week) is specifically designed to look like organic customer behavior. We use unique users, IPs, devices, and aged Google accounts. Across 1,200+ campaigns we've optimized for long-term safety — not bulk dumps that trigger filters.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Does this work for my industry? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Yes. We've delivered for HVAC, dental, restaurants, construction, roofing, plumbing, healthcare, accounting, auto repair/dealer, salons, lawyers, realtors, locksmiths, landscaping, and more — 16+ verticals across US/CA/AU. Reviews are written specifically for the language and pain points of your industry, not generic templates.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">How long until reviews start showing? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">First reviews typically appear within 3-5 days of campaign launch. We submit 5-10 reviews daily and around 3 stick per day. Full delivery for 55 reviews takes about 2-3 months. Larger packages take longer because the gradual pace keeps your account safe.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">What if I don't see results? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">We guarantee delivery of every review you ordered (only sticking reviews count). If reviews drop or come in wrong, we replace them free until your full count is delivered. We don't offer ranking guarantees because Google's algorithm has many factors — but our 1,200+ campaigns show that ranking improves dramatically once your review profile catches up to local competitors.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">What if reviews drop? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Some drops are normal — Google occasionally rolls out algorithm updates ("waves") that affect a few reviews. We automatically replace any dropped reviews until your full order is complete. We only count reviews that stick.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Can I provide input on the reviews? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Yes. You can share talking points, services to highlight, or specific themes. We can't let you choose reviewer names (those follow local naming conventions for authenticity), but content can be tailored to your business specifics.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">How does payment work? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">One-time payment via Fanbasis checkout (Visa, Mastercard, Amex, Apple Pay, Google Pay). Once paid, we open WhatsApp onboarding within 24 hours. Once a campaign starts it can't be cancelled or disputed — but it doesn't end until the full count is delivered.</div>
            </div>
        </div>
        <div class="faq-cta">
            Still have questions? <a href="https://wa.me/85777311657">Message us on WhatsApp →</a>
        </div>
        <div style="text-align: center; margin-top: 32px;">
            <a href="#pricing" class="btn btn-primary">ORDER NOW <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="container">
        <div class="footer-trust-row">
            <span><i class="fa-brands fa-cc-visa" style="color:#fff;"></i> Visa</span>
            <span><i class="fa-brands fa-cc-mastercard" style="color:#fff;"></i> Mastercard</span>
            <span><i class="fa-brands fa-cc-apple-pay" style="color:#fff;"></i> Apple Pay</span>
            <span><i class="fa-brands fa-google-pay" style="color:#fff;"></i> Google Pay</span>
            <span><i class="fa-solid fa-lock" style="color:var(--emerald);"></i> SSL Secured</span>
            <span><i class="fa-solid fa-shield-halved" style="color:var(--amber);"></i> 7-Day Guarantee</span>
        </div>
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                <p>Specialized in social media engagement, product reviews, and online reputation services for SMBs across 16+ industries.</p>
                <p class="footer-sub">A subsidiary of Pintarnya.</p>
            </div>
            <div class="footer-col">
                <h5>Industries</h5>
                <ul>
                    <li><a onclick="selectIndustry('construction', true)">Construction</a></li>
                    <li><a onclick="selectIndustry('restaurant', true)">Restaurant</a></li>
                    <li><a onclick="selectIndustry('hvac', true)">HVAC</a></li>
                    <li><a onclick="selectIndustry('roofing', true)">Roofing</a></li>
                    <li><a onclick="selectIndustry('plumbing', true)">Plumbing</a></li>
                    <li><a onclick="selectIndustry('dental', true)">Dental</a></li>
                    <li><a onclick="selectIndustry('healthcare', true)">Healthcare</a></li>
                    <li><a onclick="selectIndustry('accounting', true)">Accounting</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="https://smart-buzzer.com/tracker">Track Campaign</a></li>
                    <li><a href="https://smart-buzzer.com/report">Report Issue</a></li>
                    <li><a href="https://smart-buzzer.com/service-tnc">Terms &amp; Conditions</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Contact</h5>
                <ul>
                    <li>📞 WhatsApp: <a href="https://wa.me/6285773116557?text=Hi%20Smart%20Buzzer%2C%20I%20want%20to%20order%20Google%20Reviews.%20I%20am%20from%20the%20Promo%20Industry%20page.">+62857-7311-6557</a></li>
                    <li>📧 Email: <a href="mailto:contact@smart-buzzer.com">contact@smart-buzzer.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 Smart Buzzer. All rights reserved. · <a href="https://smart-buzzer.com/" style="color: rgba(255,255,255,0.6); text-decoration: none;">smart-buzzer.com</a>
        </div>
    </div>
</footer>

<!-- ===== STICKY MOBILE CTA ===== -->
<div class="sticky-cta">
    <a href="#order-form">GET MY REVIEWS — START NOW →</a>
</div>

<!-- ===== FLOATING WHATSAPP WIDGET ===== -->
<a href="https://wa.me/85777311657?text=Hi%20Smart%20Buzzer%20team%2C%0A%0AI%20am%20interested%20in%20your%20Google%20Reviews%20service.%0A%0ACould%20you%20share%3A%0A-%20Package%20details%20and%20pricing%0A-%20Delivery%20timeline%0A-%20Industries%20you%20support%0A%0AThank%20you." class="wa-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
    <span class="wa-float-label">Chat with us</span>
</a>

<!-- ===== UVP MODALS ===== -->
<div class="uvp-modal-ind" id="ratingsModalInd" onclick="if(event.target===this)closeUvpModalInd('ratingsModalInd')">
    <div class="uvp-modal-content-ind">
        <button class="uvp-modal-close-ind" onclick="closeUvpModalInd('ratingsModalInd')"><i class="fa-solid fa-xmark"></i></button>
        <h3>Why (20%) 4-Star + (80%) 5-Star?</h3>
        <p>A 100% 5-star profile is one of the strongest signals Google uses to flag fake reviews. Real businesses always have a small percentage of less-than-perfect reviews — that's how customer feedback actually looks.</p>
        <p>Our 80/20 mix mimics natural customer behavior. The result: your reviews stay sticky, your account stays safe, and your overall rating still hovers around 4.8 stars.</p>
        <div class="uvp-benefits"><strong>Why this matters:</strong> More sticky · More permanent · Lower drop rate</div>
    </div>
</div>

<div class="uvp-modal-ind" id="namesModalInd" onclick="if(event.target===this)closeUvpModalInd('namesModalInd')">
    <div class="uvp-modal-content-ind">
        <button class="uvp-modal-close-ind" onclick="closeUvpModalInd('namesModalInd')"><i class="fa-solid fa-xmark"></i></button>
        <h3>Why (70%) Local + (30%) Global Names?</h3>
        <p>Real local businesses get a mix of regulars and travelers. A 100% local-name profile actually looks suspicious — Google knows real customer bases include some out-of-area names too.</p>
        <p>Our 70/30 split mirrors natural reviewer demographics for SMBs in the US, Canada, and Australia.</p>
        <div class="uvp-benefits"><strong>Why this matters:</strong> More genuine · More natural · Higher trust signal</div>
    </div>
</div>

<script>
// ===== INDUSTRY DATA (with sample reviews + customer photos) =====
var industryData = {
    'construction': {
        icon: 'fa-helmet-safety',
        name: 'Construction',
        subtitle: 'For general contractors, builders, remodelers, and trades. Reviews highlight craftsmanship, timeliness, cleanup, and budget transparency.',
        stats: [
            {num: '+47%', label: 'Avg inbound calls after campaign'},
            {num: '#7 → #2', label: 'Local pack jump (avg, 11 wks)'},
            {num: '24+', label: 'Construction campaigns delivered'}
        ],
        painpoints: [
            'Bidding wars where one bad review tanks the deal',
            'Local competitors with 50+ reviews look more credible',
            'Hard to ask busy clients for reviews after project ends',
            'Word-of-mouth drying up after one rough job'
        ],
        sampleStars: 5,
        sampleReview: '"Mike\'s crew remodeled our kitchen on time and under budget. Caught a load-bearing issue the previous contractor missed. Cleanup was perfect — would hire again in a heartbeat."',
        sampleAuthor: 'Jennifer K. · Dallas resident',
        sampleAvatar: 'JK',
        testQuote: '"Got 88 reviews over 3 months. Zero drops. Phone has been ringing more since week 3 — finally beating the big franchise in our area."',
        testAuthor: 'Mike R., General Contractor · Dallas, TX',
        testPhoto: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&q=80'
    },
    'restaurant': {
        icon: 'fa-utensils',
        name: 'Restaurant / Café',
        subtitle: 'For independent restaurants, cafés, food trucks, and family eateries. Reviews mention specific dishes, ambiance, service speed, and the small details only real diners notice.',
        stats: [
            {num: '+22%', label: 'Avg foot traffic lift'},
            {num: 'Top 3', label: 'Local pack rank reached'},
            {num: '13+', label: 'Restaurant campaigns delivered'}
        ],
        painpoints: [
            'Slow weeknights despite the food being excellent',
            'Yelp/Google ranking buried under chains',
            'One angry review pulling the average below 4 stars',
            'Hard to compete with restaurants that bought reviews years ago'
        ],
        sampleStars: 5,
        sampleReview: '"The truffle gnocchi was incredible — best Italian in Brooklyn hands down. Got the corner booth on the patio, perfect summer night. Service was warm and attentive without hovering."',
        sampleAuthor: 'Marco D. · Brooklyn local',
        sampleAvatar: 'MD',
        testQuote: '"Reviews actually mention our menu and patio. Foot traffic up ~22% since we ranked top 3 in our area."',
        testAuthor: 'Anita P., Restaurant Owner · Brooklyn, NY',
        testPhoto: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&q=80'
    },
    'roofing': {
        icon: 'fa-house-chimney',
        name: 'Roofing',
        subtitle: 'For roofing contractors handling residential and commercial work. Reviews address cleanup, warranty, insurance claims, and storm-damage response.',
        stats: [
            {num: '2x', label: 'Avg jobs after storm season'},
            {num: '4.2 → 4.8', label: 'Rating lift (avg)'},
            {num: '9+', label: 'Roofing campaigns delivered'}
        ],
        painpoints: [
            'Storm-chasers undercutting on price with fake reviews',
            'Insurance adjusters checking your Google rating before approval',
            'Hard to convert quotes when ranking #4+',
            'High-ticket service means every lead matters'
        ],
        sampleStars: 5,
        sampleReview: '"After the hailstorm last month, James and his crew were on my roof within 48 hours. Worked directly with my insurance adjuster — no back-and-forth on my end. Clean, professional, fair price."',
        sampleAuthor: 'Robert M. · Tampa homeowner',
        sampleAvatar: 'RM',
        testQuote: '"After Hurricane Helene we doubled our jobs. Insurance adjusters now find us first when homeowners ask for recommendations."',
        testAuthor: 'James T., Roofing Contractor · Tampa, FL',
        testPhoto: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&q=80'
    },
    'auto_repair': {
        icon: 'fa-car-burst',
        name: 'Auto Repair',
        subtitle: 'For independent auto shops, mechanics, and specialty repair. Reviews focus on honesty, fair pricing, and turnaround time.',
        stats: [
            {num: '+35%', label: 'Avg first-time customers'},
            {num: 'Top 3', label: 'Outranked franchise chains'},
            {num: '8+', label: 'Auto Repair campaigns delivered'}
        ],
        painpoints: [
            'Big chains dominating the first 3 results',
            'Distrust from new customers who default to dealer',
            'Hard to convert one-time visits into regulars',
            'Reputation damaged by one unfair complaint'
        ],
        sampleStars: 5,
        sampleReview: '"Brought my truck in for a diagnostic the dealer wanted $2,400 to fix. Rico\'s team found it was just a faulty sensor — out the door for $180. Will not go anywhere else for repairs."',
        sampleAuthor: 'David S. · Phoenix driver',
        sampleAvatar: 'DS',
        testQuote: '"Independent shop in a sea of franchise chains. After our campaign we get more first-time customers calling — they tell us they read the reviews first."',
        testAuthor: 'Rico M., Auto Repair Owner · Phoenix, AZ',
        testPhoto: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=120&q=80'
    },
    'plumbing': {
        icon: 'fa-faucet-drip',
        name: 'Plumbing',
        subtitle: 'For plumbers, drain specialists, and emergency-service plumbing companies. Reviews mention response time, cleanliness, fair pricing, and professionalism.',
        stats: [
            {num: '+30%', label: 'Avg after-hours emergency calls'},
            {num: 'Top 3', label: 'Local pack rank reached'},
            {num: '8+', label: 'Plumbing campaigns delivered'}
        ],
        painpoints: [
            'Emergency calls go to whoever ranks #1 on Google',
            'Customers comparison-shop reviews before calling',
            'Hard to compete with national brands on visibility',
            'Negative review from one bad day can hurt for months'
        ],
        sampleStars: 5,
        sampleReview: '"Burst pipe at 11pm on a Sunday. Brian was here within 90 minutes. Fixed it, didn\'t price-gouge, cleaned up like nothing happened. This is who I call from now on."',
        sampleAuthor: 'Karen B. · Denver homeowner',
        sampleAvatar: 'KB',
        testQuote: '"Emergency calls have always been the toughest. Now that we rank in the local pack, after-hours calls are up about 30%."',
        testAuthor: 'Brian K., Plumbing Owner · Denver, CO',
        testPhoto: 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=120&q=80'
    },
    'hvac': {
        icon: 'fa-temperature-half',
        name: 'HVAC',
        subtitle: 'For HVAC contractors, AC techs, and heating specialists. Reviews mention diagnosis accuracy, fair pricing, comfort improvements, and warranty support.',
        stats: [
            {num: 'Page 2 → Top 3', label: 'Local pack jump (avg)'},
            {num: '+40%', label: 'Avg seasonal call volume'},
            {num: '6+', label: 'HVAC campaigns delivered'}
        ],
        painpoints: [
            'Summer/winter spike when ranking matters most',
            'Big franchise brands taking the top spots',
            'Customers comparison-shopping 3+ companies before calling',
            'Replacement-vs-repair credibility hinges on trust'
        ],
        sampleStars: 5,
        sampleReview: '"Carlos came out same day in 100°F heat, found a refrigerant leak the last guy missed. AC has been running cold ever since. Fair price, no upsell. Highly recommend."',
        sampleAuthor: 'Patricia L. · Houston resident',
        sampleAvatar: 'PL',
        testQuote: '"Heading into summer last year we were stuck on page 2. Now we are top 3 — phone rings before the heat hits."',
        testAuthor: 'Carlos V., HVAC Owner · Houston, TX',
        testPhoto: 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=120&q=80'
    },
    'dental': {
        icon: 'fa-tooth',
        name: 'Dental',
        subtitle: 'For dental practices and orthodontists. Reviews focus on bedside manner, pain-free experience, insurance handling, and family-friendly service.',
        stats: [
            {num: '+35%', label: 'Avg new-patient bookings'},
            {num: '4.2 → 4.9', label: 'Rating lift (avg)'},
            {num: '5+', label: 'Dental campaigns delivered'}
        ],
        painpoints: [
            'Patients research reviews before booking first appointment',
            'Insurance directories rank by Google rating',
            'Family practices need to look established and trusted',
            'Compliance-aware: nothing can flag patient privacy'
        ],
        sampleStars: 5,
        sampleReview: '"Dr. Sarah\'s office made my 6-year-old\'s first cleaning a great experience. Front desk handled my insurance without me lifting a finger. Genuinely warm staff — finally found our family dentist."',
        sampleAuthor: 'Megan R. · Austin parent',
        sampleAvatar: 'MR',
        testQuote: '"Was nervous about anything that could risk compliance. Smart Buzzer\'s gradual pace put me at ease. New-patient bookings up significantly."',
        testAuthor: 'Dr. Sarah L., Dental Practice · Austin, TX',
        testPhoto: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=120&q=80'
    },
    'accounting': {
        icon: 'fa-calculator',
        name: 'Accounting',
        subtitle: 'For CPAs, bookkeepers, and tax preparation services. Reviews focus on professionalism, accuracy, deadline management, and saving clients money.',
        stats: [
            {num: '2.5x', label: 'Avg new clients pre-tax season'},
            {num: 'Top Rated', label: 'Filter unlocked on Google'},
            {num: '8+', label: 'Accounting campaigns delivered'}
        ],
        painpoints: [
            'Tax season ranks decide annual revenue',
            'Big firms with hundreds of reviews look more legitimate',
            'Hard to break into Google\'s "Top Rated" filter',
            'Every new client wants social proof before signing engagement'
        ],
        sampleStars: 5,
        sampleReview: '"Pat caught a deduction my last CPA missed for 3 years running. Filed amended returns and we got back $14k. Professional, responsive, and explained everything in plain English."',
        sampleAuthor: 'Tom W. · Charlotte business owner',
        sampleAvatar: 'TW',
        testQuote: '"As a small CPA practice, getting noticed before tax season is everything. Reviews mention our specialties so they actually attract the right clients."',
        testAuthor: 'Pat O., CPA · Charlotte, NC',
        testPhoto: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&q=80'
    },
    'healthcare': {
        icon: 'fa-stethoscope',
        name: 'Healthcare / Clinic',
        subtitle: 'For medical clinics, urgent care, chiropractors, and specialty practices. Reviews focus on wait times, staff friendliness, treatment outcomes, and insurance smoothness.',
        stats: [
            {num: '+38%', label: 'Avg new patient inquiries'},
            {num: 'In-network', label: 'Rating threshold passed'},
            {num: '7+', label: 'Healthcare campaigns delivered'}
        ],
        painpoints: [
            'Patients increasingly choose clinics by Google reviews',
            'Insurance partners check ratings for in-network listings',
            'New clinics struggle to look established',
            'One bad experience can sink a small practice'
        ],
        sampleStars: 5,
        sampleReview: '"Dr. Aaron\'s clinic was a 30-min drive but worth it. Got in same-day for a chronic issue 2 other clinics dismissed. Diagnosed and treated — feeling 80% better in 2 weeks."',
        sampleAuthor: 'Linda H. · San Diego patient',
        sampleAvatar: 'LH',
        testQuote: '"Just opened our second location. Within 2 months, ratings comparable to the original — patients couldn\'t tell which was the new one."',
        testAuthor: 'Dr. Aaron F., Clinic Owner · San Diego, CA',
        testPhoto: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=120&q=80'
    },
    'auto_dealer': {
        icon: 'fa-car',
        name: 'Auto Dealer',
        subtitle: 'For independent and franchise dealerships. Reviews focus on no-pressure sales, transparent pricing, and post-sale support.',
        stats: [
            {num: '+28%', label: 'Avg lot foot traffic'},
            {num: '4.0 → 4.6', label: 'Rating lift (avg)'},
            {num: '7+', label: 'Auto Dealer campaigns delivered'}
        ],
        painpoints: [
            'Online research dominates buying decisions now',
            'Big dealerships rank by sheer volume of reviews',
            'One angry buyer can drag rating down for a year',
            'CarGurus/AutoTrader visibility tied to Google reputation'
        ],
        sampleStars: 5,
        sampleReview: '"Tony\'s lot is small but his prices beat the big dealerships in town. No surprise fees, took my trade-in at a fair number, and delivered the car detailed and full of gas. Refreshing."',
        sampleAuthor: 'Marcus J. · Atlanta buyer',
        sampleAvatar: 'MJ',
        testQuote: '"Used dealer market is brutal — buyers shop reviews before stepping on the lot. Our Google rating was the only thing keeping us in the conversation."',
        testAuthor: 'Tony G., Used Auto Dealer · Atlanta, GA',
        testPhoto: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=120&q=80'
    },
    'lawyer': {
        icon: 'fa-scale-balanced',
        name: 'Lawyer',
        subtitle: 'For solo and small-firm attorneys (family, criminal, personal injury, immigration). Reviews focus on communication, outcomes, and empathy.',
        stats: [
            {num: '3x', label: 'Avg consult lead volume'},
            {num: 'Top 3', label: 'Practice area rank reached'},
            {num: '4+', label: 'Lawyer campaigns delivered'}
        ],
        painpoints: [
            'High-stakes cases mean every lead is worth thousands',
            'Big firms with hundreds of reviews dominate search',
            'Bar-association ethical limits on direct asks',
            'Hard to convert prospects without strong social proof'
        ],
        sampleStars: 5,
        sampleReview: '"After my accident the insurance company was lowballing me hard. The team here negotiated a settlement 4x what was first offered. Professional, kept me updated, didn\'t over-promise. Worth every cent of the contingency."',
        sampleAuthor: 'Anonymous · PI client',
        sampleAvatar: 'AC',
        testQuote: '"PI law is competitive — every consult matters. Now we rank top 3 for our practice areas. Lead volume tripled in 4 months."',
        testAuthor: 'Anonymous, Personal Injury Attorney',
        testPhoto: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=120&q=80'
    },
    'realtor': {
        icon: 'fa-house',
        name: 'Realtor',
        subtitle: 'For independent realtors and small brokerages. Reviews focus on local market knowledge, negotiation, and end-to-end transaction smoothness.',
        stats: [
            {num: '+45%', label: 'Avg listing inquiries'},
            {num: 'Top Producer', label: 'Rating range reached'},
            {num: '5+', label: 'Realtor campaigns delivered'}
        ],
        painpoints: [
            'Buyers/sellers Google you before calling',
            'Big-name brokerages ranking ahead of solo agents',
            'Need to look like a "top producer" from day one',
            'Referrals not enough in slower markets'
        ],
        sampleStars: 5,
        sampleReview: '"Linda sold our Scottsdale home in 9 days, $40k over asking. Her local market knowledge was incredible — knew exactly which buyer pool to target. Hands down the best agent I\'ve worked with in 4 sales."',
        sampleAuthor: 'Robert &amp; Susan T. · Sellers',
        sampleAvatar: 'RT',
        testQuote: '"Solo realtor in a market dominated by Compass and Re/Max. Reviews changed the conversation — sellers now ask for me by name."',
        testAuthor: 'Linda H., Realtor · Scottsdale, AZ',
        testPhoto: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&q=80'
    },
    'salon': {
        icon: 'fa-scissors',
        name: 'Salon / Spa',
        subtitle: 'For hair salons, nail spas, barbershops, and beauty studios. Reviews mention specific services, atmosphere, and stylist personality.',
        stats: [
            {num: '+50%', label: 'Avg walk-in bookings'},
            {num: 'Top 3', label: 'Booking app priority unlocked'},
            {num: '4+', label: 'Salon campaigns delivered'}
        ],
        painpoints: [
            'Walk-ins decide based on reviews alone',
            'Booking apps prioritize highly-rated salons',
            'Hard to compete with chains for new-customer flow',
            'One unhappy client can hurt for months'
        ],
        sampleStars: 5,
        sampleReview: '"Sophia\'s color work is unreal — went from box-dye damage to a salon-quality balayage in one session. The space is gorgeous, no chemical smell, and they actually listen. Booked my next 3 visits already."',
        sampleAuthor: 'Isabella M. · Miami client',
        sampleAvatar: 'IM',
        testQuote: '"Salons live and die by online ratings. After 88 reviews we are getting walk-ins from people who never knew we existed."',
        testAuthor: 'Sophia M., Salon Owner · Miami, FL',
        testPhoto: 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=120&q=80'
    },
    'locksmith': {
        icon: 'fa-key',
        name: 'Locksmith',
        subtitle: 'For independent locksmiths handling residential, automotive, and commercial work. Reviews focus on response time, fair pricing, and trust.',
        stats: [
            {num: '+60%', label: 'Avg emergency call volume'},
            {num: 'Top 3', label: 'Outranked scammer listings'},
            {num: '3+', label: 'Locksmith campaigns delivered'}
        ],
        painpoints: [
            'Lockout-scammers dominating search ranks',
            'Locked-out customers pick whoever ranks first — fast',
            'Trust is the entire pitch (you\'re inviting a stranger)',
            'Hard to differentiate from low-rated competitors'
        ],
        sampleStars: 5,
        sampleReview: '"Locked out at midnight in Vegas. Eli was here in 25 minutes, picked the lock without damaging the door, and charged exactly what he quoted on the phone. Saved my night."',
        sampleAuthor: 'Christopher R. · Las Vegas',
        sampleAvatar: 'CR',
        testQuote: '"Locksmith industry is overrun with scammers. Real reviews from real-looking local accounts changed our positioning overnight."',
        testAuthor: 'Eli W., Locksmith · Las Vegas, NV',
        testPhoto: 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=120&q=80'
    },
    'landscaping': {
        icon: 'fa-tree',
        name: 'Landscaping',
        subtitle: 'For lawn care, landscaping companies, and tree services. Reviews focus on dependability, attention to detail, and seasonal results.',
        stats: [
            {num: '+40%', label: 'Avg recurring contracts'},
            {num: 'Top 3', label: 'Spring-rush rank reached'},
            {num: '4+', label: 'Landscaping campaigns delivered'}
        ],
        painpoints: [
            'Spring rush goes to whoever ranks highest',
            'Recurring contracts depend on social proof',
            'Hard to stand out from "lawn-mowing kid" competitors',
            'Quality work needs visibility to convert'
        ],
        sampleStars: 5,
        sampleReview: '"Greg\'s team transformed our backyard. Custom hardscaping, irrigation, sod — all on schedule and within budget. Quality is on par with crews charging 2x. Already booked them for spring cleanup."',
        sampleAuthor: 'Andrew &amp; Beth N. · Raleigh',
        sampleAvatar: 'AN',
        testQuote: '"Spring is everything. After our campaign, recurring lawn-care contracts went up 40% because new customers trusted us before the first quote."',
        testAuthor: 'Greg P., Landscaping Owner · Raleigh, NC',
        testPhoto: 'https://images.unsplash.com/photo-1527081400256-ba1d989a8a4e?w=120&q=80'
    },
    'other': {
        icon: 'fa-store',
        name: 'Other Industries',
        subtitle: 'Don\'t see your industry? We\'ve served retail, fitness studios, pet services, photographers, event planners, cleaning services, moving companies, and many more. If your business has a Google profile and serves locals — we can help.',
        stats: [
            {num: '1,200+', label: 'Total campaigns delivered'},
            {num: '16+', label: 'Verticals successfully served'},
            {num: 'Top 3', label: 'Avg local-pack rank reached'}
        ],
        painpoints: [
            'Local SEO benefits every brick-and-mortar business',
            'Trust signals work the same across every vertical',
            'Industry-specific copy still applies to niche services',
            'Whatever your competition is doing on Google — you can match it'
        ],
        sampleStars: 5,
        sampleReview: '"Kira\'s Portland studio combines barre, pilates, and strength in 45-min classes. Instructors actually correct your form — felt the difference in week 2. Locker rooms are spotless. Best class quality I\'ve found in 8 years of studios."',
        sampleAuthor: 'Hannah K. · Portland member',
        sampleAvatar: 'HK',
        testQuote: '"Run a small fitness studio. Reviews mention our class formats and instructor names. New signups doubled."',
        testAuthor: 'Kira S., Studio Owner · Portland, OR',
        testPhoto: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&q=80'
    }
};

// ===== INDUSTRY SELECTOR LOGIC =====
function renderIndustry(key) {
    var d = industryData[key] || industryData['construction'];
    var painsHtml = d.painpoints.map(function(p) {
        return '<li><i class="fa-solid fa-triangle-exclamation"></i><span>' + p + '</span></li>';
    }).join('');
    var stars = '★★★★★'.substring(0, d.sampleStars);

    var industryShort = d.name.split('/')[0].trim();

    document.getElementById('industryContent').innerHTML =
        '<div class="industry-content-grid">' +
            '<div class="industry-content-left">' +
                '<div class="ic-icon"><i class="fa-solid ' + d.icon + '"></i></div>' +
                '<h3>Built for ' + d.name + '</h3>' +
                '<p class="ic-subtitle">' + d.subtitle + '</p>' +
                '<ul class="ic-checklist">' +
                    '<li><i class="fa-solid fa-check"></i><span><strong>' + industryShort + '-Tailored Content</strong></span></li>' +
                    '<li><i class="fa-solid fa-check"></i><span><strong>Gradual Posting</strong> — 5-10 daily, ~3 stick</span></li>' +
                    '<li><i class="fa-solid fa-check"></i><span><strong>Real Aged Accounts</strong> — 6 months+, unique IPs</span></li>' +
                '</ul>' +
            '</div>' +
            '<div class="industry-content-right">' +
                '<h4>What ' + d.name + ' Owners Struggle With</h4>' +
                '<ul class="ic-painpoints">' + painsHtml + '</ul>' +
                '<span class="ic-sample-label">📝 Sample Review We\'d Write</span>' +
                '<div class="ic-sample-card">' +
                    '<div class="ic-sample-stars">' + stars + '</div>' +
                    '<div class="ic-sample-text">' + d.sampleReview + '</div>' +
                    '<div class="ic-sample-author">' +
                        '<div class="ic-sample-photo" style="background-image: url(\'' + d.testPhoto + '\');"></div>' +
                        '<span>' + d.sampleAuthor + '</span>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
}

function selectIndustry(key, scrollTo) {
    document.querySelectorAll('.industry-pill').forEach(function(p) {
        p.classList.toggle('active', p.getAttribute('data-industry') === key);
    });
    // Close dropdown if open
    var moreList = document.getElementById('industryMoreList');
    if (moreList) moreList.classList.remove('show');

    renderIndustry(key);
    safeLocalStorage('set', 'sb_industry', key);

    var dropdown = document.getElementById('ofIndustry');
    if (dropdown && Array.from(dropdown.options).some(function(o) { return o.value === key; })) {
        dropdown.value = key;
        saveFormField('industry', key);
    }

    logAnalyticsEvent('INDUSTRY_SELECTED', {industry: key});

    if (scrollTo) {
        document.getElementById('industries').scrollIntoView({behavior: 'smooth', block: 'start'});
    }
}

function toggleMoreIndustries(ev) {
    ev.stopPropagation();
    var list = document.getElementById('industryMoreList');
    list.classList.toggle('show');
}
document.addEventListener('click', function(ev) {
    var list = document.getElementById('industryMoreList');
    if (!list) return;
    if (!ev.target.closest('.industry-more-wrap')) list.classList.remove('show');
});

// Initial industry (URL #hash, ?ind=, or stored)
(function() {
    var urlParams = new URLSearchParams(window.location.search);
    var hashKey = (window.location.hash || '').replace(/^#/, '').toLowerCase();
    // Allow hyphen alias: auto-repair -> auto_repair, auto-dealer -> auto_dealer
    if (hashKey) hashKey = hashKey.replace(/-/g, '_');
    var fromHash = (hashKey && industryData[hashKey]) ? hashKey : null;
    var initialInd = fromHash || urlParams.get('ind') || safeLocalStorage('get', 'sb_industry') || 'construction';
    if (!industryData[initialInd]) initialInd = 'construction';
    selectIndustry(initialInd, !!fromHash);
})();

// React to in-page hash changes (e.g. user clicks #restaurant link on same page)
window.addEventListener('hashchange', function() {
    var key = (window.location.hash || '').replace(/^#/, '').toLowerCase().replace(/-/g, '_');
    if (key && industryData[key]) {
        selectIndustry(key, true);
    }
});

// ===== UVP MODAL FUNCTIONS =====
function openUvpModalInd(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeUvpModalInd(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.uvp-modal-ind.show').forEach(function(m) { m.classList.remove('show'); });
        document.body.style.overflow = '';
    }
});

// ===== FAQ ACCORDION =====
function toggleFaq(item) {
    item.classList.toggle('active');
}

// ===== TESTIMONIAL CAROUSEL =====
function scrollTesti(direction) {
    var grid = document.getElementById('csGrid');
    if (!grid) return;
    var firstCard = grid.querySelector('.cs-card');
    var step = firstCard ? (firstCard.offsetWidth + 20) : 340;
    grid.scrollBy({ left: direction * step, behavior: 'smooth' });
}

// ===== ORDER FORM LOGIC =====
function selectPkg(el) {
    document.querySelectorAll('.of-pkg').forEach(function(p) { p.classList.remove('selected'); });
    el.classList.add('selected');
    var radio = el.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
    saveFormField('pkg', el.getAttribute('data-pkg'));
}

function preSelectPkg(pkg) {
    var card = document.querySelector('.of-pkg[data-pkg="' + pkg + '"]');
    if (card) selectPkg(card);
}

// Form auto-save
function saveFormField(field, value) {
    safeLocalStorage('set', 'sb_form_' + field, value);
}
function restoreForm() {
    var biz = safeLocalStorage('get', 'sb_form_biz');
    var wa = safeLocalStorage('get', 'sb_form_wa');
    var email = safeLocalStorage('get', 'sb_form_email');
    var pkg = safeLocalStorage('get', 'sb_form_pkg');
    var industry = safeLocalStorage('get', 'sb_form_industry');
    if (biz) document.getElementById('ofBizName').value = biz;
    if (wa) document.getElementById('ofWhatsapp').value = wa;
    if (email) document.getElementById('ofEmail').value = email;
    if (industry) document.getElementById('ofIndustry').value = industry;
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
    var indInput = document.getElementById('ofIndustry');
    if (bizInput) bizInput.addEventListener('input', function() { saveFormField('biz', this.value); });
    if (waInput) waInput.addEventListener('input', function() { saveFormField('wa', this.value); });
    if (emailInput) emailInput.addEventListener('input', function() { saveFormField('email', this.value); });
    if (indInput) indInput.addEventListener('change', function() { saveFormField('industry', this.value); });
});

// ===== GLOBAL PACKAGE METADATA =====
var sbPkgMeta = {
    'starter':     {id: 'pkg_starter_55',     name: 'Buy Google Reviews - 55 Local',  item_category: 'Google Reviews', price: 360.00, reviews: 55},
    'growth':      {id: 'pkg_growth_88',      name: 'Buy Google Reviews - 88 Local',  item_category: 'Google Reviews', price: 550.00, reviews: 88},
    'performance': {id: 'pkg_performance_110', name: 'Buy Google Reviews - 110 Local', item_category: 'Google Reviews', price: 660.00, reviews: 110}
};
var sbLastSelectedPkg = null;

// ===== view_item: fires when pricing section becomes visible =====
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
                    value: sbPkgMeta['growth'].price,
                    items: items
                }
            });
            logAnalyticsEvent('VIEW_ITEM', {location: 'pricing'});
        }
    }
    window.addEventListener('scroll', function() { setTimeout(fireViewItem, 150); });
    document.addEventListener('DOMContentLoaded', function() { setTimeout(fireViewItem, 500); });
})();

// ===== add_to_cart + Lead pixel =====
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-package]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var pkg = this.getAttribute('data-package');
            var meta = sbPkgMeta[pkg];
            if (!meta) return;
            sbLastSelectedPkg = pkg;
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
            if (typeof fbq !== 'undefined') { fbq('track', 'Lead', {content_name: meta.name}); }
            if (typeof ttq !== 'undefined') { ttq.track('SubmitForm', {content_name: meta.name}); }
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'Pricing Click',
                value: meta.price,
                currency: 'USD'
            });
            logAnalyticsEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {package: pkg, location: 'pricing'});
        });
    });

    // generate_lead: WhatsApp
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"], a[href*="api.whatsapp"]').forEach(function(waBtn) {
        waBtn.addEventListener('click', function() {
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

// ===== CLICK HEATMAP =====
document.addEventListener('click', function(e) {
    var target = e.target;
    var tagName = target.tagName.toLowerCase();
    var interactiveElements = ['a', 'button', 'input', 'select', 'textarea'];
    if (interactiveElements.includes(tagName) || target.onclick || target.closest('a') || target.closest('button')) {
        logAnalyticsEvent('CLICK_HEATMAP', {x: e.clientX, y: e.clientY, element: tagName});
    }
});

// ===== SCROLL DEPTH =====
window.addEventListener('scroll', function() {
    var scrollPercent = (window.scrollY + window.innerHeight) / document.body.scrollHeight * 100;
    [25, 50, 75, 100].forEach(function(depth) {
        if (scrollPercent >= depth && !scrollDepths[depth]) {
            scrollDepths[depth] = true;
            logAnalyticsEvent('SCROLL_DEPTH_' + depth, {depth: depth});
        }
    });
});

// ===== TIME ON PAGE & EXIT =====
var sbLastClickedUrl = '';
document.addEventListener('click', function(ev) {
    var anchor = ev.target.closest('a');
    if (anchor && anchor.href) { sbLastClickedUrl = anchor.href; }
});
window.addEventListener('beforeunload', function() {
    var timeSpent = Math.floor((Date.now() - pageLoadTime) / 1000);
    beaconAnalyticsEvent('TIME_ON_PAGE', {duration: timeSpent});
    beaconAnalyticsEvent('EXIT_PAGE', {exit_url: sbLastClickedUrl || 'tab_close_or_back', time_spent: timeSpent});
});

// ===== EXTERNAL LINK =====
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('header a, footer a').forEach(function(link) {
        link.addEventListener('click', function() {
            var href = this.getAttribute('href');
            if (href && !href.startsWith('#')) {
                logAnalyticsEvent('EXTERNAL_LINK_CLICK', {
                    location: this.closest('header') ? 'header' : 'footer',
                    url: href,
                    text: this.textContent.trim()
                });
            }
        });
    });
});

// ===== SMOOTH SCROLL =====
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
                if (history.pushState) history.pushState(null, null, targetId);
            }
        });
    });
});

// ===== PAGE VIEW + RETURN VISITOR =====
logAnalyticsEvent('PAGE_VIEW', {return_visitor: returnVisitor});
if (returnVisitor) logAnalyticsEvent('RETURN_VISITOR', {});

// ===== COUNTDOWN TIMER =====
function updateCountdown() {
    var now = new Date();
    var tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    var diff = tomorrow - now;
    var hours = Math.floor(diff / (1000 * 60 * 60));
    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((diff % (1000 * 60)) / 1000);
    var hEl = document.getElementById('ofH'), mEl = document.getElementById('ofM'), sEl = document.getElementById('ofS');
    if (hEl) hEl.textContent = String(hours).padStart(2, '0');
    if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
    if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
}
setInterval(updateCountdown, 1000);
updateCountdown();

// ===== SUBMIT ORDER =====
function submitOrder() {
    var biz = document.getElementById('ofBizName').value.trim();
    var wa = document.getElementById('ofWhatsapp').value.trim();
    var email = document.getElementById('ofEmail').value.trim();
    var industry = document.getElementById('ofIndustry').value.trim();
    var pkg = document.querySelector('.of-pkg.selected');

    if (!biz) { alert('Please enter your Google Business name.'); return; }
    if (!wa) { alert('Please enter your WhatsApp number.'); return; }
    if (!pkg) { alert('Please select a package.'); return; }

    var pkgName = pkg.querySelector('.of-pkg-name').textContent.replace(/POPULAR/g, '').trim();
    var pkgPrice = pkg.querySelector('.of-pkg-price').textContent.trim();
    var pkgValue = pkg.getAttribute('data-pkg');
    var meta = sbPkgMeta[pkgValue] || sbPkgMeta['growth'];
    var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

    var nameParts = biz.split(' ');
    var firstName = nameParts[0] || '';
    var lastName = nameParts.slice(1).join(' ') || '';

    // dataLayer: begin_checkout
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'begin_checkout',
        user_data: {email: email || '', phone_number: wa, first_name: firstName, last_name: lastName},
        ecommerce: {
            currency: 'USD',
            value: meta.price,
            items: [{item_id: meta.id, item_name: meta.name, item_category: meta.item_category, price: meta.price, quantity: 1}]
        }
    });

    // dataLayer: add_payment_info
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'add_payment_info',
        ecommerce: {
            currency: 'USD',
            value: meta.price,
            payment_type: 'Credit Card',
            items: [{item_id: meta.id, item_name: meta.name, item_category: meta.item_category, price: meta.price, quantity: 1}]
        }
    });

    // LocalStorage bridge
    safeLocalStorage('set', 'sb_user_email', email || '');
    safeLocalStorage('set', 'sb_user_phone', wa || '');
    safeLocalStorage('set', 'sb_user_fname', firstName || '');
    safeLocalStorage('set', 'sb_user_lname', lastName || '');
    safeLocalStorage('set', 'sb_txn_id', txnId);
    safeLocalStorage('set', 'sb_pkg', pkgValue);

    // FB + TT pixels
    if (typeof fbq !== 'undefined') {
        fbq('track', 'InitiateCheckout', {value: meta.price, currency: 'USD', content_name: pkgName, content_type: 'product', content_ids: [pkgValue]});
    }
    if (typeof ttq !== 'undefined') {
        ttq.track('InitiateCheckout', {value: meta.price, currency: 'USD', content_name: pkgName});
    }

    logAnalyticsEvent('ORDER_SUBMIT', {package: pkgName, price: pkgPrice, business: biz, industry: industry, location: 'order_form'});

    // Customer log
    fetch('log.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            businessName: biz,
            whatsapp: wa,
            businessEmail: email,
            action: pkgValue.toUpperCase(),
            pageUrl: window.location.href,
            industry: industry
        })
    }).catch(function(err) { console.log('Customer log error:', err); });

    var fanbasisLinks = {
        'starter': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/LYvlW',
        'growth': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/MEJmB',
        'performance': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/NEYnK'
    };
    var paymentUrl = fanbasisLinks[pkgValue];
    if (paymentUrl) {
        safeLocalStorage('set', 'sb_form_biz', '');
        safeLocalStorage('set', 'sb_form_wa', '');
        safeLocalStorage('set', 'sb_form_email', '');
        safeLocalStorage('set', 'sb_form_pkg', '');
        safeLocalStorage('set', 'sb_form_industry', '');
        window.location.href = paymentUrl;
    }
}
</script>
</body>
</html>
