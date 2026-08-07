<?php
/**
 * Smart Buzzer — /redeem/submit.php
 * -------------------------------------------------------------------------
 * Internal admin tool. Add / update / remove balance entries, then rebuild
 * redeem_data.json so /redeem/ serves the new numbers immediately.
 *
 * Writes to data/manual.csv only — the exported CSVs (single/multi/tracker)
 * are never touched, so a fresh Google Sheet export can't wipe manual edits.
 * manual.csv has the highest priority in build_lib.php.
 *
 * Not a customer-facing landing page: no GTM, no pixels, no analytics.
 */

session_start();
require_once __DIR__ . '/build_lib.php';

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------
define('SB_SUBMIT_PASSWORD', 'smartbuzzer2025');

$baseDir    = __DIR__;
$dataDir    = $baseDir . '/data';
$manualPath = $dataDir . '/manual.csv';
$MANUAL_HEADER = array('business', 'label', 'email', 'remaining', 'action', 'updated_at');

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($n) { return '$' . number_format((float)$n, 2); }

// ---------------------------------------------------------------------------
// Auth
// ---------------------------------------------------------------------------
if (isset($_GET['logout'])) {
    unset($_SESSION['sb_redeem_admin']);
    header('Location: submit.php');
    exit;
}

$loginError = '';
if (!empty($_POST['password_attempt'])) {
    if (hash_equals(SB_SUBMIT_PASSWORD, (string)$_POST['password_attempt'])) {
        $_SESSION['sb_redeem_admin'] = true;
        header('Location: submit.php');
        exit;
    }
    $loginError = 'Wrong password.';
}

$authed = !empty($_SESSION['sb_redeem_admin']);

