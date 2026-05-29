<?php
/**
 * /social/thankyou.php - Thank You Page for Social Media LP
 *
 * Flow: User pays on Fanbasis → Fanbasis redirects here → Purchase pixel fires on load
 * All order data is read from LocalStorage (set at begin_checkout on index.php)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
<!-- End Google Tag Manager -->
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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', Arial, sans-serif;
            background: #F2F2F7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Apple-style entrance animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes checkDraw {
            from { stroke-dashoffset: 24; }
            to { stroke-dashoffset: 0; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .ty-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow:
                0 0 0 0.5px rgba(0, 0, 0, 0.04),
                0 4px 16px rgba(0, 0, 0, 0.06),
                0 16px 48px rgba(0, 0, 0, 0.06);
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .ty-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #34C759 0%, #30D158 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
            box-shadow: 0 4px 16px rgba(52, 199, 89, 0.3);
        }

        .ty-icon svg {
            width: 40px;
            height: 40px;
            color: #fff;
            stroke-dasharray: 24;
            stroke-dashoffset: 24;
            animation: checkDraw 0.4s ease-out 0.7s forwards;
        }

        .ty-title {
            font-size: 30px;
            font-weight: 700;
            color: #1D1D1F;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            animation: fadeIn 0.5s ease 0.5s both;
        }

        .ty-subtitle {
            font-size: 16px;
            color: #86868B;
            margin-bottom: 32px;
            line-height: 1.6;
            animation: fadeIn 0.5s ease 0.6s both;
        }

        .ty-summary {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            text-align: left;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.7s both;
        }

        .ty-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 15px;
            color: #86868B;
            transition: background 0.2s ease;
        }

        .ty-summary-row span:last-child {
            font-weight: 600;
            color: #1D1D1F;
        }

        .ty-summary-total {
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            margin-top: 8px;
            padding-top: 14px;
            font-size: 16px;
        }

        .ty-summary-total span:last-child {
            font-size: 22px;
            font-weight: 700;
            color: #FF6B35;
        }

        .ty-btn {
            display: inline-block;
            width: 100%;
            padding: 18px 32px;
            background: linear-gradient(135deg, #FF6B35 0%, #FF8F5E 100%);
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.8s both;
            letter-spacing: -0.2px;
        }

        .ty-btn:hover {
            background: linear-gradient(135deg, #e55a25 0%, #FF6B35 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 107, 53, 0.3);
        }

        .ty-btn:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2);
        }

        .ty-footer {
            margin-top: 28px;
            font-size: 13px;
            color: #AEAEB2;
            animation: fadeIn 0.5s ease 0.9s both;
        }

        .ty-footer img {
            height: 24px;
            margin-bottom: 8px;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .ty-footer img:hover {
            opacity: 1;
        }

        @media (max-width: 520px) {
            .ty-card {
                padding: 36px 24px;
                border-radius: 20px;
            }
            .ty-title { font-size: 26px; }
            .ty-subtitle { font-size: 15px; }
            .ty-summary { border-radius: 16px; padding: 20px; }
            .ty-btn { border-radius: 16px; padding: 16px 24px; }
        }
    </style>
</head>
<body>
    <div class="ty-card">
        <div class="ty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 class="ty-title">Payment Successful!</h1>
        <p class="ty-subtitle">Thank you for your purchase. Your order is being processed and you'll receive updates soon.</p>

        <div class="ty-summary" id="tySummary" style="display:none;">
            <div class="ty-summary-row">
                <span>Platform</span>
                <span id="tyPlatform">-</span>
            </div>
            <div class="ty-summary-row">
                <span>Service</span>
                <span id="tyCategory">-</span>
            </div>
            <div class="ty-summary-row">
                <span>Quantity</span>
                <span id="tyQty">-</span>
            </div>
            <div class="ty-summary-row ty-summary-total">
                <span>Total</span>
                <span id="tyPrice">-</span>
            </div>
        </div>

        <a href="https://smart-buzzer.com/social/" class="ty-btn">Back to Smart Buzzer</a>

        <div class="ty-footer">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer"><br>
            Powered by Smart Buzzer
        </div>
    </div>

    <script>
        // ========== READ ORDER DATA FROM LOCALSTORAGE ==========
        window.dataLayer = window.dataLayer || [];

        var sbPhone    = localStorage.getItem('sb_user_phone') || '';
        var sbFname    = localStorage.getItem('sb_user_fname') || '';
        var sbLname    = localStorage.getItem('sb_user_lname') || '';
        var sbEmail    = localStorage.getItem('sb_user_email') || '';
        var sbTxnId    = localStorage.getItem('sb_txn_id') || ('SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6));
        var pkgId      = localStorage.getItem('sb_pkg') || '';
        var pkgName    = localStorage.getItem('sb_pkg_name') || '';
        var pkgPrice   = parseFloat(localStorage.getItem('sb_pkg_price')) || 0;
        var pkgQty     = parseInt(localStorage.getItem('sb_pkg_qty')) || 0;
        var pkgCategory = localStorage.getItem('sb_pkg_category') || '';
        var pkgPlatform = localStorage.getItem('sb_pkg_platform') || '';

        // Populate order summary
        if (pkgPrice > 0) {
            document.getElementById('tySummary').style.display = 'block';
            document.getElementById('tyPlatform').textContent = pkgPlatform;
            document.getElementById('tyCategory').textContent = pkgCategory;
            document.getElementById('tyQty').textContent = pkgQty.toLocaleString();
            document.getElementById('tyPrice').textContent = '$' + pkgPrice.toLocaleString();
        }

        // ========== FIRE PURCHASE EVENTS ON PAGE LOAD ==========

        // dataLayer: purchase
        if (pkgId) {
            window.dataLayer.push({ecommerce: null});
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
                    value: pkgPrice,
                    currency: 'USD',
                    items: [{
                        item_id: pkgId,
                        item_name: pkgName,
                        item_category: 'Social Media',
                        price: pkgPrice,
                        quantity: 1
                    }]
                }
            });
        }

        // FB Pixel: Purchase (on page load)
        if (typeof fbq !== 'undefined' && pkgPrice > 0) {
            fbq('track', 'Purchase', {
                value: pkgPrice,
                currency: 'USD',
                content_name: pkgName,
                content_type: 'product',
                content_ids: [pkgId]
            });
        }

        // TikTok: CompletePayment (on page load)
        if (typeof ttq !== 'undefined' && pkgPrice > 0) {
            ttq.track('CompletePayment', {
                value: pkgPrice,
                currency: 'USD',
                content_name: pkgName,
                content_type: 'product'
            });
        }

        // Clear LocalStorage after firing
        localStorage.removeItem('sb_user_phone');
        localStorage.removeItem('sb_user_fname');
        localStorage.removeItem('sb_user_lname');
        localStorage.removeItem('sb_user_email');
        localStorage.removeItem('sb_txn_id');
        localStorage.removeItem('sb_pkg');
        localStorage.removeItem('sb_pkg_name');
        localStorage.removeItem('sb_pkg_price');
        localStorage.removeItem('sb_pkg_qty');
        localStorage.removeItem('sb_pkg_category');
        localStorage.removeItem('sb_pkg_platform');

        // Analytics tracking
        var sbSessionId = sessionStorage.getItem('sb_session_id') || ('sb_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9));

        function sbTrackEvent(eventType, eventData) {
            var data = {
                event_type: eventType,
                page_url: window.location.href,
                data: JSON.stringify(eventData || {}),
                session_id: sbSessionId
            };
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'analytics.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.timeout = 3000;
            var params = [];
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    params.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
                }
            }
            xhr.send(params.join('&'));
        }

        sbTrackEvent('THANKYOU_PAGE_VIEW', { pkg: pkgId, price: pkgPrice });

        // Track exit
        window.addEventListener('beforeunload', function() {
            sbTrackEvent('THANKYOU_EXIT', { pkg: pkgId });
        });
    </script>
</body>
</html>
