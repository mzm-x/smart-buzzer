<?php
// Smart Buzzer - Unified Thank You Page v4.0
// Handles ALL landing pages: /promo/, /promo-b1g1/, etc.
// Deploy to: https://smart-buzzer.com/thankyou.php
// Fires Purchase pixel + dataLayer push on page load
// Supports Fanbasis callback params AND legacy params

// ===== ALL PACKAGES FROM ALL LANDING PAGES =====
$packages = [
    // LP Promo packages
    'starter'     => ['name' => 'Promo Starter',     'reviews' => '55',  'price' => '360.00', 'display' => '$360', 'item_id' => 'pkg_starter_55',     'item_name' => 'Buy Google Reviews - 55 Local',       'source_lp' => 'promo'],
    'growth'      => ['name' => 'Promo Growth',      'reviews' => '88',  'price' => '550.00', 'display' => '$550', 'item_id' => 'pkg_growth_88',      'item_name' => 'Buy Google Reviews - 88 Local',       'source_lp' => 'promo'],
    'performance' => ['name' => 'Promo Performance', 'reviews' => '110', 'price' => '660.00', 'display' => '$660', 'item_id' => 'pkg_performance_110', 'item_name' => 'Buy Google Reviews - 110 Local',      'source_lp' => 'promo'],
    // LP B1G1 V2 packages
    'booster'     => ['name' => 'B1G1 Booster',      'reviews' => '65',  'price' => '380.00', 'display' => '$380', 'item_id' => 'pkg_booster_65',     'item_name' => 'Buy Google Reviews - 65 Local (B1G1)',  'source_lp' => 'promo-b1g1'],
    'dominator'   => ['name' => 'B1G1 Dominator',    'reviews' => '120', 'price' => '680.00', 'display' => '$680', 'item_id' => 'pkg_dominator_120',  'item_name' => 'Buy Google Reviews - 120 Local (B1G1)', 'source_lp' => 'promo-b1g1'],
    // LP Seasonal (Valentine) packages
    'cupid'        => ['name' => 'Valentine Cupid',        'reviews' => '50',  'price' => '300.00', 'display' => '$300', 'item_id' => 'pkg_cupid_50',        'item_name' => 'Buy Google Reviews - 50 Local (Valentine)',  'source_lp' => 'seasonal'],
    'sweetheart'   => ['name' => 'Valentine Sweetheart',   'reviews' => '100', 'price' => '550.00', 'display' => '$550', 'item_id' => 'pkg_sweetheart_100',   'item_name' => 'Buy Google Reviews - 100 Local (Valentine)', 'source_lp' => 'seasonal'],
    'heartbreaker' => ['name' => 'Valentine Heartbreaker', 'reviews' => '130', 'price' => '650.00', 'display' => '$650', 'item_id' => 'pkg_heartbreaker_130', 'item_name' => 'Buy Google Reviews - 130 Local (Valentine)', 'source_lp' => 'seasonal'],
    // LP Mid-Year Sale packages
    'midyear_starter' => ['name' => 'Mid-Year Starter', 'reviews' => '50',  'price' => '300.00', 'display' => '$300', 'item_id' => 'pkg_midyear_starter_50',  'item_name' => 'Buy Google Reviews - 50 Local (Mid-Year Sale)',  'source_lp' => 'midyear'],
    'midyear_growth'  => ['name' => 'Mid-Year Growth',  'reviews' => '100', 'price' => '550.00', 'display' => '$550', 'item_id' => 'pkg_midyear_growth_100',   'item_name' => 'Buy Google Reviews - 100 Local (Mid-Year Sale)', 'source_lp' => 'midyear'],
    'midyear_elite'   => ['name' => 'Mid-Year Elite',   'reviews' => '130', 'price' => '650.00', 'display' => '$650', 'item_id' => 'pkg_midyear_elite_130',    'item_name' => 'Buy Google Reviews - 130 Local (Mid-Year Sale)', 'source_lp' => 'midyear'],
    // LP California packages
    'california_starter'     => ['name' => 'California Starter',     'reviews' => '50',  'price' => '300.00', 'display' => '$300', 'item_id' => 'pkg_california_starter_50',     'item_name' => 'Buy Google Reviews - 50 Local (California)',  'source_lp' => 'promo-california'],
    'california_growth'      => ['name' => 'California Growth',      'reviews' => '100', 'price' => '550.00', 'display' => '$550', 'item_id' => 'pkg_california_growth_100',     'item_name' => 'Buy Google Reviews - 100 Local (California)', 'source_lp' => 'promo-california'],
    'california_performance' => ['name' => 'California Performance', 'reviews' => '130', 'price' => '650.00', 'display' => '$650', 'item_id' => 'pkg_california_performance_130', 'item_name' => 'Buy Google Reviews - 130 Local (California)', 'source_lp' => 'promo-california'],
    // LP Industry packages
    'industry_starter'     => ['name' => 'Industry Starter',     'reviews' => '55',  'price' => '360.00', 'display' => '$360', 'item_id' => 'pkg_industry_starter_55',      'item_name' => 'Buy Google Reviews - 55 Local (Industry)',  'source_lp' => 'promo-industry'],
    'industry_growth'      => ['name' => 'Industry Growth',      'reviews' => '88',  'price' => '550.00', 'display' => '$550', 'item_id' => 'pkg_industry_growth_88',       'item_name' => 'Buy Google Reviews - 88 Local (Industry)',  'source_lp' => 'promo-industry'],
    'industry_performance' => ['name' => 'Industry Performance', 'reviews' => '110', 'price' => '650.00', 'display' => '$650', 'item_id' => 'pkg_industry_performance_110', 'item_name' => 'Buy Google Reviews - 110 Local (Industry)', 'source_lp' => 'promo-industry'],
    // LP Tripadvisor packages
    'tripadvisor_starter'     => ['name' => 'Tripadvisor Starter',     'reviews' => '28', 'price' => '250.00', 'display' => '$250', 'item_id' => 'pkg_tripadvisor_starter_28',     'item_name' => 'Buy Tripadvisor Reviews - 28 Local', 'source_lp' => 'promo-tripadvisor'],
    'tripadvisor_growth'      => ['name' => 'Tripadvisor Growth',      'reviews' => '35', 'price' => '300.00', 'display' => '$300', 'item_id' => 'pkg_tripadvisor_growth_35',      'item_name' => 'Buy Tripadvisor Reviews - 35 Local', 'source_lp' => 'promo-tripadvisor'],
    'tripadvisor_performance' => ['name' => 'Tripadvisor Performance', 'reviews' => '50', 'price' => '400.00', 'display' => '$400', 'item_id' => 'pkg_tripadvisor_performance_50', 'item_name' => 'Buy Tripadvisor Reviews - 50 Local', 'source_lp' => 'promo-tripadvisor'],
];

