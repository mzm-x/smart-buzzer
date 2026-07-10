<?php
/**
 * Smart Buzzer — /redeem balance-lookup data builder
 * -------------------------------------------------------------------------
 * Reads redeem/data/single.csv and redeem/data/multi.csv and produces
 * redeem/redeem_data.json, the lookup snapshot consumed by redeem/index.php.
 *
 * Run from the repo root:   php redeem/build_data.php
 * Run from inside redeem/:  php build_data.php
 *
 * Both work — all paths are resolved relative to __DIR__.
 *
 * Input CSVs (both optional; whichever exist are processed):
 *   data/single.csv  header: business,email,remaining          (label = business)
 *   data/multi.csv   header: business,label,email,remaining
 *
 * email may be blank, "No Email", or contain TWO emails separated by
 * whitespace/comma/semicolon. Each valid email produces its own order object
 * (same order redeemable by either email — intended).
 *
 * Never crashes on a malformed row: it is skipped and counted.
 */

// ---------------------------------------------------------------------------
// Path resolution (works from repo root AND from inside redeem/)
// ---------------------------------------------------------------------------
$baseDir    = __DIR__;
$dataDir    = $baseDir . '/data';
$outFile    = $baseDir . '/redeem_data.json';
$singlePath = $dataDir . '/single.csv';
$multiPath  = $dataDir . '/multi.csv';

