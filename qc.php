<?php
/**
 * QC OTOMATIS — Smart Buzzer (TripAdvisor Review Matcher)
 * -------------------------------------------------------
 * Cocokin review yang disubmit jobseeker (submission report) vs review yang
 * beneran muncul di TripAdvisor (dataset scrape). Hitung Apply Expired,
 * Submit Result, Show Up, Not Show Up, Stay, Drop — per campaign.
 *
 * Matching: kolom W ("COPY PASTE REVIEW") di submission report  <->  kolom
 * "text" di dataset TripAdvisor. Match => ambil "url"/nama/rating/tanggal.
 * Stay/Drop: tiap upload baru di campaign yang sama, cek apakah url hasil
 * match sebelumnya masih ada di dataset terbaru. Masih ada => Stay, hilang => Drop.
 *
 * Standalone tool — TIDAK menyentuh GTM/pixel/log landing page manapun.
 */

session_start();
date_default_timezone_set('Asia/Jakarta');
mb_internal_encoding('UTF-8');

/* ---------- Optional password (kosongkan '' untuk matiin) ---------- */
$QC_PASSWORD = ''; // contoh: 'smartbuzzer2025'
if ($QC_PASSWORD !== '') {
    if (isset($_POST['qc_login'])) {
        if (hash_equals($QC_PASSWORD, (string)($_POST['qc_pass'] ?? ''))) $_SESSION['qc_auth'] = true;
    }
    if (isset($_GET['logout'])) { unset($_SESSION['qc_auth']); }
    if (empty($_SESSION['qc_auth'])) { render_login(); exit; }
}

/* ---------- Storage ---------- */
$DATA_DIR = __DIR__ . '/qc_data';
if (!is_dir($DATA_DIR)) { @mkdir($DATA_DIR, 0775, true); }
$ht = $DATA_DIR . '/.htaccess';
if (!file_exists($ht)) { @file_put_contents($ht, "Require all denied\nDeny from all\n"); }

/* ================= Helpers ================= */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function strip_bom($s){ return preg_replace('/^\xEF\xBB\xBF/', '', (string)$s); }

