<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Get authentic Tripadvisor reviews from real travelers — a natural mix of local + global names. Trusted by 1,200+ hotels, restaurants & attractions. Starting $250 for 28 reviews.">
    <title>Tripadvisor Reviews - Smart Buzzer | 1,200+ Happy Hospitality Clients</title>
    
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WJ6ZK3MR');</script>
    <!-- End Google Tag Manager -->

    <!-- Meta Pixel: managed via GTM (tag: FB - Pageview, All Pages) -->
    <!-- Do NOT add direct fbq('init') here — GTM handles all FB events -->

    <!-- Analytics Tracking - MUST BE BEFORE ANY OTHER SCRIPTS -->
    <script>
    // Safe localStorage wrapper (handles incognito mode)
    function safeLocalStorage() {
        try {
            const test = '__storage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return localStorage;
        } catch (e) {
            return {
                _data: {},
                getItem: function(key) { return this._data[key] || null; },
                setItem: function(key, value) { this._data[key] = value; },
                removeItem: function(key) { delete this._data[key]; }
            };
        }
    }

    // Generate unique session ID
    function generateSessionId() {
        return 'sess_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }

    // Get or create session ID
    var sessionId = safeLocalStorage().getItem('sb_session_id');
    if (!sessionId) {
        sessionId = generateSessionId();
        safeLocalStorage().setItem('sb_session_id', sessionId);
    }

    // Check return visitor
    function checkReturnVisitor() {
        var storage = safeLocalStorage();
        var visitCount = parseInt(storage.getItem('sb_visit_count') || '0');
        visitCount++;
        storage.setItem('sb_visit_count', visitCount.toString());
        
        if (visitCount > 1) {
            trackEvent('RETURN_VISITOR', { is_return: true, visit_count: visitCount });
        }
    }

    // Track event to analytics.php
    function trackEvent(eventType, data) {
        try {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'analytics.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.timeout = 3000;
            xhr.send(JSON.stringify({
                event_type: eventType,
                page_url: window.location.href,
                data: data || {},
                session_id: sessionId
            }));
        } catch (e) {
            console.log('Analytics error:', e);
        }
    }

    // Track page view immediately
    trackEvent('PAGE_VIEW', {});
    checkReturnVisitor();

    // ===== Tripadvisor Package Metadata (for GTM dataLayer) =====
    var sbPkgMeta = {
        'starter':     {id: 'pkg_tripadvisor_starter_28',     name: 'Buy Tripadvisor Reviews - 28 Local',  item_category: 'Tripadvisor Reviews', price: 250.00, reviews: 28},
        'growth':      {id: 'pkg_tripadvisor_growth_35',      name: 'Buy Tripadvisor Reviews - 35 Local',  item_category: 'Tripadvisor Reviews', price: 300.00, reviews: 35},
        'performance': {id: 'pkg_tripadvisor_performance_50', name: 'Buy Tripadvisor Reviews - 50 Local',  item_category: 'Tripadvisor Reviews', price: 400.00, reviews: 50}
    };

    // Fire view_item when pricing section becomes visible
    var viewItemFired = false;
    function fireViewItem() {
        if (viewItemFired) return;
        viewItemFired = true;
        var items = [];
        for (var key in sbPkgMeta) {
            var m = sbPkgMeta[key];
            items.push({item_id: m.id, item_name: m.name, item_category: m.item_category, price: m.price, quantity: 1});
        }
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            event: 'view_item',
            ecommerce: { currency: 'USD', value: sbPkgMeta.growth.price, items: items }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        var pricing = document.getElementById('pricing');
        if (!pricing) return;
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) { if (e.isIntersecting) { fireViewItem(); io.disconnect(); } });
            }, {threshold: 0.3});
            io.observe(pricing);
        } else {
            window.addEventListener('scroll', function() {
                var rect = pricing.getBoundingClientRect();
                if (rect.top < window.innerHeight * 0.7) fireViewItem();
            });
        }
    });
    </script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --ta-green: #34E0A1;
            --ta-green-dark: #2BC68B;
            --ta-black: #000000;
            --dark: #0A0A0A;
            --gray-900: #1A1A1A;
            --gray-700: #3F3F3F;
            --gray-500: #737373;
            --gray-300: #D4D4D4;
            --gray-200: #E5E5E5;
            --gray-100: #F5F5F5;
            --gray-50: #FAFAFA;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.5;
            color: var(--gray-900);
            background: #fff;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        .header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--gray-200);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 32px;
            align-items: center;
        }

        .nav-links a {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            text-decoration: none;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--ta-green-dark);
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--ta-green);
            color: var(--dark);
            box-shadow: 0 1px 2px rgba(52, 224, 161, 0.18);
        }

        .btn-primary:hover {
            background: var(--ta-green-dark);
            box-shadow: 0 4px 12px rgba(52, 224, 161, 0.28);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--dark);
            color: #fff;
        }

        .btn-secondary:hover {
            background: var(--gray-700);
            transform: translateY(-1px);
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
        }

        /* Hero */
        .hero {
            padding: 80px 0 100px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gray-100);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 24px;
        }

        .badge-green {
            color: var(--ta-green-dark);
        }

        h1 {
            font-size: 56px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--ta-black);
            margin-bottom: 24px;
        }

        .text-green {
            color: var(--ta-green);
        }

        .hero p {
            font-size: 20px;
            line-height: 1.6;
            color: var(--gray-500);
            margin-bottom: 36px;
        }

        .hero-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 48px;
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-700);
        }

        .check-icon {
            width: 20px;
            height: 20px;
            background: var(--ta-green);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            flex-shrink: 0;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        /* Section Dividers */
        .section-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gray-300), transparent);
            margin: 0;
        }

        /* Stats */
        .stats {
            padding: 48px 0;
            border-top: 1px solid var(--gray-200);
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 48px;
            font-weight: 900;
            color: var(--ta-green);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-500);
        }

        /* Section Headers */
        .section {
            padding: 96px 0;
        }

        .section-header {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 64px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            background: var(--gray-100);
            color: var(--ta-green-dark);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        h2 {
            font-size: 48px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--dark);
            margin-bottom: 16px;
        }

        .section-desc {
            font-size: 19px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* Pricing */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 56px;
        }

        .price-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.3s;
        }

        .price-card:hover {
            border-color: var(--ta-green);
            box-shadow: 0 20px 40px -12px rgba(52, 224, 161, 0.22);
            transform: translateY(-4px);
        }

        .price-card.featured {
            border: 2px solid var(--ta-green);
            box-shadow: 0 10px 30px -8px rgba(52, 224, 161, 0.28);
        }

        .popular-tag {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--ta-green);
            color: var(--dark);
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
            margin-bottom: 12px;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
        }

        .price-currency {
            font-size: 32px;
            font-weight: 900;
            color: var(--dark);
        }

        .price-amount {
            font-size: 56px;
            font-weight: 900;
            color: var(--dark);
            line-height: 1;
        }

        .price-decimal {
            font-size: 32px;
            font-weight: 900;
            color: var(--gray-500);
        }

        .plan-reviews {
            font-size: 16px;
            color: var(--gray-500);
            margin-bottom: 4px;
        }

        .plan-per-review {
            font-size: 14px;
            color: var(--ta-green-dark);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .savings-badge {
            display: inline-block;
            background: #FF6B35;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .lightbox.active {
            display: flex;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 85vh;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            width: 36px;
            height: 36px;
            background: #fff;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .lightbox-close:hover {
            background: var(--ta-green);
            transform: scale(1.1);
        }

        .clickable-image {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .clickable-image:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .plan-features {
            list-style: none;
            margin-bottom: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            font-size: 15px;
            color: var(--gray-700);
        }

        .btn-plan {
            width: 100%;
            background: var(--ta-black);
            color: #fff;
            padding: 14px;
            font-weight: 700;
        }

        .btn-plan:hover {
            background: var(--ta-green);
            color: var(--ta-black);
        }

        .price-card.featured .btn-plan {
            background: var(--ta-green);
            color: var(--dark);
        }

        .price-card.featured .btn-plan:hover {
            background: var(--ta-green-dark);
        }

        /* Payment Box */
        .payment-box {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 40px;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .payment-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .payment-option {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            transition: all 0.2s;
        }

        .payment-option:hover {
            border-color: var(--ta-green);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .payment-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .payment-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-700);
        }

        /* Process */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
        }

        .process-item {
            text-align: center;
            padding: 32px 24px;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            transition: all 0.2s;
        }

        .process-item:hover {
            border-color: var(--ta-green);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .process-number {
            width: 48px;
            height: 48px;
            background: var(--ta-green);
            color: var(--dark);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            margin: 0 auto 20px;
        }

        .process-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .process-desc {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* Content Sections */
        .content-section {
            padding: 96px 0;
        }

        .content-flex {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 64px;
            align-items: center;
        }

        .content-image img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Why Section */
        .why-section {
            padding: 96px 0;
            background: var(--gray-50);
        }

        .why-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }

        .why-image img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .why-features {
            list-style: none;
        }

        .why-features li {
            display: flex;
            gap: 16px;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .why-features li:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        /* Proof Grid */
        .proof-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            max-width: 800px;
            margin: 0 auto;
        }

        .proof-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            overflow: hidden;
        }

        .image-zoom-wrapper {
            overflow: hidden;
        }

        .image-zoom-wrapper img {
            width: 100%;
            height: auto;
            transition: transform 0.3s;
        }

        .proof-card:hover .image-zoom-wrapper img {
            transform: scale(1.02);
        }

        /* Dashboard Preview */
        .dashboard-preview {
            max-width: 1000px;
            margin: 0 auto;
        }

        .dashboard-preview img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--gray-200);
        }

        /* Clients Grid */
        .clients-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .client-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            transition: all 0.2s;
        }

        .client-logo:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .client-logo img {
            width: 100%;
            height: auto;
            max-height: 60px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: all 0.2s;
        }

        .client-logo:hover img {
            filter: grayscale(0%);
            opacity: 1;
        }

        /* CTA */
        .cta {
            padding: 96px 0;
            background: var(--dark);
        }

        .cta-content {
            text-align: center;
            max-width: 640px;
            margin: 0 auto;
        }

        .cta h2 {
            color: #fff;
            margin-bottom: 16px;
        }

        .cta p {
            font-size: 19px;
            color: var(--gray-300);
            margin-bottom: 32px;
        }

        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        /* Footer */
        .footer {
            padding: 64px 0 32px;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 64px;
            margin-bottom: 48px;
        }

        .footer-brand img {
            height: 40px;
            margin-bottom: 16px;
        }

        .footer-desc {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .footer-subsidiary {
            font-size: 13px;
            color: var(--gray-500);
            font-style: italic;
        }

        .footer-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            font-size: 14px;
            color: var(--gray-500);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--ta-green-dark);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 32px;
            border-top: 1px solid var(--gray-200);
            font-size: 14px;
            color: var(--gray-500);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-grid,
            .pricing-grid,
            .process-grid,
            .content-flex,
            .why-flex {
                grid-template-columns: 1fr;
            }

            .hero-grid {
                gap: 48px;
            }

            .hero-image {
                order: -1;
            }

            h1 {
                font-size: 44px;
            }

            h2 {
                font-size: 36px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .payment-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .clients-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .nav-links {
                display: none;
            }
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 36px;
            }

            h2 {
                font-size: 32px;
            }

            .hero-buttons,
            .cta-buttons {
                flex-direction: column;
            }

            .btn-lg {
                width: 100%;
            }

            .features-list {
                grid-template-columns: 1fr;
            }

            .stats-grid,
            .payment-grid {
                grid-template-columns: 1fr;
            }

            .nav-buttons {
                display: none;
            }

            .process-grid {
                gap: 16px;
            }
        }

        /* ============ INLINE ORDER FORM ============ */
        .ob-modal-box {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 560px;
            padding: 32px;
            position: relative;
            box-shadow: 0 10px 30px -8px rgba(0,0,0,0.12);
            border: 1px solid var(--gray-200);
        }

        .of-countdown {
            background: #DC2626;
            color: #fff;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-transform: none;
        }
        .of-countdown-blocks { display: flex; gap: 6px; }
        .of-countdown-blocks span {
            background: rgba(0,0,0,0.25);
            padding: 4px 9px;
            border-radius: 6px;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            font-size: 15px;
            min-width: 32px;
            text-align: center;
        }

        .of-field { margin-bottom: 18px; }
        .of-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .of-label .req { color: #DC2626; }
        .of-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: var(--dark);
            background: #fff;
            transition: all 0.2s;
        }
        .of-input:focus {
            outline: none;
            border-color: var(--ta-green);
            box-shadow: 0 0 0 3px rgba(52, 224, 161, 0.15);
        }

        .of-pkg-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px; }
        .of-pkg {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border: 1px solid var(--gray-300);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }
        .of-pkg:hover { border-color: var(--ta-green); }
        .of-pkg.selected {
            border: 2px solid var(--ta-green);
            background: #ECFDF5;
            padding: 15px 17px;
        }
        .of-pkg-radio {
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-300);
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .of-pkg.selected .of-pkg-radio {
            border-color: var(--ta-green);
        }
        .of-pkg.selected .of-pkg-radio::after {
            content: '';
            position: absolute;
            inset: 3px;
            background: var(--ta-green);
            border-radius: 50%;
        }
        .of-pkg-info { flex: 1; }
        .of-pkg-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .of-pkg-tag {
            background: var(--ta-green);
            color: var(--dark);
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .of-pkg-detail {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 3px;
        }
        .of-pkg-detail s { color: var(--gray-500); }
        .of-pkg-price {
            font-size: 24px;
            font-weight: 900;
            color: var(--ta-green-dark);
            font-variant-numeric: tabular-nums;
        }

        .of-submit {
            width: 100%;
            background: var(--ta-green);
            color: var(--dark);
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }
        .of-submit:hover {
            background: var(--ta-green-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(52, 224, 161, 0.35);
        }

        .of-trust {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-top: 16px;
            font-size: 12px;
            color: var(--gray-500);
            flex-wrap: wrap;
        }
        .of-trust span { display: inline-flex; align-items: center; gap: 5px; }

        @media (max-width: 560px) {
            .ob-modal-box { padding: 22px 18px; }
            .of-pkg { padding: 13px 14px; gap: 10px; }
            .of-pkg-price { font-size: 20px; }
            .of-trust { gap: 10px; font-size: 11px; }
        }

        /* ============ FAQ ACCORDION ============ */
        .faq-section { padding: 96px 0; background: var(--gray-50); }
        .faq-list { max-width: 760px; margin: 0 auto; }
        .faq-item {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .faq-item:hover { border-color: var(--ta-green); }
        .faq-q {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 20px 24px;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            font-family: inherit;
        }
        .faq-icon {
            width: 28px;
            height: 28px;
            background: var(--ta-green);
            color: var(--dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
            transition: transform 0.2s;
        }
        .faq-item.open .faq-icon { transform: rotate(45deg); }
        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 24px;
            font-size: 15px;
            line-height: 1.6;
            color: var(--gray-700);
        }
        .faq-item.open .faq-a {
            max-height: 400px;
            padding: 0 24px 22px;
        }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJ6ZK3MR"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="https://smart-buzzer.com/" class="logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                </a>
                <div class="nav-links">
                    <a href="#pricing">Pricing</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#reviews">Our Reviews</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="nav-buttons">
                    <a href="#pricing" class="btn btn-primary">Get Started</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <div class="badge">
                        <span style="color: var(--ta-green)">★★★★★</span>
                        <span>1,200+ Happy Hospitality Clients</span>
                    </div>
                    <h1>Boost Your <span class="text-green">Tripadvisor Rating</span> With Real Traveler Reviews</h1>
                    <p>Buy genuine Tripadvisor reviews from real travelers — a natural mix of local + global names. Safe, natural posting. Trusted by 1,200+ hotels, restaurants & attractions across the USA.</p>
                    <div class="hero-buttons">
                        <a href="#pricing" class="btn btn-primary btn-lg">LEARN MORE</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://smart-buzzer.com/promo-tripadvisor/x.png" alt="Tripadvisor Reviews Dashboard">
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Process / How It Works -->
    <section class="section" id="how-it-works" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">How It Works</div>
                <h2>4 Simple Steps</h2>
            </div>
            <div class="process-grid">
                <div class="process-item">
                    <div class="process-number">1</div>
                    <div class="process-title">Order & Pay</div>
                    <div class="process-desc">Choose package and complete payment via crypto, card, Zelle, or wire.</div>
                </div>
                <div class="process-item">
                    <div class="process-number">2</div>
                    <div class="process-title">Approve Content</div>
                    <div class="process-desc">Review content within 24 hours or provide your own.</div>
                </div>
                <div class="process-item">
                    <div class="process-number">3</div>
                    <div class="process-title">We Post</div>
                    <div class="process-desc">Reviews posted 1-3 per week using aged accounts and unique IPs.</div>
                </div>
                <div class="process-item">
                    <div class="process-number">4</div>
                    <div class="process-title">Track Live</div>
                    <div class="process-desc">Monitor progress via real-time dashboard.</div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Pricing -->
    <section class="section" id="pricing">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Pricing</div>
                <h2>Choose Your Package</h2>
            </div>

            <div class="pricing-grid">
                <!-- Starter -->
                <div class="price-card">
                    <div class="plan-name">Starter</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">250</span>
                        <span class="price-decimal">.00</span>
                    </div>
                    <div class="plan-reviews">28 Reviews</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> Local + Global Traveler Names</li>
                        <li><span class="check-icon">✓</span> Customized Content</li>
                        <li><span class="check-icon">✓</span> Gradual Posting: 1-3 Reviews Per Week<sup style="color:var(--ta-green-dark);font-weight:800;">*</sup></li>
                        <li><span class="check-icon">✓</span> Detailed Report With Names</li>
                        <li><span class="check-icon">✓</span> For 1 Business Link</li>
                    </ul>
                    <a href="javascript:void(0)" onclick="scrollToOrder('starter')" class="btn btn-plan" data-package="starter">ORDER NOW</a>
                </div>

                <!-- Growth -->
                <div class="price-card featured">
                    <div class="popular-tag">NEW</div>
                    <div class="plan-name">Growth</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">300</span>
                        <span class="price-decimal">.00</span>
                    </div>
                    <div class="plan-reviews">35 Reviews</div>
                    <div class="savings-badge">Save $15 (4% OFF)</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> Local + Global Traveler Names</li>
                        <li><span class="check-icon">✓</span> Customized Content</li>
                        <li><span class="check-icon">✓</span> Gradual Posting: 1-3 Reviews Per Week<sup style="color:var(--ta-green-dark);font-weight:800;">*</sup></li>
                        <li><span class="check-icon">✓</span> Detailed Report With Names</li>
                        <li><span class="check-icon">✓</span> For 2 Business Links</li>
                    </ul>
                    <a href="javascript:void(0)" onclick="scrollToOrder('growth')" class="btn btn-plan" data-package="growth">ORDER NOW</a>
                </div>

                <!-- Performance -->
                <div class="price-card">
                    <div class="plan-name">Performance</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">400</span>
                        <span class="price-decimal">.00</span>
                    </div>
                    <div class="plan-reviews">50 Reviews</div>
                    <div class="savings-badge">Save $50 (10% OFF)</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> Local + Global Traveler Names</li>
                        <li><span class="check-icon">✓</span> Customized Content</li>
                        <li><span class="check-icon">✓</span> Gradual Posting: 1-3 Reviews Per Week<sup style="color:var(--ta-green-dark);font-weight:800;">*</sup></li>
                        <li><span class="check-icon">✓</span> Detailed Report With Names</li>
                        <li><span class="check-icon">✓</span> For 3 Business Links</li>
                    </ul>
                    <a href="javascript:void(0)" onclick="scrollToOrder('performance')" class="btn btn-plan" data-package="performance">ORDER NOW</a>
                </div>
            </div>
            <p style="max-width:760px; margin:0 auto; text-align:center; font-size:13px; line-height:1.6; color:var(--gray-500);">
                <span style="color:var(--ta-green-dark); font-weight:800;">*</span> Reviews are posted gradually (1–3 per week) to stay natural and compliant. Each review counts as <strong style="color:var(--gray-700);">delivered the moment it shows up</strong> on your listing (1 review = 1 delivered), and we <strong style="color:var(--gray-700);">replace any review that drops within 7 days, free (one replacement per review)</strong>. Reviews staying live past 7 days are considered final. All packages include this guarantee.
            </p>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Inline Order Form -->
    <section class="section order-form-section" id="order-form" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Order Now</div>
                <h2>Start Your Tripadvisor Boost</h2>
                <p class="section-desc">Fill in your details below to complete the order</p>
            </div>

            <div class="ob-modal-box" style="margin: 0 auto;">
                <div class="of-countdown">
                    <span>Offer expires in:</span>
                    <div class="of-countdown-blocks">
                        <span id="ofCdH">00</span>
                        <span style="background:transparent;padding:4px 0;min-width:0">:</span>
                        <span id="ofCdM">00</span>
                        <span style="background:transparent;padding:4px 0;min-width:0">:</span>
                        <span id="ofCdS">00</span>
                    </div>
                </div>

                <div class="of-field">
                    <label class="of-label" for="ofBizName">Tripadvisor Business Name + Location <span class="req">*</span></label>
                    <input type="text" id="ofBizName" class="of-input" placeholder="e.g. Sunset Boutique Hotel, Miami FL" autocomplete="off">
                </div>

                <div class="of-field">
                    <label class="of-label" for="ofWhatsapp">WhatsApp Number (for order updates) <span class="req">*</span></label>
                    <input type="tel" id="ofWhatsapp" class="of-input" placeholder="+1 Enter your WhatsApp number" autocomplete="off">
                </div>

                <div class="of-field">
                    <label class="of-label" for="ofEmail">Email Address</label>
                    <input type="email" id="ofEmail" class="of-input" placeholder="Enter your email address (optional)" autocomplete="off">
                </div>

                <div class="of-field">
                    <label class="of-label">Select Your Package:</label>
                    <div class="of-pkg-list">
                        <div class="of-pkg" data-pkg="starter" onclick="selectPkg(this)">
                            <div class="of-pkg-radio"></div>
                            <div class="of-pkg-info">
                                <div class="of-pkg-name">Starter — 28 Reviews</div>
                            </div>
                            <div class="of-pkg-price">$250</div>
                        </div>
                        <div class="of-pkg selected" data-pkg="growth" onclick="selectPkg(this)">
                            <div class="of-pkg-radio"></div>
                            <div class="of-pkg-info">
                                <div class="of-pkg-name">Growth — 35 Reviews <span class="of-pkg-tag">New</span></div>
                                <div class="of-pkg-detail"><s>$315</s> — Save $15</div>
                            </div>
                            <div class="of-pkg-price">$300</div>
                        </div>
                        <div class="of-pkg" data-pkg="performance" onclick="selectPkg(this)">
                            <div class="of-pkg-radio"></div>
                            <div class="of-pkg-info">
                                <div class="of-pkg-name">Performance — 50 Reviews</div>
                                <div class="of-pkg-detail"><s>$450</s> — Save $50</div>
                            </div>
                            <div class="of-pkg-price">$400</div>
                        </div>
                    </div>
                </div>

                <div class="of-guarantee" style="border:1px solid var(--ta-green); background:#f0fdf9; border-radius:10px; padding:13px 15px; margin-bottom:14px; font-size:13px; line-height:1.6; color:var(--dark);">
                    <strong style="display:block; margin-bottom:4px; color:var(--ta-green-dark);">✓ Free 7-Day Replacement Guarantee</strong>
                    You're protected. Each review counts as <strong>delivered the moment it shows up</strong> on your listing (1 review = 1 delivered). If any delivered review drops <strong>within 7 days</strong>, we <strong>replace it free, one time per review</strong> — no questions asked. Reviews staying live past 7 days are considered final. <span style="color:var(--gray-500);">By completing your order you agree to this fair-use policy.</span>
                </div>

                <button class="of-submit" onclick="submitOrder()">COMPLETE ORDER →</button>
                <p style="text-align:center; font-size:12px; color:var(--gray-500); margin-top:8px;">No recurring charges · One-time payment · Cancel anytime before campaign starts</p>

                <div class="of-trust">
                    <span>🔒 Secure Checkout</span>
                    <span>🛡 SSL Protected</span>
                    <span>✓ Account Safety</span>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Real Local Reviews -->
    <section class="section" id="reviews">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Proof</div>
                <h2>REAL TRAVELER REVIEWS</h2>
            </div>
            <div class="proof-grid">
                <div class="proof-card">
                    <div class="image-zoom-wrapper">
                        <img src="https://smart-buzzer.com/promo-tripadvisor/ta.jpg" alt="Tripadvisor Review Example 1" class="clickable-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Choose Your Own Sentences -->
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Customization</div>
                <h2>CHOOSE YOUR OWN SENTENCES</h2>
            </div>
            <div class="content-flex">
                <div class="content-image">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review Sentences" class="clickable-image">
                </div>
                <div class="content-text">
                    <p style="font-size: 18px; color: var(--gray-500); margin-bottom: 24px; line-height: 1.7;">
                        You choose the content, or let us create comprehensive variety for you. Every review is completely unique with zero repetition.
                    </p>
                    <p style="font-size: 16px; color: var(--gray-500); display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--ta-green); font-weight: 600;">✓</span> Human-written content tailored to your business
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Why People Use Our Services -->
    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Benefits</div>
                <h2>WHY PEOPLE USE OUR SERVICES</h2>
            </div>
            <div class="why-flex">
                <div class="why-image">
                    <img src="https://smart-buzzer.com/promo-tripadvisor/x.png" alt="Tripadvisor Review Example" class="clickable-image">
                </div>
                <div class="why-list">
                    <ul class="why-features">
                        <li style="border-left: 3px solid var(--ta-green);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Genuine Local + Global Traveler Names</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">A natural mix of local + global traveler names — exactly how real TripAdvisor reviews look</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--ta-green);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Unique users, IPs, devices, and aged accounts</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Complete technical authenticity guaranteed</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--ta-green);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Tailored reviews for your business</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Custom content that matches your services</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--ta-green);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Gradual posting (1–3 reviews per week)</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Natural pacing prevents algorithm detection</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Track Your Order -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Transparency</div>
                <h2>TRACK YOUR ORDER EVERY DAY<br>100% TRANSPARENCY</h2>
            </div>
            <div class="dashboard-preview">
                <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Progress Dashboard" class="clickable-image">
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Experience Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Experience</div>
                <h2>EXPERIENCED IN SERVING OVER 1200+ HOTELS, RESTAURANTS<br>& ATTRACTIONS ACROSS THE USA</h2>
            </div>
            <div class="dashboard-preview">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="Trello Board" class="clickable-image">
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Clients Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Trusted By</div>
                <h2>OUR CLIENTS</h2>
            </div>
            <div class="clients-grid">
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers4.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers7.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers1.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.43.55.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers8.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers6.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-11-at-15.44.07.png" alt="Client Logo">
                </div>
                <div class="client-logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Customers2.png" alt="Client Logo">
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">FAQ</div>
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-q" type="button">
                        <span>Is this safe for my Tripadvisor business listing?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-a">
                        Yes. Our gradual posting cadence (1–3 reviews per week, mixed 4-star and 5-star) is designed to look natural to Tripadvisor's algorithm. We use aged accounts with unique IPs and devices — the same patterns real travelers leave behind. Over 1,200 hospitality businesses have used this approach without issue.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q" type="button">
                        <span>Are reviewers real travelers (not bots)?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-a">
                        Yes — real human travelers, never bots or AI-generated profiles. Because TripAdvisor reviewers are travelers from everywhere, names are a natural mix of local + global — exactly how genuine TripAdvisor reviews look. Each review is human-written and tailored to your business, and you'll get a detailed report with reviewer names so you can verify authenticity.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q" type="button">
                        <span>What happens if Tripadvisor removes a review?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-a">
                        Some drops are normal — it's how Tripadvisor's algorithm works. Each review counts as delivered once it shows up on your listing (counted per review). If a delivered review drops <strong>within 7 days</strong> of posting, we <strong>replace it for free (one replacement per review)</strong>. After the 7-day window, that review is considered final and is no longer covered by the replacement guarantee.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q" type="button">
                        <span>How long until my reviews start appearing?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-a">
                        Reviews start appearing within the first week of order confirmation. For a 28-review Starter package, expect full delivery in ~3–4 weeks. Larger packages take longer — gradual pacing is what keeps your account safe. You'll track every step live on your dashboard.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q" type="button">
                        <span>Can I provide my own review content?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-a">
                        Yes. You can give us specific talking points, services to highlight, or full review text. Or let our team write custom content tailored to your business — every review is unique with zero repetition. You approve all content before posting.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Boost Your Tripadvisor Rating?</h2>
                <p>Join 1,200+ hotels, restaurants & attractions. Start building traveler credibility today.</p>
                <div class="cta-buttons">
                    <a href="#pricing" class="btn btn-primary btn-lg">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" height="50">
                    </div>
                    <div class="footer-desc">Specialized in social media engagement, product reviews, and online reputation services.</div>
                    <div class="footer-subsidiary"><i>A subsidiary of Pintarnya.</i></div>
                </div>
                <div>
                    <div class="footer-title">Quick Links</div>
                    <ul class="footer-links">
                        <li><a href="https://smart-buzzer.com/tracker">Track Campaign</a></li>
                        <li><a href="https://smart-buzzer.com/report">Report Issue</a></li>
                        <li><a href="https://smart-buzzer.com/service-tnc">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-title">Contact</div>
                    <ul class="footer-links">
                        <li><a href="https://wa.me/6285183081655?text=Hi%20Smart%20Buzzer%2C%20I%20want%20to%20order%20Tripadvisor%20Reviews">📞 WhatsApp: +62851-8308-1655</a></li>
                        <li><a href="mailto:contact@smart-buzzer.com">📧 Email: contact@smart-buzzer.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                © 2025 Smart Buzzer. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-content">
            <button class="lightbox-close" onclick="closeLightbox()">×</button>
            <img src="" alt="Preview" id="lightbox-img">
        </div>
    </div>

    <script>
    // ========================================
    // LIGHTBOX FUNCTIONS
    // ========================================
    function openLightbox(imgSrc) {
        var lightbox = document.getElementById('lightbox');
        var lightboxImg = document.getElementById('lightbox-img');
        lightboxImg.src = imgSrc;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        var lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Close lightbox on background click
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    // Close lightbox on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Add click event to all clickable images
    document.querySelectorAll('.clickable-image').forEach(function(img) {
        img.addEventListener('click', function() {
            openLightbox(this.src);
        });
    });
    </script>

    <!-- Analytics & Tracking Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ========================================
        // SCROLL DEPTH TRACKING
        // ========================================
        var scrollDepths = { 25: false, 50: false, 75: false, 100: false };
        
        function getScrollPercent() {
            var h = document.documentElement;
            var b = document.body;
            var st = 'scrollTop';
            var sh = 'scrollHeight';
            return Math.round((h[st] || b[st]) / ((h[sh] || b[sh]) - h.clientHeight) * 100);
        }
        
        window.addEventListener('scroll', function() {
            var percent = getScrollPercent();
            
            if (percent >= 25 && !scrollDepths[25]) {
                scrollDepths[25] = true;
                trackEvent('SCROLL_DEPTH_25', { depth: 25 });
            }
            if (percent >= 50 && !scrollDepths[50]) {
                scrollDepths[50] = true;
                trackEvent('SCROLL_DEPTH_50', { depth: 50 });
            }
            if (percent >= 75 && !scrollDepths[75]) {
                scrollDepths[75] = true;
                trackEvent('SCROLL_DEPTH_75', { depth: 75 });
            }
            if (percent >= 100 && !scrollDepths[100]) {
                scrollDepths[100] = true;
                trackEvent('SCROLL_DEPTH_100', { depth: 100 });
            }
        });

        // ========================================
        // TIME ON PAGE TRACKING
        // ========================================
        var pageStartTime = Date.now();
        
        window.addEventListener('beforeunload', function() {
            var timeSpent = Math.round((Date.now() - pageStartTime) / 1000);
            trackEvent('TIME_ON_PAGE', { duration: timeSpent });
            trackEvent('EXIT_PAGE', { exit_url: document.referrer, time_spent: timeSpent });
        });

        // ========================================
        // CLICK HEATMAP TRACKING
        // ========================================
        document.addEventListener('click', function(e) {
            var element = e.target.tagName.toLowerCase();
            if (e.target.className) {
                element += '.' + e.target.className.split(' ')[0];
            }
            trackEvent('CLICK_HEATMAP', {
                x: e.pageX,
                y: e.pageY,
                element: element
            });
        });

        // ========================================
        // PRICING BUTTON CLICK TRACKING (analytics only)
        // ========================================
        var pricingButtons = document.querySelectorAll('[data-package]');
        pricingButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                var packageName = this.getAttribute('data-package');
                trackEvent('ORDER_' + packageName.toUpperCase() + '_CLICK', {
                    package: packageName,
                    location: 'pricing'
                });
                // generate_lead dataLayer for lead tracking
                var meta = sbPkgMeta[packageName];
                if (meta) {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        event: 'generate_lead',
                        method: 'order_button',
                        value: meta.price,
                        currency: 'USD'
                    });
                }
            });
        });

        // ========================================
        // EXTERNAL LINK CLICK TRACKING
        // ========================================
        document.querySelectorAll('a[href^="http"]').forEach(function(link) {
            if (!link.href.includes(window.location.hostname)) {
                link.addEventListener('click', function() {
                    var location = 'body';
                    if (this.closest('header')) location = 'header';
                    if (this.closest('footer')) location = 'footer';
                    
                    trackEvent('EXTERNAL_LINK_CLICK', {
                        location: location,
                        url: this.href,
                        text: this.innerText.substring(0, 50)
                    });
                });
            }
        });

        // ========================================
        // SMOOTH SCROLL FOR ANCHOR LINKS
        // ========================================
        document.querySelectorAll('a[href^="#"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var targetId = this.getAttribute('href');
                var target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

    });

    // ========================================
    // INLINE ORDER FORM — scroll, package select, countdown, submitOrder
    // ========================================

    // Fanbasis payment URLs per package
    var fanbasisLinks = {
        'starter':     'https://www.fanbasis.com/agency-checkout/smartbuzzer/KAGGR',
        'growth':      'https://www.fanbasis.com/agency-checkout/smartbuzzer/OJGGp',
        'performance': 'https://www.fanbasis.com/agency-checkout/smartbuzzer/QAXXl'
    };

    // Scroll to order form + pre-select package
    function scrollToOrder(pkg) {
        if (pkg) {
            document.querySelectorAll('.of-pkg').forEach(function(el) {
                el.classList.toggle('selected', el.getAttribute('data-pkg') === pkg);
            });
        }
        var target = document.getElementById('order-form');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function selectPkg(el) {
        document.querySelectorAll('.of-pkg').forEach(function(p) { p.classList.remove('selected'); });
        el.classList.add('selected');
    }

    // Auto-save form inputs + restore on load
    document.addEventListener('DOMContentLoaded', function() {
        var fields = [
            {id: 'ofBizName',  key: 'sb_form_biz_ta'},
            {id: 'ofWhatsapp', key: 'sb_form_wa_ta'},
            {id: 'ofEmail',    key: 'sb_form_email_ta'}
        ];
        var s = safeLocalStorage();
        fields.forEach(function(f) {
            var el = document.getElementById(f.id);
            if (!el) return;
            var saved = s.getItem(f.key);
            if (saved) el.value = saved;
            el.addEventListener('input', function() { s.setItem(f.key, el.value); });
        });
    });

    // 24-hour rolling countdown — resets at midnight local time
    function tickCountdown() {
        var now = new Date();
        var end = new Date(now);
        end.setHours(23, 59, 59, 999);
        var diff = end - now;
        if (diff < 0) diff = 0;
        var h = Math.floor(diff / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        var pad = function(n) { return n < 10 ? '0' + n : '' + n; };
        var hEl = document.getElementById('ofCdH');
        var mEl = document.getElementById('ofCdM');
        var sEl = document.getElementById('ofCdS');
        if (hEl) hEl.textContent = pad(h);
        if (mEl) mEl.textContent = pad(m);
        if (sEl) sEl.textContent = pad(s);
    }
    setInterval(tickCountdown, 1000);
    tickCountdown();

    // ========================================
    // SUBMIT ORDER — validation + GTM dataLayer + LocalStorage bridge + log.php POST + Fanbasis redirect
    // ========================================
    function submitOrder() {
        var biz   = document.getElementById('ofBizName').value.trim();
        var wa    = document.getElementById('ofWhatsapp').value.trim();
        var email = document.getElementById('ofEmail').value.trim();
        var sel   = document.querySelector('.of-pkg.selected');

        if (!biz) { alert('Please enter your Tripadvisor business name.'); return; }
        if (!wa)  { alert('Please enter your WhatsApp number.'); return; }
        if (!sel) { alert('Please select a package.'); return; }

        var pkgValue = sel.getAttribute('data-pkg');
        var meta = sbPkgMeta[pkgValue] || sbPkgMeta.growth;
        var txnId = 'SB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

        var nameParts = biz.split(' ');
        var firstName = nameParts[0] || '';
        var lastName  = nameParts.slice(1).join(' ') || '';

        // === GTM: begin_checkout (GA4 + user_data) ===
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

        // === GTM: add_payment_info (same click, GA4) ===
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

        // === LocalStorage bridge: persist user_data for purchase event on thankyou.php ===
        localStorage.setItem('sb_user_email', email || '');
        localStorage.setItem('sb_user_phone', wa || '');
        localStorage.setItem('sb_user_fname', firstName || '');
        localStorage.setItem('sb_user_lname', lastName || '');
        localStorage.setItem('sb_txn_id', txnId);
        localStorage.setItem('sb_pkg', pkgValue);

        // === Facebook Pixel fallback (managed via GTM primarily) ===
        if (typeof fbq !== 'undefined') {
            fbq('track', 'InitiateCheckout', {
                value: meta.price,
                currency: 'USD',
                content_name: meta.name,
                content_type: 'product',
                content_ids: [meta.id]
            });
        }

        // === Analytics: ORDER_SUBMIT ===
        trackEvent('ORDER_SUBMIT', {
            package: pkgValue,
            price: meta.price,
            business: biz,
            location: 'modal'
        });

        // === Log to customer_data.log via log.php (FORM_SUBMIT) ===
        fetch('log.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                businessName: biz,
                whatsapp: wa,
                businessEmail: email,
                action: 'TRIPADVISOR_' + pkgValue.toUpperCase(),
                pageUrl: window.location.href
            })
        }).catch(function(err) { console.log('Customer log error:', err); });

        // Clear auto-saved form data
        try {
            localStorage.removeItem('sb_form_biz_ta');
            localStorage.removeItem('sb_form_wa_ta');
            localStorage.removeItem('sb_form_email_ta');
        } catch (e) {}

        // Redirect to Fanbasis payment gateway
        var paymentUrl = fanbasisLinks[pkgValue];
        if (paymentUrl) {
            window.location.href = paymentUrl;
        }
    }

    // ========================================
    // FAQ ACCORDION
    // ========================================
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.faq-q').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = this.parentElement;
                var wasOpen = item.classList.contains('open');
                // Close all
                document.querySelectorAll('.faq-item').forEach(function(i) { i.classList.remove('open'); });
                // Toggle this one
                if (!wasOpen) item.classList.add('open');
            });
        });
    });
    </script>

    <!-- Floating CTWA Sales Button -->
    <a href="https://wa.me/628979133204?text=Hi%20Smart%20Buzzer!%20I%20saw%20your%20Tripadvisor%20packages%20and%20I'd%20like%20to%20get%20a%20quote%20%26%20check%20my%20listing."
       id="ctwaSalesBtn" class="ctwa-fab" target="_blank" rel="noopener" aria-label="Chat with Sales on WhatsApp">
        <span class="ctwa-icon">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="#fff" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/>
            </svg>
        </span>
        <span class="ctwa-text">
            <span class="ctwa-label">Chat with Sales</span>
            <span class="ctwa-sub"><span class="ctwa-online"></span>Online now · replies in minutes</span>
        </span>
    </a>
    <style>
        .ctwa-fab {
            position: fixed; bottom: 22px; right: 22px; z-index: 9999;
            display: inline-flex; align-items: center; gap: 11px;
            background: #25D366; color: #fff; text-decoration: none;
            padding: 10px 18px 10px 12px; border-radius: 50px;
            box-shadow: 0 6px 20px rgba(37,211,102,.45);
            font-family: inherit; transition: transform .15s ease, box-shadow .15s ease;
            animation: ctwaPulse 2.4s infinite;
        }
        .ctwa-fab:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(37,211,102,.55); animation: none; }
        .ctwa-icon { display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,.18); flex-shrink:0; }
        .ctwa-text { display:flex; flex-direction:column; line-height:1.15; }
        .ctwa-label { font-size:15px; font-weight:700; white-space:nowrap; }
        .ctwa-sub { font-size:11px; font-weight:500; opacity:.92; white-space:nowrap; display:flex; align-items:center; gap:5px; }
        .ctwa-online { width:7px; height:7px; border-radius:50%; background:#bbf7d0; box-shadow:0 0 0 0 rgba(187,247,208,.8); animation:ctwaBlink 1.6s infinite; }
        @keyframes ctwaPulse { 0%{box-shadow:0 6px 20px rgba(37,211,102,.45),0 0 0 0 rgba(37,211,102,.45);} 70%{box-shadow:0 6px 20px rgba(37,211,102,.45),0 0 0 14px rgba(37,211,102,0);} 100%{box-shadow:0 6px 20px rgba(37,211,102,.45),0 0 0 0 rgba(37,211,102,0);} }
        @keyframes ctwaBlink { 0%,100%{opacity:1;} 50%{opacity:.35;} }
        @media (max-width: 600px) {
            .ctwa-fab { bottom: 16px; right: 16px; padding: 8px; gap: 0; }
            .ctwa-text { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { .ctwa-fab, .ctwa-online { animation: none; } }
    </style>
    <script>
        document.getElementById('ctwaSalesBtn').addEventListener('click', function() {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'generate_lead',
                method: 'WhatsApp',
                value: (typeof sbPkgMeta !== 'undefined' && sbPkgMeta.growth) ? sbPkgMeta.growth.price : 0,
                currency: 'USD'
            });
            if (typeof trackEvent === 'function') {
                trackEvent('CTWA_SALES_CLICK', { location: 'floating_button', destination: 'whatsapp_sales' });
            }
        });
    </script>
</body>
</html>