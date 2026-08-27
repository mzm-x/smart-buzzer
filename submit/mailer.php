<?php
/**
 * ============================================================================
 * File: /submit/mailer.php
 * Smart Buzzer — Self-contained SMTP mailer (no Composer dependency)
 * ----------------------------------------------------------------------------
 * WHY THIS EXISTS
 * A client disputed a charge because after submitting the onboarding form he
 * received NOTHING in writing — no order ID, no timeline, no proof. This file
 * sends an automatic order-confirmation email the moment an order lands.
 *
 * DESIGN NOTES
 * - Zero Composer deps (deployment is manual file-by-file via cPanel).
 * - Credentials live in /submit/data/smtp-config.json (gitignored, same
 *   pattern as wa-config.json). Falls back to the SMTP_* constants in
 *   config.php. If neither is configured, sending is a silent no-op — it must
 *   NEVER break order submission.
 * - Supports port 465 (implicit SSL) and 587 (STARTTLS), AUTH LOGIN + PLAIN.
 * - Every attempt is appended to /submit/data/email_log.json.
 *
 * PUBLIC API
 *   sbMailerConfig()                  -> array config (+ 'enabled' flag)
 *   sbSendOrderConfirmation($order)   -> bool  (fire-and-forget safe)
 *   sbSmtpSend($to, $subj, $html, $text, $opt = []) -> array{ok,error}
 * ============================================================================
 */

