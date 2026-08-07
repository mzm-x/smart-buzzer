<?php
/**
 * /sw-google/payment.php — Zelle / Crypto payment + proof-of-payment upload
 *
 * Flow: index.php order form -> submitOrder() -> redirect here with ?pkg= & biz=
 *   1) Show Zelle + Crypto instructions with the correct amount for the package
 *   2) Customer uploads proof of payment (image/PDF) -> POSTs to upload.php
 *   3) On successful upload, JS fires the GA4 `purchase` dataLayer + FB/TikTok
 *      Purchase pixels (reads user_data from LocalStorage bridge), then shows
 *      a confirmation state.
 *
 * NOTE: Purchase pixel/dataLayer fires AFTER a confirmed proof upload (our own
 * "thank you" moment) — there is no third-party gateway. GTM installed on this
 * page too. customer_data.log (FORM_SUBMIT) is written on index.php order submit.
 */
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// ---- Package map (must match index.php sbPkgMeta) ----
$packages = [
    'starter'     => ['name' => 'Starter',     'reviews' => 72,  'price' => 360.00, 'display' => '$360'],
    'growth'      => ['name' => 'Growth',      'reviews' => 96,  'price' => 430.00, 'display' => '$430'],
    'performance' => ['name' => 'Performance', 'reviews' => 132, 'price' => 530.00, 'display' => '$530'],
];

// ---- Payment destinations ----
$ZELLE_NUMBER = '5032671660';
$ZELLE_NAME   = 'Ghirish Pokardash';

// ---- Parse + sanitize input ----
$pkgKey = isset($_GET['pkg']) ? preg_replace('/[^a-z]/', '', strtolower($_GET['pkg'])) : 'growth';
if (!isset($packages[$pkgKey])) { $pkgKey = 'growth'; }
$pkg = $packages[$pkgKey];