// ===== FANBASIS REF CODE TO PACKAGE KEY MAPPING =====
$refMap = [
    // LP Promo
    'LP-PROMO-55'  => 'starter',
    'LP-PROMO-88'  => 'growth',
    'LP-PROMO-110' => 'performance',
    // LP B1G1 V2
    'LP-B1G1-65'   => 'booster',
    'LP-B1G1-120'  => 'dominator',
    // LP Seasonal (Valentine)
    'LP-SEASONAL-50'  => 'cupid',
    'LP-SEASONAL-100' => 'sweetheart',
    'LP-SEASONAL-130' => 'heartbreaker',
    // LP Mid-Year Sale
    'LP-MIDYEAR-50'  => 'midyear_starter',
    'LP-MIDYEAR-100' => 'midyear_growth',
    'LP-MIDYEAR-130' => 'midyear_elite',
    // LP California
    'LP-CA-50'  => 'california_starter',
    'LP-CA-100' => 'california_growth',
    'LP-CA-130' => 'california_performance',
    // LP Industry
    'LP-ID-55'  => 'industry_starter',
    'LP-ID-88'  => 'industry_growth',
    'LP-ID-110' => 'industry_performance',
    // LP Tripadvisor (current ref codes)
    'LP-TRIP-28' => 'tripadvisor_starter',
    'LP-TRIP-35' => 'tripadvisor_growth',
    'LP-TRIP-50' => 'tripadvisor_performance',
    // LP Tripadvisor (legacy ref codes — kept for backward compat)
    'LP-TRIPADVISOR-30' => 'tripadvisor_starter',
    'LP-TRIPADVISOR-40' => 'tripadvisor_growth',
    'LP-TRIPADVISOR-50' => 'tripadvisor_performance',
];

