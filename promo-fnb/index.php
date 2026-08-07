<?php // WhatsApp number is HARDCODED for this LP on purpose — it deliberately does NOT
// read /wa-config.php, so /wa-config.json and the /analytics/ dashboard cannot
// override it. If the number changes, edit it right here.
$SB_WA_NUMBER  = "6285121563813";
$SB_WA_DISPLAY = "+62 851-2156-3813"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Buzzer — Google Reviews for Restaurants</title>
<meta name="description" content="Google reviews written around your actual dishes, specials and atmosphere. Real people, aged accounts, gradual posting, 7-day replacement per review. Trusted by 1,200+ campaigns.">
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
<!-- End Google Tag Manager -->

<!-- Meta Pixel: managed via GTM (do NOT add direct fbq init here) -->

<!-- TikTok Pixel Code Start -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(
var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script")
;n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
  ttq.load('D25JHKBC77UF6R3NPOGG');
  ttq.page();
}(window, document, 'ttq');
</script>
<!-- TikTok Pixel Code End -->

<!-- Analytics Tracking Variables -->
<script>
let sessionId = null;
let pageLoadTime = Date.now();
let scrollDepths = {25: false, 50: false, 75: false, 100: false};
let returnVisitor = false;

function safeLocalStorage(action, key, value) {
    try {
        if (action === 'get') return localStorage.getItem(key);
        if (action === 'set') return localStorage.setItem(key, value);
        return null;
    } catch (e) {
        console.warn('localStorage not available:', e);
        return null;
    }
}

function getSessionId() {
    let sid = sessionStorage.getItem('sb_session_id');
    if (!sid) {
        sid = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('sb_session_id', sid);
    }
    return sid;
}

function checkReturnVisitor() {
    const visited = safeLocalStorage('get', 'sb_visited');
    if (visited) {
        returnVisitor = true;
        const count = parseInt(safeLocalStorage('get', 'sb_visit_count') || '0') + 1;
        safeLocalStorage('set', 'sb_visit_count', count.toString());
    } else {
        safeLocalStorage('set', 'sb_visited', 'true');
        safeLocalStorage('set', 'sb_visit_count', '1');
    }
}

sessionId = getSessionId();
checkReturnVisitor();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital,wght@0,400;1,400&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}

:root{
  --red:#D42B2B;--red-dark:#B22222;--red-light:#FDE8E8;--red-glow:rgba(212,43,43,.15);
  --white:#FFFFFF;--off-white:#F7F5F2;--cream:#FFF9F0;
  --text:#1A1A1A;--text-mid:#4A4A4A;--text-light:#888;
  --chrome:#E2E2E2;--chrome-dark:#C8C8C8;
  --star:#F5B731;--success:#2D8A4E;
  --ticket:#FFFDF7;--ticket-border:#E8E0D0;
  /* Readable sans stack replacing the old Caveat script face */
  --sans-hand:Arial,Helvetica,'Helvetica Neue','Segoe UI',sans-serif;
}

html{scroll-behavior:smooth}
body{font-family:'Inter',Arial,Helvetica,sans-serif;background:var(--white);color:var(--text);-webkit-font-smoothing:antialiased}
.container{max-width:1080px;margin:0 auto;padding:0 24px}
.handwritten{font-family:var(--sans-hand);font-weight:700}