// Blank CSV template, so nobody has to guess the column names.
if ($authed && isset($_GET['template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="redeem-balance-template.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('Business Name', 'Listing Label', 'Business Email', 'Units Remaining'));
    fputcsv($out, array('SafePro Roofing & Chimney', '', 'cimhasa333@gmail.com', '68'));
    fputcsv($out, array('New Image Restoration', '', 'demariajasob@yahoo.com jasondemaria2123@icloud.con', '20'));
    fputcsv($out, array('Cash Your Gold', 'Logan Gold Buyers', 'gagan@goldsecure.com.au', '35'));
    fputcsv($out, array('Old Client That Refunded', '', 'someone@example.com', 'remove'));
    fclose($out);
    exit;
}

// ---------------------------------------------------------------------------
// manual.csv helpers
// ---------------------------------------------------------------------------

/** Stable identity for a manual row: what it overrides. */
function sb_manual_id($label, $emailRaw) {
    return substr(md5(strtolower(trim($label)) . '|' . strtolower(trim($emailRaw))), 0, 12);
}

function sb_manual_read($path) {
    $out = array();
    if (!is_file($path)) { return $out; }
    $fh = @fopen($path, 'r');
    if ($fh === false) { return $out; }
    $header = fgetcsv($fh);
    if ($header === false) { fclose($fh); return $out; }

    $col = array();
    foreach ($header as $i => $name) {
        $key = strtolower(sb_clean_text($name));
        if ($key !== '') { $col[$key] = $i; }
    }
    $get = function ($row, $key) use ($col) {
        return (isset($col[$key]) && isset($row[$col[$key]])) ? $row[$col[$key]] : '';
    };

    while (($row = fgetcsv($fh)) !== false) {
        if ($row === array(null)) { continue; }
        if (count($row) === 1 && trim((string)$row[0]) === '') { continue; }
        $label    = sb_clean_text($get($row, 'label'));
        $business = sb_clean_text($get($row, 'business'));
        if ($label === '')    { $label = $business; }
        if ($business === '') { $business = $label; }
        $emailRaw = sb_clean_text($get($row, 'email'));
        if ($emailRaw === '') { continue; }

        $out[] = array(
            'id'         => sb_manual_id($label, $emailRaw),
            'business'   => $business,
            'label'      => $label,
            'email'      => $emailRaw,
            'emails'     => sb_extract_emails($emailRaw),
            'remaining'  => sb_clean_text($get($row, 'remaining')),
            'action'     => strtolower(sb_clean_text($get($row, 'action'))) === 'remove' ? 'remove' : 'set',
            'updated_at' => sb_clean_text($get($row, 'updated_at')),
        );
    }
    fclose($fh);
    return $out;
}

function sb_manual_write($path, array $rows, array $header) {
    $dir = dirname($path);
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

    $tmp = $path . '.tmp';
    $fh  = @fopen($tmp, 'w');
    if ($fh === false) { return false; }
    fputcsv($fh, $header);
    foreach ($rows as $r) {
        fputcsv($fh, array(
            $r['business'], $r['label'], $r['email'],
            $r['action'] === 'remove' ? '' : $r['remaining'],
            $r['action'], $r['updated_at'],
        ));
    }
    fclose($fh);
    if (!@rename($tmp, $path)) { @unlink($tmp); return false; }
    return true;
}

/**
 * Does this row look like a header rather than data?
 * (An entry can't have "email" as its business name and no "@" anywhere.)
 */
function sb_looks_like_header($business, $emailRaw, $remRaw) {
    $all = strtolower($business . ' ' . $emailRaw . ' ' . $remRaw);
    return strpos($all, '@') === false
        && strpos($all, 'email') !== false
        && (strpos($all, 'business') !== false || strpos($all, 'client') !== false
            || strpos($all, 'unit') !== false || strpos($all, 'remain') !== false);
}

/**
 * Map a header row to column indexes so column ORDER doesn't matter in an
 * uploaded CSV. Returns null when the header isn't recognisable (then the file
 * is read positionally instead).
 *
 * "Business email" contains both words, so email is matched first.
 */
function sb_map_header($cells) {
    $map = array('business' => null, 'label' => null, 'email' => null, 'remaining' => null);

    foreach ($cells as $i => $cell) {
        $k = strtolower(sb_clean_text($cell));
        if ($k === '') { continue; }

        if ($map['email'] === null && strpos($k, 'email') !== false) { $map['email'] = $i; continue; }
        if ($map['remaining'] === null && (strpos($k, 'remain') !== false || strpos($k, 'unit') !== false
            || strpos($k, 'qty') !== false || strpos($k, 'quantity') !== false || strpos($k, 'balance') !== false)) {
            $map['remaining'] = $i; continue;
        }
        if ($map['label'] === null && (strpos($k, 'label') !== false || strpos($k, 'listing') !== false
            || strpos($k, 'mission') !== false || strpos($k, 'link') !== false)) {
            $map['label'] = $i; continue;
        }
        if ($map['business'] === null && (strpos($k, 'business') !== false || strpos($k, 'client') !== false
            || strpos($k, 'company') !== false || strpos($k, 'name') !== false)) {
            $map['business'] = $i; continue;
        }
    }

    return ($map['email'] !== null && $map['remaining'] !== null && $map['business'] !== null) ? $map : null;
}

/**
 * Validate one entry and turn it into a manual.csv row.
 * Returns ['row' => [...]] or ['error' => 'Line 3: ...'].
 * $seen is by-reference so duplicates within the same batch are caught.
 */
function sb_make_entry($business, $label, $emailRaw, $remRaw, $lineNo, array &$seen) {
    $business = sb_clean_text($business);
    $label    = sb_clean_text($label);
    $emailRaw = sb_clean_text($emailRaw);
    $remRaw   = sb_clean_text($remRaw);

    if ($label === '')    { $label = $business; }
    if ($business === '') { $business = $label; }

    if ($business === '') {
        return array('error' => 'Line ' . $lineNo . ': business name is empty.');
    }

    $emails = sb_extract_emails($emailRaw);
    if (empty($emails)) {
        return array('error' => 'Line ' . $lineNo . ': "' . $emailRaw . '" is not a valid email address.');
    }

    $remove    = in_array(strtolower($remRaw), array('remove', 'suppress', 'hide', '-', 'x'), true);
    $remaining = $remove ? null : sb_parse_remaining($remRaw);
    if (!$remove && $remaining === null) {
        return array('error' => 'Line ' . $lineNo . ': units "' . $remRaw . '" must be a whole number 0 or higher (or "remove").');
    }

    $id = sb_manual_id($label, $emailRaw);
    if (isset($seen[$id])) {
        return array('error' => 'Line ' . $lineNo . ': duplicate of line ' . $seen[$id] . ' (same label + email).');
    }
    $seen[$id] = $lineNo;

    return array('row' => array(
        'id'         => $id,
        'business'   => $business,
        'label'      => $label,
        'email'      => $emailRaw,
        'emails'     => $emails,
        'remaining'  => $remove ? '' : (string)$remaining,
        'action'     => $remove ? 'remove' : 'set',
        'updated_at' => date('Y-m-d H:i'),
    ));
}

/**
 * Turn 3 or 4 positional cells into an entry.
 *   3 cells: Business, Email, Units          (label = business)
 *   4 cells: Business, Label, Email, Units
 */
function sb_entry_from_cells(array $parts, $lineNo, array &$seen) {
    if (count($parts) < 3) {
        return array('error' => 'Line ' . $lineNo . ': need at least 3 fields (Business, Email, Units) — got ' . count($parts) . '.');
    }
    if (count($parts) > 4) {
        return array('error' => 'Line ' . $lineNo . ': too many fields (' . count($parts) . '). Max is Business, Label, Email, Units.');
    }

    if (count($parts) === 3) {
        list($business, $emailRaw, $remRaw) = $parts;
        $label = '';
    } else {
        list($business, $label, $emailRaw, $remRaw) = $parts;
    }

    if ($lineNo === 1 && sb_looks_like_header($business, $emailRaw, $remRaw)) {
        return array('skip' => true);
    }

    return sb_make_entry($business, $label, $emailRaw, $remRaw, $lineNo, $seen);
}

/**
 * Parse the bulk paste box — one entry per line, pipe-separated (tabs also
 * accepted so you can paste a column block straight out of a spreadsheet).
 * Blank lines, "#" comments and a header row are ignored.
 *
 * Returns ['rows' => [...], 'errors' => ['Line 3: ...', ...]].
 */
function sb_parse_bulk($text) {
    $rows   = array();
    $errors = array();
    $seen   = array();

    foreach (preg_split("/\r\n|\r|\n/", (string)$text) as $i => $line) {
        $n = $i + 1;
        if (trim($line) === '') { continue; }
        if (substr(ltrim($line), 0, 1) === '#') { continue; }

        $parts = (strpos($line, '|') !== false) ? explode('|', $line) : preg_split("/\t+/", $line);
        $parts = array_map('sb_clean_text', $parts);
        $parts = array_values(array_filter($parts, function ($p) { return $p !== ''; }));

        $res = sb_entry_from_cells($parts, $n, $seen);
        if (isset($res['skip']))  { continue; }
        if (isset($res['error'])) { $errors[] = $res['error']; continue; }
        $rows[] = $res['row'];
    }

    return array('rows' => $rows, 'errors' => $errors);
}

/**
 * Parse an uploaded CSV. If the first row is a recognisable header the columns
 * are matched BY NAME (so order doesn't matter and extra columns are ignored);
 * otherwise every row is read positionally like the paste box.
 */
function sb_parse_csv_file($path, $maxRows = 5000) {
    $rows   = array();
    $errors = array();
    $seen   = array();

    $fh = @fopen($path, 'r');
    if ($fh === false) {
        return array('rows' => array(), 'errors' => array('Could not read the uploaded file.'));
    }

    $map     = null;
    $lineNo  = 0;
    $dataNo  = 0;
    $first   = true;

    while (($cells = fgetcsv($fh)) !== false) {
        $lineNo++;
        if ($cells === array(null)) { continue; }
        if (count($cells) === 1 && trim((string)$cells[0]) === '') { continue; }
        if (substr(ltrim((string)$cells[0]), 0, 1) === '#') { continue; }

        if ($first) {
            $first = false;
            $map = sb_map_header($cells);
            if ($map !== null) { continue; }          // named header consumed
        }

        if (++$dataNo > $maxRows) {
            $errors[] = 'File has more than ' . number_format($maxRows) . ' rows — split it into smaller files.';
            break;
        }

        if ($map !== null) {
            $get = function ($key) use ($map, $cells) {
                return ($map[$key] !== null && isset($cells[$map[$key]])) ? $cells[$map[$key]] : '';
            };
            $res = sb_make_entry($get('business'), $get('label'), $get('email'), $get('remaining'), $lineNo, $seen);
        } else {
            $parts = array_values(array_filter(array_map('sb_clean_text', $cells), function ($p) { return $p !== ''; }));
            $res = sb_entry_from_cells($parts, $lineNo, $seen);
        }

        if (isset($res['skip']))  { continue; }
        if (isset($res['error'])) { $errors[] = $res['error']; continue; }
        $rows[] = $res['row'];
    }
    fclose($fh);

    if (empty($rows) && empty($errors)) {
        $errors[] = 'No usable rows found in that file. Expected columns: Business, Email, Units remaining.';
    }
    return array('rows' => $rows, 'errors' => $errors);
}

/** Keep data/ off the public web. */
function sb_ensure_htaccess($dataDir) {
    if (!is_dir($dataDir)) { @mkdir($dataDir, 0755, true); }
    $ht = $dataDir . '/.htaccess';
    if (!is_file($ht)) {
        @file_put_contents($ht, "Order allow,deny\nDeny from all\nRequire all denied\n");
    }
}

// ---------------------------------------------------------------------------
// Actions
// ---------------------------------------------------------------------------
$notice     = '';
$error      = '';
$build      = null;
$bulkErrors = array();
$bulkText   = '';

if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do'])) {
    sb_ensure_htaccess($dataDir);
    $rows = sb_manual_read($manualPath);
    $do   = $_POST['do'];

    if ($do === 'bulk') {
        $upload = isset($_FILES['csvfile']) ? $_FILES['csvfile'] : null;
        $hasUpload = $upload && isset($upload['error']) && $upload['error'] !== UPLOAD_ERR_NO_FILE;

        $parsed = array('rows' => array(), 'errors' => array());
        $source = 'paste';

        if ($hasUpload) {
            $source = 'file';
            if ($upload['error'] !== UPLOAD_ERR_OK) {
                $why = array(
                    UPLOAD_ERR_INI_SIZE   => 'the file is larger than the server upload limit',
                    UPLOAD_ERR_FORM_SIZE  => 'the file is too large',
                    UPLOAD_ERR_PARTIAL    => 'the upload was interrupted',
                    UPLOAD_ERR_NO_TMP_DIR => 'the server has no temp folder configured',
                    UPLOAD_ERR_CANT_WRITE => 'the server could not write the file',
                    UPLOAD_ERR_EXTENSION  => 'a server extension blocked the upload',
                );
                $parsed['errors'][] = 'Upload failed — '
                    . (isset($why[$upload['error']]) ? $why[$upload['error']] : 'error code ' . $upload['error']) . '.';
            } elseif (!is_uploaded_file($upload['tmp_name'])) {
                $parsed['errors'][] = 'Upload rejected.';
            } else {
                $ext = strtolower(pathinfo((string)$upload['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, array('csv', 'txt', 'tsv'), true)) {
                    $parsed['errors'][] = 'Only .csv, .tsv or .txt files are accepted — got ".' . h($ext) . '".';
                } elseif ((int)$upload['size'] > 4 * 1024 * 1024) {
                    $parsed['errors'][] = 'File is larger than 4 MB.';
                } else {
                    $parsed = sb_parse_csv_file($upload['tmp_name']);
                }
            }
        } else {
            $parsed = sb_parse_bulk(isset($_POST['bulk']) ? $_POST['bulk'] : '');
        }

        if (!empty($parsed['errors'])) {
            // Atomic: one bad line blocks the whole paste, so you never get a half import.
            $bulkErrors = $parsed['errors'];
            $error = 'Nothing was saved — fix ' . count($bulkErrors)
                   . ' line' . (count($bulkErrors) === 1 ? '' : 's') . ' below and paste again.';
        } elseif (empty($parsed['rows'])) {
            $error = $source === 'file'
                ? 'Nothing to save — that file had no usable rows.'
                : 'Nothing to save — paste some entries or choose a CSV file.';
        } else {
            $added = 0; $updated = 0; $suppressed = 0; $units = 0;

            foreach ($parsed['rows'] as $new) {
                $replaced = false;
                foreach ($rows as $i => $r) {
                    if ($r['id'] === $new['id']) { $rows[$i] = $new; $replaced = true; break; }
                }
                if (!$replaced) { $rows[] = $new; }

                if ($new['action'] === 'remove') { $suppressed++; }
                else {
                    $replaced ? $updated++ : $added++;
                    $units += (int)$new['remaining'];   // once per listing, not per email
                }
            }

            if (sb_manual_write($manualPath, $rows, $MANUAL_HEADER)) {
                $bits = array();
                if ($added)      { $bits[] = $added . ' added'; }
                if ($updated)    { $bits[] = $updated . ' updated'; }
                if ($suppressed) { $bits[] = $suppressed . ' suppressed'; }
                $notice = ($source === 'file' ? 'Imported "' . basename((string)$upload['name']) . '": ' : '')
                        . implode(', ', $bits) . ' — ' . money($units * 5) . ' of balance published.';
            } else {
                $error = 'Could not write data/manual.csv — check folder permissions.';
            }
        }
    } elseif ($do === 'delete') {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $kept = array();
        $hit  = false;
        foreach ($rows as $r) {
            if ($r['id'] === $id) { $hit = true; continue; }
            $kept[] = $r;
        }
        if (!$hit) {
            $error = 'Entry not found.';
        } elseif (sb_manual_write($manualPath, $kept, $MANUAL_HEADER)) {
            $notice = 'Manual entry deleted. The value from the CSV export (if any) applies again.';
        } else {
            $error = 'Could not write data/manual.csv — check folder permissions.';
        }
    }

    if ($error === '') {
        $build = sb_redeem_build($baseDir);
        if (empty($build['ok'])) { $error = 'Rebuild failed: ' . $build['error']; }
    }
}

if ($authed && isset($_GET['rebuild'])) {
    $build = sb_redeem_build($baseDir);
    if (empty($build['ok'])) { $error = 'Rebuild failed: ' . $build['error']; }
    else { $notice = 'Rebuilt redeem_data.json from all CSV sources.'; }
}

// ---------------------------------------------------------------------------
// Current state
// ---------------------------------------------------------------------------
$manualRows = $authed ? sb_manual_read($manualPath) : array();

$snapshot = array('generated_at' => '-', 'count' => 0, 'orders' => array());
$raw = @file_get_contents($baseDir . '/redeem_data.json');
if ($raw !== false) {
    $j = json_decode($raw, true);
    if (is_array($j) && isset($j['orders']) && is_array($j['orders'])) { $snapshot = $j; }
}
// A 2-email order exists twice in the JSON (once per address) but is a single
// balance the client can redeem once — so count listings, not order objects,
// otherwise the total overstates what we actually owe.
$snapEmails   = array();
$snapListings = array();
foreach ($snapshot['orders'] as $o) {
    if (isset($o['email'])) { $snapEmails[strtolower($o['email'])] = true; }
    $lk = strtolower((isset($o['business']) ? $o['business'] : '') . '|'
                   . (isset($o['label']) ? $o['label'] : '')) . '|'
                   . (isset($o['remaining']) ? (int)$o['remaining'] : 0);
    $snapListings[$lk] = isset($o['amount']) ? (float)$o['amount'] : 0;
}
$snapTotal = array_sum($snapListings);

// Live search across the published snapshot
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = array();
if ($authed && $q !== '') {
    $needle = strtolower($q);
    foreach ($snapshot['orders'] as $o) {
        $hay = strtolower(
            (isset($o['email']) ? $o['email'] : '') . ' ' .
            (isset($o['business']) ? $o['business'] : '') . ' ' .
            (isset($o['label']) ? $o['label'] : '') . ' ' .
            (isset($o['code']) ? $o['code'] : '')
        );
        if (strpos($hay, $needle) !== false) { $results[] = $o; }
        if (count($results) >= 200) { break; }
    }
}

// Keep the paste box filled on error so nothing typed is lost; prefill it when editing.
if ($error !== '' && isset($_POST['bulk'])) {
    $bulkText = (string)$_POST['bulk'];
} elseif ($authed && isset($_GET['edit'])) {
    foreach ($manualRows as $r) {
        if ($r['id'] === $_GET['edit']) {
            $units = $r['action'] === 'remove' ? 'remove' : $r['remaining'];
            $bulkText = ($r['label'] !== '' && $r['label'] !== $r['business'])
                ? $r['business'] . ' | ' . $r['label'] . ' | ' . $r['email'] . ' | ' . $units
                : $r['business'] . ' | ' . $r['email'] . ' | ' . $units;
            break;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Redeem Admin — Smart Buzzer</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{
  --blue:#2563EB; --blue-hover:#1D4ED8; --blue-light:#EFF6FF;
  --green:#059669; --red:#DC2626; --amber:#B45309;
  --bg:#F5F5F4; --card:#FFFFFF; --border:#E5E5E4;
  --text:#1A1A1A; --text-2:#525252; --muted:#A3A3A3;
  --r-sm:10px; --r-md:14px; --r-lg:18px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  background:var(--bg); color:var(--text); line-height:1.5;
  -webkit-font-smoothing:antialiased; padding:28px 18px 64px;
}
.wrap{max-width:1080px;margin:0 auto;}
.head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:22px;}
.head img{height:34px;width:auto;}
.head .title{font-size:20px;font-weight:900;letter-spacing:-0.02em;}
.head .sub{font-size:13px;color:var(--text-2);}
.head .actions{display:flex;gap:8px;flex-wrap:wrap;}

.card{background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);padding:22px;margin-bottom:18px;box-shadow:0 2px 14px rgba(15,23,42,.04);}
.card h2{font-size:15px;font-weight:800;margin-bottom:4px;}
.card .hint{font-size:13px;color:var(--text-2);margin-bottom:16px;}

.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;}
.stat{background:var(--blue-light);border:1px solid #BFDBFE;border-radius:var(--r-md);padding:14px 16px;}
.stat .k{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text-2);}
.stat .v{font-size:22px;font-weight:900;letter-spacing:-0.02em;margin-top:2px;}

label{display:block;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);margin-bottom:6px;}
.field{width:100%;font-family:inherit;font-size:15px;padding:12px 13px;border:1.5px solid var(--border);border-radius:var(--r-sm);background:#fff;color:var(--text);}
.field:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 4px rgba(37,99,235,.12);}
.row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
.row-1{margin-bottom:14px;}
.help{font-size:12px;color:var(--muted);margin-top:6px;}

.btn{cursor:pointer;font-family:inherit;font-size:14px;font-weight:700;color:#fff;background:var(--blue);border:none;border-radius:var(--r-sm);padding:11px 18px;text-decoration:none;display:inline-block;transition:background .15s;}
.btn:hover{background:var(--blue-hover);}
.btn-ghost{background:#fff;color:var(--text-2);border:1.5px solid var(--border);}
.btn-ghost:hover{background:#FAFAF9;}
.btn-warn{background:#fff;color:var(--amber);border:1.5px solid #FDE68A;}
.btn-warn:hover{background:#FFFBEB;}
.btn-danger{background:#fff;color:var(--red);border:1.5px solid #FECACA;}
.btn-danger:hover{background:#FEF2F2;}
.btn-sm{font-size:12.5px;padding:7px 11px;}

.msg{border-radius:var(--r-md);padding:13px 16px;font-size:14px;margin-bottom:18px;}
.msg-ok{background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;}
.msg-err{background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;}
.msg-info{background:#FFFBEB;border:1px solid #FDE68A;color:var(--amber);}

table{width:100%;border-collapse:collapse;font-size:13.5px;}
th{text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);padding:9px 10px;border-bottom:1.5px solid var(--border);white-space:nowrap;}
td{padding:11px 10px;border-bottom:1px solid var(--border);vertical-align:top;}
tr:last-child td{border-bottom:none;}
.tbl-wrap{overflow-x:auto;}
.mono{font-family:ui-monospace,'SFMono-Regular',Menlo,Consolas,monospace;font-size:12.5px;}
.pill{display:inline-block;font-size:11px;font-weight:800;padding:3px 9px;border-radius:999px;white-space:nowrap;}
.pill-set{background:var(--blue-light);color:var(--blue-hover);border:1px solid #BFDBFE;}
.pill-rm{background:#FEF2F2;color:var(--red);border:1px solid #FECACA;}
.amount{font-weight:800;color:var(--green);}
.empty{text-align:center;color:var(--muted);font-size:14px;padding:26px 0;}
.srch{display:flex;gap:10px;flex-wrap:wrap;}
.srch .field{flex:1;min-width:220px;}
.src-list{font-size:13px;color:var(--text-2);}
.src-list li{margin:4px 0 4px 18px;}

textarea.field{resize:vertical;line-height:1.65;min-height:180px;}
textarea.mono{font-family:ui-monospace,'SFMono-Regular',Menlo,Consolas,monospace;font-size:13.5px;}

.fmt{background:#FAFAF9;border:1px solid var(--border);border-radius:var(--r-md);padding:14px 16px;margin-bottom:16px;}
.fmt-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;}
.fmt-row code{font-family:ui-monospace,'SFMono-Regular',Menlo,Consolas,monospace;font-size:13px;color:var(--text);}
.fmt-row code b{color:var(--blue);font-weight:900;padding:0 2px;}
.fmt-tag{display:inline-block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;
  background:var(--blue-light);color:var(--blue-hover);border:1px solid #BFDBFE;border-radius:999px;padding:3px 9px;white-space:nowrap;}
.fmt-tag-alt{background:#fff;color:var(--text-2);border-color:var(--border);}
.fmt-notes{margin:12px 0 0 18px;font-size:12.5px;color:var(--text-2);}
.fmt-notes li{margin:5px 0;}
.fmt-notes code{background:#fff;border:1px solid var(--border);border-radius:5px;padding:1px 5px;font-size:12px;}

.or{display:flex;align-items:center;gap:12px;margin:20px 0 14px;}
.or::before,.or::after{content:"";flex:1;height:1px;background:var(--border);}
.or span{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
.upload{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.file{flex:1;min-width:240px;font-family:inherit;font-size:13.5px;color:var(--text-2);
  border:1.5px dashed var(--border);border-radius:var(--r-sm);padding:12px 13px;background:#FAFAF9;cursor:pointer;}
.file:hover{border-color:var(--blue);background:var(--blue-light);}
.file::file-selector-button{font-family:inherit;font-size:13px;font-weight:700;color:#fff;background:var(--blue);
  border:none;border-radius:8px;padding:8px 14px;margin-right:12px;cursor:pointer;}
.file::file-selector-button:hover{background:var(--blue-hover);}
.help code{background:#FAFAF9;border:1px solid var(--border);border-radius:5px;padding:1px 5px;font-size:12px;}

@media (max-width:640px){
  .row{grid-template-columns:1fr;}
  body{padding:20px 12px 48px;}
  .card{padding:18px 15px;}
}
</style>
</head>
<body>
<div class="wrap">

  <div class="head">
    <div>
      <a href="https://smart-buzzer.com/"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer"></a>
      <div class="title">Redeem Admin</div>
      <div class="sub">Balance &amp; voucher entries for <a href="index.php">/redeem/</a></div>
    </div>
<?php if ($authed): ?>
    <div class="actions">
      <a class="btn btn-ghost btn-sm" href="?rebuild=1">Rebuild JSON</a>
      <a class="btn btn-ghost btn-sm" href="index.php" target="_blank" rel="noopener">Open lookup</a>
      <a class="btn btn-ghost btn-sm" href="?logout=1">Log out</a>
    </div>
<?php endif; ?>
  </div>

<?php if (!$authed): ?>

  <div class="card" style="max-width:420px;">
    <h2>Admin login</h2>
    <p class="hint">Internal tool. Enter the admin password to continue.</p>
<?php if ($loginError !== ''): ?>
    <div class="msg msg-err"><?php echo h($loginError); ?></div>
<?php endif; ?>
    <form method="post" action="">
      <div class="row-1">
        <label for="pw">Password</label>
        <input class="field" type="password" id="pw" name="password_attempt" autofocus required>
      </div>
      <button class="btn" type="submit">Log in</button>
    </form>
  </div>

<?php else: ?>

<?php if ($notice !== ''): ?>
  <div class="msg msg-ok"><?php echo h($notice); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
  <div class="msg msg-err"><?php echo h($error); ?></div>
<?php endif; ?>

  <div class="card">
    <h2>Published snapshot</h2>
    <p class="hint">This is exactly what <code>/redeem/</code> serves right now.</p>
    <div class="grid">
      <div class="stat"><div class="k">Listings</div><div class="v"><?php echo number_format(count($snapListings)); ?></div></div>
      <div class="stat"><div class="k">Distinct emails</div><div class="v"><?php echo number_format(count($snapEmails)); ?></div></div>
      <div class="stat"><div class="k">Total balance</div><div class="v"><?php echo h(money($snapTotal)); ?></div></div>
      <div class="stat"><div class="k">Generated</div><div class="v" style="font-size:17px;"><?php echo h($snapshot['generated_at']); ?></div></div>
    </div>
    <div class="help" style="margin-top:10px;">
      <?php echo number_format(count($snapshot['orders'])); ?> lookup records across
      <?php echo number_format(count($snapListings)); ?> listings — an order with two emails is
      stored twice but counted <strong>once</strong> here, since the client can only redeem it once.
    </div>
<?php if ($build !== null && !empty($build['ok'])): ?>
    <div class="msg msg-info" style="margin-top:16px;margin-bottom:0;">
      Rebuild:
<?php
      $bits = array();
      foreach ($build['sources'] as $n => $s) {
          $bits[] = $s['status'] === 'ok'
              ? $n . '.csv ' . number_format($s['rows']) . ' rows &rarr; ' . number_format($s['orders']) . ' orders'
              : $n . '.csv missing';
      }
      echo implode(' &middot; ', $bits);
      if (!empty($build['skipIneligible'])) {
          $r = array();
          foreach ($build['skipIneligible'] as $reason => $n) { $r[] = h($reason) . ' ' . $n; }
          echo '<br>Tracker rows skipped as ineligible: ' . implode(', ', $r) . '.';
      }
      if (!empty($build['skipZero'])) {
          echo ' Zero/negative remaining skipped: ' . (int)$build['skipZero'] . '.';
      }
?>
    </div>
<?php endif; ?>
  </div>

  <div class="card">
    <h2>Add / update balances</h2>
    <p class="hint">
      One client per line. Saving writes to <code>data/manual.csv</code> and rebuilds
      <code>redeem_data.json</code> immediately. A manual entry always wins over the CSV
      exports, so this is also how you correct a wrong number from the sheet.
    </p>

    <div class="fmt">
      <div class="fmt-row"><span class="fmt-tag">Format</span><code>Business Name <b>|</b> Email <b>|</b> Units remaining</code></div>
      <div class="fmt-row"><span class="fmt-tag fmt-tag-alt">Multi-listing</span><code>Business Name <b>|</b> Listing Label <b>|</b> Email <b>|</b> Units remaining</code></div>
      <ul class="fmt-notes">
        <li><strong>The dollar amount is calculated automatically</strong> — $5 per unit. Never type a price.</li>
        <li>Two emails on one order? Put both in the email field separated by a <strong>space</strong> — either one pulls up the balance.</li>
        <li>Units can be the word <code>remove</code> to hide a listing from the lookup.</li>
        <li>Tabs work too, so you can paste a column block straight out of the sheet. Blank lines and <code>#</code> comments are ignored.</li>
      </ul>
    </div>

<?php if (!empty($bulkErrors)): ?>
    <div class="msg msg-err">
      <strong>Fix these lines — nothing was saved:</strong>
      <ul style="margin:8px 0 0 18px;">
<?php foreach ($bulkErrors as $e): ?>
        <li><?php echo h($e); ?></li>
<?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>

    <form method="post" action="" enctype="multipart/form-data">
      <input type="hidden" name="do" value="bulk">
      <label for="bulk">Paste entries</label>
      <textarea class="field mono" id="bulk" name="bulk" rows="10" spellcheck="false"
placeholder="SafePro Roofing &amp; Chimney | cimhasa333@gmail.com | 68
New Image Restoration | demariajasob@yahoo.com jasondemaria2123@icloud.con | 20
Cash Your Gold | Logan Gold Buyers | gagan@goldsecure.com.au | 35
Old Client That Refunded | someone@example.com | remove"><?php echo h($bulkText); ?></textarea>
      <div class="help" id="bulkCount">Nothing entered yet.</div>

      <div class="or"><span>or upload a CSV</span></div>

      <div class="upload">
        <input class="file" type="file" id="csvfile" name="csvfile" accept=".csv,.tsv,.txt,text/csv">
        <a class="btn btn-ghost btn-sm" href="?template=1">Download template</a>
      </div>
      <div class="help">
        Columns <code>Business Name</code>, <code>Business Email</code>, <code>Units Remaining</code>
        (plus optional <code>Listing Label</code>). With a header row the column <strong>order doesn't
        matter</strong> and extra columns are ignored. A file overrides whatever is in the paste box.
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
        <button class="btn" type="submit">Save &amp; rebuild</button>
<?php if ($bulkText !== ''): ?>
        <a class="btn btn-ghost" href="submit.php">Clear</a>
<?php endif; ?>
      </div>
    </form>
  </div>

  <div class="card">
    <h2>Manual entries <span style="color:var(--muted);font-weight:600;">(<?php echo count($manualRows); ?>)</span></h2>
    <p class="hint">Rows stored in <code>data/manual.csv</code>. Deleting one hands control back to the CSV export.</p>
<?php if (empty($manualRows)): ?>
    <div class="empty">No manual entries yet.</div>
<?php else: ?>
    <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Business</th><th>Label</th><th>Email(s)</th>
          <th>Remaining</th><th>Amount</th><th>Type</th><th>Updated</th><th></th>
        </tr>
      </thead>
      <tbody>
<?php foreach (array_reverse($manualRows) as $r): ?>
        <tr>
          <td><?php echo h($r['business']); ?></td>
          <td><?php echo h($r['label']); ?></td>
          <td class="mono"><?php echo implode('<br>', array_map('h', $r['emails'])); ?></td>
          <td><?php echo $r['action'] === 'remove' ? '&mdash;' : h($r['remaining']); ?></td>
          <td class="amount"><?php echo $r['action'] === 'remove' ? '&mdash;' : h(money((int)$r['remaining'] * 5)); ?></td>
          <td><span class="pill <?php echo $r['action'] === 'remove' ? 'pill-rm' : 'pill-set'; ?>"><?php echo $r['action'] === 'remove' ? 'SUPPRESSED' : 'BALANCE'; ?></span></td>
          <td class="mono"><?php echo h($r['updated_at']); ?></td>
          <td style="white-space:nowrap;">
            <a class="btn btn-ghost btn-sm" href="?edit=<?php echo h($r['id']); ?>#bulk">Edit</a>
            <form method="post" action="" style="display:inline;" onsubmit="return confirm('Delete this manual entry?');">
              <input type="hidden" name="do" value="delete">
              <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
              <button class="btn btn-danger btn-sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
    </div>
<?php endif; ?>
  </div>

  <div class="card">
    <h2>Check what a customer sees</h2>
    <p class="hint">Search the published snapshot by email, business, label or voucher code.</p>
    <form method="get" action="" class="srch">
      <input class="field" type="text" name="q" value="<?php echo h($q); ?>" placeholder="demariajasob@yahoo.com">
      <button class="btn" type="submit">Search</button>
<?php if ($q !== ''): ?>
      <a class="btn btn-ghost" href="submit.php">Clear</a>
<?php endif; ?>
    </form>
<?php if ($q !== ''): ?>
<?php if (empty($results)): ?>
    <div class="empty">No match for &ldquo;<?php echo h($q); ?>&rdquo;.</div>
<?php else: ?>
    <div class="tbl-wrap" style="margin-top:16px;">
    <table>
      <thead><tr><th>Email</th><th>Business</th><th>Label</th><th>Remaining</th><th>Amount</th><th>Code</th></tr></thead>
      <tbody>
<?php $sum = 0; foreach ($results as $o): $sum += (float)$o['amount']; ?>
        <tr>
          <td class="mono"><?php echo h($o['email']); ?></td>
          <td><?php echo h($o['business']); ?></td>
          <td><?php echo h($o['label']); ?></td>
          <td><?php echo (int)$o['remaining']; ?></td>
          <td class="amount"><?php echo h(money($o['amount'])); ?></td>
          <td class="mono"><?php echo h($o['code']); ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <p class="hint" style="margin:14px 0 0;"><?php echo count($results); ?> listing<?php echo count($results) === 1 ? '' : 's'; ?> &middot; total <strong class="amount"><?php echo h(money($sum)); ?></strong></p>
<?php endif; ?>
<?php endif; ?>
  </div>

  <div class="card">
    <h2>Data sources</h2>
    <p class="hint">Read in this order — a later source overrides an earlier one on the same <code>email + label</code>.</p>
    <ol class="src-list">
<?php
    $srcInfo = array(
        'single.csv'  => 'business, email, remaining',
        'multi.csv'   => 'business, label, email, remaining',
        'tracker.csv' => 'Late Debt Tracker export — refunded, disputed, client-stopped, finished and zero-balance rows are skipped',
        'manual.csv'  => 'this admin page',
    );
    foreach ($srcInfo as $file => $desc) {
        $exists = is_file($dataDir . '/' . $file);
        echo '<li><code>data/' . h($file) . '</code> — ' . $desc
           . ' <strong style="color:' . ($exists ? 'var(--green)' : 'var(--muted)') . ';">'
           . ($exists ? 'present' : 'not uploaded') . '</strong></li>';
    }
?>
    </ol>
  </div>

<?php endif; ?>

</div>

<script>
// Live count + running total so you can sanity-check a big paste before saving.
(function () {
  var box  = document.getElementById('bulk');
  var out  = document.getElementById('bulkCount');
  var file = document.getElementById('csvfile');
  if (!box || !out) { return; }

  function tally() {
    var ok = 0, remove = 0, bad = 0, units = 0;

    if (file && file.files && file.files.length) {
      out.textContent = 'Using file "' + file.files[0].name + '" — the paste box is ignored.';
      out.style.color = '#2563EB';
      return;
    }

    box.value.split(/\r\n|\r|\n/).forEach(function (line, i) {
      if (!line.trim() || line.trim().charAt(0) === '#') { return; }

      var parts = (line.indexOf('|') !== -1 ? line.split('|') : line.split(/\t+/))
        .map(function (p) { return p.trim(); })
        .filter(function (p) { return p !== ''; });

      if (parts.length < 3 || parts.length > 4) { bad++; return; }

      var email = parts[parts.length - 2];
      var rem   = parts[parts.length - 1];

      var emails = email.split(/[\s,;]+/).filter(function (e) { return e.indexOf('@') !== -1; });
      if (!emails.length) {
        // Header row from the template — not an error.
        if (i === 0 && /email/i.test(line) && /(business|client)/i.test(line)) { return; }
        bad++; return;
      }

      if (/^(remove|suppress|hide|-|x)$/i.test(rem)) { remove++; return; }
      if (!/^\d+$/.test(rem.replace(/,/g, ''))) { bad++; return; }

      ok++;
      units += parseInt(rem.replace(/,/g, ''), 10);   // once per listing, not per email
    });

    if (!ok && !remove && !bad) { out.textContent = 'Nothing entered yet.'; out.style.color = ''; return; }

    var bits = [];
    if (ok)     { bits.push(ok + ' entr' + (ok === 1 ? 'y' : 'ies') + ' = $' + (units * 5).toLocaleString('en-US') + '.00'); }
    if (remove) { bits.push(remove + ' to suppress'); }
    if (bad)    { bits.push(bad + ' line' + (bad === 1 ? '' : 's') + " that won't parse"); }

    out.textContent = bits.join(' · ');
    out.style.color = bad ? '#DC2626' : '#059669';
  }

  box.addEventListener('input', tally);
  if (file) { file.addEventListener('change', tally); }
  tally();
})();
</script>
</body>
</html>
