<?php
/**
 * ============================================================================
 * File: /submit/test-email.php
 * Smart Buzzer — SMTP self-test & preview (AM login required)
 * ----------------------------------------------------------------------------
 * Open https://smart-buzzer.com/submit/test-email.php after uploading
 * mailer.php + email-template.php and filling data/smtp-config.json.
 *
 *   ?preview=1            -> render the confirmation email in the browser
 *   ?send=you@domain.com  -> actually send a test email to that address
 *
 * Shows the live SMTP conversation on failure so misconfiguration is obvious
 * (wrong port, wrong password, blocked outbound 465/587, etc).
 * Safe to leave installed — it is behind the AM login — but you can delete it
 * once email is confirmed working.
 * ============================================================================
 */

session_start();
require_once __DIR__ . '/auth.php';
requireAuth();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/email-template.php';

$sampleOrder = [
    'orderId'       => 'SB-' . date('Ymd') . '-TEST',
    'email'         => $_GET['send'] ?? 'preview@example.com',
    'whatsapp'      => '+1 415 555 0132',
    'businessNames' => 'Acme Dental Care',
    'quantity'      => 132,
    'productType'   => 'Rating & Review',
    'platform'      => 'Google',
    'timestamp'     => date('Y-m-d H:i:s'),
    'orderType'     => 'reviews',
];

// ---------- PREVIEW MODE ----------
if (isset($_GET['preview'])) {
    $mail = sbRenderOrderConfirmation($sampleOrder);
    header('Content-Type: text/html; charset=utf-8');
    echo $mail['html'];
    exit;
}

$cfg    = sbMailerConfig();
$result = null;

// ---------- SEND MODE ----------
if (!empty($_GET['send']) && filter_var($_GET['send'], FILTER_VALIDATE_EMAIL)) {
    $mail   = sbRenderOrderConfirmation($sampleOrder);
    $result = sbSmtpSend($_GET['send'], '[TEST] ' . $mail['subject'], $mail['html'], $mail['text'],
                         ['order_id' => $sampleOrder['orderId']]);
}