/* ── DIVIDERS ── */
.checker-divider{height:18px;background-image:repeating-conic-gradient(#1A1A1A 0% 25%,#fff 0% 50%);background-size:18px 18px}

/* ── NAV ── */
nav{
  position:sticky;top:0;z-index:100;
  background:rgba(255,255,255,.92);backdrop-filter:blur(14px);
  border-bottom:3px solid var(--red);padding:12px 0;
}
nav .container{display:flex;align-items:center;justify-content:space-between}
.logo{font-family:'DM Serif Display',serif;font-size:24px;letter-spacing:0.5px;color:var(--text);text-decoration:none}
.logo span{color:var(--red)}
.nav-links{display:flex;gap:24px;align-items:center}
.nav-links a{font-size:13px;font-weight:600;color:var(--text-mid);text-decoration:none;transition:color .2s}
.nav-links a:hover{color:var(--text)}
.nav-cta{background:var(--red)!important;color:#fff!important;padding:8px 22px;border-radius:4px;font-weight:700!important;transition:background .2s!important}
.nav-cta:hover{background:var(--red-dark)!important}
@media(max-width:600px){.nav-links a:not(.nav-cta){display:none}}

/* ── HERO ── */
.hero{
  padding:80px 0 60px;text-align:center;
  background:
    url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5 Q32 15 30 25 Q28 15 30 5z' fill='%23f0e8da' opacity='.35'/%3E%3Ccircle cx='50' cy='45' r='4' fill='none' stroke='%23f0e8da' stroke-width='1' opacity='.3'/%3E%3Ccircle cx='10' cy='50' r='2.5' fill='%23f0e8da' opacity='.25'/%3E%3C/svg%3E")
    var(--white);
}
.hero-open{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--red);color:#fff;border-radius:4px;padding:8px 24px;
  font-family:'Inter',Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:2.5px;
  text-transform:uppercase;margin-bottom:28px;
}
.hero-open .dot{
  width:8px;height:8px;background:#4ADE80;border-radius:50%;
  box-shadow:0 0 8px rgba(74,222,128,.6);animation:pulse 2s infinite;
}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

.hero h1{
  font-family:'DM Serif Display',serif;
  font-size:clamp(42px,8.5vw,82px);line-height:1.05;letter-spacing:-0.5px;margin-bottom:14px;
}
.hero h1 .red{color:var(--red)}
/* Was Caveat script — now Arial for legibility */
.hero-handwritten{
  font-family:var(--sans-hand);
  font-size:clamp(17px,2.9vw,22px);line-height:1.35;
  color:var(--red);font-weight:700;letter-spacing:-0.1px;
  margin-bottom:18px;display:block;
}
.hero-sub{
  font-size:17px;color:var(--text-mid);max-width:520px;
  margin:0 auto 36px;line-height:1.65;
}
.hero-cta{
  display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;
  padding:16px 40px;border-radius:4px;font-family:'Inter',Arial,sans-serif;
  font-size:15px;font-weight:800;letter-spacing:2px;text-transform:uppercase;
  text-decoration:none;box-shadow:0 4px 16px var(--red-glow);transition:all .2s;
}
.hero-cta:hover{background:var(--red-dark);transform:translateY(-2px)}
.hero-cta-note{
  font-family:var(--sans-hand);font-size:14px;font-weight:600;
  color:var(--text-light);margin-top:12px;
}

.hero-proof{
  display:flex;justify-content:center;gap:32px;margin-top:48px;flex-wrap:wrap;
}
.proof-item{
  background:var(--white);border:2px solid var(--chrome);border-radius:8px;
  padding:16px 24px;text-align:center;min-width:140px;
  box-shadow:0 2px 8px rgba(0,0,0,.04);
}
.proof-num{
  font-family:'DM Serif Display',serif;font-size:38px;color:var(--red);
  letter-spacing:0;line-height:1;
}
.proof-label{font-family:'Inter',Arial,sans-serif;font-size:11px;color:var(--text-light);letter-spacing:1.5px;text-transform:uppercase;font-weight:600;margin-top:4px}

/* ── CUISINE ── */
.cuisine-section{padding:72px 0;background:var(--off-white)}
.stag{font-family:'Inter',Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:3px;color:var(--red);text-transform:uppercase;margin-bottom:10px;text-align:center}
.stitle{font-family:'DM Serif Display',serif;font-size:clamp(30px,5vw,48px);letter-spacing:-0.5px;color:var(--text);line-height:1.1;text-align:center;margin-bottom:10px}
/* Was Caveat script — now Arial */
.shand{font-family:var(--sans-hand);font-size:16px;font-weight:600;color:var(--text-mid);text-align:center;margin-bottom:32px;line-height:1.5}
.ssub{font-size:15px;color:var(--text-mid);max-width:480px;margin:0 auto 36px;text-align:center;line-height:1.6}

/* ── PRICING CARDS (3-up) ── */
.pricing-section{padding:72px 0;background:var(--off-white)}

.pk-cards{
  display:grid;grid-template-columns:repeat(3,1fr);gap:18px;
  max-width:1000px;margin:40px auto 0;align-items:start;
}
.pk-card{
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:5px 5px 0 var(--text);padding:26px 22px 24px;
  display:flex;flex-direction:column;position:relative;
}
.pk-card.popular{
  border-color:var(--red);box-shadow:5px 5px 0 var(--red);
}
.pk-badge{
  position:absolute;top:-13px;left:50%;transform:translateX(-50%);
  background:var(--red);color:#fff;padding:4px 16px;border-radius:3px;
  font-family:'Inter',Arial,sans-serif;font-size:10px;font-weight:800;letter-spacing:2px;
  white-space:nowrap;
}
.pk-name{
  font-family:'DM Serif Display',serif;font-size:27px;color:var(--text);
  line-height:1.15;margin-bottom:3px;
}
.pk-reviews{
  font-size:15px;font-weight:700;color:var(--red);letter-spacing:0;margin-bottom:14px;
}
.pk-price{
  font-family:'DM Serif Display',serif;font-size:52px;color:var(--red);
  line-height:1;display:flex;align-items:baseline;gap:9px;flex-wrap:wrap;
}
.pk-was{
  font-family:'Inter',Arial,sans-serif;font-size:19px;font-weight:600;
  color:var(--text-light);text-decoration:line-through;
}
.pk-per{
  font-family:'Inter',Arial,sans-serif;font-size:12.5px;color:var(--text-mid);
  letter-spacing:0;margin-top:7px;
}
/* Savings pulled out of the grey line into a badge people actually see */
.pk-save{
  display:inline-block;margin-left:8px;
  background:#DCFCE7;color:#166534;
  padding:3px 9px;border-radius:3px;
  font-size:11.5px;font-weight:800;letter-spacing:.4px;vertical-align:middle;
}
.pk-note{
  font-size:13.5px;color:var(--text-mid);line-height:1.6;
  margin:12px 0 16px;padding-bottom:16px;border-bottom:2px dashed var(--chrome);
}
.pk-feats{list-style:none;display:flex;flex-direction:column;gap:9px;margin-bottom:20px;flex:1}
.pk-feats li{display:flex;gap:9px;align-items:flex-start;font-size:13.5px;color:var(--text-mid);line-height:1.5}
.pk-feats .pkc{
  width:18px;height:18px;flex-shrink:0;border-radius:50%;
  background:var(--red-light);color:var(--red);
  display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:800;margin-top:1px;
}
.pk-btn{
  display:block;width:100%;text-align:center;text-decoration:none;
  padding:14px 18px;border-radius:4px;
  font-family:'Inter',Arial,sans-serif;font-size:14px;font-weight:800;letter-spacing:1.5px;
  text-transform:uppercase;cursor:pointer;transition:all .2s;
  background:transparent;color:var(--text);border:2px solid var(--text);
}
.pk-btn:hover{background:var(--text);color:#fff}
.pk-card.popular .pk-btn{background:var(--red);color:#fff;border-color:var(--red)}
.pk-card.popular .pk-btn:hover{background:var(--red-dark);border-color:var(--red-dark)}
/* Payment + reassurance strip under the cards */
.pk-trust{
  max-width:1000px;margin:28px auto 0;
  display:flex;align-items:center;justify-content:center;
  gap:16px;flex-wrap:wrap;
  font-size:13.5px;color:var(--text-mid);
}
.pk-cards-accepted{display:flex;align-items:center;gap:8px}
.pk-brand{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--white);border:2px solid var(--chrome);border-radius:4px;
  padding:6px 11px;font-size:12px;font-weight:800;color:var(--text);
  letter-spacing:.4px;line-height:1;
}
.pk-visa{color:#1A1F71;font-style:italic;letter-spacing:1px}
.pk-mc i{
  width:14px;height:14px;border-radius:50%;display:inline-block;
}
.pk-mc i:first-child{background:#EB001B}
.pk-mc i:nth-child(2){background:#F79E1B;margin-left:-7px;opacity:.9}
.pk-trust-item{display:inline-flex;align-items:center;gap:6px;font-weight:600}
.pk-trust-sep{width:1px;height:18px;background:var(--chrome-dark)}
.pk-trust-ask{
  display:inline-flex;align-items:center;gap:7px;flex-wrap:wrap;
  background:none;border:none;cursor:pointer;padding:0;
  font-family:'Inter',Arial,sans-serif;font-size:13.5px;color:var(--text-mid);
}
.pk-trust-link{
  color:var(--red);font-weight:700;
  border-bottom:2px solid var(--red-light);transition:border-color .2s;
}
.pk-trust-ask:hover .pk-trust-link{border-color:var(--red)}

/* Onboarding modal — what happens after you order */
.ob-modal{
  display:none;position:fixed;inset:0;z-index:2100;
  background:rgba(0,0,0,.72);padding:24px;
  align-items:center;justify-content:center;
}
.ob-modal.active{display:flex}
.ob-box{
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:7px 7px 0 var(--red);
  max-width:540px;width:100%;max-height:88vh;overflow-y:auto;
}
.ob-head{
  background:var(--red);color:#fff;padding:15px 20px;
  border-bottom:3px solid var(--text);
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  position:sticky;top:0;
}
.ob-head h3{font-family:'DM Serif Display',serif;font-size:21px;line-height:1.2}
.ob-close{
  background:none;border:none;color:#fff;font-size:26px;line-height:1;
  cursor:pointer;padding:0 4px;opacity:.85;
}
.ob-close:hover{opacity:1}
.ob-body{padding:22px 20px 24px}
.ob-body p{font-size:14.5px;color:var(--text-mid);line-height:1.7;margin-bottom:16px}
.ob-body p strong{color:var(--text)}
.ob-body img{
  width:100%;display:block;border:2px solid var(--chrome);border-radius:5px;
}
@media(max-width:760px){
  .pk-trust{gap:12px;font-size:13px;flex-direction:column}
  .pk-trust-sep{display:none}
  .pk-trust-ask{justify-content:center;text-align:center}
}
@media(max-width:900px){
  .pk-cards{grid-template-columns:1fr;max-width:440px;gap:26px}
  .pk-card{box-shadow:4px 4px 0 var(--text)}
  .pk-card.popular{box-shadow:4px 4px 0 var(--red)}
}

/* ── HOW ── */
.how-section{padding:72px 0;background:var(--off-white)}
.how-steps{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:0;max-width:820px;margin:48px auto 0;position:relative;
}
.how-steps::before{
  content:'';position:absolute;top:28px;left:14%;right:14%;height:4px;
  background:repeating-linear-gradient(90deg,var(--red) 0 10px,transparent 10px 20px);
}
.how-step{text-align:center;position:relative;z-index:1}
.how-num{
  width:56px;height:56px;border-radius:50%;
  background:var(--red);color:#fff;
  display:inline-flex;align-items:center;justify-content:center;
  font-family:'DM Serif Display',serif;font-size:22px;margin-bottom:14px;
  box-shadow:0 4px 12px var(--red-glow);
}
.how-step-title{font-family:'DM Serif Display',serif;font-size:18px;letter-spacing:0;color:var(--text);margin-bottom:3px}
/* Was Caveat script — now Arial */
.how-step-hand{font-family:var(--sans-hand);font-size:13px;font-weight:600;color:var(--red);margin-bottom:5px}
.how-step-desc{font-size:12px;color:var(--text-light);line-height:1.5;padding:0 8px}
@media(max-width:760px){
  .how-steps{grid-template-columns:1fr;gap:28px}
  .how-steps::before{display:none}
}

/* ── TRUST ── */
.trust-section{padding:72px 0;background:var(--white)}
.trust-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
  gap:14px;max-width:840px;margin:40px auto 0;
}
.trust-card{
  background:var(--white);border:2px solid var(--chrome);border-radius:8px;
  padding:22px;transition:border-color .2s;
}
.trust-card:hover{border-color:var(--red)}
.trust-icon{
  width:42px;height:42px;border-radius:8px;margin-bottom:14px;
  background:var(--red-light);color:var(--red);
  display:flex;align-items:center;justify-content:center;
}
.trust-icon svg{width:22px;height:22px;stroke:currentColor;fill:none;
  stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.trust-card h4{font-family:'DM Serif Display',serif;font-size:17px;letter-spacing:0;color:var(--text);margin-bottom:6px}
.trust-card p{font-size:13px;color:var(--text-mid);line-height:1.55}

/* ── ORDER FORM (diner ticket style) ── */
.order-form-section{padding:72px 0;background:var(--white)}
.order-form-wrapper{
  max-width:560px;margin:32px auto 0;
  background:var(--white);border:3px solid var(--text);border-radius:4px;
  box-shadow:4px 4px 0 var(--text);overflow:hidden;
}
.order-form-head{
  background:var(--red);color:#fff;padding:16px 24px;text-align:center;
  border-bottom:3px solid var(--text);
}
.order-form-head h3{font-family:'DM Serif Display',serif;font-size:26px;letter-spacing:.5px}
.order-form-countdown{
  font-family:var(--sans-hand);font-size:13px;font-weight:700;letter-spacing:.5px;
  margin-top:4px;opacity:.95;
}
.order-form-countdown span{
  background:rgba(255,255,255,.22);padding:1px 6px;border-radius:3px;
  font-variant-numeric:tabular-nums;
}
.order-form-body{padding:26px 24px}
.of-group{margin-bottom:16px}
.of-label{
  display:block;font-family:'Inter',Arial,sans-serif;font-size:12px;font-weight:700;
  letter-spacing:1px;text-transform:uppercase;color:var(--text);margin-bottom:6px;
}
.of-req{color:var(--red);margin-left:2px}
.of-input{
  width:100%;padding:13px 14px;border:2px solid var(--chrome);border-radius:4px;
  font-family:'Inter',Arial,sans-serif;font-size:15px;color:var(--text);outline:none;
  background:var(--white);transition:border-color .2s;
}
.of-input:focus{border-color:var(--red)}
.of-input::placeholder{color:var(--text-light)}
.of-package-label{
  display:block;font-family:'Inter',Arial,sans-serif;font-size:12px;font-weight:700;
  letter-spacing:1px;text-transform:uppercase;color:var(--text);margin:22px 0 8px;
}
.of-packages{display:flex;flex-direction:column;gap:8px;margin-bottom:18px}
.of-pkg{
  display:flex;align-items:center;gap:12px;
  border:2px solid var(--chrome);border-radius:4px;padding:12px 14px;
  cursor:pointer;transition:all .2s;background:var(--white);
}
.of-pkg:hover{border-color:var(--chrome-dark)}
.of-pkg.selected{border-color:var(--red);background:var(--red-light)}
.of-pkg-radio{accent-color:var(--red);width:17px;height:17px;flex-shrink:0;cursor:pointer}
.of-pkg-info{flex:1;display:flex;flex-direction:column;gap:2px}
.of-pkg-name{font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.of-pkg-detail{font-size:12px;color:var(--text-light)}
.of-pkg-detail s{opacity:.75}
.of-pkg-popular{
  background:var(--red);color:#fff;font-size:9px;font-weight:700;letter-spacing:1.2px;
  padding:2px 7px;border-radius:3px;
}
.of-pkg-price{font-family:'DM Serif Display',serif;font-size:24px;color:var(--red);line-height:1}
.of-notice{
  background:var(--cream);border:1px solid var(--ticket-border);border-radius:4px;
  padding:0;margin-bottom:18px;overflow:hidden;
}
.of-notice strong{color:var(--text)}
.ofn-urgent{
  background:var(--red-light);border-bottom:1px solid var(--ticket-border);
  padding:12px 14px;font-size:13.5px;color:var(--text-mid);line-height:1.55;
}
.ofn-urgent strong{color:var(--red)}
.ofn-fine{
  padding:12px 14px;font-size:12px;color:var(--text-light);line-height:1.55;
}
.ofn-fine strong{color:var(--text-mid)}
.of-submit{
  width:100%;background:var(--red);color:#fff;border:none;border-radius:4px;
  padding:17px 20px;font-family:'Inter',Arial,sans-serif;font-size:16px;font-weight:800;
  letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all .2s;
  box-shadow:0 4px 16px var(--red-glow);
}
.of-submit:hover{background:var(--red-dark);transform:translateY(-2px)}
.of-trust{
  display:flex;flex-wrap:wrap;justify-content:center;gap:16px;margin-top:16px;
  font-size:12px;color:var(--text-light);font-weight:600;
}

/* ── CTA ── */
.cta-section{
  padding:90px 0;text-align:center;
  background:var(--red);color:#fff;position:relative;overflow:hidden;
}
.cta-section::before{
  content:'';position:absolute;inset:0;
  background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.06) 1px,transparent 0);
  background-size:20px 20px;pointer-events:none;
}
.cta-section h2{
  font-family:'DM Serif Display',serif;font-size:clamp(36px,7vw,68px);
  letter-spacing:0;line-height:1.05;margin-bottom:8px;position:relative;z-index:1;
}
/* Was Caveat script — now Arial */
.cta-hand{
  font-family:var(--sans-hand);font-size:clamp(15px,2.4vw,19px);font-weight:700;
  letter-spacing:.3px;color:rgba(255,255,255,.92);margin-bottom:18px;position:relative;z-index:1;
}
.cta-sub{font-size:16px;color:rgba(255,255,255,.85);max-width:460px;margin:0 auto 36px;line-height:1.6;position:relative;z-index:1}
.cta-btn-white{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--white);color:var(--red);padding:16px 44px;border-radius:4px;
  font-family:'Inter',Arial,sans-serif;font-size:16px;font-weight:800;letter-spacing:2px;
  text-transform:uppercase;text-decoration:none;border:none;cursor:pointer;transition:all .2s;
  position:relative;z-index:1;box-shadow:0 4px 20px rgba(0,0,0,.15);
}
.cta-btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(0,0,0,.2)}
.cta-contacts{
  margin-top:28px;display:flex;justify-content:center;gap:24px;flex-wrap:wrap;
  position:relative;z-index:1;
}
.cta-contacts a{font-size:13px;color:rgba(255,255,255,.85);text-decoration:none;font-weight:600;display:flex;align-items:center;gap:6px;transition:color .2s}
.cta-contacts a:hover{color:#fff}

footer{
  padding:26px 0;border-top:3px solid var(--chrome);text-align:center;
  font-size:12px;color:var(--text-light);background:var(--white);
}
footer a{color:var(--text-light)}
.footer-links{display:flex;justify-content:center;gap:18px;flex-wrap:wrap;margin-bottom:10px}
.footer-links a{font-size:12px;font-weight:600;text-decoration:none}
.footer-links a:hover{color:var(--red)}

@media(prefers-reduced-motion:no-preference){
  .fade-up{opacity:0;transform:translateY(20px);transition:opacity .5s ease,transform .5s ease}
  .fade-up.visible{opacity:1;transform:translateY(0)}
}
@media(prefers-reduced-motion:reduce){.fade-up{opacity:1;transform:none}}

@media(max-width:640px){
  .menu-row{grid-template-columns:1fr;align-items:start}
  .menu-row-right{text-align:left;margin-top:10px}
  .menu-row-btn{width:100%;text-align:center}
  .order-form-body{padding:22px 18px}
}

/* ═══════════════════════════════════════════════════════════════
   FULL-LENGTH BUILD — proof, urgency & conversion sections
   ═══════════════════════════════════════════════════════════════ */

/* ── 00 · ANNOUNCEMENT BAR ── */
.announce-bar{background:var(--text);color:#fff;padding:9px 0}
.announce-bar-inner{
  max-width:1080px;margin:0 auto;padding:0 24px;
  display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;
}
.announce-bar-text{font-size:13px;font-weight:600;letter-spacing:.2px}
.announce-bar-timer{display:flex;gap:3px;align-items:center}
.abt-block{
  background:var(--red);padding:2px 7px;border-radius:3px;
  font-size:13px;font-weight:800;font-variant-numeric:tabular-nums;
}
.abt-sep{font-weight:800;opacity:.6}
.announce-bar-cta{
  background:var(--red);color:#fff;text-decoration:none;
  padding:5px 16px;border-radius:3px;font-size:11px;font-weight:800;letter-spacing:1.5px;
  transition:background .2s;
}
.announce-bar-cta:hover{background:var(--red-dark)}
@media(max-width:600px){
  .announce-bar-text{font-size:11px}
  .abt-block{font-size:11px;padding:2px 5px}
  .announce-bar-cta{font-size:10px;padding:4px 11px}
  .announce-bar-inner{gap:9px}
}

/* ── 01 · NAV LOGO ── */
.logo-img{display:block;height:38px;width:auto}
@media(max-width:600px){.logo-img{height:30px}}

/* ── 02 · HERO TWO-COLUMN ── */
.hero.hero-v2{text-align:left;padding:64px 0 56px}
.hero-grid{
  display:grid;grid-template-columns:1.05fr .95fr;
  gap:48px;align-items:center;
}
.hero-text-col{min-width:0}
.hero.hero-v2 .hero-open{margin-bottom:22px}
.hero.hero-v2 h1{font-size:clamp(38px,6.2vw,62px)}
.hero.hero-v2 .hero-handwritten{margin-bottom:16px}
.hero.hero-v2 .hero-sub{margin:0 0 22px;max-width:none}
.hero-checks{list-style:none;margin:0 0 26px;display:flex;flex-direction:column;gap:9px}
.hero-checks li{display:flex;align-items:flex-start;gap:9px;font-size:14.5px;color:var(--text-mid);line-height:1.5}
.hero-checks .hc{
  width:19px;height:19px;flex-shrink:0;border-radius:50%;
  background:var(--red-light);color:var(--red);
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:800;margin-top:1px;
}
.hero-btn-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.hero-cta-ghost{
  display:inline-flex;align-items:center;gap:8px;
  background:transparent;color:var(--text);border:2px solid var(--chrome-dark);
  padding:14px 30px;border-radius:4px;font-family:'Inter',Arial,sans-serif;
  font-size:14px;font-weight:800;letter-spacing:1.6px;text-transform:uppercase;
  text-decoration:none;transition:all .2s;
}
.hero-cta-ghost:hover{border-color:var(--red);color:var(--red)}
.hero-visual-col{position:relative;min-width:0}
.hero-frame{
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:6px 6px 0 var(--red);overflow:hidden;
}
.hero-frame-bar{
  background:var(--text);padding:8px 12px;display:flex;gap:6px;align-items:center;
}
.hero-frame-bar span{width:10px;height:10px;border-radius:50%;background:#585858;display:block}
.hero-frame-bar span:first-child{background:var(--red)}
.hero-frame img{width:100%;display:block}
.hero-float-badge{
  position:absolute;right:-14px;bottom:-20px;
  background:var(--white);border:2px solid var(--chrome);border-radius:8px;
  padding:11px 16px;display:flex;align-items:center;gap:11px;
  box-shadow:0 8px 24px rgba(0,0,0,.12);
}
.hero-float-badge .hfb-star{
  width:36px;height:36px;border-radius:50%;background:var(--star);color:#fff;
  display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;
}
.hero-float-badge .hfb-txt{display:flex;flex-direction:column;line-height:1.25}
.hero-float-badge .hfb-txt strong{font-size:15px;color:var(--text)}
.hero-float-badge .hfb-txt span{font-size:11px;color:var(--text-light)}
.hero.hero-v2 .hero-proof{justify-content:flex-start;gap:14px;margin-top:44px}

/* Mobile: image ALWAYS above text (CLAUDE.md rule) */
@media(max-width:880px){
  .hero.hero-v2{text-align:center;padding:44px 0 48px}
  .hero-grid{grid-template-columns:1fr;gap:34px}
  .hero-visual-col{order:1}
  .hero-text-col{order:2}
  .hero-checks{align-items:flex-start;text-align:left;max-width:390px;margin-left:auto;margin-right:auto}
  .hero-btn-row,.hero-live{justify-content:center}
  .hero.hero-v2 .hero-proof{justify-content:center}
  .hero-float-badge{right:10px;bottom:-18px}
}

/* ── 03 · TRUST BAR ── */
.trust-bar{background:var(--text);padding:15px 0}
.trust-bar-inner{
  display:flex;flex-wrap:wrap;justify-content:center;gap:10px 26px;
}
.tb-chip{
  display:flex;align-items:center;gap:8px;
  color:#fff;font-size:13px;font-weight:600;letter-spacing:.2px;
}
.tb-chip .tbi{color:var(--star);font-size:13px}
.tb-chip .tbdot{
  width:8px;height:8px;border-radius:50%;background:#4ADE80;
  box-shadow:0 0 8px rgba(74,222,128,.6);animation:pulse 2s infinite;
}
@media(max-width:600px){.tb-chip{font-size:11.5px}.trust-bar-inner{gap:8px 16px}}

/* ── SHARED · FRAMED IMAGE + SECTION PIECES ── */
.framed-shot{
  max-width:900px;margin:0 auto;
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:5px 5px 0 var(--text);overflow:hidden;
}
.framed-shot-bar{background:var(--text);padding:8px 12px;display:flex;gap:6px}
.framed-shot-bar span{width:10px;height:10px;border-radius:50%;background:#585858;display:block}
.framed-shot-bar span:first-child{background:var(--red)}
.framed-shot img{width:100%;display:block}
img[data-preview]{cursor:zoom-in;transition:opacity .2s}
img[data-preview]:hover{opacity:.92}

.sec-white{padding:72px 0;background:var(--white)}
.sec-off{padding:72px 0;background:var(--off-white)}
.sec-cream{padding:72px 0;background:var(--cream)}

/* Two-column image + text (image first on mobile) */
.split-flex{
  display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;
  max-width:940px;margin:36px auto 0;
}
.split-img{min-width:0}
.split-img img{
  width:100%;display:block;border:3px solid var(--text);border-radius:6px;
  box-shadow:5px 5px 0 var(--red);
}
.split-body{min-width:0}
.split-body p{font-size:15px;color:var(--text-mid);line-height:1.7;margin-bottom:18px}
.split-list{list-style:none;display:flex;flex-direction:column;gap:11px}
.split-list li{display:flex;gap:10px;align-items:flex-start;font-size:14.5px;color:var(--text-mid);line-height:1.55}
.split-list .sc{
  width:21px;height:21px;flex-shrink:0;border-radius:50%;
  background:var(--red);color:#fff;display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:800;margin-top:1px;
}
.split-list strong{color:var(--text);display:block;margin-bottom:1px}
@media(max-width:880px){
  .split-flex{grid-template-columns:1fr;gap:26px}
  .split-img{order:1}
  .split-body{order:2}
}

/* ── 05 · CUISINE SELECTOR (rich panel) ── */
.cuisine-pills{
  display:flex;flex-wrap:wrap;justify-content:center;gap:8px;
  max-width:840px;margin:0 auto 14px;
}
.cpill{
  display:inline-flex;align-items:center;gap:7px;
  background:var(--white);border:2px solid var(--chrome);border-radius:100px;
  padding:9px 17px;font-family:'Inter',Arial,sans-serif;
  font-size:13px;font-weight:700;color:var(--text-mid);
  cursor:pointer;transition:all .18s;white-space:nowrap;
}
.cpill .cemo{font-size:15px;line-height:1}
.cpill:hover{border-color:var(--chrome-dark);color:var(--text);transform:translateY(-1px)}
.cpill.active{background:var(--red);border-color:var(--red);color:#fff}
.cuisine-more-wrap{position:relative}
.cuisine-more-btn{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--white);border:2px dashed var(--chrome-dark);border-radius:100px;
  padding:9px 17px;font-family:'Inter',Arial,sans-serif;
  font-size:13px;font-weight:700;color:var(--text-mid);cursor:pointer;transition:all .18s;
}
.cuisine-more-btn:hover{border-color:var(--red);color:var(--red)}
.cuisine-more-list{
  display:none;position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);
  background:var(--white);border:2px solid var(--text);border-radius:6px;
  box-shadow:4px 4px 0 var(--text);padding:6px;z-index:60;min-width:190px;
}
.cuisine-more-list.show{display:block}
.cuisine-more-list button{
  display:flex;align-items:center;gap:8px;width:100%;
  background:none;border:none;text-align:left;cursor:pointer;
  padding:9px 12px;border-radius:4px;
  font-family:'Inter',Arial,sans-serif;font-size:13px;font-weight:600;color:var(--text);
}
.cuisine-more-list button:hover{background:var(--red-light);color:var(--red)}

.cuisine-panel{
  max-width:960px;margin:26px auto 0;
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:6px 6px 0 var(--red);overflow:hidden;
}
.cp-grid{display:grid;grid-template-columns:1fr 1.1fr}
.cp-left{border-right:3px solid var(--text);display:flex;flex-direction:column}
.cp-photo{position:relative;height:190px;overflow:hidden;border-bottom:3px solid var(--text)}
.cp-photo img{width:100%;height:100%;object-fit:cover;display:block}
.cp-photo::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(180deg,rgba(0,0,0,0) 45%,rgba(0,0,0,.55) 100%);
}
.cp-photo-tag{
  position:absolute;left:14px;bottom:12px;z-index:1;
  background:var(--red);color:#fff;padding:5px 13px;border-radius:3px;
  font-family:'Inter',Arial,sans-serif;font-size:10px;font-weight:800;letter-spacing:1.8px;
  text-transform:uppercase;
}
.cp-left-body{padding:22px 22px 24px;flex:1}
.cp-left h3{font-family:'DM Serif Display',serif;font-size:23px;color:var(--text);margin-bottom:7px;line-height:1.2}
.cp-sub{font-size:13.5px;color:var(--text-mid);line-height:1.6;margin-bottom:16px}
.cp-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;margin-bottom:16px}
.cp-stat{
  background:var(--off-white);border:1px solid var(--chrome);border-radius:5px;
  padding:10px 6px;text-align:center;
}
.cp-stat b{display:block;font-family:'DM Serif Display',serif;font-size:19px;color:var(--red);line-height:1.1}
.cp-stat span{display:block;font-size:9.5px;color:var(--text-light);line-height:1.3;margin-top:3px;letter-spacing:.2px}
.cp-checks{list-style:none;display:flex;flex-direction:column;gap:8px}
.cp-checks li{display:flex;gap:8px;align-items:flex-start;font-size:13px;color:var(--text-mid);line-height:1.45}
.cp-checks .cc{color:var(--success);font-weight:800;flex-shrink:0}

.cp-right{padding:24px 24px 26px;background:var(--ticket)}
.cp-right h4{
  font-family:'Inter',Arial,sans-serif;font-size:11px;font-weight:800;letter-spacing:2px;
  text-transform:uppercase;color:var(--red);margin-bottom:13px;
}
.cp-pains{list-style:none;display:flex;flex-direction:column;gap:9px;margin-bottom:20px}
.cp-pains li{display:flex;gap:9px;align-items:flex-start;font-size:13.5px;color:var(--text-mid);line-height:1.5}
.cp-pains .cx{color:var(--red);font-weight:800;flex-shrink:0;font-size:12px;margin-top:1px}
.cp-sample-label{
  display:block;font-family:'Inter',Arial,sans-serif;font-size:10px;font-weight:800;
  letter-spacing:1.8px;text-transform:uppercase;color:var(--text-light);margin-bottom:8px;
}
.cp-sample{
  background:var(--white);border:2px solid var(--ticket-border);border-radius:4px;
  padding:15px 16px;margin-bottom:18px;
}
.cp-sample-stars{color:var(--star);font-size:14px;letter-spacing:2px;margin-bottom:7px}
.cp-sample-text{font-size:13.5px;color:var(--text-mid);line-height:1.65;font-style:italic;margin-bottom:11px}
.cp-sample-by{display:flex;align-items:center;gap:9px}
.cp-sample-by .cpav{
  width:28px;height:28px;border-radius:50%;background-size:cover;background-position:center;
  flex-shrink:0;border:2px solid var(--chrome);
}
.cp-sample-by span{font-size:12px;font-weight:600;color:var(--text-light)}
.cp-owner{
  background:var(--red-light);border-left:4px solid var(--red);border-radius:0 5px 5px 0;
  padding:14px 15px;display:flex;gap:12px;align-items:flex-start;
}
.cp-owner .cpav-lg{
  width:40px;height:40px;border-radius:50%;background-size:cover;background-position:center;
  flex-shrink:0;border:2px solid var(--white);
}
.cp-owner-txt p{font-size:13px;color:var(--text-mid);line-height:1.55;font-style:italic;margin-bottom:5px}
.cp-owner-txt b{font-size:11.5px;color:var(--text);font-style:normal;display:block}
@media(max-width:880px){
  .cp-grid{grid-template-columns:1fr}
  .cp-left{border-right:none;border-bottom:3px solid var(--text)}
  .cp-photo{height:170px}
}

/* ── 08 · SHOW-UP GUARANTEE ── */
.gt-wrap{max-width:880px;margin:34px auto 0}
.gt-callout{
  background:var(--cream);border:2px solid var(--star);border-radius:8px;
  padding:22px 24px;margin-bottom:24px;text-align:center;
}
.gt-callout strong{
  display:block;font-family:'DM Serif Display',serif;font-size:21px;
  color:var(--text);margin-bottom:9px;line-height:1.3;
}
.gt-callout span{font-size:14.5px;color:var(--text-mid);line-height:1.7}
.gt-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px}
.gt-step{
  background:var(--white);border:2px solid var(--chrome);border-radius:8px;
  padding:22px 18px;text-align:center;
}
.gt-step .gtn{
  width:42px;height:42px;border-radius:50%;background:var(--red-light);color:var(--red);
  display:flex;align-items:center;justify-content:center;margin:0 auto 12px;
  font-family:'DM Serif Display',serif;font-size:19px;
}
.gt-step.ok .gtn{background:#DCFCE7;color:var(--success)}
.gt-step strong{display:block;font-size:14.5px;color:var(--text);margin-bottom:6px}
.gt-step.ok strong{color:var(--success)}
.gt-step span{font-size:13px;color:var(--text-mid);line-height:1.6}
.gt-summary{
  background:var(--white);border:2px solid var(--chrome);border-radius:8px;
  padding:20px 22px;font-size:14.5px;line-height:1.75;color:var(--text-mid);
}
.gt-summary p{margin-bottom:9px}
.gt-summary p:last-child{margin-bottom:0}
.gt-yes{color:var(--success);font-weight:700}
.gt-no{color:var(--text-light);font-weight:700}
@media(max-width:760px){.gt-steps{grid-template-columns:1fr}}

/* ── 15 · CASE STUDY (placeholder) ── */
.cs-card{
  max-width:900px;margin:34px auto 0;
  background:var(--white);border:3px solid var(--text);border-radius:6px;
  box-shadow:6px 6px 0 var(--text);overflow:hidden;
}
.cs-head{
  background:var(--text);color:#fff;padding:14px 24px;
  display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
}
.cs-head .csh-l{font-family:'DM Serif Display',serif;font-size:20px}
.cs-head .csh-r{
  background:var(--red);padding:4px 12px;border-radius:3px;
  font-family:'Inter',Arial,sans-serif;font-size:10px;font-weight:800;letter-spacing:1.6px;
}
.cs-img{border-bottom:3px solid var(--text)}
.cs-img img{width:100%;display:block}
.cs-metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:0}
.cs-metric{
  padding:20px 14px;text-align:center;border-right:1px solid var(--chrome);
}
.cs-metric:last-child{border-right:none}
.cs-metric b{display:block;font-family:'DM Serif Display',serif;font-size:23px;color:var(--red);line-height:1.15;white-space:nowrap}
.cs-metric span{display:block;font-size:10.5px;color:var(--text-light);letter-spacing:1px;text-transform:uppercase;margin-top:5px;font-weight:600}
.cs-note{
  background:var(--off-white);border-top:1px solid var(--chrome);
  padding:14px 24px;font-size:12.5px;color:var(--text-light);line-height:1.6;text-align:center;
}
@media(max-width:700px){
  .cs-metrics{grid-template-columns:1fr}
  .cs-metric{border-right:none;border-bottom:1px solid var(--chrome)}
  .cs-metric:last-child{border-bottom:none}
}

/* ── 20 · OWNER TESTIMONIALS ── */
.testi-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:16px;
  max-width:960px;margin:38px auto 0;
}
.testi-card-fnb{
  background:var(--ticket);border:2px solid var(--ticket-border);border-radius:6px;
  padding:22px 20px;display:flex;flex-direction:column;
}
.testi-card-fnb .tstars{color:var(--star);font-size:15px;letter-spacing:2px;margin-bottom:11px}
.testi-card-fnb p{font-size:14px;color:var(--text-mid);line-height:1.7;font-style:italic;flex:1;margin-bottom:16px}
.testi-by{display:flex;align-items:center;gap:11px;border-top:2px dashed var(--ticket-border);padding-top:13px}
.testi-by .tav{
  width:40px;height:40px;border-radius:50%;background-size:cover;background-position:center;
  flex-shrink:0;border:2px solid var(--white);box-shadow:0 1px 5px rgba(0,0,0,.1);
}
.testi-by .tmeta{display:flex;flex-direction:column;line-height:1.35}
.testi-by .tmeta strong{font-size:13.5px;color:var(--text)}
.testi-by .tmeta span{font-size:11.5px;color:var(--text-light)}
@media(max-width:860px){.testi-grid{grid-template-columns:1fr}}

/* ── 21 · CLIENT LOGOS ── */
.clients-grid{
  display:grid;grid-template-columns:repeat(4,1fr);gap:14px;
  max-width:900px;margin:36px auto 0;
}
.client-logo{
  background:var(--white);border:2px solid var(--chrome);border-radius:6px;
  padding:18px 14px;display:flex;align-items:center;justify-content:center;
  min-height:88px;transition:border-color .2s;
}
.client-logo:hover{border-color:var(--red)}
.client-logo img{max-width:100%;max-height:52px;width:auto;object-fit:contain;display:block}
@media(max-width:760px){.clients-grid{grid-template-columns:1fr 1fr;gap:10px}}

/* ── 22 · FAQ ── */
.faq-list{max-width:800px;margin:34px auto 0;display:flex;flex-direction:column;gap:10px}
.faq-item{
  background:var(--white);border:2px solid var(--chrome);border-radius:6px;overflow:hidden;
  transition:border-color .2s;
}
.faq-item.open{border-color:var(--red)}
.faq-q{
  width:100%;background:none;border:none;cursor:pointer;text-align:left;
  padding:17px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;
  font-family:'Inter',Arial,sans-serif;font-size:15px;font-weight:700;color:var(--text);
  line-height:1.45;
}
.faq-arrow{
  flex-shrink:0;color:var(--red);font-size:11px;transition:transform .25s;
}
.faq-item.open .faq-arrow{transform:rotate(180deg)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease}
.faq-item.open .faq-a{max-height:820px}
.faq-a-inner{
  padding:0 20px 18px;font-size:14px;color:var(--text-mid);line-height:1.75;
  border-top:1px dashed var(--chrome);padding-top:15px;margin:0 20px 18px;padding-left:0;padding-right:0;
}

/* ── FLOATING · WHATSAPP WIDGET ── */
.wa-float{position:fixed;right:22px;bottom:22px;z-index:900}
.wa-chat-box{
  position:absolute;right:0;bottom:74px;width:300px;
  background:var(--white);border:3px solid var(--text);border-radius:8px;
  box-shadow:5px 5px 0 var(--red);overflow:hidden;
  opacity:0;visibility:hidden;transform:translateY(10px);transition:all .22s;
}
.wa-chat-box.active{opacity:1;visibility:visible;transform:translateY(0)}
.wa-chat-head{background:#25D366;color:#fff;padding:13px 16px;display:flex;gap:11px;align-items:center}
.wa-chat-avatar{
  width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.25);
  display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;
}
.wa-chat-head h4{font-size:14px;font-weight:700;line-height:1.2}
.wa-chat-head p{font-size:11px;opacity:.9;margin-top:1px}
.wa-chat-body{padding:16px;background:var(--cream)}
.wa-bubble{
  background:var(--white);border-radius:2px 8px 8px 8px;padding:11px 13px;
  font-size:13px;color:var(--text-mid);line-height:1.55;box-shadow:0 1px 4px rgba(0,0,0,.07);
}
.wa-bubble small{display:block;margin-top:6px;font-size:10.5px;color:var(--text-light)}
.wa-chat-foot{padding:12px 16px;background:var(--white);border-top:1px solid var(--chrome)}
.wa-chat-foot a{
  display:block;background:#25D366;color:#fff;text-align:center;text-decoration:none;
  padding:11px;border-radius:4px;font-size:13px;font-weight:800;letter-spacing:1.2px;
  transition:background .2s;
}
.wa-chat-foot a:hover{background:#1EB855}
.wa-trigger{
  position:relative;width:58px;height:58px;border-radius:50%;border:none;cursor:pointer;
  background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;
  box-shadow:0 5px 18px rgba(37,211,102,.42);transition:transform .2s;
}
.wa-trigger:hover{transform:scale(1.06)}
.wa-trigger svg{width:29px;height:29px;fill:#fff;position:relative;z-index:1}
.wa-pulse{
  position:absolute;inset:0;border-radius:50%;background:#25D366;
  animation:waPulse 2.2s infinite;
}
@keyframes waPulse{0%{transform:scale(1);opacity:.55}70%{transform:scale(1.55);opacity:0}100%{opacity:0}}

/* ── FLOATING · SOCIAL PROOF TOAST ── */
.sp-toast{
  position:fixed;left:22px;bottom:22px;z-index:880;max-width:305px;
  background:var(--white);border:2px solid var(--text);border-radius:6px;
  box-shadow:4px 4px 0 var(--red);padding:13px 15px;
  display:flex;align-items:center;gap:12px;
  opacity:0;visibility:hidden;transform:translateY(14px);transition:all .35s;
}
.sp-toast.visible{opacity:1;visibility:visible;transform:translateY(0)}
.sp-toast-close{
  position:absolute;top:5px;right:8px;background:none;border:none;cursor:pointer;
  font-size:17px;line-height:1;color:var(--text-light);padding:2px;
}
.sp-toast-close:hover{color:var(--text)}
.sp-toast-avatar{
  width:40px;height:40px;border-radius:50%;color:#fff;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:16px;font-weight:800;position:relative;
}
.sp-verified{
  position:absolute;right:-2px;bottom:-2px;width:15px;height:15px;border-radius:50%;
  background:var(--success);border:2px solid var(--white);
}
.sp-toast-text{display:flex;flex-direction:column;line-height:1.35;min-width:0}
.sp-toast-text strong{font-size:13px;color:var(--text)}
.sp-action{font-size:12px;color:var(--text-mid)}
.sp-time{font-size:10.5px;color:var(--text-light);margin-top:2px}
@media(max-width:600px){.sp-toast{left:12px;right:12px;max-width:none;bottom:88px}}

/* ── FLOATING · STICKY BOTTOM CTA ── */
.sticky-cta{
  position:fixed;left:0;right:0;bottom:0;z-index:870;
  background:var(--text);border-top:3px solid var(--red);
  padding:10px 0;transform:translateY(110%);transition:transform .28s;
}
.sticky-cta.visible{transform:translateY(0)}
.sticky-cta-inner{
  max-width:1080px;margin:0 auto;padding:0 20px;
  display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;
}
.sticky-cta-text{color:#fff;font-size:13px;font-weight:600}
.sticky-cta-timer{display:flex;gap:6px}
.sct-block{
  background:rgba(255,255,255,.14);border-radius:4px;padding:4px 9px;text-align:center;min-width:42px;
}
.sct-num{display:block;color:#fff;font-size:15px;font-weight:800;font-variant-numeric:tabular-nums;line-height:1}
.sct-label{display:block;color:rgba(255,255,255,.6);font-size:8.5px;letter-spacing:1px;text-transform:uppercase;margin-top:2px}
.sticky-cta-btn{
  background:var(--red);color:#fff;text-decoration:none;
  padding:10px 26px;border-radius:4px;
  font-size:13px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;
  transition:background .2s;
}
.sticky-cta-btn:hover{background:var(--red-dark)}
@media(max-width:600px){
  .sticky-cta-text{display:none}
  .sticky-cta-inner{gap:10px}
  .sticky-cta-btn{padding:10px 18px;font-size:12px}
  .wa-float{bottom:74px;right:14px}
  .wa-trigger{width:50px;height:50px}
  .wa-trigger svg{width:25px;height:25px}
  .wa-chat-box{width:280px}
}

/* ── IMAGE MODAL ── */
.image-modal{
  display:none;position:fixed;inset:0;z-index:2000;
  background:rgba(0,0,0,.9);padding:40px 20px;
  align-items:center;justify-content:center;
}
.image-modal.active{display:flex}
.image-modal img{max-width:100%;max-height:90vh;display:block;border-radius:4px}
.modal-close{
  position:absolute;top:18px;right:26px;color:#fff;font-size:38px;line-height:1;
  cursor:pointer;font-weight:300;
}
.modal-close:hover{color:var(--red)}
</style>
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- 00 · ANNOUNCEMENT BAR -->
<div class="announce-bar">
  <div class="announce-bar-inner">
    <span class="announce-bar-text">&#9200; Limited offer ends in:</span>
    <div class="announce-bar-timer">
      <span class="abt-block" id="abtH">23</span><span class="abt-sep">:</span>
      <span class="abt-block" id="abtM">59</span><span class="abt-sep">:</span>
      <span class="abt-block" id="abtS">59</span>
    </div>
    <a href="#pricing" class="announce-bar-cta">ORDER NOW &rarr;</a>
  </div>
</div>

<!-- 01 · NAV -->
<nav>
  <div class="container">
    <a href="https://smart-buzzer.com/" class="logo">
      <img class="logo-img" src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
    </a>
    <div class="nav-links">
      <a href="#pricing">Menu</a>
      <a href="#how">How It Works</a>
      <a href="#reviews">Our Reviews</a>
      <a href="#order-form" class="nav-cta">Order Now</a>
    </div>
  </div>
</nav>

<!-- 02 · HERO -->
<section class="hero hero-v2">
  <div class="container">
    <div class="hero-grid">

      <div class="hero-text-col">
        <div class="hero-open"><span class="dot"></span> NOW SERVING &middot; 1,200+ RESTAURANTS</div>
        <h1><span class="red">Google Reviews</span><br>for Restaurants</h1>
        <span class="hero-handwritten">Written for your menu &mdash; served fresh daily</span>
        <p class="hero-sub">Custom reviews crafted around your dishes, your atmosphere, your cuisine &mdash; posted gradually by real people on aged accounts.</p>

        <ul class="hero-checks">
          <li><span class="hc">&#10003;</span> Real diners on aged local accounts &mdash; unique IPs and devices</li>
          <li><span class="hc">&#10003;</span> Reviews mention your actual dishes by name, not generic praise</li>
          <li><span class="hc">&#10003;</span> 7-day free replacement on every review that shows up</li>
        </ul>

        <div class="hero-btn-row">
          <a href="#cuisine" class="hero-cta">SEE MY CUISINE &darr;</a>
          <a href="#how" class="hero-cta-ghost">HOW IT WORKS</a>
        </div>
        <div class="hero-cta-note">First reviews live within 24 hours of your approval</div>

        <div class="hero-proof">
          <div class="proof-item">
            <div class="proof-num">1,200+</div>
            <div class="proof-label">Campaigns</div>
          </div>
          <div class="proof-item">
            <div style="font-size:28px;margin-bottom:4px">&#11088;&#11088;&#11088;&#11088;&#11088;</div>
            <div class="proof-label">80/20 Star Mix</div>
          </div>
        </div>
      </div>

      <div class="hero-visual-col">
        <div class="hero-frame">
          <div class="hero-frame-bar"><span></span><span></span><span></span></div>
          <img src="https://reputationmanage.co/wp-content/uploads/2025/06/buy-google-reviews.png" alt="Google Business Profile review dashboard" data-preview="true">
        </div>
        <div class="hero-float-badge">
          <div class="hfb-star">&#9733;</div>
          <div class="hfb-txt">
            <strong>4.9 / 5.0</strong>
            <span>Client satisfaction</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- 03 · TRUST BAR -->
<div class="trust-bar">
  <div class="container">
    <div class="trust-bar-inner">
      <span class="tb-chip"><span class="tbi">&#9733;</span> 100% Real Local Reviewers</span>
      <span class="tb-chip"><span class="tbi">&#127860;</span> Menu-Aware Content</span>
      <span class="tb-chip"><span class="tbdot"></span> 47 reviews posted in the last 24h</span>
      <span class="tb-chip"><span class="tbi">&#128260;</span> 7-Day Replacement Per Review</span>
    </div>
  </div>
</div>

<!-- 04 · CASE STUDY -->
<section class="sec-white fade-up" id="case-study">
  <div class="container">
    <div class="stag">CASE STUDY</div>
    <h2 class="stitle">Real Results From Our Restaurant Clients</h2>
    <p class="shand">From 3.5 stars to 4.9 on their Google Maps listing</p>

    <div class="cs-card">
      <div class="cs-head">
        <span class="csh-l">New York Diner Co. &middot; Google Maps listing</span>
        <span class="csh-r">BEFORE &rarr; AFTER</span>
      </div>
      <div class="cs-img">
        <img src="https://smart-buzzer.com/promo-fnb/Before.jpg" alt="New York Diner Co. Google Maps listing before and after: 3.5 stars with 23 reviews, then 4.9 stars with 145 reviews" data-preview="true">
      </div>
      <div class="cs-metrics">
        <div class="cs-metric"><b>3.5 &rarr; 4.9</b><span>Star rating</span></div>
        <div class="cs-metric"><b>23 &rarr; 145</b><span>Reviews on the listing</span></div>
        <div class="cs-metric"><b>+122</b><span>Reviews we delivered</span></div>
      </div>
      <div class="cs-note">
        Figures taken from the listing screenshots above. Individual results depend on your listing, your competition and the package you choose &mdash; we do not guarantee ranking outcomes.
      </div>
    </div>
  </div>
</section>

<div class="checker-divider"></div>

<!-- 05 · PICK YOUR RESTAURANT TYPE -->
<section class="cuisine-section" id="cuisine">
  <div class="container">
    <div class="stag">STEP 1</div>
    <h2 class="stitle">Pick Your Restaurant Type</h2>
    <p class="shand">We write reviews the way real diners talk about YOUR kind of food</p>
    <p class="ssub">Pick yours and see a live sample &mdash; plus what owners in that category actually struggle with.</p>

    <div class="cuisine-pills" id="cuisinePills">
      <button class="cpill active" data-cuisine="italian" onclick="selectCuisine('italian')"><span class="cemo">&#127837;</span> Italian</button>
      <button class="cpill" data-cuisine="chinese" onclick="selectCuisine('chinese')"><span class="cemo">&#129377;</span> Chinese</button>
      <button class="cpill" data-cuisine="mexican" onclick="selectCuisine('mexican')"><span class="cemo">&#127790;</span> Mexican</button>
      <button class="cpill" data-cuisine="japanese" onclick="selectCuisine('japanese')"><span class="cemo">&#127843;</span> Japanese</button>
      <button class="cpill" data-cuisine="indian" onclick="selectCuisine('indian')"><span class="cemo">&#127835;</span> Indian</button>
      <button class="cpill" data-cuisine="steakhouse" onclick="selectCuisine('steakhouse')"><span class="cemo">&#129385;</span> Steakhouse</button>
      <button class="cpill" data-cuisine="thai" onclick="selectCuisine('thai')"><span class="cemo">&#129360;</span> Thai</button>
      <button class="cpill" data-cuisine="greek" onclick="selectCuisine('greek')"><span class="cemo">&#129386;</span> Greek</button>
      <button class="cpill" data-cuisine="cafe" onclick="selectCuisine('cafe')"><span class="cemo">&#9749;</span> Caf&eacute;</button>
      <div class="cuisine-more-wrap">
        <button class="cuisine-more-btn" onclick="toggleMoreCuisines(event)">More 7+ &#9662;</button>
        <div class="cuisine-more-list" id="cuisineMoreList">
          <button onclick="selectCuisine('pizzeria')"><span class="cemo">&#127829;</span> Pizzeria</button>
          <button onclick="selectCuisine('seafood')"><span class="cemo">&#129424;</span> Seafood</button>
          <button onclick="selectCuisine('bbq')"><span class="cemo">&#127830;</span> BBQ / Grill</button>
          <button onclick="selectCuisine('bakery')"><span class="cemo">&#129360;</span> Bakery</button>
          <button onclick="selectCuisine('korean')"><span class="cemo">&#127833;</span> Korean</button>
          <button onclick="selectCuisine('bar')"><span class="cemo">&#127866;</span> Bar / Pub</button>
          <button onclick="selectCuisine('other')"><span class="cemo">&#127860;</span> Other</button>
        </div>
      </div>
    </div>

    <div class="cuisine-panel" id="cuisinePanel"><!-- rendered by JS --></div>
  </div>
</section>

<div class="checker-divider"></div>

<!-- 06 · GUARANTEE -->
<section class="sec-cream fade-up" id="guarantee">
  <div class="container">
    <div class="stag">OUR PROMISE</div>
    <h2 class="stitle">Our Guarantee</h2>
    <p class="shand">Exactly what we promise &mdash; and what we don't</p>

    <div class="gt-wrap">
      <div class="gt-callout">
        <strong>We guarantee reviews appear on your listing &mdash; not that they stay forever.</strong>
        <span>You pay for every review we deliver live onto your Google listing. Whether it stays long-term is decided by Google&rsquo;s algorithm &mdash; no seller on earth controls that, and we don&rsquo;t pretend otherwise. What we <strong>do</strong> guarantee is a 7-day free replacement window on every single review. It is what it is, and we sell it honestly.</span>
      </div>

      <div class="gt-steps">
        <div class="gt-step">
          <div class="gtn">1</div>
          <strong>A review shows up</strong>
          <span>The moment a review appears on your listing it counts as delivered. 1 review = 1 delivered.</span>
        </div>
        <div class="gt-step">
          <div class="gtn">2</div>
          <strong>You get 7 days per review</strong>
          <span>Each review has its own 7-day window starting the day it shows up &mdash; not after the whole order is done.</span>
        </div>
        <div class="gt-step ok">
          <div class="gtn">3</div>
          <strong>Drops in 7 days? We replace it</strong>
          <span>If a review drops inside its 7 days we add a new one free &mdash; one replacement per review.</span>
        </div>
      </div>

      <div class="gt-summary">
        <p><span class="gt-yes">&#10003; If it drops within 7 days of showing up</span> &mdash; we replace it free, one time.</p>
        <p><span class="gt-no">&mdash; If it stays live past 7 days</span> &mdash; it&rsquo;s final and counts as delivered. If it drops <strong>after</strong> the 7 days, it is <strong>not replaced</strong>.</p>
        <p><span class="gt-no">&mdash; If a replacement later drops</span> &mdash; that one is not replaced again (one replacement per review).</p>
      </div>
    </div>
  </div>
</section>

<!-- 07 · PRICING -->
<section class="pricing-section fade-up" id="pricing">
  <div class="container">
    <div class="stag">STEP 2</div>
    <h2 class="stitle">Choose Your Package</h2>
    <p class="shand">You pay for reviews that show up on your listing</p>

    <div class="pk-cards">

      <div class="pk-card">
        <div class="pk-name">Starter</div>
        <div class="pk-reviews">72 reviews on your listing</div>
        <div class="pk-price">$350</div>
        <div class="pk-per">One-time payment</div>
        <p class="pk-note">We submit around 300 reviews gradually so 72 show up on your business listing.</p>
        <ul class="pk-feats">
          <li><span class="pkc">&#10003;</span> Written around your actual dishes</li>
          <li><span class="pkc">&#10003;</span> Real people, aged local accounts</li>
          <li><span class="pkc">&#10003;</span> You approve every review first</li>
          <li><span class="pkc">&#10003;</span> Free replacement within 7 days</li>
          <li><span class="pkc">&#10003;</span> $4.86 per review</li>
        </ul>
        <a href="#order-form" class="pk-btn" data-package="starter" onclick="preSelectPkg('starter')">ORDER NOW</a>
      </div>

      <div class="pk-card popular">
        <span class="pk-badge">MOST POPULAR</span>
        <div class="pk-name">Growth</div>
        <div class="pk-reviews">96 reviews on your listing</div>
        <div class="pk-price">$430 <span class="pk-was">$480</span></div>
        <div class="pk-per">One-time payment <span class="pk-save">SAVE $50</span></div>
        <p class="pk-note">We submit around 400 reviews gradually so 96 show up on your business listing.</p>
        <ul class="pk-feats">
          <li><span class="pkc">&#10003;</span> Written around your actual dishes</li>
          <li><span class="pkc">&#10003;</span> Real people, aged local accounts</li>
          <li><span class="pkc">&#10003;</span> You approve every review first</li>
          <li><span class="pkc">&#10003;</span> Free replacement within 7 days</li>
          <li><span class="pkc">&#10003;</span> $4.48 per review</li>
        </ul>
        <a href="#order-form" class="pk-btn" data-package="growth" onclick="preSelectPkg('growth')">ORDER NOW &mdash; SAVE $50</a>
      </div>

      <div class="pk-card">
        <div class="pk-name">Performance</div>
        <div class="pk-reviews">132 reviews on your listing</div>
        <div class="pk-price">$530 <span class="pk-was">$660</span></div>
        <div class="pk-per">One-time payment <span class="pk-save">SAVE $130</span></div>
        <p class="pk-note">We submit around 550 reviews gradually so 132 show up on your business listing.</p>
        <ul class="pk-feats">
          <li><span class="pkc">&#10003;</span> Written around your actual dishes</li>
          <li><span class="pkc">&#10003;</span> Real people, aged local accounts</li>
          <li><span class="pkc">&#10003;</span> You approve every review first</li>
          <li><span class="pkc">&#10003;</span> Free replacement within 7 days</li>
          <li><span class="pkc">&#10003;</span> $4.01 per review</li>
        </ul>
        <a href="#order-form" class="pk-btn" data-package="performance" onclick="preSelectPkg('performance')">ORDER NOW &mdash; SAVE $130</a>
      </div>

    </div>

    <div class="pk-trust">
      <div class="pk-cards-accepted">
        <span class="pk-brand pk-visa">VISA</span>
        <span class="pk-brand pk-mc"><i></i><i></i> Mastercard</span>
      </div>
      <span class="pk-trust-sep"></span>
      <span class="pk-trust-item">&#128274; Advance Payment</span>
      <span class="pk-trust-sep"></span>
      <button class="pk-trust-ask" type="button" onclick="openOnboarding()">
        What happens after you place your order?
        <span class="pk-trust-link">Learn more &rarr;</span>
      </button>
    </div>
  </div>
</section>

<!-- 08 · HOW IT WORKS -->
<section class="how-section fade-up" id="how">
  <div class="container">
    <div class="stag">HOW IT WORKS</div>
    <h2 class="stitle">How It Works</h2>
    <p class="shand">Three steps &mdash; and you approve everything before it goes live</p>
    <div class="how-steps">
      <div class="how-step">
        <div class="how-num">1</div>
        <div class="how-step-title">You Order</div>
        <div class="how-step-hand">Takes 2 minutes</div>
        <div class="how-step-desc">Pick your package and pay. Then send us your menu and the dishes you want mentioned.</div>
      </div>
      <div class="how-step">
        <div class="how-num">2</div>
        <div class="how-step-title">We Write &amp; You Approve</div>
        <div class="how-step-hand">Nothing posts without your OK</div>
        <div class="how-step-desc">We write every review around your food. You read them all and approve before anything goes live.</div>
      </div>
      <div class="how-step">
        <div class="how-num">3</div>
        <div class="how-step-title">Reviews Go Live</div>
        <div class="how-step-hand">First ones within 24 hours</div>
        <div class="how-step-desc">A few appear on your listing every day until your package is complete. Track it all in your report.</div>
      </div>
    </div>
  </div>
</section>

<!-- 09 · WHAT YOU GET -->
<section class="trust-section fade-up">
  <div class="container">
    <div class="stag">WHY US</div>
    <h2 class="stitle">What You Get</h2>
    <div class="trust-grid">
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3v7a3 3 0 0 0 3 3v8"/><path d="M6 3v6"/><path d="M9 3v6"/><circle cx="16" cy="10" r="5"/><path d="M16 15v6"/></svg></span><h4>Menu-Specific</h4><p>Reviews mention your real dishes, specials, and atmosphere &mdash; not generic praise.</p></div>
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5l2.9 5.9 6.6.9-4.8 4.6 1.2 6.5L12 17.3l-5.9 3.1 1.2-6.5L2.5 9.3l6.6-.9z"/></svg></span><h4>Natural Star Mix</h4><p>80% five-star, 20% four-star. A natural pattern that reads as authentic.</p></div>
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5l7.5 3v6c0 4.8-3.2 9.1-7.5 10.5C7.7 20.6 4.5 16.3 4.5 11.5v-6z"/><path d="M9 12l2 2 4-4"/></svg></span><h4>Compliant by Design</h4><p>Aged accounts, unique IPs, varied devices, gradual staggered delivery.</p></div>
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11a8.5 8.5 0 0 0-14.6-5.3L3 8.5"/><path d="M3 3.5v5h5"/><path d="M3.5 13a8.5 8.5 0 0 0 14.6 5.3L21 15.5"/><path d="M21 20.5v-5h-5"/></svg></span><h4>7-Day Replacement</h4><p>Each review has its own 7-day window. Drops inside it are replaced free (one replacement per review).</p></div>
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3.5h6a1 1 0 0 1 1 1v1.5H8V4.5a1 1 0 0 1 1-1z"/><path d="M16 5.5h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-12a2 2 0 0 1 2-2h2"/><path d="M8.5 13.5l2.5 2.5 4.5-4.5"/></svg></span><h4>You Approve All</h4><p>Full content preview before posting. Nothing goes live without your sign-off.</p></div>
      <div class="trust-card"><span class="trust-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20.5h16"/><path d="M7 20.5v-6"/><path d="M12 20.5v-11"/><path d="M17 20.5v-8"/></svg></span><h4>Delivery Report</h4><p>Full transparency &mdash; track every review as it lands on your listing.</p></div>
    </div>
  </div>
</section>

<div class="checker-divider"></div>

<!-- 10 · ORDER FORM -->
<section class="order-form-section fade-up" id="order-form">
  <div class="container">
    <div class="stag">STEP 3</div>
    <h2 class="stitle">Place Your Order</h2>
    <p class="shand">Fill this in and we start on your reviews right after payment</p>

    <div class="order-form-wrapper">
      <div class="order-form-head">
        <h3>Your Order</h3>
        <div class="order-form-countdown">
          Offer expires in <span id="ofH">00</span>:<span id="ofM">00</span>:<span id="ofS">00</span>
        </div>
      </div>
      <div class="order-form-body">
        <div class="of-group">
          <label class="of-label" for="ofBizName">Google Business Name + Location<span class="of-req">*</span></label>
          <input type="text" class="of-input" id="ofBizName" placeholder="Example: John's Burgers in New York" required>
        </div>

        <div class="of-group">
          <label class="of-label" for="ofWhatsapp">WhatsApp Number (for order updates)<span class="of-req">*</span></label>
          <input type="tel" class="of-input" id="ofWhatsapp" placeholder="+1 Enter your WhatsApp number" required>
        </div>

        <div class="of-group">
          <label class="of-label" for="ofEmail">Email Address</label>
          <input type="email" class="of-input" id="ofEmail" placeholder="Enter your email address (optional)">
        </div>

        <span class="of-package-label">Select Your Package:</span>
        <div class="of-packages">
          <label class="of-pkg" data-pkg="starter" onclick="selectPkg(this)">
            <input type="radio" name="package" value="starter" class="of-pkg-radio">
            <div class="of-pkg-info">
              <span class="of-pkg-name">Starter &mdash; 72 Reviews</span>
            </div>
            <span class="of-pkg-price">$350</span>
          </label>
          <label class="of-pkg selected" data-pkg="growth" onclick="selectPkg(this)">
            <input type="radio" name="package" value="growth" class="of-pkg-radio" checked>
            <div class="of-pkg-info">
              <span class="of-pkg-name">Growth &mdash; 96 Reviews<span class="of-pkg-popular">POPULAR</span></span>
              <span class="of-pkg-detail"><s>$480</s> &mdash; Save $50</span>
            </div>
            <span class="of-pkg-price">$430</span>
          </label>
          <label class="of-pkg" data-pkg="performance" onclick="selectPkg(this)">
            <input type="radio" name="package" value="performance" class="of-pkg-radio">
            <div class="of-pkg-info">
              <span class="of-pkg-name">Performance &mdash; 132 Reviews</span>
              <span class="of-pkg-detail"><s>$660</s> &mdash; Save $130</span>
            </div>
            <span class="of-pkg-price">$530</span>
          </label>
        </div>

        <div class="of-notice">
          <p class="ofn-urgent">&#9889; <strong>Order now and we start today.</strong> Your content is written and sent for approval &mdash; the first reviews go live on your listing within 24 hours of your OK.</p>
          <p class="ofn-fine"><strong>What you're buying:</strong> reviews that <strong>show up</strong> on your Google listing, each with a 7-day free replacement (one per review). Long-term stay is decided by Google's algorithm and isn't guaranteed. Secure card checkout &middot; all sales final (refund as store voucher only).</p>
        </div>

        <button class="of-submit" onclick="submitOrder()">COMPLETE ORDER &rarr;</button>

        <div class="of-trust">
          <span>&#128274; Secure Checkout</span>
          <span>&#128737; SSL Protected</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="checker-divider"></div>

<!-- 11 · REAL LOCAL REVIEWS -->
<section class="sec-off fade-up" id="reviews">
  <div class="container">
    <div class="stag">PROOF</div>
    <h2 class="stitle">Real Local Reviews</h2>
    <p class="shand">Straight from live Google listings we've delivered</p>
    <div class="framed-shot">
      <div class="framed-shot-bar"><span></span><span></span><span></span></div>
      <img src="https://smart-buzzer.com/wp-content/uploads/2025/04/slide-3.jpg" alt="Example of delivered Google reviews" data-preview="true">
    </div>
  </div>
</section>

<!-- 12 · CHOOSE YOUR OWN SENTENCES -->
<section class="sec-white fade-up">
  <div class="container">
    <div class="stag">YOUR WORDS</div>
    <h2 class="stitle">Choose Your Own Sentences</h2>
    <p class="shand">Name the dishes you want mentioned &mdash; we write around them</p>

    <div class="split-flex">
      <div class="split-img">
        <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review sentence sheet" data-preview="true">
      </div>
      <div class="split-body">
        <p>You choose the content, or let us create the variety for you. For every review ordered we prepare <strong>8&times; unique sentences</strong> so nothing reads like a copy-paste.</p>
        <ul class="split-list">
          <li><span class="sc">&#10003;</span><span><strong>Human-written, menu-specific</strong>Your signature dishes, specials and atmosphere by name.</span></li>
          <li><span class="sc">&#10003;</span><span><strong>Preview &amp; approve before posting</strong>Nothing goes live on your listing without your sign-off.</span></li>
          <li><span class="sc">&#10003;</span><span><strong>Up to 2 revision rounds included</strong>Wrong tone or wrong dish? Send it back, we rewrite it.</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- 13 · TRACK YOUR ORDER -->
<section class="sec-off fade-up">
  <div class="container">
    <div class="stag">TRANSPARENCY</div>
    <h2 class="stitle">Track Your Order Every Day</h2>
    <p class="shand">A live dashboard &mdash; see every review as it lands</p>
    <div class="framed-shot">
      <div class="framed-shot-bar"><span></span><span></span><span></span></div>
      <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign progress dashboard" data-preview="true">
    </div>
  </div>
</section>

<!-- 14 · TRUST STRIP -->
<section class="sec-white fade-up">
  <div class="container">
    <div class="stag">SCALE</div>
    <h2 class="stitle">Serving Over 2,000+ Businesses</h2>
    <p class="shand">Across the USA, Canada and Australia</p>
    <div class="framed-shot">
      <div class="framed-shot-bar"><span></span><span></span><span></span></div>
      <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="Campaign operations board" data-preview="true">
    </div>
  </div>
</section>

<div class="checker-divider"></div>

<!-- 15 · OWNER TESTIMONIALS -->
<section class="sec-off fade-up">
  <div class="container">
    <div class="stag">WORD OF MOUTH</div>
    <h2 class="stitle">What Restaurant Owners Say</h2>
    <p class="shand">Real feedback from restaurant owners we work with</p>

    <div class="testi-grid">
      <div class="testi-card-fnb">
        <div class="tstars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p>&ldquo;The reviews actually mention our patio and the short rib ragu &mdash; not vague &lsquo;great food&rsquo; filler. Weeknight covers are up noticeably since we cracked the top 3.&rdquo;</p>
        <div class="testi-by">
          <div class="tav" style="background-image:url('https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&amp;q=80')"></div>
          <div class="tmeta"><strong>Anita P.</strong><span>Italian restaurant &middot; Brooklyn, NY</span></div>
        </div>
      </div>

      <div class="testi-card-fnb">
        <div class="tstars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p>&ldquo;We sat at 3.4 stars for two years because of a handful of bad nights. Took about five weeks to climb back. The pacing felt natural &mdash; nothing landed in a suspicious lump.&rdquo;</p>
        <div class="testi-by">
          <div class="tav" style="background-image:url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&amp;q=80')"></div>
          <div class="tmeta"><strong>Mike R.</strong><span>BBQ &amp; grill &middot; Austin, TX</span></div>
        </div>
      </div>

      <div class="testi-card-fnb">
        <div class="tstars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p>&ldquo;What sold me was approving every line before it posted. I swapped two reviews that named a dish we&rsquo;d taken off the menu. Delivery report showed up exactly as promised.&rdquo;</p>
        <div class="testi-by">
          <div class="tav" style="background-image:url('https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&amp;q=80')"></div>
          <div class="tmeta"><strong>Kira S.</strong><span>Caf&eacute; &amp; bakery &middot; Portland, OR</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 16 · CLIENT LOGOS -->
<section class="sec-white fade-up">
  <div class="container">
    <div class="stag">OUR CLIENTS</div>
    <h2 class="stitle">Businesses We Work With</h2>
    <div class="clients-grid">
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client logo" loading="lazy"></div>
      <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client logo" loading="lazy"></div>
    </div>
  </div>
</section>

<!-- 17 · FAQ -->
<section class="sec-off fade-up" id="faq">
  <div class="container">
    <div class="stag">QUESTIONS</div>
    <h2 class="stitle">Frequently Asked Questions</h2>
    <p class="shand">Everything worth knowing, answered straight</p>

    <div class="faq-list">
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Is this safe for my Google Business Profile?<span class="faq-arrow">&#9660;</span></button>
        <div class="faq-a"><div class="faq-a-inner">We use gradual posting, aged Google accounts, unique IPs and different devices for each review, with a natural mix of 4 and 5-star ratings. We have served 2,000+ businesses with a method designed to keep your account safe. Your listing is our asset too &mdash; we never compromise account safety to hit a number faster.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Will the reviews actually mention my dishes?<span class="faq-arrow">&#9660;</span></button>
        <div class="faq-a"><div class="faq-a-inner">Yes &mdash; that&rsquo;s the whole point of the restaurant-type step above. You send us your menu, your signature dishes and anything you want highlighted (patio, brunch service, the pit master, whatever). Every comment is human-written around those specifics. For each review ordered we prepare 8&times; unique sentences so nothing repeats, and you approve all content before we post.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">What does &ldquo;show-up&rdquo; mean? Do the reviews stay?<span class="faq-arrow">&#9660;</span></button>
        <div class="faq-a"><div class="faq-a-inner">We sell reviews based on <strong>show-up</strong> &mdash; the reviews we deliver live onto your Google listing. The package number (72 / 96 / 132) is how many reviews we make appear on your profile. Whether a review stays long-term is decided entirely by Google&rsquo;s own algorithm, and no seller can guarantee that. What we guarantee is the 7-day per-review replacement below. We sell it honestly: it is what it is.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">What if a review gets removed?<span class="faq-arrow">&#9660;</span></button>
        <div class="faq-a"><div class="faq-a-inner">Each review counts as delivered the moment it shows up on your listing, and gets its own 7-day replacement window starting that day. If it drops within those 7 days we replace it free &mdash; one replacement per review. Reviews that stay live past 7 days are final; if one drops after the window it is not replaced. Some drops are normal and happen even with organic reviews because of Google&rsquo;s algorithm. The same rule applies during Google&rsquo;s periodic algorithm updates: reviews still inside their 7-day window are replaced, reviews past it are final.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">How do I pay? Can I get a refund?<span class="faq-arrow">&#9660;</span></button>
        <div class="faq-a"><div class="faq-a-inner">We accept <strong>Credit / Debit Card</strong> through our secure checkout. Advance payment is required before the campaign starts. All sales are final &mdash; there are no cash refunds. If a refund is approved it is issued as a <strong>store voucher</strong> usable on any future order. Once a campaign has started the order cannot be cancelled or disputed.</div></div>
      </div>
    </div>

    <div style="text-align:center;margin-top:34px">
      <a href="#order-form" class="hero-cta">PLACE YOUR ORDER &rarr;</a>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section fade-up" id="cta">
  <div class="container">
    <h2>Ready to Fill More Tables?</h2>
    <p class="cta-hand">More reviews, more people walking through the door</p>
    <p class="cta-sub">Choose your package and get your first reviews within 24 hours of approval.</p>
    <a href="#order-form" class="cta-btn-white">PLACE YOUR ORDER &rarr;</a>
    <div class="cta-contacts">
      <a href="https://wa.me/<?php echo htmlspecialchars($SB_WA_NUMBER, ENT_QUOTES, 'UTF-8'); ?>">WhatsApp <?php echo htmlspecialchars($SB_WA_DISPLAY, ENT_QUOTES, 'UTF-8'); ?></a>
      <a href="mailto:contact@smart-buzzer.com">contact@smart-buzzer.com</a>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <div class="footer-links">
      <a href="https://smart-buzzer.com/tracker">Track Campaign</a>
      <a href="https://smart-buzzer.com/report">Report Issue</a>
      <a href="https://smart-buzzer.com/service-tnc">Terms &amp; Conditions</a>
    </div>
    &copy; 2026 Smart Buzzer &middot; A subsidiary of Pintarnya
  </div>
</footer>

<!-- FLOATING · WHATSAPP WIDGET -->
<div class="wa-float" id="waFloat">
  <div class="wa-chat-box" id="waChatBox">
    <div class="wa-chat-head">
      <div class="wa-chat-avatar">SB</div>
      <div>
        <h4>Smart Buzzer</h4>
        <p>Typically replies in minutes</p>
      </div>
    </div>
    <div class="wa-chat-body">
      <div class="wa-bubble">
        Hungry for more covers? Tell us your cuisine and we&rsquo;ll show you exactly what your reviews would say.
        <small>Smart Buzzer Team</small>
      </div>
    </div>
    <div class="wa-chat-foot">
      <a href="https://wa.me/<?php echo htmlspecialchars($SB_WA_NUMBER, ENT_QUOTES, 'UTF-8'); ?>?text=Hi%20Smart%20Buzzer%2C%20I%20run%20a%20restaurant%20and%20I%20want%20to%20order%20Google%20reviews." target="_blank" rel="noopener">START CHAT</a>
    </div>
  </div>
  <button class="wa-trigger" id="waTrigger" aria-label="Chat on WhatsApp">
    <span class="wa-pulse"></span>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.3-5.1-3.7-10.6-6.5z"/></svg>
  </button>
</div>

<!-- FLOATING · SOCIAL PROOF TOAST -->
<div class="sp-toast" id="spToast">
  <button class="sp-toast-close" onclick="closeToast()" aria-label="Close">&times;</button>
  <div class="sp-toast-avatar" id="spToastAvatar" style="background:#D42B2B">
    M
    <span class="sp-verified"></span>
  </div>
  <div class="sp-toast-text" id="spToastText">
    <strong>Marco from Brooklyn</strong>
    <span class="sp-action">just ordered 96 reviews</span>
    <span class="sp-time">2 hours ago</span>
  </div>
</div>

<!-- FLOATING · STICKY BOTTOM CTA -->
<div class="sticky-cta" id="stickyCta">
  <div class="sticky-cta-inner">
    <span class="sticky-cta-text">Offer ends in:</span>
    <div class="sticky-cta-timer">
      <div class="sct-block"><span class="sct-num" id="sctHours">23</span><span class="sct-label">Hrs</span></div>
      <div class="sct-block"><span class="sct-num" id="sctMins">59</span><span class="sct-label">Min</span></div>
      <div class="sct-block"><span class="sct-num" id="sctSecs">59</span><span class="sct-label">Sec</span></div>
    </div>
    <a href="#order-form" class="sticky-cta-btn">ORDER NOW</a>
  </div>
</div>

<!-- ONBOARDING MODAL -->
<div class="ob-modal" id="obModal">
  <div class="ob-box">
    <div class="ob-head">
      <h3>Your Next Steps After Ordering</h3>
      <button class="ob-close" onclick="closeOnboarding()" aria-label="Close">&times;</button>
    </div>
    <div class="ob-body">
      <p>Once your payment is confirmed, you&rsquo;ll be guided through our <strong>simple onboarding form</strong> &mdash; it takes about 10&ndash;15 minutes and makes sure your reviews are tailored to your restaurant.</p>
      <img src="https://content.pancake.vn/2-2602/2026/2/2/6a0576f7948df2fc53cf4d6651aaa6f1c9b7b445.png" alt="Smart Buzzer onboarding form" loading="lazy">
    </div>
  </div>
</div>

<!-- IMAGE MODAL -->
<div class="image-modal" id="imageModal">
  <span class="modal-close" id="modalClose">&times;</span>
  <img id="modalImage" alt="Preview">
</div>

<script>
/* ===================================================================
   CUISINE PANEL DATA — one entry per pill (16 total)
   =================================================================== */
function fnbPhoto(id){ return 'https://images.unsplash.com/photo-' + id + '?w=640&h=380&fit=crop&q=72'; }
function fnbFace(id){ return 'https://images.unsplash.com/photo-' + id + '?w=120&q=80'; }

var FACE_A = '1573496359142-b8d87734a5a2';
var FACE_B = '1500648767791-00dcc994a43e';
var FACE_C = '1494790108377-be9c29b29330';
var FACE_D = '1507003211169-0a1dd7228f2d';

var cuisineMeta = {
  italian:{emoji:'&#127837;',name:'Italian',photo:'1611270629569-8b357cb88da9',
    subtitle:'For trattorias, red-sauce joints and pasta bars. Reviews name the dishes people actually come back for — the ragu, the fresh pasta, the tiramisu.',
    stats:[{n:'+24%',l:'Avg weeknight covers'},{n:'Top 3',l:'Local pack rank'},{n:'19+',l:'Italian campaigns'}],
    pains:['Buried under chain Italian in the map pack','One bad pasta night dragging the average down','Regulars never bother leaving a review','Competitors who bought reviews years ago outrank you'],
    review:'"The cacio e pepe was silky and properly peppery — not a cream sauce shortcut. Fresh pasta makes all the difference. Tiramisu was a perfect ending."',
    author:'Anna F. &middot; Brooklyn local', face:FACE_C,
    ownerQuote:'"Reviews actually mention our patio and the short rib ragu. Weeknight covers are up noticeably since we hit top 3."',
    ownerName:'Anita P., Owner &middot; Brooklyn, NY', ownerFace:FACE_A},

  chinese:{emoji:'&#129377;',name:'Chinese',photo:'1585032226651-759b368d7246',
    subtitle:'For dim sum houses, Sichuan kitchens and neighbourhood takeout. Reviews call out wok hei, the dumpling skins, and the dishes off the specials board.',
    stats:[{n:'+27%',l:'Avg pickup orders'},{n:'Top 3',l:'Local pack rank'},{n:'16+',l:'Chinese campaigns'}],
    pains:['Delivery apps outranking your own listing','Reviews that only say "good food, cheap"','Tourists judging by review count alone','Newer spots with 200+ reviews stealing the search'],
    review:'"The dim sum here is the best in the area. Har gow with translucent skin, siu mai packed with flavour, and the char siu bao was properly fluffy."',
    author:'Linda W. &middot; regular', face:FACE_A,
    ownerQuote:'"We finally rank above the delivery aggregators for our own name. Pickup orders went up within the first month."',
    ownerName:'David C., Owner &middot; San Gabriel, CA', ownerFace:FACE_B},

  mexican:{emoji:'&#127790;',name:'Mexican',photo:'1565299585323-38d6b0865b47',
    subtitle:'For taquerias, cantinas and family kitchens. Reviews name the salsas, the tortillas, and whether the al pastor comes off a real trompo.',
    stats:[{n:'+31%',l:'Avg foot traffic lift'},{n:'Top 3',l:'Local pack rank'},{n:'21+',l:'Mexican campaigns'}],
    pains:['Fast-casual chains dominating "tacos near me"','Great food, almost no reviews to prove it','One bad night tanking a 4.2 average','Locals who love you but never post'],
    review:'"The al pastor tacos are legit — pineapple-kissed pork on fresh corn tortillas with a serious salsa verde. One of the best taquerias I have found."',
    author:'Carlos G. &middot; neighbourhood regular', face:FACE_B,
    ownerQuote:'"People walk in quoting the reviews — they ask for the al pastor by name before they even sit down."',
    ownerName:'Rosa M., Owner &middot; Phoenix, AZ', ownerFace:FACE_C},

  japanese:{emoji:'&#127843;',name:'Japanese',photo:'1579584425555-c3ce17fd4351',
    subtitle:'For sushi counters, ramen shops and izakaya. Reviews speak to rice temperature, broth depth and the details only a real diner notices.',
    stats:[{n:'+26%',l:'Avg reservation rate'},{n:'Top 3',l:'Local pack rank'},{n:'14+',l:'Japanese campaigns'}],
    pains:['Grocery-store sushi counters ranking above you','Hard to communicate quality through photos alone','Higher ticket means reviews matter more','Conveyor-belt chains flooding the search'],
    review:'"The omakase was a revelation — each piece of nigiri pristine, rice at the perfect temperature. The toro melted on contact. Worth every dollar."',
    author:'Alyssa N. &middot; verified diner', face:FACE_C,
    ownerQuote:'"Reservations picked up almost immediately. The reviews read like our actual customers, which was the whole point for us."',
    ownerName:'Kenji T., Owner &middot; Seattle, WA', ownerFace:FACE_D},

  indian:{emoji:'&#127835;',name:'Indian',photo:'1603894584373-5ac82b2ae398',
    subtitle:'For curry houses, tandoor kitchens and South Indian spots. Reviews mention spice level, the naan straight out of the tandoor, and the thali value.',
    stats:[{n:'+29%',l:'Avg lunch covers'},{n:'Top 3',l:'Local pack rank'},{n:'17+',l:'Indian campaigns'}],
    pains:['"Too spicy" reviews from people who ordered wrong','Buffet chains outranking a real kitchen','Lunch crowd never leaves feedback','Competing against ten curry houses on one street'],
    review:'"The butter chicken is rich and properly spiced without being overwhelming, and the garlic naan came out of the tandoor still blistering. Coming back."',
    author:'Sarah M. &middot; local diner', face:FACE_A,
    ownerQuote:'"The reviews name our biryani and the thali, so people arrive already knowing what to order. Lunch is busier than it has been in years."',
    ownerName:'Raj S., Owner &middot; Chicago, IL', ownerFace:FACE_B},

  steakhouse:{emoji:'&#129385;',name:'Steakhouse',photo:'1600891964092-4316c288032e',
    subtitle:'For chophouses, grills and American classics. Reviews speak to the sear, the temperature accuracy, and whether the sides justify the ticket.',
    stats:[{n:'+23%',l:'Avg weekend bookings'},{n:'Top 3',l:'Local pack rank'},{n:'12+',l:'Steakhouse campaigns'}],
    pains:['National chains owning the top of the map pack','High ticket price means every review is scrutinised','One overcooked steak becoming a permanent 1-star','Corporate diners filtering by rating before booking'],
    review:'"The ribeye came out a perfect medium-rare — incredible char, juicy all the way through. Paired it with the truffle mash. Outstanding meal."',
    author:'Mike T. &middot; verified diner', face:FACE_D,
    ownerQuote:'"We were losing corporate bookings on rating alone. That is no longer the conversation when someone searches us."',
    ownerName:'Frank D., Owner &middot; Dallas, TX', ownerFace:FACE_B},

  thai:{emoji:'&#129360;',name:'Thai',photo:'1637806930600-37fa8892069d',
    subtitle:'For Thai kitchens and noodle bars. Reviews mention heat balance, the wok flavour, and whether the curry paste is made in-house.',
    stats:[{n:'+25%',l:'Avg delivery orders'},{n:'Top 3',l:'Local pack rank'},{n:'13+',l:'Thai campaigns'}],
    pains:['Every Thai place in town looks identical on Google','Heat-level complaints skewing the average','Delivery apps capturing the search intent','Hard to show authenticity without reviews'],
    review:'"The green curry was perfectly balanced — creamy coconut, fragrant basil, exactly the right heat. Pad see ew had real wok flavour. Authentic."',
    author:'Natalie P. &middot; local diner', face:FACE_C,
    ownerQuote:'"The comments actually explain our spice levels, so we get fewer surprised customers and better ratings from the ones who come in."',
    ownerName:'Nok P., Owner &middot; Portland, OR', ownerFace:FACE_A},

  greek:{emoji:'&#129386;',name:'Greek / Mediterranean',photo:'1606735584785-1848fdcaea57',
    subtitle:'For tavernas, gyro shops and mezze bars. Reviews mention souvlaki, fresh pita, house-made tzatziki and family hospitality.',
    stats:[{n:'+22%',l:'Avg foot traffic lift'},{n:'Top 3',l:'Local pack rank'},{n:'11+',l:'Greek campaigns'}],
    pains:['Buried under fast-casual bowl chains in the map pack','Tourists judging purely by review count','One bad review pulling the whole average down','Regulars who never leave reviews'],
    review:'"The lamb souvlaki was charred perfectly and the tzatziki was clearly house-made. Family run and it shows in every single detail."',
    author:'Marco D. &middot; Brooklyn local', face:FACE_B,
    ownerQuote:'"Foot traffic is up around 22% since we hit top 3. The reviews mention our gyros by name, which is exactly what we wanted."',
    ownerName:'Yanni K., Owner &middot; Astoria, NY', ownerFace:FACE_D},

  cafe:{emoji:'&#9749;',name:'Caf&eacute;',photo:'1593443320739-77f74939d0da',
    subtitle:'For coffee bars, brunch spots and neighbourhood caf&eacute;s. Reviews mention the espresso, the pastry case, wifi and whether it is a good place to work.',
    stats:[{n:'+28%',l:'Avg morning traffic'},{n:'Top 3',l:'Local pack rank'},{n:'18+',l:'Caf&eacute; campaigns'}],
    pains:['Starbucks owning "coffee near me" in your postcode','Remote workers choosing by rating and wifi mentions','Great beans, almost no online proof','Brunch queue at a rival with 400 reviews'],
    review:'"The oat milk latte is perfectly smooth with a great espresso base, not too sweet. Avocado toast with poached eggs is my weekend order."',
    author:'Sophie T. &middot; regular', face:FACE_C,
    ownerQuote:'"Mornings are noticeably busier. People mention the cold brew before they have even ordered it."',
    ownerName:'Kira S., Owner &middot; Portland, OR', ownerFace:FACE_C},

  pizzeria:{emoji:'&#127829;',name:'Pizzeria',photo:'1513104890138-7c749659a591',
    subtitle:'For slice shops, Neapolitan ovens and family pizzerias. Reviews mention crust texture, the sauce, and how it travels for takeout.',
    stats:[{n:'+30%',l:'Avg takeout orders'},{n:'Top 3',l:'Local pack rank'},{n:'15+',l:'Pizzeria campaigns'}],
    pains:['Domino\'s and chains dominating every pizza search','Delivery apps outranking your own listing','Cold-delivery complaints hurting the average','Every new pizzeria launching with paid reviews'],
    review:'"Leopard-spotted crust with a proper chew — they know what they are doing with that oven. The margherita is simple and near perfect."',
    author:'Tony B. &middot; local', face:FACE_D,
    ownerQuote:'"Takeout is up and we finally show above the apps when someone searches our name. That alone paid for the campaign."',
    ownerName:'Gino R., Owner &middot; Newark, NJ', ownerFace:FACE_B},

  seafood:{emoji:'&#129424;',name:'Seafood',photo:'1606850780554-b55ea4dd0b70',
    subtitle:'For oyster bars, fish houses and coastal kitchens. Reviews speak to freshness, sourcing and whether the fish was handled properly.',
    stats:[{n:'+21%',l:'Avg weekend covers'},{n:'Top 3',l:'Local pack rank'},{n:'10+',l:'Seafood campaigns'}],
    pains:['High ticket means diners research heavily before booking','Freshness doubts from a single old review','Tourist-trap perception in coastal towns','Seasonal swings leaving quiet months with no new reviews'],
    review:'"The scallops were seared hard outside and still translucent in the middle. Chowder is properly briny instead of just cream. Fish is clearly fresh."',
    author:'Diane F. &middot; verified diner', face:FACE_A,
    ownerQuote:'"The reviews talk about our sourcing, which is what we actually compete on. Weekend covers held up through the off season."',
    ownerName:'Tom H., Owner &middot; Portland, ME', ownerFace:FACE_D},

  bbq:{emoji:'&#127830;',name:'BBQ / Grill',photo:'1679711246825-1f2bd51b16d0',
    subtitle:'For smokehouses, pits and grill joints. Reviews mention the smoke ring, the bark, the sides, and what time the brisket sells out.',
    stats:[{n:'+26%',l:'Avg daily walk-ins'},{n:'Top 3',l:'Local pack rank'},{n:'12+',l:'BBQ campaigns'}],
    pains:['Franchise BBQ chains ranking above a real pit','"Sold out" reviews reading as a negative','Hard to prove low-and-slow through photos','Weekend-only crowds leaving weekdays quiet'],
    review:'"Brisket had a real smoke ring and the bark was seasoned properly. Burnt ends sold out by 2pm — that is how you know it is the real thing."',
    author:'Wade C. &middot; regular', face:FACE_B,
    ownerQuote:'"Weekdays finally picked up. People read that we sell out and they show up earlier instead of writing us off."',
    ownerName:'Mike R., Owner &middot; Austin, TX', ownerFace:FACE_D},

  bakery:{emoji:'&#129360;',name:'Bakery',photo:'1608198093002-ad4e005484ec',
    subtitle:'For bakeries, patisseries and bread shops. Reviews mention lamination, crumb, custom cake orders and how early things sell out.',
    stats:[{n:'+24%',l:'Avg morning sales'},{n:'Top 3',l:'Local pack rank'},{n:'9+',l:'Bakery campaigns'}],
    pains:['Supermarket bakeries ranking for "bakery near me"','Custom cake enquiries going to higher-rated rivals','Early sell-outs read as poor availability','Seasonal spikes with no reviews in between'],
    review:'"Croissants have proper lamination — hundreds of flaky layers and a shattering crust. The sourdough boule has a beautiful open crumb."',
    author:'Claire W. &middot; weekly regular', face:FACE_C,
    ownerQuote:'"Custom cake enquiries roughly doubled. People find us now instead of defaulting to the supermarket counter."',
    ownerName:'Maria L., Owner &middot; Denver, CO', ownerFace:FACE_A},

  korean:{emoji:'&#127833;',name:'Korean',photo:'1632558610168-8377309e34c7',
    subtitle:'For KBBQ houses, stew kitchens and fried chicken spots. Reviews mention banchan generosity, grill service and whether the kimchi is house-fermented.',
    stats:[{n:'+27%',l:'Avg group bookings'},{n:'Top 3',l:'Local pack rank'},{n:'11+',l:'Korean campaigns'}],
    pains:['KBBQ chains dominating the group-dining search','First-timers intimidated without reviews to guide them','Ventilation and wait-time complaints skewing ratings','Competing on all-you-can-eat pricing instead of quality'],
    review:'"Banchan spread was generous and refilled without asking. The galbi had a proper caramelised char and the kimchi is clearly fermented in-house."',
    author:'Jenny H. &middot; local diner', face:FACE_A,
    ownerQuote:'"Group bookings went up because the reviews explain how the grill service works. Fewer confused first-timers, better ratings."',
    ownerName:'Sun-Woo P., Owner &middot; Los Angeles, CA', ownerFace:FACE_C},

  bar:{emoji:'&#127866;',name:'Bar / Pub',photo:'1546622891-02c72c1537b6',
    subtitle:'For neighbourhood bars, cocktail rooms and gastropubs. Reviews mention the drinks programme, the kitchen hours, and whether the vibe is right.',
    stats:[{n:'+25%',l:'Avg weeknight footfall'},{n:'Top 3',l:'Local pack rank'},{n:'13+',l:'Bar &amp; pub campaigns'}],
    pains:['Sports bars and chains owning the search','Noise and wait complaints dominating the review page','Kitchen quality invisible behind the "bar" label','Quiet weeknights with no new reviews landing'],
    review:'"Well-made cocktails without the attitude — the old fashioned was balanced and properly stirred. Kitchen stays open late, which is rare here."',
    author:'Ryan D. &middot; regular', face:FACE_D,
    ownerQuote:'"The reviews mention the kitchen, not just the drinks. Weeknights are steadier than they have been in two years."',
    ownerName:'Colin B., Owner &middot; Chicago, IL', ownerFace:FACE_B},

  other:{emoji:'&#127860;',name:'Your Restaurant',photo:'1517248135467-4c7edcad34c4',
    subtitle:'Do not see your category? We have written for food trucks, buffets, vegan kitchens, halal spots, delis, juice bars and ghost kitchens. If it has a Google listing, we can write for it.',
    stats:[{n:'1,200+',l:'Campaigns delivered'},{n:'Top 3',l:'Local pack rank'},{n:'16+',l:'Food categories covered'}],
    pains:['A niche concept nobody searches by category name','Great food with almost no online proof','Bigger competitors with a decade head start','Regulars who love you but never post a review'],
    review:'"Everything came out fresh and clearly made to order. Staff were welcoming without hovering, and the portions were honest for the price."',
    author:'Alex R. &middot; local diner', face:FACE_B,
    ownerQuote:'"We are a small concept with no category to hide in. The reviews explain what we actually do, and that brought people through the door."',
    ownerName:'Sam O., Owner &middot; Nashville, TN', ownerFace:FACE_D}
};

var sbCurrentCuisine = 'italian';

function renderCuisinePanel(key){
  var d = cuisineMeta[key] || cuisineMeta['italian'];
  var panel = document.getElementById('cuisinePanel');
  if (!panel) return;

  var statsHtml = d.stats.map(function(s){
    return '<div class="cp-stat"><b>' + s.n + '</b><span>' + s.l + '</span></div>';
  }).join('');

  var painsHtml = d.pains.map(function(p){
    return '<li><span class="cx">&#9888;</span><span>' + p + '</span></li>';
  }).join('');

  panel.innerHTML =
    '<div class="cp-grid">' +
      '<div class="cp-left">' +
        '<div class="cp-photo">' +
          '<img src="' + fnbPhoto(d.photo) + '" alt="' + d.name + ' restaurant" loading="lazy">' +
          '<span class="cp-photo-tag">' + d.emoji + ' ' + d.name + '</span>' +
        '</div>' +
        '<div class="cp-left-body">' +
          '<h3>Built for ' + d.name + '</h3>' +
          '<p class="cp-sub">' + d.subtitle + '</p>' +
          '<div class="cp-stats">' + statsHtml + '</div>' +
          '<ul class="cp-checks">' +
            '<li><span class="cc">&#10003;</span> Menu-aware content written around your dishes</li>' +
            '<li><span class="cc">&#10003;</span> 2&ndash;5 show-ups per day at the pace you choose</li>' +
            '<li><span class="cc">&#10003;</span> Aged local accounts, unique IPs and devices</li>' +
            '<li><span class="cc">&#10003;</span> 7-day free replacement on every review</li>' +
          '</ul>' +
        '</div>' +
      '</div>' +
      '<div class="cp-right">' +
        '<h4>What ' + d.name + ' owners struggle with</h4>' +
        '<ul class="cp-pains">' + painsHtml + '</ul>' +
        '<span class="cp-sample-label">&#128221; Sample review we&rsquo;d write</span>' +
        '<div class="cp-sample">' +
          '<div class="cp-sample-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>' +
          '<div class="cp-sample-text">' + d.review + '</div>' +
          '<div class="cp-sample-by">' +
            '<span class="cpav" style="background-image:url(\'' + fnbFace(d.face) + '\')"></span>' +
            '<span>' + d.author + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="cp-owner">' +
          '<span class="cpav-lg" style="background-image:url(\'' + fnbFace(d.ownerFace) + '\')"></span>' +
          '<div class="cp-owner-txt">' +
            '<p>' + d.ownerQuote + '</p>' +
            '<b>' + d.ownerName + '</b>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
}

function selectCuisine(key){
  if (!cuisineMeta[key]) return;
  sbCurrentCuisine = key;

  document.querySelectorAll('.cpill').forEach(function(p){
    p.classList.toggle('active', p.getAttribute('data-cuisine') === key);
  });

  var moreList = document.getElementById('cuisineMoreList');
  if (moreList) moreList.classList.remove('show');

  renderCuisinePanel(key);
  safeLocalStorage('set', 'sb_fnb_cuisine', key);

  if (typeof logAnalyticsEvent === 'function') {
    logAnalyticsEvent('CUISINE_SELECT', {cuisine: key});
  }
}

function toggleMoreCuisines(ev){
  ev.stopPropagation();
  var list = document.getElementById('cuisineMoreList');
  if (list) list.classList.toggle('show');
}

document.addEventListener('click', function(e){
  var list = document.getElementById('cuisineMoreList');
  if (list && list.classList.contains('show') && !e.target.closest('.cuisine-more-wrap')) {
    list.classList.remove('show');
  }
});

// Boot the selector — restore the visitor's last cuisine if they picked one before
(function(){
  var saved = safeLocalStorage('get', 'sb_fnb_cuisine');
  var start = (saved && cuisineMeta[saved]) ? saved : 'italian';
  sbCurrentCuisine = start;
  document.querySelectorAll('.cpill').forEach(function(p){
    p.classList.toggle('active', p.getAttribute('data-cuisine') === start);
  });
  renderCuisinePanel(start);
})();

const obs=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target)}})},{threshold:.12});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));

