<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine Special - Smart Buzzer | Boost Your Google Reviews</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
    <!-- End Google Tag Manager -->

    <!-- Meta Pixel: managed via GTM (tag: FB - Pageview, All Pages) -->

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

    <style>
        :root {
            --pink: #FBBDC6;
            --pink-light: #FDE8EC;
            --pink-pale: #FFF5F7;
            --pink-deep: #F2899A;
            --red: #EC263B;
            --red-dark: #C91E30;
            --red-glow: rgba(236,38,59,0.35);
            --white: #FFFFFF;
            --black: #171717;
            --black-light: #222222;
            --gray-100: #F7F7F7;
            --gray-200: #EEEEEE;
            --gray-300: #DDDDDD;
            --gray-500: #888888;
            --gray-600: #666666;
            --gray-700: #444444;
            --green: #22c55e;
            --green-light: #dcfce7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--white);
            color: var(--black);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* FLOATING HEARTS */
        .hearts-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 0; overflow: hidden;
        }
        .floating-heart {
            position: absolute; bottom: -60px; font-size: 20px; opacity: 0;
            animation: floatHeart linear infinite;
        }
        @keyframes floatHeart {
            0% { transform: translateY(0) translateX(0) rotate(0deg) scale(0.6); opacity: 0; }
            10% { opacity: 0.5; }
            50% { opacity: 0.3; transform: translateY(-50vh) translateX(30px) rotate(180deg) scale(1); }
            90% { opacity: 0.1; }
            100% { transform: translateY(-110vh) translateX(-20px) rotate(360deg) scale(0.8); opacity: 0; }
        }

        /* HEADER */
        .header {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(0,0,0,0.06); padding: 16px 24px;
        }
        .header-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logo { display: flex; align-items: center; text-decoration: none; }
        .logo img { height: 36px; width: auto; }
        .header-nav { display: flex; align-items: center; gap: 32px; }
        .header-nav a {
            color: var(--gray-600); text-decoration: none; font-size: 14px; font-weight: 500;
            transition: color 0.2s; position: relative;
        }
        .header-nav a::after {
            content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px;
            background: var(--red); transition: width 0.3s ease;
        }
        .header-nav a:hover { color: var(--black); }
        .header-nav a:hover::after { width: 100%; }
        .header-cta {
            background: var(--red); color: var(--white); padding: 10px 28px;
            border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px;
            transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .header-cta::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .header-cta:hover { background: var(--red-dark); transform: translateY(-1px); box-shadow: 0 4px 15px var(--red-glow); }
        .header-cta:hover::before { left: 100%; }

        /* COUNTDOWN */
        .countdown-bar {
            position: fixed; top: 69px; width: 100%; z-index: 999;
            background: var(--black); padding: 10px 24px;
        }
        .countdown-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; justify-content: center; align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .countdown-label { color: var(--pink); font-size: 13px; font-weight: 600; letter-spacing: 0.5px; }

        .countdown-timer { display: flex; gap: 6px; }
        .cd-block {
            background: var(--red); color: var(--white);
            padding: 4px 10px; border-radius: 6px; text-align: center; min-width: 44px;
            animation: cdPulse 2s ease-in-out infinite;
        }
        @keyframes cdPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(236,38,59,0.4); }
            50% { box-shadow: 0 0 12px 4px rgba(236,38,59,0.2); }
        }
        .cd-num { font-weight: 700; font-size: 16px; display: block; line-height: 1.2; }
        .cd-unit { font-size: 9px; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.5px; }

        /* HERO */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            padding: 180px 24px 100px; position: relative;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(251,189,198,0.25) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(236,38,59,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(253,232,236,0.4) 0%, transparent 50%);
            overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -120px; right: -120px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(251,189,198,0.3) 0%, transparent 70%);
            animation: heroBlob1 8s ease-in-out infinite alternate;
        }
        .hero::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(236,38,59,0.08) 0%, transparent 70%);
            animation: heroBlob2 10s ease-in-out infinite alternate;
        }
        @keyframes heroBlob1 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-40px,30px) scale(1.1); } }
        @keyframes heroBlob2 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,-20px) scale(1.15); } }

        .hero-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 60px;
            align-items: center; width: 100%; position: relative; z-index: 2;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--pink); color: var(--red);
            padding: 8px 20px; border-radius: 50px; font-size: 13px; font-weight: 600;
            margin-bottom: 40px; animation: fadeSlideIn 0.8s ease forwards;
        }
        .hero-badge-dot { width: 6px; height: 6px; background: var(--red); border-radius: 50%; animation: blink 1.5s ease-in-out infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
        @keyframes fadeSlideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .hero h1 {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(3.5rem, 7vw, 6rem); font-weight: 400;
            line-height: 1.05; margin-bottom: 32px; letter-spacing: -1px;
            animation: fadeSlideIn 1s ease forwards 0.2s; opacity: 0;
        }
        .hero h1 .love-wrap { position: relative; display: inline-block; color: var(--red); font-style: italic; }
        .hero h1 .love-wrap svg {
            position: absolute; top: -12px; left: -18px;
            width: calc(100% + 36px); height: calc(100% + 24px); pointer-events: none;
        }
        .hero h1 .love-wrap svg ellipse {
            fill: none; stroke: var(--red); stroke-width: 2;
            stroke-dasharray: 500; stroke-dashoffset: 500;
            animation: drawCircle 1.5s ease forwards 0.8s;
        }
        @keyframes drawCircle { to { stroke-dashoffset: 0; } }

        .hero-subtitle { font-size: 1.15rem; color: var(--gray-600); line-height: 1.7; margin-bottom: 48px; max-width: 480px; animation: fadeSlideIn 1s ease forwards 0.4s; opacity: 0; }
        .hero-cta-group { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; animation: fadeSlideIn 1s ease forwards 0.6s; opacity: 0; }

        .btn-red {
            background: var(--red); color: var(--white); padding: 16px 36px;
            border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 15px;
            transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;
            border: none; cursor: pointer; position: relative; overflow: hidden;
        }
        .btn-red::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            animation: btnShimmer 3s ease-in-out infinite;
        }
        @keyframes btnShimmer { 0% { left: -100%; } 50% { left: 100%; } 100% { left: 100%; } }
        .btn-red:hover { background: var(--red-dark); transform: translateY(-2px); box-shadow: 0 8px 30px var(--red-glow); }
        .btn-outline {
            background: transparent; color: var(--black); padding: 16px 36px;
            border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 15px;
            transition: all 0.3s ease; border: 2px solid var(--gray-300);
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-outline:hover { border-color: var(--black); background: var(--gray-100); transform: translateY(-2px); }
        .btn-wa {
            background: transparent; color: var(--black); padding: 16px 36px;
            border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 15px;
            transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;
            border: 2px solid var(--gray-300); cursor: pointer;
        }
        .btn-wa:hover { border-color: var(--black); background: var(--gray-100); transform: translateY(-2px); }

        .hero-visual { animation: fadeSlideIn 1.2s ease forwards 0.5s; opacity: 0; }
        .hero-card-main {
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-light) 100%);
            border-radius: 24px; padding: 40px; position: relative; z-index: 2;
            box-shadow: 0 20px 60px rgba(251,189,198,0.3);
        }
        .hero-stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .hero-stat { background: var(--white); border-radius: 16px; padding: 24px; text-align: center; transition: transform 0.3s; }
        .hero-stat:hover { transform: translateY(-4px); }
        .hero-stat-number { font-family: 'Instrument Serif', serif; font-size: 2.8rem; font-weight: 400; color: var(--black); line-height: 1; }
        .hero-stat-number.red { color: var(--red); }
        .hero-stat-label { font-size: 13px; color: var(--gray-600); margin-top: 6px; }
        .hero-float-card {
            position: absolute; bottom: -30px; right: -30px;
            background: var(--black); color: var(--white);
            padding: 20px 28px; border-radius: 16px; z-index: 3;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            animation: floatBounce 3s ease-in-out infinite;
        }
        @keyframes floatBounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .hero-float-card .big { font-family: 'Instrument Serif', serif; font-size: 2rem; color: var(--pink); }
        .hero-float-card .small { font-size: 12px; color: var(--gray-500); margin-top: 2px; }

        /* TRUST BAR */
        .trust-bar {
            background: var(--black); padding: 48px 24px; position: relative; z-index: 2;
        }
        .trust-bar-grid {
            max-width: 1000px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; text-align: center;
        }
        .trust-item { position: relative; }
        .trust-item::after {
            content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            width: 1px; height: 40px; background: rgba(255,255,255,0.1);
        }
        .trust-item:last-child::after { display: none; }
        .trust-number {
            font-family: 'Instrument Serif', serif;
            font-size: 2.8rem; font-weight: 400; color: var(--pink); line-height: 1;
        }
        .trust-number.green { color: var(--green); }
        .trust-label { font-size: 13px; color: var(--gray-500); margin-top: 6px; }

        /* WAVE DIVIDERS */
        .wave-divider { width: 100%; overflow: hidden; line-height: 0; position: relative; z-index: 1; }
        .wave-divider svg { display: block; width: 100%; height: 60px; }

        /* SECTION SHARED */
        .section { padding: 120px 24px; position: relative; }
        .section-header { text-align: center; margin-bottom: 64px; }
        .section-tag {
            display: inline-flex; align-items: center; gap: 8px;
            border: 1.5px solid var(--gray-300); padding: 8px 20px; border-radius: 50px;
            font-size: 13px; font-weight: 600; color: var(--gray-600);
            margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px;
        }
        .section-tag-red { border-color: var(--red); color: var(--red); background: var(--pink-pale); }
        .section-tag-green { border-color: var(--green); color: var(--green); background: var(--green-light); }
        .section-header h2 {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2.2rem, 4.5vw, 3.5rem); font-weight: 400; color: var(--black); line-height: 1.15;
        }
        .section-header h2 em { font-style: italic; color: var(--red); }
        .section-header p { color: var(--gray-600); font-size: 1.1rem; max-width: 520px; margin: 20px auto 0; line-height: 1.6; }

        /* BEFORE / AFTER */
        .ba-grid { display: grid; grid-template-columns: 1fr 80px 1fr; gap: 0; align-items: stretch; max-width: 900px; margin: 0 auto; }
        .ba-card {
            border-radius: 24px; padding: 40px 32px; text-align: center;
            transition: transform 0.3s;
        }
        .ba-card:hover { transform: translateY(-4px); }
        .ba-card.before { background: #FEF2F2; border: 2px solid #FECACA; }
        .ba-card.after { background: #F0FDF4; border: 2px solid #BBF7D0; }
        .ba-card-tag {
            display: inline-block; padding: 6px 18px; border-radius: 50px;
            font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .ba-card.before .ba-card-tag { background: #FCA5A5; color: #7F1D1D; }
        .ba-card.after .ba-card-tag { background: #86EFAC; color: #14532D; }
        .ba-stars { font-size: 2rem; margin-bottom: 12px; }
        .ba-rating {
            font-family: 'Instrument Serif', serif;
            font-size: 4rem; line-height: 1; margin-bottom: 8px;
        }
        .ba-card.before .ba-rating { color: #DC2626; }
        .ba-card.after .ba-rating { color: #16A34A; }
        .ba-label { font-size: 0.95rem; color: var(--gray-600); }
        .ba-label strong { color: var(--black); }
        .ba-arrow {
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: var(--red);
        }
        .ba-bottom { text-align: center; margin-top: 40px; }
        .ba-bottom p { font-size: 1.05rem; color: var(--gray-600); }
        .ba-bottom strong { color: var(--black); }

        /* BENEFITS DARK (How It Works) */
        .benefits-section { background: var(--black); padding: 120px 24px; position: relative; overflow: hidden; }
        .benefits-section::before {
            content: ''; position: absolute; top: -200px; right: -200px; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(236,38,59,0.12) 0%, transparent 70%);
            animation: heroBlob1 12s ease-in-out infinite alternate;
        }
        .benefits-section::after {
            content: ''; position: absolute; bottom: -150px; left: -150px; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(251,189,198,0.08) 0%, transparent 70%);
            animation: heroBlob2 15s ease-in-out infinite alternate;
        }
        .benefits-section .section-header h2 { color: var(--white); }
        .benefits-section .section-header p { color: var(--gray-500); }
        .benefits-section .section-tag { border-color: var(--gray-700); color: var(--pink); }

        /* STEPS PROGRESS */
        .steps-grid { display: flex; align-items: flex-start; max-width: 1000px; margin: 0 auto; position: relative; z-index: 1; }
        .step-item { flex: 1; text-align: center; position: relative; padding: 0 20px; }
        .step-circle-wrap { position: relative; display: flex; justify-content: center; margin-bottom: 28px; }
        .step-circle {
            width: 72px; height: 72px; border-radius: 50%;
            background: rgba(236,38,59,0.15); border: 2px solid var(--red);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Instrument Serif', serif; font-size: 1.8rem; color: var(--pink);
            position: relative; z-index: 2; transition: all 0.4s;
        }
        .step-item:hover .step-circle {
            background: var(--red); color: var(--white);
            box-shadow: 0 0 30px rgba(236,38,59,0.4); transform: scale(1.1);
        }
        .step-line {
            position: absolute; top: 36px; left: calc(50% + 36px);
            width: calc(100% - 72px); height: 2px;
            background: linear-gradient(90deg, var(--red), rgba(236,38,59,0.3));
            z-index: 1;
        }
        .step-line::after {
            content: ''; position: absolute; top: -3px; right: 0;
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--red); opacity: 0.5;
        }
        .step-item:last-child .step-line { display: none; }
        .step-item h3 { font-size: 1.05rem; font-weight: 600; color: var(--white); margin-bottom: 10px; }
        .step-item p { font-size: 0.9rem; color: var(--gray-500); line-height: 1.55; max-width: 260px; margin: 0 auto; }

        @media (max-width: 768px) {
            .steps-grid { flex-direction: column; gap: 40px; align-items: center; }
            .step-line { display: none !important; }
            .step-item { padding: 0; }
        }

        /* PRICING */
        .pricing-section { background: var(--gray-100); }
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; max-width: 1100px; margin: 0 auto; }
        .pricing-card { background: var(--white); border-radius: 24px; padding: 40px 32px; text-align: center; position: relative; border: 2px solid var(--gray-200); transition: all 0.4s; }
        .pricing-card:hover { border-color: var(--pink); transform: translateY(-6px); box-shadow: 0 20px 50px rgba(0,0,0,0.08); }
        .pricing-card.featured { border: 2px solid var(--red); box-shadow: 0 8px 30px rgba(236,38,59,0.1); }
        .pricing-card.featured:hover { transform: translateY(-10px); box-shadow: 0 24px 60px rgba(236,38,59,0.2); }
        .pricing-popular { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--red); color: var(--white); padding: 6px 24px; border-radius: 50px; font-size: 12px; font-weight: 600; letter-spacing: 0.5px; white-space: nowrap; animation: cdPulse 2s ease-in-out infinite; }
        .pricing-name { font-family: 'Instrument Serif', serif; font-size: 1.8rem; font-style: italic; color: var(--black); margin-bottom: 4px; }
        .pricing-reviews { color: var(--gray-500); font-size: 0.9rem; margin-bottom: 28px; }
        .pricing-original { font-size: 1.1rem; color: var(--gray-500); text-decoration: line-through; margin-bottom: 4px; }
        .pricing-current { font-family: 'Instrument Serif', serif; font-size: 3.2rem; color: var(--black); line-height: 1; }
        .pricing-per { font-size: 0.85rem; color: var(--gray-500); margin-top: 4px; }
        .pricing-badges { display: flex; gap: 8px; justify-content: center; margin: 20px 0 28px; flex-wrap: wrap; }
        .badge-discount { background: var(--red); color: var(--white); padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 700; }
        .badge-guarantee { background: var(--pink); color: var(--red); padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; }
        .badge-save { background: var(--black); color: var(--white); padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; }
        .pricing-features { list-style: none; margin-bottom: 32px; text-align: left; }
        .pricing-features li { padding: 10px 0; border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; gap: 12px; font-size: 0.9rem; color: var(--gray-700); }
        .pricing-features li:last-child { border-bottom: none; }
        .pf-check { width: 20px; height: 20px; background: var(--pink-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .pricing-btn { width: 100%; padding: 16px; border-radius: 50px; font-weight: 600; font-size: 15px; cursor: pointer; text-decoration: none; display: block; text-align: center; transition: all 0.3s; border: none; position: relative; overflow: hidden; }
        .pricing-card.featured .pricing-btn { background: var(--red); color: var(--white); }
        .pricing-card.featured .pricing-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); animation: btnShimmer 3s ease-in-out infinite; }
        .pricing-card.featured .pricing-btn:hover { background: var(--red-dark); transform: scale(1.03); box-shadow: 0 6px 20px var(--red-glow); }
        .pricing-card:not(.featured) .pricing-btn { background: var(--white); color: var(--black); border: 2px solid var(--black); }
        .pricing-card:not(.featured) .pricing-btn:hover { background: var(--black); color: var(--white); }



        /* TESTIMONIALS */
        .testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 1100px; margin: 0 auto; }
        .testimonial-card {
            background: var(--white); border-radius: 20px; padding: 36px 28px;
            border: 1px solid var(--gray-200); transition: all 0.3s; position: relative;
        }
        .testimonial-card:hover { border-color: var(--pink); transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.06); }
        .testimonial-stars { color: #fbbf24; font-size: 18px; margin-bottom: 16px; }
        .testimonial-text { font-size: 0.95rem; color: var(--gray-700); line-height: 1.7; margin-bottom: 20px; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 12px; }
        .testimonial-avatar {
            width: 44px; height: 44px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
        }
        .testimonial-avatar img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .testimonial-name { font-weight: 600; font-size: 0.9rem; color: var(--black); }
        .testimonial-role { font-size: 0.8rem; color: var(--gray-500); }
        .testimonial-result {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--green-light); color: #16A34A;
            padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;
            margin-top: 8px;
        }

        /* PROOF */
        .proof-card { background: var(--white); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .image-zoom-wrapper { position: relative; cursor: pointer; overflow: hidden; }
        .image-zoom-wrapper img { width: 100%; height: auto; display: block; transition: transform 0.5s ease; }
        .image-zoom-wrapper:hover img { transform: scale(1.04); }
        .zoom-hint {
            position: absolute; bottom: 16px; right: 16px;
            background: var(--black); color: var(--white);
            padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 500;
            opacity: 0; transition: all 0.3s; transform: translateY(8px);
        }
        .image-zoom-wrapper:hover .zoom-hint { opacity: 1; transform: translateY(0); }

        /* CONTENT FLEX */
        .content-flex { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .content-flex.reverse { direction: rtl; }
        .content-flex.reverse > * { direction: ltr; }
        .content-image-pink { background: linear-gradient(135deg, var(--pink) 0%, var(--pink-light) 100%); border-radius: 16px; padding: 10px; }
        .content-image-pink img { width: 100%; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); cursor: pointer; transition: transform 0.4s; }
        .content-image-pink img:hover { transform: scale(1.03); }
        .content-image img { width: 100%; border-radius: 20px; box-shadow: 0 12px 40px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.4s; }
        .content-image img:hover { transform: scale(1.03); }
        .content-text h3 { font-family: 'Instrument Serif', serif; font-size: 2rem; color: var(--black); margin-bottom: 20px; line-height: 1.2; }
        .content-text p { font-size: 1.05rem; color: var(--gray-600); line-height: 1.7; margin-bottom: 16px; }
        .content-check { display: flex; align-items: center; gap: 10px; font-size: 15px; color: var(--gray-700); }
        .content-check-icon { width: 24px; height: 24px; background: var(--pink); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--red); font-weight: 700; font-size: 13px; flex-shrink: 0; }

        /* WHY FEATURES */
        .why-features { list-style: none; }
        .why-features li { display: flex; gap: 18px; padding: 22px 24px; background: var(--white); border-radius: 16px; margin-bottom: 12px; border-left: 4px solid var(--gray-300); transition: all 0.3s; }
        .why-features li:hover { transform: translateX(6px); box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .why-features li:nth-child(1) { border-left-color: #22c55e; }
        .why-features li:nth-child(2) { border-left-color: #3b82f6; }
        .why-features li:nth-child(3) { border-left-color: #8b5cf6; }
        .why-features li:nth-child(4) { border-left-color: #f97316; }
        .why-check { width: 28px; height: 28px; background: var(--pink); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--red); font-weight: 700; font-size: 14px; flex-shrink: 0; margin-top: 2px; }
        .why-features strong { display: block; font-size: 0.95rem; color: var(--black); margin-bottom: 4px; }
        .why-features span { font-size: 0.85rem; color: var(--gray-600); }

        /* DASHBOARD */
        .dashboard-preview { background: var(--white); border-radius: 24px; padding: 16px; box-shadow: 0 8px 40px rgba(0,0,0,0.08); border: 1px solid var(--gray-200); }
        .dashboard-preview img { width: 100%; border-radius: 16px; cursor: pointer; }

        /* CLIENT MARQUEE */
        .clients-marquee-wrap { overflow: hidden; position: relative; }
        .clients-marquee-wrap::before, .clients-marquee-wrap::after { content: ''; position: absolute; top: 0; width: 100px; height: 100%; z-index: 2; }
        .clients-marquee-wrap::before { left: 0; background: linear-gradient(90deg, var(--white), transparent); }
        .clients-marquee-wrap::after { right: 0; background: linear-gradient(90deg, transparent, var(--white)); }
        .clients-marquee { display: flex; gap: 24px; width: max-content; animation: marqueeScroll 25s linear infinite; }
        .clients-marquee:hover { animation-play-state: paused; }
        @keyframes marqueeScroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .client-logo { background: var(--white); padding: 16px 24px; border-radius: 12px; text-align: center; border: 1px solid var(--gray-200); transition: all 0.3s; flex-shrink: 0; min-width: 150px; }
        .client-logo:hover { border-color: var(--pink); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .client-logo img { max-width: 100%; height: 50px; object-fit: contain; filter: grayscale(100%); opacity: 0.6; transition: all 0.3s; }
        .client-logo:hover img { filter: grayscale(0%); opacity: 1; }

        /* COMPARISON TABLE */
        .compare-table { max-width: 700px; margin: 0 auto; border-radius: 20px; overflow: hidden; border: 1px solid var(--gray-200); }
        .compare-row { display: grid; grid-template-columns: 1.2fr 1fr 1fr; align-items: center; }
        .compare-header { background: var(--black); }
        .compare-header .compare-cell { color: var(--white); font-weight: 700; font-size: 0.9rem; }
        .compare-header .compare-cell:nth-child(2) { color: #FCA5A5; }
        .compare-header .compare-cell:nth-child(3) { color: #86EFAC; }
        .compare-cell { padding: 16px 20px; font-size: 0.9rem; color: var(--gray-700); border-bottom: 1px solid var(--gray-200); }
        .compare-row:last-child .compare-cell { border-bottom: none; }
        .compare-cell.metric { font-weight: 600; color: var(--black); }
        .compare-cell.bad { color: #DC2626; }
        .compare-cell.good { color: #16A34A; font-weight: 600; }



        /* FAQ */
        .faq-container { max-width: 760px; margin: 0 auto; }
        .faq-item { background: var(--white); border-radius: 16px; margin-bottom: 12px; overflow: hidden; border: 1px solid var(--gray-200); transition: all 0.3s; }
        .faq-item:hover { border-color: var(--pink); box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
        .faq-item.active { border-color: var(--red); box-shadow: 0 4px 20px rgba(236,38,59,0.08); }
        .faq-question { width: 100%; padding: 22px 24px; background: none; border: none; text-align: left; font-size: 1rem; font-weight: 600; color: var(--black); cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-family: 'DM Sans', sans-serif; }
        .faq-icon { width: 28px; height: 28px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gray-600); font-size: 18px; transition: all 0.3s; flex-shrink: 0; }
        .faq-item.active .faq-icon { background: var(--red); color: var(--white); transform: rotate(45deg); }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
        .faq-item.active .faq-answer { max-height: 500px; }
        .faq-answer-inner { padding: 0 24px 24px; color: var(--gray-600); line-height: 1.7; font-size: 0.95rem; }



        /* CALCULATOR */
        .calc-section {
            background: linear-gradient(135deg, var(--pink-pale) 0%, var(--pink-light) 50%, var(--pink-pale) 100%);
            position: relative; overflow: hidden;
        }
        .calc-section::before {
            content: ''; position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
            width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, rgba(236,38,59,0.06) 0%, transparent 60%);
        }
        .calc-card {
            background: var(--white); border-radius: 24px; padding: 48px;
            box-shadow: 0 16px 60px rgba(0,0,0,0.08); max-width: 900px; margin: 0 auto;
            position: relative; z-index: 1;
        }
        .calc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; }
        .calc-field { margin-bottom: 20px; }
        .calc-field label {
            display: block; font-size: 0.9rem; font-weight: 600; color: var(--black);
            margin-bottom: 8px;
        }
        .calc-req { color: var(--red); }
        .calc-field input {
            width: 100%; padding: 14px 18px; border: 2px solid var(--gray-200);
            border-radius: 12px; font-size: 1rem; font-family: 'DM Sans', sans-serif;
            color: var(--black); transition: all 0.3s; outline: none;
            background: var(--gray-100);
        }
        .calc-field input:focus { border-color: var(--red); background: var(--white); box-shadow: 0 0 0 4px rgba(236,38,59,0.08); }
        .calc-field input::placeholder { color: var(--gray-500); }
        .calc-btn { margin-top: 8px; }
        .calc-result {
            background: var(--gray-100); border-radius: 20px; padding: 36px;
            min-height: 320px; display: flex; align-items: center; justify-content: center;
        }
        .calc-result-placeholder { text-align: center; }
        .calc-result-placeholder .calc-result-icon { font-size: 3rem; margin-bottom: 16px; opacity: 0.3; }
        .calc-result-placeholder p { color: var(--gray-500); font-size: 0.95rem; line-height: 1.6; max-width: 260px; }
        .calc-result-data { width: 100%; }
        .calc-result-number { text-align: center; margin-bottom: 12px; }
        .calc-big-num {
            font-family: 'Instrument Serif', serif; font-size: 4.5rem;
            color: var(--red); line-height: 1; display: block;
        }
        .calc-big-label { font-size: 0.95rem; color: var(--gray-600); font-weight: 500; }
        .calc-result-detail { text-align: center; margin-bottom: 24px; }
        .calc-result-detail p { font-size: 1rem; color: var(--gray-600); }
        .calc-rec {
            background: var(--white); border: 2px solid var(--red); border-radius: 16px;
            padding: 20px; text-align: center; margin-bottom: 20px;
            position: relative;
        }
        .calc-rec-tag {
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: var(--red); color: var(--white); padding: 4px 16px;
            border-radius: 50px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;
        }
        .calc-rec-name {
            font-family: 'Instrument Serif', serif; font-size: 1.6rem;
            font-style: italic; color: var(--black); margin-top: 8px;
        }
        .calc-rec-info { font-size: 0.9rem; color: var(--gray-600); margin-top: 4px; }
        .calc-rec-save {
            display: inline-block; background: var(--pink); color: var(--red);
            padding: 4px 14px; border-radius: 50px; font-size: 12px; font-weight: 700;
            margin-top: 8px;
        }
        .calc-error {
            color: var(--red); font-size: 0.85rem; margin-top: 6px; display: none;
        }
        .calc-error.show { display: block; }
        @media (max-width: 768px) {
            .calc-grid { grid-template-columns: 1fr; gap: 32px; }
            .calc-card { padding: 28px 20px; }
            .calc-big-num { font-size: 3.5rem; }
        }

        /* FOOTER */
        .footer { background: var(--black); color: var(--white); position: relative; }
        .footer-border-top { height: 20px; background: linear-gradient(90deg, var(--pink), var(--red), var(--pink-deep), var(--red), var(--pink)); background-size: 200% 100%; animation: footerBorderShift 6s ease-in-out infinite; }
        @keyframes footerBorderShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .footer-content { padding: 64px 24px 32px; }
        .footer-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand img { height: 44px; margin-bottom: 16px; filter: brightness(0) invert(1); }
        .footer-brand p { color: var(--gray-500); font-size: 0.9rem; line-height: 1.6; margin-bottom: 8px; }
        .footer-brand .sub { color: #555; font-size: 0.85rem; font-style: italic; margin-bottom: 20px; }
        .footer-socials { display: flex; gap: 12px; margin-top: 16px; }
        .footer-social-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; }
        .footer-social-icon svg { width: 18px; height: 18px; fill: var(--gray-500); transition: fill 0.3s; }
        .footer-social-icon:hover { background: var(--red); border-color: var(--red); transform: translateY(-3px); box-shadow: 0 6px 15px rgba(236,38,59,0.3); }
        .footer-social-icon:hover svg { fill: var(--white); }
        .footer h4 { font-size: 0.9rem; font-weight: 600; margin-bottom: 20px; color: var(--pink); text-transform: uppercase; letter-spacing: 1px; }
        .footer-links a { display: block; color: #aaa; text-decoration: none; margin-bottom: 12px; font-size: 0.9rem; transition: all 0.2s; padding-left: 0; }
        .footer-links a:hover { color: var(--white); padding-left: 6px; }
        .footer-contact a { display: flex; align-items: center; gap: 10px; color: var(--pink); text-decoration: none; margin-bottom: 14px; font-size: 0.9rem; transition: color 0.2s; }
        .footer-contact a:hover { color: var(--white); }
        .footer-divider { max-width: 1200px; margin: 0 auto; height: 1px; background: linear-gradient(90deg, transparent, #333, transparent); }
        .footer-bottom { max-width: 1200px; margin: 0 auto; padding: 24px 24px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-bottom p { color: #555; font-size: 0.85rem; }
        .footer-love { font-size: 0.85rem; color: #555; }
        .footer-love span { color: var(--red); animation: heartBeat 1.2s ease-in-out infinite; display: inline-block; }
        @keyframes heartBeat { 0%,100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        /* IMAGE MODAL */
        .image-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.92); z-index: 10000; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(8px); }
        .image-modal.active { display: flex; }
        .image-modal img { max-width: 90%; max-height: 90%; border-radius: 12px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .modal-close { position: absolute; top: 24px; right: 32px; width: 44px; height: 44px; background: rgba(255,255,255,0.1); border: none; border-radius: 50%; color: white; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .modal-close:hover { background: rgba(255,255,255,0.2); transform: rotate(90deg); }

        /* FLOATING WHATSAPP */
        .wa-float { position: fixed; bottom: 80px; right: 24px; z-index: 998; display: flex; align-items: center; gap: 10px; flex-direction: row-reverse; }
        .wa-float-btn { width: 56px; height: 56px; border-radius: 50%; background: #25d366; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 28px; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: all 0.3s; animation: waPulse 2s ease-in-out infinite; }
        @keyframes waPulse { 0%,100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); } 50% { box-shadow: 0 4px 30px rgba(37,211,102,0.6), 0 0 0 12px rgba(37,211,102,0.1); } }
        .wa-float-btn:hover { transform: scale(1.1); }
        .wa-float-btn svg { width: 28px; height: 28px; fill: white; }
        .wa-float-label { background: var(--white); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; color: var(--black); box-shadow: 0 4px 15px rgba(0,0,0,0.1); opacity: 0; transform: translateX(10px); transition: all 0.3s; white-space: nowrap; }
        .wa-float:hover .wa-float-label { opacity: 1; transform: translateX(0); }

        /* SOCIAL PROOF POPUP */
        .social-proof {
            position: fixed; bottom: 80px; left: 24px; z-index: 997;
            background: var(--white); border-radius: 16px; padding: 16px 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.15); border: 1px solid var(--gray-200);
            display: flex; align-items: center; gap: 14px; max-width: 340px;
            transform: translateX(-120%); transition: transform 0.5s cubic-bezier(0.34,1.56,0.64,1);
            opacity: 0;
        }
        .social-proof.show { transform: translateX(0); opacity: 1; }
        .social-proof-icon {
            width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
            background: var(--green-light); display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .social-proof-text { font-size: 13px; color: var(--gray-700); line-height: 1.4; }
        .social-proof-text strong { color: var(--black); }
        .social-proof-time { font-size: 11px; color: var(--gray-500); margin-top: 4px; }
        .social-proof-close {
            position: absolute; top: 8px; right: 10px; background: none; border: none;
            font-size: 16px; color: var(--gray-500); cursor: pointer; line-height: 1;
        }



        /* STICKY CTA & BACK TOP */
        .sticky-cta { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--white); padding: 12px 20px; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); z-index: 998; border-top: 1px solid var(--gray-200); }
        .sticky-cta a { display: flex; justify-content: center; align-items: center; gap: 10px; background: var(--red); color: var(--white); padding: 14px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 15px; position: relative; overflow: hidden; }
        .sticky-cta a::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); animation: btnShimmer 3s ease-in-out infinite; }


        /* DESKTOP STICKY CTA BAR */
        .desktop-sticky {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 14px 24px; box-shadow: 0 -4px 30px rgba(0,0,0,0.1);
            border-top: 1px solid var(--gray-200); z-index: 998;
            transform: translateY(100%); transition: transform 0.4s ease;
            display: none;
        }
        .desktop-sticky.show { transform: translateY(0); }
        .desktop-sticky-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
        }
        .desktop-sticky-text { font-size: 15px; font-weight: 600; color: var(--black); }
        .desktop-sticky-text span { color: var(--red); }
        .desktop-sticky-btns { display: flex; gap: 12px; align-items: center; }
        .desktop-sticky .btn-red { padding: 12px 28px; font-size: 14px; }
        .desktop-sticky .btn-wa { padding: 12px 28px; font-size: 14px; border-width: 2px; }

        /* ANIMATIONS */
        .reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }

        /* RESPONSIVE */
        @media (min-width: 769px) {
            .desktop-sticky { display: block; }
        }
        @media (max-width: 1024px) {
            .pricing-grid { grid-template-columns: 1fr; max-width: 420px; }
            .testimonials-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .trust-bar-grid { grid-template-columns: repeat(2, 1fr); gap: 24px; }
            .trust-item::after { display: none; }
        }
        @media (max-width: 768px) {
            .header-nav { display: none; }
            .hero { padding-top: 160px; }
            .hero-inner { grid-template-columns: 1fr; gap: 40px; }
            .hero-float-card { bottom: -20px; right: -10px; }
            .ba-grid { grid-template-columns: 1fr; gap: 16px; }
            .ba-arrow { transform: rotate(90deg); }
            .content-flex { grid-template-columns: 1fr; gap: 40px; }
            .content-flex.reverse { direction: ltr; }
            .compare-row { grid-template-columns: 1.4fr 1fr 1fr; }
            .compare-cell { padding: 12px 14px; font-size: 0.8rem; }
            .footer-grid { grid-template-columns: 1fr; text-align: center; }
            .footer-contact a { justify-content: center; }
            .footer-socials { justify-content: center; }
            .footer-bottom { justify-content: center; text-align: center; flex-direction: column; }
            .sticky-cta { display: block; }
            .desktop-sticky { display: none !important; }
            .wa-float { bottom: 80px; right: 16px; }
            .wa-float-label { display: none; }
            .social-proof { bottom: 80px; left: 12px; max-width: 280px; }
            .wave-divider svg { height: 35px; }
        }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <!-- FLOATING HEARTS -->
    <div class="hearts-container" id="heartsContainer"></div>

    <!-- HEADER -->
    <header class="header">
        <div class="header-inner">
            <a href="https://smart-buzzer.com/" class="logo">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
            </a>
            <nav class="header-nav">
                <a href="#pricing">Pricing</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#reviews">Reviews</a>
                <a href="#faq">FAQ</a>
            </nav>
            <a href="#pricing" class="header-cta">Order Now</a>
        </div>
    </header>

    <!-- COUNTDOWN + SPOTS LEFT -->
    <div class="countdown-bar">
        <div class="countdown-inner">
            <span class="countdown-label">&#10084;&#65039; Valentine Offer Ends In</span>
            <div class="countdown-timer">
                <div class="cd-block"><span class="cd-num" id="cd-h">00</span><span class="cd-unit">Hrs</span></div>
                <div class="cd-block"><span class="cd-num" id="cd-m">00</span><span class="cd-unit">Min</span></div>
                <div class="cd-block"><span class="cd-num" id="cd-s">00</span><span class="cd-unit">Sec</span></div>
            </div>

        </div>
    </div>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-text">
                <div class="hero-badge"><span class="hero-badge-dot"></span> Valentine Special &#8212; Limited Time</div>
                <h1>Get 5-Star Ratings<br>This Valentine.<br>Your Business<br><span class="love-wrap">Deserves<svg viewBox="0 0 160 80" preserveAspectRatio="none"><ellipse cx="80" cy="40" rx="74" ry="34"/></svg></span> It.</h1>
                <p class="hero-subtitle">Boost your business visibility with authentic local Google reviews. Give your business the love it deserves this Valentine's.</p>
                <div class="hero-cta-group">
                    <a href="#pricing" class="btn-red">See Packages &#10084;</a>
                </div>
            </div>
            <div class="hero-visual">
                <div style="border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
                    <img src="https://reputationmanage.co/wp-content/uploads/2025/06/get-more-google-reviews-for-my-business.png" alt="Get More Google Reviews" style="width: 100%; display: block;">
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST BAR (Stats Counter) -->
    <div class="trust-bar">
        <div class="trust-bar-grid">
            <div class="trust-item reveal">
                <div class="trust-number"><span data-count="2000">0</span>+</div>
                <div class="trust-label">Businesses Served</div>
            </div>
            <div class="trust-item reveal reveal-delay-1">
                <div class="trust-number"><span data-count-decimal="4.9">0</span>/5</div>
                <div class="trust-label">Client Satisfaction</div>
            </div>
            <div class="trust-item reveal reveal-delay-2">
                <div class="trust-number"><span data-count-percent="90">0</span>+</div>
                <div class="trust-label">Client Retention</div>
            </div>
        </div>
    </div>

    <!-- BEFORE / AFTER TRANSFORMATION -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Real Results</div>
                <h2>See the <em>Transformation</em></h2>
                <p>Real case study from one of our dental clinic clients in Texas</p>
            </div>
            <div class="ba-grid reveal">
                <div class="ba-card before">
                    <div class="ba-card-tag">Before</div>
                    <div class="ba-stars">&#9733;&#9733;&#9734;&#9734;&#9734;</div>
                    <div class="ba-rating">2.1</div>
                    <div class="ba-label"><strong>12 reviews</strong><br>Low visibility, losing customers</div>
                </div>
                <div class="ba-arrow">&#10132;</div>
                <div class="ba-card after">
                    <div class="ba-card-tag">After 6 Weeks</div>
                    <div class="ba-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <div class="ba-rating">4.8</div>
                    <div class="ba-label"><strong>101 reviews</strong><br>Top of local search, 45% more calls</div>
                </div>
            </div>
            <div class="ba-bottom reveal">
                <p style="margin-top: 32px;"><strong>89 authentic local reviews added</strong> &#8212; zero Google penalties or flags</p>
            </div>
        </div>
    </section>

    <!-- WAVE: WHITE to BLACK -->
    <div class="wave-divider"><svg viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,0 C300,60 900,0 1200,50 L1200,60 L0,60 Z" fill="#171717"/></svg></div>

    <!-- HOW IT WORKS - STEPS -->
    <section class="benefits-section" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">How It Works</div>
                <h2>Get Started in <em>3 Easy Steps</em></h2>
                <p>Simple, transparent process from order to delivery</p>
            </div>
            <div class="steps-grid">
                <div class="step-item reveal">
                    <div class="step-circle-wrap">
                        <div class="step-circle"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
                        <div class="step-line"></div>
                    </div>
                    <h3>Place Your Order</h3>
                    <p>Choose a package and submit your order effortlessly.</p>
                </div>
                <div class="step-item reveal reveal-delay-1">
                    <div class="step-circle-wrap">
                        <div class="step-circle"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="13" y2="13"/></svg></div>
                        <div class="step-line"></div>
                    </div>
                    <h3>Approve Your Content</h3>
                    <p>Review and approve custom-written content tailored to your business.</p>
                </div>
                <div class="step-item reveal reveal-delay-2">
                    <div class="step-circle-wrap">
                        <div class="step-circle"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                    </div>
                    <h3>Watch Your Ratings Grow</h3>
                    <p>Track real-time progress as reviews are posted gradually and safely.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- WAVE: BLACK to GRAY -->
    <div class="wave-divider"><svg viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,0 L0,40 C300,0 900,60 1200,20 L1200,0 Z" fill="#171717"/></svg></div>

    <!-- PRICING -->
    <section class="section pricing-section" id="pricing">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Valentine Pricing</div>
                <h2>Choose Your <em>Package</em></h2>
                <p>Special Valentine discounts with extended guarantee</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card reveal">
                    <div class="pricing-name">Cupid</div>
                    <div class="pricing-reviews">50 Reviews</div>
                    <div><div class="pricing-original">$325.00</div><div class="pricing-current">$300</div><div class="pricing-per">$6.00 per review</div></div>
                    <div class="pricing-badges"><span class="badge-discount">8% OFF</span><span class="badge-guarantee">7-Day Guarantee</span></div>
                    <ul class="pricing-features">
                        <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                        <li><span class="pf-check">&#10003;</span> Local Names</li>
                        <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                        <li><span class="pf-check">&#10003;</span> 1-3 Reviews Per Week</li>
                        <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                    </ul>
                    <a href="#" class="pricing-btn" data-package="Cupid">Order Now &#8212; Save $25</a>
                </div>
                <div class="pricing-card featured reveal reveal-delay-1">
                    <div class="pricing-popular">&#10084; Most Popular</div>
                    <div class="pricing-name">Sweetheart</div>
                    <div class="pricing-reviews">100 Reviews</div>
                    <div><div class="pricing-original">$650.00</div><div class="pricing-current">$550</div><div class="pricing-per">$5.50 per review</div></div>
                    <div class="pricing-badges"><span class="badge-discount">15% OFF</span><span class="badge-guarantee">17-Day Guarantee</span><span class="badge-save">Save $100</span></div>
                    <ul class="pricing-features">
                        <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                        <li><span class="pf-check">&#10003;</span> Local Names</li>
                        <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                        <li><span class="pf-check">&#10003;</span> 1-3 Reviews Per Week</li>
                        <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                        <li><span class="pf-check">&#10003;</span> Priority Support</li>
                    </ul>
                    <a href="#" class="pricing-btn" data-package="Sweetheart">Order Now &#8212; Save $100</a>
                </div>
                <div class="pricing-card reveal reveal-delay-2">
                    <div class="pricing-name">Heartbreaker</div>
                    <div class="pricing-reviews">130 Reviews</div>
                    <div><div class="pricing-original">$845.00</div><div class="pricing-current">$650</div><div class="pricing-per">$5.00 per review</div></div>
                    <div class="pricing-badges"><span class="badge-discount">23% OFF</span><span class="badge-guarantee">20-Day Guarantee</span><span class="badge-save">Save $195</span></div>
                    <ul class="pricing-features">
                        <li><span class="pf-check">&#10003;</span> (20%) 4-Star + (80%) 5-Star Ratings</li>
                        <li><span class="pf-check">&#10003;</span> Local Names</li>
                        <li><span class="pf-check">&#10003;</span> Human-Written Custom Content</li>
                        <li><span class="pf-check">&#10003;</span> 1-3 Reviews Per Week</li>
                        <li><span class="pf-check">&#10003;</span> Detailed Delivery Report</li>
                        <li><span class="pf-check">&#10003;</span> Priority Support</li>
                        <li><span class="pf-check">&#10003;</span> Extended 20-Day Guarantee</li>
                    </ul>
                    <a href="#" class="pricing-btn" data-package="Heartbreaker">Order Now &#8212; Save $195</a>
                </div>
            </div>

        </div>
    </section>

    <!-- REAL REVIEWS PROOF -->
    <section class="section" style="background: var(--gray-100);" id="reviews">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Real Examples</div>
                <h2>Real Local <em>Reviews</em></h2>
                <p>See actual reviews we've delivered for our clients</p>
            </div>
            <div class="proof-card reveal">
                <div class="image-zoom-wrapper">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2025/04/slide-3.jpg" alt="Review Example" data-preview="true">
                    <span class="zoom-hint">Click to zoom</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CHOOSE YOUR OWN SENTENCES -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <div class="content-flex reveal">
                <div class="content-image-pink">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review Sentences" data-preview="true">
                </div>
                <div class="content-text">
                    <div class="section-tag" style="margin-bottom: 16px;">Customization</div>
                    <h3>Choose Your Own Sentences</h3>
                    <p>You choose the content, or let us create comprehensive variety for you. For every 55 reviews ordered, we prepare <strong>440+ unique sentences</strong> to ensure zero repetition.</p>
                    <div class="content-check"><span class="content-check-icon">&#10003;</span> Human-written content tailored to your business</div>
                    <div class="content-check" style="margin-top:8px;"><span class="content-check-icon">&#10003;</span> Up to 2 revision rounds included</div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY PEOPLE USE OUR SERVICES -->
    <section class="section" style="background: var(--gray-100);">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Why Choose Us</div>
                <h2>Why People Use <em>Our Services</em></h2>
            </div>
            <div class="content-flex reverse reveal">
                <div class="content-image">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.36.44.png" alt="Review Example" data-preview="true">
                </div>
                <div>
                    <ul class="why-features">
                        <li><div class="why-check">&#10003;</div><div><strong>70% Local + 30% Global Names</strong><span>Natural mix of local and global reviewers for authenticity</span></div></li>
                        <li><div class="why-check">&#10003;</div><div><strong>Unique users, IPs, devices, and aged accounts</strong><span>Complete technical authenticity guaranteed</span></div></li>
                        <li><div class="why-check">&#10003;</div><div><strong>Tailored reviews for your business</strong><span>Custom content that matches your services</span></div></li>
                        <li><div class="why-check">&#10003;</div><div><strong>Gradual posting (1-3 Reviews Per Week)</strong><span>Natural pacing prevents algorithm detection</span></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- TRACK YOUR ORDER -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">Transparency</div>
                <h2>Track Your Order <em>Every Day</em></h2>
                <p>100% transparency &#8212; monitor your campaign progress in real-time</p>
            </div>
            <div class="dashboard-preview reveal">
                <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Dashboard" data-preview="true">
            </div>
        </div>
    </section>

    <!-- 2000+ BUSINESSES -->
    <section class="section" style="background: var(--gray-100);">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Trusted by Many</div>
                <h2>Serving Over 2000+ <em>Businesses</em><br>Across the USA and Canada</h2>
            </div>
            <div class="dashboard-preview reveal">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="Trello Board" data-preview="true">
            </div>
        </div>
    </section>

    <!-- OUR CLIENTS MARQUEE -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <div class="section-header"><div class="section-tag">Portfolio</div><h2>Our <em>Clients</em></h2></div>
            <div class="clients-marquee-wrap reveal">
                <div class="clients-marquee">
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client"></div>
                    <!-- Duplicate for seamless loop -->
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client"></div>
                    <div class="client-logo"><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client"></div>
                </div>
            </div>
        </div>
    </section>



    <!-- COMPARISON: WITH vs WITHOUT REVIEWS -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">The Difference</div>
                <h2>With Reviews vs <em>Without</em></h2>
                <p>What happens when customers compare your business to competitors</p>
            </div>
            <div class="compare-table reveal">
                <div class="compare-row compare-header">
                    <div class="compare-cell">Metric</div>
                    <div class="compare-cell">&#9733; 2.5 Stars</div>
                    <div class="compare-cell">&#9733; 4.8 Stars</div>
                </div>
                <div class="compare-row">
                    <div class="compare-cell metric">Customer Trust</div>
                    <div class="compare-cell bad">&#10007; Low &#8212; people skip you</div>
                    <div class="compare-cell good">&#10003; High &#8212; people choose you</div>
                </div>
                <div class="compare-row">
                    <div class="compare-cell metric">Google Maps Ranking</div>
                    <div class="compare-cell bad">&#10007; Page 2-3</div>
                    <div class="compare-cell good">&#10003; Top 3 results</div>
                </div>
                <div class="compare-row">
                    <div class="compare-cell metric">Phone Calls / Inquiries</div>
                    <div class="compare-cell bad">&#10007; 5-10 per week</div>
                    <div class="compare-cell good">&#10003; 20-40 per week</div>
                </div>
                <div class="compare-row">
                    <div class="compare-cell metric">Click-Through Rate</div>
                    <div class="compare-cell bad">&#10007; Under 5%</div>
                    <div class="compare-cell good">&#10003; 15-25%</div>
                </div>
                <div class="compare-row">
                    <div class="compare-cell metric">Revenue Impact</div>
                    <div class="compare-cell bad">&#10007; Losing customers daily</div>
                    <div class="compare-cell good">&#10003; 30-60% more revenue</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CLIENT STORIES + FAQ -->
    <section class="section" style="background: var(--gray-100);" id="faq">
        <div class="container">
            <!-- TESTIMONIALS -->
            <div class="section-header">
                <div class="section-tag section-tag-red">Client Stories</div>
                <h2>What Our Clients <em>Say</em></h2>
                <p>Real results from real business owners</p>
            </div>
            <div class="testimonials-grid" style="margin-bottom: 100px;">
                <div class="testimonial-card reveal">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <div class="testimonial-text">"We were struggling at 2.3 stars and losing customers daily. After working with Smart Buzzer, we jumped to 4.7 stars and phone calls increased by 40%. Best investment we've made."</div>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face" alt="Mike R."></div>
                        <div>
                            <div class="testimonial-name">Mike R.</div>
                            <div class="testimonial-role">Roofing Company &#8212; Texas</div>
                            <div class="testimonial-result">&#8593; 2.3 &#8594; 4.7 stars</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal reveal-delay-1">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <div class="testimonial-text">"The gradual posting really works. We never had any issues with Google and the reviews look completely natural. Our competitors are still wondering how we did it."</div>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face" alt="Sarah K."></div>
                        <div>
                            <div class="testimonial-name">Sarah K.</div>
                            <div class="testimonial-role">Dental Clinic &#8212; Florida</div>
                            <div class="testimonial-result">&#8593; 89 reviews added</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal reveal-delay-2">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <div class="testimonial-text">"I run a marketing agency and resell Smart Buzzer to my clients. The tracking dashboard makes reporting easy and none of my clients have ever had an issue. Highly recommend."</div>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" alt="James L."></div>
                        <div>
                            <div class="testimonial-name">James L.</div>
                            <div class="testimonial-role">Digital Agency &#8212; California</div>
                            <div class="testimonial-result">&#8593; 15+ agency clients</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="section-header">
                <div class="section-tag">Questions</div>
                <h2>Frequently <em>Asked</em></h2>
                <p>Everything you need to know about our service</p>
            </div>
            <div class="faq-container">
                <div class="faq-item reveal"><button class="faq-question">Is this safe for my Google Business?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">We've served 2,000+ businesses with ZERO account bans. We use aged Google accounts, unique IP addresses, diverse devices, and gradual posting patterns (1-3 reviews per week) to ensure your business stays completely safe.</div></div></div>
                <div class="faq-item reveal"><button class="faq-question">How long until I see my reviews?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">Your first reviews appear within 24 hours of content approval. Reviews are then posted gradually at 1-3 per week to ensure natural patterns and maximum stickiness. Full campaign completion takes 1-2 months depending on the package.</div></div></div>
                <div class="faq-item reveal"><button class="faq-question">What if reviews get removed?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">Our Valentine packages come with extended replacement guarantees: 7 days for Cupid, 17 days for Sweetheart, and 20 days for Heartbreaker. Any removed reviews within the guarantee period will be replaced at no extra cost.</div></div></div>
                <div class="faq-item reveal"><button class="faq-question">Can I choose what the reviews say?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">Absolutely! You can provide your own sentences or let our team create unique, tailored content for your business. For every 55 reviews, we prepare 440+ unique sentences ensuring every review is completely unique with zero repetition.</div></div></div>
                <div class="faq-item reveal"><button class="faq-question">What payment methods do you accept?<span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-inner">We accept Debit/Credit Card (Visa, Mastercard), Apple Pay, AfterPay (Buy Now, Pay Later), Cash App Pay, Zelle, Bank Transfer, and Crypto (Tether/ETH). Pay with Crypto and get an additional 5% discount on any package!</div></div></div>
            </div>
        </div>
    </section>

    <!-- REVIEW CALCULATOR -->
    <section class="section calc-section" id="calculator">
        <div class="container">
            <div class="section-header">
                <div class="section-tag section-tag-red">Free Tool</div>
                <h2>Google Review <em>Calculator</em></h2>
                <p>Find out exactly how many reviews you need to reach your goal</p>
            </div>
            <div class="calc-card reveal">
                <div class="calc-grid">
                    <div class="calc-inputs">
                        <div class="calc-field">
                            <label for="calcCurrentReviews"># of Google Reviews You Have Now <span class="calc-req">*</span></label>
                            <input type="number" id="calcCurrentReviews" placeholder="e.g. 12" min="0" max="9999">
                        </div>
                        <div class="calc-field">
                            <label for="calcCurrentRating">Current Google Star Rating <span class="calc-req">*</span></label>
                            <input type="number" id="calcCurrentRating" placeholder="e.g. 2.8" min="1" max="5" step="0.1">
                        </div>
                        <div class="calc-field">
                            <label for="calcDesiredRating">Desired Google Star Rating <span class="calc-req">*</span></label>
                            <input type="number" id="calcDesiredRating" placeholder="e.g. 4.5" min="1" max="5" step="0.1">
                        </div>
                        <button class="btn-red calc-btn" id="calcBtn" onclick="calculateReviews()">Calculate Now &#10132;</button>
                    </div>
                    <div class="calc-result" id="calcResult">
                        <div class="calc-result-placeholder" id="calcPlaceholder">
                            <div class="calc-result-icon">&#9733;</div>
                            <p>Enter your details and hit <strong>Calculate</strong> to see your personalized recommendation</p>
                        </div>
                        <div class="calc-result-data" id="calcData" style="display:none;">
                            <div class="calc-result-number">
                                <span class="calc-big-num" id="calcNeeded">0</span>
                                <span class="calc-big-label">reviews needed</span>
                            </div>
                            <div class="calc-result-detail">
                                <p>To go from <strong><span id="calcFrom">0</span> &#9733;</strong> to <strong><span id="calcTo">0</span> &#9733;</strong></p>
                            </div>
                            <div class="calc-rec" id="calcRec">
                                <div class="calc-rec-tag" id="calcRecTag">Recommended Package</div>
                                <div class="calc-rec-name" id="calcRecName">Sweetheart</div>
                                <div class="calc-rec-info" id="calcRecInfo">100 Reviews &#8212; $550</div>
                                <div class="calc-rec-save" id="calcRecSave">Save $100</div>
                            </div>
                            <a href="#pricing" class="btn-red calc-btn" style="width:100%;justify-content:center;">See All Packages &#10084;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-border-top"></div>
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-brand">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                    <p>Specialized in social media engagement, product reviews, and online reputation services.</p>
                    <p class="sub">A subsidiary of Pintarnya.</p>
                    <div class="footer-socials">
                        <a href="#" class="footer-social-icon" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="footer-social-icon" aria-label="Instagram"><svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                        <a href="#" class="footer-social-icon" aria-label="TikTok"><svg viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="https://smart-buzzer.com/tracker">Track Campaign</a>
                    <a href="https://smart-buzzer.com/report">Report Issue</a>
                    <a href="https://smart-buzzer.com/service-tnc">Terms &amp; Conditions</a>
                    <a href="#pricing">Valentine Packages</a>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <a href="https://wa.me/6285773116557?text=Hi%20Smart%20Buzzer%2C%20I'm%20interested%20in%20the%20Valentine%20Special%20package.%20Can%20you%20share%20more%20details%3F" target="_blank">&#128222; +62 857-7311-6557</a>
                    <a href="mailto:contact@smart-buzzer.com">&#128231; contact@smart-buzzer.com</a>
                    <a href="https://smart-buzzer.com" target="_blank">&#127760; smart-buzzer.com</a>
                </div>
            </div>
            <div class="footer-divider"></div>
            <div class="footer-bottom">
                <p>&copy; 2026 Smart Buzzer. All rights reserved. | Portland, Oregon, USA</p>
                <p class="footer-love">Made with <span>&#10084;</span> for your business growth</p>
            </div>
        </div>
    </footer>

    <!-- IMAGE MODAL -->
    <div class="image-modal" id="imageModal">
        <button class="modal-close" id="modalClose">&times;</button>
        <img src="" alt="Preview" id="modalImage">
    </div>

    <!-- FLOATING WHATSAPP -->
    <div class="wa-float" id="waFloat">
        <a href="https://wa.me/6285773116557?text=Hi%20Smart%20Buzzer%2C%20I'm%20interested%20in%20the%20Valentine%20Special%20package.%20Can%20you%20share%20more%20details%3F" target="_blank" class="wa-float-btn" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        <span class="wa-float-label">Chat with us!</span>
    </div>

    <!-- SOCIAL PROOF POPUP -->
    <div class="social-proof" id="socialProof">
        <button class="social-proof-close" id="spClose">&times;</button>
        <div class="social-proof-icon">&#9989;</div>
        <div>
            <div class="social-proof-text" id="spText"><strong>Mike from Houston</strong> just ordered 100 reviews</div>
            <div class="social-proof-time" id="spTime">2 minutes ago</div>
        </div>
    </div>



    <!-- DESKTOP STICKY CTA BAR -->
    <div class="desktop-sticky" id="desktopSticky">
        <div class="desktop-sticky-inner">
            <div class="desktop-sticky-text">&#10084; Valentine Special &#8212; <span>Save up to $195</span> on Google Reviews</div>
            <div class="desktop-sticky-btns">
                <a href="#pricing" class="btn-red">See Packages</a>
                <a href="https://wa.me/6285773116557?text=Hi%20Smart%20Buzzer%2C%20I'm%20interested%20in%20the%20Valentine%20Special%20package." class="btn-wa" target="_blank">WhatsApp Us</a>
            </div>
        </div>
    </div>

    <div class="sticky-cta">
        <a href="#pricing">&#10084; See Packages</a>
    </div>

    <!-- SCRIPTS -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
    // ANALYTICS
    window.dataLayer = window.dataLayer || [];
    var sessionId='session_'+Math.random().toString(36).substr(2,9)+'_'+Date.now();
    function trackEvent(t,d){fetch('analytics.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({event_type:t,page_url:window.location.href,data:JSON.stringify(d||{}),session_id:sessionId})}).catch(function(){});}
    trackEvent('PAGE_VIEW');
    var scrollD={25:false,50:false,75:false,100:false};
    window.addEventListener('scroll',function(){var p=Math.round((window.scrollY/(document.documentElement.scrollHeight-window.innerHeight))*100);[25,50,75,100].forEach(function(d){if(p>=d&&!scrollD[d]){scrollD[d]=true;trackEvent('SCROLL_DEPTH_'+d);}});});
    var t0=Date.now();window.addEventListener('beforeunload',function(){trackEvent('TIME_ON_PAGE',{seconds:Math.round((Date.now()-t0)/1000)});});

    // CLICK HEATMAP - Track X,Y coordinates on interactive elements
    document.addEventListener('click',function(ev){var el=ev.target.closest('a,button,[data-package],[data-preview]');if(el){trackEvent('CLICK_HEATMAP',{x:ev.clientX,y:ev.clientY,tag:el.tagName,id:el.id||'',cls:(el.className||'').toString().substring(0,60),text:(el.textContent||'').replace(/\s+/g,' ').trim().substring(0,50)});}});

    // EXIT PAGE - Capture exit URL and time spent
    document.addEventListener('visibilitychange',function(){if(document.visibilityState==='hidden'){var exitHref='';try{if(document.activeElement&&document.activeElement.href)exitHref=document.activeElement.href;}catch(x){}trackEvent('EXIT_PAGE',{seconds:Math.round((Date.now()-t0)/1000),exitUrl:exitHref});}});

    // RETURN VISITOR - Cookie-based detection
    (function(){var ck='sb_rv_val';var found=document.cookie.split(';').some(function(c){return c.trim().indexOf(ck+'=')===0;});if(found){trackEvent('RETURN_VISITOR');}document.cookie=ck+'=1;path=/;max-age=31536000;SameSite=Lax';})();

    // EXTERNAL LINK CLICK - Track clicks to external URLs
    document.addEventListener('click',function(ev){var lnk=ev.target.closest('a[href]');if(lnk&&lnk.hostname&&lnk.hostname!==window.location.hostname){trackEvent('EXTERNAL_LINK_CLICK',{url:lnk.href,text:(lnk.textContent||'').replace(/\s+/g,' ').trim().substring(0,50)});}});


    // PACKAGE METADATA (seasonal / Valentine)
    var sbPkgMeta = {
        'cupid':        {id: 'pkg_cupid_50',        name: 'Buy Google Reviews - 50 Local (Valentine)',  item_category: 'Google Reviews', price: 300.00, reviews: 50},
        'sweetheart':   {id: 'pkg_sweetheart_100',   name: 'Buy Google Reviews - 100 Local (Valentine)', item_category: 'Google Reviews', price: 550.00, reviews: 100},
        'heartbreaker': {id: 'pkg_heartbreaker_130', name: 'Buy Google Reviews - 130 Local (Valentine)', item_category: 'Google Reviews', price: 650.00, reviews: 130}
    };
    var sbFanbasisUrls = {
        'cupid':        'https://www.fanbasis.com/agency-checkout/smartbuzzer/BNA7X',
        'sweetheart':   'https://www.fanbasis.com/agency-checkout/smartbuzzer/D1D7x',
        'heartbreaker': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/JyMj2'
    };

    // VIEW ITEM - fires when pricing section becomes visible (30% threshold)
    var sbViewItemFired = false;
    var sbPricingObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting && !sbViewItemFired) {
                sbViewItemFired = true;
                var meta = sbPkgMeta['sweetheart']; // featured package
                window.dataLayer.push({ecommerce: null});
                window.dataLayer.push({
                    event: 'view_item',
                    ecommerce: {
                        currency: 'USD',
                        value: meta.price,
                        items: [{
                            item_id: meta.id,
                            item_name: meta.name,
                            item_category: 'Google Reviews',
                            price: meta.price,
                            quantity: 1
                        }]
                    }
                });
                trackEvent('VIEW_ITEM', {package: 'sweetheart', price: meta.price});
            }
        });
    }, {threshold: 0.3});
    var sbPricingEl = document.getElementById('pricing');
    if (sbPricingEl) sbPricingObserver.observe(sbPricingEl);

    // GENERATE LEAD - WhatsApp button clicks
    document.querySelectorAll('a[href*="wa.me"]').forEach(function(el) {
        el.addEventListener('click', function() {
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'WhatsApp',
                value: 0,
                currency: 'USD'
            });
        });
    });

    // ORDER NOW CLICK - pricing buttons -> full GTM + Pixel tracking -> redirect to Fanbasis
    document.querySelectorAll('[data-package]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var pkg = this.getAttribute('data-package').toLowerCase();
            var meta = sbPkgMeta[pkg];
            if (!meta) return;

            // FB Lead pixel
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {content_name: pkg});
            }
            // TikTok SubmitForm
            if (typeof ttq !== 'undefined') {
                ttq.track('SubmitForm', {content_name: pkg});
            }
            // GTM generate_lead
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'pricing_click',
                value: meta.price,
                currency: 'USD'
            });
            // GTM begin_checkout (no form on this LP — direct Fanbasis)
            var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
            window.dataLayer.push({ecommerce: null});
            window.dataLayer.push({
                event: 'begin_checkout',
                user_data: {email: '', phone_number: '', first_name: '', last_name: ''},
                ecommerce: {
                    currency: 'USD',
                    value: meta.price,
                    items: [{
                        item_id: meta.id,
                        item_name: meta.name,
                        item_category: 'Google Reviews',
                        price: meta.price,
                        quantity: 1
                    }]
                }
            });
            // GTM add_payment_info
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
                        item_category: 'Google Reviews',
                        price: meta.price,
                        quantity: 1
                    }]
                }
            });
            // LocalStorage bridge — persists for purchase event on thankyou.php
            try {
                localStorage.setItem('sb_txn_id', txnId);
                localStorage.setItem('sb_pkg', pkg);
                localStorage.setItem('sb_user_email', '');
                localStorage.setItem('sb_user_phone', '');
                localStorage.setItem('sb_user_fname', '');
                localStorage.setItem('sb_user_lname', '');
            } catch(e) {}
            // Analytics
            trackEvent('ORDER_' + pkg.toUpperCase() + '_CLICK', {package: pkg, price: meta.price});
            // Redirect directly to Fanbasis payment gateway
            // Fanbasis will redirect back to thankyou.php after payment
            var dest = sbFanbasisUrls[pkg];
            setTimeout(function() { window.location.href = dest; }, 300);
        });
    });

    // COUNTDOWN
    function updateCD(){var n=new Date(),m=new Date();m.setHours(24,0,0,0);var d=m-n;document.getElementById('cd-h').textContent=Math.floor(d/3600000).toString().padStart(2,'0');document.getElementById('cd-m').textContent=Math.floor((d%3600000)/60000).toString().padStart(2,'0');document.getElementById('cd-s').textContent=Math.floor((d%60000)/1000).toString().padStart(2,'0');}
    updateCD();setInterval(updateCD,1000);

    // REVEAL ON SCROLL
    var obs=new IntersectionObserver(function(e){e.forEach(function(en){if(en.isIntersecting)en.target.classList.add('visible');});},{threshold:0.1});
    document.querySelectorAll('.reveal').forEach(function(el){obs.observe(el);});

    // NUMBER COUNTER
    var counterObs=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){var el=entry.target;if(el.dataset.counted)return;el.dataset.counted='true';if(el.dataset.count!==undefined)animateCount(el,0,parseInt(el.dataset.count),1500,'+');else if(el.dataset.countDecimal!==undefined)animateCountDec(el,0,parseFloat(el.dataset.countDecimal),1500);else if(el.dataset.countPercent!==undefined)animateCount(el,0,parseInt(el.dataset.countPercent),1200,'%');}});},{threshold:0.5});
    document.querySelectorAll('[data-count],[data-count-decimal],[data-count-percent]').forEach(function(el){counterObs.observe(el);});
    function animateCount(el,s,e,dur,suf){var st=null;function step(ts){if(!st)st=ts;var p=Math.min((ts-st)/dur,1);var eased=1-Math.pow(1-p,3);el.textContent=Math.floor(eased*(e-s)+s).toLocaleString()+(suf||'');if(p<1)requestAnimationFrame(step);}requestAnimationFrame(step);}
    function animateCountDec(el,s,e,dur){var st=null;function step(ts){if(!st)st=ts;var p=Math.min((ts-st)/dur,1);var eased=1-Math.pow(1-p,3);el.textContent=(eased*(e-s)+s).toFixed(1);if(p<1)requestAnimationFrame(step);}requestAnimationFrame(step);}

    // FLOATING HEARTS
    var hc=document.getElementById('heartsContainer');var hs=['&#10084;','&#9829;','&#10085;'];
    function makeHeart(){var h=document.createElement('div');h.className='floating-heart';h.innerHTML=hs[Math.floor(Math.random()*hs.length)];h.style.left=Math.random()*100+'%';h.style.fontSize=(14+Math.random()*18)+'px';h.style.animationDuration=(8+Math.random()*12)+'s';h.style.animationDelay=Math.random()*2+'s';h.style.color=Math.random()>0.5?'#FBBDC6':'#EC263B';hc.appendChild(h);setTimeout(function(){h.remove();},22000);}
    setInterval(makeHeart,2500);for(var i=0;i<5;i++)setTimeout(makeHeart,i*500);

    // FAQ
    document.querySelectorAll('.faq-question').forEach(function(b){b.addEventListener('click',function(){var i=this.parentElement,a=i.classList.contains('active');document.querySelectorAll('.faq-item').forEach(function(f){f.classList.remove('active');});if(!a)i.classList.add('active');});});

    // IMAGE MODAL
    var modal=document.getElementById('imageModal'),mImg=document.getElementById('modalImage');
    document.querySelectorAll('[data-preview="true"]').forEach(function(i){i.addEventListener('click',function(){mImg.src=this.src;modal.classList.add('active');document.body.style.overflow='hidden';});});
    function closeM(){modal.classList.remove('active');document.body.style.overflow='';}
    document.getElementById('modalClose').addEventListener('click',closeM);
    modal.addEventListener('click',function(e){if(e.target===modal)closeM();});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeM();}});

    // SMOOTH SCROLL
    document.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();var t=document.querySelector(this.getAttribute('href'));if(t)t.scrollIntoView({behavior:'smooth',block:'start'});});});

    // DESKTOP STICKY BAR (show after scrolling past hero)
    var desktopSticky=document.getElementById('desktopSticky');
    var pricingSection=document.getElementById('pricing');
    window.addEventListener('scroll',function(){
        if(window.innerWidth>768){
            desktopSticky.classList.toggle('show',window.scrollY>800);
        }
    });

    // SOCIAL PROOF POPUP
    var spData=[
        {name:'Mike from Houston',action:'ordered 100 reviews',time:'2 minutes ago'},
        {name:'Sarah from Miami',action:'ordered 50 reviews',time:'5 minutes ago'},
        {name:'David from Phoenix',action:'ordered 130 reviews',time:'8 minutes ago'},
        {name:'Lisa from Denver',action:'ordered 100 reviews',time:'12 minutes ago'},
        {name:'Robert from Austin',action:'ordered 50 reviews',time:'15 minutes ago'},
        {name:'Jennifer from Chicago',action:'ordered 130 reviews',time:'18 minutes ago'},
        {name:'Chris from Seattle',action:'ordered 100 reviews',time:'22 minutes ago'},
        {name:'Amanda from Portland',action:'ordered 50 reviews',time:'25 minutes ago'},
    ];
    var spEl=document.getElementById('socialProof'),spIdx=0,spDismissed=false;
    function showSP(){
        if(spDismissed)return;
        var d=spData[spIdx%spData.length];
        document.getElementById('spText').innerHTML='<strong>'+d.name+'</strong> just '+d.action;
        document.getElementById('spTime').textContent=d.time;
        spEl.classList.add('show');
        trackEvent('SOCIAL_PROOF_SHOW',{name:d.name});
        setTimeout(function(){spEl.classList.remove('show');spIdx++;},5000);
    }
    setTimeout(showSP,8000);
    setInterval(function(){if(!spDismissed)showSP();},25000);
    document.getElementById('spClose').addEventListener('click',function(){spEl.classList.remove('show');spDismissed=true;});

    // REVIEW CALCULATOR
    function calculateReviews(){
        var curRev=parseFloat(document.getElementById('calcCurrentReviews').value);
        var curRat=parseFloat(document.getElementById('calcCurrentRating').value);
        var desRat=parseFloat(document.getElementById('calcDesiredRating').value);

        if(isNaN(curRev)||isNaN(curRat)||isNaN(desRat)){alert('Please fill in all fields.');return;}
        if(curRev<0){alert('Number of reviews cannot be negative.');return;}
        if(curRat<1||curRat>5){alert('Current rating must be between 1.0 and 5.0.');return;}
        if(desRat<1||desRat>5){alert('Desired rating must be between 1.0 and 5.0.');return;}
        if(desRat<=curRat){alert('Desired rating must be higher than your current rating.');return;}

        var avgNew=4.8;
        if(desRat>=avgNew){
            alert('The maximum achievable rating with our service mix is approximately 4.8 stars. Please adjust your desired rating.');
            return;
        }
        var needed=Math.ceil(curRev*(desRat-curRat)/(avgNew-desRat));
        if(needed<1)needed=1;

        document.getElementById('calcPlaceholder').style.display='none';
        document.getElementById('calcData').style.display='block';

        var numEl=document.getElementById('calcNeeded');
        var st=null;
        function step(ts){if(!st)st=ts;var p=Math.min((ts-st)/800,1);var eased=1-Math.pow(1-p,3);numEl.textContent=Math.floor(eased*needed);if(p<1)requestAnimationFrame(step);}
        requestAnimationFrame(step);

        document.getElementById('calcFrom').textContent=curRat.toFixed(1);
        document.getElementById('calcTo').textContent=desRat.toFixed(1);

        var recName,recInfo,recSave,recTag='Recommended Package';
        if(needed<=50){
            recName='Cupid';recInfo='50 Reviews \u2014 $300';recSave='Save $25 (8% OFF)';
        }else if(needed<=100){
            recName='Sweetheart';recInfo='100 Reviews \u2014 $550';recSave='Save $100 (15% OFF)';
        }else{
            recName='Heartbreaker';recInfo='130 Reviews \u2014 $650';recSave='Save $195 (23% OFF)';
        }
        if(needed>130){
            recTag='Best Value Package';
            recName='Heartbreaker';recInfo='130 Reviews \u2014 $650';recSave='Contact us for 150+ reviews (volume discounts)';
        }

        document.getElementById('calcRecTag').textContent=recTag;
        document.getElementById('calcRecName').textContent=recName;
        document.getElementById('calcRecInfo').textContent=recInfo;
        document.getElementById('calcRecSave').textContent=recSave;

        trackEvent('CALCULATOR_USED',{currentReviews:curRev,currentRating:curRat,desiredRating:desRat,reviewsNeeded:needed,recommendedPackage:recName});
    }
    </script>
</body>
</html>