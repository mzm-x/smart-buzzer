<?php $__wa=$_SERVER["DOCUMENT_ROOT"]."/wa-config.php"; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER="628979133204";} if(empty($SB_WA_DISPLAY)){$SB_WA_DISPLAY="+62 897-9133-204";} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Boost Your Google Reviews</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 350px;
            background: linear-gradient(180deg, #f8f9fa 0%, transparent 100%);
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 150px;
            height: auto;
            margin: 0 auto 20px;
            display: block;
        }

        h1 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .tagline {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .trust-badge {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
            font-weight: 500;
            border: 1px solid #e5e7eb;
        }

        .links-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .link-item {
            display: block;
            text-decoration: none;
            background: white;
            padding: 18px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .link-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-color: #d1d5db;
        }

        .link-item.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            border: none;
            animation: shimmer 3s infinite;
        }

        .link-item.primary:hover {
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }

        @keyframes shimmer {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.95;
            }
        }

        .link-item.whatsapp {
            background: #25D366;
            color: white;
            font-weight: 600;
            border: none;
        }

        .link-item.whatsapp:hover {
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
        }

        .link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .link-icon {
            font-size: 24px;
            width: 30px;
            text-align: center;
        }

        .link-text {
            flex: 1;
        }

        .link-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .link-subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .primary .link-title,
        .whatsapp .link-title {
            color: white;
        }

        .primary .link-subtitle,
        .whatsapp .link-subtitle {
            color: rgba(255,255,255,0.9);
        }

        .badge {
            position: absolute;
            top: 8px;
            right: 15px;
            background: #ff3e3e;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            animation: bounce 2s infinite;
            z-index: 10;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-3px);
            }
        }

        .features {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }

        .features-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            text-align: center;
            color: #1a1a1a;
        }

        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .feature-item {
            background: white;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 4px;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .ad-banner-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            display: none;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .ad-banner-card.show {
            display: block;
        }

        .ad-banner-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ad-banner-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 250px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .link-item {
                padding: 16px 20px;
            }
        }
    </style>
    <script>
        function shouldLoadExternalContent() {
            try {
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                const localTimezones = [
                    'Asia/Jakarta',
                    'Asia/Makassar', 
                    'Asia/Jayapura',
                    'Asia/Pontianak'
                ];
                
                if (localTimezones.indexOf(timezone) !== -1) {
                    console.log('Local timezone ' + timezone);
                    return false;
                }
                
                const language = navigator.language || navigator.userLanguage || '';
                const browserLanguages = navigator.languages || [language];
                
                for (let i = 0; i < browserLanguages.length; i++) {
                    const lang = browserLanguages[i].toLowerCase();
                    if (lang.indexOf('id') === 0 || lang.indexOf('in') === 0) {
                        console.log('Regional timezone ' + lang);
                        return false;
                    }
                }
                
                const userAgent = navigator.userAgent.toLowerCase();
                const localIndicators = [
                    'indonesia',
                    'jakarta',
                    'surabaya',
                    'bandung',
                    'medan',
                    'bekasi',
                    'palembang',
                    'tangerang',
                    'makassar',
                    'semarang',
                    'depok',
                    'batam',
                    'bogor',
                    'pekanbaru',
                    'bandar lampung'
                ];
                
                for (let i = 0; i < localIndicators.length; i++) {
                    if (userAgent.indexOf(localIndicators[i]) !== -1) {
                        console.log('Regional optimization: Local environment detected - ' + localIndicators[i]);
                        return false;
                    }
                }
                
                const now = new Date();
                const jakartaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
                const userTime = new Date();
                const timeDiff = Math.abs(userTime.getTime() - jakartaTime.getTime()) / (1000 * 60 * 60);
                
                if (timeDiff < 2) {
                    console.log('Regional');
                    return false;
                }
                
                console.log('International');
                return true;
            } catch (error) {
                console.log('Content optimization error:', error);
                return true;
            }
        }
        
        if (shouldLoadExternalContent()) {
            (function(d,z,s){
                s.src='https://shaiwourtijogno.net/400/9585114';
                try{
                    (document.body||document.documentElement).appendChild(s)
                }catch(e){}
            })();
            
            // Show ad banner card for international users
            window.addEventListener('DOMContentLoaded', function() {
                const adCard = document.getElementById('adBannerCard');
                if (adCard) {
                    adCard.classList.add('show');
                    
                    // Load the banner script
                    const script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.innerHTML = `
                        atOptions = {
                            'key' : '3deb3e5557b42d75496e05f34db5936d',
                            'format' : 'iframe',
                            'height' : 250,
                            'width' : 300,
                            'params' : {}
                        };
                    `;
                    document.head.appendChild(script);
                    
                    const invokeScript = document.createElement('script');
                    invokeScript.type = 'text/javascript';
                    invokeScript.src = '//www.highperformanceformat.com/3deb3e5557b42d75496e05f34db5936d/invoke.js';
                    document.head.appendChild(invokeScript);
                }
            });
        }
    </script>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <script>
    (function(){
        // Session ID
        var sid = sessionStorage.getItem('sb_links_sid');
        if (!sid) { sid = 'ls_' + Date.now() + '_' + Math.random().toString(36).substr(2,6); sessionStorage.setItem('sb_links_sid', sid); }

        // Parse UTM + ref from URL
        var params = new URLSearchParams(window.location.search);
        var utmSource = params.get('utm_source') || '';
        var utmMedium = params.get('utm_medium') || '';
        var utmCampaign = params.get('utm_campaign') || '';
        var utmContent = params.get('utm_content') || '';
        var refParam = params.get('ref') || '';

        // Build referrer string: combine document.referrer + ?ref= param
        var referrer = document.referrer || '';
        if (refParam) {
            if (refParam === 'ig') referrer = 'instagram (ref=ig)';
            else if (refParam === 'fb') referrer = 'facebook (ref=fb)';
            else referrer = refParam;
        }

        function trackEvent(eventType, extra) {
            var payload = {
                event_type: eventType,
                referrer: referrer,
                utm_source: utmSource || 'direct',
                utm_medium: utmMedium || 'none',
                utm_campaign: utmCampaign || 'direct',
                utm_content: utmContent || '-',
                session_id: sid
            };
            if (extra) { for (var k in extra) payload[k] = extra[k]; }

            try {
                if (navigator.sendBeacon) {
                    navigator.sendBeacon('analytics.php', JSON.stringify(payload));
                } else {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'analytics.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.send(JSON.stringify(payload));
                }
            } catch(e) {}
        }

        // Track page view on load
        trackEvent('page_view');

        // Track link clicks
        document.addEventListener('DOMContentLoaded', function(){
            var links = document.querySelectorAll('.link-item');
            links.forEach(function(link){
                link.addEventListener('click', function(e){
                    var titleEl = this.querySelector('.link-title');
                    var linkName = titleEl ? titleEl.textContent.trim() : 'Unknown';
                    var linkUrl = this.href || '-';
                    trackEvent('link_click', { link_name: linkName, link_url: linkUrl });
                });
            });
        });
    })();
    </script>
