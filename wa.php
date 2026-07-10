<?php
/**
 * wa.php — WhatsApp cross-sell preview + sender (Smart Buzzer "Show-Up" Google Reviews)
 * Audience: existing / past clients (warm). CSV has only Nama · Email · Phone.
 * ONE photo only: before/after (ba.webp).
 *
 * Usage:
 *   /wa.php                → preview the message as a WhatsApp chat mockup
 *   /wa.php?to=15512000898 → same, but the button opens a chat to that number, prefilled
 */

// ---- Config ----------------------------------------------------------------
$WA_FROM   = '628979133204'; // your sending number (for reference)
$PHOTO      = 'https://smart-buzzer.com/photos/ba2.webp'; // the ONE photo
$LP         = 'https://smart-buzzer.com/sw-google/';

// Optional: pass ?to=<number> to target a specific lead from your CSV
$to = isset($_GET['to']) ? preg_replace('/[^0-9]/', '', $_GET['to']) : '';

// ---- The opener message (Message A) ----------------------------------------
// Plain text with WhatsApp markup (*bold*). This is what actually gets sent.
$message = "Hey! It's *Smart Buzzer* 👋\n\n"
         . "We just opened something we think you'll want for your Google listing 👇\n\n"
         . "We get you Google reviews that *actually show up* on your profile — real accounts, natural 4-5★, custom comments, each backed by a *7-day free replacement*.\n\n"
         . "👆 That's a real client of ours: 2.5★ → 5.0★.\n\n"
         . "⚠️ We only take a few clients per city each month (so your reviews never compete with a neighbor's) — and this month is filling up fast.\n\n"
         . "Want me to send the details? Say *interested* to 628979133204 🙂";

// Build the wa.me link (prefilled). If ?to= is set it targets that chat.
$waLink = 'https://wa.me/' . ($to !== '' ? $to : '') . '?text=' . rawurlencode($message);

// Render *bold* → <b> for the on-screen bubble only (the sent text stays raw).
function wa_markup($t) {
    $t = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    $t = preg_replace('/\*(.+?)\*/s', '<b>$1</b>', $t);
    $t = nl2br($t);
    return $t;
}
$bubble = wa_markup($message);
$now = date('g:i A');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WA Preview — Smart Buzzer Show-Up Cross-Sell</title>
<style>
  * { box-sizing: border-box; }
  body {
    margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: #0b141a; color: #e9edef; display: flex; flex-direction: column; align-items: center;
    min-height: 100vh; padding: 24px 12px;
  }
  h1 { font-size: 16px; font-weight: 600; color: #8696a0; margin: 0 0 4px; }
  .sub { font-size: 12.5px; color: #667781; margin: 0 0 18px; text-align: center; max-width: 420px; line-height: 1.5; }

  /* Phone frame */
  .phone {
    width: 380px; max-width: 100%; border-radius: 26px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.5); border: 1px solid #1f2c33; background: #0b141a;
  }
  /* WhatsApp header */
  .wa-head {
    background: #202c33; padding: 10px 14px; display: flex; align-items: center; gap: 12px;
  }
  .wa-avatar { width: 40px; height: 40px; border-radius: 50%; background: #25d366; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #062e16; font-size: 15px; }
  .wa-name { font-size: 15px; font-weight: 600; color: #e9edef; }
  .wa-status { font-size: 12px; color: #8696a0; }

  /* Chat area — WhatsApp doodle background */
  .wa-chat {
    background-color: #0b141a;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40'%3E%3Cpath d='M0 39.5h40M39.5 0v40' stroke='%23131f27' stroke-width='.5'/%3E%3C/svg%3E");
    padding: 18px 14px 22px; min-height: 420px;
  }
  /* Outgoing bubble (green) */
  .msg {
    background: #005c4b; color: #e9edef; margin-left: auto; max-width: 90%;
    border-radius: 10px 2px 10px 10px; padding: 4px 6px 6px; position: relative;
    box-shadow: 0 1px 1px rgba(0,0,0,.2); font-size: 14.2px; line-height: 20px;
  }
  .msg img { width: 100%; display: block; border-radius: 8px; margin-bottom: 6px; }
  .msg .txt { padding: 2px 6px 0; }
  .msg .meta { text-align: right; font-size: 11px; color: #8fb9ab; padding: 2px 4px 0; }
  .msg .tick { color: #53bdeb; }
  .msg b { font-weight: 700; }

  /* Actions */
  .actions { width: 380px; max-width: 100%; margin-top: 18px; display: flex; flex-direction: column; gap: 10px; }
  .btn {
    display: block; text-align: center; text-decoration: none; padding: 13px 18px; border-radius: 10px;
    font-size: 15px; font-weight: 700;
  }
  .btn-send { background: #25d366; color: #052e16; }
  .btn-copy { background: #202c33; color: #e9edef; border: 1px solid #2a3942; cursor: pointer; font: inherit; font-weight: 700; }
  .hint { font-size: 11.5px; color: #667781; text-align: center; line-height: 1.6; }
  .hint code { background: #202c33; padding: 1px 6px; border-radius: 4px; color: #8696a0; }
</style>
</head>
<body>

  <h1>WhatsApp Preview — Show-Up Cross-Sell</h1>
  <p class="sub">This is exactly how <b>Message A (opener)</b> looks with the one photo. <?= $to !== '' ? 'Targeting <b>+'.htmlspecialchars($to).'</b>.' : 'Add <code>?to=NUMBER</code> to target a lead.' ?></p>

  <div class="phone">
    <div class="wa-head">
      <div class="wa-avatar">SB</div>
      <div>
        <div class="wa-name">Smart Buzzer</div>
        <div class="wa-status">business account</div>
      </div>
    </div>
    <div class="wa-chat">
      <div class="msg">
        <img src="<?= htmlspecialchars($PHOTO) ?>" alt="Before / after — 2.5★ to 5.0★ on Google">
        <div class="txt"><?= $bubble ?></div>
        <div class="meta"><?= $now ?> <span class="tick">✓✓</span></div>
      </div>
    </div>
  </div>

  <div class="actions">
    <a class="btn btn-send" href="<?= htmlspecialchars($waLink) ?>" target="_blank" rel="noopener">Open in WhatsApp &amp; send &rarr;</a>
    <button class="btn btn-copy" onclick="copyMsg()">Copy message text</button>
    <p class="hint">
      Send the <b>photo first</b>, then paste this text.<br>
      Sending number: <code><?= htmlspecialchars($WA_FROM) ?></code> &middot; Packages: <a href="<?= htmlspecialchars($LP) ?>" style="color:#53bdeb;">/sw-google/</a>
    </p>
  </div>

  <textarea id="raw" style="position:absolute;left:-9999px;top:-9999px;"><?= htmlspecialchars($message) ?></textarea>
  <script>
    function copyMsg(){
      var t = document.getElementById('raw');
      t.select(); t.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(t.value).then(function(){
        var b = document.querySelector('.btn-copy');
        var o = b.textContent; b.textContent = 'Copied ✓';
        setTimeout(function(){ b.textContent = o; }, 1500);
      });
    }
  </script>
</body>
</html>