// ===== PARSE FANBASIS PARAMS =====
$fbRef         = isset($_GET['ref'])          ? htmlspecialchars($_GET['ref'], ENT_QUOTES, 'UTF-8') : '';
$fbEmail       = isset($_GET['email'])        ? htmlspecialchars(urldecode($_GET['email']), ENT_QUOTES, 'UTF-8') : '';
$fbName        = isset($_GET['name'])         ? htmlspecialchars(urldecode($_GET['name']), ENT_QUOTES, 'UTF-8') : '';
$fbPaymentId   = isset($_GET['payment_id'])   ? htmlspecialchars($_GET['payment_id'], ENT_QUOTES, 'UTF-8') : '';
$fbPhone       = isset($_GET['phone'])        ? htmlspecialchars($_GET['phone'], ENT_QUOTES, 'UTF-8') : '';
$fbCoupon      = isset($_GET['coupon_code'])  ? htmlspecialchars($_GET['coupon_code'], ENT_QUOTES, 'UTF-8') : '';
$fbProductName = isset($_GET['product_name']) ? htmlspecialchars(urldecode($_GET['product_name']), ENT_QUOTES, 'UTF-8') : '';

// ===== PARSE LEGACY PARAMS (fallback) =====
$legacyPkg  = isset($_GET['pkg'])  ? htmlspecialchars($_GET['pkg'], ENT_QUOTES, 'UTF-8') : '';
$legacyDest = isset($_GET['dest']) ? $_GET['dest'] : '';
$legacyBiz  = isset($_GET['biz'])  ? htmlspecialchars(urldecode($_GET['biz']), ENT_QUOTES, 'UTF-8') : '';

// ===== DETERMINE PACKAGE =====
$pkg = 'growth'; // default
if (!empty($fbRef) && isset($refMap[$fbRef])) {
    $pkg = $refMap[$fbRef];
} elseif (!empty($legacyPkg) && isset($packages[$legacyPkg])) {
    $pkg = $legacyPkg;
}
$pkgInfo = $packages[$pkg];

// ===== TRANSACTION ID =====
$transactionId = !empty($fbPaymentId)
    ? $fbPaymentId
    : 'SB_' . time() . '_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);

// ===== USER DATA =====
$userEmail = $fbEmail;
$userPhone = $fbPhone;
$userName  = !empty($fbName) ? $fbName : $legacyBiz;
$nameParts = explode(' ', $userName, 2);
$firstName = isset($nameParts[0]) ? $nameParts[0] : '';
$lastName  = isset($nameParts[1]) ? $nameParts[1] : '';