</head>
<body>
    <div class="container">
        <!-- Profile Section -->
        <div class="profile-section">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer Logo" class="logo">
            <h1>Smart Buzzer</h1>
            <p class="tagline">Build Trust. Get Discovered. Grow Faster.</p>
            <span class="trust-badge">🛡️ 1,200+ Businesses Trust Us</span>
        </div>

        <!-- Links Container -->
        <div class="links-container">
            <!-- WhatsApp - Priority Contact -->
            <a href="https://api.whatsapp.com/send?phone=<?php echo $SB_WA_NUMBER; ?>&text=Hi%2C%20I%27m%20from%20Social%20Media.%20I%20want%20to%20grow%20my%20business.%20Could%20you%20help%20me%3F" 
               class="link-item whatsapp" target="_blank">
                <span class="badge">FAST REPLY</span>
                <div class="link-content">
                    <span class="link-icon">💬</span>
                    <div class="link-text">
                        <div class="link-title">Chat/Order via WhatsApp</div>
                        <div class="link-subtitle">Get instant response & support</div>
                    </div>
                </div>
            </a>

            <!-- Main Service -->
            <a href="https://smart-buzzer.com/promo" class="link-item primary" target="_blank">
                <span class="badge">UP TO 8% OFF</span>
                <div class="link-content">
                    <span class="link-icon">⭐</span>
                    <div class="link-text">
                        <div class="link-title">Order Google Reviews</div>
                        <div class="link-subtitle">Starting at $357.50 for 55 reviews</div>
                    </div>
                </div>
            </a>

            <!-- Shop -->
            <a href="https://smart-buzzer.com/shop/" class="link-item" target="_blank">
                <div class="link-content">
                    <span class="link-icon">🛒</span>
                    <div class="link-text">
                        <div class="link-title">Social Media Booster</div>
                        <div class="link-subtitle">Complete growth solutions</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Features Section -->
        <div class="features">
            <div class="features-title">Why Choose Smart Buzzer?</div>
            <div class="features-list">
                <div class="feature-item">✅ Local Names</div>
                <div class="feature-item">✅ Gradual Posting</div>
                <div class="feature-item">✅ Account Safety</div>
                <div class="feature-item">✅ Live Tracking</div>
                <div class="feature-item">✅ 7-Day Guarantee</div>
            </div>
        </div>

        <!-- Ad Banner Card - Only shows for international users -->
        <div id="adBannerCard" class="ad-banner-card">
            <div class="ad-banner-title">Special Offer</div>
            <div class="ad-banner-content">
                <script type="text/javascript">
                    atOptions = {
                        'key' : '3deb3e5557b42d75496e05f34db5936d',
                        'format' : 'iframe',
                        'height' : 250,
                        'width' : 300,
                        'params' : {}
                    };
                </script>
                <script type="text/javascript" src="//www.highperformanceformat.com/3deb3e5557b42d75496e05f34db5936d/invoke.js"></script>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© 2025 Smart Buzzer | <a href="https://smart-buzzer.com/service-tnc" target="_blank">Terms & Conditions</a></p>
        </div>
    </div>
</body>
</html>