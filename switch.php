<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Service Conversion Calculator</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-section {
            background: #fafafa;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e5e5e5;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        select, input {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
        }
        
        select:focus, input:focus {
            outline: none;
            border-color: #4285f4;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .calculate-btn {
            background: #4285f4;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .calculate-btn:hover {
            background: #3367d6;
        }
        
        .results {
            display: none;
        }
        
        .conversion-section {
            background: #fff8e1;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 24px;
            margin-top: 30px;
        }
        
        .conversion-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .conversion-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        
        .conversion-option {
            position: relative;
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
        }
        
        .conversion-option h4 {
            color: #1a1a1a;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .remaining-display {
            font-size: 20px;
            font-weight: 600;
            color: #4285f4;
            margin: 12px 0;
        }
        
        .conversion-option p {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }
        
        .mix-inputs {
            margin: 12px 0;
        }
        
        .mix-inputs input {
            width: 70px;
            margin: 4px;
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .mix-inputs button {
            padding: 8px 16px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin: 4px;
        }
        
        .mix-inputs button:hover {
            background: #3367d6;
        }
        
        .alert {
            background: #f8f9fa;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 16px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        
        .alert strong {
            color: #1a1a1a;
        }
        
        #mixResult {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .chat-section {
            margin-top: 30px;
            background: #f0f8ff;
            border: 1px solid #4285f4;
            border-radius: 8px;
            padding: 24px;
        }
        
        .chat-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            text-align: center;
        }
        
        .chat-box {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .chat-content {
            padding: 16px;
            min-height: 120px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.5;
            white-space: pre-wrap;
            background: #fafafa;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .copy-btn {
            background: #34a853;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .copy-btn:hover {
            background: #2d8a47;
        }
        
        .copy-btn.copied {
            background: #1a73e8;
        }
        
        .section-divider {
            height: 1px;
            background: #eee;
            margin: 50px 0;
        }

        .sm-toggle {
            display: flex;
            gap: 24px;
            padding: 12px 0 2px;
        }

        .sm-toggle label {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            margin-bottom: 0;
        }

        .sm-toggle input {
            width: auto;
            padding: 0;
            margin: 0;
        }

        .sm-pop {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #fbbc04;
            color: #1a1a1a;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .sm-helper {
            background: #fff;
            border: 1px dashed #d5d5d5;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .sm-helper-label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }

        .sm-helper-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .sm-helper-row input {
            width: 120px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .sm-helper-row span {
            color: #888;
            font-weight: 600;
        }

        .sm-helper-row button {
            padding: 8px 14px;
            background: #4285f4;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
        }

        .sm-helper-row button:hover {
            background: #3367d6;
        }

        .sm-helper-note {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .conversion-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Converter</h1>
            <p>Switch from ratings to reviews or mix both services</p>
        </div>
        
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label for="currency">Location</label>
                    <select id="currency">
                        <option value="USD">🇺🇸 United States</option>
                        <option value="CAD">🇨🇦 Canada</option>
                        <option value="AUD">🇦🇺 Australia</option>
                        <option value="GBP">🇬🇧 United Kingdom</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="originalAmount">Amount paid (<span id="currencyLabel">$</span>)</label>
                    <input type="number" id="originalAmount" placeholder="460" min="0" step="0.01">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="originalQuantity">Quantity ordered</label>
                    <input type="number" id="originalQuantity" placeholder="110" min="0">
                </div>
                
                <div class="form-group">
                    <label for="completedQuantity">Quantity Show Up</label>
                    <input type="number" id="completedQuantity" placeholder="10" min="0">
                </div>
            </div>
            
            <button class="calculate-btn" onclick="calculateConversion()">
                Calculate Options
            </button>
        </div>
        
        <div class="results" id="results">
            <div class="conversion-section">
                <div class="conversion-title">Options for Remaining Order</div>
                <div class="conversion-grid">
                    <div class="conversion-option">
                        <h4>Switch All to Reviews</h4>
                        <div class="remaining-display" id="allToReviews">0 reviews</div>
                        <p>Use all remaining money for reviews with written comments</p>
                    </div>
                    
                    <div class="conversion-option">
                        <h4>Keep Ratings Only</h4>
                        <div class="remaining-display" id="keepRatingOnly">0 ratings</div>
                        <p>Continue with original ratings-only order</p>
                    </div>
                    
                    <div class="conversion-option">
                        <h4>Mix Both</h4>
                        <div class="mix-inputs">
                            <input type="number" id="mixReviews" placeholder="Reviews">
                            <input type="number" id="mixRatings" placeholder="Ratings">
                            <button onclick="calculateMix()">Check</button>
                        </div>
                        <div id="mixResult"></div>
                    </div>
                </div>
            </div>
            
            <div class="alert" id="alertMessage"></div>
            
            <div class="chat-section">
                <div class="chat-title">WhatsApp Message for Client</div>
                <div class="chat-box">
                    <div class="chat-content" id="chatContent">
                        <!-- Generated message will appear here -->
                    </div>
                    <button class="copy-btn" id="copyBtn" onclick="copyToClipboard()">Copy Text</button>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="header">
            <h1>Reviews &rarr; Social Media</h1>
            <p>Convert a review balance into social media followers or likes</p>
        </div>

        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label for="smBudget">Budget ($) &mdash; actual amount paid</label>
                    <input type="number" id="smBudget" placeholder="357.50" min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label>Service</label>
                    <div class="sm-toggle">
                        <label><input type="radio" name="smMetric" value="followers" checked> Followers</label>
                        <label><input type="radio" name="smMetric" value="likes"> Likes</label>
                    </div>
                </div>
            </div>

            <div class="sm-helper">
                <span class="sm-helper-label">Estimate budget from reviews (optional)</span>
                <div class="sm-helper-row">
                    <input type="number" id="smReviews" placeholder="# reviews" min="0">
                    <span>&times;</span>
                    <input type="number" id="smRate" value="6.50" min="0" step="0.01" title="Price per review (edit for discounts)">
                    <span>=</span>
                    <button type="button" id="smApplyEst" onclick="applyReviewEstimate()">Use estimate</button>
                </div>
                <p class="sm-helper-note">Fills Budget only when it's empty. Got a discount? Just type the real amount in Budget &mdash; it always wins. Click the button to overwrite Budget with this estimate.</p>
            </div>

            <button class="calculate-btn" onclick="calculateSocial()">
                Calculate Social Media
            </button>
        </div>

        <div class="results" id="smResults">
            <div class="conversion-section">
                <div class="conversion-title">Best-Fit Package</div>
                <div class="conversion-grid" id="smGrid"></div>
                <div class="alert" id="smMargin"></div>
            </div>

            <div class="chat-section">
                <div class="chat-title">WhatsApp Message for Client</div>
                <div class="chat-box">
                    <div class="chat-content" id="smChatContent">
                        <!-- Generated message will appear here -->
                    </div>
                    <button class="copy-btn" id="smCopyBtn" onclick="copySocialToClipboard()">Copy Text</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const pricing = {
            USD: { rating: 4.00, review: 6.50, symbol: '$' },
            CAD: { rating: 5.77, review: 8.00, symbol: 'CAD $' },
            AUD: { rating: 6.36, review: 10.00, symbol: 'AUD $' },
            GBP: { rating: 3.00, review: 5.00, symbol: '£' }
        };
        
        function calculateConversion() {
            const currency = document.getElementById('currency').value;
            const originalAmount = parseFloat(document.getElementById('originalAmount').value);
            const originalQuantity = parseInt(document.getElementById('originalQuantity').value);
            const completedQuantity = parseInt(document.getElementById('completedQuantity').value);
            
            if (!originalAmount || !originalQuantity || completedQuantity < 0) {
                alert('Please fill in all fields correctly');
                return;
            }
            
            const remaining = originalQuantity - completedQuantity;
            const rates = pricing[currency];
            
            // Update currency label
            document.getElementById('currencyLabel').textContent = rates.symbol;
            
            if (remaining <= 0) {
                alert('No remaining quantity to convert');
                return;
            }
            
            // Calculate per-unit price from original order
            const originalPerUnit = originalAmount / originalQuantity;
            const remainingValue = remaining * originalPerUnit;
            
            // Calculate conversion options
            const reviewsFromBalance = Math.floor(remainingValue / rates.review);
            const ratingsFromBalance = Math.floor(remainingValue / rates.rating);
            
            // Update display
            document.getElementById('allToReviews').textContent = `${reviewsFromBalance} reviews`;
            document.getElementById('keepRatingOnly').textContent = `${ratingsFromBalance} ratings`;
            
            // Generate WhatsApp message
            generateWhatsAppMessage(currency, originalAmount, originalQuantity, completedQuantity, remaining, remainingValue, reviewsFromBalance, ratingsFromBalance, rates);
            
            // Show alert message
            const alertDiv = document.getElementById('alertMessage');
            alertDiv.innerHTML = `
                <strong>Order Summary:</strong><br>
                • Ordered: ${originalQuantity} ratings for ${rates.symbol}${originalAmount.toFixed(2)}<br>
                • Completed: ${completedQuantity} ratings<br>
                • Remaining: ${remaining} ratings<br>
                • Money left: ${rates.symbol}${remainingValue.toFixed(2)}<br>
                • Rate: ${rates.symbol}${originalPerUnit.toFixed(2)} per rating
            `;
            
            document.getElementById('results').style.display = 'block';
        }
        
        function calculateMix() {
            const currency = document.getElementById('currency').value;
            const originalAmount = parseFloat(document.getElementById('originalAmount').value);
            const originalQuantity = parseInt(document.getElementById('originalQuantity').value);
            const completedQuantity = parseInt(document.getElementById('completedQuantity').value);
            const mixReviews = parseInt(document.getElementById('mixReviews').value) || 0;
            const mixRatings = parseInt(document.getElementById('mixRatings').value) || 0;
            
            const remaining = originalQuantity - completedQuantity;
            const originalPerUnit = originalAmount / originalQuantity;
            const remainingValue = remaining * originalPerUnit;
            
            const rates = pricing[currency];
            const mixCost = (mixReviews * rates.review) + (mixRatings * rates.rating);
            const difference = remainingValue - mixCost;
            
            const mixResultDiv = document.getElementById('mixResult');
            
            if (mixCost <= remainingValue) {
                mixResultDiv.innerHTML = `
                    <div style="color: #34a853; font-weight: 600;">
                        ✓ This works!<br>
                        Cost: ${rates.symbol}${mixCost.toFixed(2)}<br>
                        ${difference > 0 ? `Money left: ${rates.symbol}${difference.toFixed(2)}` : 'Perfect!'}
                    </div>
                `;
            } else {
                mixResultDiv.innerHTML = `
                    <div style="color: #ea4335; font-weight: 600;">
                        ✗ Too expensive by ${rates.symbol}${Math.abs(difference).toFixed(2)}
                    </div>
                `;
            }
        }
        
        function generateWhatsAppMessage(currency, originalAmount, originalQuantity, completedQuantity, remaining, remainingValue, reviewsFromBalance, ratingsFromBalance, rates) {
            const chatContent = document.getElementById('chatContent');
            
            if (!chatContent) {
                console.error('Chat content element not found');
                return;
            }
            
            const message = `Hi! 👋

I've reviewed your current order and here are your options for the remaining balance:

📊 **Current Status:**
• Original order: ${originalQuantity} ratings (${rates.symbol}${originalAmount.toFixed(2)})
• Completed: ${completedQuantity} ratings
• Remaining: ${remaining} ratings
• Remaining value: ${rates.symbol}${remainingValue.toFixed(2)}

💡 **Your Options:**

**Option 1: Switch to Full Reviews**
• Get ${reviewsFromBalance} full reviews with written content
• Rating + written comments from local names
• Same gradual posting schedule

**Option 2: Keep Rating Only**
• Continue with ${ratingsFromBalance} rating-only service
• Just star ratings without comments

**Option 3: Custom Mix**
• Combine both services as needed
• You choose how many of each

Which option would work best for your business? I can process the change immediately! 🚀

📞 WhatsApp: https://wa.me/6285183081655
🌐 Smart Buzzer: https://smart-buzzer.com/google`;

            chatContent.textContent = message;
        }
        
        function copyToClipboard() {
            const chatContent = document.getElementById('chatContent');
            const copyBtn = document.getElementById('copyBtn');
            
            if (!chatContent || !copyBtn) {
                console.error('Required elements not found');
                return;
            }
            
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(chatContent.textContent).then(() => {
                    showCopyFeedback(copyBtn);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopy(chatContent, copyBtn);
                });
            } else {
                fallbackCopy(chatContent, copyBtn);
            }
        }
        
        function fallbackCopy(chatContent, copyBtn) {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = chatContent.textContent;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                showCopyFeedback(copyBtn);
            } catch (err) {
                console.error('Fallback copy failed: ', err);
                alert('Copy failed. Please manually select and copy the text.');
            }
            
            document.body.removeChild(textarea);
        }
        
        function showCopyFeedback(copyBtn) {
            copyBtn.textContent = 'Copied!';
            copyBtn.classList.add('copied');
            
            setTimeout(() => {
                copyBtn.textContent = 'Copy Text';
                copyBtn.classList.remove('copied');
            }, 2000);
        }
        
        // Auto-calculate when inputs change
        document.getElementById('currency').addEventListener('change', function() {
            const currency = this.value;
            const rates = pricing[currency];
            
            // Update currency label
            document.getElementById('currencyLabel').textContent = rates.symbol;
            
            if (document.getElementById('originalAmount').value) {
                calculateConversion();
            }
        });

        /* ============ REVIEWS -> SOCIAL MEDIA ============ */
        const REVIEW_PRICE = 6.50;

        const socialPricing = {
            followers: {
                label: 'Followers',
                unit: 'follower',
                cogs: 0.00072313,
                tiers: [
                    { qty: 1500,  price: 200 },
                    { qty: 10000, price: 500, popular: true },
                    { qty: 20000, price: 900 },
                    { qty: 25000, price: 1000 }
                ]
            },
            likes: {
                label: 'Likes',
                unit: 'like',
                cogs: 0.00033,
                tiers: [
                    { qty: 1000,  price: 160 },
                    { qty: 2500,  price: 200, popular: true },
                    { qty: 5000,  price: 270 },
                    { qty: 10000, price: 370 }
                ]
            }
        };

        function fmtQty(n) {
            return n >= 1000 ? (n / 1000) + 'K' : n;
        }

        function calculateSocial() {
            const reviewsRaw = parseInt(document.getElementById('smReviews').value) || 0;
            let budget = parseFloat(document.getElementById('smBudget').value) || 0;
            if (budget <= 0) budget = reviewEstimate(); // fall back to reviews x rate

            if (budget <= 0) {
                alert('Enter a budget (or a review count to estimate one)');
                return;
            }

            const metric = document.querySelector('input[name="smMetric"]:checked').value;
            const data = socialPricing[metric];
            const tiers = data.tiers;

            // Snap to nearest affordable tier (highest tier with price <= budget)
            let best = null;
            for (const t of tiers) {
                if (t.price <= budget) best = t;
            }
            let shortfall = 0;
            if (!best) {
                best = tiers[0];
                shortfall = tiers[0].price - budget;
            }
            const leftover = budget - best.price;
            const nextTier = tiers[tiers.indexOf(best) + 1] || null;

            const perUnit = best.price / best.qty;
            const cogs    = best.qty * data.cogs;
            const profit  = best.price - cogs;
            const margin  = (profit / best.price) * 100;

            // Best-fit package card + budget note card
            let note;
            if (shortfall > 0) {
                note = `Budget $${budget.toFixed(2)} is $${shortfall.toFixed(2)} short of the smallest ${data.label.toLowerCase()} package. Showing the entry tier.`;
            } else if (leftover > 0 && nextTier) {
                note = `Budget $${budget.toFixed(2)} fits ${fmtQty(best.qty)} ${data.label}. Add $${(nextTier.price - budget).toFixed(2)} to reach ${fmtQty(nextTier.qty)}.`;
            } else if (leftover > 0) {
                note = `Budget $${budget.toFixed(2)} fits ${fmtQty(best.qty)} ${data.label}. $${leftover.toFixed(2)} left over.`;
            } else {
                note = `Budget $${budget.toFixed(2)} is an exact match for ${fmtQty(best.qty)} ${data.label}.`;
            }

            document.getElementById('smGrid').innerHTML = `
                <div class="conversion-option">
                    ${best.popular ? '<div class="sm-pop">POPULAR</div>' : ''}
                    <h4>${fmtQty(best.qty)} ${data.label}</h4>
                    <div class="remaining-display">$${best.price}</div>
                    <p>$${perUnit.toFixed(3)} per ${data.unit}</p>
                </div>
                <div class="conversion-option">
                    <h4>Budget Check</h4>
                    <div class="remaining-display">$${budget.toFixed(2)}</div>
                    <p>${note}</p>
                </div>
            `;

            const basis = reviewsRaw > 0 ? ` &middot; effective $${(budget / reviewsRaw).toFixed(2)}/review on ${reviewsRaw} reviews` : '';
            document.getElementById('smMargin').innerHTML =
                `<strong>Operator view:</strong> ${fmtQty(best.qty)} ${data.label} &middot; sell $${best.price} &middot; cost $${cogs.toFixed(2)} &middot; profit $${profit.toFixed(2)} (${margin.toFixed(1)}% margin)${basis}`;

            generateSocialMessage(best, data, perUnit);
            document.getElementById('smResults').style.display = 'block';
        }

        function generateSocialMessage(best, data, perUnit) {
            const message = `Hi! 👋

Here's what your remaining balance can convert into:

📦 Package: ${fmtQty(best.qty)} ${data.label}
💰 Value: $${best.price}
✨ Rate: $${perUnit.toFixed(3)} per ${data.unit}

Same trusted delivery — gradual and natural posting.
Want me to switch your order to this?`;

            document.getElementById('smChatContent').textContent = message;
        }

        function copySocialToClipboard() {
            const content = document.getElementById('smChatContent');
            const btn = document.getElementById('smCopyBtn');
            if (!content || !btn) return;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(content.textContent)
                    .then(() => showCopyFeedback(btn))
                    .catch(() => fallbackCopy(content, btn));
            } else {
                fallbackCopy(content, btn);
            }
        }

        /* Estimate-from-reviews helper — Budget stays the source of truth */
        function reviewEstimate() {
            const r = parseInt(document.getElementById('smReviews').value) || 0;
            const rate = parseFloat(document.getElementById('smRate').value) || 0;
            return r * rate;
        }

        function updateEstimateUI() {
            const est = reviewEstimate();
            const btn = document.getElementById('smApplyEst');
            btn.textContent = est > 0 ? `Use $${est.toFixed(2)}` : 'Use estimate';
            // Auto-fill Budget only when it's empty — never overwrite a manual budget
            const budget = document.getElementById('smBudget');
            if (budget.value.trim() === '' && est > 0) {
                budget.value = est.toFixed(2);
            }
        }

        function applyReviewEstimate() {
            const est = reviewEstimate();
            if (est > 0) document.getElementById('smBudget').value = est.toFixed(2);
        }

        document.getElementById('smReviews').addEventListener('input', updateEstimateUI);
        document.getElementById('smRate').addEventListener('input', updateEstimateUI);
    </script>
</body>
</html>