// ---------------------------------------------------------------------------
// Accumulators
// ---------------------------------------------------------------------------
$orders           = [];
$distinctEmails   = [];              // email => true
$skipNoEmail      = 0;
$skipBadRemaining = 0;
$rowsRead         = ['single' => 0, 'multi' => 0];
$ordersFrom       = ['single' => 0, 'multi' => 0];
$fileStatus       = ['single' => 'missing', 'multi' => 'missing'];

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/** Trim, drop leading '=' / stray quote chars (spreadsheet artifacts), collapse internal whitespace. */
function clean_text($s) {
    $s = trim((string)$s);
    // strip a UTF-8 BOM if it snuck into the first cell
    $s = preg_replace('/^\xEF\xBB\xBF/', '', $s);
    // strip leading '=' and stray wrapping quotes/space (e.g. Google Sheets ="..." export)
    $s = ltrim($s, "= \t\"'");
    $s = trim($s, "\"'");
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

/** Return non-negative int, or null if the value is not a clean non-negative integer. */
function parse_remaining($raw) {
    $r = str_replace(',', '', trim((string)$raw)); // tolerate "1,000"
    if ($r === '' || !preg_match('/^\d+$/', $r)) {
        return null;
    }
    return (int)$r;
}

/** Split an email cell into normalized candidate addresses that contain '@'. */
function extract_emails($raw) {
    $parts = preg_split('/[\s,;]+/', trim((string)$raw), -1, PREG_SPLIT_NO_EMPTY);
    $out = [];
    foreach ($parts as $p) {
        $p = strtolower(trim($p));
        if ($p !== '' && strpos($p, '@') !== false) {
            $out[$p] = true; // dedupe within the same cell
        }
    }
    return array_keys($out);
}

/**
 * Process one CSV file.
 * $type is 'single' or 'multi'. For 'single' the label defaults to business.
 */
function process_file(
    $path, $type,
    array &$orders, array &$distinctEmails,
    &$skipNoEmail, &$skipBadRemaining,
    array &$rowsRead, array &$ordersFrom, array &$fileStatus
) {
    if (!is_file($path)) {
        $fileStatus[$type] = 'missing';
        return;
    }
    $fh = fopen($path, 'r');
    if ($fh === false) {
        $fileStatus[$type] = 'unreadable';
        return;
    }
    $fileStatus[$type] = 'ok';

    // Header row -> column-name => index map (order-independent, tolerant of extra columns)
    $header = fgetcsv($fh);
    if ($header === false) {
        fclose($fh);
        return;
    }
    $col = [];
    foreach ($header as $i => $name) {
        $key = strtolower(trim((string)$name));
        $key = preg_replace('/^\xEF\xBB\xBF/', '', $key); // BOM on first cell
        if ($key !== '') {
            $col[$key] = $i;
        }
    }
    $get = function ($row, $key) use ($col) {
        return (isset($col[$key]) && isset($row[$col[$key]])) ? $row[$col[$key]] : '';
    };

    while (($row = fgetcsv($fh)) !== false) {
        // Skip truly blank lines (fgetcsv yields [null] or a single empty cell)
        if ($row === [null]) {
            continue;
        }
        if (count($row) === 1 && trim((string)$row[0]) === '') {
            continue;
        }

        $rowsRead[$type]++;

        try {
            $business = clean_text($get($row, 'business'));

            if ($type === 'multi') {
                $label = clean_text($get($row, 'label'));
                if ($label === '') {
                    $label = $business;
                }
            } else {
                $label = $business;
            }

            $emails = extract_emails($get($row, 'email'));
            if (empty($emails)) {
                $skipNoEmail++;
                continue;
            }

            $remaining = parse_remaining($get($row, 'remaining'));
            if ($remaining === null) {
                $skipBadRemaining++;
                continue;
            }

            foreach ($emails as $email) {
                $code = 'SB-' . strtoupper(substr(md5($email . '|' . $label . '|' . $remaining), 0, 6));
                $orders[] = [
                    'email'     => $email,
                    'business'  => $business,
                    'label'     => $label,
                    'remaining' => $remaining,
                    'amount'    => $remaining * 5,
                    'code'      => $code,
                ];
                $distinctEmails[$email] = true;
                $ordersFrom[$type]++;
            }
        } catch (\Throwable $e) {
            // Never crash on a malformed row — count it and move on.
            $skipBadRemaining++;
            continue;
        }
    }

    fclose($fh);
}

// ---------------------------------------------------------------------------
// Build
// ---------------------------------------------------------------------------
process_file(
    $singlePath, 'single',
    $orders, $distinctEmails,
    $skipNoEmail, $skipBadRemaining,
    $rowsRead, $ordersFrom, $fileStatus
);
process_file(
    $multiPath, 'multi',
    $orders, $distinctEmails,
    $skipNoEmail, $skipBadRemaining,
    $rowsRead, $ordersFrom, $fileStatus
);

$totalOrders  = count($orders);
$totalSkipped = $skipNoEmail + $skipBadRemaining;
$distinctCnt  = count($distinctEmails);
$totalAmount  = 0;
foreach ($orders as $o) {
    $totalAmount += $o['amount'];
}

// ---------------------------------------------------------------------------
// Write redeem_data.json (pretty-printed)
// ---------------------------------------------------------------------------
$payload = [
    'generated_at' => date('Y-m-d'),
    'count'        => $totalOrders,
    'orders'       => $orders,
];

$json = json_encode(
    $payload,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
if ($json === false) {
    fwrite(STDERR, 'ERROR: failed to encode JSON: ' . json_last_error_msg() . "\n");
    exit(1);
}
if (file_put_contents($outFile, $json . "\n") === false) {
    fwrite(STDERR, 'ERROR: failed to write ' . $outFile . "\n");
    exit(1);
}

// ---------------------------------------------------------------------------
// CLI summary
// ---------------------------------------------------------------------------
if ($fileStatus['single'] !== 'ok') {
    fwrite(STDERR, 'note: data/single.csv ' . $fileStatus['single'] . " — skipped\n");
}
if ($fileStatus['multi'] !== 'ok') {
    fwrite(STDERR, 'note: data/multi.csv ' . $fileStatus['multi'] . " — skipped\n");
}

printf(
    "single.csv: %d rows -> %d orders | multi.csv: %d rows -> %d orders | skipped %d (no-email %d, bad-remaining %d) | %d orders, %d distinct emails, total \$%s\n",
    $rowsRead['single'], $ordersFrom['single'],
    $rowsRead['multi'],  $ordersFrom['multi'],
    $totalSkipped, $skipNoEmail, $skipBadRemaining,
    $totalOrders, $distinctCnt, number_format($totalAmount)
);

echo 'Wrote ' . $outFile . "\n";
