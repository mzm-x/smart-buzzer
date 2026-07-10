<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="robots" content="noindex,nofollow">
<title>Presence Research — Smart Buzzer</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;font-size:16px;line-height:1.5;background:#F5F5F5;color:#1A1A1A;-webkit-font-smoothing:antialiased;padding-bottom:40px}
button{cursor:pointer;border:none;background:none;font-family:inherit}
input,textarea{font-family:inherit}

.topbar{position:sticky;top:0;z-index:100;background:#fff;border-bottom:1px solid #E0E0E0;height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 16px}
.tb-brand{display:flex;align-items:center;gap:10px}
.tb-img{height:34px;width:auto;object-fit:contain}
.tb-sub{font-size:12px;font-weight:600;color:#666;letter-spacing:.04em;text-transform:uppercase;border-left:1px solid #E0E0E0;padding-left:10px}
.tb-count{font-size:13px;font-weight:600;color:#2E7D32;font-variant-numeric:tabular-nums}

.wrap{max-width:560px;margin:0 auto;padding:0 16px}

.cover-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:30px 26px;margin-top:24px;animation:fsi .3s ease}
.cover-kicker{display:inline-block;background:#E8F5E9;color:#2E7D32;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:6px 12px;border-radius:20px;margin-bottom:16px}
.cover-title{font-size:26px;font-weight:800;line-height:1.25;letter-spacing:-.01em;margin-bottom:12px}
.cover-body{font-size:15px;color:#555;line-height:1.65;margin-bottom:20px}
.cover-facts{display:flex;gap:0;border:1px solid #E8E8E8;border-radius:10px;overflow:hidden;margin-bottom:22px}
.fact{flex:1;text-align:center;padding:12px 6px;background:#FAFAFA}
.fact+.fact{border-left:1px solid #E8E8E8}
.fact .n{font-size:20px;font-weight:800;color:#2E7D32}
.fact .l{font-size:11.5px;color:#777}
.btn-start{width:100%;padding:15px 24px;border-radius:10px;background:#2E7D32;color:#fff;font-size:16px;font-weight:700;transition:background .15s}
.btn-start:hover{background:#1B5E20}
.btn-start:active{transform:scale(.99)}
.resume-note{text-align:center;font-size:12.5px;color:#2E7D32;font-weight:600;margin-top:12px}
.cover-tiny{text-align:center;font-size:12px;color:#999;margin-top:14px}
.cover-field{margin-bottom:14px}
.cover-field label{display:block;font-size:13.5px;font-weight:600;color:#1A1A1A;margin-bottom:6px}
.cover-field label span{color:#999;font-weight:400}
.cover-field input{width:100%;border:1.5px solid #E0E0E0;border-radius:10px;padding:13px 14px;font-size:15px;outline:none;transition:border-color .15s}
.cover-field input:focus{border-color:#2E7D32}

.prog{display:flex;align-items:center;gap:12px;padding:16px 2px 4px}
.prog-track{flex:1;height:8px;background:#E0E0E0;border-radius:4px;overflow:hidden}
.prog-track i{display:block;height:100%;width:0;background:linear-gradient(90deg,#43A047,#2E7D32);border-radius:4px;transition:width .35s ease}
.prog-lbl{font-size:12.5px;color:#2E7D32;white-space:nowrap;font-weight:700}
.gift-hook{background:#FFF8E1;border:1px solid #FFE08A;color:#7A5200;font-size:13px;font-weight:600;border-radius:10px;padding:11px 14px;margin-bottom:16px;line-height:1.5}
.gift-hook b{color:#5A3D00}

.screen{display:none}
.screen.active{display:block;animation:fsi .28s ease forwards}
@keyframes fsi{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:24px 22px;margin-top:12px}
.q-meta{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.q-lbl{font-size:12px;font-weight:700;color:#2E7D32;text-transform:uppercase;letter-spacing:.6px}
.q-star{margin-left:auto;font-size:10px;font-weight:700;color:#B26A00;background:#FFF3E0;border-radius:20px;padding:3px 9px;letter-spacing:.04em}
.q-title{font-size:19px;font-weight:700;line-height:1.35;margin-bottom:6px}
.q-help{font-size:13.5px;color:#777;margin-bottom:18px}
.q-title+.opts,.q-title+textarea,.q-title+.notewrap{margin-top:18px}

.opts{display:flex;flex-direction:column;gap:9px}
.opt{display:flex;align-items:center;gap:12px;border:1.5px solid #E0E0E0;background:#fff;border-radius:10px;padding:13px 14px;transition:border-color .15s,background .15s;text-align:left;width:100%}
.opt:hover{border-color:#A5D6A7}
.opt.dim{opacity:.45;pointer-events:none}
.opt .mark{width:20px;height:20px;flex-shrink:0;border:1.6px solid #BDBDBD;display:flex;align-items:center;justify-content:center;transition:all .15s;background:#fff}
.opt.radio .mark{border-radius:50%}
.opt.check .mark{border-radius:6px}
.opt .mark svg{width:12px;height:12px;opacity:0;transform:scale(.5);transition:.15s}
.opt .txt{font-size:15px;font-weight:500}
.opt.sel{border-color:#2E7D32;background:#F1F8F1}
.opt.sel .mark{background:#2E7D32;border-color:#2E7D32}
.opt.sel .mark svg{opacity:1;transform:scale(1)}

.reveal{margin:0 0 0 32px;max-height:0;overflow:hidden;transition:max-height .2s ease}
.reveal.show{max-height:120px}
.reveal input{width:100%;border:none;border-bottom:1.5px solid #E0E0E0;background:transparent;padding:9px 2px;font-size:14px;outline:none;margin:6px 0}
.reveal input:focus{border-color:#2E7D32}

.notewrap{margin-top:14px}
.notewrap label{display:block;font-size:12.5px;color:#777;margin-bottom:6px}
.notewrap input{width:100%;border:1.5px solid #E0E0E0;border-radius:10px;padding:12px 14px;font-size:14.5px;outline:none;transition:border-color .15s}
.notewrap input:focus{border-color:#2E7D32}

textarea.longtext{width:100%;min-height:130px;resize:vertical;border:1.5px solid #E0E0E0;border-radius:10px;padding:13px 14px;font-size:15px;line-height:1.6;outline:none;transition:border-color .15s}
textarea.longtext:focus{border-color:#2E7D32}

.contact{margin-top:4px;margin-left:32px;display:flex;flex-direction:column;gap:9px;max-height:0;overflow:hidden;transition:max-height .25s ease}
.contact.show{max-height:220px;margin-top:10px}
.contact input{width:100%;border:1.5px solid #E0E0E0;border-radius:10px;padding:12px 14px;font-size:14.5px;outline:none;transition:border-color .15s}
.contact input:focus{border-color:#2E7D32}

.nav{display:flex;align-items:center;gap:10px;padding:16px 0 6px}
.btn{padding:14px 22px;border-radius:10px;font-size:15px;font-weight:700;transition:.15s}
.btn-next{background:#2E7D32;color:#fff;flex:1}
.btn-next:hover{background:#1B5E20}
.btn-next:active{transform:scale(.99)}
.btn-next:disabled{background:#CDE4CE;color:#EAF5EA;cursor:not-allowed}
.btn-next:disabled:hover{background:#CDE4CE}
.btn-back{background:#fff;color:#555;border:1.5px solid #E0E0E0}
.btn-back:hover{border-color:#BDBDBD}

.thanks{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:44px 26px;margin-top:28px;text-align:center;animation:fsi .35s ease}
.thanks .tick{width:74px;height:74px;border-radius:50%;background:#E8F5E9;color:#2E7D32;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:34px}
.thanks h2{font-size:24px;font-weight:800;margin-bottom:10px}
.thanks p{color:#666;font-size:15px;line-height:1.65;max-width:42ch;margin:0 auto}
.reward{margin-top:22px;border:1.5px dashed #A5D6A7;border-radius:12px;padding:18px 16px;background:#F1F8F1;text-align:left}
.reward h3{font-size:14px;font-weight:800;color:#2E7D32;margin-bottom:4px}
.reward p{font-size:13px;color:#555;margin-bottom:12px;max-width:none}
.reward .opts2{display:flex;flex-direction:column;gap:8px}
.reward .ropt{display:flex;align-items:center;gap:10px;border:1.5px solid #C8E6C9;background:#fff;border-radius:10px;padding:11px 13px;font-size:14px;font-weight:600;color:#1A1A1A;text-align:left;width:100%}
.reward .ropt.sel{border-color:#2E7D32;background:#E8F5E9}
.reward .rin{margin-top:12px;display:none}
.reward .rin.show{display:block}
.reward .rin input{width:100%;border:1.5px solid #C8E6C9;border-radius:10px;padding:12px 14px;font-size:14px;outline:none}
.reward .rin input:focus{border-color:#2E7D32}
.reward .rdone{font-size:13px;color:#2E7D32;font-weight:700;margin-top:10px;display:none}
.footer{text-align:center;font-size:12px;color:#999;padding:22px 0 10px}
</style>
</head>
<body>

<div class="topbar">
  <div class="tb-brand">
    <img class="tb-img" src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
    <span class="tb-sub">Presence Research</span>
  </div>
  <span class="tb-count" id="tbCount">~5 min</span>
</div>

<div class="wrap">

  <!-- COVER -->
  <section class="screen active" id="cover">
    <div class="cover-card">
      <span class="cover-kicker">100% Research — No Sales</span>
      <div class="cover-title">How do local hospitality businesses manage their online presence?</div>
      <p class="cover-body">We're studying how businesses like yours handle reviews &amp; online presence (Tripadvisor, Google, etc.) — 100% research, no sales, no pitch. About 5 minutes, one easy question at a time.</p>
      <div class="gift-hook">&#127873; <b>Finish and get 2 free thank-yous:</b><br>1. Your pick of <b>10 Tripadvisor reviews</b> or <b>1,000 Instagram followers</b>, on us<br>2. A <b>free benchmark report</b> emailed to you — see how businesses like yours compare</div>
      <div class="cover-facts">
        <div class="fact"><div class="n">~5</div><div class="l">minutes</div></div>
        <div class="fact"><div class="n">&#127873;</div><div class="l">free gift</div></div>
        <div class="fact"><div class="n">0</div><div class="l">sales pitch</div></div>
      </div>
      <div class="cover-field">
        <label>Where should we send your gift &amp; report? <span>(optional)</span></label>
        <input type="text" id="coverWa" placeholder="WhatsApp number" style="margin-bottom:8px">
        <input type="email" id="coverEmail" placeholder="Email (for your free report)">
      </div>
      <button class="btn-start" id="beginBtn">Start &amp; claim my gift</button>
      <div class="resume-note" id="resumeNote" style="display:none">You have an unfinished draft — Start will continue where you left off.</div>
      <div class="cover-tiny">No spam, no sales — your contact is only used to send the gift.</div>
    </div>
  </section>

  <!-- SURVEY -->
  <section class="screen" id="stage">
    <div class="prog">
      <div class="prog-track"><i id="progFill"></i></div>
      <span class="prog-lbl" id="progLbl"></span>
    </div>
    <div class="card" id="card"></div>
    <div class="nav">
      <button class="btn btn-back" id="backBtn">Back</button>
      <button class="btn btn-next" id="nextBtn">Continue</button>
    </div>
  </section>

  <!-- THANKS -->
  <section class="screen" id="thanks">
    <div class="thanks">
      <div class="tick">&#10003;</div>
      <h2>That's everything — thank you!</h2>
      <p>Your answers genuinely help us understand how businesses like yours manage their online presence. No sales follow-up — this was research only.</p>

      <div class="reward" id="rewardBox">
        <h3>&#127873; Small thank-you (optional)</h3>
        <p>Pick one free gift and we'll set it up for you — no cost, no strings.</p>
        <div class="opts2">
          <button class="ropt" data-r="tripadvisor">10 free Tripadvisor reviews</button>
          <button class="ropt" data-r="followers">1,000 free Instagram followers</button>
          <button class="ropt" data-r="none">No thanks</button>
        </div>
        <div class="rin" id="rewardIn">
          <input type="text" id="rewardWa2" placeholder="WhatsApp number" style="margin-bottom:8px">
          <input type="email" id="rewardEmail2" placeholder="Email (for gift + report)">
          <button class="btn-start" id="rewardSend" style="margin-top:10px">Claim my gift</button>
        </div>
        <div class="rdone" id="rewardDone">&#10003; Got it — we'll be in touch to set up your gift.</div>
      </div>
    </div>
  </section>

  <div class="footer">Smart Buzzer &middot; Presence Research</div>
</div>

<script>
/* ============ QUESTION BANK (Q1–Q24) ============ */
var SECTIONS = {A:'About you',B:'Your customers',C:'Tripadvisor',D:"What's tricky",E:'Tools & budget',F:'Your take',G:'Last one'};
var Q = [
 /* A — WHO */
 {id:'q1', s:'A', type:'radio', title:"What's your role?",
   opts:['Owner','Manager','Marketing',{v:'Other',reveal:'Your role'}]},
 {id:'q2', s:'A', type:'radio', title:'How many locations do you operate?',
   opts:['1','2–4','5–15','15+']},
 {id:'q3', s:'A', type:'radio', title:"Roughly what's your current average rating?",
   opts:['Under 3.5','3.5–4.2','4.3–4.6','4.7+','Not sure']},
 {id:'q4', s:'A', type:'radio', title:'Roughly how many reviews on your main platform?',
   opts:['<20','20–100','100–500','500+']},

 /* B — CUSTOMER & REVENUE BEHAVIOR */
 {id:'q5', s:'B', type:'radio', star:true, title:"What's the #1 thing you're trying to GROW right now?",
   opts:['More new customers','More bookings','More repeat customers','Higher-value customers','Keep it steady',{v:'Other',reveal:'Something else'}]},
 {id:'q6', s:'B', type:'checkbox', star:true, max:2, title:'Where do most of your NEW customers come from today?', help:'Pick up to 2.',
   opts:['Walk-ins','Google','Tripadvisor','Instagram / Facebook','Booking.com / OTAs','Referrals','Paid ads',{v:'Other',reveal:'Where else'}]},
 {id:'q7', s:'B', type:'checkbox', star:true, max:2, title:'When customers are deciding, what do you think they check or care about most?', help:'Pick up to 2.',
   opts:['Rating & reviews','Photos','Price','Location','Recommendations','Social media','Not sure']},
 {id:'q8', s:'B', type:'checkbox', title:'Which platforms do you actively push or invest in today?', help:'Pick all that apply.',
   opts:['Google','Tripadvisor','Facebook','Yelp','Booking.com','Instagram',{v:'None actively',exclusive:true},{v:'Other',reveal:'Which one'}]},

 /* C — TRIPADVISOR */
 {id:'q9', s:'C', type:'radio', star:true, title:'Is Tripadvisor driving customers or revenue for your business?',
   opts:['Yes clearly','Somewhat','Not really','Not sure',"We're not on Tripadvisor"]},
 {id:'q10', s:'C', type:'checkbox', title:'What do you mainly use Tripadvisor for?', help:'Pick all that apply.',
   opts:['Attracting new customers','Reputation & trust','Bookings & enquiries','Photos & business info','Replying to reviews',{v:"We don't actively use it",exclusive:true}]},
 {id:'q11', s:'C', type:'radio', title:'How often do you maintain your Tripadvisor listing (photos, info, replying to reviews)?',
   opts:['Weekly','Monthly','Rarely','Never']},

 /* D — PAIN & CURRENT BEHAVIOR */
 {id:'q12', s:'D', type:'textarea', star:true, title:"What's your single biggest challenge with your online PRESENCE right now?", help:'Not just reviews — the whole picture. A sentence is plenty.',
   placeholder:'Type your answer…'},
 {id:'q13', s:'D', type:'checkbox', star:true, title:'Which of these eat your time or worry you?', help:'Pick all that apply.',
   opts:['Replying to reviews','Keeping photos & info updated','Ranking or visibility vs competitors','Monitoring what’s said about you','Managing multiple platforms or locations','Getting bookings or enquiries from listings',{v:'None',exclusive:true}]},
 {id:'q14', s:'D', type:'textarea', star:true, title:"Of those, which ones can you NOT fix on your own — where you'd want outside help?", help:'Optional.',
   placeholder:'Type your answer…'},
 {id:'q15', s:'D', type:'radio', title:'When you get a negative review, what usually happens?',
   opts:['We respond fast','We respond eventually',"We don't respond","We often don't see it in time"]},
 {id:'q16', s:'D', type:'checkbox', title:'What do you currently DO to get more reviews?', help:'Pick all that apply.',
   opts:['Ask in person','Signage or QR','Email','SMS','Nothing systematic','A tool or agency does it',{v:'Other',reveal:'What else'}]},

 /* E — BUDGET & SPENDING BEHAVIOR */
 {id:'q17', s:'E', type:'radio', star:true, title:'Do you use any tool or service for this today?',
   opts:['No','Free tools only',{v:'Paid tool',reveal:'Which tool?'},{v:'An agency',reveal:'Who?'}]},
 {id:'q18', s:'E', type:'radio', star:true, title:'Have you ever PAID to improve reviews / reputation / presence?',
   opts:[{v:'Yes',reveal:'How much per month?'},'No','Considered it']},
 {id:'q19', s:'E', type:'radio', title:'Roughly how much do you spend on MARKETING per month?',
   opts:['<$100','$100–500','$500–2,000','$2,000+','Prefer not to say'],
   note:'Mostly goes to…'},
 {id:'q20', s:'E', type:'textarea', star:true, title:'Have you ever actively searched for a service to fix this?', help:'If yes — where did you look, and what mattered most when comparing options?',
   placeholder:'Type your answer…'},

 /* F — VALUE */
 {id:'q21', s:'F', type:'radio', star:true, title:'If a tool/service could fix your biggest problem — how valuable is that?',
   opts:['Must-have','Nice-to-have','Not needed']},
 {id:'q22', s:'F', type:'radio', star:true, title:'What would you expect to pay per month for that?',
   opts:['<$30','$30–75','$75–150','$150–300','$300+',"Wouldn't pay"]},
 {id:'q23', s:'F', type:'textarea', star:true, title:'Magic wand: if you could fix ONE thing about how customers find & choose you online, what would it be?',
   placeholder:'Type your answer…'},

 /* G — BRIDGE */
 {id:'q24', s:'G', type:'radio', star:true, title:'Open to a quick 15-min call to tell us more?', help:'No pitch — a small thank-you for your time.',
   opts:[{v:'Yes',contact:true},'Maybe later','No']}
];

/* ============ STATE ============ */
var KEY = 'tp_survey_v3';
var params = new URLSearchParams(location.search);
// Parse survey criteria from URL. Tolerant: accepts ?seg=high_high&tier=nontourist&type=restaurant,
// legacy ?seg=4N, full text, or a combined ?c=high_high+nontourist+restaurant / bare ?=... .
// Normalizes to {seg:'4N' (quadrant+T/N), type:'Restaurant'}.
function normCrit(){
  var p = new URLSearchParams(location.search);
  var quad='', tier='', type='';
  function toQuad(s){ s=(s||'').toLowerCase().replace(/[^a-z0-9]/g,'');
    if(/^(1|s1|lowlow|lowratinglowreview)$/.test(s))return '1';
    if(/^(2|s2|lowhigh|lowratinghighreview)$/.test(s))return '2';
    if(/^(3|s3|highlow|highratinglowreview)$/.test(s))return '3';
    if(/^(4|s4|highhigh|highratinghighreview)$/.test(s))return '4';
    var m=s.match(/^([1-4])[tn]$/); return m?m[1]:''; }
  function toTier(s){ s=(s||'').toLowerCase().replace(/[^a-z]/g,'');
    if(/nontourist|^non$|^n$/.test(s))return 'N';
    if(/tourist|^t$/.test(s))return 'T'; return ''; }
  function toType(s){ s=(s||'').toLowerCase();
    if(/restaurant|resto|cafe|bistro|diner/.test(s))return 'Restaurant';
    if(/hotel|b&b|bnb|motel|inn|lodg/.test(s))return 'Hotel';
    if(/things.?to.?do|attraction|activit|\btours?\b|operator/.test(s))return 'Things to Do'; return ''; }
  // explicit params first
  var segRaw=p.get('seg')||'';
  var sc=segRaw.toLowerCase().replace(/[^a-z0-9]/g,'').match(/^([1-4])([tn])$/);
  if(sc){ quad=sc[1]; tier=sc[2].toUpperCase(); } else { quad=toQuad(segRaw); }
  if(!tier) tier=toTier(p.get('tier')||p.get('geo'));
  type=toType(p.get('type'));
  // combined ?c=/?crit=/bare ?=... — URL '+' already decodes to space, so detect from the joined string
  var combined=(p.get('c')||p.get('crit')||p.get('')||'').toLowerCase();
  if(combined){
    var j=combined.replace(/[^a-z0-9]/g,'');
    if(!quad){ if(/highratinghighreview|highhigh/.test(j))quad='4';
      else if(/highratinglowreview|highlow/.test(j))quad='3';
      else if(/lowratinghighreview|lowhigh/.test(j))quad='2';
      else if(/lowratinglowreview|lowlow/.test(j))quad='1'; }
    if(!tier){ if(/nontourist/.test(j))tier='N'; else if(/tourist/.test(j))tier='T'; }
    if(!type){ if(/restaurant|resto|cafe|bistro|diner/.test(j))type='Restaurant';
      else if(/hotel|bnb|motel|inn|lodg/.test(j))type='Hotel';
      else if(/thingstodo|attraction|activit|tours/.test(j))type='Things to Do'; }
  }
  return { seg: quad?(quad+(tier||'')):segRaw.toUpperCase(), type: type };
}
var crit = normCrit();
var state = {
  rid: null,
  seg: crit.seg,
  type: crit.type,
  channel: params.get('channel') || '',
  startedAt: null,
  idx: 0,
  answers: {},
  reward: null,
  rewardWa: '',
  rewardEmail: '',
  rewardContact: ''
};
var saved = null;
try { saved = JSON.parse(localStorage.getItem(KEY) || 'null'); } catch(e) {}

var el = function(id){ return document.getElementById(id); };
function ans(id){ return state.answers[id] || {}; }
function esc(s){ return String(s==null?'':s).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
function needsAnswer(q){ return q.type === 'radio' || q.type === 'checkbox'; }
function isAnswered(q){
  var a = ans(q.id);
  if (q.type === 'checkbox') return (a.values || []).length > 0;
  if (q.type === 'radio') return !!a.value;
  return true; // open-text is optional
}

/* ============ COVER / RESUME ============ */
if (saved && saved.startedAt && !saved.completed && saved.idx > 0) {
  el('resumeNote').style.display = 'block';
}
el('beginBtn').addEventListener('click', function(){
  if (saved && saved.startedAt && !saved.completed) {
    state = Object.assign(state, saved);
    var _c = normCrit();
    if (_c.seg)  state.seg  = _c.seg;
    if (_c.type) state.type = _c.type;
    if (params.get('channel')) state.channel = params.get('channel');
  }
  if (!state.rid) state.rid = 'r' + Date.now().toString(36) + Math.random().toString(36).substr(2, 8);
  if (!state.startedAt) state.startedAt = Date.now();
  state.rewardWa = el('coverWa').value.trim();
  state.rewardEmail = el('coverEmail').value.trim();
  state.rewardContact = combineContact();
  save();
  show('stage');
  render();
  sync();
});

function show(id){
  ['cover','stage','thanks'].forEach(function(s){ el(s).classList.toggle('active', s === id); });
}

/* ============ RENDER ============ */
function render(){
  var q = Q[state.idx];
  var pct = Math.round((state.idx + 1) / Q.length * 100);
  var left = Q.length - state.idx; // includes current question
  var sprint = left <= 5;
  el('progFill').style.width = pct + '%';
  el('progFill').style.background = sprint ? 'linear-gradient(90deg,#FB8C00,#E53935)' : 'linear-gradient(90deg,#43A047,#2E7D32)';
  el('progLbl').style.color = sprint ? '#E53935' : '#2E7D32';
  el('progLbl').textContent = left === 1 ? '🎉 Last question!' : (sprint ? left + ' questions left' : pct + '% done');
  var h = '<div class="q-meta"><span class="q-lbl">' + SECTIONS[q.s] + '</span></div>' +
          '<div class="q-title">' + q.title + '</div>' +
          (q.help ? '<div class="q-help">' + q.help + '</div>' : '');
  if (q.type === 'radio' || q.type === 'checkbox') h += renderOpts(q);
  else if (q.type === 'textarea') h += renderText(q);
  if (q.note) h += renderNote(q);
  el('card').innerHTML = h;
  wire(q);
  el('backBtn').style.visibility = state.idx === 0 ? 'hidden' : 'visible';
  el('nextBtn').textContent = state.idx === Q.length - 1 ? 'Finish' : 'Continue';
  el('nextBtn').disabled = needsAnswer(q) && !isAnswered(q);
  el('tbCount').textContent = '🎁 Gift at the end';
}

var CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';

function renderOpts(q){
  var a = ans(q.id);
  var sel = q.type === 'checkbox' ? (a.values || []) : a.value;
  var atMax = q.max && q.type === 'checkbox' && sel.length >= q.max;
  var out = '<div class="opts">';
  q.opts.forEach(function(o){
    var v = (typeof o === 'string') ? o : o.v;
    var on = q.type === 'checkbox' ? sel.indexOf(v) > -1 : sel === v;
    var dim = (atMax && !on) ? ' dim' : '';
    out += '<button class="opt ' + q.type + (on ? ' sel' : '') + dim + '" data-v="' + esc(v) + '">' +
           '<span class="mark">' + CHECK + '</span><span class="txt">' + esc(v) + '</span></button>';
    if (typeof o === 'object' && o.reveal) {
      var rv = (a.reveals && a.reveals[v]) || '';
      out += '<div class="reveal' + (on ? ' show' : '') + '" data-for="' + esc(v) + '"><input type="text" placeholder="' + esc(o.reveal) + '" value="' + esc(rv) + '"></div>';
    }
    if (typeof o === 'object' && o.contact) {
      var c = a.contact || {};
      out += '<div class="contact' + (on ? ' show' : '') + '">' +
             '<input type="text" placeholder="Your name" data-c="name" value="' + esc(c.name || '') + '">' +
             '<input type="text" placeholder="Best email or WhatsApp" data-c="contact" value="' + esc(c.contact || '') + '"></div>';
    }
  });
  return out + '</div>';
}

function renderText(q){
  return '<textarea class="longtext" placeholder="' + esc(q.placeholder || '') + '">' + esc(ans(q.id).value || '') + '</textarea>';
}

function renderNote(q){
  return '<div class="notewrap"><label>' + esc(q.note) + '</label>' +
         '<input type="text" id="noteInput" placeholder="e.g. Google Ads, agency, staff, signage…" value="' + esc(ans(q.id).note || '') + '"></div>';
}

/* ============ WIRING ============ */
function wire(q){
  if (q.type === 'radio' || q.type === 'checkbox') {
    el('card').querySelectorAll('.opt').forEach(function(btn){
      btn.addEventListener('click', function(){ if (!btn.classList.contains('dim')) toggle(q, btn.dataset.v); });
    });
    el('card').querySelectorAll('.reveal input').forEach(function(inp){
      inp.addEventListener('input', function(){
        var v = inp.closest('.reveal').dataset.for;
        var a = ans(q.id); a.reveals = a.reveals || {}; a.reveals[v] = inp.value;
        state.answers[q.id] = a; save();
      });
    });
    var cw = el('card').querySelector('.contact');
    if (cw) cw.querySelectorAll('input').forEach(function(inp){
      inp.addEventListener('input', function(){
        var a = ans(q.id); a.contact = a.contact || {}; a.contact[inp.dataset.c] = inp.value;
        state.answers[q.id] = a; save();
      });
    });
  } else if (q.type === 'textarea') {
    var t = el('card').querySelector('textarea');
    t.addEventListener('input', function(){
      var a = ans(q.id); a.value = t.value; state.answers[q.id] = a; save();
    });
  }
  var ni = el('noteInput');
  if (ni) ni.addEventListener('input', function(){
    var a = ans(q.id); a.note = ni.value; state.answers[q.id] = a; save();
  });
}

function toggle(q, v){
  var a = ans(q.id);
  if (q.type === 'radio') { a.value = a.value === v ? '' : v; state.answers[q.id] = a; }
  else {
    a.values = a.values || [];
    var opt = q.opts.find(function(o){ return (typeof o === 'object' ? o.v : o) === v; });
    var excl = typeof opt === 'object' && opt.exclusive;
    if (a.values.indexOf(v) > -1) a.values = a.values.filter(function(x){ return x !== v; });
    else {
      if (excl) a.values = [v];
      else {
        a.values = a.values.filter(function(x){
          var oo = q.opts.find(function(o){ return (typeof o === 'object' ? o.v : o) === x; });
          return !(typeof oo === 'object' && oo.exclusive);
        });
        if (!q.max || a.values.length < q.max) a.values = a.values.concat(v);
      }
    }
    state.answers[q.id] = a;
  }
  save(); render();
}

/* ============ NAV ============ */
el('nextBtn').addEventListener('click', function(){ advance(); });
el('backBtn').addEventListener('click', function(){
  if (state.idx > 0) { state.idx--; save(); render(); window.scrollTo({top: 0, behavior: 'smooth'}); }
});
function advance(){
  var q = Q[state.idx];
  if (needsAnswer(q) && !isAnswered(q)) return; // don't advance without an answer
  if (state.idx >= Q.length - 1) { finish(); return; }
  state.idx++; save(); sync(); render();
  window.scrollTo({top: 0, behavior: 'smooth'});
}
function finish(){
  state.completed = true; state.completedAt = Date.now(); save();
  show('thanks');
  el('tbCount').textContent = 'Done';
  sync(true);
}

/* ============ REWARD (thank-you) ============ */
document.querySelectorAll('#rewardBox .ropt').forEach(function(b){
  b.addEventListener('click', function(){
    document.querySelectorAll('#rewardBox .ropt').forEach(function(x){ x.classList.remove('sel'); });
    b.classList.add('sel');
    state.reward = b.dataset.r; save();
    var showIn = b.dataset.r !== 'none';
    if (showIn) {
      if (!el('rewardWa2').value)    el('rewardWa2').value    = state.rewardWa || '';
      if (!el('rewardEmail2').value) el('rewardEmail2').value = state.rewardEmail || '';
    }
    el('rewardIn').classList.toggle('show', showIn);
    el('rewardDone').style.display = showIn ? 'none' : 'block';
    if (!showIn) sync(true);
  });
});
el('rewardSend').addEventListener('click', function(){
  state.rewardWa = el('rewardWa2').value.trim();
  state.rewardEmail = el('rewardEmail2').value.trim();
  state.rewardContact = combineContact();
  save(); sync(true);
  el('rewardIn').classList.remove('show');
  el('rewardDone').style.display = 'block';
});

/* ============ PERSIST + SYNC ============ */
function combineContact(){
  var p = [];
  if (state.rewardWa) p.push('WA: ' + state.rewardWa);
  if (state.rewardEmail) p.push('Email: ' + state.rewardEmail);
  return p.join(' · ');
}
function save(){ try { localStorage.setItem(KEY, JSON.stringify(state)); } catch(e) {} }

function sync(final){
  var payload = {
    rid: state.rid,
    seg: state.seg,
    type: state.type || '',
    channel: state.channel,
    status: (final || state.completed) ? 'completed' : 'in_progress',
    last_q: state.idx + 1,
    started_at: state.startedAt ? new Date(state.startedAt).toISOString() : '',
    time_spent_seconds: state.startedAt ? Math.round((Date.now() - state.startedAt) / 1000) : 0,
    reward: state.reward || '',
    reward_contact: combineContact(),
    answers: state.answers
  };
  try {
    fetch('submit.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload),
      keepalive: true
    }).catch(function(){});
  } catch(e) {}
}

/* ===== Auto-resume: if the tab was closed mid-survey, jump back in ===== */
if (saved && saved.startedAt && !saved.completed) {
  state = Object.assign(state, saved);
  var _c2 = normCrit();
  if (_c2.seg)  state.seg  = _c2.seg;
  if (_c2.type) state.type = _c2.type;
  if (params.get('channel')) state.channel = params.get('channel');
  if (state.idx >= Q.length) state.idx = Q.length - 1;
  show('stage');
  render();
}
</script>
</body>
</html>
