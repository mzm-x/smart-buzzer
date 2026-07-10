<?php $__wa=$_SERVER["DOCUMENT_ROOT"]."/wa-config.php"; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER="628979133204";} if(empty($SB_WA_DISPLAY)){$SB_WA_DISPLAY="+62 897-9133-204";} ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="robots" content="noindex,nofollow">
<title>Smart Buzzer — Survey</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;font-size:16px;line-height:1.5;background:#F5F5F5;color:#1A1A1A;-webkit-font-smoothing:antialiased;padding-bottom:40px}
button{cursor:pointer;border:none;background:none;font-family:inherit}

/* TOP BAR */
.topbar{position:sticky;top:0;z-index:100;background:white;border-bottom:1px solid #E0E0E0;height:56px;display:flex;align-items:center;padding:0 16px;gap:10px}
.tb-img{height:36px;width:auto;object-fit:contain;flex-shrink:0}

/* EMAIL SCREEN */
.email-wrap{max-width:480px;margin:32px auto 0;padding:0 16px}
.email-hero{text-align:center;margin-bottom:24px}
.email-hero-icon{font-size:48px;margin-bottom:12px}
.email-hero-title{font-size:22px;font-weight:700;color:#1A1A1A;margin-bottom:8px}
.email-hero-sub{font-size:15px;color:#666;line-height:1.6}
.reward-pill{display:inline-flex;align-items:center;gap:6px;background:#E8F5E9;color:#2E7D32;font-size:14px;font-weight:600;padding:8px 16px;border-radius:20px;margin-top:14px}
.email-card{background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:24px}
.email-label{font-size:13px;font-weight:600;color:#1A1A1A;margin-bottom:6px;display:block}
.email-note{font-size:12px;color:#999;margin-left:4px;font-weight:400}
.email-inp{width:100%;padding:13px 16px;border:1.5px solid #E0E0E0;border-radius:8px;font-size:15px;color:#1A1A1A;outline:none;transition:border-color .15s;font-family:inherit;margin-bottom:14px}
.email-inp:focus{border-color:#2E7D32}
.btn-start{width:100%;padding:15px 24px;border-radius:8px;background:#2E7D32;color:white;font-size:16px;font-weight:600;border:none;cursor:pointer;transition:background .15s;margin-top:4px}
.btn-start:hover{background:#1B5E20}
.btn-start:active{transform:scale(.99)}
.email-tiny{text-align:center;font-size:12px;color:#999;margin-top:12px}

/* BANNER */
.banner{margin:16px 16px 0;border-radius:12px;background:linear-gradient(135deg,#2E7D32,#43A047);padding:16px 20px;text-align:center;color:white}
.bn-main{font-size:17px;font-weight:600;line-height:1.4}
.bn-sub{font-size:13px;opacity:.85;margin-top:4px}

/* PROGRESS */
.prog-wrap{display:flex;align-items:center;gap:12px;padding:14px 16px 0}
.prog-bar{display:flex;gap:4px;flex:1;height:6px}
.pseg{flex:1;border-radius:3px;background:#E0E0E0;transition:background .3s}
.pseg.done{background:#66BB6A}
.pseg.cur{background:#2E7D32}
.step-lbl{font-size:13px;color:#666;white-space:nowrap;flex-shrink:0}

/* SCREENS */
.screen{display:none}
.screen.active{display:block;animation:fsi .3s ease forwards}
@keyframes fsi{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

/* CARD */
.card{background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:24px;margin:12px 16px}
.q-lbl{font-size:12px;font-weight:500;color:#2E7D32;text-transform:uppercase;letter-spacing:.5px}
.q-title{font-size:18px;font-weight:600;color:#1A1A1A;margin-top:4px;line-height:1.35}
.q-sub{font-size:14px;color:#666;margin-top:6px;margin-bottom:20px}

/* OPTIONS */
.opt{display:flex;align-items:center;gap:12px;padding:12px 16px;border:1.5px solid #E0E0E0;border-radius:8px;margin-bottom:10px;background:white;cursor:pointer;transition:border-color .15s,background .15s;user-select:none;-webkit-tap-highlight-color:transparent}
.opt:last-of-type{margin-bottom:0}
.opt:active{background:#F5F5F5}
.oi{font-size:18px;width:24px;text-align:center;flex-shrink:0}
.ot{font-size:15px;color:#1A1A1A;flex:1;line-height:1.4}
.ocb{width:20px;height:20px;border-radius:4px;border:1.5px solid #CCC;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s}
.opt.selected{border-color:#2E7D32;background:#E8F5E9}
.opt.selected .ocb{background:#2E7D32;border-color:#2E7D32}
.other-inp{width:100%;padding:10px 14px;border:1.5px solid #E0E0E0;border-radius:8px;font-size:14px;outline:none;margin-top:8px;display:none;font-family:inherit}
.other-inp:focus{border-color:#2E7D32}

/* SPEND ROWS */
.srow{display:flex;align-items:center;flex-wrap:wrap;gap:8px;padding:12px 0;border-bottom:1px solid #F0F0F0}
.srow:last-child{border-bottom:none;padding-bottom:0}
.slbl{font-size:14px;color:#1A1A1A;min-width:120px;flex-shrink:0}
.spills{display:flex;gap:6px;flex-wrap:wrap}
.spill{padding:6px 11px;border-radius:6px;border:1px solid #E0E0E0;font-size:12px;color:#666;background:white;cursor:pointer;transition:all .15s;white-space:nowrap;font-family:inherit}
.spill.selected{background:#2E7D32;color:white;border-color:#2E7D32}
.spill:active{transform:scale(.96)}

/* RANK ITEMS */
.ritem{display:flex;align-items:center;gap:12px;padding:12px 16px;border:1.5px solid #E0E0E0;border-radius:8px;margin-bottom:8px;background:white;cursor:pointer;transition:all .15s;user-select:none;-webkit-tap-highlight-color:transparent}
.ritem.ranked{border-color:#2E7D32;background:#E8F5E9;border-left:3px solid #2E7D32}
.rnum{width:24px;height:24px;border-radius:50%;background:#F5F5F5;font-size:12px;font-weight:600;color:#999;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s}
.rnum.on{background:#2E7D32;color:white}
.rtxt{font-size:15px;color:#1A1A1A;flex:1;line-height:1.4}
.rhdl{color:#CCC;font-size:18px;flex-shrink:0}

/* BUTTONS */
.navbtns{display:flex;gap:10px;padding:4px 16px 16px}
.btn-back{padding:14px 20px;border:1.5px solid #E0E0E0;border-radius:8px;background:white;color:#666;font-size:15px;font-weight:500;transition:background .15s;flex-shrink:0}
.btn-back:hover{background:#F5F5F5}
.btn-next{flex:1;padding:14px 24px;border-radius:8px;background:#2E7D32;color:white;font-size:15px;font-weight:600;transition:background .15s}
.btn-next:hover{background:#1B5E20}
.btn-next:active{transform:scale(.99)}
.btn-next:disabled{background:#CCC;cursor:not-allowed;transform:none}

/* COMPLETE */
.done-wrap{padding:40px 16px;text-align:center;max-width:480px;margin:0 auto}
.done-check{width:72px;height:72px;border-radius:50%;background:#E8F5E9;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:#2E7D32;font-weight:700}
.done-title{font-size:22px;font-weight:700;margin-bottom:10px}
.done-sub{font-size:15px;color:#666;margin-bottom:24px;line-height:1.6}
.reward-box{background:#E8F5E9;border-radius:12px;padding:20px;margin-bottom:24px}
.rwd-num{font-size:28px;font-weight:700;color:#2E7D32}
.rwd-desc{font-size:14px;color:#2E7D32;margin-top:6px;line-height:1.5}
.done-contact{font-size:14px;color:#666;margin-bottom:8px}
.wa-link{display:block;color:#2E7D32;font-size:16px;font-weight:500;margin-bottom:20px;text-decoration:none}
.wa-link:hover{text-decoration:underline}
.btn-site{display:block;padding:14px 24px;border:1.5px solid #2E7D32;border-radius:8px;color:#2E7D32;font-size:15px;font-weight:600;text-align:center;background:white;text-decoration:none;transition:all .15s}
.btn-site:hover{background:#E8F5E9}

/* CALL BACK SCREENS */
.cb-wrap{padding:32px 16px;text-align:center;max-width:480px;margin:0 auto}
.cb-icon{width:72px;height:72px;border-radius:50%;background:#E3F2FD;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:34px}
.cb-title{font-size:20px;font-weight:700;color:#1A1A1A;margin-bottom:10px}
.cb-sub{font-size:15px;color:#666;margin-bottom:28px;line-height:1.6}
.btn-callyes{width:100%;padding:16px 24px;border-radius:8px;background:#2E7D32;color:white;font-size:16px;font-weight:600;border:none;cursor:pointer;transition:background .15s;margin-bottom:12px;display:block;font-family:inherit}
.btn-callyes:hover{background:#1B5E20}
.btn-callno{width:100%;padding:14px 24px;border-radius:8px;background:white;color:#666;font-size:15px;font-weight:500;border:1.5px solid #E0E0E0;cursor:pointer;transition:all .15s;display:block;font-family:inherit}
.btn-callno:hover{background:#F5F5F5}
.time-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:4px 0 16px}
.topt{padding:14px 12px;border:1.5px solid #E0E0E0;border-radius:8px;text-align:center;cursor:pointer;font-size:14px;font-weight:500;line-height:1.5;transition:all .15s;background:white;user-select:none;-webkit-tap-highlight-color:transparent}
.topt small{display:block;font-size:12px;color:#999;font-weight:400;margin-top:2px}
.topt.selected{border-color:#2E7D32;background:#E8F5E9;color:#2E7D32}
.topt.selected small{color:#388E3C}
.topt:active{transform:scale(.97)}
.time-lbl{font-size:14px;font-weight:600;color:#1A1A1A;margin-top:4px;margin-bottom:12px;display:block}
.phone-note{font-size:12px;color:#999;margin-top:-8px;margin-bottom:14px;display:block}

/* FOOTER */
.foot{text-align:center;padding:16px;font-size:12px;color:#999}

/* RESPONSIVE */
@media(min-width:481px){
    .banner,.card,.prog-wrap,.navbtns{max-width:520px;margin-left:auto;margin-right:auto}
    .card{box-shadow:0 2px 8px rgba(0,0,0,.1)}
    .prog-wrap,.navbtns{padding-left:0;padding-right:0}
}
@media(max-width:360px){
    .q-title{font-size:16px}
    .slbl{min-width:90px;font-size:13px}
    .spill{padding:5px 8px;font-size:11px}
}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" class="tb-img">
</div>

<!-- PROGRESS (hidden on email screen) -->
<div class="prog-wrap" id="prog-wrap" style="display:none">
    <div class="prog-bar" id="pbar"></div>
    <span class="step-lbl" id="slbl">Step 1 of 7</span>
</div>

<!-- ===== EMAIL SCREEN ===== -->
<div class="screen active" id="semail">
<div class="email-wrap">
    <div class="email-hero">
        <h2 class="email-hero-title">Help us improve our service</h2>
        <p class="email-hero-sub">Would you like to take a quick survey? <strong>Only 7 questions, takes ~1 minute.</strong> This helps us improve our system.</p>
        <div class="reward-pill">~1 minute · 7 questions</div>
    </div>
    <div class="email-card">
        <label class="email-label" for="inp-email">Email address <span style="color:#D32F2F">*</span></label>
        <input type="email" id="inp-email" class="email-inp" placeholder="yourname@email.com" autocomplete="email" inputmode="email">
        <label class="email-label" for="inp-name">Your name <span class="email-note">(optional)</span></label>
        <input type="text" id="inp-name" class="email-inp" placeholder="John Smith" autocomplete="name">
        <label class="email-label" for="inp-phone">Phone number <span class="email-note">(optional)</span></label>
        <input type="tel" id="inp-phone" class="email-inp" placeholder="+1 555 000 0000" autocomplete="tel" inputmode="tel">
        <span class="phone-note">For your free 2-minute discovery call</span>
        <button class="btn-start" onclick="startSurvey()">Start Survey →</button>
    </div>
    <p class="email-tiny">🔒 Your answers are confidential. No spam.</p>
</div>
</div>

<!-- ===== Q1 ===== -->
<div class="screen" id="sq1">
<div class="card">
    <div class="q-lbl">Question 1</div>
    <h2 class="q-title">What marketing channels are you currently spending money on?</h2>
    <p class="q-sub">Select all that apply</p>
    <div>
        <div class="opt" onclick="tog(this,'channels_used','google_reviews')"><span class="oi">⭐</span><span class="ot">Google Reviews (paid service)</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','google_seo')"><span class="oi">🔍</span><span class="ot">Google SEO (organic search)</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','google_ads')"><span class="oi">📢</span><span class="ot">Google Ads / SEM (pay-per-click)</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','google_pmax')"><span class="oi">📊</span><span class="ot">Google PMax (Performance Max)</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','google_maps_ads')"><span class="oi">📍</span><span class="ot">Google Maps Ads / Local Ads</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','fb_ig_ads')"><span class="oi">📱</span><span class="ot">Facebook / Instagram Ads</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','fb_marketplace')"><span class="oi">🛒</span><span class="ot">Facebook Marketplace</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','yelp')"><span class="oi">📝</span><span class="ot">Yelp</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','website')"><span class="oi">🌐</span><span class="ot">Website / Landing Page</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'channels_used','tiktok_youtube')"><span class="oi">🎬</span><span class="ot">TikTok / YouTube</span><span class="ocb"></span></div>
        <div class="opt" data-other="q1ot" onclick="tog(this,'channels_used','other')"><span class="oi">➕</span><span class="ot">Other</span><span class="ocb"></span></div>
        <input type="text" id="q1ot" class="other-inp" placeholder="Please specify..." maxlength="200">
    </div>
</div>
<div class="navbtns"><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q2 (CONDITIONAL: FB/IG) ===== -->
<div class="screen" id="sq2">
<div class="card">
    <div class="q-lbl">Question 2</div>
    <h2 class="q-title">Which Facebook / Instagram activities are you paying for?</h2>
    <p class="q-sub">Select all that apply</p>
    <div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','fb_ads')"><span class="oi">📢</span><span class="ot">Facebook Ads campaigns</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','ig_ads')"><span class="oi">📸</span><span class="ot">Instagram Ads</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','boosted_posts')"><span class="oi">🚀</span><span class="ot">Boosted Posts</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','marketplace')"><span class="oi">🛒</span><span class="ot">Marketplace listings</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','agency')"><span class="oi">🏢</span><span class="ot">Social media agency / freelancer</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','influencer')"><span class="oi">🎤</span><span class="ot">Influencer marketing</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'fb_ig_detail','organic')"><span class="oi">📝</span><span class="ot">Organic content creation (in-house)</span><span class="ocb"></span></div>
        <div class="opt" data-other="q2ot" onclick="tog(this,'fb_ig_detail','other')"><span class="oi">➕</span><span class="ot">Other</span><span class="ocb"></span></div>
        <input type="text" id="q2ot" class="other-inp" placeholder="Please specify..." maxlength="200">
    </div>
</div>
<div class="navbtns"><button class="btn-back" onclick="go('back')">← Back</button><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q3: MONTHLY SPEND ===== -->
<div class="screen" id="sq3">
<div class="card">
    <div class="q-lbl">Question 3</div>
    <h2 class="q-title">How much do you spend monthly on each channel?</h2>
    <p class="q-sub">Tap the range for each channel you use</p>
    <div id="q3rows"><!-- filled dynamically --></div>
</div>
<div class="navbtns"><button class="btn-back" onclick="go('back')">← Back</button><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q4: GOALS ===== -->
<div class="screen" id="sq4">
<div class="card">
    <div class="q-lbl">Question 4</div>
    <h2 class="q-title">Why are you spending on these channels? What's your main goal?</h2>
    <p class="q-sub">Pick your top 1-2 goals</p>
    <div>
        <div class="opt" onclick="tog(this,'main_goals','get_more_leads',2)"><span class="oi">💰</span><span class="ot">Get more leads &amp; phone calls</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'main_goals','increase_revenue',2)"><span class="oi">📈</span><span class="ot">Increase revenue / sales</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'main_goals','build_trust',2)"><span class="oi">🛡️</span><span class="ot">Build trust &amp; credibility</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'main_goals','beat_competitors',2)"><span class="oi">🏆</span><span class="ot">Beat competitors</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'main_goals','reduce_cpl',2)"><span class="oi">📉</span><span class="ot">Reduce cost per lead / lower marketing spend</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'main_goals','brand_awareness',2)"><span class="oi">📣</span><span class="ot">Brand awareness &amp; visibility</span><span class="ocb"></span></div>
    </div>
</div>
<div class="navbtns"><button class="btn-back" onclick="go('back')">← Back</button><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q5: TAP-TO-RANK ===== -->
<div class="screen" id="sq5">
<div class="card">
    <div class="q-lbl">Question 5</div>
    <h2 class="q-title">Where do most of your leads / customers come from today?</h2>
    <p class="q-sub">Tap to rank your top 3 lead sources (1 = most important)</p>
    <div>
        <div class="ritem" data-val="google_maps"    onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📍 Google Maps / Google Business Profile</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="google_search"  onclick="rank(this)"><span class="rnum"></span><span class="rtxt">🔍 Google Search (organic SEO)</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="google_ads"     onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📢 Google Ads (SEM / PMax)</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="fb_organic"     onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📱 Facebook / Instagram (organic)</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="fb_ads"         onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📱 Facebook / Instagram Ads</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="yelp"           onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📝 Yelp</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="website"        onclick="rank(this)"><span class="rnum"></span><span class="rtxt">🌐 Website (direct traffic)</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="referrals"      onclick="rank(this)"><span class="rnum"></span><span class="rtxt">🤝 Referrals / word of mouth</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="email"          onclick="rank(this)"><span class="rnum"></span><span class="rtxt">📧 Email marketing</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="tiktok_youtube" onclick="rank(this)"><span class="rnum"></span><span class="rtxt">🎬 TikTok / YouTube</span><span class="rhdl">⋮⋮</span></div>
        <div class="ritem" data-val="other"          onclick="rank(this)"><span class="rnum"></span><span class="rtxt">➕ Other</span><span class="rhdl">⋮⋮</span></div>
    </div>
</div>
<div class="navbtns"><button class="btn-back" onclick="go('back')">← Back</button><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q6: CHALLENGES ===== -->
<div class="screen" id="sq6">
<div class="card">
    <div class="q-lbl">Question 6</div>
    <h2 class="q-title">What's your biggest marketing challenge right now?</h2>
    <p class="q-sub">Pick 1-2 challenges</p>
    <div>
        <div class="opt" onclick="tog(this,'challenges','not_enough_reviews',2)"><span class="oi">⭐</span><span class="ot">Not enough reviews / low star rating</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','high_ad_cost',2)"><span class="oi">💸</span><span class="ot">High ad cost, low return on investment</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','low_maps_rank',2)"><span class="oi">📍</span><span class="ot">Not ranking well on Google Maps</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','dont_know',2)"><span class="oi">❓</span><span class="ot">Don't know what's actually working</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','competitors',2)"><span class="oi">🏆</span><span class="ot">Competitors outrank me everywhere</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','too_many_channels',2)"><span class="oi">😵</span><span class="ot">Managing too many channels is overwhelming</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'challenges','not_enough_leads',2)"><span class="oi">🚫</span><span class="ot">Not getting enough leads / phone calls</span><span class="ocb"></span></div>
        <div class="opt" data-other="q6ot" onclick="tog(this,'challenges','other',2)"><span class="oi">➕</span><span class="ot">Other</span><span class="ocb"></span></div>
        <input type="text" id="q6ot" class="other-inp" placeholder="Please specify..." maxlength="200">
    </div>
</div>
<div class="navbtns"><button class="btn-back" onclick="go('back')">← Back</button><button class="btn-next" onclick="go('next')">Continue →</button></div>
</div>

<!-- ===== Q7: EXPECTED RESULTS ===== -->
<div class="screen" id="sq7">
<div class="card">
    <div class="q-lbl">Question 7</div>
    <h2 class="q-title">What result do you expect from improving your Google reviews?</h2>
    <p class="q-sub">Select all that apply</p>
    <div>
        <div class="opt" onclick="tog(this,'expected_results','more_calls')"><span class="oi">📞</span><span class="ot">More phone calls &amp; inquiries</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'expected_results','higher_maps_rank')"><span class="oi">📍</span><span class="ot">Higher Google Maps ranking</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'expected_results','more_trust')"><span class="oi">🛡️</span><span class="ot">More trust from potential customers</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'expected_results','beat_competitors')"><span class="oi">🏆</span><span class="ot">Beat competitors' ratings</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'expected_results','more_traffic')"><span class="oi">🌐</span><span class="ot">More website traffic</span><span class="ocb"></span></div>
        <div class="opt" onclick="tog(this,'expected_results','higher_conversion')"><span class="oi">💰</span><span class="ot">Higher conversion rate / more sales</span><span class="ocb"></span></div>
    </div>
</div>
<div class="navbtns">
    <button class="btn-back" onclick="go('back')">← Back</button>
    <button class="btn-next" onclick="goCallBack()">Next →</button>
</div>
</div>

<!-- ===== CALL BACK: YES/NO ===== -->
<div class="screen" id="scallback">
<div class="cb-wrap">
    <div class="cb-icon">&#128222;</div>
    <h2 class="cb-title">One last thing...</h2>
    <p class="cb-sub">Would you like a <strong>FREE 2-minute discovery call</strong>?<br>We'll share personalized recommendations based on your answers.</p>
    <button class="btn-callyes" onclick="showCallTime()">&#128222; Yes, call me!</button>
    <button class="btn-callno" id="decline-btn" onclick="declineCall()">No thanks, I'm good &#8594;</button>
</div>
</div>

<!-- ===== CALL BACK: TIME PICKER ===== -->
<div class="screen" id="scalltime">
<div class="card">
    <div class="q-lbl">Almost done!</div>
    <h2 class="q-title">When's the best time to call?</h2>
    <p class="q-sub">We'll reach out within 1 business day</p>
    <label class="email-label" for="inp-callphone">Your phone number</label>
    <input type="tel" id="inp-callphone" class="email-inp" placeholder="+1 555 000 0000" inputmode="tel" autocomplete="tel">
    <span class="time-lbl">Best time to call:</span>
    <div class="time-grid">
        <div class="topt" data-val="morning" onclick="selTime(this)">&#127749; Morning<small>9am – 12pm</small></div>
        <div class="topt" data-val="afternoon" onclick="selTime(this)">&#9728;&#65039; Afternoon<small>12pm – 3pm</small></div>
        <div class="topt" data-val="late_pm" onclick="selTime(this)">&#127751; Late PM<small>3pm – 6pm</small></div>
        <div class="topt" data-val="flexible" onclick="selTime(this)">&#128336; Flexible<small>Anytime</small></div>
    </div>
</div>
<div class="navbtns">
    <button class="btn-back" onclick="showCallBack()">&#8592; Back</button>
    <button class="btn-next" id="confirm-btn" onclick="confirmCall()">Confirm &#8594;</button>
</div>
</div>

<!-- ===== COMPLETE ===== -->
<div class="screen" id="sdone">
<div class="done-wrap">
    <div class="done-check">✓</div>
    <h2 class="done-title">Thank you! Survey complete.</h2>
    <p class="done-sub">Your answers help us improve our system and serve you better.</p>
    <p class="done-contact">Questions? WhatsApp us:</p>
    <a href="https://wa.me/<?php echo $SB_WA_NUMBER; ?>" class="wa-link">📞 <?php echo $SB_WA_DISPLAY; ?></a>
    <a href="https://smart-buzzer.com/promo/" class="btn-site">Visit our website →</a>
</div>
</div>

<div class="foot">🔒 Your answers are confidential | smart-buzzer.com</div>

<script>
// ── STATE ──────────────────────────────────────────────────
const S = {
    q: 0, skipQ2: false, t0: Date.now(), callTime: '',
    a: {
        email:'', name:'', phone:'',
        channels_used:[], channels_other:'',
        fb_ig_detail:[], fb_ig_other:'',
        monthly_spend:{},
        main_goals:[],
        lead_sources:{},
        challenges:[], challenges_other:'',
        expected_results:[]
    }
};

const CHAN = {
    google_reviews:'Google Reviews', google_seo:'Google SEO',
    google_ads:'Google Ads / SEM', google_pmax:'Google PMax',
    google_maps_ads:'Google Maps Ads', fb_ig_ads:'Facebook / IG Ads',
    fb_marketplace:'FB Marketplace', yelp:'Yelp',
    website:'Website', tiktok_youtube:'TikTok / YouTube', other:'Other'
};
const SPEND = ['$0','$1 - $500','$500 - $1.5K','$1.5K - $5K','$5K+'];
const CHK = '<svg width="12" height="10" viewBox="0 0 12 10" fill="none"><polyline points="1,5 4.5,8.5 11,1.5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// ── EMAIL SCREEN HANDLER ────────────────────────────────────
function startSurvey() {
    const email = document.getElementById('inp-email').value.trim();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        toast('Please enter a valid email address');
        document.getElementById('inp-email').focus();
        return;
    }
    S.a.email = email;
    S.a.name  = document.getElementById('inp-name').value.trim();
    S.a.phone = document.getElementById('inp-phone').value.trim();
    // Pre-fill call screen phone if provided
    if (S.a.phone) {
        const cp = document.getElementById('inp-callphone');
        if (cp) cp.value = S.a.phone;
    }
    show(1);
}

// ── NAVIGATE ───────────────────────────────────────────────
function go(dir) {
    if (dir === 'next') {
        if (!validate(S.q)) return;
        saveText(S.q);
        let nxt = S.q + 1;
        if (nxt === 2 && S.skipQ2) nxt = 3;
        if (nxt === 3) buildQ3();
        if (nxt > 7) return;
        show(nxt);
    } else {
        let prv = S.q - 1;
        if (prv === 2 && S.skipQ2) prv = 1;
        if (prv < 1) { show(0); return; }
        show(prv);
    }
}

function show(q) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    const el = document.getElementById(q === 0 ? 'semail' : 'sq'+q);
    if (el) el.classList.add('active');
    S.q = q;
    const inSurvey = q > 0;
    document.getElementById('prog-wrap').style.display = inSurvey ? '' : 'none';
    if (inSurvey) updProg();
    window.scrollTo({top:0,behavior:'smooth'});
}

function showSpecial(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.getElementById('prog-wrap').style.display = 'none';
    window.scrollTo({top:0,behavior:'smooth'});
}

const Q_NAMES = {1:'Channels',2:'FB / IG',3:'Monthly Spend',4:'Your Goals',5:'Lead Sources',6:'Challenges',7:'Expected Results'};

function updProg() {
    const total = S.skipQ2 ? 6 : 7;
    const MAP   = S.skipQ2 ? {1:1,3:2,4:3,5:4,6:5,7:6} : {1:1,2:2,3:3,4:4,5:5,6:6,7:7};
    const vis   = MAP[S.q] || 1;
    const bar   = document.getElementById('pbar');
    while (bar.children.length < total) { const d=document.createElement('div');d.className='pseg';bar.appendChild(d); }
    while (bar.children.length > total) bar.removeChild(bar.lastChild);
    Array.from(bar.children).forEach((s,i) => s.className='pseg '+(i<vis-1?'done':i===vis-1?'cur':''));
    document.getElementById('slbl').textContent = vis+' of '+total+' · '+(Q_NAMES[S.q]||'');
}

// ── VALIDATE ───────────────────────────────────────────────
function validate(q) {
    const t = (msg) => { toast(msg); return false; };
    if (q===1) {
        if (!S.a.channels_used.length) return t('Please select at least 1 channel');
        S.skipQ2 = !S.a.channels_used.includes('fb_ig_ads') && !S.a.channels_used.includes('fb_marketplace');
        return true;
    }
    if (q===2 && !S.a.fb_ig_detail.length) return t('Please select at least 1 activity');
    if (q===4 && !S.a.main_goals.length) return t('Please select at least 1 goal');
    if (q===5 && !Object.keys(S.a.lead_sources).length) return t('Please rank at least 1 lead source');
    if (q===6 && !S.a.challenges.length) return t('Please select at least 1 challenge');
    if (q===7 && !S.a.expected_results.length) return t('Please select at least 1 expected result');
    return true;
}

function saveText(q) {
    const g = id => { const e=document.getElementById(id); return e?e.value.trim():''; };
    if (q===1) S.a.channels_other   = g('q1ot');
    if (q===2) S.a.fb_ig_other      = g('q2ot');
    if (q===6) S.a.challenges_other = g('q6ot');
}

// ── TOGGLE OPTION ──────────────────────────────────────────
function tog(el, key, val, max) {
    max = max||99;
    const sel = el.classList.contains('selected');
    const cb  = el.querySelector('.ocb');
    const oid = el.dataset.other;
    if (sel) {
        S.a[key] = S.a[key].filter(v=>v!==val);
        el.classList.remove('selected'); cb.innerHTML='';
        if (oid) { const i=document.getElementById(oid); if(i) i.style.display='none'; }
    } else {
        if (S.a[key].length>=max) { toast('Please select up to '+max+' options'); return; }
        S.a[key].push(val);
        el.classList.add('selected'); cb.innerHTML=CHK;
        if (oid) { const i=document.getElementById(oid); if(i){i.style.display='block';i.focus();} }
    }
}

// ── Q3: BUILD SPEND ROWS ───────────────────────────────────
function buildQ3() {
    const c = document.getElementById('q3rows');
    const prev = Object.assign({}, S.a.monthly_spend);
    c.innerHTML = ''; S.a.monthly_spend = {};
    S.a.channels_used.forEach(ch => {
        const lbl = CHAN[ch]||ch;
        const saved = prev[lbl]||'$0';
        const row = document.createElement('div');
        row.className = 'srow';
        row.innerHTML = '<span class="slbl">'+lbl+'</span><div class="spills">'+
            SPEND.map(o=>'<button class="spill'+(o===saved?' selected':'')+'" onclick="selSpend(this,\''+lbl+'\',\''+o.replace(/'/g,"\\'")+'\')" >'+o+'</button>').join('')+
        '</div>';
        c.appendChild(row);
        S.a.monthly_spend[lbl] = saved;
    });
}

function selSpend(el, ch, val) {
    el.closest('.srow').querySelectorAll('.spill').forEach(p=>p.classList.remove('selected'));
    el.classList.add('selected');
    S.a.monthly_spend[ch] = val;
}

// ── Q5: TAP-TO-RANK ────────────────────────────────────────
function rank(el) {
    const val = el.dataset.val;
    const R   = S.a.lead_sources;
    const ex  = Object.keys(R).find(k=>R[k]===val);
    if (ex) {
        const n = parseInt(ex); delete R[ex];
        const sorted = Object.keys(R).map(Number).sort((a,b)=>a-b);
        S.a.lead_sources = {};
        sorted.forEach((k,i)=>{ S.a.lead_sources[i+1]=R[k]; });
    } else {
        const cnt = Object.keys(R).length;
        if (cnt>=3) { toast('You can only rank 3 sources. Tap a ranked item to remove it first.'); return; }
        S.a.lead_sources[cnt+1] = val;
    }
    refreshRank();
}

function refreshRank() {
    const R = S.a.lead_sources;
    document.querySelectorAll('.ritem').forEach(el => {
        const v = el.dataset.val;
        const r = Object.keys(R).find(k=>R[k]===v);
        const n = el.querySelector('.rnum');
        if (r) { n.textContent=r; n.className='rnum on'; el.classList.add('ranked'); }
        else   { n.textContent=''; n.className='rnum';   el.classList.remove('ranked'); }
    });
}

// ── CALL BACK FLOW ─────────────────────────────────────────
function goCallBack() {
    if (!validate(7)) return;
    saveText(7);
    showSpecial('scallback');
}

function showCallTime() { showSpecial('scalltime'); }
function showCallBack() { showSpecial('scallback'); }

function selTime(el) {
    document.querySelectorAll('.topt').forEach(t => t.classList.remove('selected'));
    el.classList.add('selected');
    S.callTime = el.dataset.val;
}

function declineCall() {
    const btn = document.getElementById('decline-btn');
    if (btn) { btn.disabled=true; btn.textContent='Submitting...'; }
    doSubmit(false, '', '');
}

function confirmCall() {
    if (!S.callTime) { toast('Please select a preferred call time'); return; }
    const phone = document.getElementById('inp-callphone').value.trim();
    const btn = document.getElementById('confirm-btn');
    if (btn) { btn.disabled=true; btn.textContent='Submitting...'; }
    doSubmit(true, phone, S.callTime);
}

// ── SUBMIT ─────────────────────────────────────────────────
async function doSubmit(callRequested, callPhone, callTime) {
    try {
        const res = await fetch('submit.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({
                answers: S.a,
                time_spent_seconds: Math.round((Date.now()-S.t0)/1000),
                call_requested: callRequested,
                call_phone: callPhone || '',
                call_preferred_time: callTime || ''
            })
        });
        const data = await res.json();
        if (data.success) {
            showSpecial('sdone');
        } else {
            toast(data.error||'Something went wrong. Please try again.');
            const btn = document.querySelector('.screen.active button:disabled');
            if (btn) { btn.disabled=false; btn.textContent=callRequested?'Confirm →':'No thanks, I\'m good →'; }
        }
    } catch(e) {
        toast('Network error. Please check your connection.');
        const btn = document.querySelector('.screen.active button:disabled');
        if (btn) { btn.disabled=false; btn.textContent=callRequested?'Confirm →':'No thanks, I\'m good →'; }
    }
}

// ── TOAST ──────────────────────────────────────────────────
function toast(msg) {
    const old = document.getElementById('sb-t'); if(old) old.remove();
    const t = document.createElement('div'); t.id='sb-t'; t.textContent=msg;
    Object.assign(t.style,{position:'fixed',bottom:'80px',left:'50%',transform:'translateX(-50%)',
        background:'#1A1A1A',color:'white',padding:'12px 20px',borderRadius:'8px',
        fontSize:'14px',fontWeight:'500',zIndex:'9999',maxWidth:'calc(100% - 32px)',
        textAlign:'center',opacity:'0',transition:'opacity .2s',pointerEvents:'none',whiteSpace:'nowrap'});
    document.body.appendChild(t);
    setTimeout(()=>t.style.opacity='1',10);
    setTimeout(()=>{t.style.opacity='0';setTimeout(()=>t.remove(),200);},2800);
}

// ── INIT ───────────────────────────────────────────────────
const bar = document.getElementById('pbar');
for(let i=0;i<7;i++){const d=document.createElement('div');d.className='pseg '+(i===0?'cur':'');bar.appendChild(d);}

document.getElementById('inp-email').addEventListener('keydown', e => { if(e.key==='Enter') startSurvey(); });
document.getElementById('inp-name').addEventListener('keydown',  e => { if(e.key==='Enter') startSurvey(); });
document.getElementById('inp-phone').addEventListener('keydown', e => { if(e.key==='Enter') startSurvey(); });
</script>
</body>
</html>
