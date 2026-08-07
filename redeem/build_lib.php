<?php
/**
 * Smart Buzzer — /redeem shared build library
 * -------------------------------------------------------------------------
 * Single source of truth for turning the CSV sources into redeem_data.json.
 *
 * Used by:
 *   build_data.php  (CLI)
 *   submit.php      (admin UI — rebuilds after every save)
 *
 * Sources, in ascending priority (later overrides earlier on email+label):
 *   1. data/single.csv   business,email,remaining                (label = business)
 *   2. data/multi.csv    business,label,email,remaining
 *   3. data/tracker.csv  Late Debt Tracker export (header mapped by name)
 *   4. data/manual.csv   business,label,email,remaining,action,updated_at
 *
 * Merge semantics, keyed on strtolower(email) . '|' . strtolower(label):
 *   - WITHIN one file, several rows may share a key — a client can buy the same
 *     listing twice, so each row stays its own voucher. Byte-identical rows
 *     (same remaining) collapse to one.
 *   - ACROSS files, a later source REPLACES everything an earlier source said
 *     about that key. That's what makes manual.csv an override rather than an
 *     addition, and stops a fresh tracker export from double-counting orders
 *     already present in the legacy single/multi exports.
 * A manual row with action=remove suppresses that key entirely.
 *
 * Never crashes on a malformed row: it is skipped and counted.
 */

// ---------------------------------------------------------------------------
// Text helpers
// ---------------------------------------------------------------------------

if (!function_exists('sb_clean_text')) {
/** Trim, drop leading '=' / stray quote chars (spreadsheet artifacts), collapse internal whitespace. */
function sb_clean_text($s) {
    $s = trim((string)$s);
    $s = preg_replace('/^\xEF\xBB\xBF/', '', $s);          // UTF-8 BOM
    $s = ltrim($s, "= \t\"'");                              // Google Sheets ="..." export
    $s = trim($s, "\"'");
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}
}

if (!function_exists('sb_parse_remaining')) {
/** Return non-negative int, or null if not a clean non-negative integer. */
function sb_parse_remaining($raw) {
    $r = str_replace(',', '', trim((string)$raw));          // tolerate "1,000"
    if ($r === '' || !preg_match('/^\d+$/', $r)) {
        return null;
    }
    return (int)$r;
}
}

if (!function_exists('sb_extract_emails')) {
/** Split an email cell into normalized candidate addresses that contain '@'. */
function sb_extract_emails($raw) {
    $parts = preg_split('/[\s,;]+/', trim((string)$raw), -1, PREG_SPLIT_NO_EMPTY);
    $out = array();
    foreach ($parts as $p) {
        $p = strtolower(trim($p, " \t\"'<>"));
        if ($p !== '' && strpos($p, '@') !== false) {
            $out[$p] = true;                                 // dedupe within the same cell
        }
    }
    return array_keys($out);
}
}

if (!function_exists('sb_voucher_code')) {
/** Deterministic voucher: SB- + first 6 hex chars (upper) of md5(email|label|remaining). */
function sb_voucher_code($email, $label, $remaining) {
    return 'SB-' . strtoupper(substr(md5($email . '|' . $label . '|' . $remaining), 0, 6));
}
}

if (!function_exists('sb_key')) {
/** Dedup key for an order. */
function sb_key($email, $label) {
    return strtolower(trim($email)) . '|' . strtolower(trim($label));
}
}

// ---------------------------------------------------------------------------
// CSV reading
// ---------------------------------------------------------------------------

if (!function_exists('sb_read_csv')) {
/**
 * Read a CSV into ['header' => [name => index], 'rows' => [[...], ...]].
 * $headerNeedle: if given, scan the first $scanDepth lines for the real header row
 * (the Late Debt Tracker export has a group-header row above the real one).
 * Returns null when the file is missing/unreadable/empty.
 */
function sb_read_csv($path, $headerNeedle = null, $scanDepth = 8) {
    if (!is_file($path)) { return null; }
    $fh = @fopen($path, 'r');
    if ($fh === false) { return null; }

    $col    = array();
    $found  = false;
    $probes = 0;

    while (($row = fgetcsv($fh)) !== false) {
        $probes++;
        $map = array();
        foreach ($row as $i => $name) {
            $key = strtolower(sb_clean_text($name));
            if ($key !== '' && !isset($map[$key])) { $map[$key] = $i; }
        }
        if ($headerNeedle === null) {
            $col = $map; $found = true; break;
        }
        if (isset($map[strtolower($headerNeedle)])) {
            $col = $map; $found = true; break;
        }
        if ($probes >= $scanDepth) { break; }
    }

    if (!$found) { fclose($fh); return null; }

    $rows = array();
    while (($row = fgetcsv($fh)) !== false) {
        if ($row === array(null)) { continue; }
        if (count($row) === 1 && trim((string)$row[0]) === '') { continue; }
        $rows[] = $row;
    }
    fclose($fh);

    return array('header' => $col, 'rows' => $rows);
}
}

