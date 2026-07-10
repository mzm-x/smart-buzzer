<?php
// Customer data logger — FORM_SUBMIT only (no click tracking)
// 18-column format consistent with /promo/ and /promo-b1g1/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action']) && $data['action'] === 'log_customer') {
        $ts      = date('Y-m-d H:i:s');
        $clean   = function($v) { return str_replace(["\t","\n","\r"], '', htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8')); };
        $biz     = isset($data['biz'])      ? $clean($data['biz'])      : '-';
        $email   = isset($data['email'])    ? $clean($data['email'])    : '-';
        $wa      = isset($data['wa'])       ? $clean($data['wa'])       : '-';
        $pkgKey  = isset($data['pkg'])      ? $clean($data['pkg'])      : '-';
        $reviews = isset($data['reviews'])  ? (int)$data['reviews']     : 0;
        $pageUrl = isset($data['page_url']) ? $clean($data['page_url']) : '-';

        // Map midyear pkg key to clean display name (consistent with /promo/ pattern)
        $pkgMap = [
            'midyear_starter' => 'Starter',
            'midyear_growth'  => 'Growth',
            'midyear_elite'   => 'Elite',
        ];
        $pkgName = isset($pkgMap[$pkgKey]) ? $pkgMap[$pkgKey] : $pkgKey;

        // Parse UTM from page_url (6 params)
        $utmSource = 'direct'; $utmMedium = 'none'; $utmCampaign = 'direct';
        $utmContent = '-'; $utmTerm = '-'; $placement = '-';
        if (!empty($data['page_url'])) {
            $parsed = parse_url($data['page_url']);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                $utmSource   = isset($params['utm_source'])    ? str_replace(["\t","\n","\r"], '', $params['utm_source'])    : 'direct';
                $utmMedium   = isset($params['utm_medium'])    ? str_replace(["\t","\n","\r"], '', $params['utm_medium'])    : 'none';
                $utmCampaign = isset($params['utm_campaign'])  ? str_replace(["\t","\n","\r"], '', $params['utm_campaign'])  : 'direct';
                $utmContent  = isset($params['utm_content'])   ? str_replace(["\t","\n","\r"], '', $params['utm_content'])   : '-';
                $utmTerm     = isset($params['utm_term'])      ? str_replace(["\t","\n","\r"], '', $params['utm_term'])      : '-';
                $placement   = isset($params['utm_placement']) ? str_replace(["\t","\n","\r"], '', $params['utm_placement']) :
                              (isset($params['placement'])     ? str_replace(["\t","\n","\r"], '', $params['placement'])     : '-');
            }
        }

        // 18-column format: timestamp | business | location | email | whatsapp | package | page_url | reviews | utm_source | utm_medium | utm_campaign | utm_content | placement | state | zip | country | status | utm_term
        $line = implode("\t", [$ts, $biz, '-', $email, $wa, $pkgName, $pageUrl, $reviews, $utmSource, $utmMedium, $utmCampaign, $utmContent, $placement, '-', '-', '-', 'FORM_SUBMIT', $utmTerm]) . "\n";
        @file_put_contents(__DIR__ . '/customer_data.log', $line, FILE_APPEND | LOCK_EX);
        echo json_encode(['status' => 'ok']);
        exit;
    }
    http_response_code(400);
    echo json_encode(['status' => 'error']);
    exit;
}
$__wa=$_SERVER['DOCUMENT_ROOT'].'/wa-config.php'; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER='628979133204';} if(empty($SB_WA_DISPLAY)){$SB_WA_DISPLAY='+62 897-9133-204';}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mid-Year Sale - Smart Buzzer | Boost Your Google Reviews</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    <!-- Analytics Session -->
    <script>
    function getSessionId() {
        var sid = sessionStorage.getItem('sb_session_id');
        if (!sid) {
            sid = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('sb_session_id', sid);
        }
        return sid;
    }
    var sessionId = getSessionId();
    </script>

    <style>
        :root {
            --primary:       #1E40AF;
            --primary-dark:  #1E3A8A;
            --primary-light: #DBEAFE;
            --accent:        #F59E0B;
            --accent-dark:   #D97706;
            --accent-light:  #FEF3C7;
            --white:         #FFFFFF;
            --black:         #0F172A;
            --gray-50:       #F8FAFC;
            --gray-100:      #F1F5F9;
            --gray-200:      #E2E8F0;
            --gray-500:      #64748B;
            --gray-600:      #475569;
            --gray-700:      #334155;
            --green:         #059669;
            --green-light:   #D1FAE5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--white);
            color: var(--black);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ===== HEADER ===== */
        .header {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200); padding: 14px 24px;
        }
        .header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; text-decoration: none; }
        .logo img { height: 36px; width: auto; }
        .header-nav { display: flex; align-items: center; gap: 28px; }
        .header-nav a { color: var(--gray-600); text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s; }
        .header-nav a:hover { color: var(--primary); }
        .header-cta {
            background: var(--primary); color: var(--white); padding: 10px 24px;
            border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;
            transition: all 0.2s;
        }
        .header-cta:hover { background: var(--primary-dark); transform: translateY(-1px); }
        @media (max-width: 768px) {
            .header-nav { display: none; }
        }

        /* ===== COUNTDOWN BAR ===== */
        .countdown-bar {
            position: fixed; top: 65px; width: 100%; z-index: 999;
            background: #DC2626; padding: 9px 24px;
        }
        .countdown-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; justify-content: center; align-items: center; gap: 14px; flex-wrap: wrap;
        }
        .countdown-label { color: var(--accent-light); font-size: 13px; font-weight: 600; }
        .countdown-timer { display: flex; gap: 6px; }
        .cd-block {
            background: rgba(255,255,255,0.25); color: var(--white);
            padding: 3px 10px; border-radius: 6px; text-align: center; min-width: 44px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .cd-num { font-weight: 800; font-size: 15px; display: block; line-height: 1.3; }
        .cd-unit { font-size: 9px; text-transform: uppercase; opacity: 0.85; letter-spacing: 0.5px; }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            padding: 120px 24px 80px;
            background: linear-gradient(160deg, #EFF6FF 0%, #FAFAF9 55%, #FFFBEB 100%);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(30,64,175,0.07) 0%, transparent 70%);
        }
        .hero::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
        }
        .hero-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; position: relative; z-index: 1; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--accent-light); color: var(--accent-dark);
            padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 700;
            border: 1px solid rgba(245,158,11,0.3); margin-bottom: 24px;
        }
        .hero-badge i { font-size: 12px; }
        .hero h1 {
            font-size: 52px; font-weight: 900; line-height: 1.1; letter-spacing: -1.5px;
            color: var(--black); margin-bottom: 20px;
        }
        .hero h1 em { font-style: normal; color: var(--primary); }
        .hero-subtitle { font-size: 18px; color: var(--gray-600); line-height: 1.6; margin-bottom: 36px; }
        .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; }
        .btn-primary {
            background: var(--primary); color: var(--white); padding: 16px 32px;
            border-radius: 14px; text-decoration: none; font-weight: 700; font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1); display: inline-flex; align-items: center; gap: 10px;
            box-shadow: 0 4px 16px rgba(30,64,175,0.35);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(30,64,175,0.45); }
        .btn-secondary {
            background: var(--white); color: var(--primary); padding: 16px 28px;
            border-radius: 14px; text-decoration: none; font-weight: 600; font-size: 15px;
            border: 2px solid var(--primary-light); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-secondary:hover { background: var(--primary-light); }
        .hero-trust { display: flex; gap: 24px; margin-top: 32px; flex-wrap: wrap; }
        .hero-trust-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--gray-600); font-weight: 500; }
        .hero-trust-item i { color: var(--green); font-size: 14px; }
        .hero-image { position: relative; }
        .hero-image img { width: 100%; border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,0.12); }
        .hero-stat-badge {
            position: absolute; background: var(--white); border-radius: 12px;
            padding: 12px 18px; box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            display: flex; align-items: center; gap: 10px;
        }
        .hero-stat-badge.top { top: -16px; right: -16px; }
        .hero-stat-badge.bottom { bottom: -16px; left: -16px; }
        .hero-stat-num { font-size: 22px; font-weight: 800; color: var(--primary); }
        .hero-stat-label { font-size: 12px; color: var(--gray-500); font-weight: 500; }
        .hero-image-mobile { display: none; }
        @media (max-width: 900px) {
            .hero { padding: 148px 20px 60px; }
            .hero-inner { grid-template-columns: 1fr; gap: 0; }
            .hero-badge { margin-bottom: 14px; }
            .hero h1 { font-size: 34px; margin-bottom: 14px; }
            .hero-subtitle { font-size: 16px; margin-bottom: 24px; }
            .hero-actions { margin-bottom: 0; }
            .hero-trust { gap: 14px; margin-top: 20px; }
            .hero-image { display: none; }
            .hero-image-mobile {
                display: block;
                margin: 14px 0 14px;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 12px 36px rgba(0,0,0,0.11);
            }
            .hero-image-mobile img { width: 100%; display: block; border-radius: 20px; }
        }

        /* ===== SECTIONS ===== */
        .section { padding: 80px 0; }
        .section-header { text-align: center; margin-bottom: 56px; }
        .section-tag {
            display: inline-block; background: var(--primary-light); color: var(--primary);
            padding: 5px 16px; border-radius: 50px; font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;
        }
        .section-tag-amber { background: var(--accent-light); color: var(--accent-dark); }
        .section-header h2 { font-size: 36px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 12px; }
        .section-header h2 em { font-style: normal; color: var(--primary); }
        .section-header p { font-size: 17px; color: var(--gray-500); max-width: 540px; margin: 0 auto; }

        /* ===== HOW IT WORKS ===== */
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .step-card {
            background: var(--gray-50); border: 1px solid var(--gray-200);
            border-radius: 20px; padding: 32px; text-align: center;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .step-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.08); }
        .step-num {
            width: 48px; height: 48px; background: var(--primary); color: var(--white);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; margin: 0 auto 20px;
        }
        .step-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .step-card p { font-size: 14px; color: var(--gray-500); line-height: 1.6; }
        @media (max-width: 768px) { .steps-grid { grid-template-columns: 1fr; } }

        /* ===== PRICING ===== */
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 1000px; margin: 0 auto; }
        .pricing-card {
            background: var(--white); border: 2px solid var(--gray-200);
            border-radius: 20px; padding: 32px; position: relative;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            display: flex; flex-direction: column;
        }
        .pricing-card:hover { transform: translateY(-5px); box-shadow: 0 20px 48px rgba(0,0,0,0.1); }
        .pricing-card.featured { border-color: var(--primary); background: var(--primary); color: var(--white); }
        .pricing-popular {
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            background: var(--accent); color: var(--white);
            padding: 4px 20px; border-radius: 50px; font-size: 12px; font-weight: 700; white-space: nowrap;
        }
        .pricing-name { font-size: 22px; font-weight: 800; margin-bottom: 14px; }

        /* Qty block — hero element */
        .pricing-qty-block {
            display: inline-flex; align-items: baseline; gap: 6px;
            margin-bottom: 20px;
        }
        .pricing-card.featured .pricing-qty-block { background: none; border: none; }
        .pricing-qty-num { font-size: 18px; font-weight: 700; color: var(--black); }
        .pricing-card.featured .pricing-qty-num { color: var(--white); }
        .pricing-qty-label { font-size: 18px; font-weight: 700; color: var(--gray-600); }
        .pricing-card.featured .pricing-qty-label { color: rgba(255,255,255,0.85); }

        /* Price block */
        .pricing-original { font-size: 18px; color: var(--gray-400); text-decoration: line-through; margin-bottom: 2px; }
        .pricing-card.featured .pricing-original { color: rgba(255,255,255,0.5); }
        .pricing-current { font-size: 52px; font-weight: 900; letter-spacing: -2px; color: var(--primary); line-height: 1; margin-bottom: 0; }
        .pricing-card.featured .pricing-current { color: var(--white); }

        /* Savings block — unified strip */
        .pricing-savings-block {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
            background: #fef3c7; border: 1px solid #fbbf24;
            border-radius: 10px; padding: 8px 14px; margin: 14px 0 16px;
        }
        .pricing-card.featured .pricing-savings-block { background: rgba(251,191,36,0.18); border-color: rgba(251,191,36,0.45); }
        .savings-amount { font-size: 15px; font-weight: 800; color: #92400e; }
        .pricing-card.featured .savings-amount { color: #fde68a; }
        .savings-divider { color: #d97706; font-size: 12px; }
        .pricing-card.featured .savings-divider { color: rgba(251,191,36,0.5); }
        .savings-pct { font-size: 12px; font-weight: 700; color: #92400e; background: #fde68a; padding: 2px 8px; border-radius: 20px; }
        .pricing-card.featured .savings-pct { color: var(--primary); background: #fbbf24; }
        .savings-per { font-size: 12px; color: #b45309; }
        .pricing-card.featured .savings-per { color: rgba(251,191,36,0.85); }

        /* Guarantee badge */
        .pricing-guarantee { margin-bottom: 20px; }
        .badge-guarantee { background: var(--green-light); color: var(--green); padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
        .pricing-card.featured .badge-guarantee { background: rgba(255,255,255,0.18); color: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.3); }

        .pricing-features { list-style: none; margin-bottom: 28px; flex: 1; }
        .pricing-features li { display: flex; align-items: flex-start; gap: 10px; padding: 6px 0; font-size: 14px; }
        .pricing-features li + li { border-top: 1px solid var(--gray-200); }
        .pricing-card.featured .pricing-features li + li { border-top-color: rgba(255,255,255,0.15); }
        .pf-check { color: var(--green); font-weight: 700; flex-shrink: 0; margin-top: 2px; }
        .pricing-card.featured .pf-check { color: var(--accent); }
        .pricing-btn {
            display: block; width: 100%; padding: 14px 20px;
            background: var(--primary); color: var(--white);
            font-size: 15px; font-weight: 700; border: none; border-radius: 12px;
            cursor: pointer; text-decoration: none; text-align: center;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1); box-shadow: 0 4px 12px rgba(30,64,175,0.3);
            margin-top: auto;
        }
        .pricing-btn:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30,64,175,0.4); }
        .pricing-card.featured .pricing-btn { background: var(--white); color: var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .pricing-card.featured .pricing-btn:hover { background: var(--accent-light); }
        @media (max-width: 900px) { .pricing-grid { grid-template-columns: 1fr; max-width: 420px; } }

        /* ===== PACKAGE PICKER (form selector) ===== */
        .pkg-picker { display: flex; gap: 16px; max-width: 860px; margin: 0 auto; }
        .pkg-option {
            flex: 1; border: 2px solid var(--gray-200); border-radius: 20px;
            padding: 24px 20px; cursor: pointer; text-align: center; position: relative;
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .pkg-option:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: 0 12px 32px rgba(30,64,175,0.12); }
        .pkg-option.active { border-color: var(--primary); background: var(--primary-light); box-shadow: 0 8px 28px rgba(30,64,175,0.18); transform: translateY(-3px); }
        .pkg-opt-badge {
            position: absolute; top: -13px; left: 50%; transform: translateX(-50%);
            background: var(--accent); color: var(--white);
            padding: 4px 18px; border-radius: 50px; font-size: 11px; font-weight: 700; white-space: nowrap;
        }
        .pkg-opt-name { font-size: 20px; font-weight: 800; color: var(--black); margin-bottom: 4px; }
        .pkg-opt-reviews { font-size: 13px; color: var(--gray-500); margin-bottom: 14px; font-weight: 500; }
        .pkg-opt-price { font-size: 38px; font-weight: 900; color: var(--primary); letter-spacing: -1px; line-height: 1; margin-bottom: 4px; }
        .pkg-opt-was { font-size: 13px; color: var(--gray-500); margin-bottom: 10px; }
        .pkg-opt-was s { margin-right: 4px; }
        .pkg-opt-save { display: inline-block; background: var(--accent); color: var(--white); padding: 3px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .pkg-cta-wrap { text-align: center; margin-top: 32px; }
        .pkg-cta-btn {
            background: var(--primary); color: var(--white); padding: 16px 48px;
            border-radius: 14px; font-size: 16px; font-weight: 700; border: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(30,64,175,0.35);
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            animation: submitPulse 2s ease-in-out infinite;
        }
        .pkg-cta-btn:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(30,64,175,0.45); animation: none; }
        @media (max-width: 700px) { .pkg-picker { flex-direction: column; } }

        /* ===== CONTENT FLEX ===== */
        .content-flex { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
        .content-flex.reverse { direction: rtl; }
        .content-flex.reverse > * { direction: ltr; }
        .content-image img { width: 100%; border-radius: 16px; box-shadow: 0 12px 32px rgba(0,0,0,0.08); cursor: zoom-in; }
        .content-text h3 { font-size: 28px; font-weight: 800; margin-bottom: 16px; letter-spacing: -0.5px; }
        .content-text p { font-size: 16px; color: var(--gray-600); line-height: 1.7; margin-bottom: 20px; }
        .content-check { display: flex; align-items: flex-start; gap: 10px; font-size: 15px; color: var(--gray-700); margin-bottom: 10px; }
        .content-check-icon { color: var(--green); font-weight: 700; flex-shrink: 0; }
        @media (max-width: 768px) { .content-flex, .content-flex.reverse { grid-template-columns: 1fr; direction: ltr; } }

        /* ===== WHY LIST ===== */
        .why-features { list-style: none; }
        .why-features li { display: flex; align-items: flex-start; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--gray-200); }
        .why-check { width: 28px; height: 28px; background: var(--green-light); color: var(--green); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; margin-top: 2px; }
        .why-features strong { display: block; font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .why-features span { font-size: 14px; color: var(--gray-500); }

        /* ===== DASHBOARD PREVIEW ===== */
        .dashboard-preview img { width: 100%; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.1); cursor: zoom-in; }

        /* ===== STATS ROW ===== */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 16px; padding: 28px; text-align: center; }
        .stat-num { font-size: 40px; font-weight: 900; color: var(--primary); letter-spacing: -1px; }
        .stat-label { font-size: 14px; color: var(--gray-500); margin-top: 6px; font-weight: 500; }
        @media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }

        /* ===== CLIENTS MARQUEE ===== */
        .clients-marquee-wrap { overflow: hidden; position: relative; }
        .clients-marquee-wrap::before, .clients-marquee-wrap::after {
            content: ''; position: absolute; top: 0; width: 100px; height: 100%; z-index: 1;
        }
        .clients-marquee-wrap::before { left: 0; background: linear-gradient(to right, var(--white), transparent); }
        .clients-marquee-wrap::after { right: 0; background: linear-gradient(to left, var(--white), transparent); }
        .clients-marquee { display: flex; gap: 40px; animation: marquee 30s linear infinite; width: max-content; }
        .client-logo img { height: 48px; width: auto; filter: grayscale(100%); opacity: 0.5; transition: all 0.3s; }
        .client-logo img:hover { filter: grayscale(0%); opacity: 1; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* ===== COMPARISON TABLE ===== */
        .compare-table { border: 1px solid var(--gray-200); border-radius: 16px; overflow: hidden; }
        .compare-row { display: grid; grid-template-columns: 2fr 1fr 1fr; }
        .compare-row + .compare-row { border-top: 1px solid var(--gray-200); }
        .compare-cell { padding: 14px 20px; font-size: 14px; }
        .compare-header .compare-cell { background: var(--primary); color: var(--white); font-weight: 700; font-size: 13px; }
        .compare-cell.metric { font-weight: 600; color: var(--gray-700); }
        .compare-cell.bad { color: #DC2626; background: #FEF2F2; }
        .compare-cell.good { color: var(--green); background: var(--green-light); font-weight: 600; }

        /* ===== TESTIMONIALS ===== */
        .testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .testimonial-card { background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 20px; padding: 28px; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .testimonial-card:hover { transform: translateY(-3px); }
        .testimonial-stars { color: var(--accent); font-size: 14px; margin-bottom: 14px; }
        .testimonial-text { font-size: 15px; color: var(--gray-700); line-height: 1.7; font-style: italic; margin-bottom: 20px; }
        .testimonial-author { display: flex; align-items: center; gap: 14px; }
        .testimonial-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--primary); color: var(--white); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0; }
        .testimonial-name { font-size: 14px; font-weight: 700; }
        .testimonial-biz { font-size: 13px; color: var(--gray-500); }
        .testimonial-result { display: inline-block; background: var(--green-light); color: var(--green); padding: 3px 10px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-top: 6px; }
        @media (max-width: 768px) { .testimonials-grid { grid-template-columns: 1fr; } }

        /* ===== FAQ ===== */
        .faq-list { max-width: 760px; margin: 0 auto; }
        .faq-item { border: 1px solid var(--gray-200); border-radius: 16px; margin-bottom: 12px; overflow: hidden; }
        .faq-question {
            width: 100%; background: none; border: none; padding: 18px 24px;
            font-size: 15px; font-weight: 600; color: var(--black); cursor: pointer;
            display: flex; justify-content: space-between; align-items: center; text-align: left;
            transition: background 0.2s;
        }
        .faq-question:hover { background: var(--gray-50); }
        .faq-icon { font-size: 20px; color: var(--primary); flex-shrink: 0; margin-left: 16px; font-weight: 300; transition: transform 0.3s; }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .faq-answer-inner { padding: 0 24px 18px; font-size: 14px; color: var(--gray-600); line-height: 1.7; }
        .faq-item.active .faq-answer { max-height: 400px; }
        .faq-item.active .faq-icon { transform: rotate(45deg); }

        /* ===== REVIEW CALCULATOR ===== */
        .calc-card { background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 20px; padding: 40px; max-width: 700px; margin: 0 auto; }
        .calc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .calc-field label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-600); margin-bottom: 8px; }
        .calc-field input {
            width: 100%; padding: 12px 16px; border: 2px solid var(--gray-200); border-radius: 10px;
            font-size: 16px; font-weight: 600; color: var(--black); background: var(--white);
            transition: border-color 0.2s; outline: none;
        }
        .calc-field input:focus { border-color: var(--primary); }
        .calc-btn {
            width: 100%; padding: 14px; background: var(--primary); color: var(--white);
            border: none; border-radius: 10px; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
        }
        .calc-btn:hover { background: var(--primary-dark); }
        .calc-result { margin-top: 24px; display: none; }
        .calc-result-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .calc-result-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 12px; padding: 20px; text-align: center; }
        .calc-big-num { font-size: 52px; font-weight: 900; color: var(--primary); letter-spacing: -2px; line-height: 1; }
        .calc-big-label { font-size: 13px; color: var(--gray-500); margin-top: 6px; }
        .calc-rec { background: var(--primary); color: var(--white); border-radius: 12px; padding: 20px; }
        .calc-rec-tag { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; margin-bottom: 6px; }
        .calc-rec-name { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .calc-rec-info { font-size: 14px; opacity: 0.85; margin-bottom: 4px; }
        .calc-rec-save { font-size: 13px; color: var(--accent); font-weight: 600; }
        @media (max-width: 600px) { .calc-grid { grid-template-columns: 1fr; } .calc-result-grid { grid-template-columns: 1fr; } }

        /* ===== FOOTER ===== */
        .footer { background: #ffffff; color: rgba(0,0,0,0.7); padding: 60px 0 32px; border-top: 1px solid #e5e7eb; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px; margin-bottom: 40px; }
        .footer-brand img { height: 36px; margin-bottom: 16px; }
        .footer-brand p { font-size: 14px; line-height: 1.6; color: rgba(0,0,0,0.5); }
        .footer-brand p em { display: block; margin-top: 8px; font-style: italic; font-size: 13px; color: rgba(0,0,0,0.35); }
        .footer-col h4 { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px; }
        .footer-col a { display: block; font-size: 14px; color: rgba(0,0,0,0.5); text-decoration: none; margin-bottom: 10px; transition: color 0.2s; }
        .footer-col a:hover { color: #111827; }
        .footer-bottom { border-top: 1px solid rgba(0,0,0,0.1); padding-top: 24px; text-align: center; font-size: 13px; color: rgba(0,0,0,0.35); }
        @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr; gap: 32px; } }

        /* ===== WHATSAPP FLOAT ===== */
        .wa-float-btn {
            position: fixed; bottom: 80px; right: 24px; z-index: 900;
            width: 56px; height: 56px; background: #25D366; color: var(--white);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 26px; box-shadow: 0 4px 16px rgba(37,211,102,0.4); text-decoration: none;
            transition: all 0.3s; animation: waPulse 2.5s ease-in-out infinite;
        }
        .wa-float-btn:hover { transform: scale(1.1); box-shadow: 0 8px 24px rgba(37,211,102,0.5); animation: none; }
        @keyframes waPulse {
            0%, 100% { box-shadow: 0 4px 16px rgba(37,211,102,0.4); }
            50% { box-shadow: 0 4px 24px rgba(37,211,102,0.6), 0 0 0 10px rgba(37,211,102,0.08); }
        }

        /* ===== SOCIAL PROOF POPUP ===== */
        .social-proof {
            position: fixed; bottom: 24px; left: 24px; z-index: 800;
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: 14px; padding: 14px 18px; box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            display: flex; align-items: center; gap: 12px; max-width: 300px;
            transform: translateX(-120%); transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
        }
        .social-proof.show { transform: translateX(0); }
        .sp-icon { width: 36px; height: 36px; background: var(--green-light); color: var(--green); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .sp-text { font-size: 13px; color: var(--gray-700); line-height: 1.4; }
        .sp-time { font-size: 11px; color: var(--gray-500); }
        .sp-close { margin-left: auto; background: none; border: none; cursor: pointer; color: var(--gray-500); font-size: 16px; flex-shrink: 0; padding: 2px; }

        /* ===== DESKTOP STICKY BAR ===== */
        .desktop-sticky {
            position: fixed; bottom: 0; left: 0; width: 100%; z-index: 700;
            background: var(--primary-dark); padding: 14px 24px;
            transform: translateY(100%); transition: transform 0.3s ease;
            display: none;
        }
        .desktop-sticky.show { transform: translateY(0); }
        .desktop-sticky-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .desktop-sticky-text { color: var(--white); font-size: 15px; font-weight: 600; }
        .desktop-sticky-text span { color: var(--accent); }
        .desktop-sticky-btns { display: flex; gap: 12px; }
        .btn-amber { background: var(--accent); color: var(--white); padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.2s; }
        .btn-amber:hover { background: var(--accent-dark); }
        .btn-wa-sm { background: #25D366; color: var(--white); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.2s; }
        .btn-wa-sm:hover { background: #1ebe5a; }
        @media (min-width: 769px) { .desktop-sticky { display: block; } }

        /* ===== MOBILE STICKY CTA ===== */
        .sticky-cta {
            position: fixed; bottom: 0; left: 0; width: 100%; z-index: 700;
            background: var(--primary); padding: 14px 24px; text-align: center;
        }
        .sticky-cta a { color: var(--white); text-decoration: none; font-weight: 700; font-size: 15px; }
        @media (min-width: 769px) { .sticky-cta { display: none; } }

        /* ===== IMAGE MODAL ===== */
        .image-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.88); z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 24px; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .image-modal.active { opacity: 1; pointer-events: auto; }
        .image-modal img { max-width: 90vw; max-height: 88vh; border-radius: 12px; object-fit: contain; }
        .modal-close { position: absolute; top: 20px; right: 24px; background: rgba(255,255,255,0.15); border: none; color: var(--white); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
        .modal-close:hover { background: rgba(255,255,255,0.3); }

        /* ===== REVEAL ===== */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s cubic-bezier(0.4,0,0.2,1), transform 0.6s cubic-bezier(0.4,0,0.2,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.12s; }
        .reveal-delay-2 { transition-delay: 0.24s; }

        /* ===== PROOF CARD ===== */
        .proof-card { max-width: 800px; margin: 0 auto; }
        .proof-card img { width: 100%; border-radius: 16px; box-shadow: 0 12px 32px rgba(0,0,0,0.1); cursor: zoom-in; }
        .image-zoom-wrapper { position: relative; }
        .zoom-hint {
            position: absolute; bottom: 14px; right: 14px;
            background: rgba(0,0,0,0.6); color: var(--white);
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;
        }

        /* ===== ORDER FORM ===== */
        .order-form-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 24px; padding: 40px; max-width: 680px; margin: 0 auto; box-shadow: 0 8px 40px rgba(0,0,0,0.06); }
        .of-pkg-selector { display: flex; gap: 10px; margin-bottom: 28px; }
        .of-pkg-option { flex: 1; position: relative; border: 2px solid var(--gray-200); border-radius: 14px; cursor: pointer; transition: all 0.25s cubic-bezier(0.4,0,0.2,1); }
        .of-pkg-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .of-pkg-option label { display: block; padding: 14px 10px; cursor: pointer; text-align: center; }
        .of-pkg-option.selected { border-color: var(--primary); background: var(--primary-light); transform: translateY(-2px); }
        .of-pkg-option:hover:not(.selected) { border-color: var(--primary); }
        .of-pkg-badge { display: block; font-size: 10px; font-weight: 700; color: var(--accent-dark); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .of-pkg-name { display: block; font-size: 16px; font-weight: 800; color: var(--black); }
        .of-pkg-detail { display: block; font-size: 13px; color: var(--gray-500); margin-top: 2px; }
        .of-fields { display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; }
        .of-field label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-600); margin-bottom: 6px; }
        .of-req { color: #DC2626; }
        .of-optional { color: var(--gray-500); font-weight: 400; font-size: 12px; }
        .of-field input { width: 100%; padding: 13px 16px; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 15px; color: var(--black); outline: none; transition: border-color 0.2s; background: var(--white); font-family: inherit; }
        .of-field input:focus { border-color: var(--primary); }
        .of-field input.error { border-color: #DC2626; }
        .of-submit-btn { width: 100%; padding: 16px 24px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: var(--white); font-size: 16px; font-weight: 700; border: none; border-radius: 14px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); letter-spacing: 0.3px; box-shadow: 0 4px 16px rgba(30,64,175,0.35); margin-bottom: 16px; animation: submitPulse 2s ease-in-out infinite; }
        .of-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(30,64,175,0.45); animation: none; }
        .of-submit-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; animation: none; }
        @keyframes submitPulse { 0%, 100% { box-shadow: 0 4px 16px rgba(30,64,175,0.35); } 50% { box-shadow: 0 4px 24px rgba(30,64,175,0.55); } }
        .of-trust { display: flex; align-items: center; justify-content: center; gap: 20px; font-size: 12px; color: var(--gray-500); flex-wrap: wrap; }
        .of-trust i { margin-right: 4px; }
        @media (max-width: 600px) { .of-pkg-selector { grid-template-columns: 1fr; } .order-form-card { padding: 24px; } }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- HEADER -->
<header class="header">
    <div class="header-inner">
        <a href="https://smart-buzzer.com/" class="logo">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
        </a>
        <nav class="header-nav">
            <a href="#pricing">Pricing</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#reviews">Reviews</a>
        </nav>
    </div>
</header>

<!-- COUNTDOWN BAR -->
<div class="countdown-bar">
    <div class="countdown-inner">
        <span class="countdown-label">&#128680; Prices go up when this timer hits zero:</span>
        <div class="countdown-timer">
            <div class="cd-block"><span class="cd-num" id="cd-h">00</span><span class="cd-unit">hrs</span></div>
            <div class="cd-block"><span class="cd-num" id="cd-m">00</span><span class="cd-unit">min</span></div>
            <div class="cd-block"><span class="cd-num" id="cd-s">00</span><span class="cd-unit">sec</span></div>
        </div>
        <span style="color:rgba(255,255,255,0.85);font-size:13px;font-weight:600;">Save up to $195 today</span>
    </div>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-badge">
                <i class="fa-solid fa-tag"></i>
                Mid-Year Sale &#8212; Limited Time Only
            </div>
            <div class="hero-image-mobile">
                <img src="https://reputationmanage.co/wp-content/uploads/2025/06/get-more-google-reviews-for-my-business.png" alt="Get More Google Reviews">
            </div>
            <h1>More Google Reviews.<br><em>More Customers.</em></h1>
            <p class="hero-subtitle">Real reviews, posted gradually to boost your rankings and bring in more calls — without disrupting your existing profile.</p>
            <div class="hero-actions">
                <a href="#pricing" class="btn-primary">
                    <i class="fa-solid fa-bolt"></i> See Sale Prices
                </a>
            </div>
            <div class="hero-trust">
                <div class="hero-trust-item"><i class="fa-solid fa-circle-check"></i> 2,000+ campaigns completed</div>
                <div class="hero-trust-item"><i class="fa-solid fa-star"></i> 4.9/5 avg rating</div>
                <div class="hero-trust-item"><i class="fa-solid fa-shield-halved"></i> Gradual delivery, built to stick</div>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://reputationmanage.co/wp-content/uploads/2025/06/get-more-google-reviews-for-my-business.png" alt="Get More Google Reviews" data-preview="true">
            <div class="hero-stat-badge top">
                <div>
                    <div class="hero-stat-num">2,000+</div>
                    <div class="hero-stat-label">Businesses Served</div>
                </div>
            </div>
            <div class="hero-stat-badge bottom">
                <div>
                    <div class="hero-stat-num">4.9&#9733;</div>
                    <div class="hero-stat-label">Avg Client Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" style="background: var(--gray-50);" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Simple Process</div>
            <h2>How It <em>Works</em></h2>
            <p>Get more Google reviews in 3 easy steps</p>
        </div>
        <div class="steps-grid">
            <div class="step-card reveal">
                <div class="step-num">1</div>
                <h3>Choose Your Package</h3>
                <p>Pick the package that fits your goals. All come with our Mid-Year Sale discount.</p>
            </div>
            <div class="step-card reveal reveal-delay-1">
                <div class="step-num">2</div>
                <h3>Submit Your Business</h3>
                <p>Complete your order and provide your Google Business link. Takes under 2 minutes.</p>
            </div>
            <div class="step-card reveal reveal-delay-2">
                <div class="step-num">3</div>
                <h3>Reviews Go Live</h3>
                <p>Reviews are posted gradually (1-3/week) with custom content. Track every single one.</p>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section" id="pricing">
    <div class="container">
        <div class="section-header">
            <div class="section-tag section-tag-amber">&#9889; Mid-Year Sale</div>
            <h2>Choose Your <em>Package</em></h2>
            <p>Mid-Year Sale pricing &#8212; biggest discounts of the year</p>
        </div>
        <div class="pricing-grid">
            <!-- STARTER -->
            <div class="pricing-card reveal">
                <div class="pricing-name">Starter</div>
                <div class="pricing-qty-block">
                    <span class="pricing-qty-num">50</span>
                    <span class="pricing-qty-label">Google Reviews</span>
                </div>
                <div class="pricing-original">$325.00</div>
                <div class="pricing-current">$300</div>
                <div class="pricing-savings-block">
                    <span class="savings-amount">Save $25</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-pct">8% OFF</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-per">$6.00/review</span>
                </div>
                <div class="pricing-guarantee"><span class="badge-guarantee">7-Day Guarantee</span></div>
                <ul class="pricing-features">
                    <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                    <li><span class="pf-check">&#10003;</span> 70% Local + 30% Global Names</li>
                    <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                    <li><span class="pf-check">&#10003;</span> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                </ul>
                <a href="#order-form" class="pricing-btn" data-package="midyear_starter">Order Now &#8212; Save $25</a>
            </div>
            <!-- GROWTH (FEATURED) -->
            <div class="pricing-card featured reveal reveal-delay-1">
                <div class="pricing-popular">&#9889; Best Value</div>
                <div class="pricing-name">Growth</div>
                <div class="pricing-qty-block">
                    <span class="pricing-qty-num">100</span>
                    <span class="pricing-qty-label">Google Reviews</span>
                </div>
                <div class="pricing-original">$650.00</div>
                <div class="pricing-current">$550</div>
                <div class="pricing-savings-block">
                    <span class="savings-amount">Save $100</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-pct">15% OFF</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-per">$5.50/review</span>
                </div>
                <div class="pricing-guarantee"><span class="badge-guarantee">17-Day Guarantee</span></div>
                <ul class="pricing-features">
                    <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                    <li><span class="pf-check">&#10003;</span> 70% Local + 30% Global Names</li>
                    <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                    <li><span class="pf-check">&#10003;</span> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                    <li><span class="pf-check">&#10003;</span> Priority Support</li>
                </ul>
                <a href="#order-form" class="pricing-btn" data-package="midyear_growth">Order Now &#8212; Save $100</a>
            </div>
            <!-- ELITE -->
            <div class="pricing-card reveal reveal-delay-2">
                <div class="pricing-name">Elite</div>
                <div class="pricing-qty-block">
                    <span class="pricing-qty-num">130</span>
                    <span class="pricing-qty-label">Google Reviews</span>
                </div>
                <div class="pricing-original">$845.00</div>
                <div class="pricing-current">$650</div>
                <div class="pricing-savings-block">
                    <span class="savings-amount">Save $195</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-pct">23% OFF</span>
                    <span class="savings-divider">·</span>
                    <span class="savings-per">$5.00/review</span>
                </div>
                <div class="pricing-guarantee"><span class="badge-guarantee">20-Day Guarantee</span></div>
                <ul class="pricing-features">
                    <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                    <li><span class="pf-check">&#10003;</span> 70% Local + 30% Global Names</li>
                    <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                    <li><span class="pf-check">&#10003;</span> 5-10 Reviews Submitted Daily, ~3 Stick</li>
                    <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                    <li><span class="pf-check">&#10003;</span> Priority Support</li>
                    <li><span class="pf-check">&#10003;</span> Extended 20-Day Guarantee</li>
                </ul>
                <a href="#order-form" class="pricing-btn" data-package="midyear_elite">Order Now &#8212; Save $195</a>
            </div>
        </div>
    </div>
</section>

<!-- ORDER FORM -->
<section class="section" id="order-form">
    <div class="container">
        <div class="section-header">
            <div class="section-tag section-tag-amber">&#9889; Secure Order</div>
            <h2>Complete Your <em>Order</em></h2>
            <p>Fill in your details to claim your Mid-Year Sale package</p>
        </div>
        <div class="order-form-card reveal">
            <!-- Package Selector (3 horizontal buttons) -->
            <div class="pkg-picker" id="pkgPicker" style="margin-bottom:28px;">
                <div class="pkg-option" id="pkgopt-midyear_starter" data-package="midyear_starter" onclick="selectPkgCard('midyear_starter')">
                    <div class="pkg-opt-name">Starter</div>
                    <div class="pkg-opt-reviews">50 Reviews</div>
                    <div class="pkg-opt-price">$300</div>
                    <div class="pkg-opt-save" style="margin-top:6px;">Save $25</div>
                </div>
                <div class="pkg-option active" id="pkgopt-midyear_growth" data-package="midyear_growth" onclick="selectPkgCard('midyear_growth')">
                    <div class="pkg-opt-badge">&#9889; Best Value</div>
                    <div class="pkg-opt-name">Growth</div>
                    <div class="pkg-opt-reviews">100 Reviews</div>
                    <div class="pkg-opt-price">$550</div>
                    <div class="pkg-opt-save" style="margin-top:6px;">Save $100</div>
                </div>
                <div class="pkg-option" id="pkgopt-midyear_elite" data-package="midyear_elite" onclick="selectPkgCard('midyear_elite')">
                    <div class="pkg-opt-name">Elite</div>
                    <div class="pkg-opt-reviews">130 Reviews</div>
                    <div class="pkg-opt-price">$650</div>
                    <div class="pkg-opt-save" style="margin-top:6px;">Save $195</div>
                </div>
            </div>
            <!-- Hidden radio inputs for form compat -->
            <div style="display:none;">
                <input type="radio" name="of-pkg" id="pkg-starter" value="midyear_starter">
                <input type="radio" name="of-pkg" id="pkg-growth" value="midyear_growth" checked>
                <input type="radio" name="of-pkg" id="pkg-elite" value="midyear_elite">
            </div>
            <div class="of-fields">
                <div class="of-field">
                    <label for="ofBizName">Google Business Name <span class="of-req">*</span></label>
                    <input type="text" id="ofBizName" placeholder="Enter your business name" autocomplete="organization">
                </div>
                <div class="of-field">
                    <label for="ofWhatsapp">WhatsApp Number <span class="of-req">*</span></label>
                    <input type="tel" id="ofWhatsapp" placeholder="+1 Enter your WhatsApp number" autocomplete="tel">
                </div>
                <div class="of-field">
                    <label for="ofEmail">Email Address <span class="of-optional">(optional)</span></label>
                    <input type="email" id="ofEmail" placeholder="Enter your email address (optional)" autocomplete="email">
                </div>
            </div>
            <button class="of-submit-btn" id="ofSubmitBtn" onclick="submitOrder()">
                COMPLETE ORDER <i class="fa-solid fa-arrow-right"></i>
            </button>
            <div class="of-trust">
                <span><i class="fa-solid fa-lock" style="color:var(--green);"></i> Secure &amp; Private</span>
                <span><i class="fa-solid fa-shield-halved" style="color:var(--primary);"></i> SSL Protected</span>
                <span><i class="fa-solid fa-credit-card" style="color:var(--gray-500);"></i> Cards Accepted</span>
            </div>
        </div>
    </div>
</section>

<!-- REAL REVIEWS PROOF -->
<section class="section" style="background: var(--gray-50);" id="reviews">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Real Examples</div>
            <h2>Real Local <em>Reviews</em></h2>
            <p>See actual reviews we've delivered for our clients</p>
        </div>
        <div class="proof-card reveal">
            <div class="image-zoom-wrapper">
                <img src="https://smart-buzzer.com/wp-content/uploads/2025/04/slide-3.jpg" alt="Review Example" data-preview="true">
                <span class="zoom-hint">Click to zoom</span>
            </div>
        </div>
    </div>
</section>

<!-- CHOOSE YOUR SENTENCES -->
<section class="section">
    <div class="container">
        <div class="content-flex reveal">
            <div class="content-image">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Custom Review Sentences" data-preview="true">
            </div>
            <div class="content-text">
                <div class="section-tag" style="margin-bottom: 16px;">Customization</div>
                <h3>Choose Your Own Sentences</h3>
                <p>You choose the content, or let us create comprehensive variety for you. For every 55 reviews ordered, we prepare <strong>440+ unique sentences</strong> to ensure zero repetition.</p>
                <div class="content-check"><span class="content-check-icon">&#10003;</span> Human-written content tailored to your business</div>
                <div class="content-check" style="margin-top:8px;"><span class="content-check-icon">&#10003;</span> Up to 2 revision rounds included</div>
            </div>
        </div>
    </div>
</section>

<!-- WHY PEOPLE USE OUR SERVICES -->
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Why Choose Us</div>
            <h2>Why People Use <em>Our Services</em></h2>
        </div>
        <div class="content-flex reverse reveal">
            <div class="content-image">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.36.44.png" alt="Review Example" data-preview="true">
            </div>
            <div>
                <ul class="why-features">
                    <li><div class="why-check">&#10003;</div><div><strong>70% Local + 30% Global Names</strong><span>Natural mix of local and global reviewers for authenticity</span></div></li>
                    <li><div class="why-check">&#10003;</div><div><strong>Unique users, IPs, devices, and aged accounts</strong><span>Complete technical authenticity guaranteed</span></div></li>
                    <li><div class="why-check">&#10003;</div><div><strong>Tailored reviews for your business</strong><span>Custom content that matches your services</span></div></li>
                    <li><div class="why-check">&#10003;</div><div><strong>Gradual posting (5-10 daily, ~3 stick)</strong><span>Natural pacing prevents algorithm detection</span></div></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- TRACK YOUR ORDER -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Transparency</div>
            <h2>Track Your Order <em>Every Day</em></h2>
            <p>100% transparency &#8212; monitor your campaign progress in real-time</p>
        </div>
        <div class="dashboard-preview reveal">
            <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Dashboard" data-preview="true">
        </div>
    </div>
</section>


<!-- CLIENTS MARQUEE -->
<section class="section">
    <div class="container">
        <div class="section-header"><div class="section-tag">Portfolio</div><h2>Our <em>Clients</em></h2></div>
        <div class="clients-marquee-wrap reveal">
            <div class="clients-marquee">
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client"></div>
                <!-- Duplicate for seamless loop -->
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client"></div>
                <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client"></div>
            </div>
        </div>
    </div>
</section>

<!-- WITH vs WITHOUT -->
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">The Difference</div>
            <h2>With Reviews vs <em>Without</em></h2>
            <p>What happens when customers compare your business to competitors</p>
        </div>
        <div class="compare-table reveal">
            <div class="compare-row compare-header">
                <div class="compare-cell">Metric</div>
                <div class="compare-cell">&#9733; 2.5 Stars</div>
                <div class="compare-cell">&#9733; 4.8 Stars</div>
            </div>
            <div class="compare-row">
                <div class="compare-cell metric">Customer Trust</div>
                <div class="compare-cell bad">&#10007; Low &#8212; people skip you</div>
                <div class="compare-cell good">&#10003; High &#8212; people choose you</div>
            </div>
            <div class="compare-row">
                <div class="compare-cell metric">Google Maps Ranking</div>
                <div class="compare-cell bad">&#10007; Page 2-3</div>
                <div class="compare-cell good">&#10003; Top 3 results</div>
            </div>
            <div class="compare-row">
                <div class="compare-cell metric">Monthly Calls</div>
                <div class="compare-cell bad">&#10007; Below average</div>
                <div class="compare-cell good">&#10003; +45% more calls</div>
            </div>
            <div class="compare-row">
                <div class="compare-cell metric">First Impression</div>
                <div class="compare-cell bad">&#10007; Customers doubt you</div>
                <div class="compare-cell good">&#10003; Instant credibility</div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Success Stories</div>
            <h2>What Our <em>Clients Say</em></h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card reveal">
                <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="testimonial-text">"Smart Buzzer completely transformed our online presence. We went from 12 reviews to over 100 in just 3 months and our phone hasn't stopped ringing."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">J</div>
                    <div>
                        <div class="testimonial-name">James R.</div>
                        <div class="testimonial-biz">HVAC Contractor, Texas</div>
                        <div class="testimonial-result">&#8593; 89 reviews added</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-1">
                <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="testimonial-text">"The gradual posting really works. We never had any issues with Google and the reviews look completely natural. Our competitors are still wondering how we did it."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">S</div>
                    <div>
                        <div class="testimonial-name">Sarah M.</div>
                        <div class="testimonial-biz">Dental Clinic, California</div>
                        <div class="testimonial-result">&#8593; 100 reviews added</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-2">
                <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="testimonial-text">"Best investment I've made for my restaurant. Jumped from 3.2 to 4.7 stars and we're now the top result in our area. Absolutely worth every penny."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">D</div>
                    <div>
                        <div class="testimonial-name">David K.</div>
                        <div class="testimonial-biz">Restaurant Owner, Florida</div>
                        <div class="testimonial-result">&#8593; 130 reviews added</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">FAQ</div>
            <h2>Frequently Asked <em>Questions</em></h2>
        </div>
        <div class="faq-list">
            <div class="faq-item reveal"><button class="faq-question">Is this safe for my Google Business?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">Our gradual delivery system (5-10 reviews submitted daily, ~3 stick per day) uses aged Google accounts, unique IP addresses, and diverse devices — designed to look completely natural. We've completed 2,000+ campaigns with our compliant, gradual approach.</div></div></div>
            <div class="faq-item reveal"><button class="faq-question">How long until I see my reviews?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">Your first reviews appear within 24 hours of content approval. Reviews are then posted gradually at 1-3 per week to ensure natural patterns and maximum stickiness. Full campaign completion takes 1-2 months depending on the package.</div></div></div>
            <div class="faq-item reveal"><button class="faq-question">What payment methods do you accept?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">We accept Debit/Credit Card (Visa, Mastercard), Apple Pay, AfterPay (Buy Now, Pay Later), Cash App Pay, Zelle, Bank Transfer, and Crypto (Tether/ETH). Pay with Crypto and get an additional 5% discount on any package!</div></div></div>
        </div>
    </div>
</section>

<!-- REVIEW CALCULATOR -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Calculator</div>
            <h2>Find Out How Many <em>Reviews You Need</em></h2>
            <p>Calculate exactly how many reviews you need to reach your rating goal</p>
        </div>
        <div class="calc-card reveal">
            <div class="calc-grid">
                <div class="calc-field">
                    <label>Current Reviews</label>
                    <input type="number" id="calcCurrentReviews" placeholder="e.g. 15" min="0">
                </div>
                <div class="calc-field">
                    <label>Current Rating</label>
                    <input type="number" id="calcCurrentRating" placeholder="e.g. 3.5" min="1" max="5" step="0.1">
                </div>
                <div class="calc-field">
                    <label>Desired Rating</label>
                    <input type="number" id="calcDesiredRating" placeholder="e.g. 4.5" min="1" max="5" step="0.1">
                </div>
            </div>
            <button class="calc-btn" onclick="calculateReviews()">Calculate Now</button>
            <div class="calc-result" id="calcData">
                <div class="calc-result-grid">
                    <div class="calc-result-card">
                        <div class="calc-big-num" id="calcNeeded">0</div>
                        <div class="calc-big-label">reviews needed</div>
                    </div>
                    <div class="calc-result-card">
                        <div style="font-size:14px;color:var(--gray-500);margin-bottom:6px;">From <span id="calcFrom">-</span> &#8594; <span id="calcTo">-</span> stars</div>
                        <div class="calc-rec" id="calcRec">
                            <div class="calc-rec-tag" id="calcRecTag">Recommended Package</div>
                            <div class="calc-rec-name" id="calcRecName">Growth</div>
                            <div class="calc-rec-info" id="calcRecInfo">100 Reviews &#8212; $550</div>
                            <div class="calc-rec-save" id="calcRecSave">Save $100 (15% OFF)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="calcPlaceholder" style="text-align:center;padding:20px 0;color:var(--gray-500);font-size:14px;">
                Fill in your details above and click Calculate
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                <p>Specialized in social media engagement, product reviews, and online reputation services.<em>A subsidiary of Pintarnya.</em></p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <a href="https://smart-buzzer.com/tracker" target="_blank">Track Campaign</a>
                <a href="https://smart-buzzer.com/report" target="_blank">Report Issue</a>
                <a href="https://smart-buzzer.com/service-tnc" target="_blank">Terms &amp; Conditions</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <a href="https://wa.me/<?php echo $SB_WA_NUMBER; ?>" target="_blank">&#128222; <?php echo $SB_WA_DISPLAY; ?></a>
                <a href="mailto:contact@smart-buzzer.com">contact@smart-buzzer.com</a>
            </div>
        </div>
        <div class="footer-bottom">&copy; 2026 Smart Buzzer. All rights reserved.</div>
    </div>
</footer>

<!-- WHATSAPP FLOAT -->
<a href="https://wa.me/<?php echo $SB_WA_NUMBER; ?>?text=Hi%20Smart%20Buzzer%2C%20I'm%20interested%20in%20the%20Mid-Year%20Sale%20package." target="_blank" class="wa-float-btn" aria-label="WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- SOCIAL PROOF POPUP -->
<div class="social-proof" id="socialProof">
    <div class="sp-icon"><i class="fa-solid fa-circle-check"></i></div>
    <div>
        <div class="sp-text" id="spText"><strong>Mike from Houston</strong> just ordered 100 reviews</div>
        <div class="sp-time" id="spTime">2 minutes ago</div>
    </div>
    <button class="sp-close" id="spClose" aria-label="Close">&#10005;</button>
</div>

<!-- DESKTOP STICKY BAR -->
<div class="desktop-sticky" id="desktopSticky">
    <div class="desktop-sticky-inner">
        <div class="desktop-sticky-text">&#9889; Mid-Year Sale &#8212; <span>Save up to $195</span> on Google Reviews</div>
        <div class="desktop-sticky-btns">
            <a href="#pricing" class="btn-amber">See Packages</a>
            <a href="https://wa.me/<?php echo $SB_WA_NUMBER; ?>?text=Hi%20Smart%20Buzzer%2C%20I'm%20interested%20in%20the%20Mid-Year%20Sale%20package." target="_blank" class="btn-wa-sm">WhatsApp Us</a>
        </div>
    </div>
</div>

<!-- MOBILE STICKY CTA -->
<div class="sticky-cta">
    <a href="#pricing">&#9889; See Mid-Year Sale Packages</a>
</div>

<!-- IMAGE MODAL -->
<div class="image-modal" id="imageModal">
    <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    <img id="modalImage" src="" alt="Preview">
</div>

<script>
    window.dataLayer = window.dataLayer || [];

    // ===== ANALYTICS =====
    function trackEvent(t, d) {
        fetch('analytics.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({event_type: t, page_url: window.location.href, data: JSON.stringify(d || {}), session_id: sessionId})
        }).catch(function() {});
    }

    trackEvent('PAGE_VIEW');

    var scrollD = {25: false, 50: false, 75: false, 100: false};
    window.addEventListener('scroll', function() {
        var p = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
        [25, 50, 75, 100].forEach(function(d) {
            if (p >= d && !scrollD[d]) { scrollD[d] = true; trackEvent('SCROLL_DEPTH_' + d); }
        });
    });

    var t0 = Date.now();
    window.addEventListener('beforeunload', function() {
        trackEvent('TIME_ON_PAGE', {seconds: Math.round((Date.now() - t0) / 1000)});
    });

    document.addEventListener('click', function(ev) {
        var el = ev.target.closest('a,button,[data-package],[data-preview]');
        if (el) {
            trackEvent('CLICK_HEATMAP', {x: ev.clientX, y: ev.clientY, tag: el.tagName, id: el.id || '', cls: (el.className || '').toString().substring(0, 60), text: (el.textContent || '').replace(/\s+/g, ' ').trim().substring(0, 50)});
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            var exitHref = '';
            try { if (document.activeElement && document.activeElement.href) exitHref = document.activeElement.href; } catch(x) {}
            trackEvent('EXIT_PAGE', {seconds: Math.round((Date.now() - t0) / 1000), exitUrl: exitHref});
        }
    });

    (function() {
        var ck = 'sb_rv_my';
        var found = document.cookie.split(';').some(function(c) { return c.trim().indexOf(ck + '=') === 0; });
        if (found) { trackEvent('RETURN_VISITOR'); }
        document.cookie = ck + '=1;path=/;max-age=31536000;SameSite=Lax';
    })();

    document.querySelectorAll('a[href]').forEach(function(lnk) {
        lnk.addEventListener('click', function() {
            if (lnk.hostname && lnk.hostname !== window.location.hostname) {
                trackEvent('EXTERNAL_LINK_CLICK', {url: lnk.href, text: (lnk.textContent || '').replace(/\s+/g, ' ').trim().substring(0, 50)});
            }
        });
    });

    // ===== PACKAGE METADATA =====
    var sbPkgMeta = {
        'midyear_starter': {id: 'pkg_midyear_starter_50',  name: 'Buy Google Reviews - 50 Local (Mid-Year Sale)',  item_category: 'Google Reviews', price: 300.00, reviews: 50},
        'midyear_growth':  {id: 'pkg_midyear_growth_100',  name: 'Buy Google Reviews - 100 Local (Mid-Year Sale)', item_category: 'Google Reviews', price: 550.00, reviews: 100},
        'midyear_elite':   {id: 'pkg_midyear_elite_130',   name: 'Buy Google Reviews - 130 Local (Mid-Year Sale)', item_category: 'Google Reviews', price: 650.00, reviews: 130}
    };

    // Track last selected package for WhatsApp / floating CTA tracking
    var sbLastSelectedPkg = null;
    var sbFanbasisUrls = {
        'midyear_starter': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/BNA7X',
        'midyear_growth':  'https://www.fanbasis.com/agency-checkout/smartbuzzer/D1D7x',
        'midyear_elite':   'https://www.fanbasis.com/agency-checkout/smartbuzzer/JyMj2'
    };

    // ===== VIEW ITEM — pricing section visible =====
    var sbViewItemFired = false;
    var sbPricingObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting && !sbViewItemFired) {
                sbViewItemFired = true;
                var defaultMeta = sbPkgMeta['midyear_growth'];
                var allItems = [];
                var totalValue = 0;
                Object.keys(sbPkgMeta).forEach(function(k) {
                    var m = sbPkgMeta[k];
                    allItems.push({item_id: m.id, item_name: m.name, item_category: 'Google Reviews', price: m.price, quantity: 1});
                    totalValue += m.price;
                });
                window.dataLayer.push({ecommerce: null});
                window.dataLayer.push({
                    event: 'view_item',
                    ecommerce: {
                        currency: 'USD', value: defaultMeta.price,
                        items: allItems
                    }
                });
                trackEvent('VIEW_ITEM', {package: 'all', price: defaultMeta.price});
            }
        });
    }, {threshold: 0.3});
    var sbPricingEl = document.getElementById('pricing');
    if (sbPricingEl) sbPricingObserver.observe(sbPricingEl);

    // ===== GENERATE LEAD — WhatsApp clicks =====
    document.querySelectorAll('a[href*="wa.me"]').forEach(function(el) {
        el.addEventListener('click', function() {
            // Use last selected package if available; otherwise value 0 (user hasn't picked yet)
            var waMeta = (typeof sbLastSelectedPkg !== 'undefined' && sbLastSelectedPkg) ? sbPkgMeta[sbLastSelectedPkg] : null;
            var waValue = waMeta ? waMeta.price : 0;
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

    // ===== PACKAGE CARD SELECTOR (pricing section) =====
    function selectPkgCard(pkg) {
        document.querySelectorAll('.pkg-option').forEach(function(el) {
            el.classList.toggle('active', el.getAttribute('data-package') === pkg);
        });
        selectPkg(pkg); // also sync the order form selector
    }

    // ===== ORDER NOW — pricing card CTA =====
    function handlePkgCTA() {
        var pkg = selectedPkg;
        var meta = sbPkgMeta[pkg];
        if (!meta) return;
        // Fire Lead pixel + TikTok SubmitForm (use readable meta.name, not key)
        if (typeof fbq !== 'undefined') { fbq('track', 'Lead', {content_name: meta.name}); }
        if (typeof ttq !== 'undefined') { ttq.track('SubmitForm', {content_name: meta.name}); }
        // GTM add_to_cart (updates ecommerce for GTM variables)
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            event: 'add_to_cart',
            ecommerce: {
                currency: 'USD', value: meta.price,
                items: [{item_id: meta.id, item_name: meta.name, item_category: 'Google Reviews', price: meta.price, quantity: 1}]
            }
        });
        // GTM generate_lead
        window.dataLayer.push({event: 'generate_lead', method: 'pricing_click', value: meta.price, currency: 'USD'});
        // Analytics
        trackEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {package: pkg, price: meta.price});
        // Scroll to form
        var formEl = document.getElementById('order-form');
        if (formEl) formEl.scrollIntoView({behavior: 'smooth', block: 'start'});
    }

    // Legacy data-package buttons (kept for backward compat)
    document.querySelectorAll('[data-package]').forEach(function(btn) {
        if (btn.classList.contains('pkg-option') || btn.classList.contains('pkg-cta-btn')) return;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var pkg = this.getAttribute('data-package');
            var meta = sbPkgMeta[pkg];
            if (!meta) return;
            if (typeof fbq !== 'undefined') { fbq('track', 'Lead', {content_name: meta.name}); }
            if (typeof ttq !== 'undefined') { ttq.track('SubmitForm', {content_name: meta.name}); }
            // GTM add_to_cart (updates ecommerce for GTM variables)
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'add_to_cart',
                ecommerce: {
                    currency: 'USD', value: meta.price,
                    items: [{item_id: meta.id, item_name: meta.name, item_category: 'Google Reviews', price: meta.price, quantity: 1}]
                }
            });
            window.dataLayer.push({event: 'generate_lead', method: 'pricing_click', value: meta.price, currency: 'USD'});
            trackEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {package: pkg, price: meta.price});
            selectPkg(pkg);
            selectPkgCard(pkg);
            var formEl = document.getElementById('order-form');
            if (formEl) formEl.scrollIntoView({behavior: 'smooth', block: 'start'});
        });
    });

    // ===== PACKAGE SELECTOR =====
    var selectedPkg = 'midyear_growth';
    function selectPkg(pkg) {
        selectedPkg = pkg;
        sbLastSelectedPkg = pkg;
        // Sync hidden radios
        var map = {starter: 'midyear_starter', growth: 'midyear_growth', elite: 'midyear_elite'};
        ['starter', 'growth', 'elite'].forEach(function(k) {
            var radio = document.getElementById('pkg-' + k);
            if (radio) radio.checked = (map[k] === pkg);
        });
        // Sync pkg-option cards (pricing section + form selector)
        document.querySelectorAll('.pkg-option').forEach(function(el) {
            el.classList.toggle('active', el.getAttribute('data-package') === pkg);
        });
        try { localStorage.setItem('sb_form_pkg', pkg); } catch(e) {}
    }

    // ===== FORM AUTO-SAVE & RESTORE =====
    (function restoreForm() {
        try {
            var biz = localStorage.getItem('sb_form_biz');
            var wa  = localStorage.getItem('sb_form_wa');
            var em  = localStorage.getItem('sb_form_email');
            var pkg = localStorage.getItem('sb_form_pkg');
            if (biz) document.getElementById('ofBizName').value = biz;
            if (wa)  document.getElementById('ofWhatsapp').value = wa;
            if (em)  document.getElementById('ofEmail').value = em;
            if (pkg && sbPkgMeta[pkg]) selectPkgCard(pkg);
        } catch(e) {}
    })();
    document.getElementById('ofBizName').addEventListener('input', function() { try { localStorage.setItem('sb_form_biz', this.value); } catch(e) {} });
    document.getElementById('ofWhatsapp').addEventListener('input', function() { try { localStorage.setItem('sb_form_wa', this.value); } catch(e) {} });
    document.getElementById('ofEmail').addEventListener('input', function() { try { localStorage.setItem('sb_form_email', this.value); } catch(e) {} });

    // ===== SUBMIT ORDER =====
    var ofSubmitting = false;
    function submitOrder() {
        if (ofSubmitting) return;
        var biz  = (document.getElementById('ofBizName').value || '').trim();
        var wa   = (document.getElementById('ofWhatsapp').value || '').trim();
        var em   = (document.getElementById('ofEmail').value || '').trim();
        var pkg  = selectedPkg;
        var meta = sbPkgMeta[pkg];
        if (!meta) return;

        // Validate required fields
        var bizEl = document.getElementById('ofBizName');
        var waEl  = document.getElementById('ofWhatsapp');
        bizEl.classList.remove('error');
        waEl.classList.remove('error');
        if (!biz) { bizEl.classList.add('error'); bizEl.focus(); return; }
        if (!wa)  { waEl.classList.add('error');  waEl.focus();  return; }

        ofSubmitting = true;
        var btn = document.getElementById('ofSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = 'Processing... <i class="fa-solid fa-spinner fa-spin"></i>';

        // Parse name for user_data
        var nameParts = biz.split(' ');
        var firstName = nameParts[0] || '';
        var lastName  = nameParts.slice(1).join(' ') || '';
        var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

        // GTM begin_checkout with real user_data
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            event: 'begin_checkout',
            user_data: {email: em, phone_number: wa, first_name: firstName, last_name: lastName},
            ecommerce: {
                currency: 'USD', value: meta.price,
                items: [{item_id: meta.id, item_name: meta.name, item_category: 'Google Reviews', price: meta.price, quantity: 1}]
            }
        });
        // GTM add_payment_info
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            event: 'add_payment_info',
            ecommerce: {
                currency: 'USD', value: meta.price, payment_type: 'Credit Card',
                items: [{item_id: meta.id, item_name: meta.name, item_category: 'Google Reviews', price: meta.price, quantity: 1}]
            }
        });
        // FB InitiateCheckout
        if (typeof fbq !== 'undefined') {
            fbq('track', 'InitiateCheckout', {value: meta.price, currency: 'USD', content_name: meta.name, content_type: 'product', content_ids: [pkg]});
        }
        // TikTok InitiateCheckout
        if (typeof ttq !== 'undefined') {
            ttq.track('InitiateCheckout', {value: meta.price, currency: 'USD', content_name: meta.name});
        }
        // LocalStorage bridge for purchase event on thankyou.php
        try {
            localStorage.setItem('sb_user_email', em);
            localStorage.setItem('sb_user_phone', wa);
            localStorage.setItem('sb_user_fname', firstName);
            localStorage.setItem('sb_user_lname', lastName);
            localStorage.setItem('sb_txn_id', txnId);
            localStorage.setItem('sb_pkg', pkg);
            // Clear form auto-save
            localStorage.removeItem('sb_form_biz');
            localStorage.removeItem('sb_form_wa');
            localStorage.removeItem('sb_form_email');
            localStorage.removeItem('sb_form_pkg');
        } catch(e) {}

        // Analytics
        trackEvent('ORDER_SUBMIT', {package: pkg, price: meta.price, biz: biz, wa: wa});

        // Log customer data to customer_data.log
        // Send pkg KEY (e.g., 'midyear_starter') — PHP logger maps it to clean name ('Starter')
        fetch('index.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'log_customer', biz: biz, email: em || '-', wa: wa, pkg: pkg, reviews: meta.reviews, page_url: window.location.href})
        }).catch(function() {});

        // Redirect directly to Fanbasis payment gateway
        // Fanbasis will redirect back to thankyou.php after payment
        var dest = sbFanbasisUrls[pkg];
        setTimeout(function() { window.location.href = dest; }, 300);
    }

    // ===== COUNTDOWN (daily reset) =====
    function updateCD() {
        var n = new Date(), m = new Date();
        m.setHours(24, 0, 0, 0);
        var d = m - n;
        document.getElementById('cd-h').textContent = Math.floor(d / 3600000).toString().padStart(2, '0');
        document.getElementById('cd-m').textContent = Math.floor((d % 3600000) / 60000).toString().padStart(2, '0');
        document.getElementById('cd-s').textContent = Math.floor((d % 60000) / 1000).toString().padStart(2, '0');
    }
    updateCD(); setInterval(updateCD, 1000);

    // ===== REVEAL ON SCROLL =====
    var obs = new IntersectionObserver(function(e) {
        e.forEach(function(en) { if (en.isIntersecting) en.target.classList.add('visible'); });
    }, {threshold: 0.1});
    document.querySelectorAll('.reveal').forEach(function(el) { obs.observe(el); });

    // ===== NUMBER COUNTER =====
    var counterObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                if (el.dataset.counted) return;
                el.dataset.counted = 'true';
                if (el.dataset.count !== undefined) animateCount(el, 0, parseInt(el.dataset.count), 1500, '+');
                else if (el.dataset.countDecimal !== undefined) animateCountDec(el, 0, parseFloat(el.dataset.countDecimal), 1500);
                else if (el.dataset.countPercent !== undefined) animateCount(el, 0, parseInt(el.dataset.countPercent), 1200, '%');
            }
        });
    }, {threshold: 0.5});
    document.querySelectorAll('[data-count],[data-count-decimal],[data-count-percent]').forEach(function(el) { counterObs.observe(el); });
    function animateCount(el, s, e, dur, suf) {
        var st = null;
        function step(ts) { if (!st) st = ts; var p = Math.min((ts - st) / dur, 1); var eased = 1 - Math.pow(1 - p, 3); el.textContent = Math.floor(eased * (e - s) + s).toLocaleString() + (suf || ''); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
    }
    function animateCountDec(el, s, e, dur) {
        var st = null;
        function step(ts) { if (!st) st = ts; var p = Math.min((ts - st) / dur, 1); var eased = 1 - Math.pow(1 - p, 3); el.textContent = (eased * (e - s) + s).toFixed(1); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
    }

    // ===== FAQ =====
    document.querySelectorAll('.faq-question').forEach(function(b) {
        b.addEventListener('click', function() {
            var item = this.parentElement, active = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(function(f) { f.classList.remove('active'); });
            if (!active) item.classList.add('active');
        });
    });

    // ===== IMAGE MODAL =====
    var modal = document.getElementById('imageModal'), mImg = document.getElementById('modalImage');
    document.querySelectorAll('[data-preview="true"]').forEach(function(i) {
        i.addEventListener('click', function() { mImg.src = this.src; modal.classList.add('active'); document.body.style.overflow = 'hidden'; });
    });
    function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }
    document.getElementById('modalClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

    // ===== SMOOTH SCROLL =====
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var t = document.querySelector(this.getAttribute('href'));
            if (t) t.scrollIntoView({behavior: 'smooth', block: 'start'});
        });
    });

    // ===== DESKTOP STICKY BAR =====
    var desktopSticky = document.getElementById('desktopSticky');
    window.addEventListener('scroll', function() {
        if (window.innerWidth > 768) desktopSticky.classList.toggle('show', window.scrollY > 700);
    });

    // ===== SOCIAL PROOF POPUP =====
    var spData = [
        {name: 'Mike from Houston',     action: 'ordered 100 reviews', time: '2 minutes ago'},
        {name: 'Sarah from Miami',      action: 'ordered 50 reviews',  time: '5 minutes ago'},
        {name: 'David from Phoenix',    action: 'ordered 130 reviews', time: '8 minutes ago'},
        {name: 'Lisa from Denver',      action: 'ordered 100 reviews', time: '12 minutes ago'},
        {name: 'Robert from Austin',    action: 'ordered 50 reviews',  time: '15 minutes ago'},
        {name: 'Jennifer from Chicago', action: 'ordered 130 reviews', time: '18 minutes ago'},
        {name: 'Chris from Seattle',    action: 'ordered 100 reviews', time: '22 minutes ago'},
        {name: 'Amanda from Portland',  action: 'ordered 50 reviews',  time: '25 minutes ago'},
    ];
    var spEl = document.getElementById('socialProof'), spIdx = 0, spDismissed = false;
    function showSP() {
        if (spDismissed) return;
        var d = spData[spIdx % spData.length];
        document.getElementById('spText').innerHTML = '<strong>' + d.name + '</strong> just ' + d.action;
        document.getElementById('spTime').textContent = d.time;
        spEl.classList.add('show');
        setTimeout(function() { spEl.classList.remove('show'); spIdx++; }, 5000);
    }
    setTimeout(showSP, 8000); setInterval(function() { if (!spDismissed) showSP(); }, 25000);
    document.getElementById('spClose').addEventListener('click', function() { spEl.classList.remove('show'); spDismissed = true; });

    // ===== REVIEW CALCULATOR =====
    function calculateReviews() {
        var curRev = parseFloat(document.getElementById('calcCurrentReviews').value);
        var curRat = parseFloat(document.getElementById('calcCurrentRating').value);
        var desRat = parseFloat(document.getElementById('calcDesiredRating').value);
        if (isNaN(curRev) || isNaN(curRat) || isNaN(desRat)) { alert('Please fill in all fields.'); return; }
        if (curRev < 0) { alert('Number of reviews cannot be negative.'); return; }
        if (curRat < 1 || curRat > 5) { alert('Current rating must be between 1.0 and 5.0.'); return; }
        if (desRat < 1 || desRat > 5) { alert('Desired rating must be between 1.0 and 5.0.'); return; }
        if (desRat <= curRat) { alert('Desired rating must be higher than your current rating.'); return; }
        var avgNew = 4.8;
        if (desRat >= avgNew) { alert('Maximum achievable rating is approximately 4.8 stars.'); return; }
        var needed = Math.ceil(curRev * (desRat - curRat) / (avgNew - desRat));
        if (needed < 1) needed = 1;
        document.getElementById('calcPlaceholder').style.display = 'none';
        document.getElementById('calcData').style.display = 'block';
        var numEl = document.getElementById('calcNeeded');
        var st = null;
        function step(ts) { if (!st) st = ts; var p = Math.min((ts - st) / 800, 1); numEl.textContent = Math.floor((1 - Math.pow(1 - p, 3)) * needed); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
        document.getElementById('calcFrom').textContent = curRat.toFixed(1);
        document.getElementById('calcTo').textContent = desRat.toFixed(1);
        var recName, recInfo, recSave, recTag = 'Recommended Package';
        if (needed <= 50) {
            recName = 'Starter'; recInfo = '50 Reviews \u2014 $300'; recSave = 'Save $25 (8% OFF)';
        } else if (needed <= 100) {
            recName = 'Growth'; recInfo = '100 Reviews \u2014 $550'; recSave = 'Save $100 (15% OFF)';
        } else {
            recName = 'Elite'; recInfo = '130 Reviews \u2014 $650'; recSave = 'Save $195 (23% OFF)';
        }
        if (needed > 130) { recTag = 'Best Value Package'; recSave = 'Contact us for 150+ reviews (volume discounts)'; }
        document.getElementById('calcRecTag').textContent = recTag;
        document.getElementById('calcRecName').textContent = recName;
        document.getElementById('calcRecInfo').textContent = recInfo;
        document.getElementById('calcRecSave').textContent = recSave;
        trackEvent('CALCULATOR_USED', {currentReviews: curRev, currentRating: curRat, desiredRating: desRat, reviewsNeeded: needed, recommendedPackage: recName});
    }
</script>
</body>
</html>