/* ===================================================================
   ANALYTICS TRACKING SYSTEM
   =================================================================== */
function logAnalyticsEvent(eventType, data = {}) {
    const analyticsData = {
        event_type: eventType,
        page_url: window.location.href,
        data: JSON.stringify(data),
        session_id: sessionId
    };

    fetch('analytics.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(analyticsData)
    }).catch(err => console.log('Analytics error:', err));
}

// sendBeacon for reliable exit tracking
function beaconAnalyticsEvent(eventType, data = {}) {
    var payload = JSON.stringify({
        event_type: eventType,
        page_url: window.location.href,
        data: JSON.stringify(data),
        session_id: sessionId
    });
    if (navigator.sendBeacon) {
        navigator.sendBeacon('analytics.php', new Blob([payload], {type: 'application/json'}));
    } else {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'analytics.php', false);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(payload);
    }
}

// 1. PAGE VIEW + RETURN VISITOR
document.addEventListener('DOMContentLoaded', function() {
    logAnalyticsEvent('PAGE_VIEW', {});
    if (returnVisitor) {
        logAnalyticsEvent('RETURN_VISITOR', {
            is_return: true,
            visit_count: parseInt(safeLocalStorage('get', 'sb_visit_count') || '1')
        });
    }
});

// 2. SCROLL DEPTH TRACKING (debounced)
let scrollTimeout;
function checkScrollDepth() {
    const scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
    if (scrollPercent >= 25 && !scrollDepths[25]) { scrollDepths[25] = true; logAnalyticsEvent('SCROLL_DEPTH_25', {depth: 25}); }
    if (scrollPercent >= 50 && !scrollDepths[50]) { scrollDepths[50] = true; logAnalyticsEvent('SCROLL_DEPTH_50', {depth: 50}); }
    if (scrollPercent >= 75 && !scrollDepths[75]) { scrollDepths[75] = true; logAnalyticsEvent('SCROLL_DEPTH_75', {depth: 75}); }
    if (scrollPercent >= 100 && !scrollDepths[100]) { scrollDepths[100] = true; logAnalyticsEvent('SCROLL_DEPTH_100', {depth: 100}); }
}
window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(checkScrollDepth, 100);
});

