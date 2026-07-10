<?php $__wa=$_SERVER["DOCUMENT_ROOT"]."/wa-config.php"; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER="628979133204";} if(empty($SB_WA_DISPLAY)){$SB_WA_DISPLAY="+62 897-9133-204";} ?>
<?php
/* lookup-only balance/voucher tool — no tracking, no writes, safe to re-check */

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($n){ return '$' . number_format((float)$n, 2); }

// --- Voucher validity (single global expiry shown on every voucher) ---
$voucherValidUntil = '1 Sep 2026';

// --- Load data (graceful if missing/invalid) ---
$dataAvailable = true;
$orders = array();
$raw = @file_get_contents(__DIR__ . '/redeem_data.json');
if ($raw === false) {
    $dataAvailable = false;
} else {
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['orders']) || !is_array($data['orders'])) {
        $dataAvailable = false;
    } else {
        $orders = $data['orders'];
    }
}

// --- Handle lookup: POST from form, OR GET ?email= for shareable auto-show links ---
$emailInput = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $emailInput = $_POST['email'];
} elseif (isset($_GET['email']) && $_GET['email'] !== '') {
    $emailInput = $_GET['email'];
} elseif (!empty($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '@') !== false) {
    // lenient: also accept ?=email@x.com or ?email@x.com (bare query)
    $qs  = $_SERVER['QUERY_STRING'];
    $val = (strpos($qs, '=') !== false) ? substr($qs, strpos($qs, '=') + 1) : $qs;
    $emailInput = urldecode($val);
}
$submitted       = ($emailInput !== '');
$normalizedEmail = strtolower(trim($emailInput));

$matches = array();
if ($submitted && $dataAvailable && $normalizedEmail !== '') {
    foreach ($orders as $o) {
        if (!is_array($o) || !isset($o['email'])) { continue; }
        if (strtolower(trim($o['email'])) === $normalizedEmail) {
            $matches[] = $o;
        }
    }
}

// --- Group matches by business + compute total ---
$groups = array();
$total  = 0;
foreach ($matches as $m) {
    $biz = isset($m['business']) && $m['business'] !== '' ? $m['business'] : 'Your Orders';
    if (!isset($groups[$biz])) { $groups[$biz] = array(); }
    $groups[$biz][] = $m;
    $total += isset($m['amount']) ? (float)$m['amount'] : 0;
}
$multipleBusinesses = count($groups) > 1;

$waLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $SB_WA_NUMBER);

