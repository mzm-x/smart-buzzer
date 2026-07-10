<?php
session_start();
$PASS = 'smartbuzzer2025';
if (!isset($_SESSION['tpob_auth'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['pass'] ?? '') === $PASS) {
        $_SESSION['tpob_auth'] = true;
    } else {
        ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Smart Buzzer TripAdvisor Outbound</title>
        <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',sans-serif;background:#F8FAFC;display:flex;align-items:center;justify-content:center;min-height:100vh;color:#1E293B}
        .login{background:#fff;border-radius:16px;padding:40px;max-width:380px;width:90%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #E2E8F0}
        .login img{height:40px;margin-bottom:24px}.login h2{font-size:20px;margin-bottom:8px}.login p{color:#64748B;font-size:14px;margin-bottom:24px}
        .login input{width:100%;padding:14px 16px;background:#F8FAFC;border:1.5px solid #D1D5DB;border-radius:10px;color:#1E293B;font-size:15px;outline:none;margin-bottom:16px}
        .login input:focus{border-color:#00AA6C}.login button{width:100%;padding:14px;background:#00AA6C;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer}
        .login button:hover{background:#008f5b}</style></head><body>
        <form class="login" method="POST"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
        <h2>TripAdvisor Outbound</h2><p>Enter password to access</p>
        <input type="password" name="pass" placeholder="Password" autofocus>
        <button type="submit">Login</button></form></body></html><?php
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Buzzer — TripAdvisor Outbound</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#F8FAFC;--bg2:#fff;--bg3:#F1F5F9;--bg4:#E2E8F0;
    --tx:#1E293B;--tx2:#334155;--tx3:#64748B;--tx4:#94A3B8;
    --bd:#E2E8F0;--bd2:#D1D5DB;
    --accent:#00AA6C;--accent2:#008f5b;
    --green:#16A34A;--greenBg:#F0FDF4;--greenBd:#BBF7D0;
    --amber:#D97706;--amberBg:#FFFBEB;
    --red:#DC2626;--redBg:#FEF2F2;
    --purple:#9333EA;--purpleBg:#F5F3FF;
    --blue:#2563EB;--blueBg:#EFF6FF;
    --shadow:0 1px 3px rgba(0,0,0,.05);
}
[data-theme="dark"]{
    --bg:#0F172A;--bg2:#1E293B;--bg3:#1E293B;--bg4:#334155;
    --tx:#E2E8F0;--tx2:#CBD5E1;--tx3:#94A3B8;--tx4:#64748B;
    --bd:#334155;--bd2:#475569;
    --green:#4ADE80;--greenBg:#052e16;--greenBd:#14532D;
    --amber:#FBBF24;--amberBg:#451A03;
    --red:#F87171;--redBg:#450a0a;
    --purple:#C084FC;--purpleBg:#2E1065;
    --blue:#60A5FA;--blueBg:#0c1e3d;
    --shadow:0 1px 3px rgba(0,0,0,.3);
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--tx);font-size:13px;min-height:100vh;transition:background .2s,color .2s}
a{color:var(--accent);text-decoration:none}a:hover{text-decoration:underline}

.hdr{background:var(--bg2);border-bottom:1px solid var(--bd);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow)}
.hdr img{height:28px}
.hdr-l{display:flex;align-items:center;gap:10px}
.hdr-tag{font-size:11px;font-weight:700;color:var(--accent);background:var(--greenBg);padding:3px 8px;border-radius:5px;border:1px solid var(--greenBd)}
.hdr-r{display:flex;align-items:center;gap:8px}
.badge{padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:var(--bg3);color:var(--tx3)}
.badge-g{background:var(--greenBg);color:var(--green)}

.thm{width:34px;height:18px;border-radius:9px;background:var(--bd2);border:none;cursor:pointer;position:relative;transition:background .2s}
.thm::after{content:'';position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;background:#fff;transition:transform .2s;box-shadow:0 1px 2px rgba(0,0,0,.2)}
[data-theme="dark"] .thm{background:var(--accent)}
[data-theme="dark"] .thm::after{transform:translateX(16px)}

.ctn{max-width:1440px;margin:0 auto;padding:20px}

.stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:16px}
.stat{background:var(--bg2);border:1px solid var(--bd);border-radius:10px;padding:14px 16px;box-shadow:var(--shadow);transition:background .2s}
.stat-v{font-size:22px;font-weight:700;color:var(--tx)}
.stat-l{font-size:10px;color:var(--tx4);margin-top:2px;text-transform:uppercase;letter-spacing:.3px}
.usage-bar{background:var(--bd);border-radius:3px;height:6px;overflow:hidden;margin-top:8px}
.usage-fill{height:100%;border-radius:3px;transition:width .3s}

.tabs{display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid var(--bd);flex-wrap:wrap}
.tab{padding:10px 20px;font-size:13px;font-weight:600;color:var(--tx3);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;background:none;border-top:none;border-left:none;border-right:none;font-family:inherit}
.tab:hover{color:var(--tx)}
.tab.on{color:var(--accent);border-bottom-color:var(--accent)}
.tab-panel{display:none}.tab-panel.on{display:block}

.g2{display:grid;grid-template-columns:380px 1fr;gap:20px}
@media(max-width:1024px){.g2{grid-template-columns:1fr}.stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.stats{grid-template-columns:1fr 1fr}}

.cd{background:var(--bg2);border:1px solid var(--bd);border-radius:10px;overflow:hidden;box-shadow:var(--shadow);transition:background .2s}
.cd-h{padding:12px 16px;border-bottom:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between;background:var(--bg3)}
.cd-h h3{font-size:13px;font-weight:600;color:var(--tx)}
.cd-b{padding:16px}

.fg{margin-bottom:12px}
.fl{display:block;font-size:10px;font-weight:600;color:var(--tx3);margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px}
.fi,.fs,.ft{width:100%;padding:8px 11px;background:var(--bg);border:1.5px solid var(--bd);border-radius:7px;color:var(--tx);font-size:12px;font-family:inherit;outline:none;transition:border .15s}
.fi:focus,.fs:focus,.ft:focus{border-color:var(--accent)}
.ft{min-height:60px;resize:vertical}
.fs{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;background-size:13px;padding-right:28px;background-color:var(--bg)}
.fh{font-size:9px;color:var(--tx4);margin-top:2px}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:8px}

/* CONTENT TYPE CHECKS */
.cks{display:flex;gap:6px;flex-wrap:wrap}
.ck{flex:1;min-width:90px;display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid var(--bd);border-radius:7px;cursor:pointer;background:var(--bg);transition:all .12s;font-size:11px;font-weight:500;color:var(--tx2)}
.ck:hover{border-color:var(--accent)}
.ck.on{border-color:var(--accent);background:var(--greenBg);color:var(--accent)}
.ck input{width:14px;height:14px;accent-color:var(--accent);cursor:pointer}
.enrich{display:flex;align-items:center;gap:8px;padding:9px 11px;border:1.5px dashed var(--bd2);border-radius:7px;background:var(--bg);cursor:pointer}
.enrich input{width:15px;height:15px;accent-color:var(--accent)}
.enrich-t{font-size:11px;font-weight:600;color:var(--tx2)}
.enrich-h{font-size:9px;color:var(--amber)}

.btn{padding:7px 14px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;transition:all .15s;font-family:inherit}
.btn-p{background:var(--accent);color:#fff;width:100%;padding:9px 16px}
.btn-p:hover{background:var(--accent2)}
.btn-p:disabled{background:var(--bd);color:var(--tx4);cursor:not-allowed}
.btn-s{padding:4px 9px;font-size:10px;border-radius:5px}
.btn-g{background:#059669;color:#fff}.btn-g:hover{background:#047857}
.btn-o{background:var(--bg);border:1.5px solid var(--bd);color:var(--tx3)}
.btn-o:hover{border-color:var(--accent);color:var(--accent)}
.btn-r{background:var(--redBg);color:var(--red);border:1px solid var(--red)}

.pw{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:4px}
.pill{padding:3px 8px;border:1.5px solid var(--bd);border-radius:5px;font-size:10px;font-weight:500;cursor:pointer;background:var(--bg);color:var(--tx3);transition:all .12s;display:inline-flex;align-items:center;gap:3px}
.pill:hover{border-color:var(--accent);color:var(--accent)}
.pill.on{background:var(--accent);color:#fff;border-color:var(--accent)}
.pw-lbl{font-size:9px;font-weight:700;color:var(--tx4);text-transform:uppercase;letter-spacing:.5px;margin:6px 0 3px}

.tw{overflow-x:auto;max-height:calc(100vh - 220px);overflow-y:auto}
table{width:100%;border-collapse:collapse;font-size:11px;min-width:820px}
thead{background:var(--bg3);position:sticky;top:0;z-index:2}
th{padding:7px 8px;text-align:left;font-size:9px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;border-bottom:1px solid var(--bd);white-space:nowrap}
td{padding:7px 8px;border-bottom:1px solid var(--bg3);white-space:nowrap;max-width:180px;overflow:hidden;text-overflow:ellipsis;color:var(--tx2)}
tr:hover td{background:var(--bg3)}
.cc{width:28px;text-align:center}
input[type="checkbox"]{width:14px;height:14px;accent-color:var(--accent);cursor:pointer}
.stars{color:#F59E0B;font-size:10px}
.rn{color:var(--tx4);margin-left:2px;font-size:10px}
.ct-b{font-size:9px;font-weight:600;padding:1px 5px;border-radius:4px;background:var(--bg3);color:var(--tx3)}

/* SEGMENT BADGE */
.sb{font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;display:inline-block;white-space:nowrap}

.st{padding:2px 6px;border-radius:4px;font-size:9px;font-weight:600;display:inline-block}
.st-ok{background:var(--greenBg);color:var(--green)}
.st-run{background:var(--bg4);color:var(--accent)}
.st-fail{background:var(--redBg);color:var(--red)}

.tb{display:flex;align-items:center;justify-content:space-between;padding:8px 14px;border-bottom:1px solid var(--bd);flex-wrap:wrap;gap:6px;background:var(--bg3)}
.tb-l{display:flex;align-items:center;gap:6px}
.tb-r{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.si{padding:5px 9px;background:var(--bg);border:1px solid var(--bd);border-radius:5px;color:var(--tx);font-size:11px;width:150px;outline:none}
.si:focus{border-color:var(--accent)}

.empty{padding:40px 16px;text-align:center;color:var(--tx4)}
.empty-i{font-size:36px;margin-bottom:8px}
.empty h4{font-size:14px;color:var(--tx3);margin-bottom:3px}

.ri{display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--bg3)}
.ri:last-child{border-bottom:none}
.ri-t{font-size:11px;color:var(--tx);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ri-m{font-size:9px;color:var(--tx4)}

@keyframes spin{to{transform:rotate(360deg)}}
.sp{width:14px;height:14px;border:2px solid var(--bd);border-top-color:var(--accent);border-radius:50%;animation:spin .6s linear infinite;display:inline-block}

.toast{position:fixed;bottom:16px;right:16px;background:var(--bg2);border:1px solid var(--bd);border-radius:8px;padding:10px 16px;font-size:11px;color:var(--tx);box-shadow:0 8px 30px rgba(0,0,0,.12);z-index:9999;display:none;align-items:center;gap:6px}
.toast.show{display:flex}

.ht{width:100%;border-collapse:collapse;font-size:11px}
.ht th{padding:8px 10px;text-align:left;font-size:9px;font-weight:600;color:var(--tx3);text-transform:uppercase;border-bottom:1px solid var(--bd);background:var(--bg3)}
.ht td{padding:8px 10px;border-bottom:1px solid var(--bg3);color:var(--tx2)}
.ht tr:hover td{background:var(--bg3)}
.ht-total{font-weight:700;background:var(--bg3)}

/* SEGMENTS TAB */
.seg-nav{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.seg-btn{padding:8px 12px;border:1.5px solid var(--bd);border-radius:8px;cursor:pointer;background:var(--bg2);transition:all .12s;text-align:left;min-width:130px}
.seg-btn:hover{border-color:var(--accent)}
.seg-btn.on{border-color:var(--accent);box-shadow:0 0 0 2px var(--greenBg)}
.seg-btn .sbt{font-size:11px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:5px}
.seg-btn .sbd{font-size:9px;color:var(--tx4);margin-top:2px}
.seg-btn .segc{margin-left:auto;font-size:12px;font-weight:700}
.seg-dot{width:9px;height:9px;border-radius:50%;display:inline-block}
.seg-ct{display:flex;gap:4px;flex-wrap:wrap;margin-bottom:12px}
.tier-hd{display:flex;align-items:center;justify-content:space-between;padding:9px 14px;background:var(--bg3);border-top:1px solid var(--bd);border-bottom:1px solid var(--bd)}
.tier-hd h3{font-size:12px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:6px}
.tier-c{font-size:10px;font-weight:600;color:var(--tx3);background:var(--bg2);border:1px solid var(--bd);padding:1px 7px;border-radius:10px}
.seg-note{font-size:10px;color:var(--tx4);margin-bottom:10px;line-height:1.5}
.seg-muted{opacity:.5}
.seg-muted:hover{opacity:1}
.seg-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:8px 4px 10px}
.seg-bar-l{font-size:11px;color:var(--tx3)}
.seg-bar-r{display:flex;gap:6px}

/* RECIPE */
.recipe{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;margin-bottom:14px;border:1.5px solid var(--greenBd);background:var(--greenBg);border-radius:9px;cursor:pointer;transition:all .12s}
.recipe:hover{border-color:var(--accent)}
.recipe b{font-size:12px;color:var(--accent)}
.recipe-h{font-size:9px;color:var(--tx3);margin-top:2px;max-width:270px;line-height:1.4}
.recipe-go{font-size:11px;font-weight:700;color:#fff;background:var(--accent);padding:5px 12px;border-radius:6px;white-space:nowrap}

/* CONTACT FILTER */
.seg-filters{margin-bottom:12px}
.seg-frow{display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap}
.seg-flbl{font-size:9px;font-weight:700;color:var(--tx4);text-transform:uppercase;letter-spacing:.5px;width:56px;flex-shrink:0}
.seg-cc{display:flex;gap:4px;flex-wrap:wrap}
.seg-cc .pill b{font-weight:700;margin-left:3px}
.seg-nudge{font-size:11px;color:var(--amber);background:var(--amberBg);border:1px solid var(--amber);border-radius:7px;padding:8px 12px;margin-bottom:10px;line-height:1.5}
</style>
</head>
<body>

<!-- HEADER -->
<div class="hdr">
    <div class="hdr-l">
        <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
        <span class="hdr-tag">TripAdvisor</span>
    </div>
    <div class="hdr-r">
        <span class="badge" id="planBadge">...</span>
        <span class="badge badge-g" id="usageBadge">--</span>
        <button class="thm" onclick="toggleTheme()" title="Toggle dark/light"></button>
    </div>
</div>

<div class="ctn">

<!-- STATS -->
<div class="stats">
    <div class="stat"><div class="stat-v" id="sRuns">-</div><div class="stat-l">Total Runs</div></div>
    <div class="stat"><div class="stat-v" id="sLeads">-</div><div class="stat-l">Total Leads (DB)</div></div>
    <div class="stat"><div class="stat-v" id="sEmails">-</div><div class="stat-l">Leads with Email</div><div style="font-size:9px;color:var(--tx4);margin-top:2px"><span id="sUniqEmails">-</span> unik</div></div>
    <div class="stat"><div class="stat-v" id="sCost">-</div><div class="stat-l">Total Cost (Apify)</div></div>
    <div class="stat">
        <div class="stat-v" id="sAvgCost">-</div><div class="stat-l">Avg Cost / Run</div>
        <div class="usage-bar"><div class="usage-fill" id="sBar" style="width:0%;background:var(--green)"></div></div>
        <div style="display:flex;justify-content:space-between;font-size:9px;color:var(--tx4);margin-top:3px"><span id="sUsed">$0</span><span id="sLimit">/ $199</span></div>
    </div>
</div>

<!-- TABS -->
<div class="tabs">
    <button class="tab on" onclick="showTab(0)">New Scrape</button>
    <button class="tab" onclick="showTab(1)">Leads Database</button>
    <button class="tab" onclick="showTab(2)">Blast Lists</button>
    <button class="tab" onclick="showTab(3)">Run History</button>
</div>

<!-- ═══ TAB 0: NEW SCRAPE ═══ -->
<div class="tab-panel on" id="tp0">
<div class="g2">
<div>
    <div class="cd" style="margin-bottom:16px">
        <div class="cd-h"><h3>New Scrape</h3></div>
        <div class="cd-b">
            <div class="recipe" onclick="applyRecipe()">
                <div><b>&#127919; Find small businesses</b><div class="recipe-h">Sets Things to Do + Restaurants, Max 1000 &mdash; the mix that surfaces low-review leads (S1/S3) that actually reply.</div></div>
                <span class="recipe-go">Apply</span>
            </div>
            <div class="fg"><label class="fl">Target Cities *</label>
                <input class="fi" id="targetCity" placeholder="Extra cities, comma-separated (or a Tripadvisor URL)">
                <div class="fh">Pilih beberapa kota sekaligus (pill bisa multi) &mdash; tiap kota jadi 1 run paralel, jadi cepet.</div>
                <div class="pw-lbl">Tourist Cities</div>
                <div class="pw" id="tourPills">
                    <span class="pill" onclick="pickCity(this,'New York')">New York</span>
                    <span class="pill" onclick="pickCity(this,'Las Vegas')">Las Vegas</span>
                    <span class="pill" onclick="pickCity(this,'Orlando')">Orlando</span>
                    <span class="pill" onclick="pickCity(this,'Miami')">Miami</span>
                    <span class="pill" onclick="pickCity(this,'San Francisco')">San Francisco</span>
                </div>
                <div class="pw-lbl">Non-Tourist Cities</div>
                <div class="pw" id="nonPills">
                    <span class="pill" onclick="pickCity(this,'Houston')">Houston</span>
                    <span class="pill" onclick="pickCity(this,'Phoenix')">Phoenix</span>
                    <span class="pill" onclick="pickCity(this,'Philadelphia')">Philadelphia</span>
                    <span class="pill" onclick="pickCity(this,'Dallas')">Dallas</span>
                    <span class="pill" onclick="pickCity(this,'San Antonio')">San Antonio</span>
                </div>
            </div>
            <div class="fg"><label class="fl">Content Types *</label>
                <div class="cks">
                    <label class="ck on" id="ckRestL"><input type="checkbox" id="ckRest" checked onchange="syncCk()"> Restaurants</label>
                    <label class="ck on" id="ckHotelL"><input type="checkbox" id="ckHotel" checked onchange="syncCk()"> Hotels</label>
                    <label class="ck on" id="ckAttrL"><input type="checkbox" id="ckAttr" checked onchange="syncCk()"> Things to Do</label>
                </div>
            </div>
            <div class="fr">
                <div class="fg"><label class="fl">Max Results</label>
                    <select class="fs" id="maxResults"><option value="20">20</option><option value="50" selected>50</option><option value="100">100</option><option value="200">200</option><option value="500">500</option><option value="1000">1000</option></select>
                </div>
                <div class="fg"><label class="fl">Currency</label>
                    <select class="fs" id="currency"><option value="USD" selected>USD</option><option value="CAD">CAD</option><option value="EUR">EUR</option><option value="AUD">AUD</option><option value="GBP">GBP</option></select>
                </div>
            </div>
            <div class="fg"><label class="fl">Language</label>
                <select class="fs" id="language"><option value="en" selected>English</option><option value="es">Spanish</option><option value="fr">French</option></select>
            </div>
            <div class="fg">
                <label class="enrich"><input type="checkbox" id="enrichEmails">
                    <span><span class="enrich-t">Find business emails (enrichment)</span><br><span class="enrich-h">Extra Apify cost per email found. Leave off for phone + website only.</span></span>
                </label>
            </div>
            <button class="btn btn-p" id="btnScrape" onclick="startScrape()">Start Scraping</button>
        </div>
    </div>
    <div class="cd">
        <div class="cd-h"><h3>Recent Runs</h3></div>
        <div class="cd-b" id="runsBox" style="max-height:220px;overflow-y:auto">
            <div class="empty"><h4>No runs yet</h4></div>
        </div>
    </div>
</div>
<div>
    <div class="cd">
        <div class="tb" id="resTb" style="display:none">
            <div class="tb-l">
                <input type="checkbox" id="selAll" onchange="togSelAll()">
                <span style="font-size:10px;color:var(--tx3)" id="selInfo">0 selected of 0</span>
            </div>
            <div class="tb-r">
                <input class="si" id="filterIn" placeholder="Filter..." oninput="filterRes()">
                <button class="btn btn-s btn-g" onclick="exportEmails()" id="btnExp" disabled>Export Emails</button>
                <button class="btn btn-s btn-o" onclick="exportCSV()">Export CSV</button>
            </div>
        </div>
        <div class="tw" id="resTable">
            <div class="empty" id="emptyRes"><div class="empty-i">&#127958;</div><h4>Ready to scrape</h4><p>Pick a target city + content types, then Start Scraping</p></div>
        </div>
    </div>
</div>
</div>
</div>

<!-- ═══ TAB 1: LEADS DATABASE ═══ -->
<div class="tab-panel" id="tp1">
<div class="cd">
    <div class="tb">
        <div class="tb-l">
            <input type="checkbox" id="dbSelAll" onchange="togDbSelAll()">
            <span style="font-size:10px;color:var(--tx3)" id="dbInfo">0 leads</span>
        </div>
        <div class="tb-r">
            <select class="si" id="dbFilterType" onchange="loadLeadsDb()" style="width:120px"><option value="">All Types</option><option>Restaurant</option><option>Hotel</option><option>Things to Do</option></select>
            <select class="si" id="dbFilterSeg" onchange="loadLeadsDb()" style="width:140px"><option value="">All Segments</option><option value="s1">Low / Low</option><option value="s2">Low / High</option><option value="s3">High / Low</option><option value="s4">High / High</option></select>
            <select class="si" id="dbFilterTier" onchange="loadLeadsDb()" style="width:120px"><option value="">All Cities</option><option value="tourist">Tourist</option><option value="nontourist">Non-Tourist</option><option value="other">Other</option></select>
            <select class="si" id="dbFilterContact" onchange="loadLeadsDb()" style="width:120px"><option value="">All Contacts</option><option value="wa">Has WA</option><option value="email">Has Email</option><option value="both">WA+Email</option></select>
            <input class="si" id="dbSearch" placeholder="Search..." oninput="loadLeadsDb()" style="width:130px">
            <button class="btn btn-s btn-g" onclick="exportDbEmails()" id="dbBtnExp">Export Emails</button>
            <button class="btn btn-s btn-o" onclick="exportDbCSV()">Export CSV</button>
        </div>
    </div>
    <div class="tw" id="dbTable" style="max-height:calc(100vh - 300px)">
        <div class="empty"><div class="empty-i">&#128203;</div><h4>Loading leads...</h4></div>
    </div>
</div>
</div>

<!-- ═══ TAB 2: SEGMENTS ═══ -->
<div class="tab-panel" id="tp2">
<div class="seg-note">Auto-bucketed jadi <b>4 kategori</b>: rating (cut <b>4.0</b>) &times; review (cut <b>50</b>). Yang review-nya sedikit (<b>Low/Low</b>, <b>High/Low</b>) paling gampang dibales. Klik <b>Download XLS (4 tab)</b> &mdash; 1 file, 4 sheet: Low/Low, Low/High, High/Low, High/High. Kerjain <b>Tourist</b> &amp; <b>Non-Tourist</b> terpisah.</div>
<div class="seg-nav" id="segNav">
    <div class="seg-btn on" data-seg="s1" onclick="pickSeg('s1')"><div class="sbt"><span class="seg-dot" style="background:var(--red)"></span> Low / Low<span class="segc" id="segc_s1" style="color:var(--red)">0</span></div><div class="sbd">Rating &lt;4.0 &middot; &lt;50 reviews</div></div>
    <div class="seg-btn" data-seg="s2" onclick="pickSeg('s2')"><div class="sbt"><span class="seg-dot" style="background:var(--purple)"></span> Low / High<span class="segc" id="segc_s2" style="color:var(--purple)">0</span></div><div class="sbd">Rating &lt;4.0 &middot; &ge;50 reviews</div></div>
    <div class="seg-btn" data-seg="s3" onclick="pickSeg('s3')"><div class="sbt"><span class="seg-dot" style="background:var(--green)"></span> High / Low<span class="segc" id="segc_s3" style="color:var(--green)">0</span></div><div class="sbd">Rating &ge;4.0 &middot; &lt;50 reviews</div></div>
    <div class="seg-btn seg-muted" data-seg="s4" onclick="pickSeg('s4')"><div class="sbt"><span class="seg-dot" style="background:var(--blue)"></span> High / High<span class="segc" id="segc_s4" style="color:var(--blue)">0</span></div><div class="sbd">Leaders &middot; won't reply</div></div>
</div>
<div class="seg-filters">
    <div class="seg-frow"><span class="seg-flbl">Type</span>
        <div class="seg-ct" id="segCt">
            <span class="pill on" data-ct="" onclick="pickSegCt(this,'')">All Types</span>
            <span class="pill" data-ct="Restaurant" onclick="pickSegCt(this,'Restaurant')">Restaurants</span>
            <span class="pill" data-ct="Hotel" onclick="pickSegCt(this,'Hotel')">Hotels</span>
            <span class="pill" data-ct="Things to Do" onclick="pickSegCt(this,'Things to Do')">Things to Do</span>
        </div>
    </div>
    <div class="seg-frow"><span class="seg-flbl">Contact</span>
        <div class="seg-cc" id="segCc">
            <span class="pill on" data-cf="" onclick="pickContact(this,'')">All</span>
            <span class="pill" data-cf="wa" onclick="pickContact(this,'wa')">Has WA<b id="ccWA">0</b></span>
            <span class="pill" data-cf="email" onclick="pickContact(this,'email')">Has Email<b id="ccEmail">0</b></span>
            <span class="pill" data-cf="both" onclick="pickContact(this,'both')">WA+Email<b id="ccBoth">0</b></span>
        </div>
    </div>
</div>
<div class="seg-bar" id="segBar"></div>
<div class="cd">
    <div id="segBody">
        <div class="empty"><div class="empty-i">&#128202;</div><h4>Loading segments...</h4></div>
    </div>
</div>
</div>

<!-- ═══ TAB 3: RUN HISTORY ═══ -->
<div class="tab-panel" id="tp3">
<div class="cd">
    <div class="cd-h"><h3>Run History</h3></div>
    <div class="tw" id="histTable" style="max-height:calc(100vh - 280px)">
        <div class="empty"><h4>Loading...</h4></div>
    </div>
</div>
</div>

</div><!-- /ctn -->
<div class="toast" id="toast"><span id="toastMsg"></span></div>

<script>
// ── STATE ──────────────────────────────────────────────
let leads=[], filtered=[], activeRunId=null, pollTimer=null, dbLeads=[], activeRuns=[];
let segAll=[], curSeg='s1', curSegCt='', contactFilter='', segGroups={tourist:[],nontourist:[],other:[]};

const SEG={
    s1:{short:'Low/Low',   full:'Low Rating / Low Review',  c:'var(--red)',    bg:'var(--redBg)'},
    s2:{short:'Low/High',  full:'Low Rating / High Review', c:'var(--purple)', bg:'var(--purpleBg)'},
    s3:{short:'High/Low',  full:'High Rating / Low Review', c:'var(--green)',  bg:'var(--greenBg)'},
    s4:{short:'High/High', full:'High Rating / High Review',c:'var(--blue)',   bg:'var(--blueBg)'},
    mid:{short:'Mid',      full:'Mid',                      c:'var(--tx3)',    bg:'var(--bg3)'}
};

document.addEventListener('DOMContentLoaded',()=>{
    const t=localStorage.getItem('sb_theme'); if(t) document.documentElement.setAttribute('data-theme',t);
    loadLimits(); loadRuns(); loadStats();
});

function toggleTheme(){
    const d=document.documentElement, c=d.getAttribute('data-theme');
    if(c==='dark'){d.removeAttribute('data-theme');localStorage.setItem('sb_theme','');}
    else{d.setAttribute('data-theme','dark');localStorage.setItem('sb_theme','dark');}
}

// ── TABS ───────────────────────────────────────────────
function showTab(n){
    document.querySelectorAll('.tab').forEach((t,i)=>t.classList.toggle('on',i===n));
    document.querySelectorAll('.tab-panel').forEach((p,i)=>p.classList.toggle('on',i===n));
    if(n===1) loadLeadsDb();
    if(n===2) loadSegments();
    if(n===3) loadHistory();
}

// ── CITY PILLS ─────────────────────────────────────────
function pickCity(el,city){ el.classList.toggle('on'); }
function getCities(){
    const out=[], seen={};
    const add=c=>{c=(c||'').trim(); if(!c)return; const k=c.toLowerCase(); if(!seen[k]){seen[k]=1;out.push(c);}};
    document.querySelectorAll('#tourPills .pill.on,#nonPills .pill.on').forEach(p=>add(p.textContent));
    (document.getElementById('targetCity').value||'').split(/[,\n]/).forEach(add);
    return out;
}

// ── CONTENT TYPE CHECKS ────────────────────────────────
function syncCk(){
    [['ckRest','ckRestL'],['ckHotel','ckHotelL'],['ckAttr','ckAttrL']].forEach(([c,l])=>{
        document.getElementById(l).classList.toggle('on',document.getElementById(c).checked);
    });
}

// ── STATS ──────────────────────────────────────────────
async function loadStats(){
    try{
        const r=await fetch('api.php?action=stats'), d=await r.json();
        document.getElementById('sRuns').textContent=d.totalRuns||0;
        document.getElementById('sLeads').textContent=(d.dbLeads||0).toLocaleString();
        document.getElementById('sEmails').textContent=(d.dbEmails||0).toLocaleString();
        document.getElementById('sUniqEmails').textContent=(d.dbUniqueEmails||0).toLocaleString();
        document.getElementById('sCost').textContent='$'+(d.totalCostUsd||0).toFixed(2);
        document.getElementById('sAvgCost').textContent='$'+(d.avgCostPerRun||0).toFixed(2);
    }catch(e){}
}

// ── LIMITS ─────────────────────────────────────────────
async function loadLimits(){
    try{
        const r=await fetch('api.php?action=limits'),d=await r.json();
        if(d.error)return;
        const plan=d.plan||{}, lim=d.monthlyLimitUsd||199, used=d.usageUsd||0;
        const pct=lim>0?Math.min(100,(used/lim)*100):0;
        document.getElementById('planBadge').textContent=plan.description||'Free';
        document.getElementById('usageBadge').textContent='$'+used.toFixed(2)+' / $'+lim.toFixed(0);
        document.getElementById('sUsed').textContent='$'+used.toFixed(2);
        document.getElementById('sLimit').textContent='/ $'+lim.toFixed(0);
        const bar=document.getElementById('sBar');
        bar.style.width=pct+'%';
        bar.style.background=pct>90?'var(--red)':pct>70?'var(--amber)':'var(--green)';
    }catch(e){}
}

// ── RUNS ───────────────────────────────────────────────
async function loadRuns(){
    try{
        const r=await fetch('api.php?action=runs'),d=await r.json(),runs=d.runs||[];
        if(!runs.length){document.getElementById('runsBox').innerHTML='<div class="empty"><h4>No runs</h4></div>';return;}
        let h='';
        runs.slice(0,10).forEach(run=>{
            const t=run.query||'';
            const ct=(run.contentTypes||[]).join(', ');
            const sc=run.status==='SUCCEEDED'?'st-ok':run.status==='FAILED'?'st-fail':'st-run';
            const cost=run.costUsd?(' | $'+parseFloat(run.costUsd).toFixed(2)):'';
            h+='<div class="ri"><div><div class="ri-t" title="'+esc(t)+'">'+esc(t)+'</div><div class="ri-m">'+esc(ct)+' | '+(run.startedAt||'')+cost+'</div></div><div style="display:flex;gap:5px;align-items:center"><span class="st '+sc+'">'+run.status+'</span><button class="btn btn-s btn-o" onclick="loadRes(\''+run.runId+'\')">View</button></div></div>';
        });
        document.getElementById('runsBox').innerHTML=h;
    }catch(e){}
}

// ── START SCRAPE ───────────────────────────────────────
async function startScrape(){
    const cities=getCities();
    if(!cities.length){toast('Pilih minimal 1 kota');return;}
    if(cities.length>12){toast('Max 12 kota per batch');return;}
    const rest=document.getElementById('ckRest').checked, hotel=document.getElementById('ckHotel').checked, attr=document.getElementById('ckAttr').checked;
    if(!rest&&!hotel&&!attr){toast('Pick at least one content type');return;}
    const base={
        restaurants:rest, hotels:hotel, attractions:attr,
        maxResults:parseInt(document.getElementById('maxResults').value),
        language:document.getElementById('language').value,
        currency:document.getElementById('currency').value,
        enrichEmails:document.getElementById('enrichEmails').checked
    };
    const btn=document.getElementById('btnScrape');
    btn.disabled=true;btn.innerHTML='<span class="sp"></span> Starting...';
    activeRuns=[]; leads=[]; filtered=[];
    for(const city of cities){
        const payload=Object.assign({},base);
        const isUrl=/^https?:\/\//i.test(city)&&city.toLowerCase().indexOf('tripadvisor.')!==-1;
        if(isUrl)payload.url=city; else payload.query=city;
        try{
            const r=await fetch('api.php?action=start',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
            const d=await r.json();
            if(d.success)activeRuns.push({runId:d.runId,city:city,status:'RUNNING',done:false});
        }catch(e){}
    }
    btn.disabled=false;btn.textContent='Start Scraping';
    if(!activeRuns.length){toast('Gagal start run');return;}
    activeRunId=activeRuns[0].runId;
    toast(activeRuns.length+' run started (paralel)');
    renderRunStatus(); loadRuns(); pollMulti();
}

function renderRunStatus(){
    document.getElementById('resTb').style.display='none';
    let h='<div style="padding:16px">';
    h+='<div style="font-size:13px;font-weight:600;margin-bottom:12px">Scraping '+activeRuns.length+' kota paralel'+(leads.length?(' &middot; '+leads.length+' leads'):'')+'</div>';
    activeRuns.forEach(run=>{
        let ic,col;
        if(!run.done){ic='<span class="sp"></span>';col='var(--tx3)';}
        else if(run.status==='SUCCEEDED'){ic='&#10003;';col='var(--green)';}
        else{ic='&#10007;';col='var(--red)';}
        h+='<div class="ri"><div class="ri-t">'+esc(run.city)+'</div><div style="color:'+col+';font-size:11px;display:flex;align-items:center;gap:6px">'+ic+' '+run.status+'</div></div>';
    });
    h+='<p style="font-size:10px;color:var(--tx4);margin-top:12px;line-height:1.5">Leads auto-save &amp; digabung. Pas semua selesai, buka tab <b>Blast Lists</b> &rarr; <b>&#127919; Targets</b>.</p></div>';
    document.getElementById('resTable').innerHTML=h;
}

function pollMulti(){
    if(pollTimer)clearInterval(pollTimer);
    pollTimer=setInterval(async()=>{
        for(const run of activeRuns){
            if(run.done)continue;
            try{
                const r=await fetch('api.php?action=status&runId='+run.runId),d=await r.json();
                run.status=d.status;
                if(d.status==='SUCCEEDED'){
                    run.done=true;
                    try{const rr=await fetch('api.php?action=results&runId='+run.runId),dd=await rr.json();if(dd.success){leads=leads.concat(dd.leads||[]);filtered=leads;}}catch(e){}
                }else if(d.status==='FAILED'||d.status==='ABORTED'){run.done=true;}
            }catch(e){}
        }
        renderRunStatus();
        if(activeRuns.every(x=>x.done)){
            clearInterval(pollTimer);pollTimer=null;
            toast('Selesai: '+leads.length+' leads');
            if(leads.length)renderRes();
            loadRuns();loadStats();loadLimits();
        }
    },5000);
}

async function loadRes(rid){
    document.getElementById('resTable').innerHTML='<div class="empty"><span class="sp" style="width:24px;height:24px;border-width:3px"></span><h4 style="margin-top:10px">Loading...</h4></div>';
    document.getElementById('resTb').style.display='none';
    try{
        const r=await fetch('api.php?action=results&runId='+rid),d=await r.json();
        if(d.success){leads=d.leads||[];filtered=leads;renderRes();toast(leads.length+' leads');loadStats();}
        else document.getElementById('resTable').innerHTML='<div class="empty"><h4>Failed</h4></div>';
    }catch(e){document.getElementById('resTable').innerHTML='<div class="empty"><h4>Error</h4></div>';}
}

// ── RENDER RESULTS ─────────────────────────────────────
function renderRes(){
    if(!filtered.length){document.getElementById('resTable').innerHTML='<div class="empty"><h4>No results</h4></div>';document.getElementById('resTb').style.display='none';return;}
    document.getElementById('resTb').style.display='flex';
    const we=filtered.filter(l=>l.email).length, wp=filtered.filter(l=>l.phone).length;
    document.getElementById('selInfo').textContent='0 sel | '+filtered.length+' total | '+we+' emails | '+wp+' phones';
    let h='<table><thead><tr><th class="cc"><input type="checkbox" id="selAllTh" onchange="togSelAll()"></th><th>#</th><th>Business</th><th>Type</th><th>Segment</th><th>Rating</th><th>Reviews</th><th>Phone</th><th>Email</th><th>Website</th><th>City/State</th><th>TripAdvisor</th></tr></thead><tbody>';
    filtered.forEach((l,i)=>{
        const es=l.email?'color:var(--green);font-weight:500':'color:var(--tx4)';
        const cs=[l.city,l.state].filter(Boolean).join(', ');
        h+='<tr><td class="cc"><input type="checkbox" class="lcb" data-i="'+i+'" onchange="updSel()"></td><td>'+(i+1)+'</td><td title="'+esc(l.title)+'">'+esc(l.title||'-')+'</td><td><span class="ct-b">'+esc(l.contentType||'-')+'</span></td><td>'+segBadge(segOf(l))+'</td><td><span class="stars">'+star(l.rating)+'</span><span class="rn">'+(l.rating||'-')+'</span></td><td>'+(l.reviewsCount||0)+'</td><td>'+esc(l.phone||'-')+'</td><td style="'+es+'">'+esc(l.email||'N/A')+'</td><td>'+(l.website?'<a href="'+esc(l.website)+'" target="_blank">Visit</a>':'-')+'</td><td>'+esc(cs||'-')+'</td><td>'+(l.url?'<a href="'+esc(l.url)+'" target="_blank">View</a>':'-')+'</td></tr>';
    });
    h+='</tbody></table>';
    document.getElementById('resTable').innerHTML=h;
}

function filterRes(){
    const q=document.getElementById('filterIn').value.toLowerCase();
    filtered=q?leads.filter(l=>(l.title||'').toLowerCase().includes(q)||(l.email||'').toLowerCase().includes(q)||(l.phone||'').toLowerCase().includes(q)||(l.contentType||'').toLowerCase().includes(q)||(l.city||'').toLowerCase().includes(q)):leads;
    renderRes();
}
function togSelAll(){const c=event.target.checked;document.querySelectorAll('.lcb').forEach(x=>x.checked=c);['selAll','selAllTh'].forEach(id=>{const e=document.getElementById(id);if(e)e.checked=c;});updSel();}
function updSel(){const n=document.querySelectorAll('.lcb:checked').length;document.getElementById('btnExp').disabled=n===0;const we=filtered.filter(l=>l.email).length;document.getElementById('selInfo').textContent=n+' sel | '+filtered.length+' total | '+we+' emails';}

// ── EXPORTS (results) ──────────────────────────────────
function exportEmails(){
    const sel=[];document.querySelectorAll('.lcb:checked').forEach(cb=>{const l=filtered[+cb.dataset.i];if(l&&l.email)sel.push(l);});
    if(!sel.length){toast('No emails selected');return;}
    dl(leadCsv(sel),'ta_emails_'+ds()+'.csv');toast(sel.length+' exported');
}
function exportCSV(){
    if(!filtered.length){toast('No results');return;}
    dl(leadCsv(filtered),'ta_leads_'+ds()+'.csv');toast(filtered.length+' exported');
}
function leadCsv(list){
    let c='Business,Type,Segment,Rating,Reviews,Phone,Email,Website,Address,City,State,Zip,CityTier,TripAdvisor,Survey_Link\n';
    list.forEach(l=>{c+=csvR([l.title,l.contentType,(SEG[segOf(l)]||{}).full,l.rating,l.reviewsCount,l.phone,l.email,l.website,l.address,l.city,l.state,l.zipCode,l.cityTier,l.url,surveyLink(l)]);});
    return c;
}

// ── LEADS DATABASE TAB ─────────────────────────────────
async function loadLeadsDb(){
    const type=document.getElementById('dbFilterType').value;
    const seg=document.getElementById('dbFilterSeg').value;
    const tier=document.getElementById('dbFilterTier').value;
    const contact=document.getElementById('dbFilterContact').value;
    const q=document.getElementById('dbSearch').value.trim();
    let url='api.php?action=leads';
    if(type)url+='&type='+encodeURIComponent(type);
    if(tier)url+='&tier='+encodeURIComponent(tier);
    if(q)url+='&search='+encodeURIComponent(q);
    try{
        const r=await fetch(url),d=await r.json();
        dbLeads=d.leads||[];
        if(seg)dbLeads=dbLeads.filter(l=>segOf(l)===seg);
        if(contact==='wa')dbLeads=dbLeads.filter(l=>normPhone(l.phone));
        else if(contact==='email')dbLeads=dbLeads.filter(l=>l.email);
        else if(contact==='both')dbLeads=dbLeads.filter(l=>normPhone(l.phone)&&l.email);
        const we=dbLeads.filter(l=>l.email).length;
        document.getElementById('dbInfo').textContent=dbLeads.length+' leads | '+we+' with email';
        if(!dbLeads.length){document.getElementById('dbTable').innerHTML='<div class="empty"><div class="empty-i">&#128203;</div><h4>No leads saved yet</h4><p>Leads are saved automatically after each scrape</p></div>';return;}
        let h='<table><thead><tr><th class="cc"><input type="checkbox" id="dbSelAllTh" onchange="togDbSelAll()"></th><th>#</th><th>Business</th><th>Type</th><th>Segment</th><th>Rating</th><th>Reviews</th><th>Phone</th><th>Email</th><th>City/State</th><th>Website</th><th>TripAdvisor</th></tr></thead><tbody>';
        dbLeads.forEach((l,i)=>{
            const es=l.email?'color:var(--green);font-weight:500':'color:var(--tx4)';
            const cs=[l.city,l.state].filter(Boolean).join(', ');
            h+='<tr><td class="cc"><input type="checkbox" class="dcb" data-i="'+i+'"></td><td>'+(i+1)+'</td><td title="'+esc(l.title)+'">'+esc(l.title||'-')+'</td><td><span class="ct-b">'+esc(l.contentType||'-')+'</span></td><td>'+segBadge(segOf(l))+'</td><td><span class="stars">'+star(l.rating)+'</span><span class="rn">'+(l.rating||'-')+'</span></td><td>'+(l.reviewsCount||0)+'</td><td>'+esc(l.phone||'-')+'</td><td style="'+es+'">'+esc(l.email||'N/A')+'</td><td>'+esc(cs||'-')+'</td><td>'+(l.website?'<a href="'+esc(l.website)+'" target="_blank">Visit</a>':'-')+'</td><td>'+(l.url?'<a href="'+esc(l.url)+'" target="_blank">View</a>':'-')+'</td></tr>';
        });
        h+='</tbody></table>';
        document.getElementById('dbTable').innerHTML=h;
    }catch(e){document.getElementById('dbTable').innerHTML='<div class="empty"><h4>Error loading</h4></div>';}
}
function togDbSelAll(){const c=event.target.checked;document.querySelectorAll('.dcb').forEach(x=>x.checked=c);['dbSelAll','dbSelAllTh'].forEach(id=>{const e=document.getElementById(id);if(e)e.checked=c;});}
function exportDbEmails(){
    const sel=[];document.querySelectorAll('.dcb:checked').forEach(cb=>{const l=dbLeads[+cb.dataset.i];if(l&&l.email)sel.push(l);});
    if(!sel.length){toast('No emails selected');return;}
    dl(leadCsv(sel),'ta_db_emails_'+ds()+'.csv');toast(sel.length+' exported');
}
function exportDbCSV(){
    if(!dbLeads.length){toast('No leads');return;}
    dl(leadCsv(dbLeads),'ta_db_all_'+ds()+'.csv');toast(dbLeads.length+' exported');
}

// ── SEGMENTS TAB ───────────────────────────────────────
async function loadSegments(){
    try{
        const r=await fetch('api.php?action=leads'),d=await r.json();
        segAll=d.leads||[];
    }catch(e){segAll=[];}
    const cnt={s1:0,s2:0,s3:0,s4:0};
    segAll.forEach(l=>{const s=segOf(l); if(cnt[s]!=null)cnt[s]++;});
    Object.keys(cnt).forEach(k=>{const e=document.getElementById('segc_'+k); if(e)e.textContent=cnt[k];});
    renderSegment();
}
function pickSeg(s){curSeg=s;document.querySelectorAll('#segNav .seg-btn').forEach(b=>b.classList.toggle('on',b.dataset.seg===s));renderSegment();}
function pickSegCt(el,ct){curSegCt=ct;document.querySelectorAll('#segCt .pill').forEach(p=>p.classList.remove('on'));el.classList.add('on');renderSegment();}
function renderSegment(){
    if(!segAll.length){
        document.getElementById('segBar').innerHTML='';
        document.getElementById('segBody').innerHTML='<div class="empty" style="padding:40px"><div class="empty-i">&#128202;</div><h4>No leads yet</h4><p>Run a scrape first &mdash; your target businesses show up here, split Tourist / Non-Tourist.</p></div>';
        return;
    }
    let base=segAll.filter(l=>segOf(l)===curSeg);
    if(curSegCt)base=base.filter(l=>(l.contentType||'')===curSegCt);
    // counts for the Contact filter buttons (before applying the filter)
    const nWA=base.filter(l=>normPhone(l.phone)).length;
    const nEmail=base.filter(l=>l.email).length;
    const nBoth=base.filter(l=>normPhone(l.phone)&&l.email).length;
    const setC=(id,v)=>{const e=document.getElementById(id); if(e)e.textContent=v;};
    setC('ccWA',nWA); setC('ccEmail',nEmail); setC('ccBoth',nBoth);
    // apply Contact filter
    let rows=base;
    if(contactFilter==='wa')rows=base.filter(l=>normPhone(l.phone));
    else if(contactFilter==='email')rows=base.filter(l=>l.email);
    else if(contactFilter==='both')rows=base.filter(l=>normPhone(l.phone)&&l.email);
    segGroups.tourist=rows.filter(l=>l.cityTier==='tourist');
    segGroups.nontourist=rows.filter(l=>l.cityTier==='nontourist');
    segGroups.other=rows.filter(l=>l.cityTier==='other');
    const blastable=rows.filter(l=>normPhone(l.phone)).length;
    // supply-side nudge: filtering by email but yield is low -> tell them to enrich
    let nudge='';
    if(contactFilter==='email'&&base.length&&nEmail/base.length<0.4)
        nudge='<div class="seg-nudge">Cuma <b>'+nEmail+'</b> dari '+base.length+' ada email. Nyalain <b>&#9993; Find business emails (enrichment)</b> di New Scrape buat nambah email (ada biaya Apify).</div>';
    document.getElementById('segBar').innerHTML=
        '<div class="seg-bar-l">'+rows.length+' leads &middot; <b style="color:var(--green)">'+blastable+' ada WA</b>'+(contactFilter?' &middot; filter: '+cfLabel():'')+'</div>'
        +'<div class="seg-bar-r"><button class="btn btn-s btn-g" onclick="exportXls()">&#11015; XLS (4 tab'+(contactFilter?' &middot; '+cfLabel():'')+')</button>'+(rows.length?'<button class="btn btn-s btn-o" onclick="exportView(\'all\')">&#11015; Export shown</button>':'')+'</div>';
    let h=nudge+segSection('&#127958; Tourist Cities',segGroups.tourist,'tourist')
        +segSection('&#127961; Non-Tourist Cities',segGroups.nontourist,'nontourist');
    if(segGroups.other.length)h+=segSection('Other Cities',segGroups.other,'other');
    document.getElementById('segBody').innerHTML=h;
}
function cfLabel(){return ({wa:'Has WA',email:'Has Email',both:'WA+Email'})[contactFilter]||'All';}
function pickContact(el,f){contactFilter=f;document.querySelectorAll('#segCc .pill').forEach(p=>p.classList.remove('on'));el.classList.add('on');renderSegment();}
function segSection(title,list,tier){
    const blast=list.filter(l=>normPhone(l.phone)).length;
    let head='<div class="tier-hd"><h3>'+title+' <span class="tier-c">'+list.length+'</span>'
        +(blast?' <span class="tier-c" style="color:var(--green)">'+blast+' blastable</span>':'')+'</h3>'
        +(list.length?'<button class="btn btn-s btn-o" onclick="exportView(\''+tier+'\')">&#11015; Export</button>':'')+'</div>';
    if(!list.length)return head+segEmpty();
    let h=head+'<div class="tw" style="max-height:none"><table><thead><tr><th>#</th><th>Business</th><th>Seg</th><th>Type</th><th>City/State</th><th>Rating</th><th>Reviews</th><th>WA Phone</th><th>Email</th><th>Website</th><th>TA</th></tr></thead><tbody>';
    list.forEach((l,i)=>{
        const es=l.email?'color:var(--green);font-weight:500':'color:var(--tx4)';
        const cs=[l.city,l.state].filter(Boolean).join(', ');
        const np=normPhone(l.phone);
        const ph=np?'<span style="color:var(--tx)">'+esc(np)+'</span>':'<span style="color:var(--tx4)">no phone</span>';
        h+='<tr><td>'+(i+1)+'</td><td title="'+esc(l.title)+'">'+esc(l.title||'-')+'</td><td>'+segBadge(segOf(l))+'</td><td><span class="ct-b">'+esc(l.contentType||'-')+'</span></td><td>'+esc(cs||'-')+'</td><td><span class="stars">'+star(l.rating)+'</span><span class="rn">'+(l.rating||'-')+'</span></td><td>'+(l.reviewsCount||0)+'</td><td>'+ph+'</td><td style="'+es+'">'+esc(l.email||'N/A')+'</td><td>'+(l.website?'<a href="'+esc(l.website)+'" target="_blank">Visit</a>':'-')+'</td><td>'+(l.url?'<a href="'+esc(l.url)+'" target="_blank">View</a>':'-')+'</td></tr>';
    });
    h+='</tbody></table></div>';
    return h;
}
function segEmpty(){
    const lowRev=(curSeg==='s1'||curSeg==='s3');
    if(lowRev)return '<div class="empty" style="padding:24px"><div class="empty-i">&#128269;</div><h4>Belum ada bisnis kecil di sini</h4><p style="max-width:440px;margin:5px auto 0;line-height:1.5">Usaha kecil (review &lt;50) ada di <b>Things to Do</b> &amp; <b>Restaurants</b>, dan di ekor daftar. Buka <b>New Scrape</b> &rarr; klik <b>&#127919; Find small businesses</b> (Max 1000), scrape ulang.</p></div>';
    return '<div class="empty" style="padding:24px"><h4>No leads in this group</h4></div>';
}
function segScope(scope){
    if(scope==='all')return [].concat(segGroups.tourist,segGroups.nontourist,segGroups.other);
    return segGroups[scope]||[];
}
function viewCsv(list){
    let c='Business,WA_Phone_E164,Email,Segment,CityTier,Type,City,State,Rating,Reviews,Website,TripAdvisor,Survey_Link\n';
    list.forEach(l=>{c+=csvR([l.title,normPhone(l.phone),l.email,(SEG[segOf(l)]||{}).full,l.cityTier,l.contentType,l.city,l.state,l.rating,l.reviewsCount,l.website,l.url,surveyLink(l)]);});
    return c;
}
// Export exactly what's on screen (respects segment + type + contact filter)
function exportView(scope){
    const list=segScope(scope);
    if(!list.length){toast('Gak ada lead di view ini');return;}
    dl(viewCsv(list),'ta_'+curSeg+'_'+scope+'_'+(contactFilter||'all')+'_'+ds()+'.csv');
    toast(list.length+' exported ('+cfLabel()+')');
}

// ── RUN HISTORY TAB ────────────────────────────────────
async function loadHistory(){
    try{
        const r=await fetch('api.php?action=runs'),d=await r.json(),runs=d.runs||[];
        if(!runs.length){document.getElementById('histTable').innerHTML='<div class="empty"><h4>No runs</h4></div>';return;}
        let totL=0,totE=0,totC=0;
        let h='<table class="ht"><thead><tr><th>#</th><th>Target City</th><th>Content</th><th>Results</th><th>Emails</th><th>Cost</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>';
        runs.forEach((run,i)=>{
            const t=run.query||'';
            const ct=(run.contentTypes||[]).join(', ');
            const sc=run.status==='SUCCEEDED'?'st-ok':run.status==='FAILED'?'st-fail':'st-run';
            const rc=run.resultsCount||0, ec=run.emailsCount||0, cc=parseFloat(run.costUsd||0);
            totL+=rc;totE+=ec;totC+=cc;
            h+='<tr><td>'+(i+1)+'</td><td style="max-width:160px;overflow:hidden;text-overflow:ellipsis" title="'+esc(t)+'">'+esc(t)+'</td><td style="font-size:10px">'+esc(ct)+'</td><td>'+rc+'</td><td>'+ec+'</td><td>$'+cc.toFixed(2)+'</td><td><span class="st '+sc+'">'+run.status+'</span></td><td style="font-size:10px">'+(run.startedAt||'').slice(0,16)+'</td><td><button class="btn btn-s btn-o" onclick="showTab(0);setTimeout(()=>loadRes(\''+run.runId+'\'),100)">View</button></td></tr>';
        });
        h+='<tr class="ht-total"><td colspan="3" style="text-align:right">TOTALS:</td><td>'+totL+'</td><td>'+totE+'</td><td>$'+totC.toFixed(2)+'</td><td colspan="3"></td></tr>';
        h+='</tbody></table>';
        document.getElementById('histTable').innerHTML=h;
    }catch(e){document.getElementById('histTable').innerHTML='<div class="empty"><h4>Error</h4></div>';}
}

// ── HELPERS ────────────────────────────────────────────
function segBadge(s){const m=SEG[s]||SEG.mid;return '<span class="sb" style="color:'+m.c+';background:'+m.bg+'" title="'+m.full+'">'+m.short+'</span>';}
// Normalize a messy phone to US E.164 (+1XXXXXXXXXX). Returns '' if unusable for a WA blast.
function normPhone(raw){
    if(!raw)return '';
    let d=(''+raw).replace(/[^\d]/g,'');
    if(d.slice(0,2)==='00')d=d.slice(2);      // strip intl 00 prefix
    if(d.length===11&&d[0]==='1')return '+'+d; // US w/ country code
    if(d.length===10)return '+1'+d;            // US local -> add +1
    if(d.length>11)return '+'+d;               // already has some country code
    return '';                                 // <10 digits = unusable
}
function applyRecipe(){
    document.getElementById('ckRest').checked=true;
    document.getElementById('ckHotel').checked=false;
    document.getElementById('ckAttr').checked=true;
    syncCk();
    document.getElementById('maxResults').value='1000';
    toast('Recipe: Things to Do + Restaurants, Max 1000');
}
// Recompute segment from rating+reviews (single source of truth, 4-way, no Mid).
// rating >=4.0 = high, reviews >=50 = high. Handles old 'mid'-tagged data too.
function segOf(l){
    const r=parseFloat(l.rating)||0, n=parseInt(l.reviewsCount)||0;
    const hiR=r>=4.0, hiN=n>=50;
    if(!hiR&&!hiN)return 's1';
    if(!hiR&&hiN)return 's2';
    if(hiR&&!hiN)return 's3';
    return 's4';
}
// Build the tp-survey CTA link per lead (descriptive params so PMM reads the criteria).
function surveyLink(l){
    const segMap={s1:'low_low',s2:'low_high',s3:'high_low',s4:'high_high'};
    const typeMap={'Restaurant':'restaurant','Hotel':'hotel','Things to Do':'things-to-do'};
    const tier=(l.cityTier==='tourist')?'tourist':(l.cityTier==='nontourist')?'nontourist':'other';
    return 'https://smart-buzzer.com/tp-survey/?seg='+(segMap[segOf(l)]||'')+'&tier='+tier+'&type='+(typeMap[l.contentType]||'')+'&channel=email';
}
// Download an .xls (SpreadsheetML) with 4 sheets = one per rating x review segment.
function exportXls(){
    if(!segAll.length){toast('No leads');return;}
    let src=segAll;
    if(curSegCt)src=src.filter(l=>(l.contentType||'')===curSegCt);
    // XLS ikut filter Contact yang lagi aktif: Has Email -> cuma email, WA+Email -> dua-duanya
    if(contactFilter==='wa')src=src.filter(l=>normPhone(l.phone));
    else if(contactFilter==='email')src=src.filter(l=>l.email);
    else if(contactFilter==='both')src=src.filter(l=>normPhone(l.phone)&&l.email);
    const defs=[['s1','Low-Low'],['s2','Low-High'],['s3','High-Low'],['s4','High-High']];
    const sheets=defs.map(function(d){
        const rows=src.filter(l=>segOf(l)===d[0]).slice();
        rows.sort((a,b)=>(normPhone(b.phone)?1:0)-(normPhone(a.phone)?1:0)); // blastable first
        return {name:d[1],rows:rows};
    });
    const tot=sheets.reduce((s,x)=>s+x.rows.length,0);
    if(!tot){toast('Belum ada lead'+(contactFilter?' ('+cfLabel()+')':''));return;}
    dlXls(buildXls(sheets),'ta_leads_4tab_'+(contactFilter||'all')+'_'+ds()+'.xls');
    toast('XLS: '+tot+' leads, 4 tab'+(contactFilter?' ('+cfLabel()+')':''));
}
function buildXls(sheets){
    const cols=['Business','WA_Phone_E164','Email','Segment','CityTier','Type','City','State','Rating','Reviews','Website','TripAdvisor','Survey_Link'];
    // Split '<?' so PHP (with short_open_tag On) never mistakes these XML PIs for open tags.
    let x='<'+'?xml version="1.0"?'+'>\n'+'<'+'?mso-application progid="Excel.Sheet"?'+'>\n<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n';
    sheets.forEach(function(sh){
        x+='<Worksheet ss:Name="'+xesc(sh.name)+'"><Table>';
        x+='<Row>'+cols.map(c=>'<Cell><Data ss:Type="String">'+xesc(c)+'</Data></Cell>').join('')+'</Row>';
        sh.rows.forEach(function(l){
            x+='<Row>'+xc(l.title,'s')+xc(normPhone(l.phone),'s')+xc(l.email,'s')+xc((SEG[segOf(l)]||{}).full,'s')+xc(l.cityTier,'s')+xc(l.contentType,'s')+xc(l.city,'s')+xc(l.state,'s')+xc(l.rating,'n')+xc(l.reviewsCount,'n')+xc(l.website,'s')+xc(l.url,'s')+xc(surveyLink(l),'s')+'</Row>';
        });
        x+='</Table></Worksheet>';
    });
    return x+'</Workbook>';
}
function xc(v,t){
    if(t==='n'){const n=parseFloat(v);if(!isNaN(n)&&v!==''&&v!=null)return '<Cell><Data ss:Type="Number">'+n+'</Data></Cell>';}
    return '<Cell><Data ss:Type="String">'+xesc(v==null?'':String(v))+'</Data></Cell>';
}
function xesc(s){return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function dlXls(x,f){const b=new Blob([x],{type:'application/vnd.ms-excel'});const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=f;a.click();}
function esc(s){if(s===0)return '0';if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
function star(s){s=parseFloat(s);if(!s)return '';let r='';for(let i=0;i<Math.round(s);i++)r+='&#9733;';return r;}
function csvR(a){return a.map(v=>'"'+String(v==null?'':v).replace(/"/g,'""')+'"').join(',')+'\n';}
function dl(c,f){const b=new Blob([c],{type:'text/csv'});const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=f;a.click();}
function ds(){return new Date().toISOString().slice(0,10).replace(/-/g,'');}
function toast(m){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=m;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
</script>
</body>
</html>