/* ===================================================================
   GLOBAL PACKAGE METADATA
   Must stay in sync with the promo rows in root thankyou.php $packages
   =================================================================== */
var sbPkgMeta = {
    'starter':     {id: 'pkg_fnb_starter_72',      name: 'Buy Google Reviews - 72 Show-Up (Restaurant)',  item_category: 'Google Reviews', price: 350.00, reviews: 72},
    'growth':      {id: 'pkg_fnb_growth_96',       name: 'Buy Google Reviews - 96 Show-Up (Restaurant)',  item_category: 'Google Reviews', price: 430.00, reviews: 96},
    'performance': {id: 'pkg_fnb_performance_132', name: 'Buy Google Reviews - 132 Show-Up (Restaurant)', item_category: 'Google Reviews', price: 530.00, reviews: 132}
};

// Track last selected package for WhatsApp / floating CTA tracking
var sbLastSelectedPkg = null;

// === view_item: fires when pricing section becomes visible ===
(function() {
    var viewItemFired = false;
    function fireViewItem() {
        if (viewItemFired) return;
        var pricingEl = document.getElementById('pricing');
        if (!pricingEl) return;
        var rect = pricingEl.getBoundingClientRect();
        var windowH = window.innerHeight || document.documentElement.clientHeight;
        if (rect.top < windowH && rect.bottom > 0) {
            viewItemFired = true;
            var pkgKeys = Object.keys(sbPkgMeta);
            var items = pkgKeys.map(function(k) {
                var m = sbPkgMeta[k];
                return { item_id: m.id, item_name: m.name, item_category: m.item_category, price: m.price, quantity: 1 };
            });
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'view_item',
                ecommerce: {
                    currency: 'USD',
                    value: sbPkgMeta['growth'].price,
                    items: items
                }
            });
            logAnalyticsEvent('VIEW_ITEM', {location: 'pricing'});
        }
    }
    window.addEventListener('scroll', function() { setTimeout(fireViewItem, 150); });
    document.addEventListener('DOMContentLoaded', function() { setTimeout(fireViewItem, 500); });
})();