if (!function_exists('sb_cell')) {
/** Safe column-name lookup on a row. */
function sb_cell(array $col, $row, $key) {
    $key = strtolower($key);
    return (isset($col[$key]) && isset($row[$col[$key]])) ? $row[$col[$key]] : '';
}
}

// ---------------------------------------------------------------------------
// Tracker eligibility (Late Debt Tracker export)
// ---------------------------------------------------------------------------

if (!function_exists('sb_tracker_skip_reason')) {
/**
 * Business rules — which tracker rows may NOT hand out a balance voucher.
 * Returns a short reason string, or null when the row is eligible.
 *
 * Excluded: refunds already executed or approved, disputed orders,
 * campaigns the client asked to stop, and finished campaigns.
 */
function sb_tracker_skip_reason(array $col, $row) {
    $lc = function ($v) { return strtolower(sb_clean_text($v)); };

    // Header cell is "Executed\nRefund" in the sheet; sb_clean_text collapses it.
    if ($lc(sb_cell($col, $row, 'Executed Refund')) === 'yes') { return 'refund-executed'; }

    if ($lc(sb_cell($col, $row, 'Approval')) === 'approved') { return 'refund-approved'; }

    $overall = $lc(sb_cell($col, $row, 'Overall Status (Biz)'));
    if (strpos($overall, 'disputed') !== false)   { return 'disputed'; }
    if (strpos($overall, 'clean debt') !== false) { return 'clean-debt'; }
    if (strpos($overall, 'request to stop') !== false || strpos($overall, 'req to stop') !== false) {
        return 'client-stopped';
    }

    $notesBiz = $lc(sb_cell($col, $row, 'Notes Biz'));
    if (strpos($notesBiz, 'request to stop') !== false || strpos($notesBiz, 'req to stop') !== false) {
        return 'client-stopped';
    }

    if ($lc(sb_cell($col, $row, 'Campaign Status')) === 'finished') { return 'finished'; }

    return null;
}
}

// ---------------------------------------------------------------------------
// Build
// ---------------------------------------------------------------------------