$logFile = __DIR__ . '/data/email_log.json';
$logs    = is_readable($logFile) ? json_decode((string)file_get_contents($logFile), true) : [];
$logs    = is_array($logs) ? array_slice($logs, -12) : [];
$e       = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SMTP Self-Test &middot; Smart Buzzer</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<style>
  body{margin:0;background:#F1F5F9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0F172A;}
  .wrap{max-width:820px;margin:0 auto;padding:32px 16px 60px;}
  h1{font-size:22px;margin:0 0 4px;} .sub{color:#64748B;font-size:14px;margin:0 0 24px;}
  .card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px;margin-bottom:18px;}
  .row{display:flex;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid #F1F5F9;font-size:14px;}
  .row:last-child{border-bottom:0;} .k{color:#64748B;} .v{font-weight:600;word-break:break-all;}
  .pill{display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;}
  .ok{background:#DCFCE7;color:#15803D;} .bad{background:#FEE2E2;color:#B91C1C;} .warn{background:#FEF3C7;color:#B45309;}
  input[type=email]{padding:11px 12px;border:1px solid #CBD5E1;border-radius:10px;font-size:14px;min-width:260px;}
  button,.btn{display:inline-block;padding:11px 18px;border:0;border-radius:10px;background:#2563EB;color:#fff;font-weight:700;font-size:14px;cursor:pointer;text-decoration:none;}
  .btn.sec{background:#0F172A;}
  pre{background:#0F172A;color:#E2E8F0;padding:14px;border-radius:10px;overflow:auto;font-size:12px;line-height:1.55;}
  table{width:100%;border-collapse:collapse;font-size:13px;} td,th{padding:7px 8px;border-bottom:1px solid #F1F5F9;text-align:left;}
  th{color:#64748B;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.5px;}
</style></head><body>
<div class="wrap">
  <h1>SMTP Self-Test</h1>
  <p class="sub">Verify the order-confirmation email before trusting it in production.</p>

  <div class="card">
    <div class="row"><span class="k">Status</span><span class="v">
      <?php if ($cfg['enabled']): ?><span class="pill ok">CONFIGURED</span>
      <?php else: ?><span class="pill bad">NOT CONFIGURED</span><?php endif; ?>
    </span></div>
    <div class="row"><span class="k">Config file</span><span class="v"><?= is_readable(__DIR__.'/data/smtp-config.json') ? 'data/smtp-config.json &#10003;' : 'missing &mdash; copy smtp-config.example.json' ?></span></div>
    <div class="row"><span class="k">Host</span><span class="v"><?= $e($cfg['host']) ?>:<?= $e($cfg['port']) ?> (<?= $e(strtoupper($cfg['secure'])) ?>)</span></div>
    <div class="row"><span class="k">Username</span><span class="v"><?= $e($cfg['username']) ?></span></div>
    <div class="row"><span class="k">Password</span><span class="v"><?= $cfg['password'] !== '' ? str_repeat('&bull;', 10) . ' set' : '<span class="pill bad">EMPTY</span>' ?></span></div>
    <div class="row"><span class="k">From</span><span class="v"><?= $e($cfg['from_name']) ?> &lt;<?= $e($cfg['from_email']) ?>&gt;</span></div>
    <div class="row"><span class="k">Reply-To</span><span class="v"><?= $e($cfg['reply_to']) ?></span></div>
    <div class="row"><span class="k">BCC (ops copy)</span><span class="v"><?= $cfg['bcc'] !== '' ? $e($cfg['bcc']) : '&mdash;' ?></span></div>
    <div class="row"><span class="k">openssl extension</span><span class="v"><?= extension_loaded('openssl') ? '<span class="pill ok">LOADED</span>' : '<span class="pill bad">MISSING</span>' ?></span></div>
  </div>

  <?php if ($result !== null): ?>
  <div class="card">
    <?php if ($result['ok']): ?>
      <p><span class="pill ok">SENT</span> &nbsp;Test email delivered to <strong><?= $e($_GET['send']) ?></strong>. Check the inbox (and spam folder).</p>
    <?php else: ?>
      <p><span class="pill bad">FAILED</span> &nbsp;<?= $e($result['error']) ?></p>
      <p style="font-size:13px;color:#64748B;margin:8px 0 0;">Common causes: wrong password (use an App Password for Gmail), wrong port/encryption combo (465&rarr;ssl, 587&rarr;tls), or the host blocking outbound SMTP. Set <code>"debug": true</code> in the config to record the full conversation in the log below.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <input type="email" name="send" placeholder="your@email.com" required value="<?= $e($_GET['send'] ?? '') ?>">
      <button type="submit">Send test email</button>
      <a class="btn sec" href="?preview=1" target="_blank">Preview in browser</a>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 10px;font-size:15px;">Recent email activity</h3>
    <?php if (!$logs): ?><p style="color:#64748B;font-size:14px;margin:0;">No email activity yet.</p>
    <?php else: ?>
    <table><tr><th>Time</th><th>Event</th><th>To</th><th>Order</th><th>Detail</th></tr>
      <?php foreach (array_reverse($logs) as $l): ?>
      <tr>
        <td><?= $e($l['timestamp'] ?? '') ?></td>
        <td><span class="pill <?= ($l['event'] ?? '') === 'sent' ? 'ok' : ((($l['event'] ?? '') === 'failed') ? 'bad' : 'warn') ?>"><?= $e(strtoupper($l['event'] ?? '')) ?></span></td>
        <td><?= $e($l['to'] ?? '') ?></td>
        <td><?= $e($l['order_id'] ?? '') ?></td>
        <td style="color:#64748B;"><?= $e($l['error'] ?? $l['reason'] ?? (isset($l['ms']) ? $l['ms'].' ms' : '')) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
</body></html>