// === add_to_cart: fires on pricing ORDER button click ===
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-package]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var pkg = this.getAttribute('data-package');
            var meta = sbPkgMeta[pkg];
            if (!meta) return;

            sbLastSelectedPkg = pkg;

            // dataLayer: add_to_cart (GA4 standard)
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'add_to_cart',
                ecommerce: {
                    currency: 'USD',
                    value: meta.price,
                    items: [{
                        item_id: meta.id,
                        item_name: meta.name,
                        item_category: meta.item_category,
                        price: meta.price,
                        quantity: 1
                    }]
                }
            });

            // Facebook Pixel - Lead (fallback; GTM is primary)
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {content_name: meta.name});
            }
            // TikTok Pixel - SubmitForm
            if (typeof ttq !== 'undefined') {
                ttq.track('SubmitForm', {content_name: meta.name});
            }

            // dataLayer: generate_lead
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'Pricing Click',
                value: meta.price,
                currency: 'USD'
            });

            logAnalyticsEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {
                package: pkg,
                location: 'pricing'
            });
        });
    });

    // === generate_lead: WhatsApp CTA clicks ===
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"], a[href*="api.whatsapp"]').forEach(function(waBtn) {
        waBtn.addEventListener('click', function() {
            var waMeta = sbLastSelectedPkg ? sbPkgMeta[sbLastSelectedPkg] : null;
            var waValue = waMeta ? waMeta.price : 0;
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'WhatsApp',
                value: waValue,
                currency: 'USD',
                content_name: waMeta ? waMeta.name : 'No package selected'
            });
        });
    });
});