// --- WhatsApp "Redeem" CTA -> account-manager number, with prefilled order details ---
$waRedeemNumber = '6287870707202';
$waRedeemLink   = '';
if (!empty($matches)) {
    $lines   = array();
    $lines[] = "Hi Smart Buzzer, I'd like to redeem my review balance.";
    $lines[] = "";
    $lines[] = "Email: " . $normalizedEmail;
    $lines[] = "Total balance: " . money($total);
    $lines[] = "";
    foreach ($matches as $m) {
        $lbl = isset($m['label']) && $m['label'] !== '' ? $m['label'] : (isset($m['business']) ? $m['business'] : 'Prepaid Order');
        $amt = money(isset($m['amount']) ? $m['amount'] : 0);
        $rem = isset($m['remaining']) ? (int)$m['remaining'] : 0;
        $cd  = isset($m['code']) ? $m['code'] : '';
        $lines[] = "\xE2\x80\xA2 " . $lbl . " \xE2\x80\x94 " . $amt . " (" . $rem . " reviews) \xE2\x80\x94 " . $cd;
    }
    $waRedeemLink = 'https://wa.me/' . $waRedeemNumber . '?text=' . rawurlencode(implode("\n", $lines));
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Smart Buzzer — Check Your Balance</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<!-- lookup-only tool: no tracking -->
<style>
:root{
  --blue-primary:#2563EB; --blue-hover:#1D4ED8; --blue-light:#EFF6FF;
  --green-success:#059669; --bg-light:#FAFAF9; --card-bg:#FFFFFF;
  --border-color:#E5E5E4; --text-primary:#1A1A1A; --text-secondary:#525252;
  --text-muted:#A3A3A3; --radius-sm:12px; --radius-md:16px; --radius-lg:20px;
  --radius-xl:24px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  background:var(--bg-light);
  color:var(--text-primary);
  line-height:1.55;
  -webkit-font-smoothing:antialiased;
  padding:32px 18px 56px;
  display:flex;justify-content:center;
}
.wrap{width:100%;max-width:520px;}
.brand{text-align:center;margin-bottom:26px;}
.brand img{height:44px;width:auto;}
.hero{text-align:center;margin-bottom:24px;}
.hero h1{
  font-size:26px;font-weight:900;letter-spacing:-0.02em;
  margin-bottom:8px;
}
.hero p{font-size:15px;color:var(--text-secondary);}

.card{
  background:var(--card-bg);
  border:1px solid var(--border-color);
  border-radius:var(--radius-xl);
  padding:26px 22px;
  box-shadow:0 4px 24px rgba(15,23,42,.06);
}

/* Lookup form */
.form-label{
  display:block;font-size:13px;font-weight:700;
  color:var(--text-secondary);margin-bottom:8px;
  text-transform:uppercase;letter-spacing:.04em;
}
.field{
  width:100%;font-family:inherit;font-size:16px;
  padding:15px 16px;border:1.5px solid var(--border-color);
  border-radius:var(--radius-sm);background:#fff;color:var(--text-primary);
  transition:border-color .15s, box-shadow .15s;
}
.field:focus{
  outline:none;border-color:var(--blue-primary);
  box-shadow:0 0 0 4px rgba(37,99,235,.12);
}
.btn{
  width:100%;margin-top:14px;cursor:pointer;
  font-family:inherit;font-size:16px;font-weight:700;
  color:#fff;background:var(--blue-primary);
  border:none;border-radius:var(--radius-sm);
  padding:15px 18px;transition:background .15s, transform .05s;
}
.btn:hover{background:var(--blue-hover);}
.btn:active{transform:translateY(1px);}
.helper{
  font-size:13px;color:var(--text-muted);
  margin-top:14px;text-align:center;
}

/* States / messages */
.notice{
  border-radius:var(--radius-md);padding:16px 18px;font-size:14.5px;
  margin-bottom:22px;
}
.notice-warn{
  background:#FEF3C7;border:1px solid #FDE68A;color:#92400E;
}
.empty{
  text-align:center;padding:8px 4px 4px;
}
.empty .emo{font-size:34px;line-height:1;margin-bottom:12px;}
.empty h2{font-size:19px;font-weight:800;margin-bottom:8px;}
.empty p{font-size:14.5px;color:var(--text-secondary);}

/* Results */
.results-head{text-align:center;margin-bottom:22px;}
.results-head h2{font-size:21px;font-weight:900;letter-spacing:-0.01em;}
.results-head p{font-size:14px;color:var(--text-secondary);margin-top:4px;word-break:break-word;}

.group-title{
  font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;
  color:var(--text-secondary);margin:22px 4px 12px;
  display:flex;align-items:center;gap:10px;
}
.group-title::after{content:"";flex:1;height:1px;background:var(--border-color);}

.voucher{
  position:relative;overflow:hidden;
  background:var(--card-bg);
  border:1px solid var(--border-color);
  border-radius:var(--radius-lg);
  padding:22px 20px 20px;
  margin-bottom:14px;
  box-shadow:0 2px 14px rgba(15,23,42,.05);
}
.voucher::before{
  content:"";position:absolute;top:0;left:0;right:0;height:5px;
  background:linear-gradient(90deg,var(--blue-primary),#3B82F6 55%,var(--green-success));
}
.voucher .amount{
  font-size:40px;font-weight:900;letter-spacing:-0.02em;
  color:var(--blue-primary);line-height:1.05;margin-top:4px;
}
.voucher .label{
  font-size:15px;font-weight:600;color:var(--text-primary);
  margin-top:6px;word-break:break-word;
}
.voucher .valid{
  display:inline-flex;align-items:center;gap:6px;
  margin-top:12px;font-size:13px;font-weight:700;
  color:#B45309;background:#FFFBEB;
  border:1px solid #FDE68A;border-radius:999px;padding:6px 12px;
}
.voucher .code-row{
  margin-top:16px;padding-top:14px;border-top:1px dashed var(--border-color);
  display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
}
.voucher .code-lbl{font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
.voucher .code-pill{
  font-family:'SFMono-Regular',ui-monospace,Menlo,Consolas,monospace;
  font-size:15px;font-weight:700;letter-spacing:.06em;
  color:var(--blue-hover);background:var(--blue-light);
  border:1px solid #BFDBFE;border-radius:10px;
  padding:8px 14px;
}
.total{
  margin-top:20px;background:var(--blue-light);
  border:1px solid #BFDBFE;border-radius:var(--radius-md);
  padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.total .t-lbl{font-size:14px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.04em;}
.total .t-val{font-size:26px;font-weight:900;color:var(--green-success);letter-spacing:-0.02em;}

.recheck{
  display:block;text-align:center;margin-top:20px;
  font-size:14px;font-weight:700;color:var(--blue-primary);
  text-decoration:none;
}
.recheck:hover{text-decoration:underline;}

/* Redeem CTA (WhatsApp) */
.redeem-cta{margin-top:22px;text-align:center;}
.redeem-cta .cta-copy{font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;margin-bottom:4px;}
.redeem-cta .cta-sub{font-size:13.5px;color:var(--text-secondary);margin-bottom:14px;}
.btn-wa{
  display:flex;align-items:center;justify-content:center;gap:10px;width:100%;
  background:#25D366;color:#fff;font-weight:800;font-size:16px;text-decoration:none;
  padding:15px 18px;border-radius:var(--radius-sm);
  transition:background .15s, transform .05s;
  box-shadow:0 6px 18px rgba(37,211,102,.30);
}
.btn-wa:hover{background:#1EBE57;}
.btn-wa:active{transform:translateY(1px);}
.btn-wa svg{width:21px;height:21px;fill:#fff;flex:none;}

.footer{
  text-align:center;margin-top:26px;font-size:13.5px;color:var(--text-secondary);
}
.footer a{color:var(--blue-primary);font-weight:700;text-decoration:none;}
.footer a:hover{text-decoration:underline;}

@media (max-width:400px){
  body{padding:24px 14px 44px;}
  .card{padding:22px 18px;}
  .voucher .amount{font-size:34px;}
  .hero h1{font-size:23px;}
}
</style>
</head>
<body>
<div class="wrap">

  <div class="brand">
    <a href="https://smart-buzzer.com/"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer"></a>
  </div>

<?php if (!$submitted): ?>

  <div class="hero">
    <h1>Check Your Balance</h1>
    <p>Enter the email on your order to see your prepaid review balance and voucher codes.</p>
  </div>

  <div class="card">
    <form method="post" action="">
      <label class="form-label" for="email">Email Address</label>
      <input class="field" type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" autofocus required>
      <button class="btn" type="submit">Check Balance</button>
      <p class="helper">This is a lookup only &mdash; your balance never changes when you check it.</p>
    </form>
  </div>

<?php elseif (!$dataAvailable): ?>

  <div class="hero">
    <h1>Check Your Balance</h1>
  </div>
  <div class="card">
    <div class="notice notice-warn">
      Our balance lookup service is temporarily unavailable. Please try again in a few minutes, or reach us on WhatsApp and we&rsquo;ll check it for you.
    </div>
    <a class="recheck" href="<?php echo h($_SERVER['PHP_SELF']); ?>">&larr; Try again</a>
  </div>

<?php elseif (empty($matches)): ?>

  <div class="hero">
    <h1>Check Your Balance</h1>
  </div>
  <div class="card">
    <div class="empty">
      <div class="emo">&#128269;</div>
      <h2>No balance found for that email</h2>
      <p>
        We couldn&rsquo;t find any orders for
        <?php if ($normalizedEmail !== ''): ?><strong><?php echo h($normalizedEmail); ?></strong><?php else: ?>that email<?php endif; ?>.
        Double-check the spelling, or use the email you ordered with.
      </p>
    </div>
    <a class="recheck" href="<?php echo h($_SERVER['PHP_SELF']); ?>">&larr; Check a different email</a>
    <p class="helper">Still stuck? <a class="footer" style="font-weight:700;color:var(--blue-primary);" href="<?php echo h($waLink); ?>" target="_blank" rel="noopener">Chat us on WhatsApp</a></p>
  </div>

<?php else: ?>

  <div class="results-head">
    <h2>Your Balance</h2>
    <p><?php echo h($normalizedEmail); ?></p>
  </div>

  <div class="card">
    <?php foreach ($groups as $bizName => $bizOrders): ?>
      <?php if ($multipleBusinesses): ?>
        <div class="group-title"><?php echo h($bizName); ?></div>
      <?php endif; ?>
      <?php foreach ($bizOrders as $o):
        $amount    = isset($o['amount']) ? $o['amount'] : 0;
        $label     = isset($o['label']) && $o['label'] !== '' ? $o['label'] : (isset($o['business']) ? $o['business'] : 'Prepaid Order');
        $remaining = isset($o['remaining']) ? (int)$o['remaining'] : 0;
        $code      = isset($o['code']) ? $o['code'] : '';
      ?>
      <div class="voucher">
        <div class="amount"><?php echo h(money($amount)); ?></div>
        <div class="label"><?php echo h($label); ?></div>
        <span class="valid">&#128197; Valid until <?php echo h($voucherValidUntil); ?></span>
        <?php if ($code !== ''): ?>
        <div class="code-row">
          <span class="code-lbl">Voucher Code</span>
          <span class="code-pill"><?php echo h($code); ?></span>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endforeach; ?>

    <div class="total">
      <span class="t-lbl">Total Balance</span>
      <span class="t-val"><?php echo h(money($total)); ?></span>
    </div>

    <?php if ($waRedeemLink !== ''): ?>
    <div class="redeem-cta">
      <div class="cta-copy">Ready to put your <?php echo h(money($total)); ?> to work?</div>
      <div class="cta-sub">Message your account manager to activate your remaining reviews.</div>
      <a class="btn-wa" href="<?php echo h($waRedeemLink); ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 .5C7.4.5.5 7.4.5 16c0 2.8.7 5.4 2 7.7L.5 31.5l8-2.1c2.2 1.2 4.8 1.9 7.5 1.9 8.6 0 15.5-6.9 15.5-15.5S24.6.5 16 .5zm0 28c-2.4 0-4.7-.6-6.7-1.8l-.5-.3-4.8 1.3 1.3-4.6-.3-.5c-1.3-2.1-2-4.5-2-7C3 8.8 8.8 3 16 3s13 5.8 13 13-5.8 12.5-13 12.5zm7.1-9.4c-.4-.2-2.3-1.1-2.6-1.3-.3-.1-.6-.2-.8.2-.2.4-.9 1.3-1.1 1.5-.2.2-.4.3-.8.1-.4-.2-1.6-.6-3.1-1.9-1.1-1-1.9-2.2-2.1-2.6-.2-.4 0-.6.2-.8.2-.2.4-.4.5-.7.2-.2.2-.4.4-.7.1-.3 0-.5 0-.7-.1-.2-.8-2-1.1-2.7-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.4 1.4 3.6c.2.2 2.4 3.7 5.9 5.2.8.4 1.5.6 2 .7.8.3 1.6.2 2.2.1.7-.1 2.3-.9 2.6-1.8.3-.9.3-1.6.2-1.8-.1-.1-.3-.2-.7-.4z"/></svg>
        Redeem on WhatsApp
      </a>
    </div>
    <?php endif; ?>

    <a class="recheck" href="<?php echo h($_SERVER['PHP_SELF']); ?>">&larr; Check another email</a>
  </div>

<?php endif; ?>

  <div class="footer">
    Need help? <a href="<?php echo h($waLink); ?>" target="_blank" rel="noopener">Chat us on WhatsApp</a> &middot; <?php echo h($SB_WA_DISPLAY); ?>
  </div>

</div>
</body>
</html>