if (!defined('SB_MAILER_LOADED')) {
    define('SB_MAILER_LOADED', true);

// ============================================================================
// CONFIG
// ============================================================================

function sbMailerConfig() {
    static $cfg = null;
    if ($cfg !== null) return $cfg;

    $cfg = [
        'enabled'    => false,
        'host'       => defined('SMTP_HOST') ? SMTP_HOST : '',
        'port'       => defined('SMTP_PORT') ? (int)SMTP_PORT : 587,
        'secure'     => '',            // 'ssl' | 'tls' | '' (auto by port)
        'username'   => defined('SMTP_USERNAME') ? SMTP_USERNAME : '',
        'password'   => defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '',
        'from_email' => defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@smart-buzzer.com',
        'from_name'  => defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Smart Buzzer',
        'reply_to'   => defined('BUSINESS_EMAIL') ? BUSINESS_EMAIL : 'contact@smart-buzzer.com',
        'bcc'        => '',            // internal copy for ops (optional)
        'timeout'    => 12,
        'debug'      => false,
    ];

    $file = __DIR__ . '/data/smtp-config.json';
    if (is_readable($file)) {
        $json = json_decode((string)file_get_contents($file), true);
        if (is_array($json)) {
            foreach ($cfg as $k => $v) {
                if (array_key_exists($k, $json) && $json[$k] !== '') {
                    $cfg[$k] = is_int($v) ? (int)$json[$k] : $json[$k];
                }
            }
            if (isset($json['enabled'])) $cfg['enabled'] = (bool)$json['enabled'];
        }
    }

    // Auto-detect encryption from port when not explicitly set
    if ($cfg['secure'] === '') {
        $cfg['secure'] = ((int)$cfg['port'] === 465) ? 'ssl' : 'tls';
    }
    $cfg['secure'] = strtolower((string)$cfg['secure']);
    if (!in_array($cfg['secure'], ['ssl', 'tls', 'none'], true)) $cfg['secure'] = 'tls';

    // Hard requirement: host + username + password, otherwise stay disabled
    if ($cfg['host'] === '' || $cfg['username'] === '' || $cfg['password'] === '') {
        $cfg['enabled'] = false;
    }

    return $cfg;
}

// ============================================================================
// LOGGING
// ============================================================================

function sbMailLog($entry) {
    $dir  = __DIR__ . '/data';
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    $file = $dir . '/email_log.json';

    $entry = array_merge(['timestamp' => date('Y-m-d H:i:s')], $entry);

    $fp = @fopen($file, 'c+');
    if (!$fp) return;
    if (@flock($fp, LOCK_EX)) {
        $raw = '';
        while (!feof($fp)) { $raw .= fread($fp, 8192); }
        $logs = json_decode($raw, true);
        if (!is_array($logs)) $logs = [];
        $logs[] = $entry;
        if (count($logs) > 500) $logs = array_slice($logs, -500);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// ============================================================================
// LOW-LEVEL SMTP
// ============================================================================

class SB_Smtp
{
    private $sock = null;
    private $cfg;
    private $trace = [];

    public function __construct(array $cfg) { $this->cfg = $cfg; }

    public function getTrace() { return $this->trace; }

    private function read()
    {
        $out = '';
        while (($line = fgets($this->sock, 1024)) !== false) {
            $out .= $line;
            // Last line of a multiline reply has a space at position 4
            if (strlen($line) < 4 || $line[3] !== '-') break;
        }
        if ($out === '') {
            $meta = stream_get_meta_data($this->sock);
            if (!empty($meta['timed_out'])) {
                $this->trace[] = 'S: [no reply — read timed out]';
                throw new Exception('No reply from server (read timed out). '
                    . 'The mail host accepted the message body but never answered.');
            }
            $this->trace[] = 'S: [connection closed by server]';
        } else {
            $this->trace[] = 'S: ' . trim($out);
        }
        return $out;
    }

    private function cmd($cmd, $expect, $hide = false)
    {
        $this->trace[] = 'C: ' . ($hide ? '***' : trim($cmd));
        fwrite($this->sock, $cmd . "\r\n");
        $res  = $this->read();
        $code = (int)substr($res, 0, 3);
        if (!in_array($code, (array)$expect, true)) {
            throw new Exception('SMTP ' . trim($cmd === '' ? '(data)' : explode(' ', $cmd)[0])
                . ' failed: ' . trim($res));
        }
        return $res;
    }

    public function send($from, $fromName, $recipients, $rawMessage)
    {
        $c       = $this->cfg;
        $timeout = (int)$c['timeout'];
        $host    = $c['host'];
        $port    = (int)$c['port'];
        $prefix  = ($c['secure'] === 'ssl') ? 'ssl://' : '';

        $ctx = stream_context_create([
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'SNI_enabled' => true],
        ]);

        $errno = 0; $errstr = '';
        $this->sock = @stream_socket_client(
            $prefix . $host . ':' . $port,
            $errno, $errstr, $timeout,
            STREAM_CLIENT_CONNECT, $ctx
        );
        if (!$this->sock) {
            throw new Exception("Connect failed to {$host}:{$port} — {$errstr} ({$errno})");
        }
        stream_set_timeout($this->sock, $timeout);

        $greet = $this->read();
        if ((int)substr($greet, 0, 3) !== 220) {
            throw new Exception('Bad greeting: ' . trim($greet));
        }

        $ehloName = $this->ehloName();
        $caps = $this->cmd('EHLO ' . $ehloName, [250]);

        if ($c['secure'] === 'tls') {
            $this->cmd('STARTTLS', [220]);
            $ok = @stream_socket_enable_crypto(
                $this->sock, true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
                | (defined('STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT : 0)
                | (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : 0)
            );
            if (!$ok) throw new Exception('STARTTLS handshake failed');
            $caps = $this->cmd('EHLO ' . $ehloName, [250]);
        }

        $this->auth($caps);

        $this->cmd('MAIL FROM:<' . $from . '>', [250]);
        foreach ($recipients as $rcpt) {
            $this->cmd('RCPT TO:<' . $rcpt . '>', [250, 251]);
        }
        $this->cmd('DATA', [354]);

        // Dot-stuffing (RFC 5321 §4.5.2)
        $body = preg_replace('/^\./m', '..', $rawMessage);
        $this->trace[] = 'C: [message body ' . strlen($body) . ' bytes]';
        fwrite($this->sock, $body . "\r\n.\r\n");

        // The server scans the message before it answers — Hostinger regularly takes
        // 15-30s on a full HTML mail. A 12s command timeout reads that as silence and
        // reports a phantom rejection, so the accept gets its own longer budget.
        stream_set_timeout($this->sock, max(45, $timeout));
        $res = $this->read();
        stream_set_timeout($this->sock, $timeout);
        if ((int)substr($res, 0, 3) !== 250) {
            throw new Exception('Message rejected: ' . trim($res));
        }

        @fwrite($this->sock, "QUIT\r\n");
        @fclose($this->sock);
        $this->sock = null;
        return true;
    }

    private function ehloName()
    {
        $h = $_SERVER['SERVER_NAME'] ?? gethostname();
        if (!$h || !preg_match('/^[A-Za-z0-9.\-]+$/', $h)) $h = 'smart-buzzer.com';
        return $h;
    }

    private function auth($caps)
    {
        $c    = $this->cfg;
        $caps = strtoupper($caps);

        if (strpos($caps, 'AUTH') !== false && strpos($caps, 'PLAIN') !== false
            && strpos($caps, 'LOGIN') === false) {
            $this->cmd('AUTH PLAIN ' . base64_encode("\0" . $c['username'] . "\0" . $c['password']), [235], true);
            return;
        }

        try {
            $this->cmd('AUTH LOGIN', [334]);
            $this->cmd(base64_encode($c['username']), [334], true);
            $this->cmd(base64_encode($c['password']), [235], true);
        } catch (Exception $e) {
            // Fallback for servers that only advertise PLAIN
            $this->cmd('AUTH PLAIN ' . base64_encode("\0" . $c['username'] . "\0" . $c['password']), [235], true);
        }
    }
}

// ============================================================================
// MESSAGE BUILDER + PUBLIC SEND
// ============================================================================

function sbBuildMessage($cfg, $to, $subject, $html, $text, $opt = [])
{
    $eol      = "\r\n";
    $boundary = 'sb_' . bin2hex(random_bytes(12));
    $msgId    = '<' . bin2hex(random_bytes(12)) . '@smart-buzzer.com>';
    $encSubj  = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $fromName = '=?UTF-8?B?' . base64_encode($cfg['from_name']) . '?=';

    $h   = [];
    $h[] = 'Date: ' . date('r');
    $h[] = 'Message-ID: ' . $msgId;
    $h[] = 'From: ' . $fromName . ' <' . $cfg['from_email'] . '>';
    $h[] = 'To: <' . $to . '>';
    if (!empty($cfg['reply_to'])) $h[] = 'Reply-To: <' . $cfg['reply_to'] . '>';
    if (!empty($opt['order_id']))  $h[] = 'X-SB-Order-ID: ' . $opt['order_id'];
    $h[] = 'Subject: ' . $encSubj;
    $h[] = 'MIME-Version: 1.0';
    $h[] = 'X-Mailer: SmartBuzzer-Mailer/1.0';
    $h[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

    $b   = [];
    $b[] = '--' . $boundary;
    $b[] = 'Content-Type: text/plain; charset=UTF-8';
    $b[] = 'Content-Transfer-Encoding: base64';
    $b[] = '';
    $b[] = chunk_split(base64_encode($text), 76, $eol);
    $b[] = '--' . $boundary;
    $b[] = 'Content-Type: text/html; charset=UTF-8';
    $b[] = 'Content-Transfer-Encoding: base64';
    $b[] = '';
    $b[] = chunk_split(base64_encode($html), 76, $eol);
    $b[] = '--' . $boundary . '--';

    return implode($eol, $h) . $eol . $eol . implode($eol, $b);
}

/**
 * Send one email. Never throws — returns ['ok' => bool, 'error' => string].
 */
function sbSmtpSend($to, $subject, $html, $text, $opt = [])
{
    $cfg = sbMailerConfig();

    if (!$cfg['enabled']) {
        sbMailLog(['event' => 'skipped', 'to' => $to, 'subject' => $subject,
                   'reason' => 'SMTP not configured (data/smtp-config.json)']);
        return ['ok' => false, 'error' => 'SMTP not configured'];
    }
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        sbMailLog(['event' => 'skipped', 'to' => $to, 'reason' => 'invalid recipient']);
        return ['ok' => false, 'error' => 'Invalid recipient'];
    }

    $recipients = [$to];
    if (!empty($cfg['bcc']) && filter_var($cfg['bcc'], FILTER_VALIDATE_EMAIL)) {
        $recipients[] = $cfg['bcc'];
    }

    $raw   = sbBuildMessage($cfg, $to, $subject, $html, $text, $opt);
    $smtp  = new SB_Smtp($cfg);
    $start = microtime(true);

    try {
        $smtp->send($cfg['from_email'], $cfg['from_name'], $recipients, $raw);
        sbMailLog([
            'event'    => 'sent',
            'to'       => $to,
            'bcc'      => $cfg['bcc'] ?: '',
            'subject'  => $subject,
            'order_id' => $opt['order_id'] ?? '',
            'ms'       => (int)((microtime(true) - $start) * 1000),
        ]);
        return ['ok' => true, 'error' => ''];
    } catch (Exception $e) {
        sbMailLog([
            'event'    => 'failed',
            'to'       => $to,
            'subject'  => $subject,
            'order_id' => $opt['order_id'] ?? '',
            'error'    => $e->getMessage(),
            'trace'    => $cfg['debug'] ? $smtp->getTrace() : null,
            'ms'       => (int)((microtime(true) - $start) * 1000),
        ]);
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Build + send the order confirmation. Safe to call fire-and-forget.
 */
function sbSendOrderConfirmation($order)
{
    require_once __DIR__ . '/email-template.php';

    $to = trim($order['email'] ?? '');
    if ($to === '') return false;

    $mail = sbRenderOrderConfirmation($order);
    $res  = sbSmtpSend($to, $mail['subject'], $mail['html'], $mail['text'],
                       ['order_id' => $order['orderId'] ?? '']);
    return $res['ok'];
}

/**
 * Was a confirmation for this order already sent? (idempotency guard —
 * protects against double submit / retried requests.)
 */
function sbAlreadyConfirmed($orderId)
{
    if ($orderId === '' || $orderId === null) return false;
    $file = __DIR__ . '/data/email_log.json';
    if (!is_readable($file)) return false;
    $logs = json_decode((string)file_get_contents($file), true);
    if (!is_array($logs)) return false;
    foreach ($logs as $l) {
        if (($l['event'] ?? '') === 'sent' && ($l['order_id'] ?? '') === $orderId) return true;
    }
    return false;
}

/**
 * Queue the confirmation email to be sent AFTER the HTTP response is flushed.
 *
 * Order submission must never wait on (or fail because of) SMTP. jsonResponse()
 * calls exit, but shutdown functions still run — and we close the connection
 * first via fastcgi_finish_request()/litespeed_finish_request() where the host
 * supports it, so the customer sees the thank-you screen instantly.
 */
function sbQueueOrderConfirmation($order)
{
    if (empty($order['email'])) return;
    if (sbAlreadyConfirmed($order['orderId'] ?? '')) return;

    @ignore_user_abort(true);

    register_shutdown_function(function () use ($order) {
        if (function_exists('fastcgi_finish_request')) {
            @fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            @litespeed_finish_request();
        }
        try {
            sbSendOrderConfirmation($order);
        } catch (Throwable $e) {
            sbMailLog(['event' => 'failed', 'to' => $order['email'] ?? '',
                       'order_id' => $order['orderId'] ?? '', 'error' => 'shutdown: ' . $e->getMessage()]);
        }
    });
}

} // SB_MAILER_LOADED