function norm($s){
    $s = (string)$s;
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/[^a-z0-9]+/u', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}
function slugify($s){
    $s = strtolower(trim((string)$s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return $s !== '' ? $s : 'campaign';
}
/** Robust CSV read (handles quoted multiline fields like TripAdvisor text). */
function read_csv_file($path){
    $rows = [];
    if (($fh = fopen($path, 'r')) !== false) {
        while (($r = fgetcsv($fh, 0, ',', '"')) !== false) { $rows[] = $r; }
        fclose($fh);
    }
    return $rows;
}
/** Find a header column index by needle(s). $exact = strict equality. */
function header_index($headers, $needles, $exact = false){
    foreach ($headers as $i => $hd) {
        $hd2 = strip_bom(trim((string)$hd));
        foreach ((array)$needles as $n) {
            if ($exact) { if (strcasecmp($hd2, $n) === 0) return $i; }
            else { if (stripos($hd2, $n) !== false) return $i; }
        }
    }
    return -1;
}
/** Build a search index from the TripAdvisor dataset rows. */
function build_dataset_index($rows){
    $out = ['rows' => [], 'exact' => [], 'urlset' => []];
    if (count($rows) < 2) return $out;
    $hd = $rows[0]; $hd[0] = strip_bom($hd[0]);
    $ti = header_index($hd, 'text', true);
    $ui = header_index($hd, 'url', true);
    $ni = header_index($hd, 'user/name', true);
    $ri = header_index($hd, 'rating', true);
    $di = header_index($hd, 'publishedDate', true);
    for ($k = 1; $k < count($rows); $k++) {
        $r = $rows[$k];
        $text = $ti >= 0 ? ($r[$ti] ?? '') : '';
        $url  = $ui >= 0 ? trim($r[$ui] ?? '') : '';
        $nn   = norm($text);
        $row = [
            'text'   => $text,
            'url'    => $url,
            'name'   => $ni >= 0 ? ($r[$ni] ?? '') : '',
            'rating' => $ri >= 0 ? ($r[$ri] ?? '') : '',
            'date'   => $di >= 0 ? ($r[$di] ?? '') : '',
            'norm'   => $nn,
        ];
        $out['rows'][] = $row;
        if ($nn !== '' && !isset($out['exact'][$nn])) $out['exact'][$nn] = $row;
        if ($url !== '') $out['urlset'][$url] = true;
    }
    return $out;
}
/** Match a submitted review text against the dataset. Returns matched row or null. */
function find_match($reviewText, $idx){
    $n = norm($reviewText);
    if ($n === '' || strlen($n) < 8) return null;
    // 1) exact (after normalization — handles trailing punctuation/case)
    if (isset($idx['exact'][$n])) return $idx['exact'][$n];
    // 2) containment either direction (handles truncation / minor tail edits)
    foreach ($idx['rows'] as $row) {
        $dn = $row['norm'];
        if (strlen($dn) < 8) continue;
        if (strpos($dn, $n) !== false || strpos($n, $dn) !== false) return $row;
    }
    // 3) similarity fallback (guarded by shared prefix to keep it fast)
    $pfx = substr($n, 0, 18);
    $best = null; $bestPct = 0.0;
    foreach ($idx['rows'] as $row) {
        if ($row['norm'] === '' || substr($row['norm'], 0, 18) !== $pfx) continue;
        similar_text($n, $row['norm'], $pct);
        if ($pct > $bestPct) { $bestPct = $pct; $best = $row; }
    }
    return ($best && $bestPct >= 88) ? $best : null;
}
/** Submission dianggap "ngerjain" (Submit Result) kecuali statusnya Apply Expired. */
function is_worked_status($status){
    return strpos(strtolower((string)$status), 'apply expired') === false;
}
function compute_counts($store){
    $c = ['apply_expired'=>0,'submit_result'=>0,'show_up'=>0,'not_show_up'=>0,'stay'=>0,'drop'=>0,'total'=>0,'other'=>0];
    foreach ($store['submissions'] as $s) {
        $c['total']++;
        $st = strtolower($s['status'] ?? '');
        if (strpos($st, 'apply expired') !== false) { $c['apply_expired']++; continue; }
        // Selain Apply Expired = ngerjain (Submit Result). Show Up ditentukan dari ada/tidaknya link match.
        $c['submit_result']++;
        if (!empty($s['everShownUp'])) {
            $c['show_up']++;
            if (!empty($s['shownUpLatest'])) $c['stay']++; else $c['drop']++;
        } else {
            $c['not_show_up']++;
        }
    }
    return $c;
}
/** Set submission ID yang "show up" (shownUpLatest) di snapshot phase tertentu. */
function shown_set($subs){
    $set = [];
    foreach ((array)$subs as $sid => $s) if (!empty($s['shownUpLatest'])) $set[$sid] = true;
    return $set;
}
/**
 * Klasifikasi QC satu submission (sadar-phase). $shownPrev = sudah show up di phase SEBELUMNYA?
 *   expired (Apply Expired) · showup (baru muncul phase ini) · stay (bertahan dari phase lalu) ·
 *   drop (pernah muncul, kini hilang) · notshow (ngerjain tapi tak pernah muncul)
 */
function qc_classify($s, $shownPrev){
    if (strpos(strtolower((string)($s['status'] ?? '')), 'apply expired') !== false) return 'expired';
    if (!empty($s['shownUpLatest'])) return $shownPrev ? 'stay' : 'showup';
    if (!empty($s['everShownUp']))   return 'drop';
    return 'notshow';
}
function qc_label($code){
    static $m = ['expired'=>'Apply Expired','showup'=>'Show Up','stay'=>'Stay','drop'=>'Drop','notshow'=>'Not Show Up'];
    return $m[$code] ?? '—';
}
/** Format tanggal Y-m-d -> "d M" (cth 26 Jun). Kosong -> ''. */
function qc_fmt_date($d){ $d = trim((string)$d); if ($d === '') return ''; $t = strtotime($d); return $t ? date('d M', $t) : $d; }
/** Selisih hari antara dua tanggal (int) atau null kalau salah satu kosong. */
function qc_days_between($from, $to){ $f = strtotime((string)$from); $t = strtotime((string)$to); if (!$f || !$t) return null; return (int)floor(($t - $f) / 86400); }
/** Counts per-phase: Show Up (baru) vs Stay (bertahan) dibedakan via $prevShown. */
function compute_counts_ph($subs, $prevShown = []){
    $c = ['apply_expired'=>0,'submit_result'=>0,'show_up'=>0,'not_show_up'=>0,'stay'=>0,'drop'=>0,'total'=>0];
    foreach ((array)$subs as $sid => $s) {
        $c['total']++;
        $code = qc_classify($s, isset($prevShown[$sid]));
        if ($code === 'expired') { $c['apply_expired']++; continue; }
        $c['submit_result']++;
        if     ($code === 'showup') $c['show_up']++;
        elseif ($code === 'stay')   $c['stay']++;
        elseif ($code === 'drop')   $c['drop']++;
        else                        $c['not_show_up']++;
    }
    return $c;
}
/** Return phases[]; for legacy campaigns without phases, synthesize one from current state. */
function get_phases($store){
    if (!empty($store['phases']) && is_array($store['phases'])) return $store['phases'];
    $ups = $store['uploads'] ?? [];
    return [[
        'phase'          => max(1, count($ups)),
        'ts'             => $store['updated'] ?? '',
        'datasetRows'    => $ups ? ($ups[count($ups)-1]['datasetRows'] ?? 0) : 0,
        'submissionRows' => count($store['submissions'] ?? []),
        'counts'         => compute_counts($store),
        'submissions'    => $store['submissions'] ?? [],
    ]];
}
function render_stats_html($c){
    ob_start(); ?>
    <div class="stats">
      <div class="stat expired"><div class="lab">Apply Expired</div><div class="num"><?= $c['apply_expired'] ?></div></div>
      <div class="stat submit"><div class="lab">Submit Result</div><div class="num"><?= $c['submit_result'] ?></div></div>
      <div class="stat show"><div class="lab">Show Up</div><div class="num"><?= (int)$c['show_up'] + (int)$c['stay'] ?></div></div>
      <div class="stat notshow"><div class="lab">Not Show Up</div><div class="num"><?= $c['not_show_up'] ?></div></div>
      <div class="stat stay"><div class="lab">Stay</div><div class="num"><?= $c['stay'] ?></div></div>
      <div class="stat drop"><div class="lab">Drop</div><div class="num"><?= $c['drop'] ?></div></div>
    </div>
    <?php return ob_get_clean();
}
function render_table_html($submissions, $prevShown = []){
    // hitung per-QC buat chip filter (sadar-phase: Show Up vs Stay)
    $cnt = ['all'=>0,'showup'=>0,'stay'=>0,'drop'=>0,'notshow'=>0,'expired'=>0];
    foreach ((array)$submissions as $sid => $s) {
        $cnt['all']++; $cnt[qc_classify($s, isset($prevShown[$sid]))]++;
    }
    ob_start(); ?>
    <div class="qcfilter">
      <button type="button" class="qcf active" data-qc="all"     onclick="qcFilter(this)">Semua <b><?= $cnt['all'] ?></b></button>
      <button type="button" class="qcf c-showup" data-qc="showup" onclick="qcFilter(this)">Show Up <b><?= $cnt['showup'] ?></b></button>
      <button type="button" class="qcf c-stay"  data-qc="stay"    onclick="qcFilter(this)">Stay <b><?= $cnt['stay'] ?></b></button>
      <button type="button" class="qcf c-drop"  data-qc="drop"    onclick="qcFilter(this)">Drop <b><?= $cnt['drop'] ?></b></button>
      <button type="button" class="qcf c-notshow" data-qc="notshow" onclick="qcFilter(this)">Not Show Up <b><?= $cnt['notshow'] ?></b></button>
      <button type="button" class="qcf c-expired" data-qc="expired" onclick="qcFilter(this)">Apply Expired <b><?= $cnt['expired'] ?></b></button>
    </div>
    <div class="tablewrap">
    <table class="qctable">
      <thead><tr>
        <th>#</th><th>Nama</th><th>Reg Name</th><th>Status</th><th>QC</th><th>Rating</th><th>Tgl Muncul</th><th>Update Data</th><th>Days</th><th>Review</th><th>Bukti SS</th><th>Link</th>
      </tr></thead>
      <tbody>
      <?php $i = 0; foreach ((array)$submissions as $sid => $s): $i++;
        $qcClass = qc_classify($s, isset($prevShown[$sid]));
        $qcLabel = qc_label($qcClass);
        $rowClass = $qcClass === 'drop' ? 'dropped' : '';
        $url = $s['matchedUrl'] ?? '';
        $updRaw = $s['updatedDate'] ?? '';
        $updFmt = qc_fmt_date($updRaw);
        $pubRaw = $s['publishedDate'] ?? '';
        $pubFmt = qc_fmt_date($pubRaw);
        $days   = qc_days_between($pubRaw, $updRaw); // umur review = Update Data − Tgl Muncul (publishedDate)
      ?>
        <tr class="<?= $rowClass ?>" data-qc="<?= $qcClass ?>">
          <td><?= $i ?></td>
          <td><?= h($s['name'] ?? '') ?: '<span class="muted">—</span>' ?></td>
          <td><?= h($s['regName'] ?? '') ?: '<span class="muted">—</span>' ?></td>
          <td><?= h($s['status'] ?? '') ?></td>
          <td><span class="pill <?= $qcClass ?>"><?= $qcLabel ?></span></td>
          <td><?= $s['rating'] ?? '' ? h($s['rating']).'/5' : '<span class="muted">—</span>' ?></td>
          <td class="muted"><?= $pubFmt !== '' ? '<span title="'.h($pubRaw).'">'.h($pubFmt).'</span>' : '—' ?></td>
          <td class="<?= $qcClass==='drop'?'updcell-drop':'' ?>"><?= $updFmt!=='' ? '<span title="'.h($updRaw).($qcClass==='drop'?' · drop':'').'">'.h($updFmt).'</span>' : '<span class="muted">—</span>' ?></td>
          <td><?= $days===null ? '<span class="muted">—</span>' : (int)$days.' <span class="muted">hari</span>' ?></td>
          <td class="rev"><?= h(mb_strimwidth($s['reviewText'] ?? '', 0, 160, '…')) ?: '<span class="muted">—</span>' ?></td>
          <td><?php $pf = $s['proofUrl'] ?? ''; if ($pf): ?><a class="thumb" href="<?= h($pf) ?>" target="_blank" rel="noopener" title="Buka screenshot bukti"><img src="<?= h($pf) ?>" loading="lazy" alt="SS" onerror="this.style.display='none';this.parentNode.classList.add('noimg');this.parentNode.textContent='buka SS ↗';"></a><?php else: ?><span class="muted">—</span><?php endif; ?></td>
          <td><?= $url ? '<a class="lnk" href="'.h($url).'" target="_blank" rel="noopener">buka ↗</a>' : '<span class="muted">—</span>' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php return ob_get_clean();
}
/**
 * Build the cross-campaign jobseeker pool.
 * "Ngerjain" = semua submission selain Apply Expired. Unik per jobseeker_id (fallback: nama).
 * Show Up Pool = ngerjain >= $min & showUp >= 1 · No-Show Pool = ngerjain >= $min & showUp == 0.
 */
function build_pool($DATA_DIR, $min = 2){
    $byKey = [];
    foreach (glob($DATA_DIR . '/*.json') as $f) {
        $d = json_decode(file_get_contents($f), true);
        if (!$d || empty($d['submissions'])) continue;
        $cname = $d['campaign'] ?? ($d['slug'] ?? basename($f, '.json'));
        $phs   = (!empty($d['phases']) && is_array($d['phases'])) ? $d['phases'] : [];
        $prevShown = count($phs) >= 2 ? shown_set($phs[count($phs)-2]['submissions'] ?? []) : [];
        foreach ($d['submissions'] as $sid => $s) {
            if (!is_worked_status($s['status'] ?? '')) continue; // ngerjain = semua selain Apply Expired
            $jid  = trim((string)($s['jobseekerId'] ?? ''));
            $name = trim((string)($s['name'] ?? ''));
            $key  = $jid !== '' ? 'id:'.$jid : ($name !== '' ? 'nm:'.norm($name) : '');
            if ($key === '') continue;
            $showed = !empty($s['everShownUp']);
            $qc = qc_label(qc_classify($s, isset($prevShown[$sid])));
            if (!isset($byKey[$key])) $byKey[$key] = ['id'=>$jid,'name'=>$name,'worked'=>0,'showUp'=>0,'noShow'=>0,'campaigns'=>[],'instances'=>[]];
            $byKey[$key]['id']   = $jid !== '' ? $jid : $byKey[$key]['id'];
            $byKey[$key]['name'] = $name !== '' ? $name : $byKey[$key]['name'];
            $byKey[$key]['worked']++;
            if ($showed) $byKey[$key]['showUp']++; else $byKey[$key]['noShow']++;
            $byKey[$key]['campaigns'][$cname] = true;
            $byKey[$key]['instances'][] = [
                'campaign'=>$cname, 'status'=>$s['status'] ?? '', 'qc'=>$qc,
                'reviewText'=>$s['reviewText'] ?? '', 'matchedUrl'=>$s['matchedUrl'] ?? '',
                'proofUrl'=>$s['proofUrl'] ?? '', 'rating'=>$s['rating'] ?? '',
            ];
        }
    }
    $showup = []; $noshow = [];
    foreach ($byKey as $e) {
        if ($e['worked'] < $min) continue;
        $e['campaigns'] = array_keys($e['campaigns']);
        if ($e['showUp'] >= 1) $showup[] = $e; else $noshow[] = $e;
    }
    $sorter = fn($a,$b) => ($b['worked'] <=> $a['worked']) ?: ($b['showUp'] <=> $a['showUp']);
    usort($showup, $sorter); usort($noshow, $sorter);
    return ['showup'=>$showup, 'noshow'=>$noshow, 'min'=>$min];
}
function render_pool_table($list, $pfx){
    ob_start();
    if (!$list) { echo '<div class="muted">Belum ada jobseeker yang memenuhi kriteria. Pastikan campaign sudah diproses (ulang) supaya <code>jobseeker_id</code> tersimpan.</div>'; return ob_get_clean(); }
    ?>
    <div class="tablewrap">
    <table class="qctable pooltable">
      <thead><tr>
        <th>#</th><th>Jobseeker ID</th><th>Nama</th><th>Ngerjain</th><th>Show Up</th><th>No-Show</th><th>Campaign</th><th></th>
      </tr></thead>
      <tbody>
      <?php $i = 0; foreach ($list as $e): $i++; $rid = $pfx.$i; ?>
        <tr class="poolrow" onclick="togglePool('<?= $rid ?>')" style="cursor:pointer">
          <td><?= $i ?></td>
          <td><?= h($e['id'] ?: '—') ?></td>
          <td><?= h($e['name'] ?: '—') ?></td>
          <td><strong><?= (int)$e['worked'] ?></strong></td>
          <td><span class="pill stay"><?= (int)$e['showUp'] ?></span></td>
          <td><?= (int)$e['noShow'] > 0 ? '<span class="pill drop">'.(int)$e['noShow'].'</span>' : '<span class="muted">0</span>' ?></td>
          <td class="muted"><span class="pcamp" title="<?= h(implode(', ', $e['campaigns'])) ?>"><?= h(implode(', ', $e['campaigns'])) ?></span></td>
          <td class="muted">▾</td>
        </tr>
        <tr id="d_<?= $rid ?>" class="pooldetail" style="display:none">
          <td colspan="8" style="padding:0;background:var(--hover)">
            <div class="pool-ins-wrap">
            <?php foreach ($e['instances'] as $ins): ?>
              <div class="pool-ins">
                <span class="pill <?= $ins['qc']==='Show Up'?'showup':($ins['qc']==='Stay'?'stay':($ins['qc']==='Drop'?'drop':'notshow')) ?>"><?= h($ins['qc']) ?></span>
                <span class="pi-camp" title="<?= h($ins['campaign']) ?>"><?= h($ins['campaign']) ?></span>
                <span class="pi-rev" title="<?= h($ins['reviewText']) ?>"><?= h(mb_strimwidth($ins['reviewText'], 0, 130, '…')) ?: '—' ?></span>
                <span class="pi-link"><?= $ins['proofUrl'] ? '<a class="lnk" href="'.h($ins['proofUrl']).'" target="_blank" rel="noopener">SS ↗</a>' : '<span class="muted">—</span>' ?></span>
                <span class="pi-link"><?= $ins['matchedUrl'] ? '<a class="lnk" href="'.h($ins['matchedUrl']).'" target="_blank" rel="noopener">link ↗</a>' : '<span class="muted">—</span>' ?></span>
              </div>
            <?php endforeach; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php return ob_get_clean();
}

/* ================= Actions ================= */
$notice = ''; $error = ''; $result = null; $activeSlug = '';

/* ---- CSV export (must run before any HTML) ---- */
if (isset($_GET['export'])) {
    $slug = slugify($_GET['export']);
    $file = $DATA_DIR . '/' . $slug . '.json';
    if (file_exists($file)) {
        $store = json_decode(file_get_contents($file), true);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="qc_' . $slug . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF"); // BOM for Excel
        fputcsv($out, ['Submission ID','Full Name','Reg Name','Status','QC Status','Update Data','Days','Drop Date','Review Text','Matched Reviewer','Rating','Tgl Muncul','TripAdvisor URL','Bukti SS (cdn.pintarnya)']);
        $phs = (!empty($store['phases']) && is_array($store['phases'])) ? $store['phases'] : [];
        $prevShown = count($phs) >= 2 ? shown_set($phs[count($phs)-2]['submissions'] ?? []) : [];
        foreach ($store['submissions'] as $sid => $s) {
            $qc   = qc_label(qc_classify($s, isset($prevShown[$sid])));
            $upd  = $s['updatedDate'] ?? '';
            $days = qc_days_between($s['publishedDate'] ?? '', $upd);
            fputcsv($out, [$sid,$s['name']??'',$s['regName']??'',$s['status']??'',$qc,$upd,$days===null?'':$days,$s['droppedDate']??'',$s['reviewText']??'',$s['matchedName']??'',$s['rating']??'',$s['publishedDate']??'',$s['matchedUrl']??'',$s['proofUrl']??'']);
        }
        fclose($out);
    }
    exit;
}

/* ---- Pool CSV export (must run before any HTML) ---- */
if (isset($_GET['pool_export'])) {
    $min  = max(2, (int)($_GET['min'] ?? 2));
    $pool = build_pool($DATA_DIR, $min);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="qc_pool.csv"');
    $out = fopen('php://output', 'w'); fprintf($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Pool','Jobseeker ID','Nama','Ngerjain','Show Up','No-Show','Campaigns']);
    foreach (['Show Up'=>$pool['showup'], 'No-Show'=>$pool['noshow']] as $lbl => $list)
        foreach ($list as $e)
            fputcsv($out, [$lbl, $e['id'], $e['name'], $e['worked'], $e['showUp'], $e['noShow'], implode(' | ', $e['campaigns'])]);
    fclose($out);
    exit;
}

/* ---- Delete campaign ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $slug = slugify($_POST['campaign'] ?? '');
    $file = $DATA_DIR . '/' . $slug . '.json';
    if (file_exists($file)) { @unlink($file); $notice = 'Campaign "' . h($slug) . '" dihapus.'; }
}

/* ---- Process upload ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'process') {
    $mode = (($_POST['campaign_mode'] ?? 'new') === 'continue') ? 'continue' : 'new';
    if ($mode === 'continue') {
        $slugSel  = slugify($_POST['campaign_existing'] ?? '');
        $fSel     = $DATA_DIR . '/' . $slugSel . '.json';
        $campaign = ($slugSel !== '' && file_exists($fSel)) ? (json_decode(file_get_contents($fSel), true)['campaign'] ?? $slugSel) : '';
    } else {
        $campaign = trim($_POST['campaign_new'] ?? '');
    }
    if ($campaign === '') {
        $error = $mode === 'continue' ? 'Pilih campaign yang mau dilanjutkan dulu.' : 'Nama campaign baru wajib diisi.';
    } elseif (empty($_FILES['submission_csv']['tmp_name']) || empty($_FILES['dataset_csv']['tmp_name'])) {
        $error = 'Upload kedua file CSV: submission report + dataset TripAdvisor.';
    } else {
        $slug    = slugify($campaign);
        $subRows = read_csv_file($_FILES['submission_csv']['tmp_name']);
        $dsRows  = read_csv_file($_FILES['dataset_csv']['tmp_name']);

        if (count($subRows) < 2)      $error = 'Submission report kosong / tidak terbaca.';
        elseif (count($dsRows) < 2)   $error = 'Dataset TripAdvisor kosong / tidak terbaca.';
        else {
            $idx = build_dataset_index($dsRows);

            // Detect submission report columns by header
            $sh = $subRows[0]; $sh[0] = strip_bom($sh[0]);
            $cId     = header_index($sh, 'submission_id', true);
            $cJob    = header_index($sh, 'jobseeker_id', true);  // buat Pool List (unik per jobseeker)
            $cStatus = header_index($sh, 'application_status', true);
            $cName   = header_index($sh, 'full_name', true);
            $cReg    = header_index($sh, 'Nama kamu saat registrasi', false);
            $cProof  = header_index($sh, 'Masukan bukti', false);  // kolom bukti SS (cdn.pintarnya)
            // Kolom review (kolom W di submission report Pintarnya). Deteksi berlapis biar
            // tahan ke variasi parsing header HTML yang panjang antar-mission/re-export:
            $cReview = header_index($sh, ['COPY PASTE REVIEW', 'Memberikan RATING'], false); // 1) teks header
            if ($cReview < 0) {                                                              // 2) kolom instruksi "1. Klik link" ke-2 (pertama = registrasi, kedua = review)
                $instr = [];
                foreach ($sh as $ix => $hd) { if (stripos(strip_bom(trim((string)$hd)), '1. Klik link') !== false) $instr[] = $ix; }
                if (count($instr) >= 2) $cReview = $instr[1];
            }
            if ($cReview < 0 && $cProof > 0)  $cReview = $cProof - 1; // 3) kolom tepat sebelum bukti SS
            if ($cReview < 0 && isset($sh[22])) $cReview = 22;        // 4) fallback posisi: kolom W (index 22)

            if ($cReview < 0) {
                $error = 'Kolom review (kolom W / "COPY PASTE REVIEW") tidak ketemu di submission report.';
            } else {
                $file  = $DATA_DIR . '/' . $slug . '.json';
                $store = file_exists($file) ? json_decode(file_get_contents($file), true) : null;
                if (!is_array($store)) {
                    $store = ['campaign'=>$campaign,'slug'=>$slug,'created'=>date('Y-m-d H:i:s'),'uploads'=>[],'submissions'=>[]];
                }
                $now   = date('Y-m-d H:i:s');
                $today = date('Y-m-d'); // tanggal proses (auto) — dipakai buat Update Data / drop date
                $subs = $store['submissions'];
                $prevShownSnap = shown_set($subs); // state phase sebelumnya — buat bedain Show Up (baru) vs Stay (bertahan)
                $seen = [];

                // Pass 1: every submission row in THIS upload
                for ($k = 1; $k < count($subRows); $k++) {
                    $r      = $subRows[$k];
                    $sid    = $cId >= 0 ? trim($r[$cId] ?? '') : '';
                    if ($sid === '') $sid = 'row' . $k;
                    $status = $cStatus >= 0 ? trim($r[$cStatus] ?? '') : '';
                    $name   = $cName   >= 0 ? trim($r[$cName] ?? '') : '';
                    $reg    = $cReg    >= 0 ? trim($r[$cReg] ?? '') : '';
                    $review = $cReview >= 0 ? trim($r[$cReview] ?? '') : '';
                    $proof  = $cProof  >= 0 ? trim($r[$cProof] ?? '') : '';
                    $job    = $cJob    >= 0 ? trim($r[$cJob] ?? '') : '';
                    $seen[$sid] = true;
                    $prev = $subs[$sid] ?? null;

                    $shown = false; $url=''; $mname=''; $rating=''; $date='';
                    $m = $review !== '' ? find_match($review, $idx) : null;
                    if ($m) {
                        $shown = true; $url=$m['url']; $mname=$m['name']; $rating=$m['rating']; $date=$m['date'];
                    } elseif ($prev && !empty($prev['matchedUrl']) && isset($idx['urlset'][trim($prev['matchedUrl'])])) {
                        // previously matched url still present in the new dataset => still shown
                        $shown = true; $url=$prev['matchedUrl']; $mname=$prev['matchedName']??''; $rating=$prev['rating']??''; $date=$prev['publishedDate']??'';
                    }
                    $ever = ($prev['everShownUp'] ?? false) || $shown;
                    $rec = [
                        'jobseekerId'  => $job !== '' ? $job : ($prev['jobseekerId'] ?? ''),
                        'name'         => $name,
                        'regName'      => $reg,
                        'status'       => $status,
                        'reviewText'   => $review,
                        'proofUrl'     => $proof !== '' ? $proof : ($prev['proofUrl'] ?? ''),
                        'everShownUp'  => $ever,
                        'shownUpLatest'=> $shown,
                        'matchedUrl'   => $url  !== '' ? $url  : ($prev['matchedUrl']   ?? ''),
                        'matchedName'  => $mname!== '' ? $mname: ($prev['matchedName']  ?? ''),
                        'rating'       => $rating!=='' ? $rating:($prev['rating']       ?? ''),
                        'publishedDate'=> $date !== '' ? $date : ($prev['publishedDate']?? ''),
                        'firstSeen'    => $prev['firstSeen'] ?? $now,
                        'lastSeen'     => $now,
                    ];
                    $rec['dropped'] = ($rec['everShownUp'] && !$rec['shownUpLatest']);
                    // ==== date stamps (auto: tanggal proses) ====
                    $shownPrev = !empty($prev['shownUpLatest']);
                    $fShown = $prev['firstShownDate'] ?? '';
                    if ($shown && $fShown === '') $fShown = (!empty($prev['everShownUp']) && !empty($prev['firstSeen'])) ? substr($prev['firstSeen'],0,10) : $today;
                    $dDate = $prev['droppedDate'] ?? '';
                    if ($shownPrev && !$shown) $dDate = $today; // baru drop hari ini
                    $rec['firstShownDate'] = $fShown;
                    $rec['droppedDate']    = $dDate;
                    $rec['updatedDate']    = ($rec['everShownUp'] && !$shown) ? ($dDate ?: $today) : $today; // Drop -> beku di tgl drop, selain itu -> tgl cek terakhir
                    $subs[$sid] = $rec;
                }

                // Pass 2: stored submissions NOT in this upload's report — re-check Stay/Drop vs new dataset
                foreach ($subs as $sid => $rec) {
                    if (isset($seen[$sid])) continue;
                    $shownPrev = !empty($rec['shownUpLatest']);
                    $shown = false;
                    if (!empty($rec['matchedUrl']) && isset($idx['urlset'][trim($rec['matchedUrl'])])) {
                        $shown = true;
                    } elseif (!empty($rec['reviewText'])) {
                        $m = find_match($rec['reviewText'], $idx);
                        if ($m) { $shown = true; if (empty($rec['matchedUrl'])) { $rec['matchedUrl']=$m['url']; $rec['matchedName']=$m['name']; $rec['rating']=$m['rating']; $rec['publishedDate']=$m['date']; } }
                    }
                    $rec['shownUpLatest'] = $shown;
                    $rec['everShownUp']   = ($rec['everShownUp'] ?? false) || $shown;
                    $rec['dropped']       = ($rec['everShownUp'] && !$shown);
                    $rec['lastSeen']      = $now;
                    // ==== date stamps (auto: tanggal proses) ====
                    if ($shown && empty($rec['firstShownDate'])) $rec['firstShownDate'] = !empty($rec['firstSeen']) ? substr($rec['firstSeen'],0,10) : $today;
                    if ($shownPrev && !$shown) $rec['droppedDate'] = $today; // baru drop hari ini
                    $rec['updatedDate'] = ($rec['everShownUp'] && !$shown) ? (($rec['droppedDate'] ?? '') ?: $today) : $today;
                    $subs[$sid] = $rec;
                }

                $store['submissions'] = $subs;
                $store['uploads'][]   = ['ts'=>$now, 'submissionRows'=>count($subRows)-1, 'datasetRows'=>count($idx['rows'])];
                if (!isset($store['phases']) || !is_array($store['phases'])) $store['phases'] = [];
                $store['phases'][] = [
                    'phase'          => count($store['phases']) + 1,
                    'ts'             => $now,
                    'datasetRows'    => count($idx['rows']),
                    'submissionRows' => count($subRows) - 1,
                    'counts'         => compute_counts_ph($subs, $prevShownSnap),
                    'submissions'    => $subs,   // snapshot cumulative state at this upload
                ];
                $store['updated']     = $now;
                $store['campaign']    = $campaign;
                file_put_contents($file, json_encode($store, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

                $result = $store; $activeSlug = $slug;
                $notice = 'Berhasil diproses — upload ke-' . count($store['uploads']) . ' untuk campaign "' . h($campaign) . '".';
            }
        }
    }
}

/* ---- View existing campaign ---- */
if (!$result && isset($_GET['view'])) {
    $slug = slugify($_GET['view']);
    $file = $DATA_DIR . '/' . $slug . '.json';
    if (file_exists($file)) { $result = json_decode(file_get_contents($file), true); $activeSlug = $slug; }
}

/* ---- Campaign list ---- */
$campaigns = [];
foreach (glob($DATA_DIR . '/*.json') as $f) {
    $d = json_decode(file_get_contents($f), true);
    if ($d) $campaigns[] = ['slug'=>$d['slug'] ?? basename($f,'.json'), 'name'=>$d['campaign'] ?? basename($f,'.json'), 'updated'=>$d['updated'] ?? ''];
}
usort($campaigns, fn($a,$b)=>strcmp($b['updated'],$a['updated']));

$counts = $result ? compute_counts($result) : null;

/* ---- Pool List view ---- */
$showPool  = isset($_GET['pool']);
$minWorked = max(2, (int)($_GET['min'] ?? 2));
$pool      = $showPool ? build_pool($DATA_DIR, $minWorked) : null;

// Default tab: pool -> pool · fresh -> new · viewing saved campaign -> check · right after upload -> continue
$defaultMode = $showPool ? 'pool' : (!$result ? 'new' : (isset($_GET['view']) ? 'check' : 'continue'));

/* ================= Login screen ================= */
function render_login(){ ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>QC Otomatis — Login</title>
<style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0f172a;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0}
.box{background:#1e293b;padding:32px;border-radius:14px;width:320px;box-shadow:0 10px 40px rgba(0,0,0,.4)}
h1{color:#fff;font-size:18px;margin:0 0 16px}input{width:100%;box-sizing:border-box;padding:12px;border-radius:8px;border:1px solid #334155;background:#0f172a;color:#fff;margin-bottom:12px}
button{width:100%;padding:12px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:600;cursor:pointer}</style></head>
<body><form class="box" method="post"><h1>🔒 QC Otomatis — Smart Buzzer</h1>
<input type="password" name="qc_pass" placeholder="Password" autofocus>
<button type="submit" name="qc_login" value="1">Masuk</button></form></body></html>
<?php }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>QC Otomatis — Smart Buzzer</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<meta name="color-scheme" content="dark light">
<script>(function(){try{var t=localStorage.getItem('qc_theme')||'dark';document.documentElement.setAttribute('data-theme',t);}catch(e){}})();</script>
<style>
:root{
  --bg:#0f172a;--panel:#1e293b;--panel2:#172033;--line:#334155;--txt:#e2e8f0;--mut:#94a3b8;
  --input:#0b1220;--inputh:#0c1426;--hover:rgba(255,255,255,.03);--lnk:#7dd3fc;
  --blue:#3b82f6;--green:#22c55e;--red:#ef4444;--orange:#f59e0b;--gray:#64748b;--cyan:#0ea5e9;
  --shadow:0 10px 30px rgba(0,0,0,.30);
}
html[data-theme="light"]{
  --bg:#eaeef4;--panel:#ffffff;--panel2:#f6f8fc;--line:#e2e8f1;--txt:#0f172a;--mut:#5a6a85;
  --input:#f4f7fb;--inputh:#eef2f8;--hover:rgba(15,23,42,.035);--lnk:#0369a1;
  --shadow:0 6px 22px rgba(15,23,42,.08);
}
*{box-sizing:border-box}
html{background:var(--bg)}
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--txt);transition:background .25s ease,color .25s ease}
.wrap{max-width:1240px;margin:0 auto;padding:24px}
.head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.head h1{font-size:20px;margin:0;display:flex;align-items:center;gap:10px}
.badge{background:var(--input);border:1px solid var(--line);color:var(--mut);font-size:12px;padding:4px 10px;border-radius:999px}
.card{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:18px;margin-bottom:18px;box-shadow:var(--shadow)}
.card h2{font-size:14px;text-transform:uppercase;letter-spacing:.04em;color:var(--mut);margin:0 0 14px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
label{display:block;font-size:13px;color:var(--mut);margin:0 0 6px}
input[type=text],select{width:100%;padding:11px;border-radius:9px;border:1px solid var(--line);background:var(--input);color:var(--txt);font-size:14px}
input[type=file]{width:100%;padding:9px;border-radius:9px;border:1px dashed var(--line);background:var(--input);color:var(--mut);font-size:13px}
.dropzone{border:1.5px dashed var(--line);background:var(--input);border-radius:11px;padding:18px 14px;text-align:center;cursor:pointer;transition:all .15s ease;user-select:none}
.dropzone:hover{border-color:var(--blue);background:var(--inputh)}
.dropzone.dragover{border-color:var(--blue);background:rgba(59,130,246,.12);box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.dropzone.has-file{border-style:solid;border-color:var(--green);background:rgba(34,197,94,.06)}
.dz-icon{font-size:24px;line-height:1}
.dz-text{font-size:13px;color:var(--mut);margin-top:7px}
.dz-text strong{color:var(--txt)}
.dz-file{font-size:12px;margin-top:6px;word-break:break-all}
.dropzone.has-file .dz-file{color:#86efac;font-weight:600}
.btn{display:inline-block;border:0;border-radius:9px;padding:11px 18px;font-weight:600;font-size:14px;cursor:pointer;text-decoration:none}
.btn-primary{background:var(--blue);color:#fff}
.btn-ghost{background:transparent;border:1px solid var(--line);color:var(--txt)}
.btn-danger{background:transparent;border:1px solid var(--red);color:#fca5a5}
.row{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
.row>div{flex:1;min-width:200px}
.stats{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}
@media(max-width:880px){.stats{grid-template-columns:repeat(2,1fr)}.grid{grid-template-columns:1fr}}
.stat{background:var(--panel2);border:1px solid var(--line);border-radius:12px;padding:14px;border-left:4px solid var(--gray)}
.stat .lab{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut)}
.stat .num{font-size:30px;font-weight:800;margin-top:4px}
.stat.expired{border-left-color:var(--gray)} .stat.submit{border-left-color:var(--blue)}
.stat.show{border-left-color:var(--cyan)} .stat.notshow{border-left-color:var(--red)}
.stat.stay{border-left-color:var(--green)} .stat.drop{border-left-color:var(--orange)}
.notice{padding:11px 14px;border-radius:9px;margin-bottom:14px;font-size:14px}
.notice.ok{background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.4);color:#86efac}
.notice.err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5}
table{width:100%;border-collapse:collapse;font-size:13px}
th,td{text-align:left;padding:9px 10px;border-bottom:1px solid var(--line);vertical-align:top}
th{color:var(--mut);font-size:11px;text-transform:uppercase;letter-spacing:.04em;position:sticky;top:0;background:var(--panel)}
tr:hover td{background:var(--hover)}
.pill{display:inline-block;padding:2px 9px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap}
.pill.stay{background:rgba(34,197,94,.15);color:#86efac}
.pill.drop{background:rgba(245,158,11,.15);color:#fcd34d}
.pill.notshow{background:rgba(239,68,68,.15);color:#fca5a5}
.pill.expired{background:rgba(100,116,139,.18);color:#cbd5e1}
.pill.showup{background:rgba(14,165,233,.16);color:#7dd3fc}
.rev{max-width:340px;color:var(--mut);font-size:12px;line-height:1.45}
.lnk{color:var(--lnk);text-decoration:none}.lnk:hover{text-decoration:underline}
.toolbar{display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:12px}
.search{max-width:260px}
.muted{color:var(--mut);font-size:12px}
.tablewrap{max-height:620px;overflow:auto;border-radius:10px;border:1px solid var(--line)}
.dropped td .rev{text-decoration:line-through;opacity:.7}
.updcell-drop{background:rgba(245,158,11,.16);font-weight:700;color:#fcd34d}
html[data-theme="light"] .updcell-drop{color:#b45309}
.thumb{display:inline-block;line-height:0}
.thumb img{width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--line);transition:transform .15s ease;box-shadow:0 2px 6px rgba(0,0,0,.18)}
.thumb img:hover{transform:scale(1.08)}
.thumb.noimg{color:var(--lnk);font-size:12px;line-height:1.3;text-decoration:none}
.theme-btn{padding:7px 11px;font-size:15px;line-height:1}
.seg{display:inline-flex;background:var(--input);border:1px solid var(--line);border-radius:10px;padding:3px;gap:3px}
.seg-btn{background:transparent;border:0;color:var(--mut);padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s ease}
.seg-btn.active{background:var(--blue);color:#fff}
.seg-btn:hover:not(.active){color:var(--txt)}
.sum-head{display:flex;align-items:center;gap:12px;cursor:pointer;margin-bottom:14px}
.sum-head h2{flex:1;min-width:0}
.sum-mini{display:none;font-size:13px;white-space:nowrap}
.sum-toggle{padding:1px 12px;font-size:18px;line-height:1}
.card.collapsed .stats{display:none}
.card.collapsed .sum-head{margin-bottom:0}
.card.collapsed .sum-mini{display:inline}
.phase-head{display:flex;align-items:center;gap:12px;cursor:pointer;margin-bottom:14px}
.phase-head h2{flex:1;min-width:0}
.phase-card.collapsed .phase-head{margin-bottom:0}
.phase-card.collapsed .phase-body{display:none}
.phase-quick{display:none;font-size:13px;white-space:nowrap}
.phase-card.collapsed .phase-quick{display:inline}
.tag-new{display:inline-block;background:var(--green);color:#04210f;font-size:10px;font-weight:800;padding:2px 7px;border-radius:999px;letter-spacing:.03em;vertical-align:middle}
.phase-divider{display:flex;align-items:center;color:var(--mut);font-size:11px;text-transform:uppercase;letter-spacing:.08em;margin:2px 0 18px}
.phase-divider::before,.phase-divider::after{content:"";flex:1;height:1px;background:var(--line)}
.phase-divider span{padding:0 14px}
.qcfilter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.qcf{background:var(--input);border:1px solid var(--line);color:var(--mut);padding:6px 13px;border-radius:999px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s ease}
.qcf b{color:var(--txt);margin-left:2px}
.qcf:hover{border-color:var(--blue)}
.qcf.active{background:var(--blue);border-color:var(--blue);color:#fff}
.qcf.active b{color:#fff}
.qcf.c-stay.active{background:var(--green);border-color:var(--green)}
.qcf.c-drop.active{background:var(--orange);border-color:var(--orange);color:#3a2400}
.qcf.c-drop.active b{color:#3a2400}
.qcf.c-notshow.active{background:var(--red);border-color:var(--red)}
.qcf.c-expired.active{background:var(--gray);border-color:var(--gray)}
.qcf.c-showup.active{background:var(--cyan);border-color:var(--cyan)}
.pooltable td{vertical-align:middle}
.poolrow td{font-weight:500}
.poolrow .pcamp{max-width:420px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:bottom}
.pool-ins-wrap{padding:4px 12px 6px}
.pool-ins{display:grid;grid-template-columns:62px 240px minmax(0,1fr) 50px 56px;gap:16px;align-items:center;padding:8px 2px;border-top:1px solid var(--line);font-size:12px}
.pool-ins:first-child{border-top:0}
.pool-ins .pi-camp{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pool-ins .pi-rev{color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.4}
.pool-ins .pi-link{white-space:nowrap}
@media(max-width:880px){.pool-ins{grid-template-columns:60px minmax(0,1fr) 46px 52px;gap:10px}.pool-ins .pi-rev{display:none}}
html[data-theme="light"] .notice.ok{color:#15803d}
html[data-theme="light"] .notice.err{color:#b91c1c}
html[data-theme="light"] .pill.stay{color:#15803d}
html[data-theme="light"] .pill.drop{color:#b45309}
html[data-theme="light"] .pill.notshow{color:#b91c1c}
html[data-theme="light"] .pill.expired{color:#475569}
html[data-theme="light"] .pill.showup{color:#0369a1}
html[data-theme="light"] .badge{color:#475569}
html[data-theme="light"] th{background:var(--panel)}
</style>
</head>
<body>
<div class="wrap">
  <div class="head">
    <h1>🧪 QC Otomatis <span class="muted" style="font-weight:400">— Smart Buzzer</span></h1>
    <div style="display:flex;gap:8px;align-items:center">
      <span class="badge">TripAdvisor Review Matcher</span>
      <button id="themeBtn" type="button" class="btn btn-ghost theme-btn" onclick="toggleTheme()" title="Ganti tema (light/dark)">🌙</button>
      <?php if ($QC_PASSWORD !== ''): ?><a class="btn btn-ghost" href="?logout=1" style="padding:6px 12px;font-size:12px">Logout</a><?php endif; ?>
    </div>
  </div>

  <?php if ($notice): ?><div class="notice ok"><?= $notice ?></div><?php endif; ?>
  <?php if ($error):  ?><div class="notice err"><?= h($error) ?></div><?php endif; ?>

  <!-- ===== Upload form ===== -->
  <div class="card">
    <h2>Input Data</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="process">
      <input type="hidden" name="campaign_mode" id="campaignMode" value="<?= h($defaultMode) ?>">

      <div class="seg" style="margin-bottom:14px">
        <button type="button" class="seg-btn" data-mode="new" onclick="setMode('new')">＋ New Campaign</button>
        <button type="button" class="seg-btn" data-mode="continue" onclick="setMode('continue')">↻ Continue Campaign</button>
        <button type="button" class="seg-btn" data-mode="check" onclick="setMode('check')">🔍 Cek Data</button>
        <button type="button" class="seg-btn" data-mode="pool" onclick="location.href='?pool=1'">🏊 Pool List</button>
      </div>

      <div class="grid" style="margin-bottom:14px">
        <div>
          <div id="modeNew">
            <label>Nama Campaign Baru</label>
            <input type="text" name="campaign_new" id="campaignNew" placeholder="cth: HGI Las Vegas Juli" value="<?= $activeSlug ? '' : h($result['campaign'] ?? '') ?>" oninput="checkDup()">
            <div class="muted" id="dupWarn" style="margin-top:6px">Nama unik buat campaign baru.</div>
          </div>
          <div id="modeContinue" style="display:none">
            <label>Pilih Campaign Tersimpan</label>
            <?php if($campaigns): ?>
            <select name="campaign_existing" id="campaignExisting" onchange="updContinue()">
              <option value="">— pilih campaign —</option>
              <?php foreach($campaigns as $c): ?>
                <option value="<?= h($c['slug']) ?>" <?= $activeSlug===$c['slug']?'selected':'' ?>><?= h($c['name']) ?> (<?= h($c['updated']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <div class="muted" style="margin-top:6px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
              <span>Lanjutkan QC → hitung Stay/Drop vs upload sebelumnya.</span>
              <a id="viewLink" class="lnk" style="display:none" href="#">Lihat hasil sekarang ↗</a>
            </div>
            <?php else: ?>
            <div class="muted">Belum ada campaign tersimpan. Bikin dulu lewat <strong>New Campaign</strong>.</div>
            <?php endif; ?>
          </div>
          <div id="modeCheck" style="display:none">
            <label>Pilih Campaign buat Dicek</label>
            <?php if($campaigns): ?>
            <select id="campaignCheck">
              <option value="">— pilih campaign —</option>
              <?php foreach($campaigns as $c): ?>
                <option value="<?= h($c['slug']) ?>" <?= $activeSlug===$c['slug']?'selected':'' ?>><?= h($c['name']) ?> (<?= h($c['updated']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <div style="margin-top:10px"><button type="button" class="btn btn-primary" onclick="goCheck()">Tampilkan Data</button></div>
            <div class="muted" style="margin-top:6px">Lihat semua phase tersimpan (read-only) — tanpa perlu upload file.</div>
            <?php else: ?>
            <div class="muted">Belum ada campaign tersimpan.</div>
            <?php endif; ?>
          </div>
          <div id="modePool" style="display:none">
            <label>Pool List</label>
            <div class="muted">Daftar jobseeker yang ngerjain &ge;<?= (int)$minWorked ?>x ditampilkan di bawah ↓ (unik per <code>jobseeker_id</code>, agregat semua campaign).</div>
          </div>
        </div>
        <div></div>
        <div class="upcol">
          <label>1. Submission Report (.csv)</label>
          <div class="dropzone" data-for="submission_csv">
            <input type="file" name="submission_csv" id="submission_csv" accept=".csv" required hidden>
            <div class="dz-icon">📄</div>
            <div class="dz-text"><strong>Klik</strong> atau drag &amp; drop CSV ke sini</div>
            <div class="dz-file muted">Belum ada file</div>
          </div>
        </div>
        <div class="upcol">
          <label>2. Dataset TripAdvisor (.csv)</label>
          <div class="dropzone" data-for="dataset_csv">
            <input type="file" name="dataset_csv" id="dataset_csv" accept=".csv" required hidden>
            <div class="dz-icon">📄</div>
            <div class="dz-text"><strong>Klik</strong> atau drag &amp; drop CSV ke sini</div>
            <div class="dz-file muted">Belum ada file</div>
          </div>
        </div>
      </div>
      <button class="btn btn-primary" type="submit" id="processBtn">Proses &amp; Cocokin</button>
    </form>
  </div>

  <?php if ($showPool):
    $su = $pool['showup']; $ns = $pool['noshow'];
  ?>
  <!-- ===== Pool List ===== -->
  <div class="card">
    <div class="toolbar">
      <h2 style="margin:0">🏊 Pool List <span class="muted" style="text-transform:none;letter-spacing:0">· unik per jobseeker_id · agregat semua campaign · ngerjain = selain Apply Expired</span></h2>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <form method="get" style="display:flex;gap:6px;align-items:center;margin:0">
          <input type="hidden" name="pool" value="1">
          <span class="muted">Min ngerjain</span>
          <input type="number" name="min" min="2" value="<?= (int)$minWorked ?>" style="width:64px;padding:8px" onchange="this.form.submit()">
        </form>
        <input class="search" id="search" type="text" placeholder="Cari nama / ID…" onkeyup="filterRows()">
        <a class="btn btn-ghost" href="?pool_export=1&min=<?= (int)$minWorked ?>">Export Pool CSV</a>
      </div>
    </div>
    <div class="seg" style="margin-top:4px">
      <button type="button" class="seg-btn active" data-pool="showup" onclick="poolTab('showup')">✅ Show Up Pool (<?= count($su) ?>)</button>
      <button type="button" class="seg-btn" data-pool="noshow" onclick="poolTab('noshow')">❌ No-Show Pool (<?= count($ns) ?>)</button>
    </div>
    <div class="muted" style="margin-top:8px"><strong>Show Up Pool</strong> = ngerjain &ge;<?= (int)$minWorked ?>x &amp; minimal sekali show up · <strong>No-Show Pool</strong> = ngerjain &ge;<?= (int)$minWorked ?>x tapi tak pernah show up. Klik baris untuk lihat detail.</div>
  </div>

  <div class="card" id="poolShowup"><?= render_pool_table($su, 'su') ?></div>
  <div class="card" id="poolNoshow" style="display:none"><?= render_pool_table($ns, 'ns') ?></div>

  <?php elseif ($result && $counts):
    $phases = get_phases($result);
    $pcount = count($phases);
  ?>
  <!-- ===== Campaign toolbar ===== -->
  <div class="card">
    <div class="toolbar">
      <h2 style="margin:0">Campaign — <?= h($result['campaign']) ?> <span class="muted" style="text-transform:none;letter-spacing:0">· <?= $pcount ?> phase · update <?= h($result['updated'] ?? '') ?></span></h2>
      <div style="display:flex;gap:10px;align-items:center">
        <input class="search" id="search" type="text" placeholder="Cari nama / review / status…" onkeyup="filterRows()">
        <a class="btn btn-ghost" href="?export=<?= h($result['slug']) ?>">Export CSV</a>
        <form method="post" onsubmit="return confirm('Hapus campaign ini (semua phase)?')" style="display:inline">
          <input type="hidden" name="action" value="delete"><input type="hidden" name="campaign" value="<?= h($result['slug']) ?>">
          <button class="btn btn-danger" type="submit">Hapus</button>
        </form>
      </div>
    </div>
    <div class="muted" style="margin-top:4px">
      <strong>Show Up</strong> = baru muncul di phase ini · <strong>Stay</strong> = sudah muncul sebelumnya &amp; masih ada ·
      <strong>Drop</strong> = pernah muncul tapi sekarang hilang · <strong>Not Show Up</strong> = ngerjain tapi tak pernah ketemu link.
    </div>
  </div>

  <?php for ($pi = $pcount - 1; $pi >= 0; $pi--):
    $ph = $phases[$pi];
    $isLatest = ($pi === $pcount - 1);
    $prevShown = $pi > 0 ? shown_set($phases[$pi-1]['submissions'] ?? []) : [];
    $pc = compute_counts_ph($ph['submissions'] ?? [], $prevShown);
  ?>
  <div class="card phase-card <?= $isLatest ? '' : 'collapsed' ?>" id="phase<?= $pi ?>">
    <div class="phase-head" onclick="togglePhase(<?= $pi ?>)">
      <h2 style="margin:0">
        <?= $isLatest ? '● ' : '' ?>PHASE <?= (int)($ph['phase'] ?? $pi+1) ?><?php if ($isLatest): ?> <span class="tag-new">TERBARU</span><?php endif; ?>
        <span class="muted" style="text-transform:none;letter-spacing:0">· <?= h($ph['ts'] ?? '') ?> · dataset <?= (int)($ph['datasetRows'] ?? 0) ?> baris</span>
      </h2>
      <span class="phase-quick muted">Show Up <?= (int)$pc['show_up'] + (int)$pc['stay'] ?> · Stay <?= $pc['stay'] ?> · Drop <?= $pc['drop'] ?></span>
      <button type="button" class="btn btn-ghost sum-toggle"><?= $isLatest ? '–' : '+' ?></button>
    </div>
    <div class="phase-body">
      <?= render_stats_html($pc) ?>
      <div style="height:14px"></div>
      <?= render_table_html($ph['submissions'] ?? [], $prevShown) ?>
    </div>
  </div>
  <?php if ($isLatest && $pcount > 1): ?>
    <div class="phase-divider"><span>data sebelumnya</span></div>
  <?php endif; ?>
  <?php endfor; ?>

  <?php elseif (!$result): ?>
    <div class="card muted">Upload submission report + dataset TripAdvisor untuk mulai QC. Hasil per campaign otomatis tersimpan biar bisa dicek Stay/Drop di scrape berikutnya.</div>
  <?php endif; ?>

  <div class="muted" style="text-align:center;padding:8px 0">Smart Buzzer · QC Otomatis · data tersimpan di <code>/qc_data/</code></div>
</div>

<script>
function filterRows(){
  var s=document.getElementById('search'); if(!s) return;
  var q=s.value.toLowerCase();
  document.querySelectorAll('.qctable tbody tr:not(.pooldetail)').forEach(function(tr){
    tr.style.display = tr.innerText.toLowerCase().indexOf(q)>-1 ? '' : 'none';
  });
}
function togglePhase(i){
  var c=document.getElementById('phase'+i); if(!c) return;
  var col=c.classList.toggle('collapsed');
  var b=c.querySelector('.sum-toggle'); if(b) b.textContent=col?'+':'–';
}
function qcFilter(btn){
  var bar=btn.parentNode, qc=btn.dataset.qc;
  bar.querySelectorAll('.qcf').forEach(function(b){ b.classList.toggle('active', b===btn); });
  var wrap=bar.nextElementSibling; if(!wrap) return;
  wrap.querySelectorAll('tbody tr').forEach(function(tr){
    tr.style.display = (qc==='all' || tr.getAttribute('data-qc')===qc) ? '' : 'none';
  });
}
function goCheck(){
  var s=document.getElementById('campaignCheck');
  if(!s||!s.value){ alert('Pilih campaign dulu.'); return; }
  location.href='?view='+encodeURIComponent(s.value);
}

/* ===== Theme toggle (light/dark) ===== */
function toggleTheme(){
  var h=document.documentElement;
  var t=h.getAttribute('data-theme')==='light'?'dark':'light';
  h.setAttribute('data-theme',t);
  try{localStorage.setItem('qc_theme',t);}catch(e){}
  updThemeBtn();
}
function updThemeBtn(){
  var b=document.getElementById('themeBtn');
  if(b) b.textContent=document.documentElement.getAttribute('data-theme')==='light'?'☀️':'🌙';
}
updThemeBtn();

/* ===== Campaign mode (New / Continue) ===== */
var QC_CAMPAIGNS = <?= json_encode(array_values(array_map(function($c){return ['slug'=>$c['slug'],'name'=>$c['name']];}, $campaigns)), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
function setMode(m){
  var hm=document.getElementById('campaignMode'); if(hm) hm.value=m;
  var ids={new:'modeNew',continue:'modeContinue',check:'modeCheck',pool:'modePool'};
  Object.keys(ids).forEach(function(k){ var el=document.getElementById(ids[k]); if(el) el.style.display=(k===m)?'':'none'; });
  document.querySelectorAll('.seg-btn').forEach(function(b){ b.classList.toggle('active', b.dataset.mode===m); });
  // upload UI (dropzones + Proses) cuma untuk New/Continue
  var up = (m==='new'||m==='continue');
  document.querySelectorAll('.upcol').forEach(function(e){ e.style.display=up?'':'none'; });
  var pb=document.getElementById('processBtn'); if(pb) pb.style.display=up?'':'none';
  var ni=document.getElementById('campaignNew'), ei=document.getElementById('campaignExisting');
  if(ni) ni.required=(m==='new');
  if(ei) ei.required=(m==='continue');
}
function poolTab(w){
  var a=document.getElementById('poolShowup'), b=document.getElementById('poolNoshow');
  if(a) a.style.display = w==='showup'?'':'none';
  if(b) b.style.display = w==='noshow'?'':'none';
  document.querySelectorAll('.seg-btn[data-pool]').forEach(function(x){ x.classList.toggle('active', x.dataset.pool===w); });
}
function togglePool(rid){
  var d=document.getElementById('d_'+rid); if(d) d.style.display = d.style.display==='none'?'':'none';
}
function checkDup(){
  var el=document.getElementById('campaignNew'), w=document.getElementById('dupWarn'); if(!el||!w) return;
  var slug=(el.value||'').trim().toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
  var dup = slug!=='' && QC_CAMPAIGNS.some(function(c){return c.slug===slug;});
  if(dup){ w.innerHTML='⚠ Nama udah ada — pakai <strong>Continue Campaign</strong> biar Stay/Drop kehitung.'; w.style.color='#d97706'; }
  else { w.textContent='Nama unik buat campaign baru.'; w.style.color=''; }
}
function updContinue(){
  var s=document.getElementById('campaignExisting'), vl=document.getElementById('viewLink'); if(!s||!vl) return;
  if(s.value){ vl.style.display='inline'; vl.href='?view='+encodeURIComponent(s.value); }
  else vl.style.display='none';
}

/* ===== Summary collapse ===== */
function toggleSummary(){
  var c=document.getElementById('summaryCard'); if(!c) return;
  var col=c.classList.toggle('collapsed');
  try{localStorage.setItem('qc_sum_collapsed',col?'1':'0');}catch(e){}
  updSumToggle();
}
function updSumToggle(){
  var c=document.getElementById('summaryCard'), b=document.getElementById('sumToggle');
  if(c&&b) b.textContent=c.classList.contains('collapsed')?'+':'–';
}

(function(){
  try{
    var hm=document.getElementById('campaignMode'); if(hm) setMode(hm.value);
    updContinue();
    var c=document.getElementById('summaryCard');
    if(c && localStorage.getItem('qc_sum_collapsed')==='1') c.classList.add('collapsed');
    updSumToggle();
  }catch(e){}
})();

/* ===== Drag & drop file upload ===== */
document.querySelectorAll('.dropzone').forEach(function(dz){
  var input=dz.querySelector('input[type=file]');
  var label=dz.querySelector('.dz-file');

  function setName(){
    if(input.files && input.files.length){
      dz.classList.add('has-file');
      label.textContent='✓ '+input.files[0].name;
    } else {
      dz.classList.remove('has-file');
      label.textContent='Belum ada file';
    }
  }
  function isCsv(f){ return /\.csv$/i.test(f.name) || f.type==='text/csv'; }

  dz.addEventListener('click', function(){ input.click(); });
  input.addEventListener('change', setName);

  ['dragenter','dragover'].forEach(function(ev){
    dz.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); dz.classList.add('dragover'); });
  });
  ['dragleave','dragend'].forEach(function(ev){
    dz.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); dz.classList.remove('dragover'); });
  });
  dz.addEventListener('drop', function(e){
    e.preventDefault(); e.stopPropagation(); dz.classList.remove('dragover');
    var files=e.dataTransfer.files;
    if(!files || !files.length) return;
    if(!isCsv(files[0])){ label.textContent='⚠ harus file .csv'; dz.classList.remove('has-file'); return; }
    try { input.files=files; } catch(err){
      var dt=new DataTransfer(); dt.items.add(files[0]); input.files=dt.files;
    }
    setName();
  });
});
</script>
</body>
</html>
