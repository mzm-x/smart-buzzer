<?php
/**
 * Smart Buzzer — /redeem balance-lookup data builder (CLI)
 * -------------------------------------------------------------------------
 * Thin wrapper around build_lib.php. All parsing/merge logic lives there so
 * the CLI and the admin UI (submit.php) always produce identical output.
 *
 * Run from the repo root:   php redeem/build_data.php
 * Run from inside redeem/:  php build_data.php
 *
 * Reads whichever of these exist (later source wins on email+label):
 *   data/single.csv   business,email,remaining              (label = business)
 *   data/multi.csv    business,label,email,remaining
 *   data/tracker.csv  Late Debt Tracker export
 *   data/manual.csv   business,label,email,remaining,action,updated_at
 *
 * Writes redeem/redeem_data.json.
 */

require_once __DIR__ . '/build_lib.php';

$res = sb_redeem_build(__DIR__);

if (empty($res['ok'])) {
    fwrite(STDERR, 'ERROR: ' . $res['error'] . "\n");
    exit(1);
}

foreach ($res['sources'] as $name => $s) {
    if ($s['status'] !== 'ok') {
        fwrite(STDERR, 'note: data/' . $name . ".csv missing — skipped\n");
    }
}

$parts = array();
foreach ($res['sources'] as $name => $s) {
    if ($s['status'] === 'ok') {
        $parts[] = sprintf('%s.csv: %d rows -> %d orders', $name, $s['rows'], $s['orders']);
    }
}

$ineligible = '';
if (!empty($res['skipIneligible'])) {
    $bits = array();
    foreach ($res['skipIneligible'] as $reason => $n) { $bits[] = $reason . ' ' . $n; }
    $ineligible = ', ineligible ' . array_sum($res['skipIneligible']) . ' (' . implode(', ', $bits) . ')';
}

printf(
    "%s | skipped: no-email %d, bad-remaining %d, zero-or-negative %d%s | %d orders, %d distinct emails, total \$%s%s\n",
    implode(' | ', $parts),
    $res['skipNoEmail'],
    $res['skipBadRemaining'],
    $res['skipZero'],
    $ineligible,
    $res['orders'],
    $res['distinctEmails'],
    number_format($res['totalAmount']),
    $res['suppressed'] ? ' | ' . $res['suppressed'] . ' suppressed via manual remove' : ''
);

echo 'Wrote ' . $res['outFile'] . "\n";
