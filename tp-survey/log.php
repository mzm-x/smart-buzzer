<?php
// tp-survey/log.php v2 — Admin: Overview / Analysis / Responses / Calls / Outbound / Playbook
// 24-question survey. Seg codes: 1T/1N/2T/2N/3T/3N/4T/4N (quadrant + Tourist/Non).
session_start();
$PASSWORD = 'smartbuzzer2025';

if (isset($_GET['logout'])) { unset($_SESSION['tp_auth']); header('Location: log.php'); exit; }
if (isset($_POST['pw'])) {
    if ($_POST['pw'] === $PASSWORD) { $_SESSION['tp_auth'] = true; header('Location: log.php'); exit; }
    $login_err = 'Wrong password';
}
$authed = !empty($_SESSION['tp_auth']);

// ── data ──────────────────────────────────────────────────
$DIR = __DIR__ . '/data';
$RESP_FILE = $DIR . '/responses.json';
$OB_FILE   = $DIR . '/outbound.json';
function tp_read($f) { return file_exists($f) ? (json_decode(file_get_contents($f), true) ?: []) : []; }
function tp_write($f, $d) {
    if (!is_dir(dirname($f))) { mkdir(dirname($f), 0755, true); file_put_contents(dirname($f) . '/.htaccess', "Deny from all\nOptions -Indexes\n"); }
    $fp = fopen($f, 'c+'); if (!$fp) return false;
    flock($fp, LOCK_EX); ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($fp); flock($fp, LOCK_UN); fclose($fp); return true;
}

// ── seg helpers ───────────────────────────────────────────
$SEGS = ['1T','1N','2T','2N','3T','3N','4T','4N'];
$QUAD = ['1'=>'Low rating x Low review','2'=>'Low rating x High review','3'=>'High rating x Low review','4'=>'High rating x High review'];
function seg_quad($s){ $s=strtoupper((string)$s); return preg_match('/^([1-4])[TN]$/',$s,$m)?$m[1]:''; }
function seg_geo($s){ $s=strtoupper((string)$s); if(preg_match('/([TN])$/',$s,$m)) return $m[1]==='T'?'Tourist':'Non-tourist'; return ''; }
$QUADNAME = ['1'=>'low_low','2'=>'low_high','3'=>'high_low','4'=>'high_high'];
function seg_name($s){ $q=seg_quad($s); return $q?$GLOBALS['QUADNAME'][$q]:''; }
function seg_tier($s){ $g=seg_geo($s); return $g==='Tourist'?'tourist':($g==='Non-tourist'?'nontourist':''); }