if (!function_exists('sb_redeem_build')) {
/**
 * Rebuild redeem_data.json from every CSV source present.
 * Returns a summary array (also usable for the admin UI + CLI output).
 */
function sb_redeem_build($baseDir) {
    $dataDir = $baseDir . '/data';
    $outFile = $baseDir . '/redeem_data.json';

    $byKey    = array();   // key => [order, ...] — a later source replaces the whole bucket
    $removed  = array();   // key => true (manual action=remove)
    $stats    = array(
        'single'  => array('rows' => 0, 'orders' => 0, 'status' => 'missing'),
        'multi'   => array('rows' => 0, 'orders' => 0, 'status' => 'missing'),
        'tracker' => array('rows' => 0, 'orders' => 0, 'status' => 'missing'),
        'manual'  => array('rows' => 0, 'orders' => 0, 'status' => 'missing'),
    );
    $skipNoEmail      = 0;
    $skipBadRemaining = 0;
    $skipZero         = 0;
    $skipIneligible   = array();   // reason => count

    // Rows accumulated for the source currently being read. Flushed into $byKey
    // (replacing whatever earlier sources put there) once the file is done.
    $pending = array();

    $put = function ($business, $label, $emails, $remaining, &$countRef) use (&$pending) {
        foreach ($emails as $email) {
            $key  = sb_key($email, $label);
            $code = sb_voucher_code($email, $label, $remaining);
            if (!isset($pending[$key])) { $pending[$key] = array(); }
            if (isset($pending[$key][$code])) { continue; }   // identical row already seen
            $pending[$key][$code] = array(
                'email'     => $email,
                'business'  => $business,
                'label'     => $label,
                'remaining' => $remaining,
                'amount'    => $remaining * 5,
                'code'      => $code,
            );
            $countRef++;
        }
    };

    $flush = function () use (&$pending, &$byKey) {
        foreach ($pending as $key => $bucket) {
            $byKey[$key] = array_values($bucket);            // replace, never append
        }
        $pending = array();
    };

    // --- 1 + 2. single.csv / multi.csv (legacy exports) ---------------------
    foreach (array('single', 'multi') as $type) {
        $csv = sb_read_csv($dataDir . '/' . $type . '.csv');
        if ($csv === null) { continue; }
        $stats[$type]['status'] = 'ok';
        $col = $csv['header'];

        foreach ($csv['rows'] as $row) {
            $stats[$type]['rows']++;
            try {
                $business = sb_clean_text(sb_cell($col, $row, 'business'));
                $label    = ($type === 'multi') ? sb_clean_text(sb_cell($col, $row, 'label')) : $business;
                if ($label === '') { $label = $business; }

                $emails = sb_extract_emails(sb_cell($col, $row, 'email'));
                if (empty($emails)) { $skipNoEmail++; continue; }

                $remaining = sb_parse_remaining(sb_cell($col, $row, 'remaining'));
                if ($remaining === null) { $skipBadRemaining++; continue; }

                $put($business, $label, $emails, $remaining, $stats[$type]['orders']);
            } catch (\Throwable $e) {
                $skipBadRemaining++;
            }
        }
        $flush();
    }

    // --- 3. tracker.csv (Late Debt Tracker export) --------------------------
    $csv = sb_read_csv($dataDir . '/tracker.csv', 'Mission Names');
    if ($csv !== null) {
        $stats['tracker']['status'] = 'ok';
        $col = $csv['header'];

        foreach ($csv['rows'] as $row) {
            $stats['tracker']['rows']++;
            try {
                $business = sb_clean_text(sb_cell($col, $row, 'Client Name'));
                $label    = sb_clean_text(sb_cell($col, $row, 'Mission Names'));
                if ($label === '')    { $label = $business; }
                if ($business === '') { $business = $label; }
                if ($label === '')    { continue; }           // fully blank row

                $emails = sb_extract_emails(sb_cell($col, $row, 'Email'));
                if (empty($emails)) { $skipNoEmail++; continue; }

                $remaining = sb_parse_remaining(sb_cell($col, $row, 'Units Remaining'));
                if ($remaining === null) { $skipBadRemaining++; continue; }
                if ($remaining <= 0)     { $skipZero++; continue; }

                $reason = sb_tracker_skip_reason($col, $row);
                if ($reason !== null) {
                    if (!isset($skipIneligible[$reason])) { $skipIneligible[$reason] = 0; }
                    $skipIneligible[$reason]++;
                    continue;
                }

                $put($business, $label, $emails, $remaining, $stats['tracker']['orders']);
            } catch (\Throwable $e) {
                $skipBadRemaining++;
            }
        }
        $flush();
    }

    // --- 4. manual.csv (admin submissions — highest priority) ---------------
    $csv = sb_read_csv($dataDir . '/manual.csv');
    if ($csv !== null) {
        $stats['manual']['status'] = 'ok';
        $col = $csv['header'];

        foreach ($csv['rows'] as $row) {
            $stats['manual']['rows']++;
            try {
                $business = sb_clean_text(sb_cell($col, $row, 'business'));
                $label    = sb_clean_text(sb_cell($col, $row, 'label'));
                if ($label === '')    { $label = $business; }
                if ($business === '') { $business = $label; }

                $emails = sb_extract_emails(sb_cell($col, $row, 'email'));
                if (empty($emails)) { $skipNoEmail++; continue; }

                $action = strtolower(sb_clean_text(sb_cell($col, $row, 'action')));
                if ($action === 'remove') {
                    foreach ($emails as $email) {
                        $k = sb_key($email, $label);
                        unset($pending[$k]);
                        $removed[$k] = true;
                    }
                    continue;
                }

                $remaining = sb_parse_remaining(sb_cell($col, $row, 'remaining'));
                if ($remaining === null) { $skipBadRemaining++; continue; }

                foreach ($emails as $email) { unset($removed[sb_key($email, $label)]); }
                $put($business, $label, $emails, $remaining, $stats['manual']['orders']);
            } catch (\Throwable $e) {
                $skipBadRemaining++;
            }
        }
        $flush();
        foreach (array_keys($removed) as $k) { unset($byKey[$k]); }
    }

    // --- Emit --------------------------------------------------------------
    $orders = array();
    foreach ($byKey as $bucket) {
        foreach ($bucket as $o) { $orders[] = $o; }
    }
    usort($orders, function ($a, $b) {
        $c = strcasecmp($a['business'], $b['business']);
        if ($c !== 0) { return $c; }
        $c = strcasecmp($a['label'], $b['label']);
        if ($c !== 0) { return $c; }
        $c = strcmp($a['email'], $b['email']);
        return $c !== 0 ? $c : ($b['remaining'] - $a['remaining']);
    });

    $distinct = array();
    $totalAmount = 0;
    foreach ($orders as $o) {
        $distinct[$o['email']] = true;
        $totalAmount += $o['amount'];
    }

    $payload = array(
        'generated_at' => date('Y-m-d'),
        'count'        => count($orders),
        'orders'       => $orders,
    );
    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return array('ok' => false, 'error' => 'json_encode failed: ' . json_last_error_msg());
    }
    if (@file_put_contents($outFile, $json . "\n", LOCK_EX) === false) {
        return array('ok' => false, 'error' => 'failed to write ' . $outFile);
    }

    return array(
        'ok'              => true,
        'outFile'         => $outFile,
        'sources'         => $stats,
        'orders'          => count($orders),
        'distinctEmails'  => count($distinct),
        'totalAmount'     => $totalAmount,
        'suppressed'      => count($removed),
        'skipNoEmail'     => $skipNoEmail,
        'skipBadRemaining'=> $skipBadRemaining,
        'skipZero'        => $skipZero,
        'skipIneligible'  => $skipIneligible,
    );
}
}