// 3. CLICK HEATMAP (interactive elements only)
document.addEventListener('click', function(e) {
    const target = e.target;
    const tagName = target.tagName.toLowerCase();
    const interactiveElements = ['a', 'button', 'input', 'select', 'textarea'];
    if (interactiveElements.includes(tagName) || target.onclick || target.closest('a') || target.closest('button')) {
        logAnalyticsEvent('CLICK_HEATMAP', {
            x: e.clientX,
            y: e.clientY,
            element: tagName
        });
    }
});

// 4. TIME ON PAGE & EXIT TRACKING
var sbLastClickedUrl = '';
document.addEventListener('click', function(ev) {
    var anchor = ev.target.closest('a');
    if (anchor && anchor.href) { sbLastClickedUrl = anchor.href; }
});

window.addEventListener('beforeunload', function() {
    const timeSpent = Math.floor((Date.now() - pageLoadTime) / 1000);
    beaconAnalyticsEvent('TIME_ON_PAGE', {duration: timeSpent});
    beaconAnalyticsEvent('EXIT_PAGE', {
        exit_url: sbLastClickedUrl || 'tab_close_or_back',
        time_spent: timeSpent
    });
});

// 5. EXTERNAL LINK TRACKING (nav & footer)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('nav a').forEach(function(link) {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#')) {
                logAnalyticsEvent('EXTERNAL_LINK_CLICK', {location: 'header', url: href, text: this.textContent.trim()});
            }
        });
    });
    document.querySelectorAll('footer a').forEach(function(link) {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            logAnalyticsEvent('EXTERNAL_LINK_CLICK', {location: 'footer', url: href, text: this.textContent.trim()});
        });
    });
});