// ── answer flatten helpers ────────────────────────────────
function av($r,$q){ return trim((string)($r['answers'][$q]['value'] ?? '')); }
function avs($r,$q){ $v=$r['answers'][$q]['values']??[]; return is_array($v)?$v:[]; }
function arev($r,$q){ $o=[]; foreach(($r['answers'][$q]['reveals']??[]) as $k=>$v){ if(trim($v)!=='') $o[]=$k.': '.$v; } return implode(' | ',$o); }
function arevv($r,$q){ // reveal values only for the currently-selected radio option
    $sel=av($r,$q); $rv=$r['answers'][$q]['reveals'][$sel]??''; $rv=trim((string)$rv);
    if($rv==='') foreach(($r['answers'][$q]['reveals']??[]) as $v){ if(trim($v)!==''){$rv=trim($v);break;} }
    return $rv;
}
function aflat($r,$q){ $v=av($r,$q); $x=arevv($r,$q); return $v.($x!==''?' ('.$x.')':''); }
function alist($r,$q){ $v=implode('; ',avs($r,$q)); $x=arev($r,$q); return $v.($x!==''?' ('.$x.')':''); }
function anote($r,$q){ $v=av($r,$q); $n=trim((string)($r['answers'][$q]['note']??'')); return $v.($n!==''?' — '.$n:''); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ── AJAX ──────────────────────────────────────────────────
if ($authed && isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $act=$_GET['action']; $body=json_decode(file_get_contents('php://input'),true)?:[];
    if ($act==='call_status') {
        $id=(int)($body['id']??0); $st=in_array($body['status']??'',['pending','called','skipped'])?$body['status']:null;
        if($id&&$st){ $rs=tp_read($RESP_FILE); foreach($rs as $i=>$r) if((int)($r['id']??0)===$id){$rs[$i]['call_status']=$st;break;} tp_write($RESP_FILE,$rs); die(json_encode(['success'=>true])); }
        die(json_encode(['success'=>false]));
    }
    if ($act==='resp_delete') {
        $id=(int)($body['id']??0); $rs=array_values(array_filter(tp_read($RESP_FILE),fn($r)=>(int)($r['id']??0)!==$id)); tp_write($RESP_FILE,$rs); die(json_encode(['success'=>true]));
    }
    if ($act==='ob_add') {
        $ob=tp_read($OB_FILE); $mx=0; foreach($ob as $o)$mx=max($mx,(int)($o['id']??0));
        $cl=fn($k,$m=200)=>substr(trim(strip_tags((string)($body[$k]??''))),0,$m);
        $ob[]=['id'=>$mx+1,'date'=>$cl('date',20)?:date('Y-m-d'),'business'=>$cl('business'),'city'=>$cl('city',100),'seg'=>$cl('seg',10),'type'=>$cl('type',40),'channel'=>$cl('channel',40),'contact'=>$cl('contact'),'status'=>$cl('status',30)?:'Approached','notes'=>$cl('notes',500),'created_at'=>date('Y-m-d H:i:s')];
        tp_write($OB_FILE,$ob); die(json_encode(['success'=>true]));
    }
    if ($act==='ob_status') {
        $id=(int)($body['id']??0); $st=in_array($body['status']??'',['Approached','Responded','Completed','Declined','No reply'])?$body['status']:null;
        if($id&&$st){ $ob=tp_read($OB_FILE); foreach($ob as $i=>$o) if((int)($o['id']??0)===$id){$ob[$i]['status']=$st;break;} tp_write($OB_FILE,$ob); die(json_encode(['success'=>true])); }
        die(json_encode(['success'=>false]));
    }
    if ($act==='ob_delete') {
        $id=(int)($body['id']??0); $ob=array_values(array_filter(tp_read($OB_FILE),fn($o)=>(int)($o['id']??0)!==$id)); tp_write($OB_FILE,$ob); die(json_encode(['success'=>true]));
    }
    die(json_encode(['success'=>false,'error'=>'Unknown action']));
}

// ── export ────────────────────────────────────────────────
$EXPORT_HEADERS = ['Resp#','Date','Status','Seg','Quadrant','Geo','Type','Channel','Time (s)','Reward','Reward contact',
 'Q1 Role','Q2 Locations','Q3 Rating','Q4 #Reviews','Q5 #1 Goal','Q6 New customers from','Q7 Customers check','Q8 Platforms pushed','Q9 TA driving revenue','Q10 TA used for','Q11 TA maintain','Q12 Biggest challenge','Q13 Eats time','Q14 Cant fix alone','Q15 Neg review','Q16 Get reviews','Q17 Tool today','Q18 Ever paid','Q19 Marketing spend','Q20 Searched before','Q21 Value','Q22 Exp $/mo','Q23 Magic wand','Q24 Call?','Contact name','Contact'];
function export_row($r) {
    $c = $r['answers']['q24']['contact'] ?? [];
    return [
        $r['id']??'', $r['submitted_at']?:($r['updated_at']??''), $r['status']??'',
        strtoupper($r['seg']?:'-'), seg_quad($r['seg']?:'')?:'-', seg_geo($r['seg']?:'')?:'-', $r['type']?:'-',
        $r['channel']?:'-', $r['time_spent_seconds']??0, $r['reward']?:'-', $r['reward_contact']?:'',
        aflat($r,'q1'), av($r,'q2'), av($r,'q3'), av($r,'q4'), aflat($r,'q5'),
        alist($r,'q6'), alist($r,'q7'), alist($r,'q8'), av($r,'q9'), alist($r,'q10'), av($r,'q11'),
        av($r,'q12'), alist($r,'q13'), av($r,'q14'), av($r,'q15'), alist($r,'q16'),
        aflat($r,'q17'), aflat($r,'q18'), anote($r,'q19'), av($r,'q20'),
        av($r,'q21'), av($r,'q22'), av($r,'q23'), av($r,'q24'), $c['name']??'', $c['contact']??'',
    ];
}
if ($authed && isset($_GET['export'])) {
    $rs = tp_read($RESP_FILE); usort($rs, fn($a,$b)=>(int)($b['id']??0)-(int)($a['id']??0));
    if ($_GET['export']==='xls') {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="tp-survey-responses-'.date('Y-m-d').'.xls"');
        $xc=fn($v)=>str_replace(["&","<",">","\r\n","\n"],["&amp;","&lt;","&gt;","<br>","<br>"],(string)$v);
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"><style>td{mso-data-placement:same-cell;vertical-align:top}</style></head><body><table border="1"><tr>';
        foreach($EXPORT_HEADERS as $h) echo '<th>'.$xc($h).'</th>'; echo '</tr>';
        foreach($rs as $r){ echo '<tr>'; foreach(export_row($r) as $c) echo '<td>'.$xc($c).'</td>'; echo '</tr>'; }
        echo '</table></body></html>'; exit;
    }
    if ($_GET['export']==='ob') {
        header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="tp-survey-outbound-'.date('Y-m-d').'.csv"');
        $fp=fopen('php://output','w'); fwrite($fp,"\xEF\xBB\xBF");
        fputcsv($fp,['#','Date','Segment','Type','Business','City','Channel','Contact','Status','Notes']);
        foreach(tp_read($OB_FILE) as $o) fputcsv($fp,[$o['id'],$o['date'],$o['seg'],$o['type']??'',$o['business'],$o['city'],$o['channel'],$o['contact'],$o['status'],$o['notes']]);
        fclose($fp); exit;
    }
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="tp-survey-responses-'.date('Y-m-d').'.csv"');
    $fp=fopen('php://output','w'); fwrite($fp,"\xEF\xBB\xBF");
    fputcsv($fp,$EXPORT_HEADERS); foreach($rs as $r) fputcsv($fp,export_row($r)); fclose($fp); exit;
}

// ── aggregates ────────────────────────────────────────────
$responses = tp_read($RESP_FILE); usort($responses, fn($a,$b)=>(int)($b['id']??0)-(int)($a['id']??0));
$outbound = tp_read($OB_FILE); usort($outbound, fn($a,$b)=>(int)($b['id']??0)-(int)($a['id']??0));

$completed  = array_values(array_filter($responses, fn($r)=>($r['status']??'')==='completed'));
$inprogress = array_values(array_filter($responses, fn($r)=>($r['status']??'')!=='completed'));
$GOAL=20; $nC=count($completed);
$avgTime = $nC ? round(array_sum(array_map(fn($r)=>(int)$r['time_spent_seconds'],$completed))/$nC) : 0;
$calls = array_values(array_filter($completed, fn($r)=>av($r,'q24')==='Yes'));
$pendingCalls = count(array_filter($calls, fn($r)=>($r['call_status']??'pending')==='pending'));
$rewardCount = count(array_filter($completed, fn($r)=>!in_array($r['reward']??'',['','none'])));

$segC = array_fill_keys($SEGS,0); $segC['-']=0;
foreach($completed as $r){ $k=strtoupper($r['seg']?:'-'); if(!isset($segC[$k]))$k='-'; $segC[$k]++; }
$segApp = array_fill_keys($SEGS,0); $segApp['-']=0;
foreach($outbound as $o){ $k=strtoupper($o['seg']?:'-'); if(!isset($segApp[$k]))$k='-'; $segApp[$k]++; }

function tally($rows,$fn){ $t=[]; foreach($rows as $r){ foreach((array)$fn($r) as $v){ $v=trim((string)$v); if($v==='')continue; $t[$v]=($t[$v]??0)+1; } } arsort($t); return $t; }
$tWTP  = tally($completed, fn($r)=>av($r,'q22'));
$tVal  = tally($completed, fn($r)=>av($r,'q21'));
$tAdj  = tally($completed, fn($r)=>avs($r,'q13'));
$tPlat = tally($completed, fn($r)=>avs($r,'q8'));
$tTA   = tally($completed, fn($r)=>av($r,'q9'));
$tPaid = tally($completed, fn($r)=>av($r,'q18'));
$tChan = tally($completed, fn($r)=>$r['channel']?:'direct');
$tType = tally($completed, fn($r)=>$r['type']?:'');
$mustPct = $nC ? round(100*($tVal['Must-have']??0)/$nC) : 0;

$ADJ_SOWHAT=['Replying to reviews'=>'Response-as-a-service add-on','Keeping photos & info updated'=>'Listing/photo management service','Ranking or visibility vs competitors'=>'Rank tracking + competitor alerts','Monitoring what’s said about you'=>'Presence monitoring & alerting','Managing multiple platforms or locations'=>'Unified inbox / multi-location tier','Getting bookings or enquiries from listings'=>'Booking/enquiry optimization'];

function seg_rows($completed,$segApp,$SEGS){
    $out=[];
    foreach($SEGS as $s){
        $rows=array_values(array_filter($completed,fn($r)=>strtoupper($r['seg']?:'')===$s));
        $n=count($rows); if(!$n && empty($segApp[$s])) continue;
        $pct=fn($f)=>$n?count(array_filter($rows,$f))/$n:0;
        $pain=$pct(fn($r)=>in_array('Rating & reviews',avs($r,'q7')));
        $budget=$pct(fn($r)=>av($r,'q18')==='Yes'||in_array(av($r,'q17'),['Paid tool','An agency']));
        $wtp=$pct(fn($r)=>in_array(av($r,'q22'),['$30–75','$75–150','$150–300','$300+']));
        $intent=($pct(fn($r)=>av($r,'q21')==='Must-have')+$pct(fn($r)=>av($r,'q24')==='Yes'))/2;
        $app=(int)($segApp[$s]??0); $eng=$app>0?min(1,$n/$app):0;
        $score=$n?round(5*(.25*$pain+.25*$budget+.20*$wtp+.20*$intent+.10*$eng),2):0;
        $out[]=['seg'=>$s,'n'=>$n,'app'=>$app,'pain'=>$pain,'budget'=>$budget,'wtp'=>$wtp,'intent'=>$intent,'eng'=>$eng,'score'=>$score];
    }
    usort($out,fn($a,$b)=>$b['score']<=>$a['score']); return $out;
}
$segScores = seg_rows($completed,$segApp,$SEGS);
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="robots" content="noindex,nofollow">
<title>TP Survey — Admin</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',system-ui,sans-serif;font-size:14px;background:#F5F5F5;color:#1A1A1A;-webkit-font-smoothing:antialiased}
button{cursor:pointer;font-family:inherit;border:none;background:none} input,select,textarea{font-family:inherit} a{color:#2E7D32}
.topbar{background:#2E7D32;color:#fff;height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 20px;position:sticky;top:0;z-index:50}
.topbar b{font-size:15px}.topbar .sub{opacity:.75;font-size:12px;margin-left:8px}.topbar a{color:#fff;opacity:.85;font-size:13px;text-decoration:none}
.wrap{max-width:1180px;margin:0 auto;padding:18px 16px 60px}
.login{max-width:360px;margin:80px auto;background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.1);padding:28px}
.login h2{font-size:18px;margin-bottom:14px}.login input{width:100%;padding:12px 14px;border:1.5px solid #E0E0E0;border-radius:8px;font-size:15px;outline:none;margin-bottom:12px}.login input:focus{border-color:#2E7D32}
.login button{width:100%;padding:12px;background:#2E7D32;color:#fff;border-radius:8px;font-weight:700;font-size:15px}.login .err{color:#A23E2A;font-size:13px;margin-bottom:10px}
.tabs{display:flex;gap:4px;border-bottom:2px solid #E0E0E0;margin-bottom:18px;flex-wrap:wrap}
.tab-btn{padding:10px 16px;font-size:13.5px;font-weight:600;color:#888;border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;gap:7px;align-items:center}
.tab-btn.active{color:#2E7D32;border-bottom-color:#2E7D32}
.tbadge{background:#EEE;color:#666;font-size:11px;font-weight:700;border-radius:20px;padding:2px 8px}.tab-btn.active .tbadge{background:#E8F5E9;color:#2E7D32}.tbadge.orange{background:#FFF3CD;color:#856404}
.pane{display:none}.pane.active{display:block}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:18px}
.scard{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.07);padding:16px 18px}.scard .n{font-size:24px;font-weight:800;color:#2E7D32}.scard .l{font-size:12px;color:#888;margin-top:2px}
.box{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.07);padding:20px;margin-bottom:16px}.box h3{font-size:14px;font-weight:700;margin-bottom:14px}.box h3 small{color:#999;font-weight:500}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}@media(max-width:800px){.grid2{grid-template-columns:1fr}}
.bar-row{display:flex;align-items:center;gap:10px;margin-bottom:8px}.bar-row .bl{width:230px;font-size:12.5px;color:#444;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.bar-row .bt{flex:1;height:18px;background:#F0F0F0;border-radius:4px;overflow:hidden}.bar-row .bf{height:100%;background:#66BB6A;border-radius:4px;min-width:2px}.bar-row .bn{width:78px;font-size:12px;color:#666;font-variant-numeric:tabular-nums}
.goal-track{height:12px;background:#E0E0E0;border-radius:6px;overflow:hidden;margin:8px 0 4px}.goal-fill{height:100%;background:linear-gradient(90deg,#43A047,#2E7D32);border-radius:6px}
.quad{margin-bottom:12px}.quad .qt{font-size:12.5px;font-weight:700;margin-bottom:4px}
table{width:100%;border-collapse:collapse;font-size:13px}th{background:#FAFAFA;text-align:left;padding:9px 10px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.03em;border-bottom:1px solid #E8E8E8;white-space:nowrap}td{padding:10px;border-bottom:1px solid #F0F0F0;vertical-align:top}tr:hover td{background:#FAFBFA}.tbl-scroll{overflow-x:auto}
.badge{display:inline-block;font-size:11px;font-weight:700;border-radius:20px;padding:3px 9px;white-space:nowrap}.b-done{background:#E8F5E9;color:#2E7D32}.b-prog{background:#FFF3CD;color:#856404}.b-seg{background:#E3F2FD;color:#1565C0}.b-star{background:#FFF3E0;color:#B26A00}
.detail-row{display:none}.detail-row.open{display:table-row}.detail-row td{background:#FAFAF7;font-size:12.5px}.dgrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:8px 18px;padding:4px}.dgrid div b{color:#666;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.03em;display:block}
.gold{background:#FFFDE7;border-left:3px solid #FBC02D;padding:8px 10px;border-radius:0 6px 6px 0;margin-top:4px}
.btn{padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;transition:.15s}.btn-green{background:#2E7D32;color:#fff}.btn-green:hover{background:#1B5E20}.btn-soft{background:#E8F5E9;color:#2E7D32}.btn-soft:hover{background:#C8E6C9}.btn-ghost{background:#fff;border:1.5px solid #E0E0E0;color:#555}.btn-red{background:#FDECEA;color:#A23E2A}
.act-btn{font-size:11.5px;font-weight:700;padding:5px 10px;border-radius:6px;margin-right:4px}
.head-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;flex-wrap:wrap}.head-row h3{margin:0}
.ob-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:6px}.ob-form input,.ob-form select{padding:10px 12px;border:1.5px solid #E0E0E0;border-radius:8px;font-size:13.5px;outline:none;width:100%}.ob-form .full{grid-column:1/-1}select.mini{padding:5px 8px;border:1px solid #E0E0E0;border-radius:6px;font-size:12px}
.pb h4{font-size:14px;margin:18px 0 8px;color:#1B5E20}.pb p,.pb li{font-size:13.5px;line-height:1.65;color:#333}.pb ul,.pb ol{padding-left:22px;margin:6px 0}.pb .rule{background:#FDECEA;border-left:3px solid #A23E2A;padding:10px 14px;border-radius:0 8px 8px 0;margin:8px 0;font-weight:600}.pb .tip{background:#E8F5E9;border-left:3px solid #2E7D32;padding:10px 14px;border-radius:0 8px 8px 0;margin:8px 0}.pb code{background:#F0F0F0;padding:2px 6px;border-radius:4px;font-size:12px}.pb table td,.pb table th{font-size:12.5px}
.empty{text-align:center;color:#999;padding:36px 10px;font-size:13.5px}
</style></head><body>

<?php if (!$authed): ?>
<div class="login"><h2>TP Survey — Admin</h2>
<?php if(!empty($login_err)) echo '<div class="err">'.e($login_err).'</div>'; ?>
<form method="post"><input type="password" name="pw" placeholder="Password" autofocus><button type="submit">Login</button></form></div>
<?php else: ?>

<div class="topbar"><div><b>TP Survey Admin</b><span class="sub">Tripadvisor Presence PMF Research</span></div><a href="?logout=1">Logout</a></div>
<div class="wrap">

<div class="tabs">
  <button class="tab-btn active" data-t="overview">Overview</button>
  <button class="tab-btn" data-t="analysis">Analysis</button>
  <button class="tab-btn" data-t="responses">Responses <span class="tbadge"><?= $nC ?></span></button>
  <button class="tab-btn" data-t="calls">Call List <?php if($pendingCalls): ?><span class="tbadge orange"><?= $pendingCalls ?> pending</span><?php elseif(count($calls)): ?><span class="tbadge"><?= count($calls) ?></span><?php endif; ?></button>
  <button class="tab-btn" data-t="outbound">Outbound <span class="tbadge"><?= count($outbound) ?></span></button>
  <button class="tab-btn" data-t="playbook">&#128213; Playbook</button>
</div>

<!-- OVERVIEW -->
<div class="pane active" id="pane-overview">
  <div class="cards">
    <div class="scard"><div class="n"><?= $nC ?> / <?= $GOAL ?></div><div class="l">Completed (goal)</div></div>
    <div class="scard"><div class="n"><?= count($inprogress) ?></div><div class="l">In progress / drop-off</div></div>
    <div class="scard"><div class="n"><?= $avgTime ? floor($avgTime/60).'m '.($avgTime%60).'s' : '—' ?></div><div class="l">Avg completion time</div></div>
    <div class="scard"><div class="n"><?= count($calls) ?></div><div class="l">Yes to call (warm)</div></div>
    <div class="scard"><div class="n"><?= $mustPct ?>%</div><div class="l">Must-have (Q21)</div></div>
    <div class="scard"><div class="n"><?= $rewardCount ?></div><div class="l">Reward claimed</div></div>
  </div>
  <div class="box">
    <h3>Goal Progress <small>— 20 completed surveys, bukan 20 approach</small></h3>
    <div class="goal-track"><div class="goal-fill" style="width:<?= min(100,round(100*$nC/$GOAL)) ?>%"></div></div>
    <div style="font-size:12px;color:#888"><?= $nC ?> completed &middot; <?= max(0,$GOAL-$nC) ?> to go</div>
  </div>
  <div class="grid2">
    <div class="box">
      <h3>Completed per Category (Tourist vs Non) <small>vs approached</small></h3>
      <?php foreach(['1','2','3','4'] as $qd):
        $t='completed '.$qd; $ct=$segC[$qd.'T']; $cn=$segC[$qd.'N']; $at=$segApp[$qd.'T']; $an=$segApp[$qd.'N'];
        $mx=max(1,$ct,$cn); ?>
      <div class="quad">
        <div class="qt"><?= $qd ?>. <?= e($QUAD[$qd]) ?></div>
        <div class="bar-row"><span class="bl">Tourist (<?= $qd ?>T)</span><div class="bt"><div class="bf" style="width:<?= round(100*$ct/$mx) ?>%"></div></div><span class="bn"><?= $ct ?><?= $at?'/'.$at:'' ?></span></div>
        <div class="bar-row"><span class="bl">Non-tourist (<?= $qd ?>N)</span><div class="bt"><div class="bf" style="width:<?= round(100*$cn/$mx) ?>%;background:#42A5F5"></div></div><span class="bn"><?= $cn ?><?= $an?'/'.$an:'' ?></span></div>
      </div>
      <?php endforeach; ?>
      <div style="font-size:11.5px;color:#999;margin-top:6px">Completion rate per segment = sinyal segment mana paling engaged (Rule #2).</div>
    </div>
    <div class="box">
      <h3>Channel</h3>
      <?php $mxc=max(1,$tChan?max($tChan):1); foreach($tChan as $k=>$v): ?>
      <div class="bar-row"><span class="bl"><?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mxc) ?>%"></div></div><span class="bn"><?= $v ?></span></div>
      <?php endforeach; if(!$tChan) echo '<div class="empty">Belum ada data.</div>'; ?>
    </div>
  </div>
</div>

<!-- ANALYSIS -->
<div class="pane" id="pane-analysis">
  <?php if(!$nC): ?><div class="box empty">Belum ada completed response. Analysis muncul setelah data masuk.</div><?php else: ?>
  <div class="grid2">
    <div class="box">
      <h3>WTP — Expected $/month (Q22)</h3>
      <?php $ord=['<$30','$30–75','$75–150','$150–300','$300+',"Wouldn't pay"]; $mw=max(1,$tWTP?max($tWTP):1);
      foreach($ord as $k){ $v=$tWTP[$k]??0; ?>
      <div class="bar-row"><span class="bl"><?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mw) ?>%"></div></div><span class="bn"><?= $v ?> &middot; <?= round(100*$v/$nC) ?>%</span></div>
      <?php } ?>
      <div style="font-size:11.5px;color:#999;margin-top:8px">Target tier: $59–$129.</div>
    </div>
    <div class="box">
      <h3>Value (Q21) &amp; Ever Paid (Q18)</h3>
      <?php $mv=max(1,$tVal?max($tVal):1); foreach(['Must-have','Nice-to-have','Not needed'] as $k){ $v=$tVal[$k]??0; ?>
      <div class="bar-row"><span class="bl"><?= $k ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mv) ?>%"></div></div><span class="bn"><?= $v ?> &middot; <?= round(100*$v/$nC) ?>%</span></div>
      <?php } ?><div style="height:12px"></div>
      <?php $mp=max(1,$tPaid?max($tPaid):1); foreach(['Yes','Considered it','No'] as $k){ $v=$tPaid[$k]??0; ?>
      <div class="bar-row"><span class="bl">Ever paid: <?= $k ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mp) ?>%;background:#42A5F5"></div></div><span class="bn"><?= $v ?></span></div>
      <?php } ?>
    </div>
    <div class="box">
      <h3>Adjacent Needs (Q13) <small>— produk berikutnya</small></h3>
      <?php $ma=max(1,$tAdj?max($tAdj):1); foreach($tAdj as $k=>$v){ ?>
      <div class="bar-row"><span class="bl" title="<?= e($ADJ_SOWHAT[$k]??'') ?>"><?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$ma) ?>%;background:#AB47BC"></div></div><span class="bn"><?= $v ?></span></div>
      <?php if(isset($ADJ_SOWHAT[$k])) echo '<div style="font-size:11px;color:#999;margin:-4px 0 8px 0">&rarr; '.e($ADJ_SOWHAT[$k]).'</div>'; } if(!$tAdj) echo '<div class="empty">—</div>'; ?>
    </div>
    <div class="box">
      <h3>Platforms pushed (Q8) &amp; TA driving revenue (Q9)</h3>
      <?php $mpl=max(1,$tPlat?max($tPlat):1); foreach($tPlat as $k=>$v){ ?>
      <div class="bar-row"><span class="bl"><?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mpl) ?>%"></div></div><span class="bn"><?= $v ?></span></div>
      <?php } ?><div style="height:12px"></div>
      <?php $mt=max(1,$tTA?max($tTA):1); foreach($tTA as $k=>$v){ ?>
      <div class="bar-row"><span class="bl">TA: <?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mt) ?>%;background:#26A69A"></div></div><span class="bn"><?= $v ?></span></div>
      <?php } ?>
      <div style="font-size:11.5px;color:#999;margin-top:8px">Kalau non-tourist bilang TA nggak jalan tapi Google iya → pivot valid ke produk Google (rule #4).</div>
    </div>
    <div class="box">
      <h3>Business Type <small>— dari lead source (tp-outbound)</small></h3>
      <?php $mty=max(1,$tType?max($tType):1); foreach($tType as $k=>$v){ ?>
      <div class="bar-row"><span class="bl"><?= e($k) ?></span><div class="bt"><div class="bf" style="width:<?= round(100*$v/$mty) ?>%;background:#7E57C2"></div></div><span class="bn"><?= $v ?> &middot; <?= round(100*$v/$nC) ?>%</span></div>
      <?php } if(!$tType) echo '<div class="empty">Belum ada data type (link survey belum bawa <code>type</code>).</div>'; ?>
    </div>
  </div>
  <div class="box">
    <h3>Segment Scoring <small>— auto (pain 25 / budget 25 / WTP 20 / intent 20 / engagement 10). Judgment final baca Q12/Q23 di bawah.</small></h3>
    <div class="tbl-scroll"><table>
      <thead><tr><th>Rank</th><th>Segment</th><th>Category</th><th>n</th><th>Appr.</th><th>Pain (Q7 reviews)</th><th>Budget (Q17/18)</th><th>WTP &ge;$30</th><th>Intent (Q21+Q24)</th><th>Engage</th><th>Score /5</th></tr></thead>
      <tbody>
      <?php $rank=1; foreach($segScores as $s){ $qd=seg_quad($s['seg']); ?>
      <tr>
        <td><b><?= $s['n']?$rank++:'—' ?></b></td>
        <td><span class="badge b-seg"><?= e(seg_name($s['seg'])?:$s['seg']) ?></span></td>
        <td style="font-size:12px"><?= $qd?e($QUAD[$qd]):'—' ?> · <?= e(seg_geo($s['seg'])) ?></td>
        <td><?= $s['n'] ?></td><td><?= $s['app']?:'—' ?></td>
        <td><?= round($s['pain']*100) ?>%</td><td><?= round($s['budget']*100) ?>%</td><td><?= round($s['wtp']*100) ?>%</td><td><?= round($s['intent']*100) ?>%</td>
        <td><?= $s['app']?round($s['eng']*100).'%':'—' ?></td><td><b><?= number_format($s['score'],2) ?></b></td>
      </tr>
      <?php } if(!$segScores) echo '<tr><td colspan="11" class="empty">Belum ada data per segment.</td></tr>'; ?>
      </tbody></table></div>
  </div>
  <div class="box">
    <h3>&#11088; Open-Text Gold — Q12 challenge · Q14 can't-fix · Q20 searched · Q23 magic wand</h3>
    <?php $gold=false; foreach($completed as $r){ $q12=av($r,'q12'); $q14=av($r,'q14'); $q20=av($r,'q20'); $q23=av($r,'q23'); if($q12===''&&$q14===''&&$q20===''&&$q23==='') continue; $gold=true; ?>
    <div style="margin-bottom:14px">
      <span class="badge b-seg"><?= e(seg_name($r['seg'])?:($r['seg']?:'—')) ?></span>
      <span style="font-size:12px;color:#999">#<?= $r['id'] ?> &middot; <?= e(aflat($r,'q1')) ?> &middot; <?= e(av($r,'q22')) ?></span>
      <?php if($q12!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q12 CHALLENGE</b><br>'.e($q12).'</div>';
            if($q14!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q14 CANT FIX ALONE</b><br>'.e($q14).'</div>';
            if($q20!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q20 SEARCHED BEFORE</b><br>'.e($q20).'</div>';
            if($q23!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q23 MAGIC WAND</b><br>'.e($q23).'</div>'; ?>
    </div>
    <?php } if(!$gold) echo '<div class="empty">Belum ada jawaban open-text.</div>'; ?>
  </div>
  <?php endif; ?>
</div>

<!-- RESPONSES -->
<div class="pane" id="pane-responses">
  <div class="box">
    <div class="head-row">
      <h3>All Responses (<?= count($responses) ?>) <small>— <?= $nC ?> completed, <?= count($inprogress) ?> in-progress</small></h3>
      <div><a href="?export=csv" class="btn btn-soft" style="text-decoration:none">&#8595; Export CSV</a> <a href="?export=xls" class="btn btn-soft" style="text-decoration:none">&#8595; Export XLS</a></div>
    </div>
    <?php if(!$responses): ?><div class="empty">Belum ada respons.</div><?php else: ?>
    <div class="tbl-scroll"><table>
      <thead><tr><th>#</th><th>Date</th><th>Status</th><th>Segment</th><th>CityTier</th><th>Type</th><th>Channel</th><th>Role (Q1)</th><th>Grow (Q5)</th><th>WTP (Q22)</th><th>Call</th><th>Reward</th><th>Time</th><th></th></tr></thead>
      <tbody>
      <?php foreach($responses as $r){ $done=($r['status']??'')==='completed'; ?>
      <tr>
        <td style="color:#999"><?= $r['id'] ?></td>
        <td style="white-space:nowrap"><?= e(substr($r['submitted_at']?:($r['updated_at']??''),0,16)) ?></td>
        <td><span class="badge <?= $done?'b-done':'b-prog' ?>"><?= $done?'Completed':'Q'.(int)($r['last_q']??0).' drop' ?></span></td>
        <td><span class="badge b-seg"><?= e(seg_name($r['seg'])?:(strtoupper($r['seg']?:'—'))) ?></span></td>
        <td><?= e(seg_tier($r['seg'])?:'—') ?></td>
        <td><?= e($r['type']?:'—') ?></td>
        <td><?= e($r['channel']?:'—') ?></td>
        <td><?= e(aflat($r,'q1')?:'—') ?></td>
        <td><?= e(aflat($r,'q5')?:'—') ?></td>
        <td><?= e(av($r,'q22')?:'—') ?></td>
        <td><?= av($r,'q24')==='Yes'?'<span class="badge b-star">YES</span>':e(av($r,'q24')?:'—') ?></td>
        <td style="font-size:12px"><?= e($r['reward']&&$r['reward']!=='none'?$r['reward']:'—') ?></td>
        <td style="white-space:nowrap"><?= $r['time_spent_seconds']?floor($r['time_spent_seconds']/60).'m '.($r['time_spent_seconds']%60).'s':'—' ?></td>
        <td style="white-space:nowrap"><button class="act-btn btn-soft" onclick="toggleDetail(<?= $r['id'] ?>)">Details</button><button class="act-btn btn-red" onclick="delResp(<?= $r['id'] ?>)">&times;</button></td>
      </tr>
      <tr class="detail-row" id="det-<?= $r['id'] ?>"><td colspan="14">
        <div class="dgrid">
          <div><b>Q2 Locations</b><?= e(av($r,'q2')?:'—') ?></div>
          <div><b>Q3 Rating</b><?= e(av($r,'q3')?:'—') ?></div>
          <div><b>Q4 #Reviews</b><?= e(av($r,'q4')?:'—') ?></div>
          <div><b>Q6 New customers from</b><?= e(alist($r,'q6')?:'—') ?></div>
          <div><b>Q7 Customers check</b><?= e(alist($r,'q7')?:'—') ?></div>
          <div><b>Q8 Platforms pushed</b><?= e(alist($r,'q8')?:'—') ?></div>
          <div><b>Q9 TA driving revenue</b><?= e(av($r,'q9')?:'—') ?></div>
          <div><b>Q10 TA used for</b><?= e(alist($r,'q10')?:'—') ?></div>
          <div><b>Q11 TA maintain</b><?= e(av($r,'q11')?:'—') ?></div>
          <div><b>Q13 Eats time</b><?= e(alist($r,'q13')?:'—') ?></div>
          <div><b>Q15 Neg review</b><?= e(av($r,'q15')?:'—') ?></div>
          <div><b>Q16 Get reviews</b><?= e(alist($r,'q16')?:'—') ?></div>
          <div><b>Q17 Tool today</b><?= e(aflat($r,'q17')?:'—') ?></div>
          <div><b>Q18 Ever paid</b><?= e(aflat($r,'q18')?:'—') ?></div>
          <div><b>Q19 Marketing spend</b><?= e(anote($r,'q19')?:'—') ?></div>
          <div><b>Q21 Value</b><?= e(av($r,'q21')?:'—') ?></div>
          <div><b>Q24 Contact</b><?= e(trim(($r['answers']['q24']['contact']['name']??'').' '.($r['answers']['q24']['contact']['contact']??''))?:'—') ?></div>
          <div><b>Reward</b><?= e(($r['reward']?:'—').($r['reward_contact']?' · '.$r['reward_contact']:'')) ?></div>
        </div>
        <?php if(av($r,'q12')!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q12 BIGGEST CHALLENGE</b><br>'.e(av($r,'q12')).'</div>';
              if(av($r,'q14')!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q14 CANT FIX ALONE</b><br>'.e(av($r,'q14')).'</div>';
              if(av($r,'q20')!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q20 SEARCHED BEFORE</b><br>'.e(av($r,'q20')).'</div>';
              if(av($r,'q23')!=='') echo '<div class="gold"><b style="font-size:11px;color:#B26A00">Q23 MAGIC WAND</b><br>'.e(av($r,'q23')).'</div>'; ?>
      </td></tr>
      <?php } ?>
      </tbody></table></div>
    <?php endif; ?>
  </div>
</div>

<!-- CALLS -->
<div class="pane" id="pane-calls">
  <div class="box">
    <div class="head-row"><h3>Call Requests (<?= count($calls) ?>) <small>— Q24 "Yes" = warm list</small></h3><?php if($pendingCalls): ?><span style="font-size:13px;color:#856404;font-weight:600">&#9203; <?= $pendingCalls ?> pending</span><?php endif; ?></div>
    <?php if(!$calls): ?><div class="empty">Belum ada yang minta call.<br><small>Respondent yang jawab "Yes" di Q24 muncul di sini.</small></div>
    <?php else: ?>
    <div class="tbl-scroll"><table>
      <thead><tr><th>#</th><th>Date</th><th>Name / Contact</th><th>Segment</th><th>Role</th><th>WTP</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach($calls as $r){ $st=$r['call_status']?:'pending'; $c=$r['answers']['q24']['contact']??[]; ?>
      <tr id="cr-<?= $r['id'] ?>">
        <td style="color:#999"><?= $r['id'] ?></td>
        <td style="white-space:nowrap"><?= e(substr($r['submitted_at'],0,10)) ?></td>
        <td><?php if(!empty($c['name'])) echo '<b>'.e($c['name']).'</b><br>'; ?><span style="color:#666"><?= e($c['contact']??'—') ?></span></td>
        <td><span class="badge b-seg"><?= e(seg_name($r['seg'])?:(strtoupper($r['seg']?:'—'))) ?></span></td>
        <td><?= e(aflat($r,'q1')) ?></td>
        <td><?= e(av($r,'q22')?:'—') ?></td>
        <td><span class="badge <?= $st==='called'?'b-done':($st==='skipped'?'':'b-prog') ?>"><?= ucfirst($st) ?></span></td>
        <td style="white-space:nowrap">
          <?php if($st==='pending'): ?>
            <button class="act-btn btn-soft" onclick="callStatus(<?= $r['id'] ?>,'called')">Mark Called</button>
            <button class="act-btn btn-ghost" onclick="callStatus(<?= $r['id'] ?>,'skipped')">Skip</button>
          <?php else: ?><button class="act-btn btn-ghost" onclick="callStatus(<?= $r['id'] ?>,'pending')">Undo</button><?php endif; ?>
        </td>
      </tr>
      <?php } ?>
      </tbody></table></div>
    <?php endif; ?>
  </div>
</div>

<!-- OUTBOUND -->
<div class="pane" id="pane-outbound">
  <div class="box">
    <h3>Add Approach <small>— log tiap bisnis yang lu approach</small></h3>
    <div class="ob-form">
      <input type="date" id="obDate" value="<?= date('Y-m-d') ?>">
      <select id="obSeg"><option value="">Segment…</option><?php foreach($SEGS as $s){ $qd=seg_quad($s); echo '<option value="'.$s.'">'.$s.' — '.$QUAD[$qd].' ('.seg_geo($s).')</option>'; } ?></select>
      <select id="obType"><option value="">Type…</option><option>Restaurant</option><option>Hotel</option><option>Things to Do</option></select>
      <input type="text" id="obBiz" placeholder="Business name">
      <input type="text" id="obCity" placeholder="City">
      <select id="obChan"><option value="">Channel…</option><option>whatsapp</option><option>email</option><option>instagram</option><option>call</option><option>walk-in</option><option>other</option></select>
      <input type="text" id="obContact" placeholder="Contact (email/WA/IG)">
      <input type="text" id="obNotes" placeholder="Notes" class="full">
    </div>
    <button class="btn btn-green" onclick="obAdd()">+ Add</button>
    <span style="font-size:12px;color:#999;margin-left:10px">Link per lead: <code>tp-survey/?seg=high_low&amp;tier=tourist&amp;type=restaurant&amp;channel=email</code> (auto di kolom Survey_Link XLS tp-outbound)</span>
  </div>
  <div class="box">
    <div class="head-row"><h3>Approach Log (<?= count($outbound) ?>)</h3><a href="?export=ob" class="btn btn-soft" style="text-decoration:none">&#8595; Export CSV</a></div>
    <?php if(!$outbound): ?><div class="empty">Belum ada approach yang di-log.</div>
    <?php else: ?>
    <div class="tbl-scroll"><table>
      <thead><tr><th>#</th><th>Date</th><th>Segment</th><th>Type</th><th>Business</th><th>City</th><th>Channel</th><th>Contact</th><th>Status</th><th>Notes</th><th></th></tr></thead>
      <tbody>
      <?php foreach($outbound as $o){ ?>
      <tr>
        <td style="color:#999"><?= $o['id'] ?></td><td style="white-space:nowrap"><?= e($o['date']) ?></td>
        <td><span class="badge b-seg"><?= e(seg_name($o['seg'])?:(strtoupper($o['seg']?:'—'))) ?></span></td>
        <td><?= e($o['type']??'—'?:'—') ?></td>
        <td><b><?= e($o['business']) ?></b></td><td><?= e($o['city']) ?></td><td><?= e($o['channel']) ?></td><td><?= e($o['contact']) ?></td>
        <td><select class="mini" onchange="obStatus(<?= $o['id'] ?>,this.value)"><?php foreach(['Approached','Responded','Completed','Declined','No reply'] as $s) echo '<option '.(($o['status']??'')===$s?'selected':'').'>'.$s.'</option>'; ?></select></td>
        <td style="max-width:200px"><?= e($o['notes']) ?></td>
        <td><button class="act-btn btn-red" onclick="obDelete(<?= $o['id'] ?>)">&times;</button></td>
      </tr>
      <?php } ?>
      </tbody></table></div>
    <?php endif; ?>
  </div>
</div>

<!-- PLAYBOOK -->
<div class="pane" id="pane-playbook">
  <div class="box pb">
    <h3>&#128213; Playbook — Segment Codes, Leads, Timeline</h3>
    <div class="rule">RULE #1 — SURVEY = NO UPSELL. Reward muncul SETELAH selesai, bukan dijanjiin di copy blast.</div>
    <div class="rule">RULE #2 — GOAL = 20 COMPLETED, bukan 20 approach. Completion rate per segment = sinyal.</div>
    <div class="rule">RULE #3 — JANGAN ads sebelum tau buyer-nya siapa.</div>

    <h4>1. Segment Codes (4 kategori × Tourist/Non = 8)</h4>
    <table>
      <thead><tr><th>Kategori</th><th>Filter TA</th><th>Tourist code</th><th>Non code</th></tr></thead>
      <tbody>
        <tr><td><b>1.</b> Low rating × Low review</td><td>rating &lt; 4.0 &amp; &lt; 50 reviews</td><td><code>1T</code></td><td><code>1N</code></td></tr>
        <tr><td><b>2.</b> Low rating × High review</td><td>rating &lt; 4.0 &amp; &ge; 50 reviews</td><td><code>2T</code></td><td><code>2N</code></td></tr>
        <tr><td><b>3.</b> High rating × Low review</td><td>rating &ge; 4.0 &amp; &lt; 50 reviews</td><td><code>3T</code></td><td><code>3N</code></td></tr>
        <tr><td><b>4.</b> High rating × High review</td><td>rating &ge; 4.0 &amp; &ge; 50 reviews</td><td><code>4T</code></td><td><code>4N</code></td></tr>
      </tbody>
    </table>
    <div class="tip"><b>Link per lead (auto di kolom <code>Survey_Link</code> XLS tp-outbound):</b> format deskriptif bawa segment + tier + type, jadi admin tau kriteria tiap responden.<br>
      Contoh (High×Low, Tourist, Restaurant): <code>https://smart-buzzer.com/tp-survey/?seg=high_low&amp;tier=tourist&amp;type=restaurant&amp;channel=email</code><br>
      Non-tourist: ganti <code>tier=nontourist</code>. WA: ganti <code>channel=whatsapp</code>. (Kode lama <code>?seg=3T</code> masih jalan.)<br>
      Threshold segment (samain sama tp-outbound): High = rating &ge; 4.0, Low review = &lt; 50.</div>

    <h4>2. Sumber Leads (TA-first)</h4>
    <ul>
      <li><b>TripAdvisor top-list</b> per kota — filter sesuai tabel di atas (bubble + review count TA).</li>
      <li><b>/outbound/ Apify scraper</b> — cuma buat ambil kontak (email/WA). TA nggak expose kontak.</li>
      <li><b>FB Group</b> hospitality owners US.</li>
    </ul>

    <h4>3. Timeline (start 13 Jul, solo)</h4>
    <table>
      <thead><tr><th>Periode</th><th>Aktivitas</th><th>Target</th></tr></thead>
      <tbody>
        <tr><td>7-10 Jul</td><td>Website live, kumpulin 300 leads, finalisasi copy &amp; reward</td><td>300 leads siap</td></tr>
        <tr><td>13-15 Jul</td><td>Blast 300 leads (200 Email, 100 WA — WA dibagi 13 &amp; 14). Blast jam 19:00-24:00 WIB (= pagi US)</td><td>3-5 completed</td></tr>
        <tr><td>17 Jul</td><td>Follow-up #1 ke yang belum isi</td><td>10 completed</td></tr>
        <tr><td>20 Jul</td><td>Follow-up #2 + top-up leads segment yang seret</td><td>20 completed</td></tr>
        <tr><td>24 Jul</td><td>Analisis final — pilih 1-2 segment pemenang, mulai call</td><td>Keputusan segment</td></tr>
      </tbody>
    </table>

    <h4>4. Composition (300 leads)</h4>
    <table>
      <thead><tr><th>Kategori</th><th>Tourist</th><th>Non</th><th>Total</th></tr></thead>
      <tbody>
        <tr><td>1. Low × Low</td><td>25</td><td>25</td><td>50</td></tr>
        <tr><td>2. Low × High</td><td>45</td><td>45</td><td>90</td></tr>
        <tr><td>3. High × Low</td><td>45</td><td>45</td><td>90</td></tr>
        <tr><td>4. High × High</td><td>35</td><td>35</td><td>70</td></tr>
        <tr><td><b>Total</b></td><td><b>150</b></td><td><b>150</b></td><td><b>300</b></td></tr>
      </tbody>
    </table>

    <h4>5. Copywriting Outreach (English, no-pitch)</h4>
    <div class="tip"><b>WhatsApp / first touch:</b> Hi [name]! I came across [business name] — congrats on the great rating. We're doing independent research on how local hospitality businesses manage their online presence &amp; reviews. No sales, just a few quick questions (~5 min): [link]. Finish it and get a free gift (10 Tripadvisor reviews or 1,000 IG followers) + a benchmark report for your business 🎁</div>
    <div class="tip"><b>Email subject:</b> quick research + a free gift for [business name] — <b>Body:</b> Hi [name], we're researching how businesses like [business name] manage their online presence &amp; reviews. 100% research — no sales. ~5 min: [link]. As a thank-you: your pick of a free gift AND a benchmark report emailed to you on how similar businesses compare.</div>
    <div class="tip"><b>Follow-up #1 (48 jam):</b> Hi [name], floating this back up — the 5-min research closes soon and we'd value [business name]'s input: [link]. Free gift when you finish 🎁</div>
    <div class="tip"><b>Follow-up #2 (terakhir):</b> Last nudge! Wrapping up the research this week. 5 min + a free gift for your business: [link]. Thanks either way!</div>
    <p style="font-size:12.5px;color:#888">Aturan: sebut nama bisnis (personal &gt; massal), kirim jam 19:00-24:00 WIB (= pagi US), max 2× follow-up, jangan ubah intro copy survey.</p>

    <h4>6. Reward — 2 bait (di-teaser di cover, di-capture di kolom WA + Email)</h4>
    <ul>
      <li><b>Bait 1 — Free gift (pilih 1 di akhir):</b> 10 Tripadvisor reviews ATAU 1.000 Instagram followers. Disebut spesifik di cover biar nggak nebak-nebak.</li>
      <li><b>Bait 2 — Free benchmark report</b> dikirim ke email: "gimana bisnis sejenis lu dibanding." Ini yang bikin mereka kasih email + alasan natural buat follow-up (pintu PMSF).</li>
      <li>Di <b>cover</b> ada 2 kolom: WhatsApp + Email (optional) — kecatet duluan biar lead nggak ilang walau drop. Di admin muncul di kolom <code>Reward contact</code> (format "WA: xxx · Email: yyy").</li>
      <li>Biaya: 10 TA reviews buffer 70% → ~17 submit × Rp3.000 = Rp51.000/user. 1.000 followers buffer 30% → ~1.300 submit × Rp100 = Rp130.000/user. Est. total (10+10 user) ≈ <b>Rp1.810.000</b>.</li>
      <li><b>Report WAJIB beneran dikirim</b> — kalau nggak, rusak trust &amp; nama brand. Siapin template report simpel (rating/spend/channel rata-rata) sebelum blast.</li>
    </ul>

    <h4>6. Decision Rules (setelah ~20 data)</h4>
    <ol>
      <li>Segment menang = pain tinggi + budget EXISTS + WTP &ge; target + banyak yang mau call.</li>
      <li>Pilih 1 (max 2) segment buat unscalable selling.</li>
      <li>Nggak ada yang lolos → jangan scale, refine, ulangi.</li>
      <li>Q9 non-tourist bilang TA nggak jalan tapi Google iya → pivot ke produk Google.</li>
      <li>Q24 "Yes" = warm list = bahan bakar PMSF.</li>
    </ol>
  </div>
</div>

</div>
<script>
document.querySelectorAll('.tab-btn').forEach(function(b){ b.addEventListener('click',function(){ document.querySelectorAll('.tab-btn').forEach(function(x){x.classList.remove('active')}); document.querySelectorAll('.pane').forEach(function(x){x.classList.remove('active')}); b.classList.add('active'); document.getElementById('pane-'+b.dataset.t).classList.add('active'); }); });
function post(a,d){ return fetch('log.php?action='+a,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(d)}).then(function(r){return r.json()}); }
function toggleDetail(id){ document.getElementById('det-'+id).classList.toggle('open'); }
function delResp(id){ if(!confirm('Delete response #'+id+'?')) return; post('resp_delete',{id:id}).then(function(){location.reload()}); }
function callStatus(id,st){ post('call_status',{id:id,status:st}).then(function(){location.reload()}); }
function obAdd(){ var d={date:obDate.value,seg:obSeg.value,type:obType.value,business:obBiz.value.trim(),city:obCity.value.trim(),channel:obChan.value,contact:obContact.value.trim(),notes:obNotes.value.trim(),status:'Approached'}; if(!d.business){alert('Business name wajib');return;} post('ob_add',d).then(function(){location.reload()}); }
function obStatus(id,st){ post('ob_status',{id:id,status:st}); }
function obDelete(id){ if(!confirm('Delete approach #'+id+'?')) return; post('ob_delete',{id:id}).then(function(){location.reload()}); }
</script>
<?php endif; ?>
</body></html>
