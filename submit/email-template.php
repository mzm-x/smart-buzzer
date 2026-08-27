<?php
/**
 * ============================================================================
 * File: /submit/email-template.php
 * Smart Buzzer — Order confirmation email (HTML + plain-text)
 * ----------------------------------------------------------------------------
 * Mirrors the /submit/ thank-you screen so the customer has the exact same
 * information in writing: order ID, what happens next (milestones), how to
 * confirm, and the terms they agreed to. This written trail is what we hand
 * a payment processor if an order is ever disputed.
 *
 * Table-based + inline CSS on purpose — Gmail/Outlook/iOS Mail safe.
 * All symbols are HTML entities (repo rule: no raw emoji in source).
 *
 *   sbRenderOrderConfirmation($order) -> ['subject','html','text']
 * ============================================================================
 */

if (!function_exists('sbRenderOrderConfirmation')) {

function sbMailEsc($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/**
 * Account Manager WhatsApp line — HARDCODED, matching submit.php.
 * This email goes out AFTER payment, so it must reach the AM, not the public sales
 * line in wa-config.json. Reading that file here would mean a stale copy on the
 * server could point paying customers at the wrong number. Change it in both places:
 * this file and submit.php (line 7).
 */
define('SB_MAIL_AM_NUMBER',  '6287870707202');
define('SB_MAIL_AM_DISPLAY', '+62 878-7070-7202');

function sbMailWaNumber()  { return SB_MAIL_AM_NUMBER; }
function sbMailWaDisplay() { return SB_MAIL_AM_DISPLAY; }

/**
 * The 5 milestones: [title, descriptionHtml, state, highlight].
 * Index 1 is the current position ("we're here"). The description is authored
 * here, so a little inline markup is allowed — it is never customer input.
 * highlight = true renders the row in an amber callout box; used for the one
 * expectation customers get wrong most often (the weekend approval delay).
 */
function sbMailMilestones($isSocial = false) {
    if ($isSocial) {
        return [
            ['Order received',                     'We have your details and your order is in the queue.',      'done', false],
            ['Our ops team is preparing delivery', 'This is where your order is right now.',                    'now',  false],
            ['Delivery starts',                    '<strong>Within 24-48 hours</strong> after order confirmation.', 'next', true],
            ['Gradual delivery in progress',       'Paced to look natural, large orders take longer.',          'next', false],
            ['Order complete',                     'You receive your full target after the stay-rate buffer.',  'next', false],
        ];
    }
    return [
        ['Order received',                          'We have your business details and your order is in the queue.', 'done', false],
        ['Our ops team is preparing your campaign', 'This is where your order is right now.',                        'now',  false],
        ['You&rsquo;ll receive your content draft for approval',
         '<strong>Within 24 hours.</strong> Please note approvals may be delayed over <strong>Saturday and Sunday</strong>, as our team is off on weekends.',
         'next', true],
        ['Your campaign goes live',                 'Right after you approve the content.',                          'next', false],
        ['Your first review appears',               '1-2 days after approval, then reviews keep showing up gradually.', 'next', false],
    ];
}

function sbRenderOrderConfirmation($order)
{
    $isSocial  = (($order['orderType'] ?? '') === 'social_media')
              || (($order['type'] ?? '') === 'social_media');

    $orderId   = trim($order['orderId'] ?? '');
    $email     = trim($order['email'] ?? '');
    $business  = trim($order['businessNames'] ?? ($order['businessName'] ?? ''));
    if ($business === '') $business = $isSocial ? trim($order['smLink'] ?? 'your account') : 'your business';
    $qty       = (int)($order['quantity'] ?? $order['smQuantity'] ?? 0);
    $product   = trim($order['productType'] ?? '');
    $platform  = trim($order['platform'] ?? ($order['smPlatform'] ?? 'Google'));
    if ($platform === 'Other') $platform = trim($order['customPlatform'] ?? 'Other');
    $service   = $isSocial ? trim($order['smService'] ?? 'Social Media') : ($product !== '' ? $product : 'Rating & Review');
    $submitted = trim($order['timestamp'] ?? date('Y-m-d H:i:s'));
    $submittedNice = date('M j, Y \a\t H:i', strtotime($submitted));

    $waNum     = sbMailWaNumber();
    $waDisplay = sbMailWaDisplay();
    $waText    = rawurlencode(
        "Hi Smart Buzzer, I'd like to confirm my order.\n"
        . "Order ID: {$orderId}\n"
        . "Business: {$business}\n"
        . "Email: {$email}"
    );
    $waLink    = "https://wa.me/{$waNum}?text={$waText}";

    $unit      = $isSocial ? 'units' : 'show-up reviews';
    $qtyLine   = $qty > 0 ? ($qty . ' ' . $unit) : '&mdash;';
    $milestones = sbMailMilestones($isSocial);

    $subject = 'Order Received' . ($orderId !== '' ? ' &mdash; ' . $orderId : '');
    $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8') . ' | Smart Buzzer';

    // ---------- colors ----------
    $BLUE = '#2563EB'; $INK = '#0F172A'; $MUTED = '#64748B';
    $LINE = '#E2E8F0'; $BG = '#F1F5F9'; $GREEN = '#16A34A'; $WA = '#25D366';

    // ---------- milestone rows ----------
    $rows = '';
    $last = count($milestones) - 1;
    foreach ($milestones as $i => $m) {
        list($title, $desc, $state) = $m;
        $highlight = !empty($m[3]);

        if ($state === 'done') {
            $dotBg = $GREEN; $dotBorder = $GREEN; $dotInner = '&#10003;'; $dotColor = '#FFFFFF';
            $titleColor = $INK; $titleWeight = '600';
        } elseif ($state === 'now') {
            $dotBg = $BLUE; $dotBorder = $BLUE; $dotInner = '&bull;'; $dotColor = '#FFFFFF';
            $titleColor = $BLUE; $titleWeight = '700';
        } else {
            $dotBg = '#FFFFFF'; $dotBorder = $LINE; $dotInner = '&nbsp;'; $dotColor = '#FFFFFF';
            $titleColor = $MUTED; $titleWeight = '600';
        }

        $badge = ($state === 'now')
            ? ' <span style="display:inline-block;background:#DBEAFE;color:' . $BLUE . ';font-size:11px;font-weight:700;letter-spacing:.4px;padding:2px 8px;border-radius:999px;vertical-align:middle;">YOU ARE HERE</span>'
            : '';
        $check = ($state === 'done')
            ? ' <span style="color:' . $GREEN . ';font-size:12px;font-weight:700;">DONE</span>'
            : '';

        $connector = ($i === $last)
            ? ''
            : '<div style="width:2px;height:26px;background:' . $LINE . ';margin:4px auto 0 auto;line-height:0;font-size:0;">&nbsp;</div>';

        $rows .= '
        <tr>
          <td width="34" valign="top" style="padding:0;">
            <div style="width:22px;height:22px;border-radius:50%;background:' . $dotBg . ';border:2px solid ' . $dotBorder . ';color:' . $dotColor . ';text-align:center;line-height:22px;font-size:12px;font-weight:700;">' . $dotInner . '</div>
            ' . $connector . '
          </td>
          <td valign="top" style="padding:0 0 ' . ($i === $last ? '0' : '18') . 'px 10px;">
            <div style="font-size:15px;font-weight:' . $titleWeight . ';color:' . $titleColor . ';line-height:1.35;">' . $title . $badge . $check . '</div>
            ' . ($highlight
                ? '<div style="font-size:13px;color:#92400E;line-height:1.55;margin-top:6px;background:#FFFBEB;border:1px solid #FDE68A;border-left:3px solid #F59E0B;border-radius:10px;padding:9px 12px;">' . $desc . '</div>'
                : '<div style="font-size:13px;color:' . $MUTED . ';line-height:1.5;margin-top:3px;">' . $desc . '</div>') . '
          </td>
        </tr>';
    }

    // ---------- HTML ----------
    $html = '<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>' . sbMailEsc($subject) . '</title>
</head>
<body style="margin:0;padding:0;background:' . $BG . ';">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">Your order ' . sbMailEsc($orderId) . ' is confirmed. Here is exactly what happens next and when.</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:' . $BG . ';padding:24px 12px;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#FFFFFF;border-radius:16px;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;">

  <tr><td align="center" style="padding:28px 24px 20px 24px;border-bottom:1px solid ' . $LINE . ';">
    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" width="150" style="display:block;height:auto;border:0;">
  </td></tr>

  <tr><td style="padding:28px 28px 8px 28px;">
    <div style="display:inline-block;background:#DCFCE7;color:#15803D;font-size:12px;font-weight:700;letter-spacing:.5px;padding:5px 12px;border-radius:999px;">&#10003; ORDER RECEIVED</div>
    <h1 style="margin:14px 0 6px 0;font-size:24px;line-height:1.25;color:' . $INK . ';font-weight:800;">We&rsquo;ve got your order</h1>
    <p style="margin:0;font-size:15px;line-height:1.6;color:' . $MUTED . ';">Thanks, <strong style="color:' . $INK . ';">' . sbMailEsc($business) . '</strong>. Your onboarding form is in and our ops team is on it. Below is your order reference and exactly what happens next &mdash; keep this email.</p>
  </td></tr>

  <tr><td style="padding:20px 28px 0 28px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F8FAFC;border:1px solid ' . $LINE . ';border-radius:12px;">
      <tr><td style="padding:16px 18px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;color:' . $INK . ';">
          <tr><td style="padding:5px 0;color:' . $MUTED . ';width:42%;">Order ID</td><td style="padding:5px 0;font-weight:700;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">' . sbMailEsc($orderId !== '' ? $orderId : '-') . '</td></tr>
          <tr><td style="padding:5px 0;color:' . $MUTED . ';">Submitted</td><td style="padding:5px 0;font-weight:600;">' . sbMailEsc($submittedNice) . '</td></tr>
          <tr><td style="padding:5px 0;color:' . $MUTED . ';">Business</td><td style="padding:5px 0;font-weight:600;">' . sbMailEsc($business) . '</td></tr>
          <tr><td style="padding:5px 0;color:' . $MUTED . ';">Service</td><td style="padding:5px 0;font-weight:600;">' . sbMailEsc($service) . ' &middot; ' . sbMailEsc($platform) . '</td></tr>
          <tr><td style="padding:5px 0;color:' . $MUTED . ';">Quantity</td><td style="padding:5px 0;font-weight:600;">' . $qtyLine . '</td></tr>
        </table>
      </td></tr>
    </table>
  </td></tr>

  <tr><td style="padding:26px 28px 0 28px;">
    <div style="font-size:12px;font-weight:800;letter-spacing:1.2px;color:' . $MUTED . ';">WHAT HAPPENS NEXT</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:16px;">' . $rows . '</table>
  </td></tr>

  <tr><td style="padding:26px 28px 0 28px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr><td align="center" style="background:' . $WA . ';border-radius:12px;">
        <a href="' . sbMailEsc($waLink) . '" style="display:block;padding:16px 20px;font-size:16px;font-weight:700;color:#FFFFFF;text-decoration:none;">&#10003;&nbsp; Confirm my order on WhatsApp</a>
      </td></tr>
    </table>
    <p style="margin:10px 0 0 0;text-align:center;font-size:13px;color:' . $MUTED . ';line-height:1.5;">Send us one message and we&rsquo;ll prioritise your slot &mdash; it&rsquo;s also how we deliver your review content preview.</p>
  </td></tr>

  <tr><td style="padding:24px 28px 0 28px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;">
      <tr><td align="center" style="padding:18px;">
        <div style="display:inline-block;background:#F59E0B;color:#FFFFFF;font-size:11px;font-weight:800;letter-spacing:.6px;padding:4px 10px;border-radius:999px;">&#127873; BONUS &mdash; LOYAL CLIENT EXCLUSIVE</div>
        <div style="margin-top:10px;font-size:22px;font-weight:800;color:#92400E;">FREE $20 Voucher</div>
        <div style="font-size:14px;color:#B45309;font-weight:600;">for your second purchase</div>
        <div style="margin-top:6px;font-size:13px;color:#92400E;line-height:1.5;">Already attached to your account &mdash; we apply it automatically on your next order. No code needed.</div>
      </td></tr>
    </table>
  </td></tr>

  <tr><td style="padding:24px 28px 0 28px;">
    <div style="border-top:1px solid ' . $LINE . ';padding-top:18px;">
      <div style="font-size:12px;font-weight:800;letter-spacing:1.2px;color:' . $MUTED . ';">A QUICK REMINDER OF THE TERMS YOU AGREED TO</div>
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:10px;font-size:13px;color:' . $MUTED . ';line-height:1.6;">
        <tr><td valign="top" style="padding:3px 8px 3px 0;color:' . $BLUE . ';font-weight:700;">&bull;</td><td style="padding:3px 0;">A review counts as delivered the moment it shows up on your listing, and each review carries its own <strong style="color:' . $INK . ';">7-day replacement window</strong> starting the day it appears.</td></tr>
        <tr><td valign="top" style="padding:3px 8px 3px 0;color:' . $BLUE . ';font-weight:700;">&bull;</td><td style="padding:3px 0;">Once the campaign has started, the order is final and cannot be cancelled.</td></tr>
        <tr><td valign="top" style="padding:3px 8px 3px 0;color:' . $BLUE . ';font-weight:700;">&bull;</td><td style="padding:3px 0;">Approved refunds are issued as a store voucher for future orders &mdash; not cash.</td></tr>
      </table>
      <p style="margin:12px 0 0 0;font-size:13px;color:' . $MUTED . ';line-height:1.6;">Something looks wrong or you need to change a detail? Reply to this email or message us on WhatsApp <strong style="color:' . $INK . ';">' . sbMailEsc($waDisplay) . '</strong> before your content is approved &mdash; we&rsquo;ll sort it out.</p>
    </div>
  </td></tr>

  <tr><td align="center" style="padding:26px 28px 30px 28px;">
    <div style="font-size:12px;color:#94A3B8;line-height:1.7;">
      Smart Buzzer &middot; a subsidiary of Pintarnya<br>
      <a href="https://smart-buzzer.com" style="color:' . $BLUE . ';text-decoration:none;">smart-buzzer.com</a> &nbsp;&middot;&nbsp;
      <a href="mailto:contact@smart-buzzer.com" style="color:' . $BLUE . ';text-decoration:none;">contact@smart-buzzer.com</a><br>
      This email was sent to ' . sbMailEsc($email) . ' because an order was placed with this address.
    </div>
  </td></tr>

</table>
</td></tr>
</table>
</body></html>';

    // ---------- plain text ----------
    $t   = [];
    $t[] = 'ORDER RECEIVED - Smart Buzzer';
    $t[] = str_repeat('=', 46);
    $t[] = '';
    $t[] = 'Thanks, ' . $business . '. Your onboarding form is in and our ops';
    $t[] = 'team is on it. Keep this email as your order reference.';
    $t[] = '';
    $t[] = 'Order ID  : ' . ($orderId !== '' ? $orderId : '-');
    $t[] = 'Submitted : ' . $submittedNice;
    $t[] = 'Business  : ' . $business;
    $t[] = 'Service   : ' . $service . ' - ' . $platform;
    $t[] = 'Quantity  : ' . ($qty > 0 ? $qty . ' ' . $unit : '-');
    $t[] = '';
    $t[] = 'WHAT HAPPENS NEXT';
    $t[] = str_repeat('-', 46);
    foreach ($milestones as $m) {
        $mark  = $m[2] === 'done' ? '[x]' : ($m[2] === 'now' ? '[>]' : '[ ]');
        $plain = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
        $t[]   = $mark . ' ' . trim(html_entity_decode(strip_tags($m[0]), ENT_QUOTES, 'UTF-8'))
               . ($m[2] === 'now' ? '  <-- YOU ARE HERE' : '');
        if (!empty($m[3])) {
            $t[] = '    >> ' . $plain;
        } else {
            $t[] = '    ' . $plain;
        }
    }
    $t[] = '';
    $t[] = 'CONFIRM YOUR ORDER (gets your slot prioritised):';
    $t[] = $waLink;
    $t[] = '';
    $t[] = 'BONUS: FREE $20 voucher for your second purchase - applied';
    $t[] = 'automatically on your next order, no code needed.';
    $t[] = '';
    $t[] = 'REMINDER OF THE TERMS YOU AGREED TO';
    $t[] = str_repeat('-', 46);
    $t[] = '- A review counts as delivered the moment it shows up, and each';
    $t[] = '  review has its own 7-day replacement window from that day.';
    $t[] = '- Once the campaign has started, the order is final.';
    $t[] = '- Approved refunds are issued as a store voucher, not cash.';
    $t[] = '';
    $t[] = 'Questions? Reply to this email or WhatsApp ' . $waDisplay;
    $t[] = 'Smart Buzzer - smart-buzzer.com - contact@smart-buzzer.com';

    return [
        'subject' => $subject,
        'html'    => $html,
        'text'    => implode("\n", $t),
    ];
}

} // function_exists guard
