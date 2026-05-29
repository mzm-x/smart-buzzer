<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Get authentic Yelp reviews. 1,200+ clients. Starting $357.50 for 55 reviews.">
    <title>Yelp Reviews - Smart Buzzer | 1,200+ Happy Clients</title>
    
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '938738044322271');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=938738044322271&ev=PageView&noscript=1"
    /></noscript>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --yelp-red: #D32323;
            --yelp-red-dark: #A91B1B;
            --dark: #0A0A0A;
            --gray-900: #1A1A1A;
            --gray-700: #3F3F3F;
            --gray-500: #737373;
            --gray-300: #D4D4D4;
            --gray-100: #F5F5F5;
            --gray-50: #FAFAFA;
        }

        body {
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.5;
            color: var(--gray-900);
            background: #fff;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
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
            background: var(--yelp-red);
            color: #fff;
            box-shadow: 0 1px 2px rgba(211, 35, 35, 0.1);
        }

        .btn-primary:hover {
            background: var(--yelp-red-dark);
            box-shadow: 0 4px 12px rgba(211, 35, 35, 0.2);
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

        .badge-red {
            color: var(--yelp-red-dark);
        }

        h1 {
            font-size: 56px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--dark);
            margin-bottom: 24px;
        }

        .text-red {
            color: var(--yelp-red);
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
            background: var(--yelp-red);
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
            color: var(--yelp-red);
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
            color: var(--yelp-red-dark);
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
            border-color: var(--yelp-red);
            box-shadow: 0 20px 40px -12px rgba(211, 35, 35, 0.15);
            transform: translateY(-4px);
        }

        .price-card.featured {
            border: 2px solid var(--yelp-red);
            box-shadow: 0 10px 30px -8px rgba(211, 35, 35, 0.2);
        }

        .popular-tag {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--yelp-red);
            color: #fff;
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
            color: var(--yelp-red-dark);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .savings-badge {
            display: inline-block;
            background: #FEE2E2;
            color: #DC2626;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 24px;
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
            background: var(--dark);
            color: #fff;
            padding: 14px;
            font-weight: 700;
        }

        .btn-plan:hover {
            background: var(--gray-700);
        }

        .price-card.featured .btn-plan {
            background: var(--yelp-red);
            color: #fff;
        }

        .price-card.featured .btn-plan:hover {
            background: var(--yelp-red-dark);
        }

        /* Process */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
        }

        .process-item {
            text-align: center;
        }

        .process-number {
            width: 64px;
            height: 64px;
            background: var(--yelp-red);
            color: #fff;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(211, 35, 35, 0.25);
        }

        .process-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .process-desc {
            font-size: 15px;
            color: var(--gray-500);
            line-height: 1.5;
        }

        /* Proof Section */
        .proof-grid {
            display: grid;
            grid-template-columns: 1fr;
            max-width: 900px;
            margin: 0 auto;
        }

        .proof-card {
            border-radius: 16px;
            overflow: hidden;
        }

        .image-zoom-wrapper {
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .image-zoom-wrapper img {
            width: 100%;
            height: auto;
            transition: transform 0.3s;
        }

        .image-zoom-wrapper:hover img {
            transform: scale(1.05);
        }

        /* Content Section */
        .content-section {
            padding: 96px 0;
            background: #fff;
        }

        .content-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .content-image img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .content-text p {
            font-size: 18px;
            color: var(--gray-500);
            margin-bottom: 24px;
            line-height: 1.7;
        }

        /* Why Section */
        .why-section {
            padding: 96px 0;
            background: var(--gray-50);
        }

        .why-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .why-image img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            border-left: 3px solid var(--yelp-red);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .why-features .check-icon {
            width: 24px;
            height: 24px;
            background: var(--yelp-red);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
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
            max-width: 720px;
            margin: 0 auto;
        }

        .cta h2 {
            font-size: 48px;
            color: #fff;
            margin-bottom: 16px;
        }

        .cta p {
            font-size: 19px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 36px;
        }

        .cta-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        /* Footer */
        .footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            padding: 64px 0 32px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand {
            margin-bottom: 12px;
        }

        .footer-brand img {
            height: 36px;
            width: auto;
        }

        .footer-desc {
            font-size: 15px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        .footer-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 16px;
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
            color: var(--yelp-red);
        }

        .footer-bottom {
            padding-top: 32px;
            border-top: 1px solid var(--gray-200);
            text-align: center;
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

            h1 {
                font-size: 44px;
            }

            h2 {
                font-size: 36px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .clients-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .nav-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="#" class="logo">
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                </a>
                <div class="nav-buttons">
                    <a href="https://wa.me/6285183081655" class="btn btn-secondary">💬 WhatsApp</a>
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
                        <span style="color: var(--yelp-red)">★★★★★</span>
                        <span>1,200+ Happy Clients</span>
                    </div>
                    <h1>Boost Your <span class="text-red">Yelp Rating</span> With Authentic Reviews</h1>
                    <p>Get genuine Yelp reviews from local accounts. Safe, natural posting. Trusted by 1,200+ clients worldwide.</p>
                    <div class="hero-buttons">
                        <a href="#pricing" class="btn btn-primary btn-lg">Get Started →</a>
                    </div>
                    <div class="features-list">
                        <div class="feature-item">
                            <span class="check-icon">✓</span>
                            100% Local Names
                        </div>
                        <div class="feature-item">
                            <span class="check-icon">✓</span>
                            Gradual Posting (1-3/day)
                        </div>
                        <div class="feature-item">
                            <span class="check-icon">✓</span>
                            Real-Time Tracking
                        </div>
                        <div class="feature-item">
                            <span class="check-icon">✓</span>
                            Customized Content
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://smart-buzzer.com/yelp/yelp.webp" alt="Yelp Dashboard">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-value">1,200+</div>
                    <div class="stat-label">Happy Clients</div>
                </div>
                <div class="stat">
                    <div class="stat-value">50K+</div>
                    <div class="stat-label">Reviews Delivered</div>
                </div>
                <div class="stat">
                    <div class="stat-value">90%+</div>
                    <div class="stat-label">Retention Rate</div>
                </div>
                <div class="stat">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Account Bans</div>
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
                <p class="section-desc">Transparent pricing. Advance payment required. Reviews appear within 24 hours.</p>
            </div>

            <div class="pricing-grid">
                <!-- Starter -->
                <div class="price-card">
                    <div class="plan-name">Starter</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">357</span>
                        <span class="price-decimal">.50</span>
                    </div>
                    <div class="plan-reviews">55 Reviews</div>
                    <div class="plan-per-review">$6.50 per review</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 100% Local Names</li>
                        <li><span class="check-icon">✓</span> Gradual Posting</li>
                        <li><span class="check-icon">✓</span> Human-Written</li>
                        <li><span class="check-icon">✓</span> Live Tracking</li>
                        <li><span class="check-icon">✓</span> 7-Day Replacement</li>
                    </ul>
                    <a href="https://smart-buzzer.com/yelp?package=starter" class="btn btn-plan">Get Started</a>
                </div>

                <!-- Growth -->
                <div class="price-card featured">
                    <div class="popular-tag">Most Popular</div>
                    <div class="plan-name">Growth</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">550</span>
                        <span class="price-decimal">.00</span>
                    </div>
                    <div class="plan-reviews">88 Reviews</div>
                    <div class="plan-per-review">$6.25 per review</div>
                    <div class="savings-badge">Save $22 (4% OFF)</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 100% Local Names</li>
                        <li><span class="check-icon">✓</span> Gradual Posting</li>
                        <li><span class="check-icon">✓</span> Human-Written</li>
                        <li><span class="check-icon">✓</span> Live Tracking</li>
                        <li><span class="check-icon">✓</span> 7-Day Replacement</li>
                        <li><span class="check-icon">✓</span> Priority Support</li>
                    </ul>
                    <a href="https://smart-buzzer.com/yelp?package=growth" class="btn btn-plan">Get Started</a>
                </div>

                <!-- Performance -->
                <div class="price-card">
                    <div class="plan-name">Performance</div>
                    <div class="plan-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">660</span>
                        <span class="price-decimal">.00</span>
                    </div>
                    <div class="plan-reviews">110 Reviews</div>
                    <div class="plan-per-review">$6.00 per review</div>
                    <div class="savings-badge">Save $55 (8% OFF)</div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 100% Local Names</li>
                        <li><span class="check-icon">✓</span> Gradual Posting</li>
                        <li><span class="check-icon">✓</span> Human-Written</li>
                        <li><span class="check-icon">✓</span> Live Tracking</li>
                        <li><span class="check-icon">✓</span> 7-Day Replacement</li>
                        <li><span class="check-icon">✓</span> Priority Support</li>
                        <li><span class="check-icon">✓</span> Dedicated Manager</li>
                    </ul>
                    <a href="https://smart-buzzer.com/yelp?package=performance" class="btn btn-plan">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Process -->
    <section class="section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">How It Works</div>
                <h2>4 Simple Steps</h2>
                <p class="section-desc">Get started in minutes. Reviews appear within 24 hours.</p>
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
                    <div class="process-desc">Reviews posted 1-3 per day using aged accounts and unique IPs.</div>
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

    <!-- Real Local Reviews -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Proof</div>
                <h2>REAL LOCAL REVIEWS</h2>
            </div>
            <div class="proof-grid">
                <div class="proof-card">
                    <div class="image-zoom-wrapper">
                        <img src="https://smart-buzzer.com/yelp/yelp.jpg" alt="Review Example 1">
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
                    <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Screenshot-2024-12-19-at-16.30.55-2048x1097.png" alt="Review Sentences">
                </div>
                <div class="content-text">
                    <p style="font-size: 18px; color: var(--gray-500); margin-bottom: 24px; line-height: 1.7;">
                        You choose the content, or let us create comprehensive variety for you. Every review is completely unique with zero repetition.
                    </p>
                    <p style="font-size: 16px; color: var(--gray-500); display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--yelp-red); font-weight: 600;">✓</span> Human-written content tailored to your business
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
                    <img src="https://smart-buzzer.com/yelp/yelp1.jpg" alt="Review Example">
                </div>
                <div class="why-list">
                    <ul class="why-features">
                        <li style="border-left: 3px solid var(--yelp-red);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">100% Genuine Local Name Reviews</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Every reviewer has authentic local identity</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--yelp-red);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Unique users, IPs, devices, and aged accounts</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Complete technical authenticity guaranteed</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--yelp-red);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Tailored reviews for your business</strong>
                                <span style="font-size: 14px; color: var(--gray-500);">Custom content that matches your services</span>
                            </div>
                        </li>
                        <li style="border-left: 3px solid var(--yelp-red);">
                            <div class="check-icon">✓</div>
                            <div>
                                <strong style="display: block; font-size: 16px; color: var(--dark); margin-bottom: 4px;">Gradual posting (1–2 reviews per day)</strong>
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
                <img src="https://smart-buzzer.com/wp-content/uploads/2025/08/Screenshot-2025-08-24-at-23.27.11.webp" alt="Campaign Progress Dashboard">
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Experience Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Experience</div>
                <h2>EXPERIENCED IN SERVING OVER 1200+ BUSINESSES<br>ACROSS THE USA AND CANADA</h2>
            </div>
            <div class="dashboard-preview">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/Untitleddesign1.jpg" alt="Trello Board">
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

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Boost Your Yelp Rating?</h2>
                <p>Join 1,200+ businesses. Start building credibility today.</p>
                <div class="cta-buttons">
                    <a href="#pricing" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="https://wa.me/6285183081655" class="btn btn-secondary btn-lg">💬 WhatsApp</a>
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
                        <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer">
                    </div>
                    <div class="footer-desc">Professional Yelp review service. Build trust, get discovered, grow faster.</div>
                </div>
                <div>
                    <div class="footer-title">Services</div>
                    <ul class="footer-links">
                        <li><a href="#">Google Reviews</a></li>
                        <li><a href="#">Trustpilot Reviews</a></li>
                        <li><a href="#">Yelp Reviews</a></li>
                        <li><a href="#">Facebook Reviews</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-title">Contact</div>
                    <ul class="footer-links">
                        <li><a href="https://wa.me/6285183081655">WhatsApp</a></li>
                        <li><a href="mailto:contact@smart-buzzer.com">Email</a></li>
                        <li><a href="https://smart-buzzer.com/tracker/">Track Order</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                © 2025 Smart Buzzer. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>