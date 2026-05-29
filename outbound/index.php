<?php
session_start();
$PASS = 'smartbuzzer2025';
if (!isset($_SESSION['ob_auth'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['pass'] ?? '') === $PASS) {
        $_SESSION['ob_auth'] = true;
    } else {
        ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Smart Buzzer Outbound</title>
        <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',sans-serif;background:#F8FAFC;display:flex;align-items:center;justify-content:center;min-height:100vh;color:#1E293B}
        .login{background:#fff;border-radius:16px;padding:40px;max-width:380px;width:90%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #E2E8F0}
        .login img{height:40px;margin-bottom:24px}.login h2{font-size:20px;margin-bottom:8px}.login p{color:#64748B;font-size:14px;margin-bottom:24px}
        .login input{width:100%;padding:14px 16px;background:#F8FAFC;border:1.5px solid #D1D5DB;border-radius:10px;color:#1E293B;font-size:15px;outline:none;margin-bottom:16px}
        .login input:focus{border-color:#3B82F6}.login button{width:100%;padding:14px;background:#3B82F6;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer}
        .login button:hover{background:#2563EB}</style></head><body>
        <form class="login" method="POST"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
        <h2>Outbound Scraper</h2><p>Enter password to access</p>
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
<title>Smart Buzzer — Outbound Scraper</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#F8FAFC;--bg2:#fff;--bg3:#F1F5F9;--bg4:#E2E8F0;
    --tx:#1E293B;--tx2:#334155;--tx3:#64748B;--tx4:#94A3B8;
    --bd:#E2E8F0;--bd2:#D1D5DB;
    --accent:#3B82F6;--accent2:#2563EB;
    --green:#16A34A;--greenBg:#F0FDF4;--greenBd:#BBF7D0;
    --amber:#D97706;--amberBg:#FFFBEB;
    --red:#DC2626;--redBg:#FEF2F2;
    --shadow:0 1px 3px rgba(0,0,0,.05);
}
[data-theme="dark"]{
    --bg:#0F172A;--bg2:#1E293B;--bg3:#1E293B;--bg4:#334155;
    --tx:#E2E8F0;--tx2:#CBD5E1;--tx3:#94A3B8;--tx4:#64748B;
    --bd:#334155;--bd2:#475569;
    --green:#4ADE80;--greenBg:#052e16;--greenBd:#14532D;
    --amber:#FBBF24;--amberBg:#451A03;
    --red:#F87171;--redBg:#450a0a;
    --shadow:0 1px 3px rgba(0,0,0,.3);
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--tx);font-size:13px;min-height:100vh;transition:background .2s,color .2s}
a{color:var(--accent);text-decoration:none}a:hover{text-decoration:underline}

/* HEADER */
.hdr{background:var(--bg2);border-bottom:1px solid var(--bd);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow)}
.hdr img{height:28px}
.hdr-r{display:flex;align-items:center;gap:8px}
.badge{padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:var(--bg3);color:var(--tx3)}
.badge-g{background:var(--greenBg);color:var(--green)}

/* THEME TOGGLE */
.thm{width:34px;height:18px;border-radius:9px;background:var(--bd2);border:none;cursor:pointer;position:relative;transition:background .2s}
.thm::after{content:'';position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;background:#fff;transition:transform .2s;box-shadow:0 1px 2px rgba(0,0,0,.2)}
[data-theme="dark"] .thm{background:var(--accent)}
[data-theme="dark"] .thm::after{transform:translateX(16px)}

.ctn{max-width:1440px;margin:0 auto;padding:20px}

/* STAT CARDS */
.stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:16px}
.stat{background:var(--bg2);border:1px solid var(--bd);border-radius:10px;padding:14px 16px;box-shadow:var(--shadow);transition:background .2s}
.stat-v{font-size:22px;font-weight:700;color:var(--tx)}
.stat-l{font-size:10px;color:var(--tx4);margin-top:2px;text-transform:uppercase;letter-spacing:.3px}
.usage-bar{background:var(--bd);border-radius:3px;height:6px;overflow:hidden;margin-top:8px}
.usage-fill{height:100%;border-radius:3px;transition:width .3s}

/* TABS */
.tabs{display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid var(--bd)}
.tab{padding:10px 20px;font-size:13px;font-weight:600;color:var(--tx3);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;background:none;border-top:none;border-left:none;border-right:none;font-family:inherit}
.tab:hover{color:var(--tx)}
.tab.on{color:var(--accent);border-bottom-color:var(--accent)}
.tab-panel{display:none}.tab-panel.on{display:block}

/* GRID */
.g2{display:grid;grid-template-columns:380px 1fr;gap:20px}
@media(max-width:1024px){.g2{grid-template-columns:1fr}.stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.stats{grid-template-columns:1fr 1fr}}

/* CARD */
.cd{background:var(--bg2);border:1px solid var(--bd);border-radius:10px;overflow:hidden;box-shadow:var(--shadow);transition:background .2s}
.cd-h{padding:12px 16px;border-bottom:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between;background:var(--bg3)}
.cd-h h3{font-size:13px;font-weight:600;color:var(--tx)}
.cd-b{padding:16px}

/* FORM */
.fg{margin-bottom:12px}
.fl{display:block;font-size:10px;font-weight:600;color:var(--tx3);margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px}
.fi,.fs,.ft{width:100%;padding:8px 11px;background:var(--bg);border:1.5px solid var(--bd);border-radius:7px;color:var(--tx);font-size:12px;font-family:inherit;outline:none;transition:border .15s}
.fi:focus,.fs:focus,.ft:focus{border-color:var(--accent)}
.ft{min-height:60px;resize:vertical}
.fs{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;background-size:13px;padding-right:28px;background-color:var(--bg)}
.fh{font-size:9px;color:var(--tx4);margin-top:2px}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:8px}

/* BUTTONS */
.btn{padding:7px 14px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;transition:all .15s;font-family:inherit}
.btn-p{background:var(--accent);color:#fff;width:100%;padding:9px 16px}
.btn-p:hover{background:var(--accent2)}
.btn-p:disabled{background:var(--bd);color:var(--tx4);cursor:not-allowed}
.btn-s{padding:4px 9px;font-size:10px;border-radius:5px}
.btn-g{background:#059669;color:#fff}.btn-g:hover{background:#047857}
.btn-o{background:var(--bg);border:1.5px solid var(--bd);color:var(--tx3)}
.btn-o:hover{border-color:var(--accent);color:var(--accent)}
.btn-r{background:var(--redBg);color:var(--red);border:1px solid var(--red)}

/* PILLS */
.pw{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:4px}
.pill{padding:3px 8px;border:1.5px solid var(--bd);border-radius:5px;font-size:10px;font-weight:500;cursor:pointer;background:var(--bg);color:var(--tx3);transition:all .12s;display:inline-flex;align-items:center;gap:3px}
.pill:hover{border-color:var(--accent);color:var(--accent)}
.pill.used{border-color:var(--green);color:var(--green);background:var(--greenBg)}
.pill.used::after{content:'\\2713';font-size:9px;font-weight:700}
.pill.on{background:var(--accent);color:#fff;border-color:var(--accent)}

/* TABLE */
.tw{overflow-x:auto;max-height:calc(100vh - 220px);overflow-y:auto}
table{width:100%;border-collapse:collapse;font-size:11px;min-width:860px}
thead{background:var(--bg3);position:sticky;top:0;z-index:2}
th{padding:7px 8px;text-align:left;font-size:9px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;border-bottom:1px solid var(--bd);white-space:nowrap}
td{padding:7px 8px;border-bottom:1px solid var(--bg3);white-space:nowrap;max-width:160px;overflow:hidden;text-overflow:ellipsis;color:var(--tx2)}
tr:hover td{background:var(--bg3)}
.cc{width:28px;text-align:center}
input[type="checkbox"]{width:14px;height:14px;accent-color:var(--accent);cursor:pointer}
.stars{color:#F59E0B;font-size:10px}
.rn{color:var(--tx4);margin-left:2px;font-size:10px}

/* STATUS */
.st{padding:2px 6px;border-radius:4px;font-size:9px;font-weight:600;display:inline-block}
.st-ok{background:var(--greenBg);color:var(--green)}
.st-run{background:var(--bg4);color:var(--accent)}
.st-fail{background:var(--redBg);color:var(--red)}

/* TOOLBAR */
.tb{display:flex;align-items:center;justify-content:space-between;padding:8px 14px;border-bottom:1px solid var(--bd);flex-wrap:wrap;gap:6px;background:var(--bg3)}
.tb-l{display:flex;align-items:center;gap:6px}
.tb-r{display:flex;align-items:center;gap:5px}
.si{padding:5px 9px;background:var(--bg);border:1px solid var(--bd);border-radius:5px;color:var(--tx);font-size:11px;width:160px;outline:none}
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

/* HISTORY TABLE */
.ht{width:100%;border-collapse:collapse;font-size:11px}
.ht th{padding:8px 10px;text-align:left;font-size:9px;font-weight:600;color:var(--tx3);text-transform:uppercase;border-bottom:1px solid var(--bd);background:var(--bg3)}
.ht td{padding:8px 10px;border-bottom:1px solid var(--bg3);color:var(--tx2)}
.ht tr:hover td{background:var(--bg3)}
.ht-total{font-weight:700;background:var(--bg3)}
</style>
</head>
<body>

<!-- HEADER -->
<div class="hdr">
    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
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
    <div class="stat"><div class="stat-v" id="sEmails">-</div><div class="stat-l">Leads with Email</div></div>
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
    <button class="tab" onclick="showTab(2)">Run History</button>
</div>

<!-- ═══ TAB 0: NEW SCRAPE ═══ -->
<div class="tab-panel on" id="tp0">
<div class="g2">
<div>
    <div class="cd" style="margin-bottom:16px">
        <div class="cd-h"><h3>New Scrape</h3></div>
        <div class="cd-b">
            <div class="fg"><label class="fl">Industry Templates</label>
                <div class="pw" id="tplWrap">
                    <span class="pill" onclick="useTpl('hvac')">HVAC</span>
                    <span class="pill" onclick="useTpl('construction')">Construction</span>
                    <span class="pill" onclick="useTpl('restaurant')">Restaurant</span>
                    <span class="pill" onclick="useTpl('auto_repair')">Auto Repair</span>
                    <span class="pill" onclick="useTpl('roofing')">Roofing</span>
                    <span class="pill" onclick="useTpl('auto_dealer')">Auto Dealer</span>
                    <span class="pill" onclick="useTpl('accounting')">Accounting</span>
                    <span class="pill" onclick="useTpl('healthcare')">Healthcare</span>
                    <span class="pill" onclick="useTpl('dental')">Dental</span>
                    <span class="pill" onclick="useTpl('fnb')">F&amp;B</span>
                    <span class="pill" onclick="useTpl('locksmith')">Locksmith</span>
                    <span class="pill" onclick="useTpl('salon')">Salon/Spa</span>
                    <span class="pill" onclick="useTpl('landscaping')">Landscaping</span>
                    <span class="pill" onclick="useTpl('plumber')">Plumber</span>
                    <span class="pill" onclick="useTpl('lawyer')">Lawyer</span>
                    <span class="pill" onclick="useTpl('realtor')">Realtor</span>
                </div>
            </div>
            <div class="fg"><label class="fl">Search Terms *</label>
                <textarea class="ft" id="searchTerms" placeholder="dentist&#10;HVAC contractor" rows="3"></textarea>
                <div class="fh">One per line. Use distinct terms.</div>
            </div>
            <div class="fg"><label class="fl">Top States</label>
                <div class="pw" id="stPills">
                    <span class="pill" onclick="pickSt(this,'California')">CA</span>
                    <span class="pill" onclick="pickSt(this,'Texas')">TX</span>
                    <span class="pill" onclick="pickSt(this,'Florida')">FL</span>
                    <span class="pill" onclick="pickSt(this,'New York')">NY</span>
                    <span class="pill" onclick="pickSt(this,'Virginia')">VA</span>
                    <span class="pill" onclick="pickSt(this,'New Jersey')">NJ</span>
                    <span class="pill" onclick="pickSt(this,'Pennsylvania')">PA</span>
                    <span class="pill" onclick="pickSt(this,'Minnesota')">MN</span>
                    <span class="pill" onclick="pickSt(this,'Utah')">UT</span>
                    <span class="pill" onclick="pickSt(this,'Tennessee')">TN</span>
                    <span class="pill" onclick="pickSt(this,'North Carolina')">NC</span>
                    <span class="pill" onclick="pickSt(this,'Illinois')">IL</span>
                </div>
            </div>
            <div class="fg"><label class="fl">US State</label>
                <select class="fs" id="usState" onchange="syncStPill()">
                    <option value="">All US</option>
                    <option>Alabama</option><option>Alaska</option><option>Arizona</option><option>Arkansas</option><option>California</option><option>Colorado</option><option>Connecticut</option><option>Delaware</option><option>Florida</option><option>Georgia</option><option>Hawaii</option><option>Idaho</option><option>Illinois</option><option>Indiana</option><option>Iowa</option><option>Kansas</option><option>Kentucky</option><option>Louisiana</option><option>Maine</option><option>Maryland</option><option>Massachusetts</option><option>Michigan</option><option>Minnesota</option><option>Mississippi</option><option>Missouri</option><option>Montana</option><option>Nebraska</option><option>Nevada</option><option>New Hampshire</option><option>New Jersey</option><option>New Mexico</option><option>New York</option><option>North Carolina</option><option>North Dakota</option><option>Ohio</option><option>Oklahoma</option><option>Oregon</option><option>Pennsylvania</option><option>Rhode Island</option><option>South Carolina</option><option>South Dakota</option><option>Tennessee</option><option>Texas</option><option>Utah</option><option>Vermont</option><option>Virginia</option><option>Washington</option><option>West Virginia</option><option>Wisconsin</option><option>Wyoming</option>
                </select>
            </div>
            <div class="fg"><label class="fl">City (optional)</label><input class="fi" id="city" placeholder="e.g. Houston, Miami"></div>
            <div class="fr">
                <div class="fg"><label class="fl">Max Results</label>
                    <select class="fs" id="maxResults"><option value="20">20</option><option value="50">50</option><option value="100" selected>100</option><option value="200">200</option><option value="500">500</option><option value="1000">1000</option></select>
                </div>
                <div class="fg"><label class="fl">Language</label>
                    <select class="fs" id="language"><option value="en" selected>English</option><option value="es">Spanish</option></select>
                </div>
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
            <div class="empty" id="emptyRes"><div class="empty-i">&#128640;</div><h4>Ready to scrape</h4><p>Pick industry + state, then Start Scraping</p></div>
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
            <select class="si" id="dbFilterCat" onchange="loadLeadsDb()" style="width:130px"><option value="">All Industries</option>
                <option>HVAC contractor</option><option>Construction</option><option>Restaurant</option><option>Auto repair</option><option>Roofing</option><option>Dentist</option><option>Plumber</option><option>Salon</option><option>Landscaping</option><option>Locksmith</option><option>Accounting</option><option>Healthcare</option><option>Real estate</option><option>Lawyer</option>
            </select>
            <select class="si" id="dbFilterSt" onchange="loadLeadsDb()" style="width:110px"><option value="">All States</option>
                <option>California</option><option>Texas</option><option>Florida</option><option>New York</option><option>Virginia</option><option>New Jersey</option><option>Pennsylvania</option><option>Illinois</option>
            </select>
            <input class="si" id="dbSearch" placeholder="Search..." oninput="loadLeadsDb()" style="width:140px">
            <button class="btn btn-s btn-g" onclick="exportDbEmails()" id="dbBtnExp">Export Emails</button>
            <button class="btn btn-s btn-o" onclick="exportDbCSV()">Export CSV</button>
        </div>
    </div>
    <div class="tw" id="dbTable" style="max-height:calc(100vh - 300px)">
        <div class="empty"><div class="empty-i">&#128203;</div><h4>Loading leads...</h4></div>
    </div>
</div>
</div>

<!-- ═══ TAB 2: RUN HISTORY ═══ -->
<div class="tab-panel" id="tp2">
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
let leads=[], filtered=[], activeRunId=null, pollTimer=null, dbLeads=[];
const usedTpl = JSON.parse(localStorage.getItem('sb_tpl')||'[]');

document.addEventListener('DOMContentLoaded',()=>{
    const t=localStorage.getItem('sb_theme'); if(t) document.documentElement.setAttribute('data-theme',t);
    loadLimits(); loadRuns(); loadStats(); markTpl();
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
    if(n===2) loadHistory();
}

// ── TEMPLATES ──────────────────────────────────────────
const TPL={
    hvac:'HVAC contractor\nheating and cooling\nair conditioning repair\nfurnace repair',
    construction:'general contractor\nconstruction company\nbuilding contractor\nhome builder',
    restaurant:'restaurant\ncafe\nbistro\nbar\ndiner',
    auto_repair:'auto repair\nmechanic\ncar repair shop\nauto body shop',
    roofing:'roofing contractor\nroof repair\nroofing company',
    auto_dealer:'car dealership\nauto dealer\nused car dealer',
    accounting:'accountant\ntax preparer\nCPA\nbookkeeper',
    healthcare:'medical clinic\ndoctor\nurgent care\nfamily medicine',
    dental:'dentist\ndental clinic\nfamily dentist\ncosmetic dentist',
    fnb:'restaurant\ncatering\nbakery\nfood truck',
    locksmith:'locksmith\nlock repair\nemergency locksmith',
    salon:'hair salon\nbeauty salon\nbarber shop\nnail salon',
    landscaping:'landscaping\nlawn care\ntree service\ngarden maintenance',
    plumber:'plumber\nplumbing service\ndrain cleaning',
    lawyer:'lawyer\nattorney\nlaw firm',
    realtor:'real estate agent\nrealtor\nproperty management',
};
function useTpl(k){
    if(!TPL[k])return;
    document.getElementById('searchTerms').value=TPL[k];
    if(!usedTpl.includes(k)){usedTpl.push(k);localStorage.setItem('sb_tpl',JSON.stringify(usedTpl));}
    markTpl(); toast('Template: '+k);
}
function markTpl(){
    document.querySelectorAll('#tplWrap .pill').forEach(p=>{
        const m=(p.getAttribute('onclick')||'').match(/useTpl\('(.+?)'\)/);
        if(m&&usedTpl.includes(m[1]))p.classList.add('used');
    });
}

// ── STATE PILLS ────────────────────────────────────────
function pickSt(el,st){
    document.querySelectorAll('#stPills .pill').forEach(p=>p.classList.remove('on'));
    const dd=document.getElementById('usState');
    if(dd.value===st){dd.value='';}else{dd.value=st;el.classList.add('on');}
}
function syncStPill(){
    const v=document.getElementById('usState').value;
    document.querySelectorAll('#stPills .pill').forEach(p=>{
        p.classList.toggle('on',(p.getAttribute('onclick')||'').includes("'"+v+"'")&&v);
    });
}

// ── STATS ──────────────────────────────────────────────
async function loadStats(){
    try{
        const r=await fetch('api.php?action=stats'), d=await r.json();
        document.getElementById('sRuns').textContent=d.totalRuns||0;
        document.getElementById('sLeads').textContent=(d.dbLeads||0).toLocaleString();
        document.getElementById('sEmails').textContent=(d.dbEmails||0).toLocaleString();
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
        const rem=Math.max(0,lim-used), pct=lim>0?Math.min(100,(used/lim)*100):0;
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
            const t=(run.searchTerms||[]).join(', ');
            const sc=run.status==='SUCCEEDED'?'st-ok':run.status==='FAILED'?'st-fail':'st-run';
            const cost=run.costUsd?(' | $'+parseFloat(run.costUsd).toFixed(2)):'';
            h+='<div class="ri"><div><div class="ri-t" title="'+esc(t)+'">'+esc(t)+'</div><div class="ri-m">'+(run.state||'')+' | '+(run.startedAt||'')+cost+'</div></div><div style="display:flex;gap:5px;align-items:center"><span class="st '+sc+'">'+run.status+'</span><button class="btn btn-s btn-o" onclick="loadRes(\''+run.runId+'\')">View</button></div></div>';
        });
        document.getElementById('runsBox').innerHTML=h;
    }catch(e){}
}

// ── START SCRAPE ───────────────────────────────────────
async function startScrape(){
    const st=document.getElementById('searchTerms').value.trim();
    if(!st){toast('Enter search terms');return;}
    const btn=document.getElementById('btnScrape');
    btn.disabled=true;btn.innerHTML='<span class="sp"></span> Starting...';
    try{
        const r=await fetch('api.php?action=start',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({
            searchTerms:st, country:'United States', state:document.getElementById('usState').value,
            city:document.getElementById('city').value.trim(),
            location:(document.getElementById('usState').value||'')+', United States',
            maxResults:parseInt(document.getElementById('maxResults').value),
            language:document.getElementById('language').value
        })});
        const d=await r.json();
        if(d.success){
            activeRunId=d.runId; toast('Scrape started!');
            document.getElementById('resTable').innerHTML='<div class="empty"><span class="sp" style="width:24px;height:24px;border-width:3px"></span><h4 style="margin-top:10px">Scraping...</h4><p>Results appear automatically</p></div>';
            document.getElementById('resTb').style.display='none';
            pollStatus(d.runId); loadRuns();
        }else toast('Error: '+(d.error||'Failed'));
    }catch(e){toast('Error: '+e.message);}
    btn.disabled=false;btn.textContent='Start Scraping';
}

function pollStatus(rid){
    if(pollTimer)clearInterval(pollTimer);
    pollTimer=setInterval(async()=>{
        try{
            const r=await fetch('api.php?action=status&runId='+rid),d=await r.json();
            if(d.status==='SUCCEEDED'){clearInterval(pollTimer);pollTimer=null;toast('Done!');loadRes(rid);loadRuns();loadStats();loadLimits();}
            else if(d.status==='FAILED'||d.status==='ABORTED'){clearInterval(pollTimer);pollTimer=null;toast('Run '+d.status);loadRuns();}
        }catch(e){}
    },5000);
}

async function loadRes(rid){
    document.getElementById('resTable').innerHTML='<div class="empty"><span class="sp" style="width:24px;height:24px;border-width:3px"></span><h4 style="margin-top:10px">Loading...</h4></div>';
    document.getElementById('resTb').style.display='none';
    try{
        const r=await fetch('api.php?action=results&runId='+rid),d=await r.json();
        if(d.success){leads=d.leads||[];filtered=leads;renderRes();toast(leads.length+' leads');}
        else document.getElementById('resTable').innerHTML='<div class="empty"><h4>Failed</h4></div>';
    }catch(e){document.getElementById('resTable').innerHTML='<div class="empty"><h4>Error</h4></div>';}
}

// ── RENDER RESULTS ─────────────────────────────────────
function renderRes(){
    if(!filtered.length){document.getElementById('resTable').innerHTML='<div class="empty"><h4>No results</h4></div>';document.getElementById('resTb').style.display='none';return;}
    document.getElementById('resTb').style.display='flex';
    const we=filtered.filter(l=>l.email).length, wp=filtered.filter(l=>l.phone).length;
    document.getElementById('selInfo').textContent='0 sel | '+filtered.length+' total | '+we+' emails | '+wp+' phones';
    let h='<table><thead><tr><th class="cc"><input type="checkbox" id="selAllTh" onchange="togSelAll()"></th><th>#</th><th>Business</th><th>Category</th><th>Rating</th><th>Phone</th><th>Email</th><th>Website</th><th>Socials</th><th>City/State</th><th>Maps</th></tr></thead><tbody>';
    filtered.forEach((l,i)=>{
        const es=l.email?'color:var(--green);font-weight:500':'color:var(--tx4)';
        const cl=l.permanentlyClosed?' <span style="color:var(--red);font-size:8px">[CLOSED]</span>':'';
        let so='';
        if(l.facebook)so+='<a href="'+esc(l.facebook)+'" target="_blank" style="margin-right:2px">FB</a>';
        if(l.instagram)so+='<a href="'+esc(l.instagram)+'" target="_blank" style="margin-right:2px">IG</a>';
        if(l.linkedin)so+='<a href="'+esc(l.linkedin)+'" target="_blank" style="margin-right:2px">LI</a>';
        if(!so)so='<span style="color:var(--tx4)">-</span>';
        const cs=[l.city,l.state].filter(Boolean).join(', ');
        h+='<tr><td class="cc"><input type="checkbox" class="lcb" data-i="'+i+'" onchange="updSel()"></td><td>'+(i+1)+'</td><td title="'+esc(l.title)+'">'+esc(l.title||'-')+cl+'</td><td>'+esc(l.categoryName||'-')+'</td><td><span class="stars">'+star(l.totalScore)+'</span><span class="rn">'+(l.totalScore||'-')+'('+( l.reviewsCount||0)+')</span></td><td>'+esc(l.phone||'-')+'</td><td style="'+es+'">'+esc(l.email||'N/A')+'</td><td>'+(l.website?'<a href="'+esc(l.website)+'" target="_blank">Visit</a>':'-')+'</td><td>'+so+'</td><td>'+esc(cs||'-')+'</td><td>'+(l.url?'<a href="'+esc(l.url)+'" target="_blank">Maps</a>':'-')+'</td></tr>';
    });
    h+='</tbody></table>';
    document.getElementById('resTable').innerHTML=h;
}

function filterRes(){
    const q=document.getElementById('filterIn').value.toLowerCase();
    filtered=q?leads.filter(l=>(l.title||'').toLowerCase().includes(q)||(l.email||'').toLowerCase().includes(q)||(l.phone||'').toLowerCase().includes(q)||(l.categoryName||'').toLowerCase().includes(q)||(l.city||'').toLowerCase().includes(q)):leads;
    renderRes();
}
function togSelAll(){const c=event.target.checked;document.querySelectorAll('.lcb').forEach(x=>x.checked=c);['selAll','selAllTh'].forEach(id=>{const e=document.getElementById(id);if(e)e.checked=c;});updSel();}
function updSel(){const n=document.querySelectorAll('.lcb:checked').length;document.getElementById('btnExp').disabled=n===0;const we=filtered.filter(l=>l.email).length;document.getElementById('selInfo').textContent=n+' sel | '+filtered.length+' total | '+we+' emails';}

// ── SAVE TO DB ─────────────────────────────────────────
async function saveToDb(){
    if(!leads.length){toast('No leads');return;}
    const btn=document.getElementById('btnSave');btn.disabled=true;btn.textContent='Saving...';
    try{
        const r=await fetch('api.php?action=save_leads',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({leads:leads,runId:activeRunId||''})});
        const d=await r.json();
        if(d.success){toast(d.saved+' new leads saved ('+d.total+' total)');loadStats();}
        else toast('Error saving');
    }catch(e){toast('Error: '+e.message);}
    btn.disabled=false;btn.textContent='Save to DB';
}

// ── EXPORTS ────────────────────────────────────────────
function exportEmails(){
    const sel=[];document.querySelectorAll('.lcb:checked').forEach(cb=>{const l=filtered[+cb.dataset.i];if(l&&l.email)sel.push(l);});
    if(!sel.length){toast('No emails selected');return;}
    let c='Business Name,Email,Phone,Category,Rating,Reviews,Address,Website,Google Maps\n';
    sel.forEach(l=>{c+=csvR([l.title,l.email,l.phone,l.categoryName,l.totalScore,l.reviewsCount,l.address,l.website,l.url]);});
    dl(c,'leads_emails_'+ds()+'.csv');toast(sel.length+' exported');
}
function exportCSV(){
    if(!filtered.length){toast('No results');return;}
    let c='Business,Category,Rating,Reviews,Phone,Email,Website,Address,City,State,Zip,Facebook,Instagram,LinkedIn,Google Maps,PlaceID\n';
    filtered.forEach(l=>{c+=csvR([l.title,l.categoryName,l.totalScore,l.reviewsCount,l.phone,l.email,l.website,l.address,l.city,l.state,l.zipCode,l.facebook,l.instagram,l.linkedin,l.url,l.placeId]);});
    dl(c,'all_leads_'+ds()+'.csv');toast(filtered.length+' exported');
}

// ── LEADS DATABASE TAB ─────────────────────────────────
async function loadLeadsDb(){
    const cat=document.getElementById('dbFilterCat').value;
    const st=document.getElementById('dbFilterSt').value;
    const q=document.getElementById('dbSearch').value.trim();
    let url='api.php?action=leads';
    if(cat)url+='&category='+encodeURIComponent(cat);
    if(st)url+='&state='+encodeURIComponent(st);
    if(q)url+='&search='+encodeURIComponent(q);
    try{
        const r=await fetch(url),d=await r.json();
        dbLeads=d.leads||[];
        document.getElementById('dbInfo').textContent=dbLeads.length+' leads | '+(d.withEmail||0)+' with email';
        if(!dbLeads.length){document.getElementById('dbTable').innerHTML='<div class="empty"><div class="empty-i">&#128203;</div><h4>No leads saved yet</h4><p>Leads are saved automatically after each scrape</p></div>';return;}
        let h='<table><thead><tr><th class="cc"><input type="checkbox" id="dbSelAllTh" onchange="togDbSelAll()"></th><th>#</th><th>Business</th><th>Category</th><th>Rating</th><th>Phone</th><th>Email</th><th>City/State</th><th>Website</th><th>Saved</th></tr></thead><tbody>';
        dbLeads.forEach((l,i)=>{
            const es=l.email?'color:var(--green);font-weight:500':'color:var(--tx4)';
            const cs=[l.city,l.state].filter(Boolean).join(', ');
            h+='<tr><td class="cc"><input type="checkbox" class="dcb" data-i="'+i+'"></td><td>'+(i+1)+'</td><td>'+esc(l.title||'-')+'</td><td>'+esc(l.categoryName||'-')+'</td><td><span class="stars">'+star(l.totalScore)+'</span><span class="rn">'+(l.totalScore||'-')+'</span></td><td>'+esc(l.phone||'-')+'</td><td style="'+es+'">'+esc(l.email||'N/A')+'</td><td>'+esc(cs||'-')+'</td><td>'+(l.website?'<a href="'+esc(l.website)+'" target="_blank">Visit</a>':'-')+'</td><td style="font-size:9px;color:var(--tx4)">'+(l.savedAt||'-').slice(0,10)+'</td></tr>';
        });
        h+='</tbody></table>';
        document.getElementById('dbTable').innerHTML=h;
    }catch(e){document.getElementById('dbTable').innerHTML='<div class="empty"><h4>Error loading</h4></div>';}
}
function togDbSelAll(){const c=event.target.checked;document.querySelectorAll('.dcb').forEach(x=>x.checked=c);['dbSelAll','dbSelAllTh'].forEach(id=>{const e=document.getElementById(id);if(e)e.checked=c;});}
function exportDbEmails(){
    const sel=[];document.querySelectorAll('.dcb:checked').forEach(cb=>{const l=dbLeads[+cb.dataset.i];if(l&&l.email)sel.push(l);});
    if(!sel.length){toast('No emails selected');return;}
    let c='Business,Email,Phone,Category,City,State\n';
    sel.forEach(l=>{c+=csvR([l.title,l.email,l.phone,l.categoryName,l.city,l.state]);});
    dl(c,'db_emails_'+ds()+'.csv');toast(sel.length+' exported');
}
function exportDbCSV(){
    if(!dbLeads.length){toast('No leads');return;}
    let c='Business,Category,Rating,Phone,Email,Website,Address,City,State,Zip,Facebook,Instagram,Google Maps\n';
    dbLeads.forEach(l=>{c+=csvR([l.title,l.categoryName,l.totalScore,l.phone,l.email,l.website,l.address,l.city,l.state,l.zipCode,l.facebook,l.instagram,l.url]);});
    dl(c,'db_all_'+ds()+'.csv');toast(dbLeads.length+' exported');
}

// ── RUN HISTORY TAB ────────────────────────────────────
async function loadHistory(){
    try{
        const r=await fetch('api.php?action=runs'),d=await r.json(),runs=d.runs||[];
        if(!runs.length){document.getElementById('histTable').innerHTML='<div class="empty"><h4>No runs</h4></div>';return;}
        let totL=0,totE=0,totC=0;
        let h='<table class="ht"><thead><tr><th>#</th><th>Search Terms</th><th>State</th><th>City</th><th>Results</th><th>Emails</th><th>Cost</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>';
        runs.forEach((run,i)=>{
            const t=(run.searchTerms||[]).join(', ');
            const sc=run.status==='SUCCEEDED'?'st-ok':run.status==='FAILED'?'st-fail':'st-run';
            const rc=run.resultsCount||0, ec=run.emailsCount||0, cc=parseFloat(run.costUsd||0);
            totL+=rc;totE+=ec;totC+=cc;
            h+='<tr><td>'+(i+1)+'</td><td style="max-width:200px;overflow:hidden;text-overflow:ellipsis" title="'+esc(t)+'">'+esc(t)+'</td><td>'+esc(run.state||'-')+'</td><td>'+esc(run.city||'-')+'</td><td>'+rc+'</td><td>'+ec+'</td><td>$'+cc.toFixed(2)+'</td><td><span class="st '+sc+'">'+run.status+'</span></td><td style="font-size:10px">'+(run.startedAt||'').slice(0,16)+'</td><td><button class="btn btn-s btn-o" onclick="showTab(0);setTimeout(()=>loadRes(\''+run.runId+'\'),100)">View</button></td></tr>';
        });
        h+='<tr class="ht-total"><td colspan="4" style="text-align:right">TOTALS:</td><td>'+totL+'</td><td>'+totE+'</td><td>$'+totC.toFixed(2)+'</td><td colspan="3"></td></tr>';
        h+='</tbody></table>';
        document.getElementById('histTable').innerHTML=h;
    }catch(e){document.getElementById('histTable').innerHTML='<div class="empty"><h4>Error</h4></div>';}
}

// ── HELPERS ────────────────────────────────────────────
function esc(s){if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
function star(s){if(!s)return '';let r='';for(let i=0;i<Math.floor(s);i++)r+='&#9733;';return r;}
function csvR(a){return a.map(v=>'"'+String(v||'').replace(/"/g,'""')+'"').join(',')+'\n';}
function dl(c,f){const b=new Blob([c],{type:'text/csv'});const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=f;a.click();}
function ds(){return new Date().toISOString().slice(0,10).replace(/-/g,'');}
function toast(m){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=m;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
</script>
</body>
</html>