// 6. SMOOTH SCROLL (with history.pushState for back button)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
                if (history.pushState) { history.pushState(null, null, targetId); }
            }
        });
    });
});

// 7. COUNTDOWN TIMER (daily reset)
function updateCountdown() {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);

    const diff = tomorrow - now;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    const h = String(hours).padStart(2, '0');
    const m = String(minutes).padStart(2, '0');
    const s = String(seconds).padStart(2, '0');

    // Order form ticket
    var ofH = document.getElementById('ofH');
    var ofM = document.getElementById('ofM');
    var ofS = document.getElementById('ofS');
    if (ofH) ofH.textContent = h;
    if (ofM) ofM.textContent = m;
    if (ofS) ofS.textContent = s;

    // Announcement bar
    var abH = document.getElementById('abtH');
    var abM = document.getElementById('abtM');
    var abS = document.getElementById('abtS');
    if (abH) abH.textContent = h;
    if (abM) abM.textContent = m;
    if (abS) abS.textContent = s;

    // Sticky bottom CTA
    var sctH = document.getElementById('sctHours');
    var sctM = document.getElementById('sctMins');
    var sctS = document.getElementById('sctSecs');
    if (sctH) sctH.textContent = h;
    if (sctM) sctM.textContent = m;
    if (sctS) sctS.textContent = s;
}
updateCountdown();
setInterval(updateCountdown, 1000);