$biz = isset($_GET['biz']) ? htmlspecialchars(trim($_GET['biz']), ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment - Smart Buzzer</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
    <!-- End Google Tag Manager -->

    <!-- Meta Pixel: managed via GTM (no direct fbq init) -->
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
    src="https://www.facebook.com/tr?id=938738044322271&ev=PageView&noscript=1"/></noscript>
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --blue-primary: #2563EB; --blue-hover: #1D4ED8; --blue-light: #EFF6FF;
            --green-success: #059669; --green-light: #ECFDF5;
            --zelle: #6D1ED4; --crypto: #F7931A;
            --text-primary: #1A1A1A; --text-secondary: #525252; --text-muted: #A3A3A3;
            --border-color: #E5E5E4; --bg-light: #FAFAF9;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(180deg, #FAFAF9 0%, #F1F5F9 100%);
            color: var(--text-primary); line-height: 1.6; min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        .pay-header {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(12px);
            padding: 16px 0; box-shadow: 0 1px 0 var(--border-color); text-align: center;
        }
        .pay-header img { height: 34px; }
        .pay-wrap { max-width: 640px; margin: 0 auto; padding: 32px 20px 64px; }
        .pay-steps {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 28px; font-size: 13px; color: var(--text-muted); font-weight: 600;
        }
        .pay-steps .done { color: var(--green-success); }
        .pay-steps .cur { color: var(--blue-primary); }
        .pay-card {
            background: #fff; border: 1px solid var(--border-color); border-radius: 20px;
            padding: 32px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); margin-bottom: 20px;
        }
        .pay-title { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 6px; }
        .pay-sub { font-size: 15px; color: var(--text-secondary); margin-bottom: 24px; }
        .order-summary {
            background: var(--blue-light); border-radius: 14px; padding: 18px 20px;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 26px;
        }
        .order-summary .os-pkg { font-weight: 700; font-size: 16px; }
        .order-summary .os-rev { font-size: 13px; color: var(--text-secondary); }
        .order-summary .os-amt { font-size: 30px; font-weight: 900; color: var(--blue-hover); letter-spacing: -1px; }
        .pay-method {
            border: 1.5px solid var(--border-color); border-radius: 14px;
            padding: 20px; margin-bottom: 16px;
        }
        .pay-method-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .pay-method-head .pm-icon {
            width: 40px; height: 40px; border-radius: 10px; display: flex;
            align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0;
        }
        .pay-method-head h3 { font-size: 17px; font-weight: 700; }
        .pay-row {
            display: flex; justify-content: space-between; align-items: center; gap: 12px;
            padding: 11px 14px; background: var(--bg-light); border-radius: 10px; margin-bottom: 8px;
        }
        .pay-row:last-child { margin-bottom: 0; }
        .pay-row .pr-label { font-size: 13px; color: var(--text-muted); font-weight: 600; }
        .pay-row .pr-val { font-size: 15px; font-weight: 700; font-family: 'Inter', monospace; word-break: break-all; }
        .copy-btn {
            background: var(--blue-primary); color: #fff; border: none; border-radius: 8px;
            padding: 7px 12px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;
            transition: background 0.15s;
        }
        .copy-btn:hover { background: var(--blue-hover); }
        .copy-btn.copied { background: var(--green-success); }
        .crypto-note { font-size: 12px; color: var(--text-muted); margin-top: 10px; line-height: 1.5; }
        .upload-zone {
            border: 2px dashed #CBD5E1; border-radius: 14px; padding: 28px 20px; text-align: center;
            cursor: pointer; transition: all 0.2s; background: var(--bg-light); margin-bottom: 8px;
        }
        .upload-zone:hover, .upload-zone.drag { border-color: var(--blue-primary); background: var(--blue-light); }
        .upload-zone i { font-size: 30px; color: var(--blue-primary); margin-bottom: 10px; }
        .upload-zone p { font-size: 14px; color: var(--text-secondary); }
        .upload-zone .uz-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .file-preview {
            display: none; align-items: center; gap: 12px; padding: 12px 14px;
            background: var(--green-light); border: 1px solid #A7F3D0; border-radius: 10px; margin-bottom: 8px;
        }
        .file-preview.show { display: flex; }
        .file-preview .fp-name { flex: 1; font-size: 13px; font-weight: 600; color: #065F46; word-break: break-all; }
        .file-preview .fp-remove { background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px; }
        .of-input {
            width: 100%; padding: 13px 15px; border: 1.5px solid var(--border-color);
            border-radius: 10px; font-size: 15px; font-family: inherit; margin-bottom: 14px;
        }
        .of-input:focus { outline: none; border-color: var(--blue-primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
        .submit-btn {
            width: 100%; background: var(--green-success); color: #fff; border: none;
            border-radius: 12px; padding: 16px; font-size: 16px; font-weight: 700; cursor: pointer;
            transition: background 0.15s; letter-spacing: 0.3px;
        }
        .submit-btn:hover { background: #047857; }
        .submit-btn:disabled { background: #94A3B8; cursor: not-allowed; }
        .err-msg { color: #DC2626; font-size: 13px; margin-top: 10px; text-align: center; display: none; }
        .err-msg.show { display: block; }
        .policy-note {
            font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 16px; line-height: 1.6;
        }
        .success-state { display: none; text-align: center; padding: 20px 0; }
        .success-state.show { display: block; }
        .success-check {
            width: 76px; height: 76px; border-radius: 50%; background: var(--green-light);
            color: var(--green-success); display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin: 0 auto 20px;
        }
        .success-state h2 { font-size: 24px; font-weight: 800; margin-bottom: 10px; }
        .success-state p { font-size: 15px; color: var(--text-secondary); margin-bottom: 8px; }
        .wa-cta {
            display: inline-flex; align-items: center; gap: 8px; margin-top: 18px;
            background: #25D366; color: #fff; text-decoration: none; padding: 13px 26px;
            border-radius: 12px; font-weight: 700; font-size: 15px;
        }
        @media (max-width: 560px) {
            .pay-card { padding: 24px 18px; }
            .order-summary { flex-direction: column; align-items: flex-start; gap: 8px; }
            .order-summary .os-amt { font-size: 26px; }
        }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div class="pay-header">
        <a href="index.php"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer"></a>
    </div>

    <div class="pay-wrap">
        <div class="pay-steps">
            <span class="done"><i class="fa-solid fa-circle-check"></i> Order</span>
            <span>&mdash;</span>
            <span class="cur"><i class="fa-solid fa-circle-dot"></i> Payment</span>
            <span>&mdash;</span>
            <span><i class="fa-regular fa-circle"></i> Done</span>
        </div>

        <!-- ============ PAYMENT / UPLOAD STATE ============ -->
        <div id="payState">
            <div class="pay-card">
                <div class="pay-title">Complete your payment</div>
                <div class="pay-sub">Send the exact amount below via Zelle or Crypto, then upload your proof of payment. We verify and start your campaign right after.</div>

                <div class="order-summary">
                    <div>
                        <div class="os-pkg"><?php echo $pkg['name']; ?> Package</div>
                        <div class="os-rev"><?php echo $pkg['reviews']; ?> show-up reviews<?php echo $biz ? ' &middot; ' . $biz : ''; ?></div>
                    </div>
                    <div class="os-amt"><?php echo $pkg['display']; ?></div>
                </div>

                <!-- Zelle -->
                <div class="pay-method">
                    <div class="pay-method-head">
                        <div class="pm-icon" style="background: var(--zelle);"><i class="fa-solid fa-bolt"></i></div>
                        <h3>Pay with Zelle</h3>
                    </div>
                    <div class="pay-row">
                        <span class="pr-label">Zelle number</span>
                        <span class="pr-val" id="zelleNum"><?php echo $ZELLE_NUMBER; ?></span>
                        <button class="copy-btn" data-copy="<?php echo $ZELLE_NUMBER; ?>">Copy</button>
                    </div>
                    <div class="pay-row">
                        <span class="pr-label">Recipient name</span>
                        <span class="pr-val"><?php echo $ZELLE_NAME; ?></span>
                    </div>
                    <div class="pay-row">
                        <span class="pr-label">Amount</span>
                        <span class="pr-val"><?php echo $pkg['display']; ?> USD</span>
                    </div>
                </div>

                <!-- Proof upload -->
                <form id="proofForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="pkg" value="<?php echo htmlspecialchars($pkgKey); ?>">
                    <input type="hidden" name="business" value="<?php echo $biz; ?>">
                    <input type="hidden" name="payer_email" id="payerEmail" value="">

                    <h3 style="font-size:16px;font-weight:700;margin:8px 0 12px;">Upload proof of payment</h3>

                    <input type="text" class="of-input" id="payerName" name="payer_name" placeholder="Name on the payment (as sent)">
                    <input type="text" class="of-input" id="payerContact" name="payer_contact" placeholder="Your WhatsApp number (for confirmation)">

                    <div class="upload-zone" id="uploadZone">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p><strong>Tap to upload</strong> or drag your screenshot here</p>
                        <p class="uz-hint">JPG, PNG, WEBP or PDF &middot; max 8 MB</p>
                        <input type="file" id="proofFile" name="proof" accept="image/jpeg,image/png,image/webp,application/pdf" style="display:none;">
                    </div>
                    <div class="file-preview" id="filePreview">
                        <i class="fa-solid fa-file-circle-check" style="color:#059669;font-size:18px;"></i>
                        <span class="fp-name" id="fpName"></span>
                        <button type="button" class="fp-remove" id="fpRemove">&times;</button>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn"><i class="fa-solid fa-paper-plane"></i> Submit Proof &amp; Confirm Order</button>
                    <div class="err-msg" id="errMsg"></div>
                    <p class="policy-note">All sales final &mdash; refunds issued as store voucher only. By submitting you agree to our show-up guarantee (7-day per-review replacement).</p>
                </form>
            </div>
        </div>

        <!-- ============ SUCCESS STATE ============ -->
        <div class="pay-card success-state" id="successState">
            <div class="success-check"><i class="fa-solid fa-check"></i></div>
            <h2>Proof received &mdash; thank you!</h2>
            <p>We&rsquo;ve got your payment proof for the <strong><?php echo $pkg['name']; ?></strong> package (<?php echo $pkg['reviews']; ?> show-up reviews).</p>
            <p>Our team verifies payments within a few hours and will message you on WhatsApp to kick off your content approval.</p>
            <a class="wa-cta" href="https://wa.me/628979133204?text=Hi%20Smart%20Buzzer%2C%20I%20just%20submitted%20my%20payment%20proof%20for%20the%20<?php echo urlencode($pkg['name']); ?>%20package." target="_blank"><i class="fa-brands fa-whatsapp"></i> Message us on WhatsApp</a>
        </div>
    </div>

    <script>
    // ---- Package meta (mirror of index.php sbPkgMeta) ----
    var PKG = {
        key: '<?php echo $pkgKey; ?>',
        id: '<?php echo 'pkg_' . $pkgKey . '_' . $pkg['reviews']; ?>',
        name: '<?php echo addslashes("Buy Google Reviews - " . $pkg['reviews'] . " Show-Up"); ?>',
        price: <?php echo $pkg['price']; ?>,
        reviews: <?php echo $pkg['reviews']; ?>
    };

    // ---- session id (shared with index.php via sessionStorage) ----
    function getSessionId() {
        var sid = sessionStorage.getItem('sb_session_id');
        if (!sid) { sid = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9); sessionStorage.setItem('sb_session_id', sid); }
        return sid;
    }
    var sessionId = getSessionId();

    function logAnalyticsEvent(eventType, data) {
        try {
            fetch('analytics.php', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ event_type: eventType, page_url: window.location.href, data: JSON.stringify(data || {}), session_id: sessionId })
            }).catch(function(){});
        } catch (e) {}
    }
    logAnalyticsEvent('PAYMENT_PAGE_VIEW', { pkg: PKG.key });

    // ---- prefill email (hidden) + WhatsApp from the order-form bridge ----
    document.getElementById('payerEmail').value = localStorage.getItem('sb_user_email') || '';
    var _bridgePhone = localStorage.getItem('sb_user_phone') || '';
    if (_bridgePhone && !document.getElementById('payerContact').value) {
        document.getElementById('payerContact').value = _bridgePhone;
    }

    // ---- copy buttons ----
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var val = this.getAttribute('data-copy');
            var self = this;
            navigator.clipboard.writeText(val).then(function() {
                self.textContent = 'Copied'; self.classList.add('copied');
                setTimeout(function(){ self.textContent = 'Copy'; self.classList.remove('copied'); }, 1800);
            }).catch(function(){ self.textContent = 'Copy'; });
        });
    });

    // ---- file picker ----
    var uploadZone = document.getElementById('uploadZone');
    var proofFile = document.getElementById('proofFile');
    var filePreview = document.getElementById('filePreview');
    var fpName = document.getElementById('fpName');
    var fpRemove = document.getElementById('fpRemove');
    var MAX_BYTES = 8 * 1024 * 1024;

    uploadZone.addEventListener('click', function() { proofFile.click(); });
    ['dragover','dragenter'].forEach(function(ev){ uploadZone.addEventListener(ev, function(e){ e.preventDefault(); uploadZone.classList.add('drag'); }); });
    ['dragleave','drop'].forEach(function(ev){ uploadZone.addEventListener(ev, function(e){ e.preventDefault(); uploadZone.classList.remove('drag'); }); });
    uploadZone.addEventListener('drop', function(e){ if (e.dataTransfer.files.length) { proofFile.files = e.dataTransfer.files; showFile(); } });
    proofFile.addEventListener('change', showFile);
    fpRemove.addEventListener('click', function(){ proofFile.value = ''; filePreview.classList.remove('show'); });

    function showFile() {
        if (!proofFile.files.length) { filePreview.classList.remove('show'); return; }
        var f = proofFile.files[0];
        fpName.textContent = f.name + '  (' + Math.round(f.size/1024) + ' KB)';
        filePreview.classList.add('show');
    }

    // ---- submit ----
    var proofForm = document.getElementById('proofForm');
    var submitBtn = document.getElementById('submitBtn');
    var errMsg = document.getElementById('errMsg');
    var submitted = false;

    function showErr(msg) { errMsg.textContent = msg; errMsg.classList.add('show'); }

    proofForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (submitted) return;
        errMsg.classList.remove('show');

        if (!proofFile.files.length) { showErr('Please upload your proof of payment screenshot or PDF.'); return; }
        var f = proofFile.files[0];
        if (f.size > MAX_BYTES) { showErr('File is too large. Max size is 8 MB.'); return; }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';

        var fd = new FormData(proofForm);
        fetch('upload.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res && res.status === 'success') {
                    submitted = true;
                    firePurchase();
                    logAnalyticsEvent('PROOF_SUBMITTED', { pkg: PKG.key, file: res.file || '' });
                    document.getElementById('payState').style.display = 'none';
                    document.getElementById('successState').classList.add('show');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Proof &amp; Confirm Order';
                    showErr((res && res.message) ? res.message : 'Upload failed. Please try again or send your proof via WhatsApp.');
                }
            })
            .catch(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Proof &amp; Confirm Order';
                showErr('Network error. Please try again or send your proof via WhatsApp.');
            });
    });

    // ---- purchase event (fires once, on confirmed proof upload) ----
    function firePurchase() {
        var sbEmail = localStorage.getItem('sb_user_email') || '';
        var sbPhone = localStorage.getItem('sb_user_phone') || document.getElementById('payerContact').value || '';
        var sbFname = localStorage.getItem('sb_user_fname') || '';
        var sbLname = localStorage.getItem('sb_user_lname') || '';
        var sbTxnId = localStorage.getItem('sb_txn_id') || ('SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6));

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            event: 'purchase',
            user_data: { email: sbEmail, phone_number: sbPhone, first_name: sbFname, last_name: sbLname },
            ecommerce: {
                transaction_id: sbTxnId,
                value: PKG.price,
                currency: 'USD',
                items: [{ item_id: PKG.id, item_name: PKG.name, item_category: 'Google Reviews', price: PKG.price, quantity: 1 }]
            }
        });

        if (typeof fbq !== 'undefined') {
            fbq('track', 'Purchase', { value: PKG.price, currency: 'USD', content_name: PKG.name + ' - ' + PKG.reviews + ' Reviews', content_type: 'product', content_ids: [PKG.key] });
        }
        if (typeof ttq !== 'undefined') {
            ttq.track('CompletePayment', { value: PKG.price, currency: 'USD', content_name: PKG.name + ' - ' + PKG.reviews + ' Reviews', content_type: 'product' });
        }

        // clear the bridge
        ['sb_user_email','sb_user_phone','sb_user_fname','sb_user_lname','sb_txn_id','sb_pkg'].forEach(function(k){ localStorage.removeItem(k); });
    }
    </script>
</body>
</html>