// Sanitize destination URL (legacy)
$safeDest = filter_var($legacyDest, FILTER_VALIDATE_URL) ? $legacyDest : 'https://smart-buzzer.com/promo/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
    <!-- End Google Tag Manager -->

    <!-- Meta Pixel: managed via GTM (tag: FB - Pageview, All Pages) -->
    <!-- Do NOT add direct fbq('init') here — GTM handles all FB events -->

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
    function safeLocalStorage(action, key, value) {
        try {
            if (action === 'get') return localStorage.getItem(key);
            if (action === 'set') return localStorage.setItem(key, value);
            return null;
        } catch (e) { return null; }
    }
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #EFF6FF 0%, #FAFAF9 50%, #F5F3FF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .ty-container { max-width: 520px; width: 100%; }

        .ty-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
            overflow: hidden;
            text-align: center;
        }

        .ty-header {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            padding: 40px 32px 36px;
            position: relative;
            overflow: hidden;
        }

        .ty-header::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .ty-check-circle {
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(8px);
        }

        .ty-check-circle i { font-size: 32px; color: #FFFFFF; }

        .ty-header h1 {
            color: #FFFFFF;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .ty-header p { color: rgba(255,255,255,0.85); font-size: 15px; }

        .ty-body { padding: 32px; }

        .ty-summary {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
            text-align: left;
        }

        .ty-summary-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .ty-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .ty-summary-row + .ty-summary-row { border-top: 1px solid #E2E8F0; }
        .ty-summary-label { font-size: 14px; color: #64748B; font-weight: 500; }
        .ty-summary-value { font-size: 14px; color: #1E293B; font-weight: 600; }

        .ty-summary-total {
            margin-top: 4px;
            padding-top: 12px;
            border-top: 2px solid #CBD5E1;
        }

        .ty-summary-total .ty-summary-value {
            font-size: 20px;
            color: #2563EB;
            font-weight: 800;
        }

        .ty-source-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ty-info {
            font-size: 14px;
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .ty-cta-btn {
            display: block;
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        }

        .ty-cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,0.45);
        }

        .ty-cta-btn:active { transform: translateY(0); }
        .ty-cta-btn i { margin-left: 8px; }

        .ty-trust {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
            font-size: 12px;
            color: #94A3B8;
            font-weight: 500;
            flex-wrap: wrap;
        }

        .ty-trust i { margin-right: 4px; }

        .ty-footer {
            padding: 20px 32px;
            border-top: 1px solid #F1F5F9;
            text-align: center;
        }

        .ty-footer-logo img { height: 28px; opacity: 0.6; transition: opacity 0.3s; }
        .ty-footer-logo img:hover { opacity: 1; }
        .ty-footer-text { font-size: 11px; color: #94A3B8; margin-top: 8px; }

        @keyframes tyPulse {
            0%   { box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
            50%  { box-shadow: 0 4px 24px rgba(37,99,235,0.55); }
            100% { box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        }

        .ty-cta-btn { animation: tyPulse 2s ease-in-out infinite; }
        .ty-cta-btn:hover { animation: none; }

        @media (max-width: 560px) {
            .ty-header { padding: 32px 24px 28px; }
            .ty-header h1 { font-size: 22px; }
            .ty-body { padding: 24px; }
            .ty-trust { flex-direction: column; gap: 8px; }
            .ty-footer { padding: 16px 24px; }
        }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="ty-container">
    <div class="ty-card">
        <div class="ty-header">
            <div class="ty-check-circle">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been received successfully</p>
        </div>

        <div class="ty-body">
            <div class="ty-summary">
                <div class="ty-summary-title">Order Summary</div>
                <div class="ty-summary-row">
                    <span class="ty-summary-label">Package</span>
                    <span class="ty-summary-value"><?php echo $pkgInfo['name']; ?></span>
                </div>
                <div class="ty-summary-row">
                    <span class="ty-summary-label">Reviews</span>
                    <span class="ty-summary-value"><?php echo $pkgInfo['reviews']; ?> Local Reviews</span>
                </div>
                <?php if (!empty($transactionId)): ?>
                <div class="ty-summary-row">
                    <span class="ty-summary-label">Order ID</span>
                    <span class="ty-summary-value" style="font-size:12px; color:#94A3B8;"><?php echo $transactionId; ?></span>
                </div>
                <?php endif; ?>
                <div class="ty-summary-row ty-summary-total">
                    <span class="ty-summary-label">Total Paid</span>
                    <span class="ty-summary-value"><?php echo $pkgInfo['display']; ?></span>
                </div>
            </div>

            <p class="ty-info">We appreciate your trust in Smart Buzzer. Our team will begin working on your campaign shortly. You will receive an update via WhatsApp within 24 hours.</p>

            <button class="ty-cta-btn" id="tyCtaBtn" onclick="handlePurchaseClick()">
                CLICK HERE TO ONBOARD <i class="fa-solid fa-arrow-right"></i>
            </button>

            <div class="ty-trust">
                <span><i class="fa-solid fa-lock" style="color:#059669;"></i> Secure Payment</span>
                <span><i class="fa-solid fa-shield-halved" style="color:#2563EB;"></i> SSL Protected</span>
                <span><i class="fa-solid fa-circle-check" style="color:#059669;"></i> Account Safety Guaranteed</span>
            </div>
        </div>

        <div class="ty-footer">
            <a href="https://smart-buzzer.com/" class="ty-footer-logo">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" height="28">
            </a>
            <div class="ty-footer-text">A subsidiary of Pintarnya</div>
        </div>
    </div>
</div>

<script>
// ===== ANALYTICS TRACKING =====
var tyPageLoadTime = Date.now();

function logAnalyticsEvent(eventType, data) {
    data = data || {};
    var payload = JSON.stringify({
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
    var payload = JSON.stringify({
        event_type: eventType,
        page_url: window.location.href,
        data: JSON.stringify(data),
        session_id: sessionId
    });
    if (navigator.sendBeacon) {
        navigator.sendBeacon('analytics.php', new Blob([payload], {type: 'application/json'}));
    }
}

// ===== PURCHASE DATA FROM PHP =====
var tyPkgName       = <?php echo json_encode($pkgInfo['name']); ?>;
var tyPkgPrice      = <?php echo json_encode($pkgInfo['price']); ?>;
var tyPkgReviews    = <?php echo json_encode($pkgInfo['reviews']); ?>;
var tyPkgItemId     = <?php echo json_encode($pkgInfo['item_id']); ?>;
var tyPkgItemName   = <?php echo json_encode($pkgInfo['item_name']); ?>;
var tyTransactionId = <?php echo json_encode($transactionId); ?>;
var tyUserEmail     = <?php echo json_encode($userEmail); ?>;
var tyUserPhone     = <?php echo json_encode($userPhone); ?>;
var tyFirstName     = <?php echo json_encode($firstName); ?>;
var tyLastName      = <?php echo json_encode($lastName); ?>;
var tyPkgKey        = <?php echo json_encode($pkg); ?>;

// Track page view
logAnalyticsEvent('THANKYOU_PAGE_VIEW', {
    package: tyPkgName,
    price: tyPkgPrice,
    transaction_id: tyTransactionId,
    source_lp: <?php echo json_encode($pkgInfo['source_lp']); ?>
});

// ===== GTM DATALAYER - PURCHASE (fires on DOMContentLoaded) =====
document.addEventListener('DOMContentLoaded', function() {
    // Read user_data from LocalStorage (set at begin_checkout on LP)
    var lsEmail = '', lsPhone = '', lsFname = '', lsLname = '', lsTxnId = '';
    try {
        lsEmail = localStorage.getItem('sb_user_email') || '';
        lsPhone = localStorage.getItem('sb_user_phone') || '';
        lsFname = localStorage.getItem('sb_user_fname') || '';
        lsLname = localStorage.getItem('sb_user_lname') || '';
        lsTxnId = localStorage.getItem('sb_txn_id') || '';
    } catch(e) {}

    // Use LocalStorage data if PHP data is empty (bridge from LP → thankyou.php)
    var finalEmail = tyUserEmail || lsEmail;
    var finalPhone = tyUserPhone || lsPhone;
    var finalFname = tyFirstName || lsFname;
    var finalLname = tyLastName || lsLname;
    var finalTxnId = tyTransactionId || lsTxnId || ('SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6));

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'purchase',
        user_data: {
            email: finalEmail,
            phone_number: finalPhone,
            first_name: finalFname,
            last_name: finalLname
        },
        ecommerce: {
            transaction_id: finalTxnId,
            value: parseFloat(tyPkgPrice),
            currency: 'USD',
            items: [{
                item_id: tyPkgItemId,
                item_name: tyPkgItemName,
                item_category: 'Google Reviews',
                price: parseFloat(tyPkgPrice),
                quantity: 1
            }]
        }
    });

    // FB Purchase pixel: managed via GTM tag "FB - Purchase" (event: 'purchase')
    // Direct fbq() call removed to prevent duplicate fire with GTM tag

    // Fire TikTok CompletePayment on page load (no GTM tag for TikTok — direct call required)
    if (typeof ttq !== 'undefined') {
        ttq.track('CompletePayment', {
            value: parseFloat(tyPkgPrice),
            currency: 'USD',
            content_name: tyPkgName + ' - ' + tyPkgReviews + ' Reviews',
            content_type: 'product'
        });
    }

    // Clear LocalStorage after purchase fires
    try {
        localStorage.removeItem('sb_user_email');
        localStorage.removeItem('sb_user_phone');
        localStorage.removeItem('sb_user_fname');
        localStorage.removeItem('sb_user_lname');
        localStorage.removeItem('sb_txn_id');
        localStorage.removeItem('sb_pkg');
    } catch(e) {}
});

// ===== ONBOARD CLICK HANDLER (pixels already fired on page load) =====
var tyClickHandled = false;

function handlePurchaseClick() {
    if (tyClickHandled) return;
    tyClickHandled = true;

    // Analytics event
    logAnalyticsEvent('PURCHASE_CLICK', {
        package: tyPkgName,
        price: tyPkgPrice,
        reviews: tyPkgReviews,
        transaction_id: tyTransactionId
    });

    // 400ms delay then redirect to onboarding
    setTimeout(function() {
        window.location.href = 'https://smart-buzzer.com/submit/';
    }, 400);
}

// Exit tracking
window.addEventListener('beforeunload', function() {
    var timeSpent = Math.floor((Date.now() - tyPageLoadTime) / 1000);
    beaconAnalyticsEvent('THANKYOU_EXIT', {
        time_spent: timeSpent,
        clicked_purchase: tyClickHandled
    });
});
</script>

</body>
</html>