/* ===================================================================
   ORDER FORM
   =================================================================== */
function selectPkg(el) {
    document.querySelectorAll('.of-pkg').forEach(function(p) {
        p.classList.remove('selected');
        p.querySelector('input').checked = false;
    });
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    safeLocalStorage('set', 'sb_form_pkg', el.getAttribute('data-pkg'));
}

function preSelectPkg(pkg) {
    setTimeout(function() {
        var card = document.querySelector('.of-pkg[data-pkg="' + pkg + '"]');
        if (card) selectPkg(card);
    }, 300);
}

// ===== AUTO-SAVE & RESTORE FORM =====
(function() {
    function saveFormField(key, value) {
        safeLocalStorage('set', 'sb_form_' + key, value);
    }

    function restoreForm() {
        var biz = safeLocalStorage('get', 'sb_form_biz');
        var wa = safeLocalStorage('get', 'sb_form_wa');
        var email = safeLocalStorage('get', 'sb_form_email');
        var pkg = safeLocalStorage('get', 'sb_form_pkg');

        if (biz) document.getElementById('ofBizName').value = biz;
        if (wa) document.getElementById('ofWhatsapp').value = wa;
        if (email) document.getElementById('ofEmail').value = email;
        if (pkg) {
            var card = document.querySelector('.of-pkg[data-pkg="' + pkg + '"]');
            if (card) selectPkg(card);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        restoreForm();

        var bizInput = document.getElementById('ofBizName');
        var waInput = document.getElementById('ofWhatsapp');
        var emailInput = document.getElementById('ofEmail');
        if (bizInput) bizInput.addEventListener('input', function() { saveFormField('biz', this.value); });
        if (waInput) waInput.addEventListener('input', function() { saveFormField('wa', this.value); });
        if (emailInput) emailInput.addEventListener('input', function() { saveFormField('email', this.value); });
    });
})();

function submitOrder() {
    var biz = document.getElementById('ofBizName').value.trim();
    var wa = document.getElementById('ofWhatsapp').value.trim();
    var email = document.getElementById('ofEmail').value.trim();
    var pkg = document.querySelector('.of-pkg.selected');

    if (!biz) { alert('Please enter your Google Business name.'); return; }
    if (!wa) { alert('Please enter your WhatsApp number.'); return; }
    if (!pkg) { alert('Please select a package.'); return; }

    var pkgName = pkg.querySelector('.of-pkg-name').textContent.replace(/POPULAR/g, '').trim();
    var pkgPrice = pkg.querySelector('.of-pkg-price').textContent.trim();
    var pkgValue = pkg.getAttribute('data-pkg');

    var meta = sbPkgMeta[pkgValue] || sbPkgMeta['growth'];
    var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

    // Parse first/last name from business name
    var nameParts = biz.split(' ');
    var firstName = nameParts[0] || '';
    var lastName = nameParts.slice(1).join(' ') || '';

    // === dataLayer: begin_checkout (GA4 standard) + user_data ===
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'begin_checkout',
        user_data: {
            email: email || '',
            phone_number: wa,
            first_name: firstName,
            last_name: lastName
        },
        ecommerce: {
            currency: 'USD',
            value: meta.price,
            items: [{
                item_id: meta.id,
                item_name: meta.name,
                item_category: meta.item_category,
                price: meta.price,
                quantity: 1
            }]
        }
    });

    // === dataLayer: add_payment_info (GA4 standard, same click) ===
    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: 'add_payment_info',
        ecommerce: {
            currency: 'USD',
            value: meta.price,
            payment_type: 'Credit Card',
            items: [{
                item_id: meta.id,
                item_name: meta.name,
                item_category: meta.item_category,
                price: meta.price,
                quantity: 1
            }]
        }
    });

    // === LocalStorage bridge: persist user_data for purchase event on gateway return ===
    localStorage.setItem('sb_user_email', email || '');
    localStorage.setItem('sb_user_phone', wa || '');
    localStorage.setItem('sb_user_fname', firstName || '');
    localStorage.setItem('sb_user_lname', lastName || '');
    localStorage.setItem('sb_txn_id', txnId);
    localStorage.setItem('sb_pkg', pkgValue);

    // Facebook Pixel - InitiateCheckout (fallback)
    if (typeof fbq !== 'undefined') {
        fbq('track', 'InitiateCheckout', {
            value: meta.price,
            currency: 'USD',
            content_name: pkgName,
            content_type: 'product',
            content_ids: [pkgValue]
        });
    }
    // TikTok Pixel - InitiateCheckout (fallback)
    if (typeof ttq !== 'undefined') {
        ttq.track('InitiateCheckout', {
            value: meta.price,
            currency: 'USD',
            content_name: pkgName
        });
    }

    // Track order submission in analytics
    logAnalyticsEvent('ORDER_SUBMIT', {
        package: pkgName,
        price: pkgPrice,
        business: biz,
        location: 'order_form'
    });

    // Log customer data to customer_data.log via log.php (FORM SUBMISSIONS ONLY)
    fetch('log.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            businessName: biz,
            whatsapp: wa,
            businessEmail: email,
            action: pkgValue.toUpperCase(),
            pageUrl: window.location.href
        })
    }).catch(function(err) { console.log('Customer log error:', err); });

    // Clear saved form data
    safeLocalStorage('set', 'sb_form_biz', '');
    safeLocalStorage('set', 'sb_form_wa', '');
    safeLocalStorage('set', 'sb_form_email', '');
    safeLocalStorage('set', 'sb_form_pkg', '');

    // Commas (ex-Fanbasis) payment links per package — redirect directly to gateway.
    // Commas redirects back to https://smart-buzzer.com/thankyou.php after payment,
    // which fires the GA4 purchase event. Keep prices in sync with sbPkgMeta above.
    var commasLinks = {
        'starter':     'https://commas.com/checkout/p7jpyGkNIk6xfpo',  // 72 reviews — $350  ⚠ gateway link still priced $360
        'growth':      'https://commas.com/checkout/ZvwDEINbU6l8JO',   // 96 reviews — $430
        'performance': 'https://commas.com/checkout/p7jWybkR5uYrIkEj'  // 132 reviews — $530
    };

    var paymentUrl = commasLinks[pkgValue] || commasLinks['growth'];
    window.location.href = paymentUrl;
}

/* ===================================================================
   FAQ ACCORDION
   =================================================================== */
window.toggleFaq = function(btn) {
    var item = btn.parentElement;
    var wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(function(i) { i.classList.remove('open'); });
    if (!wasOpen) item.classList.add('open');
};

/* ===================================================================
   IMAGE MODAL (click-to-zoom on proof screenshots)
   =================================================================== */
(function() {
    var modal = document.getElementById('imageModal');
    var modalImg = document.getElementById('modalImage');
    var modalClose = document.getElementById('modalClose');
    if (!modal || !modalImg) return;

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('img[data-preview]').forEach(function(img) {
        img.addEventListener('click', function() {
            modalImg.src = this.src;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });
})();

/* ===================================================================
   ONBOARDING MODAL — "what happens after you place your order"
   =================================================================== */
window.openOnboarding = function() {
    var m = document.getElementById('obModal');
    if (!m) return;
    m.classList.add('active');
    document.body.style.overflow = 'hidden';
    if (typeof logAnalyticsEvent === 'function') {
        logAnalyticsEvent('ONBOARDING_MODAL_OPEN', {location: 'pricing_trust_strip'});
    }
};
window.closeOnboarding = function() {
    var m = document.getElementById('obModal');
    if (!m) return;
    m.classList.remove('active');
    document.body.style.overflow = '';
};
(function() {
    var m = document.getElementById('obModal');
    if (!m) return;
    m.addEventListener('click', function(e) { if (e.target === m) closeOnboarding(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && m.classList.contains('active')) closeOnboarding();
    });
})();

/* ===================================================================
   FLOATING WHATSAPP WIDGET
   =================================================================== */
(function() {
    var trigger = document.getElementById('waTrigger');
    var box = document.getElementById('waChatBox');
    if (!trigger || !box) return;
    var open = false;

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        open = !open;
        box.classList.toggle('active', open);
    });
    document.addEventListener('click', function(e) {
        if (open && !e.target.closest('#waFloat')) {
            open = false;
            box.classList.remove('active');
        }
    });
})();

/* ===================================================================
   SOCIAL PROOF TOAST
   =================================================================== */
(function() {
    var toastData = [
        {name: 'Marco from Brooklyn',   action: 'ordered 96 reviews',  time: '2 hours ago',  color: '#D42B2B'},
        {name: 'Rosa from Phoenix',     action: 'ordered 132 reviews', time: '4 hours ago',  color: '#B22222'},
        {name: 'Kenji from Seattle',    action: 'ordered 72 reviews',  time: '5 hours ago',  color: '#2D8A4E'},
        {name: 'Anita from New York',   action: 'ordered 96 reviews',  time: '7 hours ago',  color: '#D42B2B'},
        {name: 'Gino from Newark',      action: 'ordered 132 reviews', time: '9 hours ago',  color: '#B22222'},
        {name: 'Mike from Austin',      action: 'ordered 96 reviews',  time: '12 hours ago', color: '#2D8A4E'},
        {name: 'Kira from Portland',    action: 'ordered 72 reviews',  time: '1 day ago',    color: '#D42B2B'},
        {name: 'Maria from Denver',     action: 'ordered 132 reviews', time: '1 day ago',    color: '#B22222'}
    ];
    var toastEl = document.getElementById('spToast');
    var textEl = document.getElementById('spToastText');
    var avatarEl = document.getElementById('spToastAvatar');
    if (!toastEl || !textEl) return;

    var idx = 0;
    var closed = false;

    function showToast() {
        if (closed) return;
        var d = toastData[idx % toastData.length];
        textEl.innerHTML = '<strong>' + d.name + '</strong>' +
                           '<span class="sp-action">just ' + d.action + '</span>' +
                           '<span class="sp-time">' + d.time + '</span>';
        if (avatarEl) {
            avatarEl.style.background = d.color;
            avatarEl.childNodes[0].textContent = d.name.charAt(0);
        }
        toastEl.classList.add('visible');
        setTimeout(function() {
            toastEl.classList.remove('visible');
            idx++;
        }, 5000);
    }

    window.closeToast = function() {
        closed = true;
        toastEl.classList.remove('visible');
    };

    setTimeout(function() {
        showToast();
        setInterval(showToast, 25000);
    }, 8000);
})();

/* ===================================================================
   STICKY BOTTOM CTA — appears once the visitor scrolls past pricing
   =================================================================== */
(function() {
    var bar = document.getElementById('stickyCta');
    var pricing = document.getElementById('pricing');
    var form = document.getElementById('order-form');
    if (!bar || !pricing) return;

    function checkSticky() {
        var pastPricing = pricing.getBoundingClientRect().bottom < 0;
        // Hide again while the order form itself is on screen — no point nagging there
        var formVisible = false;
        if (form) {
            var fr = form.getBoundingClientRect();
            formVisible = fr.top < (window.innerHeight || 0) && fr.bottom > 0;
        }
        bar.classList.toggle('visible', pastPricing && !formVisible);
    }

    var t;
    window.addEventListener('scroll', function() {
        clearTimeout(t);
        t = setTimeout(checkSticky, 50);
    });
    checkSticky();
})();
</script>

</body>
</html>
