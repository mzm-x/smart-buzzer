<?php
$__wa=$_SERVER['DOCUMENT_ROOT'].'/wa-config.php'; if(is_readable($__wa)){include $__wa;} if(empty($SB_WA_NUMBER)){$SB_WA_NUMBER='628979133204';}
/**
 * ============================================================================
 * File: /submit/submit.php
 * Smart Buzzer Client Onboarding Form - v4.2 (Searchable Country/State/Industry)
 * 
 * Features:
 * - Service Type Selector: Popup modal for Reviews/Social Media
 * - 6-step wizard for Reviews with dynamic business cards
 * - 4-step wizard for Social Media with calculator preview
 * - TNC with sequential reveal + separate TNC for Social Media
 * - Support for Google, Yelp, Facebook, Trustpilot, Other (custom)
 * - Social Media: Instagram, TikTok, Twitter, YouTube, Other
 * - Stay Rate Calculator (89.44% guarantee)
 * - Multi-business split (min 20 reviews per business)
 * - Auto-save to localStorage with step resume
 * 
 * v4.2 Changes:
 * - Added searchable Country dropdown with 25+ countries
 * - Added dynamic State/Province/Region dropdown (auto-populates based on country)
 * - Added searchable Business Industry dropdown (120+ industries)
 * - All dropdowns support type-to-search with highlight matching
 * - Free text input accepted if no match found
 * - Keyboard navigation (Arrow keys + Enter + Escape)
 * - New fields included in preview & form submission
 * 
 * v4.0 Changes:
 * - Added Service Type Selector (Reviews Booster / Social Media)
 * - Added Social Media order flow with separate TNC
 * - Added Stay Rate Calculator for guaranteed delivery
 * - Simplified Social Media form (Platform, Service, Link, Quantity)
 * 
 * v3.5 Changes:
 * - Enhanced localStorage: saves current step for resume
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: January 2025
 * ============================================================================
 */

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

$tncFile = __DIR__ . '/data/tnc.json';
$tncPoints = [];
if (file_exists($tncFile)) {
    $tncData = json_decode(file_get_contents($tncFile), true);
    $tncPoints = $tncData['points'] ?? [];
}

// Dedupe TNC points by title — defensive against corrupted JSON
// (root cause of "9/18 agreed" bug: production tnc.json had duplicate entries
// and the wizard wouldn't unlock because DOM rendered 18 checkboxes instead of 9)
if (!empty($tncPoints) && is_array($tncPoints)) {
    $seenTitles = [];
    $dedupedPoints = [];
    foreach ($tncPoints as $point) {
        $title = isset($point['title']) ? strtolower(trim($point['title'])) : '';
        if ($title === '' || isset($seenTitles[$title])) continue;
        $seenTitles[$title] = true;
        $dedupedPoints[] = $point;
    }
    $tncPoints = $dedupedPoints;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Order Form</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --green: #269B5A;
            --blue: #09314F;
        }
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Progress bar */
        .step-dot { 
            width: 40px; height: 40px; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 14px;
            transition: all 0.3s ease;
        }
        .step-dot.active { background: var(--green); color: white; }
        .step-dot.completed { background: var(--green); color: white; }
        .step-dot.inactive { background: #E5E5E5; color: #9CA3AF; }
        .step-line { height: 3px; flex: 1; background: #E5E5E5; transition: all 0.3s ease; }
        .step-line.active { background: var(--green); }
        
        /* Form inputs */
        .sb-input {
            width: 100%; padding: 14px 16px;
            background: #F9FAFB; border: 2px solid #86EFAC;
            border-radius: 12px; font-size: 16px;
            transition: all 0.2s ease;
        }
        .sb-input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(38, 155, 90, 0.1); }
        .sb-input::placeholder { color: #9CA3AF; }
        .sb-input.error { border-color: #EF4444; }
        
        .sb-label { display: block; font-weight: 500; color: var(--blue); margin-bottom: 8px; font-size: 15px; }
        .sb-label span { color: #EF4444; }
        
        .sb-select {
            width: 100%; padding: 14px 16px;
            background: #F9FAFB; border: 2px solid #86EFAC;
            border-radius: 12px; font-size: 16px;
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
        }
        .sb-select:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(38, 155, 90, 0.1); }
        
        /* Searchable Dropdown */
        .sb-search-dropdown { position: relative; }
        .sb-search-dropdown .sb-input { padding-right: 40px; }
        .sb-search-dropdown-icon {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            pointer-events: none; color: #9CA3AF;
        }
        .sb-dropdown-list {
            position: absolute; top: 100%; left: 0; right: 0;
            background: white; border: 1px solid #E5E7EB;
            border-radius: 12px; margin-top: 4px;
            max-height: 220px; overflow-y: auto;
            z-index: 50; display: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        }
        .sb-dropdown-list.open { display: block; }
        .sb-dropdown-item {
            padding: 12px 16px; cursor: pointer;
            font-size: 15px; color: #374151;
            transition: background 0.15s;
        }
        .sb-dropdown-item:first-child { border-radius: 12px 12px 0 0; }
        .sb-dropdown-item:last-child { border-radius: 0 0 12px 12px; }
        .sb-dropdown-item:hover, .sb-dropdown-item.highlighted { background: #F0FDF4; color: var(--green); }
        .sb-dropdown-item mark { background: #FEF08A; color: inherit; border-radius: 2px; padding: 0 1px; }
        .sb-dropdown-empty { padding: 14px 16px; color: #9CA3AF; font-size: 14px; text-align: center; }
        
        .sb-textarea {
            width: 100%; padding: 14px 16px;
            background: #F9FAFB; border: 2px solid #86EFAC;
            border-radius: 12px; font-size: 16px;
            resize: vertical; min-height: 100px;
        }
        .sb-textarea:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(38, 155, 90, 0.1); }
        
        /* Radio buttons */
        .sb-radio-group { display: flex; flex-wrap: wrap; gap: 12px; }
        .sb-radio { display: none; }
        .sb-radio-label {
            display: flex; align-items: center; justify-content: center;
            padding: 14px 24px; background: #F9FAFB;
            border: 2px solid #E5E7EB; border-radius: 12px;
            cursor: pointer; font-weight: 500; font-size: 15px;
            transition: all 0.2s ease; flex: 1; min-width: 120px;
        }
        .sb-radio-label:hover { border-color: var(--green); }
        .sb-radio:checked + .sb-radio-label { background: var(--green); color: white; border-color: var(--green); }
        
        /* Buttons */
        .sb-btn-primary {
            padding: 16px 32px; background: var(--green);
            color: white; border: none; border-radius: 12px;
            font-weight: 600; font-size: 16px; cursor: pointer;
            transition: all 0.2s ease; display: inline-flex;
            align-items: center; justify-content: center;
        }
        .sb-btn-primary:hover { background: #1E8449; }
        .sb-btn-primary:disabled { background: #9CA3AF; cursor: not-allowed; }
        
        .sb-btn-secondary {
            padding: 16px 32px; background: white;
            color: var(--blue); border: 2px solid #E5E7EB;
            border-radius: 12px; font-weight: 600; font-size: 16px;
            cursor: pointer; transition: all 0.2s ease;
        }
        .sb-btn-secondary:hover { border-color: var(--blue); }
        
        .sb-btn-danger {
            padding: 16px 32px; background: #FEE2E2;
            color: #DC2626; border: 2px solid #FECACA;
            border-radius: 12px; font-weight: 600; font-size: 16px;
            cursor: pointer; transition: all 0.2s ease;
            text-decoration: none;
        }
        .sb-btn-danger:hover { background: #FECACA; }
        
        /* Checkbox */
        .sb-checkbox { width: 20px; height: 20px; accent-color: var(--green); cursor: pointer; }
        
        /* Steps container */
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* TNC Checklist */
        .tnc-item { transition: all 0.3s ease; }
        .tnc-item.checked { background: #F0FDF4; border-color: #86EFAC; }
        .tnc-checkbox:checked { background-color: var(--green); border-color: var(--green); }
        
        /* Business Card */
        .business-card {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .business-card:hover { border-color: #D1D5DB; }
        .business-card-header {
            background: #F9FAFB;
            padding: 16px 20px;
            border-bottom: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .business-card-body { padding: 20px; }
        .business-card-collapsed .business-card-body { display: none; }
        
        /* Instruction image */
        .instruction-img {
            border: 1px solid #E5E7EB; border-radius: 12px;
            cursor: pointer; transition: transform 0.2s;
        }
        .instruction-img:hover { transform: scale(1.02); }
        
        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            display: none; align-items: center; justify-content: center;
            z-index: 1000; padding: 20px;
        }
        .modal-overlay.show { display: flex; }
        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* Image modal */
        .img-modal {
            position: fixed; inset: 0; background: rgba(0,0,0,0.9);
            display: none; align-items: center; justify-content: center;
            z-index: 1000; padding: 20px;
        }
        .img-modal.show { display: flex; }
        .img-modal img { max-width: 100%; max-height: 90vh; border-radius: 8px; }
        
        /* Loading */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 1s linear infinite; }
        
        /* Toast notification */
        .sb-toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1F2937;
            color: white;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .sb-toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        .sb-toast-success { background: #059669; }
        .sb-toast-info { background: #2563EB; }
        .sb-toast-warning { background: #D97706; }
        .sb-toast-error { background: #DC2626; }
        
        /* Service Type Selector */
        .service-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 640px) {
            .service-selector { grid-template-columns: 1fr; }
        }
        .service-card {
            border: 2px solid #E5E7EB;
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: white;
        }
        .service-card:hover {
            border-color: #9CA3AF;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .service-card.selected {
            border-color: var(--green);
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            box-shadow: 0 0 0 3px rgba(38, 155, 90, 0.1);
        }
        .service-card-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .service-card-title {
            font-size: 18px;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 8px;
        }
        .service-card-desc {
            font-size: 14px;
            color: #6B7280;
        }
        
        /* Social Media Calculator */
        .sm-calculator {
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border: 1px solid #C7D2FE;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
        }
        .sm-calc-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .sm-calc-row:last-child { border-bottom: none; }
        .sm-calc-label { color: #4F46E5; font-weight: 500; }
        .sm-calc-value { font-weight: 700; color: #1F2937; }
        .sm-calc-highlight {
            background: #4F46E5;
            color: white;
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 700;
        }
        
        /* Social Media Flow Hidden by Default */
        .sm-flow { display: none; }
        .sm-flow.active { display: block; }
        .reviews-flow.hidden { display: none; }
        
        /* Service Selector Popup Modal */
        .service-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .service-popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .service-popup {
            background: white;
            border-radius: 24px;
            padding: 32px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        .service-popup-overlay.active .service-popup {
            transform: scale(1);
        }
        .service-popup-title {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            text-align: center;
            margin-bottom: 8px;
        }
        .service-popup-subtitle {
            font-size: 14px;
            color: #6B7280;
            text-align: center;
            margin-bottom: 24px;
        }
        
        /* No Refill Notice */
        .no-refill-notice {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 16px;
            font-size: 13px;
            color: #92400E;
        }
        .no-refill-notice strong {
            color: #B45309;
        }
        
        /* Survey CTA pulse (legacy, kept for compatibility) */
        @keyframes pulse-border {
            0%, 100% { border-color: #BBF7D0; box-shadow: 0 0 0 0 rgba(46,125,50,0.15); }
            50% { border-color: #2E7D32; box-shadow: 0 0 0 6px rgba(46,125,50,0); }
        }

        /* Bonus Audit CTA — Loyal Client Exclusive */
        @keyframes audit-gold-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.55), 0 8px 24px rgba(245, 158, 11, 0.25); }
            50%      { box-shadow: 0 0 0 14px rgba(245, 158, 11, 0), 0 8px 24px rgba(245, 158, 11, 0.35); }
        }
        @keyframes audit-card-glow {
            0%, 100% { border-color: #F59E0B; }
            50%      { border-color: #D97706; }
        }
        .audit-bonus-card {
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 50%, #FDE68A 100%);
            border: 2px solid #F59E0B;
            position: relative;
            overflow: hidden;
            animation: audit-card-glow 3s ease-in-out infinite;
        }
        .audit-bonus-card::before {
            content: '';
            position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.4) 50%, transparent 70%);
            transform: rotate(25deg);
            animation: audit-shimmer 5s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes audit-shimmer {
            0%, 100% { transform: translateX(-100%) rotate(25deg); }
            50%      { transform: translateX(100%) rotate(25deg); }
        }
        .audit-bonus-pill {
            display: inline-block;
            background: #92400E;
            color: #FEF3C7;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            padding: 5px 12px;
            border-radius: 999px;
            text-transform: uppercase;
        }
        .audit-bonus-title {
            font-size: 24px;
            font-weight: 800;
            color: #78350F;
            margin: 4px 0 6px 0;
            text-align: center;
            line-height: 1.2;
        }
        .audit-bonus-worth {
            font-size: 15px;
            color: #92400E;
            font-weight: 600;
            text-align: center;
            margin-bottom: 14px;
        }
        .audit-strike {
            text-decoration: line-through;
            color: #A16207;
            opacity: 0.7;
            font-weight: 500;
        }
        .audit-bonus-list {
            list-style: none;
            padding: 0; margin: 0 0 12px 0;
            text-align: left;
            background: rgba(255,255,255,0.55);
            border-radius: 12px;
            padding: 12px 14px;
        }
        .audit-bonus-list li {
            font-size: 14px;
            color: #78350F;
            padding: 4px 0;
            display: flex;
            align-items: flex-start;
        }
        .audit-check {
            color: #16A34A;
            font-weight: 800;
            margin-right: 8px;
            flex-shrink: 0;
        }
        .audit-bonus-trust {
            font-size: 12px;
            color: #92400E;
            text-align: center;
            margin-bottom: 14px;
            font-weight: 500;
        }
        .audit-bonus-cta {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            font-size: 16px;
            font-weight: 700;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: transform 0.15s ease;
            animation: audit-gold-pulse 2s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }
        .audit-bonus-cta:hover { transform: translateY(-2px); }
        .audit-bonus-cta:active { transform: translateY(0); }

        /* Audit NDA Modal */
        .audit-modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
            backdrop-filter: blur(4px);
        }
        .audit-modal-overlay.active { display: flex; }
        .audit-modal-card {
            background: white;
            border-radius: 20px;
            max-width: 520px;
            width: 100%;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: audit-modal-in 0.25s ease-out;
        }
        @keyframes audit-modal-in {
            from { opacity: 0; transform: scale(0.94) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .audit-modal-header {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            padding: 20px 24px;
            border-radius: 20px 20px 0 0;
            position: relative;
        }
        .audit-modal-close {
            position: absolute;
            top: 14px; right: 14px;
            background: rgba(255,255,255,0.7);
            border: none;
            width: 32px; height: 32px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            color: #78350F;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }
        .audit-modal-close:hover { background: white; }
        .audit-modal-body { padding: 20px 24px; }
        .audit-section-box {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }
        .audit-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #92400E;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .audit-section-list { list-style: none; padding: 0; margin: 0; }
        .audit-section-list li {
            font-size: 13.5px;
            color: #374151;
            padding: 3px 0;
            display: flex;
            align-items: flex-start;
        }
        .audit-section-list .dot {
            color: #F59E0B;
            margin-right: 8px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .audit-nda-box {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
            max-height: 180px;
            overflow-y: auto;
        }
        .audit-nda-box ol {
            margin: 0; padding-left: 20px;
            font-size: 12.5px;
            color: #4B5563;
            line-height: 1.55;
        }
        .audit-nda-box ol li { padding: 2px 0; }
        .audit-agree-row {
            display: flex;
            align-items: flex-start;
            background: #ECFDF5;
            border: 2px solid #A7F3D0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .audit-agree-row:has(input:checked) {
            background: #D1FAE5;
            border-color: #34D399;
        }
        .audit-agree-row input {
            width: 20px; height: 20px;
            margin-right: 12px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #059669;
            flex-shrink: 0;
        }
        .audit-agree-text {
            font-size: 13.5px;
            color: #065F46;
            font-weight: 500;
        }
        .audit-final-cta {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            font-size: 16px;
            font-weight: 700;
            padding: 16px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.15s;
            opacity: 0.5;
        }
        .audit-final-cta.enabled {
            opacity: 1;
            animation: audit-gold-pulse 2s ease-in-out infinite;
        }
        .audit-final-cta:not(.enabled) { cursor: not-allowed; }
        .audit-final-cta.enabled:hover { transform: translateY(-2px); }
        .audit-maybe-later {
            display: block;
            text-align: center;
            color: #9CA3AF;
            font-size: 12px;
            margin: 8px 0 16px 0;
            background: none;
            border: none;
            width: 100%;
            cursor: pointer;
            text-decoration: underline;
        }
        .audit-maybe-later:hover { color: #6B7280; }

        /* === 4-STEP WIZARD STYLES === */

        /* Step indicator (dots + line + labels) */
        .audit-stepper {
            padding: 16px 24px 4px 24px;
            background: linear-gradient(180deg, #FFFBEB 0%, #FFFFFF 100%);
            border-bottom: 1px solid #FEF3C7;
        }
        .audit-stepper-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 320px;
            margin: 0 auto;
        }
        .audit-step-dot {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: #FDE68A;
            color: #92400E;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
            border: 2px solid transparent;
        }
        .audit-step-dot.active {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.4);
        }
        .audit-step-dot.done {
            background: #16A34A;
            color: white;
        }
        .audit-step-dot.done::before {
            content: '✓';
            font-size: 15px;
            font-weight: 800;
        }
        .audit-step-dot.done {
            font-size: 0;
        }
        .audit-step-line {
            flex: 1;
            height: 3px;
            background: #FDE68A;
            margin: 0 4px;
            border-radius: 2px;
            transition: background 0.3s ease;
        }
        .audit-step-line.done {
            background: #16A34A;
        }
        .audit-stepper-labels {
            display: flex;
            justify-content: space-between;
            max-width: 320px;
            margin: 6px auto 0 auto;
        }
        .audit-step-label {
            font-size: 11px;
            font-weight: 600;
            color: #A16207;
            opacity: 0.55;
            text-align: center;
            width: 30px;
            transition: opacity 0.2s ease, color 0.2s ease;
        }
        .audit-step-label.active {
            opacity: 1;
            color: #78350F;
        }
        .audit-step-label.done {
            opacity: 1;
            color: #16A34A;
        }
        .audit-step-counter {
            text-align: center;
            font-size: 11px;
            color: #A16207;
            font-weight: 600;
            letter-spacing: 0.04em;
            margin: 10px 0 0 0;
            text-transform: uppercase;
        }

        /* Step content — only one visible at a time */
        .audit-step-content {
            display: none;
            animation: audit-step-fade 0.28s ease-out;
        }
        .audit-step-content.active {
            display: block;
        }
        @keyframes audit-step-fade {
            from { opacity: 0; transform: translateX(12px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Per-step headline */
        .audit-step-headline {
            text-align: center;
            margin-bottom: 16px;
        }
        .audit-step-emoji {
            font-size: 38px;
            line-height: 1;
            margin-bottom: 6px;
        }
        .audit-step-headline h3 {
            font-size: 20px;
            font-weight: 800;
            color: #78350F;
            margin: 0 0 4px 0;
        }
        .audit-step-headline p {
            font-size: 13px;
            color: #92400E;
            margin: 0;
        }

        /* Numbered NDA list (step 3) */
        .audit-nda-numbered {
            list-style: none;
            counter-reset: nda-counter;
            padding: 0;
            margin: 0;
        }
        .audit-nda-numbered li {
            counter-increment: nda-counter;
            position: relative;
            padding: 10px 12px 10px 44px;
            margin-bottom: 8px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 10px;
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
        }
        .audit-nda-numbered li::before {
            content: counter(nda-counter);
            position: absolute;
            left: 10px; top: 10px;
            width: 24px; height: 24px;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .audit-nda-numbered li strong {
            color: #78350F;
        }

        /* Footer — Next/Back navigation */
        .audit-modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 24px;
            border-top: 1px solid #FEF3C7;
            background: #FFFBEB;
            gap: 12px;
        }
        .audit-nav-back, .audit-nav-next {
            font-size: 14px;
            font-weight: 700;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            min-width: 100px;
        }
        .audit-nav-back {
            background: white;
            color: #78350F;
            border: 1.5px solid #FDE68A;
        }
        .audit-nav-back:hover {
            background: #FEF3C7;
            border-color: #F59E0B;
        }
        .audit-nav-next {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        }
        .audit-nav-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.45);
        }
        .audit-nav-next:disabled,
        .audit-nav-next.hidden {
            display: none;
        }
        .audit-modal-footer.is-last .audit-nav-next {
            display: none;
        }
        .audit-modal-footer.is-first .audit-nav-back {
            visibility: hidden;
        }

        /* Success card */
        .success-card {
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border: 1px solid #BBF7D0; border-radius: 20px;
        }
        
        /* Helper text */
        .helper-text { font-size: 13px; color: #6B7280; margin-top: 6px; }
        
        /* Error text */
        .error-text { font-size: 13px; color: #EF4444; margin-top: 6px; display: none; }
        .error-text.show { display: block; }
        
        /* Checkmark animation */
        .checkmark-circle { width: 80px; height: 80px; margin: 0 auto; }
        .checkmark {
            width: 80px; height: 80px; border-radius: 50%; display: block;
            stroke-width: 2; stroke: #269B5A; stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #269B5A;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark-circle-bg {
            stroke-dasharray: 166; stroke-dashoffset: 166;
            stroke-width: 2; stroke-miterlimit: 10;
            stroke: #269B5A; fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48; stroke-dashoffset: 48;
            stroke: #269B5A; stroke-width: 3;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 40px transparent; } }
        
        /* Confirm Modal */
        .confirm-item { transition: all 0.2s ease; }
        .confirm-item:has(.confirm-checkbox:checked) { background: #F0FDF4; border-color: #86EFAC; }
        
        /* Review allocation */
        .review-allocation {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
        }
        .allocation-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .allocation-card.error { border-color: #EF4444; background: #FEF2F2; }
        .allocation-card.valid { border-color: #86EFAC; background: #F0FDF4; }
    </style>
</head>
<body class="bg-white min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-3xl mx-auto px-6 py-4">
            <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" class="h-10 mx-auto">
        </div>
        
        <!-- Progress Bar -->
        <div id="progressBarContainer" class="max-w-3xl mx-auto px-6 pb-6">
            <div class="flex items-center">
                <div class="step-dot active" data-step="1">1</div>
                <div class="step-line" data-line="1"></div>
                <div class="step-dot inactive" data-step="2">2</div>
                <div class="step-line" data-line="2"></div>
                <div class="step-dot inactive" data-step="3">3</div>
                <div class="step-line" data-line="3"></div>
                <div class="step-dot inactive" data-step="4">4</div>
                <div class="step-line" data-line="4"></div>
                <div class="step-dot inactive" data-step="5">5</div>
                <div class="step-line" data-line="5"></div>
                <div class="step-dot inactive" data-step="6">6</div>
            </div>
            <div class="flex justify-between mt-2 text-xs font-medium text-gray-500">
                <span class="text-center" style="width: 45px;">Start</span>
                <span class="text-center" style="width: 45px;">Terms</span>
                <span class="text-center" style="width: 45px;">Info</span>
                <span class="text-center" style="width: 45px;">Details</span>
                <span class="text-center" style="width: 45px;">Preview</span>
                <span class="text-center" style="width: 45px;">Done</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-6 py-8">
        <form id="orderForm">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" id="referrer_url" name="referrer_url" value="">
            <input type="hidden" id="tracking_source" name="tracking_source" value="">
            <script>
                (function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const ref = urlParams.get('ref') || urlParams.get('utm_source') || urlParams.get('source') || '';
                    const campaign = urlParams.get('utm_campaign') || urlParams.get('campaign') || '';
                    let trackingSource = '';
                    if (ref) {
                        trackingSource = ref + (campaign ? ' (' + campaign + ')' : '');
                    }
                    document.getElementById('tracking_source').value = trackingSource;
                    document.getElementById('referrer_url').value = document.referrer || window.location.href;
                })();
            </script>
            
            <!-- ============================================ -->
            <!-- STEP 1: Welcome & Flow Overview -->
            <!-- ============================================ -->
            <div class="step-content active" data-step="1">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Welcome to Smart Buzzer</h1>
                    <p class="text-gray-600 text-lg max-w-lg mx-auto">
                        Complete this quick onboarding to get your reviews posted <span class="font-semibold text-green-600">smoothly & on-time</span>
                    </p>
                </div>
                
                <!-- Service Type Display & Change Button -->
                <div class="mb-8">
                    <div class="relative rounded-2xl p-6 border-2 shadow-lg transition-all" id="selectedServiceDisplay" style="background: linear-gradient(135deg, #FEF9C3 0%, #FEF08A 100%); border-color: #EAB308;">
                        <!-- Selected Badge -->
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="px-4 py-1 text-xs font-bold uppercase tracking-wider rounded-full shadow-md" style="background: #EAB308; color: white;">
                                Selected Service
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-center pt-2">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mr-4 shadow-md" id="selectedServiceIcon" style="background: white;">
                                <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-2xl font-bold text-gray-900" id="selectedServiceName">Reviews Booster</p>
                                <p class="text-sm text-gray-600 mt-1" id="selectedServiceDesc">Google, Yelp, Facebook reviews</p>
                            </div>
                        </div>
                        
                        <div class="text-center mt-5">
                            <button type="button" onclick="openServicePopup()" class="inline-flex items-center px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl border-2 border-gray-300 hover:border-gray-400 shadow-sm transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Change Service
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="serviceType" name="serviceType" value="reviews">
                </div>
                
                <!-- Reviews Flow Timeline (shown by default) -->
                <div id="reviewsFlowContent" class="reviews-flow">
                <!-- Flow Timeline -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-200">
                    <h2 class="text-center font-semibold text-gray-900 mb-6 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        How It Works
                    </h2>
                    
                    <!-- Desktop Flow -->
                    <div class="hidden md:flex items-center justify-between mb-4">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-700 mt-2">Order</p>
                            <p class="text-xs text-green-600 font-medium">Done</p>
                        </div>
                        <div class="flex-1 h-1 bg-green-300 mx-2 rounded"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center shadow-md ring-4 ring-blue-200">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <p class="text-xs font-semibold text-blue-700 mt-2">Onboarding</p>
                            <p class="text-xs text-blue-600 font-medium">You are Here</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2 rounded"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-gray-200 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <p class="text-xs font-medium text-gray-500 mt-2">WhatsApp</p>
                            <p class="text-xs text-gray-400">Connect</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2 rounded"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-gray-200 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs font-medium text-gray-500 mt-2">Preview</p>
                            <p class="text-xs text-gray-400">Approve</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2 rounded"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-gray-200 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </div>
                            <p class="text-xs font-medium text-gray-500 mt-2">Reviews</p>
                            <p class="text-xs text-gray-400">Go Live</p>
                        </div>
                    </div>
                    
                    <!-- Mobile Flow -->
                    <div class="md:hidden space-y-3">
                        <div class="flex items-center p-3 bg-green-50 rounded-xl border border-green-200">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-green-800">Order Complete</p>
                                <p class="text-xs text-green-600">Payment confirmed</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-blue-50 rounded-xl border-2 border-blue-400">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-blue-800">Onboarding</p>
                                <p class="text-xs text-blue-600">Fill business details</p>
                            </div>
                            <span class="text-blue-600 text-xs font-bold bg-blue-100 px-2 py-1 rounded-full">NOW</span>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-600">WhatsApp Connect</p>
                                <p class="text-xs text-gray-400">Chat with Account Manager</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-600">Content Preview</p>
                                <p class="text-xs text-gray-400">Review and approve content</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-600">Reviews Go Live</p>
                                <p class="text-xs text-gray-400">1-5 submitted daily, ~50% stick</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Saved Data Banner (shown via JS if data exists) -->
                <div id="savedDataBanner" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <div>
                                <p class="text-blue-900 font-medium text-sm">You have saved progress</p>
                                <p class="text-blue-600 text-xs">Your previous data has been restored</p>
                            </div>
                        </div>
                        <button type="button" onclick="clearSavedData()" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                            Clear & Start Fresh
                        </button>
                    </div>
                </div>
                
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-amber-800 font-medium">This onboarding takes around <span class="font-bold">10-15 minutes</span></p>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="button" class="sb-btn-primary text-lg px-8 py-4" onclick="showReadyModal()">
                        Start Onboarding
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                    <p class="text-sm text-gray-400 mt-4">By continuing, you are one step closer to boosting your online reputation</p>
                </div>
                </div><!-- End reviewsFlowContent -->
                
                <!-- Social Media Flow Content (hidden by default) -->
                <div id="socialMediaFlowContent" class="sm-flow">
                    <div class="bg-purple-50 rounded-2xl p-6 mb-8 border border-purple-200">
                        <h2 class="text-center font-semibold text-purple-900 mb-4 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Social Media Growth Service
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-white rounded-xl p-3 text-center border border-purple-100">
                                <div class="text-2xl mb-1">📸</div>
                                <div class="text-xs font-medium text-gray-700">Instagram</div>
                            </div>
                            <div class="bg-white rounded-xl p-3 text-center border border-purple-100">
                                <div class="text-2xl mb-1">🎵</div>
                                <div class="text-xs font-medium text-gray-700">TikTok</div>
                            </div>
                            <div class="bg-white rounded-xl p-3 text-center border border-purple-100">
                                <div class="text-2xl mb-1">🐦</div>
                                <div class="text-xs font-medium text-gray-700">Twitter</div>
                            </div>
                            <div class="bg-white rounded-xl p-3 text-center border border-purple-100">
                                <div class="text-2xl mb-1">▶️</div>
                                <div class="text-xs font-medium text-gray-700">YouTube</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-8">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <div>
                                <p class="text-indigo-900 font-semibold">Guaranteed Delivery</p>
                                <p class="text-indigo-700 text-sm mt-1">We deliver extra quantity upfront to guarantee your target. No refunds needed because we over-deliver!</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="sb-btn-primary text-lg px-8 py-4" onclick="startSocialMediaFlow()" style="background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);">
                            Start Social Media Onboarding
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                        <p class="text-sm text-gray-400 mt-4">Quick 3-step process to boost your social media presence</p>
                    </div>
                </div><!-- End socialMediaFlowContent -->
            </div>
            
            <!-- ============================================ -->
            <!-- STEP 2: Terms & Conditions -->
            <!-- ============================================ -->
            <div class="step-content" data-step="2">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: var(--blue);">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Terms & Conditions
                    </h1>
                    <p class="text-gray-500 mt-2">Please read and agree to each point before continuing</p>
                </div>
                <img 
  src="https://smart-buzzer.com/photos/hr.webp" 
  alt="Google Maps Flagged Example"
  width="350"
  height="350"
  style="display:block; margin:0 auto; object-fit:cover;"
>

                <?php if (empty($tncPoints)): ?>
                <div class="bg-gray-50 rounded-xl p-6 mb-6 text-center text-gray-500">
                    <p>No terms and conditions configured yet.</p>
                </div>
                <?php else: ?>
                <div class="space-y-4 mb-8" id="tncChecklistContainer">
                    <?php foreach ($tncPoints as $index => $point): ?>
                    <div class="tnc-item bg-white border border-gray-200 rounded-xl p-4 transition-all hover:border-gray-300 <?php echo $index > 0 ? 'tnc-hidden' : ''; ?>" data-index="<?php echo $index; ?>" style="<?php echo $index > 0 ? 'opacity: 0; transform: translateY(-10px); display: none;' : ''; ?>">
                        <label class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox" class="tnc-checkbox w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500" data-index="<?php echo $index; ?>">
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($point['title']); ?></p>
                                <?php if (!empty($point['subtitle'])): ?>
                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($point['subtitle']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex-shrink-0 ml-3 tnc-check-icon hidden">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="tncHint" class="text-center mb-4">
                    <p class="text-sm text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Check the box above to reveal the next point
                    </p>
                </div>
                <?php endif; ?>
                
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Progress:</span>
                        <span id="tncProgress" class="text-sm font-semibold text-gray-900">0 / <?php echo count($tncPoints); ?> agreed</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div id="tncProgressBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%; background: var(--green);"></div>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToStep(1)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back
                    </button>
                    <button type="button" id="btnStep2Next" class="sb-btn-primary" disabled onclick="showTncConfirmModal()">
                        I Agree & Continue
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <p id="tncWarning" class="text-sm text-amber-600 mt-3 text-center">Please agree to all <?php echo count($tncPoints); ?> points above to continue</p>
            </div>
            
            <!-- ============================================ -->
            <!-- STEP 3: Your Information -->
            <!-- ============================================ -->
            <div class="step-content" data-step="3">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: var(--blue);">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Your Information
                    </h1>
                    <p class="text-gray-500 mt-2">Tell us about yourself and your order</p>
                </div>
                
                <div class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label class="sb-label">Email Address <span>*</span></label>
                        <p class="text-xs text-gray-500 mb-2">We will send order updates and completion notifications to this email.</p>
                        <input type="email" id="email" name="email" class="sb-input" placeholder="your@email.com" required>
                        <p class="error-text" id="emailError">Please enter a valid email address</p>
                    </div>

                    <!-- Payment Email -->
                    <div>
                        <label class="sb-label">Payment Email</label>
                        <p class="text-xs text-gray-500 mb-2">Email you used to make payment via Fanbasis / Tazapay. <strong>If it's the same as your email above, leave this blank.</strong></p>
                        <input type="email" id="paymentEmail" name="paymentEmail" class="sb-input" placeholder="Only fill if different from your email above">
                    </div>

                    <!-- Telegram -->
                    <div>
                        <label class="sb-label">Telegram Username / Number</label>
                        <p class="text-xs text-gray-500 mb-2">Optional backup contact. We use Telegram as a secondary channel in case our WhatsApp is temporarily unavailable.</p>
                        <input type="text" id="telegram" name="telegram" class="sb-input" placeholder="@username or +1234567890">
                        <p class="helper-text">Enter your Telegram @username or phone number</p>
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="sb-label">WhatsApp Number <span>*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Include country code. This is our primary communication channel for order updates.</p>
                        <input type="tel" id="whatsapp" name="whatsapp" class="sb-input" placeholder="+1234567890" required>
                        <p class="helper-text">Example: +1 for USA, +62 for Indonesia, +44 for UK</p>
                    </div>
                    
                    <!-- Business Address -->
                    <div>
                        <label class="sb-label">Business Address</label>
                        <p class="text-xs text-gray-500 mb-2">Enter your full business address (street, city, zip code)</p>
                        <input type="text" id="businessLocation" name="businessLocation" class="sb-input" placeholder="e.g., 123 Main St, New York, NY 10001">
                    </div>
                    
                    <!-- Country & State/Region -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="sb-label">Country</label>
                            <p class="text-xs text-gray-500 mb-2">Select your business country</p>
                            <div class="sb-search-dropdown">
                                <input type="text" id="sbCountry" name="sbCountry" class="sb-input" placeholder="Type to search country..." autocomplete="off">
                                <input type="hidden" id="sbCountryValue" name="sbCountryValue">
                                <div class="sb-search-dropdown-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div class="sb-dropdown-list" id="sbCountryDropdown"></div>
                            </div>
                        </div>
                        <div>
                            <label class="sb-label">State / Province / Region</label>
                            <p class="text-xs text-gray-500 mb-2">Select or type your state/region</p>
                            <div class="sb-search-dropdown">
                                <input type="text" id="sbState" name="sbState" class="sb-input" placeholder="Select country first..." autocomplete="off" disabled>
                                <input type="hidden" id="sbStateValue" name="sbStateValue">
                                <div class="sb-search-dropdown-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div class="sb-dropdown-list" id="sbStateDropdown"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Industry -->
                    <div>
                        <label class="sb-label">Business Industry</label>
                        <p class="text-xs text-gray-500 mb-2">What industry does your business belong to?</p>
                        <div class="sb-search-dropdown">
                            <input type="text" id="sbIndustry" name="sbIndustry" class="sb-input" placeholder="Type to search industry..." autocomplete="off">
                            <input type="hidden" id="sbIndustryValue" name="sbIndustryValue">
                            <div class="sb-search-dropdown-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div class="sb-dropdown-list" id="sbIndustryDropdown"></div>
                        </div>
                    </div>
                    
                    <!-- Product Type -->
                    <div>
                        <label class="sb-label">Product Type <span>*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Choose the type of reviews you want us to post on your business profile.</p>
                        <div class="sb-radio-group">
                            <input type="radio" id="productRatingReview" name="productType" value="Rating & Review" class="sb-radio" checked>
                            <label for="productRatingReview" class="sb-radio-label">Rating & Review</label>
                            
                            <input type="radio" id="productRatingOnly" name="productType" value="Rating Only" class="sb-radio">
                            <label for="productRatingOnly" class="sb-radio-label">Rating Only</label>
                        </div>
                        <p class="helper-text">Rating Only = Stars only, no written review text</p>
                    </div>
                    
                    <!-- Quantity & Businesses -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="sb-label">Total Reviews Ordered <span>*</span></label>
                            <p class="text-xs text-gray-500 mb-2">Enter the total number of reviews you've ordered.</p>
                            <input type="number" id="quantity" name="quantity" class="sb-input" placeholder="1" min="1" required>
                            <p class="helper-text">Enter total reviews ordered</p>
                            <p class="error-text" id="quantityError">Please enter the number of reviews</p>
                        </div>
                        <div>
                            <label class="sb-label">Split Across Businesses <span>*</span></label>
                            <p class="text-xs text-gray-500 mb-2">How many business locations do you want reviews for?</p>
                            <div class="sb-radio-group">
                                <input type="radio" id="numBiz1" name="numBusinesses" value="1" class="sb-radio" checked>
                                <label for="numBiz1" class="sb-radio-label">1</label>
                                
                                <input type="radio" id="numBiz2" name="numBusinesses" value="2" class="sb-radio">
                                <label for="numBiz2" class="sb-radio-label">2</label>
                                
                                <input type="radio" id="numBiz3" name="numBusinesses" value="3" class="sb-radio">
                                <label for="numBiz3" class="sb-radio-label">3</label>
                                
                                <input type="radio" id="numBiz4" name="numBusinesses" value="4" class="sb-radio">
                                <label for="numBiz4" class="sb-radio-label">4</label>
                                
                                <input type="radio" id="numBiz5" name="numBusinesses" value="5" class="sb-radio">
                                <label for="numBiz5" class="sb-radio-label">5</label>
                            </div>
                            <p class="helper-text">Minimum 20 reviews per business</p>
                        </div>
                    </div>
                    
                    <!-- Allocation Preview -->
                    <div id="allocationPreview" class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden">
                        <p class="text-sm font-semibold text-blue-800 mb-3">Review Allocation Preview:</p>
                        <div id="allocationCards" class="review-allocation"></div>
                        <p id="allocationError" class="text-sm text-red-600 mt-3 hidden"></p>
                    </div>
                </div>
                
                <div class="flex justify-between mt-10">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToStep(2)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back
                    </button>
                    <button type="button" class="sb-btn-primary" onclick="validateStep3()">
                        Next Step
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- STEP 4: Business Details (Dynamic Cards) -->
            <!-- ============================================ -->
            <div class="step-content" data-step="4">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: var(--blue);">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Business Details
                    </h1>
                    <p class="text-gray-500 mt-2">Configure details for each business</p>
                </div>
                
                <div id="businessCardsContainer" class="space-y-6"></div>
                
                <div id="reviewSummary" class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Total Reviews Allocated:</span>
                        <span id="totalAllocated" class="text-lg font-bold text-green-600">0 / 0</span>
                    </div>
                    <p id="allocationStatus" class="text-sm text-gray-500 mt-1"></p>
                </div>
                
                <div class="flex justify-between mt-10">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToStep(3)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back
                    </button>
                    <button type="button" id="btnStep4Next" class="sb-btn-primary" onclick="goToPreview()">
                        Preview Order
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- STEP 5: Preview -->
            <!-- ============================================ -->
            <div class="step-content" data-step="5">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: var(--blue);">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Review Your Order
                    </h1>
                    <p class="text-gray-500 mt-2">Please review all information before submitting</p>
                </div>
                
                <div id="previewContent" class="space-y-6"></div>
                
                <!-- Timeline Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 mt-6">
                    <h3 class="font-semibold text-blue-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Estimated Timeline
                    </h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Start Date</p>
                            <p class="font-semibold text-gray-900" id="preview_startDate">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Duration</p>
                            <p class="font-semibold text-blue-600" id="preview_duration">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Est. Completion</p>
                            <p class="font-semibold text-green-600" id="preview_endDate">-</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">Timeline may vary based on your Google Business profile condition</p>
                </div>
                
                <div class="flex justify-between mt-10">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToStep(4)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Edit
                    </button>
                    <button type="button" class="sb-btn-primary" onclick="showConfirmModal()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Confirm & Submit
                    </button>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- STEP 6: Thank You -->
            <!-- ============================================ -->
            <div class="step-content" data-step="6">
                <div class="success-card p-8 text-center">
                    <div class="checkmark-circle mb-6">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark-circle-bg" cx="26" cy="26" r="25" fill="none"/>
                            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-green-800 mb-2">Thank You!</h1>
                    <p class="text-green-700 text-lg mb-8">Your order has been submitted successfully.</p>
                    
                    <div class="bg-white rounded-xl p-6 text-left max-w-md mx-auto mb-8">
                        <div class="flex items-start mb-4">
                            <svg class="w-6 h-6 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div class="ml-3">
                                <p class="font-semibold text-gray-900">Your reviews will start posting</p>
                                <p class="text-gray-600">within 1-2 business days after content approval</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div class="ml-3">
                                <p class="font-semibold text-gray-900">Remember: 7-day claim window</p>
                                <p class="text-gray-600">After completion, you have 7 days to report issues</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BONUS: Free Marketing Audit CTA (Loyal Client Exclusive) -->
                    <div class="audit-bonus-card rounded-2xl p-6 max-w-md mx-auto mb-8">
                        <div class="flex items-center justify-center mb-3">
                            <span class="audit-bonus-pill">🎁 BONUS — LOYAL CLIENT EXCLUSIVE</span>
                        </div>
                        <h3 class="audit-bonus-title">FREE Marketing Audit</h3>
                        <p class="audit-bonus-worth"><span class="audit-strike">$497</span> &nbsp;FREE for you</p>
                        <ul class="audit-bonus-list">
                            <li><span class="audit-check">✓</span> Google &amp; Meta Ads — CPR, CPL, ROAS check</li>
                            <li><span class="audit-check">✓</span> Website conversion &amp; funnel leak audit</li>
                            <li><span class="audit-check">✓</span> Per-funnel breakdown (TOFU → MOFU → BOFU)</li>
                            <li><span class="audit-check">✓</span> Actionable report in 5–7 business days</li>
                        </ul>
                        <p class="audit-bonus-trust">View-only access &middot; NDA-protected &middot; No fee</p>
                        <button type="button" onclick="openAuditModal()" class="audit-bonus-cta">
                            🎁 &nbsp;CLAIM MY FREE AUDIT&nbsp; →
                        </button>
                    </div>

                    <a id="whatsappBtn" href="#" target="_blank" class="sb-btn-primary inline-flex items-center" style="background: #25D366;">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Contact Us on WhatsApp
                    </a>

                    <p class="text-sm text-gray-500 mt-6">
                        Please contact us on WhatsApp so we can send you the review content preview.
                    </p>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SOCIAL MEDIA STEP 2: Terms & Conditions -->
            <!-- ============================================ -->
            <div class="step-content sm-step" data-step="sm-2">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: #7C3AED;">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Social Media Terms
                    </h1>
                    <p class="text-gray-500 mt-2">Please read and agree before continuing</p>
                </div>
                
                <div class="space-y-4 mb-8">
                    <!-- TNC Point 1: Guarantee -->
                    <div class="sm-tnc-item bg-white border-2 border-purple-200 rounded-xl p-4">
                        <label class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox" class="sm-tnc-checkbox w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-index="0">
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">Guaranteed Delivery System</p>
                                <p class="text-sm text-gray-600 mt-1">We use a <strong>Stay Rate of 89.44%</strong>. If you order 10,000 followers, we will deliver approximately 11,181 to ensure you receive your full 10,000 after natural drop-off.</p>
                            </div>
                            <div class="flex-shrink-0 ml-3 sm-tnc-check hidden">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </label>
                    </div>
                    
                    <!-- TNC Point 2: No Refund -->
                    <div class="sm-tnc-item bg-white border-2 border-purple-200 rounded-xl p-4">
                        <label class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox" class="sm-tnc-checkbox w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-index="1">
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">No Refund Policy</p>
                                <p class="text-sm text-gray-600 mt-1">Because we over-deliver upfront to guarantee your target number, <strong>no refunds will be issued</strong>. You are protected by our guarantee system instead.</p>
                            </div>
                            <div class="flex-shrink-0 ml-3 sm-tnc-check hidden">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </label>
                    </div>
                    
                    <!-- TNC Point 3: Delivery Time -->
                    <div class="sm-tnc-item bg-white border-2 border-purple-200 rounded-xl p-4">
                        <label class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox" class="sm-tnc-checkbox w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-index="2">
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">Delivery Timeline</p>
                                <p class="text-sm text-gray-600 mt-1">Delivery starts within 24-48 hours after order confirmation. Large orders may take several days to complete for natural growth appearance.</p>
                            </div>
                            <div class="flex-shrink-0 ml-3 sm-tnc-check hidden">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-700">Progress:</span>
                        <span id="smTncProgress" class="text-sm font-semibold text-purple-900">0 / 3 agreed</span>
                    </div>
                    <div class="mt-2 w-full bg-purple-200 rounded-full h-2">
                        <div id="smTncProgressBar" class="h-2 rounded-full transition-all duration-300 bg-purple-600" style="width: 0%;"></div>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToSmStep(1)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back
                    </button>
                    <button type="button" id="btnSmStep2Next" class="sb-btn-primary" disabled onclick="goToSmStep(3)" style="background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);">
                        I Agree & Continue
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <p id="smTncWarning" class="text-sm text-purple-600 mt-3 text-center">Please agree to all 3 points above to continue</p>
            </div>
            
            <!-- ============================================ -->
            <!-- SOCIAL MEDIA STEP 3: Order Form -->
            <!-- ============================================ -->
            <div class="step-content sm-step" data-step="sm-3">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold" style="color: #7C3AED;">
                        <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Social Media Order
                    </h1>
                    <p class="text-gray-500 mt-2">Fill in your order details</p>
                </div>
                
                <div class="space-y-6">
                    <!-- Platform -->
                    <div>
                        <label class="sb-label">Platform <span>*</span></label>
                        <select id="smPlatform" name="smPlatform" class="sb-select" required onchange="updateSmPreview()">
                            <option value="">Select Platform</option>
                            <option value="Instagram">Instagram</option>
                            <option value="TikTok">TikTok</option>
                            <option value="Twitter">Twitter / X</option>
                            <option value="YouTube">YouTube</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <!-- Custom Platform (shown when Other is selected) -->
                    <div id="smCustomPlatformContainer" class="hidden">
                        <label class="sb-label">Specify Platform <span>*</span></label>
                        <input type="text" id="smCustomPlatform" name="smCustomPlatform" class="sb-input" placeholder="Enter platform name" onchange="updateSmPreview()">
                    </div>
                    
                    <!-- Service Type -->
                    <div>
                        <label class="sb-label">Service Type <span>*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 transition-all sm-service-option">
                                <input type="radio" name="smServiceType" value="Followers" class="w-5 h-5 text-purple-600" checked onchange="updateSmPreview()">
                                <span class="ml-3 font-medium">Followers</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 transition-all sm-service-option">
                                <input type="radio" name="smServiceType" value="Likes" class="w-5 h-5 text-purple-600" onchange="updateSmPreview()">
                                <span class="ml-3 font-medium">Likes</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Social Media Link -->
                    <div>
                        <label class="sb-label">Profile / Post Link <span>*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Enter your profile link for followers, or post link for likes</p>
                        <input type="url" id="smLink" name="smLink" class="sb-input" placeholder="https://instagram.com/yourusername" required onchange="updateSmPreview()">
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label class="sb-label">Target Quantity <span>*</span></label>
                        <p class="text-xs text-gray-500 mb-2">How many followers/likes do you want to receive?</p>
                        <input type="number" id="smQuantity" name="smQuantity" class="sb-input" placeholder="10000" required oninput="calculateSmDelivery()">
                    </div>
                    
                    <!-- Calculator Preview -->
                    <div class="sm-calculator" id="smCalculatorPreview">
                        <h3 class="font-semibold text-indigo-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Delivery Calculator
                        </h3>
                        <div class="sm-calc-row">
                            <span class="sm-calc-label">Stay Rate</span>
                            <span class="sm-calc-value">89.44%</span>
                        </div>
                        <div class="sm-calc-row">
                            <span class="sm-calc-label">Your Target</span>
                            <span class="sm-calc-value" id="smCalcTarget">0</span>
                        </div>
                        <div class="sm-calc-row">
                            <span class="sm-calc-label">We Will Fulfill</span>
                            <span class="sm-calc-highlight" id="smCalcFulfill">0</span>
                        </div>
                        <div class="sm-calc-row">
                            <span class="sm-calc-label">You Will Receive</span>
                            <span class="sm-calc-value text-green-600 font-bold" id="smCalcReceive">0</span>
                        </div>
                    </div>
                    
                    <!-- No Refill Notice -->
                    <div class="no-refill-notice">
                        <strong>Important:</strong> We already give the guarantee upfront by over-delivering. After this, there is no refill guarantee.
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="sb-label">Email Address <span>*</span></label>
                        <input type="email" id="smEmail" name="smEmail" class="sb-input" placeholder="your@email.com" required>
                    </div>
                    
                    <!-- Telegram -->
                    <div>
                        <label class="sb-label">Telegram Username / Number</label>
                        <p class="text-xs text-gray-500 mb-2">Optional backup contact in case our WhatsApp is temporarily unavailable.</p>
                        <input type="text" id="smTelegram" name="smTelegram" class="sb-input" placeholder="@username or +1234567890">
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="sb-label">WhatsApp Number <span>*</span></label>
                        <input type="tel" id="smWhatsapp" name="smWhatsapp" class="sb-input" placeholder="+1234567890" required>
                        <p class="helper-text">Include country code (e.g., +1 for USA)</p>
                    </div>
                </div>
                
                <div class="flex justify-between mt-8">
                    <button type="button" class="sb-btn-secondary flex items-center justify-center" onclick="goToSmStep(2)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back
                    </button>
                    <button type="button" class="sb-btn-primary" onclick="submitSmOrder()" style="background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);">
                        Submit Order
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SOCIAL MEDIA STEP 4: Success -->
            <!-- ============================================ -->
            <div class="step-content sm-step" data-step="sm-4">
                <div class="success-card p-8 text-center" style="background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%); border-color: #C4B5FD;">
                    <div class="w-20 h-20 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-purple-900 mb-2">Order Submitted!</h1>
                    <p class="text-purple-700 mb-4">Your social media order has been received</p>
                    
                    <div class="bg-white rounded-xl p-4 mb-6 text-left">
                        <p class="text-sm text-gray-600 mb-2">Order ID:</p>
                        <p class="text-lg font-bold text-purple-900" id="smOrderIdDisplay">-</p>
                    </div>
                    
                    <div class="bg-purple-100 rounded-xl p-4 mb-6">
                        <p class="text-purple-800 font-medium">What happens next?</p>
                        <ul class="text-sm text-purple-700 mt-2 text-left space-y-1">
                            <li>1. We verify your order details</li>
                            <li>2. Delivery starts within 24-48 hours</li>
                            <li>3. You receive updates via WhatsApp</li>
                        </ul>
                    </div>

                    <!-- BONUS: Free Marketing Audit CTA (Loyal Client Exclusive) -->
                    <div class="audit-bonus-card rounded-2xl p-6 mb-6 text-center">
                        <div class="flex items-center justify-center mb-3">
                            <span class="audit-bonus-pill">🎁 BONUS — LOYAL CLIENT EXCLUSIVE</span>
                        </div>
                        <h3 class="audit-bonus-title">FREE Marketing Audit</h3>
                        <p class="audit-bonus-worth"><span class="audit-strike">$497</span> &nbsp;FREE for you</p>
                        <ul class="audit-bonus-list">
                            <li><span class="audit-check">✓</span> Google &amp; Meta Ads — CPR, CPL, ROAS check</li>
                            <li><span class="audit-check">✓</span> Website conversion &amp; funnel leak audit</li>
                            <li><span class="audit-check">✓</span> Per-funnel breakdown (TOFU → MOFU → BOFU)</li>
                            <li><span class="audit-check">✓</span> Actionable report in 5–7 business days</li>
                        </ul>
                        <p class="audit-bonus-trust">View-only access &middot; NDA-protected &middot; No fee</p>
                        <button type="button" onclick="openAuditModal()" class="audit-bonus-cta">
                            🎁 &nbsp;CLAIM MY FREE AUDIT&nbsp; →
                        </button>
                    </div>

                    <a id="smWhatsappBtn" href="https://wa.me/6287870707202" target="_blank" class="sb-btn-primary inline-flex items-center" style="background: #25D366;">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Contact Us on WhatsApp
                    </a>
                </div>
            </div>
        </form>
    </main>
    
    <!-- TNC Confirm Modal -->
    <div id="tncConfirmModal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Final Confirmation</h3>
                    <p class="text-gray-600">You have read all <?php echo count($tncPoints); ?> terms above.</p>
                    <p class="text-gray-600 mt-1">Do you fully understand and agree to proceed?</p>
                </div>
                
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <p class="text-sm text-amber-800">
                        <strong>Important:</strong> If you are unsure or have questions, we offer a full refund before starting. No hard feelings!
                    </p>
                </div>
                
                <div class="space-y-3">
                    <button type="button" onclick="confirmTncAndContinue()" class="w-full sb-btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Yes, I Agree & Continue
                    </button>
                    <a href="https://smart-buzzer.com/refund" class="w-full sb-btn-danger flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        No, I Need Refund
                    </a>
                    <a href="https://wa.me/6287870707202" target="_blank" class="w-full sb-btn-secondary flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        Chat with Admin First
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ready to Start Modal -->
    <div id="readyModal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Are You Ready?</h3>
                    <p class="text-gray-600">You are about to fill out our onboarding form</p>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <p class="text-sm text-red-800">
                        <strong>Please take this seriously.</strong> The more detailed your input, the better your reviews will be. Expect to spend <strong>10-15 minutes</strong>.
                    </p>
                </div>
                
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <p class="text-sm text-amber-800 mb-3">
                        <strong>This form requires:</strong>
                    </p>
                    <ul class="text-sm text-amber-700 space-y-2">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Your business details & Google Maps link
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Keywords you want in your reviews
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Example reviews
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Any specific instructions for review writers
                        </li>
                    </ul>
                </div>
                
                <div class="space-y-3">
                    <button type="button" onclick="confirmReadyAndStart()" class="w-full sb-btn-secondary flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Yes, I'm Ready
                    </button>
                    <button type="button" onclick="showNotReadyModal()" class="w-full sb-btn-secondary flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        I'm Not Ready Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Not Ready Modal -->
    <div id="notReadyModal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Action Required</h3>
                    <p class="text-gray-600">Please contact your Account Manager to confirm</p>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-xs text-gray-500 mb-2 font-medium">SAVE THIS LINK</p>
                    <div class="flex items-center">
                        <input type="text" id="formLinkInput" value="https://smart-buzzer.com/submit/" readonly class="flex-1 bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 mr-2">
                        <button type="button" onclick="copyFormLink()" id="copyLinkBtn" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span id="copyBtnText">Copy</span>
                        </button>
                    </div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <p class="text-sm text-red-800 mb-3">
                        <strong>You must contact your Account Manager</strong> on WhatsApp to confirm before proceeding. This is required.
                    </p>
                    <a href="https://wa.me/6287870707202?text=Hi%2C%20I%27ve%20already%20placed%20my%20order%20but%20I%20need%20more%20time%20to%20complete%20the%20onboarding%20form.%20I%27ll%20fill%20it%20out%20soon.%20Thank%20you!" target="_blank" class="w-full inline-flex items-center justify-center bg-green-500 text-white px-4 py-3 rounded-xl font-semibold hover:bg-green-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Contact Account Manager (Required)
                    </a>
                </div>
                
                <button type="button" onclick="hideNotReadyModal()" class="w-full sb-btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>
    
    <!-- Audit NDA Modal — 4-Step Wizard (BONUS Free Marketing Audit) -->
    <div id="auditNdaModal" class="audit-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="auditNdaTitle">
        <div class="audit-modal-card">
            <!-- Header (persistent across steps) -->
            <div class="audit-modal-header">
                <button type="button" class="audit-modal-close" onclick="closeAuditModal()" aria-label="Close">&times;</button>
                <div class="text-center">
                    <div class="audit-bonus-pill" style="margin-bottom: 6px;">🎁 BONUS — LOYAL CLIENT EXCLUSIVE</div>
                    <h2 id="auditNdaTitle" style="font-size: 20px; font-weight: 800; color: #78350F; margin: 2px 0;">Free Marketing Audit</h2>
                </div>
            </div>

            <!-- Step indicator -->
            <div class="audit-stepper">
                <div class="audit-stepper-row">
                    <div class="audit-step-dot active" data-step="1">1</div>
                    <div class="audit-step-line" data-line="1-2"></div>
                    <div class="audit-step-dot" data-step="2">2</div>
                    <div class="audit-step-line" data-line="2-3"></div>
                    <div class="audit-step-dot" data-step="3">3</div>
                    <div class="audit-step-line" data-line="3-4"></div>
                    <div class="audit-step-dot" data-step="4">4</div>
                </div>
                <div class="audit-stepper-labels">
                    <span class="audit-step-label active" data-step="1">Bonus</span>
                    <span class="audit-step-label" data-step="2">Need</span>
                    <span class="audit-step-label" data-step="3">NDA</span>
                    <span class="audit-step-label" data-step="4">Agree</span>
                </div>
                <p class="audit-step-counter">Step <span id="auditStepCurrent">1</span> of 4</p>
            </div>

            <!-- Body — only one step visible at a time -->
            <div class="audit-modal-body">

                <!-- ============================================ -->
                <!-- STEP 1 — Your Bonus (HOOK) -->
                <!-- ============================================ -->
                <div class="audit-step-content active" data-step="1">
                    <div class="audit-step-headline">
                        <div class="audit-step-emoji">🎁</div>
                        <h3>Your Bonus</h3>
                        <p>Worth <span class="audit-strike">$497</span> &mdash; yours <strong>FREE</strong> as a loyal client.</p>
                    </div>

                    <div class="audit-section-box">
                        <p class="audit-section-title">What you get</p>
                        <ul class="audit-section-list">
                            <li><span class="audit-check">✓</span> Google Ads audit — CPR, CPL, ROAS, optimization check</li>
                            <li><span class="audit-check">✓</span> Meta Ads audit — funnel leak detection &amp; ad fatigue</li>
                            <li><span class="audit-check">✓</span> Website CRO review — conversion bottlenecks</li>
                            <li><span class="audit-check">✓</span> Per-funnel breakdown (TOFU → MOFU → BOFU)</li>
                            <li><span class="audit-check">✓</span> Delivered in 5–7 business days via WhatsApp</li>
                        </ul>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- STEP 2 — What We Need (TRANSPARENCY) -->
                <!-- ============================================ -->
                <div class="audit-step-content" data-step="2">
                    <div class="audit-step-headline">
                        <div class="audit-step-emoji">🔒</div>
                        <h3>What We Need</h3>
                        <p>Read-only access &mdash; that&rsquo;s it. Nothing more.</p>
                    </div>

                    <div class="audit-section-box" style="background: #EFF6FF; border-color: #BFDBFE;">
                        <p class="audit-section-title" style="color: #1E40AF;">View-only access to:</p>
                        <ul class="audit-section-list">
                            <li><span class="dot" style="color: #2563EB;">•</span> Google Ads / Meta Ads dashboards (read-only)</li>
                            <li><span class="dot" style="color: #2563EB;">•</span> Google Analytics (Viewer role)</li>
                            <li><span class="dot" style="color: #2563EB;">•</span> Google Business Profile (insights only)</li>
                        </ul>
                    </div>

                    <div class="audit-section-box" style="background: #FEF2F2; border-color: #FECACA;">
                        <p class="audit-section-title" style="color: #991B1B;">We CANNOT:</p>
                        <ul class="audit-section-list">
                            <li><span class="dot" style="color: #DC2626;">✕</span> Edit or change your campaigns</li>
                            <li><span class="dot" style="color: #DC2626;">✕</span> Withdraw funds or change billing</li>
                            <li><span class="dot" style="color: #DC2626;">✕</span> Access your payment methods</li>
                            <li><span class="dot" style="color: #DC2626;">✕</span> Share your data with anyone</li>
                        </ul>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- STEP 3 — NDA Terms (LEGAL) -->
                <!-- ============================================ -->
                <div class="audit-step-content" data-step="3">
                    <div class="audit-step-headline">
                        <div class="audit-step-emoji">📜</div>
                        <h3>NDA — 7 Key Points</h3>
                        <p>Plain English. No legalese.</p>
                    </div>

                    <ol class="audit-nda-numbered">
                        <li><strong>Confidentiality.</strong> Smart Buzzer (Portland, OR) keeps your data strictly private &amp; secure.</li>
                        <li><strong>Sole Purpose.</strong> Data is used ONLY for the audit &mdash; no other use.</li>
                        <li><strong>No Third Parties.</strong> We never sell, share, or disclose your data.</li>
                        <li><strong>View-Only Access.</strong> No edits, no changes, no withdrawals.</li>
                        <li><strong>Term.</strong> 12 months or upon audit completion (whichever first).</li>
                        <li><strong>Termination.</strong> Either party may end this agreement anytime with notice.</li>
                        <li><strong>Governing Law.</strong> State of Oregon, USA.</li>
                    </ol>
                </div>

                <!-- ============================================ -->
                <!-- STEP 4 — Final Agreement (COMMITMENT) -->
                <!-- ============================================ -->
                <div class="audit-step-content" data-step="4">
                    <div class="audit-step-headline">
                        <div class="audit-step-emoji">✅</div>
                        <h3>Almost There!</h3>
                        <p>One last confirmation to claim your audit.</p>
                    </div>

                    <label class="audit-agree-row">
                        <input type="checkbox" id="auditAgreeCheck" onchange="onAuditAgreeChange()">
                        <span class="audit-agree-text">
                            <strong>I AGREE</strong> to the NDA terms and grant Smart Buzzer view-only access to my ad accounts for the free marketing audit.
                        </span>
                    </label>

                    <button type="button" id="auditFinalCta" class="audit-final-cta" disabled onclick="confirmAuditAgree()">
                        ✅ &nbsp;I AGREE — OPEN WHATSAPP&nbsp; →
                    </button>
                </div>

            </div>

            <!-- Footer — Back / Next buttons + Maybe Later -->
            <div class="audit-modal-footer">
                <button type="button" id="auditBackBtn" class="audit-nav-back" onclick="prevAuditStep()" style="visibility: hidden;">
                    ← Back
                </button>
                <button type="button" id="auditNextBtn" class="audit-nav-next" onclick="nextAuditStep()">
                    Next →
                </button>
            </div>

            <button type="button" class="audit-maybe-later" onclick="closeAuditModal()">Maybe later</button>
        </div>
    </div>

    <!-- Submit Confirm Modal -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ready to Submit?</h3>
                    <p class="text-gray-600">Please confirm all information is correct.</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Estimated Duration:</strong> <span id="confirm_duration">-</span><br>
                        <strong>Est. Completion:</strong> <span id="confirm_endDate">-</span>
                    </p>
                </div>
                
                <div class="space-y-3 mb-6">
                    <label class="confirm-item flex items-start p-4 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer">
                        <input type="checkbox" class="confirm-checkbox w-5 h-5 mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-3 text-sm text-gray-700">I confirm all business information is accurate</span>
                    </label>
                    <label class="confirm-item flex items-start p-4 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer">
                        <input type="checkbox" class="confirm-checkbox w-5 h-5 mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-3 text-sm text-gray-700">I understand that we submit 1-5 reviews daily, ~50% will stick, and drops are replaced automatically until fulfilled</span>
                    </label>
                    <label class="confirm-item flex items-start p-4 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer">
                        <input type="checkbox" class="confirm-checkbox w-5 h-5 mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-3 text-sm text-gray-700">I understand the 7-day claim window after completion</span>
                    </label>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="hideConfirmModal()" class="flex-1 sb-btn-secondary">Cancel</button>
                    <button type="button" id="finalSubmitBtn" onclick="submitOrder()" class="flex-1 sb-btn-primary" disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Submit Order
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="modal-overlay" style="background: rgba(255,255,255,0.9);">
        <div class="text-center">
            <svg class="spinner w-12 h-12 mx-auto mb-4 text-green-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-lg font-semibold text-gray-900">Submitting your order...</p>
            <p class="text-sm text-gray-500">Please wait</p>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="img-modal" onclick="hideImageModal()">
        <img id="modalImage" src="" alt="Enlarged image">
    </div>
    
    <!-- Toast Notification -->
    <div id="sbToast" class="sb-toast">
        <svg id="sbToastIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="sbToastMsg">Message</span>
    </div>
    
    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-12 py-6 text-center text-sm text-gray-500">
        <p>2025 Smart Buzzer. All rights reserved.</p>
    </footer>

    <script>
    // ============================================================================
    // SMART BUZZER ORDER FORM - v3.0
    // ============================================================================
    
    let currentStep = 1;
    const totalSteps = 6;
    let businessData = [];
    
    const PLATFORMS = ['Google', 'Yelp', 'Facebook', 'Trustpilot', 'Other'];
    const MIN_REVIEWS_PER_BUSINESS = 1;
    
    // Social Media Settings
    let currentServiceType = 'reviews'; // 'reviews' or 'social_media'
    let smCurrentStep = 1;
    const SM_STAY_RATE = 0.8944; // 89.44%
    const SM_PLATFORMS = ['Instagram', 'TikTok', 'Twitter', 'YouTube', 'Other'];
    const SM_SERVICE_TYPES = ['Followers', 'Likes'];
    
    // ============================================================================
    // COUNTRY, STATE & INDUSTRY DATA
    // ============================================================================
    
    const SB_COUNTRIES_STATES = {
        'United States': ['Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming','District of Columbia'],
        'Canada': ['Alberta','British Columbia','Manitoba','New Brunswick','Newfoundland and Labrador','Northwest Territories','Nova Scotia','Nunavut','Ontario','Prince Edward Island','Quebec','Saskatchewan','Yukon'],
        'Australia': ['New South Wales','Victoria','Queensland','South Australia','Western Australia','Tasmania','Northern Territory','Australian Capital Territory']
    };
    
    const SB_INDUSTRIES = [
        'Accounting & Tax Services','Agriculture','Airline & Aviation','Apparel & Fashion',
        'Architecture & Planning','Automotive - Dealer','Automotive - Repair & Service',
        'Banking & Finance','Beauty Salon & Spa','Biotechnology','Building Materials',
        'Business Consulting','Cannabis & CBD','Car Wash','Catering',
        'Child Care & Day Care','Chiropractic','Cleaning Services','Computer & IT Services',
        'Construction','Cosmetics','Coworking Space','Dental','E-Commerce',
        'Education & Training','Electrical Services','Engineering','Entertainment & Events',
        'Environmental Services','Eye Care & Optometry','Farming & Ranch',
        'Financial Advisory','Fitness & Gym','Florist','Food & Beverage',
        'Funeral Services','Furniture','Gaming','Graphic Design',
        'Grocery & Supermarket','Hair Salon & Barbershop','Hardware Store',
        'Healthcare - Clinic','Healthcare - Hospital','Healthcare - Mental Health',
        'Healthcare - Pharmacy','Heating & HVAC','Home Improvement','Home Staging',
        'Hotel & Hospitality','Human Resources','Import & Export','Industrial Manufacturing',
        'Insurance','Interior Design','Jewelry','Landscaping & Lawn Care',
        'Laundry & Dry Cleaning','Legal Services','Library','Limousine & Car Service',
        'Locksmith','Logistics & Shipping','Marketing & Advertising','Massage Therapy',
        'Media & Publishing','Medical Device','Medical Practice','Mining & Metals',
        'Mobile Phone Repair','Mortgage & Lending','Moving & Storage','Music & Audio',
        'Nail Salon','Nonprofit & Charity','Notary','Nursing & Home Care',
        'Oil & Energy','Painting','Parking','Pawn Shop','Pest Control',
        'Pet Services & Veterinary','Photography','Physical Therapy','Plumbing',
        'Printing','Property Management','Public Relations','Real Estate',
        'Recycling & Waste Management','Religion & Spiritual','Rental Services',
        'Restaurant','Retail - Clothing','Retail - Electronics','Retail - General',
        'Roofing','Security Services','Self Storage','Senior Care & Assisted Living',
        'Shoe Repair','Sign Making','Solar Energy','Staffing & Recruiting',
        'Tattoo & Piercing','Taxi & Ride Share','Telecommunications',
        'Therapy & Counseling','Tiling & Flooring','Tourism & Travel Agency',
        'Towing','Transportation','Tree Service','Trucking','Tutoring',
        'Upholstery','Vacation Rental','Vending Machine','Veterinary',
        'Video Production','Virtual Assistant','Warehouse','Web Development',
        'Wedding & Bridal','Wellness & Holistic','Window Cleaning','Wine & Spirits',
        'Yoga & Pilates','Other'
    ];
    
    // ============================================================================
    // SEARCHABLE DROPDOWN ENGINE
    // ============================================================================
    
    function initSearchableDropdown(inputId, dropdownId, items, hiddenId, opts = {}) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const hidden = document.getElementById(hiddenId);
        if (!input || !dropdown) return;
        
        let highlighted = -1;
        let filtered = [];
        
        function renderItems(list, query) {
            filtered = list;
            highlighted = -1;
            if (list.length === 0) {
                dropdown.innerHTML = '<div class="sb-dropdown-empty">No matches found - you can type your own</div>';
                dropdown.classList.add('open');
                return;
            }
            const q = (query || '').toLowerCase();
            dropdown.innerHTML = list.map((item, i) => {
                let label = item;
                if (q && q.length > 0) {
                    const regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                    label = item.replace(regex, '<mark>$1</mark>');
                }
                return '<div class="sb-dropdown-item" data-idx="' + i + '" data-value="' + escapeHtml(item) + '">' + label + '</div>';
            }).join('');
            dropdown.classList.add('open');
        }
        
        function selectItem(value) {
            input.value = value;
            if (hidden) hidden.value = value;
            dropdown.classList.remove('open');
            highlighted = -1;
            if (opts.onSelect) opts.onSelect(value);
            saveFormData();
        }
        
        input.addEventListener('focus', function() {
            const q = input.value.trim().toLowerCase();
            const list = q.length > 0
                ? items.filter(it => it.toLowerCase().includes(q))
                : items;
            renderItems(list, q);
        });
        
        input.addEventListener('input', function() {
            const q = input.value.trim().toLowerCase();
            if (hidden) hidden.value = input.value.trim();
            const list = q.length > 0
                ? items.filter(it => it.toLowerCase().includes(q))
                : items;
            renderItems(list, q);
            if (opts.onInput) opts.onInput(input.value.trim());
        });
        
        input.addEventListener('keydown', function(e) {
            const visibleItems = dropdown.querySelectorAll('.sb-dropdown-item');
            if (!dropdown.classList.contains('open') || visibleItems.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlighted = Math.min(highlighted + 1, visibleItems.length - 1);
                visibleItems.forEach((el, i) => el.classList.toggle('highlighted', i === highlighted));
                visibleItems[highlighted]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlighted = Math.max(highlighted - 1, 0);
                visibleItems.forEach((el, i) => el.classList.toggle('highlighted', i === highlighted));
                visibleItems[highlighted]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (highlighted >= 0 && visibleItems[highlighted]) {
                    selectItem(visibleItems[highlighted].dataset.value);
                } else {
                    dropdown.classList.remove('open');
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('open');
            }
        });
        
        dropdown.addEventListener('click', function(e) {
            const item = e.target.closest('.sb-dropdown-item');
            if (item) selectItem(item.dataset.value);
        });
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sb-search-dropdown') || !input.contains(e.target)) {
                if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
            }
        });
    }
    
    function initCountryStateDropdowns() {
        const countryNames = Object.keys(SB_COUNTRIES_STATES).sort();
        
        initSearchableDropdown('sbCountry', 'sbCountryDropdown', countryNames, 'sbCountryValue', {
            onSelect: function(country) {
                updateStateDropdown(country);
            },
            onInput: function(val) {
                const stateInput = document.getElementById('sbState');
                const match = countryNames.find(c => c.toLowerCase() === val.toLowerCase());
                if (match) {
                    updateStateDropdown(match);
                } else {
                    stateInput.disabled = true;
                    stateInput.placeholder = 'Select country first...';
                    stateInput.value = '';
                    document.getElementById('sbStateValue').value = '';
                }
            }
        });
        
        initSearchableDropdown('sbIndustry', 'sbIndustryDropdown', SB_INDUSTRIES, 'sbIndustryValue');
    }
    
    function updateStateDropdown(country) {
        const stateInput = document.getElementById('sbState');
        const stateDropdown = document.getElementById('sbStateDropdown');
        const stateHidden = document.getElementById('sbStateValue');
        const states = SB_COUNTRIES_STATES[country] || [];
        
        stateInput.value = '';
        stateHidden.value = '';
        stateDropdown.innerHTML = '';
        stateDropdown.classList.remove('open');
        
        if (states.length > 0) {
            stateInput.disabled = false;
            stateInput.placeholder = 'Type to search state/region...';
            initSearchableDropdown('sbState', 'sbStateDropdown', states, 'sbStateValue');
        } else {
            stateInput.disabled = false;
            stateInput.placeholder = 'Type your state/region...';
            initSearchableDropdown('sbState', 'sbStateDropdown', [], 'sbStateValue');
        }
    }
    
    // ============================================================================
    // INITIALIZATION
    // ============================================================================
    
    document.addEventListener('DOMContentLoaded', function() {
        initTncCheckboxes();
        initEventListeners();
        initCountryStateDropdowns();
        loadFormData();
        
        // Re-init state dropdown if country was restored
        const restoredCountry = document.getElementById('sbCountryValue').value || document.getElementById('sbCountry').value;
        if (restoredCountry) {
            const match = Object.keys(SB_COUNTRIES_STATES).find(c => c.toLowerCase() === restoredCountry.toLowerCase());
            if (match) updateStateDropdown(match);
            // Re-restore state value after dropdown reinit
            setTimeout(() => {
                const savedData = localStorage.getItem('sbOrderForm');
                if (savedData) {
                    const data = JSON.parse(savedData);
                    if (data.sbState) document.getElementById('sbState').value = data.sbState;
                    if (data.sbStateValue) document.getElementById('sbStateValue').value = data.sbStateValue;
                }
            }, 50);
        }
        
        // Auto-open service popup on first visit (if no saved data)
        const savedData = localStorage.getItem('smartBuzzerFormData');
        if (!savedData) {
            setTimeout(() => openServicePopup(), 300);
        }
    });
    
    function initTncCheckboxes() {
        const tncCheckboxes = document.querySelectorAll('.tnc-checkbox');
        const totalTnc = tncCheckboxes.length;
        const tncHint = document.getElementById('tncHint');
        
        tncCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const item = this.closest('.tnc-item');
                const checkIcon = item.querySelector('.tnc-check-icon');
                const currentIndex = parseInt(this.dataset.index);
                
                if (this.checked) {
                    item.classList.add('checked');
                    checkIcon.classList.remove('hidden');
                    
                    const nextIndex = currentIndex + 1;
                    const nextItem = document.querySelector(`.tnc-item[data-index="${nextIndex}"]`);
                    
                    if (nextItem) {
                        nextItem.style.display = 'block';
                        setTimeout(() => {
                            nextItem.style.opacity = '1';
                            nextItem.style.transform = 'translateY(0)';
                        }, 50);
                        setTimeout(() => {
                            nextItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 200);
                    }
                    
                    if (nextIndex >= totalTnc && tncHint) {
                        tncHint.style.display = 'none';
                    }
                } else {
                    item.classList.remove('checked');
                    checkIcon.classList.add('hidden');
                    
                    document.querySelectorAll('.tnc-item').forEach(tncItem => {
                        const itemIndex = parseInt(tncItem.dataset.index);
                        if (itemIndex > currentIndex) {
                            const cb = tncItem.querySelector('.tnc-checkbox');
                            if (cb) cb.checked = false;
                            tncItem.classList.remove('checked');
                            const icon = tncItem.querySelector('.tnc-check-icon');
                            if (icon) icon.classList.add('hidden');
                            tncItem.style.opacity = '0';
                            tncItem.style.transform = 'translateY(-10px)';
                            setTimeout(() => { tncItem.style.display = 'none'; }, 300);
                        }
                    });
                    
                    if (tncHint) tncHint.style.display = 'block';
                }
                
                updateTncProgress();
            });
        });
    }
    
    function updateTncProgress() {
        // Defensive: count UNIQUE data-index values (not raw DOM nodes) so duplicate
        // checkboxes from corrupted tnc.json don't inflate the total and lock the wizard.
        const allBoxes = Array.from(document.querySelectorAll('.tnc-checkbox'));
        const uniqueIndices = new Set();
        const checkedIndices = new Set();
        allBoxes.forEach(cb => {
            const idx = cb.dataset.index;
            if (idx === undefined) return;
            uniqueIndices.add(idx);
            if (cb.checked) checkedIndices.add(idx);
        });
        const totalTnc = uniqueIndices.size;
        const checked = checkedIndices.size;
        const progressText = document.getElementById('tncProgress');
        const progressBar = document.getElementById('tncProgressBar');
        const btnNext = document.getElementById('btnStep2Next');
        const warning = document.getElementById('tncWarning');

        if (progressText) progressText.textContent = `${checked} / ${totalTnc} agreed`;
        if (progressBar) progressBar.style.width = totalTnc > 0 ? `${(checked / totalTnc) * 100}%` : '0%';

        if (totalTnc > 0 && checked === totalTnc) {
            if (btnNext) btnNext.disabled = false;
            if (warning) warning.classList.add('hidden');
        } else {
            if (btnNext) btnNext.disabled = true;
            if (warning) {
                warning.textContent = `Please agree to all ${totalTnc} points above to continue`;
                warning.classList.remove('hidden');
            }
        }

        // Safety net: ensure all checked items AND the first unchecked item are visible
        let foundFirstUnchecked = false;
        document.querySelectorAll('.tnc-item').forEach(item => {
            const cb = item.querySelector('.tnc-checkbox');
            if (!cb) return;
            if (cb.checked) {
                item.style.display = 'block';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            } else if (!foundFirstUnchecked) {
                foundFirstUnchecked = true;
                item.style.display = 'block';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }
        });

        saveFormData();
    }
    
    function initEventListeners() {
        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('quantity').addEventListener('input', updateAllocationPreview);
        
        document.querySelectorAll('input[name="numBusinesses"]').forEach(radio => {
            radio.addEventListener('change', updateAllocationPreview);
        });
        
        document.querySelectorAll('input[name="productType"]').forEach(radio => {
            radio.addEventListener('change', handleProductTypeChange);
        });
        
        document.getElementById('orderForm').addEventListener('input', saveFormData);
        document.getElementById('orderForm').addEventListener('change', saveFormData);
        
        document.querySelectorAll('.confirm-checkbox').forEach(cb => {
            cb.addEventListener('change', updateConfirmButton);
        });
        
        // Social Media TNC Checkboxes
        initSmTncCheckboxes();
        
        // Social Media Platform Change
        const smPlatformEl = document.getElementById('smPlatform');
        if (smPlatformEl) {
            smPlatformEl.addEventListener('change', function() {
                const customContainer = document.getElementById('smCustomPlatformContainer');
                if (this.value === 'Other') {
                    customContainer.classList.remove('hidden');
                } else {
                    customContainer.classList.add('hidden');
                }
            });
        }
    }
    
    // ============================================================================
    // SERVICE TYPE SELECTOR
    // ============================================================================
    
    function openServicePopup() {
        const overlay = document.getElementById('servicePopupOverlay');
        overlay.classList.add('active');
        
        // Update popup cards selection state
        document.getElementById('popupCardReviews').classList.toggle('selected', currentServiceType === 'reviews');
        document.getElementById('popupCardSocialMedia').classList.toggle('selected', currentServiceType === 'social_media');
    }
    
    function closeServicePopup(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('servicePopupOverlay').classList.remove('active');
    }
    
    function selectServiceFromPopup(type) {
        selectServiceType(type);
        closeServicePopup();
    }
    
    function selectServiceType(type) {
        currentServiceType = type;
        document.getElementById('serviceType').value = type;
        
        // Update display card
        const displayCard = document.getElementById('selectedServiceDisplay');
        const iconContainer = document.getElementById('selectedServiceIcon');
        const serviceName = document.getElementById('selectedServiceName');
        const serviceDesc = document.getElementById('selectedServiceDesc');
        const badge = displayCard.querySelector('span');
        
        if (type === 'reviews') {
            // Yellow theme for Reviews
            displayCard.style.background = 'linear-gradient(135deg, #FEF9C3 0%, #FEF08A 100%)';
            displayCard.style.borderColor = '#EAB308';
            badge.style.background = '#EAB308';
            iconContainer.innerHTML = '<svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>';
            serviceName.textContent = 'Reviews Booster';
            serviceDesc.textContent = 'Google, Yelp, Facebook reviews';
        } else {
            // Purple theme for Social Media
            displayCard.style.background = 'linear-gradient(135deg, #F3E8FF 0%, #E9D5FF 100%)';
            displayCard.style.borderColor = '#9333EA';
            badge.style.background = '#9333EA';
            iconContainer.innerHTML = '<svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>';
            serviceName.textContent = 'Social Media';
            serviceDesc.textContent = 'Instagram, TikTok, Twitter, YouTube';
        }
        
        // Update popup cards selection state
        const popupReviews = document.getElementById('popupCardReviews');
        const popupSocialMedia = document.getElementById('popupCardSocialMedia');
        if (popupReviews) popupReviews.classList.toggle('selected', type === 'reviews');
        if (popupSocialMedia) popupSocialMedia.classList.toggle('selected', type === 'social_media');
        
        // Show/hide flow content
        const reviewsFlow = document.getElementById('reviewsFlowContent');
        const socialMediaFlow = document.getElementById('socialMediaFlowContent');
        
        if (type === 'reviews') {
            reviewsFlow.classList.remove('hidden');
            socialMediaFlow.classList.remove('active');
        } else {
            reviewsFlow.classList.add('hidden');
            socialMediaFlow.classList.add('active');
        }
        
        saveFormData();
    }
    
    // ============================================================================
    // SOCIAL MEDIA FLOW
    // ============================================================================
    
    function startSocialMediaFlow() {
        goToSmStep(2);
    }
    
    function goToSmStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
        
        if (step === 1) {
            // Go back to service selector (Step 1)
            document.querySelector('.step-content[data-step="1"]').classList.add('active');
            smCurrentStep = 1;
        } else {
            // Show social media step
            const smStep = document.querySelector(`.step-content[data-step="sm-${step}"]`);
            if (smStep) {
                smStep.classList.add('active');
                smCurrentStep = step;
            }
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        saveFormData();
    }
    
    function initSmTncCheckboxes() {
        const checkboxes = document.querySelectorAll('.sm-tnc-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const item = this.closest('.sm-tnc-item');
                const checkIcon = item.querySelector('.sm-tnc-check');
                
                if (this.checked) {
                    item.classList.add('border-purple-500', 'bg-purple-50');
                    item.classList.remove('border-purple-200');
                    if (checkIcon) checkIcon.classList.remove('hidden');
                } else {
                    item.classList.remove('border-purple-500', 'bg-purple-50');
                    item.classList.add('border-purple-200');
                    if (checkIcon) checkIcon.classList.add('hidden');
                }
                
                updateSmTncProgress();
            });
        });
    }
    
    function updateSmTncProgress() {
        const total = document.querySelectorAll('.sm-tnc-checkbox').length;
        const checked = document.querySelectorAll('.sm-tnc-checkbox:checked').length;
        
        const progressText = document.getElementById('smTncProgress');
        const progressBar = document.getElementById('smTncProgressBar');
        const btnNext = document.getElementById('btnSmStep2Next');
        const warning = document.getElementById('smTncWarning');
        
        if (progressText) progressText.textContent = `${checked} / ${total} agreed`;
        if (progressBar) progressBar.style.width = `${(checked / total) * 100}%`;
        
        if (checked === total) {
            if (btnNext) btnNext.disabled = false;
            if (warning) warning.classList.add('hidden');
        } else {
            if (btnNext) btnNext.disabled = true;
            if (warning) warning.classList.remove('hidden');
        }
        
        saveFormData();
    }
    
    function calculateSmDelivery() {
        const targetInput = document.getElementById('smQuantity');
        const target = parseInt(targetInput.value) || 0;
        
        const fulfill = Math.ceil(target / SM_STAY_RATE);
        
        document.getElementById('smCalcTarget').textContent = target.toLocaleString();
        document.getElementById('smCalcFulfill').textContent = fulfill.toLocaleString();
        document.getElementById('smCalcReceive').textContent = target > 0 ? target.toLocaleString() : '0';
        
        updateSmPreview();
    }
    
    function updateSmPreview() {
        // This can be expanded to show live preview of order details
        saveFormData();
    }
    
    function validateSmForm() {
        const platform = document.getElementById('smPlatform').value;
        const customPlatform = document.getElementById('smCustomPlatform').value;
        const link = document.getElementById('smLink').value;
        const quantity = document.getElementById('smQuantity').value;
        const email = document.getElementById('smEmail').value;
        const whatsapp = document.getElementById('smWhatsapp').value;
        
        if (!platform) {
            alert('Please select a platform');
            return false;
        }
        
        if (platform === 'Other' && !customPlatform.trim()) {
            alert('Please specify the platform name');
            return false;
        }
        
        if (!link.trim()) {
            alert('Please enter your profile/post link');
            return false;
        }
        
        if (!quantity || parseInt(quantity) <= 0) {
            alert('Please enter a valid quantity');
            return false;
        }
        
        if (!email || !email.includes('@')) {
            alert('Please enter a valid email address');
            return false;
        }
        
        if (!whatsapp.trim()) {
            alert('Please enter your WhatsApp number');
            return false;
        }
        
        return true;
    }
    
    async function submitSmOrder() {
        if (!validateSmForm()) return;
        
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-5 h-5 mr-2 spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Submitting...';
        
        const platform = document.getElementById('smPlatform').value;
        const customPlatform = document.getElementById('smCustomPlatform').value;
        const serviceType = document.querySelector('input[name="smServiceType"]:checked').value;
        const link = document.getElementById('smLink').value;
        const quantity = parseInt(document.getElementById('smQuantity').value);
        const fulfillQuantity = Math.ceil(quantity / SM_STAY_RATE);
        const email = document.getElementById('smEmail').value;
        const whatsapp = document.getElementById('smWhatsapp').value;
        
        const orderData = {
            orderType: 'social_media',
            platform: platform === 'Other' ? customPlatform : platform,
            customPlatform: platform === 'Other' ? customPlatform : '',
            serviceType: serviceType,
            smLink: link,
            quantity: quantity,
            fulfillQuantity: fulfillQuantity,
            stayRate: SM_STAY_RATE,
            email: email,
            telegram: document.getElementById('smTelegram').value.trim(),
            whatsapp: whatsapp,
            csrf_token: '<?php echo $csrfToken; ?>'
        };
        
        try {
            const response = await fetch('process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('smOrderIdDisplay').textContent = result.orderId;
                localStorage.removeItem('sbOrderForm');

                // Save order context for audit modal (used after agree)
                window.sbAuditCtx = {
                    orderId: result.orderId || '',
                    businessName: orderData.smLink || '',
                    email: orderData.email || '',
                    whatsapp: orderData.whatsapp || '',
                    orderType: 'social_media'
                };

                goToSmStep(4);

                // Confetti!
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
                }
            } else {
                alert(result.message || 'Error submitting order');
            }
        } catch (error) {
            console.error('Submit error:', error);
            alert('Error submitting order. Please try again.');
        }
        
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
    
    function goToStep(step) {
        document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
        document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
        
        document.querySelectorAll('.step-dot').forEach((dot, i) => {
            const dotStep = i + 1;
            dot.classList.remove('active', 'completed', 'inactive');
            if (dotStep < step) dot.classList.add('completed');
            else if (dotStep === step) dot.classList.add('active');
            else dot.classList.add('inactive');
        });
        
        document.querySelectorAll('.step-line').forEach((line, i) => {
            const lineStep = i + 1;
            line.classList.toggle('active', lineStep < step);
        });
        
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        if (step === 4) {
            generateBusinessCards();
        }
    }
    
    // ============================================================================
    // READY TO START MODAL
    // ============================================================================
    
    function showReadyModal() {
        document.getElementById('readyModal').classList.add('show');
    }
    
    function hideReadyModal() {
        document.getElementById('readyModal').classList.remove('show');
    }
    
    function confirmReadyAndStart() {
        hideReadyModal();
        goToStep(2);
    }
    
    function showNotReadyModal() {
        hideReadyModal();
        document.getElementById('notReadyModal').classList.add('show');
    }
    
    function hideNotReadyModal() {
        document.getElementById('notReadyModal').classList.remove('show');
    }
    
    function copyFormLink() {
        const input = document.getElementById('formLinkInput');
        const btn = document.getElementById('copyLinkBtn');
        const btnText = document.getElementById('copyBtnText');
        
        input.select();
        input.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(input.value).then(() => {
            btnText.textContent = 'Copied!';
            btn.classList.remove('bg-gray-900', 'hover:bg-gray-700');
            btn.classList.add('bg-green-500', 'hover:bg-green-600');
            
            setTimeout(() => {
                btnText.textContent = 'Copy';
                btn.classList.remove('bg-green-500', 'hover:bg-green-600');
                btn.classList.add('bg-gray-900', 'hover:bg-gray-700');
            }, 2000);
        });
    }
    
    // ============================================================================
    // TNC MODAL
    // ============================================================================
    
    function showTncConfirmModal() {
        document.getElementById('tncConfirmModal').classList.add('show');
    }
    
    function hideTncConfirmModal() {
        document.getElementById('tncConfirmModal').classList.remove('show');
    }
    
    function confirmTncAndContinue() {
        hideTncConfirmModal();
        goToStep(3);
    }
    
    // ============================================================================
    // VALIDATION
    // ============================================================================
    
    function validateEmail() {
        const email = document.getElementById('email');
        const error = document.getElementById('emailError');
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value);
        
        email.classList.toggle('error', !isValid && email.value);
        error.classList.toggle('show', !isValid && email.value);
        
        return isValid;
    }
    
    function validateQuantity() {
        const qty = document.getElementById('quantity');
        const error = document.getElementById('quantityError');
        const value = parseInt(qty.value) || 0;
        const isValid = value >= MIN_REVIEWS_PER_BUSINESS;
        
        qty.classList.toggle('error', !isValid && qty.value);
        error.classList.toggle('show', !isValid && qty.value);
        
        return isValid;
    }
    
    function handleProductTypeChange() {
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        const quantityField = document.getElementById('quantity');
        
        if (productType === 'Rating Only') {
            quantityField.removeAttribute('required');
        } else {
            quantityField.setAttribute('required', 'required');
        }
    }
    
    function updateAllocationPreview() {
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const numBusinesses = parseInt(document.querySelector('input[name="numBusinesses"]:checked')?.value) || 1;
        const preview = document.getElementById('allocationPreview');
        const cards = document.getElementById('allocationCards');
        const errorEl = document.getElementById('allocationError');
        
        if (quantity > 0 && numBusinesses > 0) {
            preview.classList.remove('hidden');
            
            const perBusiness = Math.floor(quantity / numBusinesses);
            const remainder = quantity % numBusinesses;
            
            // Initialize allocation array
            window.initialAllocation = [];
            for (let i = 0; i < numBusinesses; i++) {
                window.initialAllocation.push(perBusiness + (i < remainder ? 1 : 0));
            }
            
            let html = '';
            for (let i = 0; i < numBusinesses; i++) {
                const reviews = window.initialAllocation[i];
                const isValid = reviews >= MIN_REVIEWS_PER_BUSINESS;
                const isLast = i === numBusinesses - 1;
                
                html += `
                    <div class="allocation-card ${isValid ? 'valid' : 'error'}" id="allocCard${i}">
                        <p class="text-xs text-gray-500 mb-1">Business ${i + 1}</p>
                        ${isLast ? `
                            <p class="text-xl font-bold ${isValid ? 'text-green-600' : 'text-red-600'}" id="allocValue${i}">${reviews}</p>
                            <p class="text-xs text-gray-400">(auto)</p>
                        ` : `
                            <input type="number" class="w-full text-center text-xl font-bold border-0 bg-transparent ${isValid ? 'text-green-600' : 'text-red-600'}" 
                                   id="allocInput${i}" value="${reviews}" min="${MIN_REVIEWS_PER_BUSINESS}"
                                   onchange="recalculateAllocation()" oninput="recalculateAllocation()">
                            <p class="text-xs text-gray-500">reviews</p>
                        `}
                    </div>
                `;
            }
            cards.innerHTML = html;
            
            recalculateAllocation();
        } else {
            preview.classList.add('hidden');
        }
    }
    
    function recalculateAllocation() {
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const numBusinesses = parseInt(document.querySelector('input[name="numBusinesses"]:checked')?.value) || 1;
        const errorEl = document.getElementById('allocationError');
        
        if (numBusinesses <= 1) return;
        
        // Sum all inputs except last
        let allocated = 0;
        for (let i = 0; i < numBusinesses - 1; i++) {
            const input = document.getElementById(`allocInput${i}`);
            if (input) {
                allocated += parseInt(input.value) || 0;
            }
        }
        
        // Calculate remainder for last business
        const lastIndex = numBusinesses - 1;
        const lastValue = quantity - allocated;
        const lastEl = document.getElementById(`allocValue${lastIndex}`);
        const lastCard = document.getElementById(`allocCard${lastIndex}`);
        
        if (lastEl) {
            lastEl.textContent = lastValue;
            const isValid = lastValue >= MIN_REVIEWS_PER_BUSINESS;
            lastEl.className = `text-xl font-bold ${isValid ? 'text-green-600' : 'text-red-600'}`;
            lastCard.className = `allocation-card ${isValid ? 'valid' : 'error'}`;
        }
        
        // Update all card styles
        let hasError = false;
        for (let i = 0; i < numBusinesses - 1; i++) {
            const input = document.getElementById(`allocInput${i}`);
            const card = document.getElementById(`allocCard${i}`);
            if (input && card) {
                const val = parseInt(input.value) || 0;
                const isValid = val >= MIN_REVIEWS_PER_BUSINESS;
                if (!isValid) hasError = true;
                input.className = `w-full text-center text-xl font-bold border-0 bg-transparent ${isValid ? 'text-green-600' : 'text-red-600'}`;
                card.className = `allocation-card ${isValid ? 'valid' : 'error'}`;
            }
        }
        
        // Check last value
        if (lastValue < MIN_REVIEWS_PER_BUSINESS) hasError = true;
        
        // Show error message
        if (hasError || lastValue < 0) {
            if (lastValue < 0) {
                errorEl.textContent = `Over-allocated by ${Math.abs(lastValue)} reviews`;
            } else {
                errorEl.textContent = `Each business needs minimum ${MIN_REVIEWS_PER_BUSINESS} reviews`;
            }
            errorEl.classList.remove('hidden');
        } else {
            errorEl.classList.add('hidden');
        }
        
        // Store allocation for use in business cards
        window.currentAllocation = [];
        for (let i = 0; i < numBusinesses - 1; i++) {
            const input = document.getElementById(`allocInput${i}`);
            window.currentAllocation.push(parseInt(input?.value) || 0);
        }
        window.currentAllocation.push(lastValue);
    }
    
    function validateStep3() {
        const email = document.getElementById('email').value.trim();
        const whatsapp = document.getElementById('whatsapp').value.trim();
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const numBusinesses = parseInt(document.querySelector('input[name="numBusinesses"]:checked')?.value) || 1;
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        
        if (!email || !validateEmail()) {
            alert('Please enter a valid email address');
            document.getElementById('email').focus();
            return;
        }
        
        if (!whatsapp) {
            alert('Please enter your WhatsApp number');
            document.getElementById('whatsapp').focus();
            return;
        }
        
        if (productType === 'Rating & Review') {
            if (quantity < MIN_REVIEWS_PER_BUSINESS) {
                alert(`Please enter at least ${MIN_REVIEWS_PER_BUSINESS} reviews`);
                document.getElementById('quantity').focus();
                return;
            }
            
            const minRequired = numBusinesses * MIN_REVIEWS_PER_BUSINESS;
            if (quantity < minRequired) {
                alert(`You need at least ${minRequired} reviews for ${numBusinesses} businesses (${MIN_REVIEWS_PER_BUSINESS} per business)`);
                return;
            }
        }
        
        goToStep(4);
    }
    
    // ============================================================================
    // BUSINESS CARDS
    // ============================================================================
    
    function generateBusinessCards() {
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const numBusinesses = parseInt(document.querySelector('input[name="numBusinesses"]:checked')?.value) || 1;
        const container = document.getElementById('businessCardsContainer');
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        
        // Use allocation from preview if available
        let allocation = window.currentAllocation || [];
        if (allocation.length !== numBusinesses) {
            const perBusiness = Math.floor(quantity / numBusinesses);
            const remainder = quantity % numBusinesses;
            allocation = [];
            for (let i = 0; i < numBusinesses; i++) {
                allocation.push(perBusiness + (i < remainder ? 1 : 0));
            }
        }
        
        if (businessData.length !== numBusinesses) {
            businessData = [];
            for (let i = 0; i < numBusinesses; i++) {
                businessData.push({
                    reviews: allocation[i] || 0,
                    platform: 'Google',
                    customPlatform: '',
                    businessName: '',
                    businessLink: '',
                    businessType: '',
                    businessUSP: '',
                    reviewTone: 'Mixed',
                    reviewLength: 'Mixed',
                    businessDetails: '',
                    specificNames: '',
                    specificNamesPct: 'none',
                    keywords: '',
                    keywordFlex: 'Flexible',
                    exampleReviews: '',
                    additionalNotes: '',
                    currentRating: 0,
                    ratingOption: '5_star_only',
                    reviewerGender: 'mix',
                    reviewPhaseW1: '',
                    reviewPhaseW2: '',
                    reviewPhaseW3: ''
                });
            }
        } else {
            // Update reviews from allocation
            for (let i = 0; i < numBusinesses; i++) {
                businessData[i].reviews = allocation[i] || businessData[i].reviews;
            }
        }
        
        let html = '';
        for (let i = 0; i < numBusinesses; i++) {
            const data = businessData[i];
            const isRatingOnly = productType === 'Rating Only';
            
            html += `
                <div class="business-card" data-index="${i}">
                    <div class="business-card-header">
                        <div class="flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">${i + 1}</span>
                            <div>
                                <h3 class="font-semibold text-gray-900">Business ${i + 1}</h3>
                                <p class="text-sm text-gray-500" id="cardSubtitle${i}">${data.businessName || 'Not configured'}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-semibold text-green-600" id="cardReviews${i}">${data.reviews} reviews</span>
                            <button type="button" onclick="toggleBusinessCard(${i})" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5 transform transition-transform" id="cardChevron${i}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="business-card-body" id="cardBody${i}">
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="sb-label">Platform <span>*</span></label>
                                    <p class="text-xs text-gray-500 mb-2">Where do you want us to post reviews?</p>
                                    <div class="sb-radio-group" id="platformGroup${i}">
                                        ${PLATFORMS.map(p => `
                                            <input type="radio" id="platform${i}_${p.replace(/\s+/g, '')}" name="platform${i}" value="${p}" class="sb-radio" ${data.platform === p ? 'checked' : ''} onchange="handlePlatformChange(${i}, '${p}')">
                                            <label for="platform${i}_${p.replace(/\s+/g, '')}" class="sb-radio-label">${p}</label>
                                        `).join('')}
                                    </div>
                                    <div id="customPlatformWrap${i}" class="${data.platform === 'Other' ? '' : 'hidden'} mt-3">
                                        <input type="text" class="sb-input" id="customPlatform${i}" value="${escapeHtml(data.customPlatform || '')}" placeholder="Enter platform name (e.g., productreview.com.au)" onchange="updateBusinessData(${i}, 'customPlatform', this.value); updatePlatformTexts(${i}, 'Other', this.value)" oninput="updatePlatformTexts(${i}, 'Other', this.value)">
                                    </div>
                                </div>
                                <div>
                                    <label class="sb-label">Reviews for This Business <span>*</span></label>
                                    <p class="text-xs text-gray-500 mb-2">Number of reviews allocated to this business.</p>
                                    <input type="number" class="sb-input" id="reviews${i}" value="${data.reviews}" min="${MIN_REVIEWS_PER_BUSINESS}" onchange="updateBusinessReviews(${i}, this.value)">
                                    <p class="helper-text">Min ${MIN_REVIEWS_PER_BUSINESS} reviews</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="sb-label">Rating Option <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Choose the star rating distribution for your reviews.</p>
                                <div class="sb-radio-group">
                                    <input type="radio" id="ratingOption${i}_5star" name="ratingOption${i}" value="5_star_only" class="sb-radio" ${data.ratingOption === '5_star_only' || !data.ratingOption ? 'checked' : ''} onchange="updateBusinessData(${i}, 'ratingOption', '5_star_only')">
                                    <label for="ratingOption${i}_5star" class="sb-radio-label">5 Stars Only</label>

                                    <input type="radio" id="ratingOption${i}_mix" name="ratingOption${i}" value="mix_80_20" class="sb-radio" ${data.ratingOption === 'mix_80_20' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'ratingOption', 'mix_80_20')">
                                    <label for="ratingOption${i}_mix" class="sb-radio-label">Mix (80% 5-Star + 20% 4-Star)</label>
                                </div>
                            </div>

                            <div>
                                <label class="sb-label">Reviewer Gender <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Choose the gender distribution of the reviewer profiles.</p>
                                <div class="sb-radio-group">
                                    <input type="radio" id="reviewerGender${i}_mix" name="reviewerGender${i}" value="mix" class="sb-radio" ${!data.reviewerGender || data.reviewerGender === 'mix' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewerGender', 'mix')">
                                    <label for="reviewerGender${i}_mix" class="sb-radio-label">Mix Gender</label>

                                    <input type="radio" id="reviewerGender${i}_male" name="reviewerGender${i}" value="male" class="sb-radio" ${data.reviewerGender === 'male' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewerGender', 'male')">
                                    <label for="reviewerGender${i}_male" class="sb-radio-label">Male Only</label>

                                    <input type="radio" id="reviewerGender${i}_female" name="reviewerGender${i}" value="female" class="sb-radio" ${data.reviewerGender === 'female' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewerGender', 'female')">
                                    <label for="reviewerGender${i}_female" class="sb-radio-label">Female Only</label>
                                </div>
                            </div>

                            <div>
                                <label class="sb-label">Business Name <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2" id="businessNameDesc${i}">${getPlatformBusinessNameDesc(data.platform, data.customPlatform)}</p>
                                <input type="text" class="sb-input" id="businessName${i}" value="${escapeHtml(data.businessName)}" placeholder="Your business name" onchange="updateBusinessData(${i}, 'businessName', this.value)">
                            </div>
                            
                            <div>
                                <label class="sb-label">Current Rating Quantity</label>
                                <p class="text-xs text-gray-500 mb-2">How many reviews does your business currently have? Enter 0 if this is a new business.</p>
                                <input type="number" class="sb-input" id="currentRating${i}" value="${data.currentRating || ''}" placeholder="e.g., 45" min="0" onchange="updateBusinessData(${i}, 'currentRating', this.value)">
                            </div>
                            
                            <div id="instructionWrap${i}" class="bg-gray-50 rounded-xl p-4 ${data.platform !== 'Google' ? 'hidden' : ''}">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    How to Get Your Google Business Link
                                </h4>
                                <img src="https://lh7-rt.googleusercontent.com/formsz/AN7BsVAlVM29vQGLYFkqXA2T34_i18MU18KMOd16CpzfYnL9nfA_06Paa7LBwmv2BwL_j3xaC3m7MhygYIDiPimm4DjKAUQ4N-UEH2hAaJ6fZiCsEBl1E99uleWruYqiBl76EgreZUf6DYF_dK3ZIYblc5S8_B7AlB-hjfAaOZ5yK2XUm3aZ6bri8UfvScu3UtNkqenO4jCaDi8m46kg=w1280?key=nTkNe0oW2hbeELGUwWXRag" alt="Instructions" class="instruction-img w-full" onclick="showImageModal(this.src)">
                                <p class="text-xs text-gray-500 mt-2">Click image to enlarge</p>
                            </div>
                            
                            <div>
                                <label class="sb-label">Business Link <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2" id="businessLinkDesc${i}">${getPlatformLinkDesc(data.platform, data.customPlatform)}</p>
                                <input type="text" class="sb-input" id="businessLink${i}" value="${escapeHtml(data.businessLink)}" placeholder="${getPlatformLinkPlaceholder(data.platform, data.customPlatform)}" onchange="updateBusinessData(${i}, 'businessLink', this.value)">
                            </div>
                            
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <label class="sb-label" style="margin-bottom:0">Current Review Activity</label>
                                    <button type="button" class="ml-2 w-5 h-5 rounded-full bg-blue-200 text-blue-700 text-xs font-bold flex items-center justify-center flex-shrink-0 hover:bg-blue-300 transition-colors" onclick="document.getElementById('phaseInfoTip${i}').classList.toggle('hidden')" title="Why we need this">i</button>
                                </div>
                                <div id="phaseInfoTip${i}" class="hidden mb-3 text-xs text-blue-700 bg-blue-100 rounded-lg p-3 leading-relaxed">
                                    We need this information to match your business's natural review pattern. By aligning our posting pace with your existing review flow, it looks more organic to Google's algorithm and keeps your profile safe.
                                </div>
                                <p class="text-xs text-gray-600 mb-4">Tell us your current pace so we can mirror it naturally.</p>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-1 block">On average, how many new Google reviews does your business receive per week right now?</label>
                                    <select class="sb-input" id="reviewPhaseW1_${i}" onchange="updateBusinessData(${i}, 'reviewPhaseW1', this.value); mirrorWeeklyPace(${i}, this.value);">
                                        <option value="" ${!data.reviewPhaseW1 ? 'selected' : ''}>Select...</option>
                                        <option value="0-1 reviews/week" ${data.reviewPhaseW1 === '0-1 reviews/week' ? 'selected' : ''}>0-1 reviews/week (rarely get reviews)</option>
                                        <option value="1-3 reviews/week" ${data.reviewPhaseW1 === '1-3 reviews/week' ? 'selected' : ''}>1-3 reviews/week</option>
                                        <option value="4-7 reviews/week" ${data.reviewPhaseW1 === '4-7 reviews/week' ? 'selected' : ''}>4-7 reviews/week</option>
                                        <option value="8-15 reviews/week" ${data.reviewPhaseW1 === '8-15 reviews/week' ? 'selected' : ''}>8-15 reviews/week</option>
                                        <option value="15+ reviews/week" ${data.reviewPhaseW1 === '15+ reviews/week' ? 'selected' : ''}>15+ reviews/week</option>
                                        <option value="Not sure / Let Smart Buzzer decide" ${data.reviewPhaseW1 === 'Not sure / Let Smart Buzzer decide' ? 'selected' : ''}>Not sure / Let Smart Buzzer decide</option>
                                    </select>
                                </div>

                                <div class="mt-4 flex items-start gap-2 text-xs text-green-800 bg-green-50 border border-green-200 rounded-lg p-3 leading-relaxed">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>We'll mirror your existing review pattern so our posting looks natural and keeps your profile safe.</span>
                                </div>
                            </div>
                            
                            ${!isRatingOnly ? `
                            <div>
                                <label class="sb-label">What is your business type or industry? <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Example: Coffee Shop, Restaurant, Laundry Service, Workshop, Hospital, Beauty Clinic, Dental Clinic, Law Firm, etc.</p>
                                <input type="text" class="sb-input" id="businessType${i}" value="${escapeHtml(data.businessType)}" placeholder="Your answer" onchange="updateBusinessData(${i}, 'businessType', this.value)" required>
                            </div>
                            
                            <div>
                                <label class="sb-label">Tell us what makes your business unique <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Mention your top services, special offers, or what customers love about you. This helps us write authentic reviews.</p>
                                <textarea class="sb-textarea" id="businessUSP${i}" placeholder="Your answer" onchange="updateBusinessData(${i}, 'businessUSP', this.value)" required>${escapeHtml(data.businessUSP)}</textarea>
                            </div>
                            
                            <div>
                                <label class="sb-label">What tone would you like us to use for the reviews? <span>*</span></label>
                                <div class="sb-radio-group">
                                    <input type="radio" id="reviewTone${i}_Casual" name="reviewTone${i}" value="Casual" class="sb-radio" ${data.reviewTone === 'Casual' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewTone', 'Casual')">
                                    <label for="reviewTone${i}_Casual" class="sb-radio-label">Casual / Relaxed</label>
                                    
                                    <input type="radio" id="reviewTone${i}_Formal" name="reviewTone${i}" value="Formal" class="sb-radio" ${data.reviewTone === 'Formal' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewTone', 'Formal')">
                                    <label for="reviewTone${i}_Formal" class="sb-radio-label">Formal / Professional</label>
                                    
                                    <input type="radio" id="reviewTone${i}_Mixed" name="reviewTone${i}" value="Mixed" class="sb-radio" ${data.reviewTone === 'Mixed' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewTone', 'Mixed')">
                                    <label for="reviewTone${i}_Mixed" class="sb-radio-label">Mixed</label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="sb-label">What length would you prefer for the reviews? <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Choose how you'd like the reviews to be written in terms of word count.</p>
                                <div class="sb-radio-group">
                                    <input type="radio" id="reviewLength${i}_Mixed" name="reviewLength${i}" value="Mixed" class="sb-radio" ${data.reviewLength === 'Mixed' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewLength', 'Mixed')">
                                    <label for="reviewLength${i}_Mixed" class="sb-radio-label">Mixed (50% Short, 50% Long) - Natural</label>
                                    
                                    <input type="radio" id="reviewLength${i}_Long" name="reviewLength${i}" value="Long" class="sb-radio" ${data.reviewLength === 'Long' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewLength', 'Long')">
                                    <label for="reviewLength${i}_Long" class="sb-radio-label">Long Only (20+ words per review)</label>
                                    
                                    <input type="radio" id="reviewLength${i}_Short" name="reviewLength${i}" value="Short" class="sb-radio" ${data.reviewLength === 'Short' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'reviewLength', 'Short')">
                                    <label for="reviewLength${i}_Short" class="sb-radio-label">Short Only (~9-10 words per review)</label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="sb-label">Tell us more about your business details <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">We use this info to create <strong>realistic, detailed reviews</strong>. Please provide anything applicable:</p>
                                <ul class="text-xs text-gray-500 mb-3 ml-4 list-disc">
                                    <li>Menu items (if F&B)</li>
                                    <li>Ambience (e.g. cozy, modern, family-friendly)</li>
                                    <li>Price range or value-for-money notes</li>
                                    <li>Estimated service time</li>
                                    <li>Any signature service or special treatment</li>
                                    <li>Additional info customers usually love</li>
                                </ul>
                                <textarea class="sb-textarea" id="businessDetails${i}" placeholder="Your answer" onchange="updateBusinessData(${i}, 'businessDetails', this.value)" required>${escapeHtml(data.businessDetails)}</textarea>
                            </div>
                            
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                                <div class="flex items-start gap-3 mb-3">
                                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div>
                                        <h4 class="text-sm font-semibold text-amber-900 mb-1">Why we no longer name specific staff in reviews</h4>
                                    </div>
                                </div>
                                <p class="text-sm text-amber-900 leading-relaxed mb-2">
                                    Google's latest algorithm flags reviews that mention specific staff names &mdash; these reviews often get filtered out and never appear publicly.
                                </p>
                                <p class="text-sm text-amber-900 leading-relaxed mb-3">
                                    To protect your review survival rate, we now write authentic reviews that focus on your service quality, customer experience, and what makes your business unique &mdash; without naming individual team members.
                                </p>
                                <ul class="space-y-1.5 text-sm text-amber-900">
                                    <li class="flex items-start gap-2"><span class="text-amber-700 font-bold flex-shrink-0">&check;</span><span>Reviews still feel personal and genuine</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-700 font-bold flex-shrink-0">&check;</span><span>Higher show-up rate on Google</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-700 font-bold flex-shrink-0">&check;</span><span>Safer from Google's review filters</span></li>
                                </ul>
                            </div>
                            
                            <div>
                                <label class="sb-label">Provide Keywords You Want to Include in the Google Maps Reviews <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Please give us minimum <strong>15-20 Keywords & DETAILED</strong>.</p>
                                <p class="text-xs text-gray-500 mb-2">Examples:</p>
                                <ul class="text-xs text-gray-500 mb-3 ml-4 list-disc">
                                    <li>Clean Place</li>
                                    <li>Friendly & Satisfying Service</li>
                                    <li>Easily Accessible Location</li>
                                    <li>Customer Service is Very Helpful</li>
                                    <li>Very Satisfying Products</li>
                                    <li>Professional Staff</li>
                                    <li>Great Value for Money</li>
                                </ul>
                                <textarea class="sb-textarea" id="keywords${i}" placeholder="Enter keywords separated by commas or new lines..." onchange="updateBusinessData(${i}, 'keywords', this.value)" required>${escapeHtml(data.keywords)}</textarea>
                            </div>
                            
                            <div>
                                <label class="sb-label">Are you okay with us adding some additional related keywords? <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Or would you prefer that we stick strictly to the ones you provided?</p>
                                <div class="flex flex-col gap-2">
                                    <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:border-green-400 transition-all ${data.keywordFlex === 'Flexible' ? 'bg-green-50 border-green-400' : ''}" id="keywordFlexLabel${i}_Flexible">
                                        <input type="radio" id="keywordFlex${i}_Flexible" name="keywordFlex${i}" value="Flexible" class="w-4 h-4 text-green-600" ${data.keywordFlex === 'Flexible' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'keywordFlex', 'Flexible'); updateKeywordFlexStyle(${i})">
                                        <span class="ml-3 text-sm font-medium text-gray-700">Yes, Smart Buzzer can add more related keywords</span>
                                    </label>
                                    <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:border-green-400 transition-all ${data.keywordFlex === 'Strict' ? 'bg-green-50 border-green-400' : ''}" id="keywordFlexLabel${i}_Strict">
                                        <input type="radio" id="keywordFlex${i}_Strict" name="keywordFlex${i}" value="Strict" class="w-4 h-4 text-green-600" ${data.keywordFlex === 'Strict' ? 'checked' : ''} onchange="updateBusinessData(${i}, 'keywordFlex', 'Strict'); updateKeywordFlexStyle(${i})">
                                        <span class="ml-3 text-sm font-medium text-gray-700">No, stick to the owner's business keywords</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="sb-label">Enter Examples of Reviews You Expect <span>*</span></label>
                                <p class="text-xs text-gray-500 mb-2"><strong>(PLEASE BE DETAILED - MAXIMUM 18 WORDS PER EXAMPLE)</strong></p>
                                <p class="text-xs text-gray-500 mb-2">Examples:</p>
                                <ul class="text-xs text-gray-500 mb-3 ml-4 list-disc">
                                    <li>Trust your laundry to this shop - the results are always spotless, and the fragrance lasts a long time!</li>
                                    <li>This laundry shop is truly a champion when it comes to premium single-item care. My clothes always come back clean and fresh!</li>
                                </ul>
                                <p class="text-xs text-gray-500 mb-2">(Maximum: 5 examples)</p>
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-amber-800"><strong>*IMPORTANT:</strong> THESE ARE JUST EXAMPLES FOR OUR REFERENCE. NOT ALL LONGER REVIEWS WILL APPEAR PUBLICLY. REVIEWS ARE DISPLAYED BASED ON GOOGLE'S ALGORITHM, WHICH MIXES LONGER, STANDARD, AND SHORTER REVIEWS. HOWEVER, WE STRIVE TO GENERATE HIGH-QUALITY CONTENT FOR MAXIMUM VISIBILITY.</p>
                                </div>
                                <textarea class="sb-textarea" id="exampleReviews${i}" placeholder="Your answer" onchange="updateBusinessData(${i}, 'exampleReviews', this.value)" required>${escapeHtml(data.exampleReviews)}</textarea>
                            </div>
                            
                            <div>
                                <label class="sb-label">Additional Notes (Optional)</label>
                                <p class="text-xs text-gray-500 mb-2">Any other instructions, special requests, or things you want us to know about your business.</p>
                                <textarea class="sb-textarea" id="additionalNotes${i}" placeholder="Your answer" onchange="updateBusinessData(${i}, 'additionalNotes', this.value)">${escapeHtml(data.additionalNotes)}</textarea>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        
        container.innerHTML = html;
        updateReviewSummary();
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function toggleBusinessCard(index) {
        const body = document.getElementById(`cardBody${index}`);
        const chevron = document.getElementById(`cardChevron${index}`);
        const card = body.closest('.business-card');
        
        card.classList.toggle('business-card-collapsed');
        chevron.style.transform = card.classList.contains('business-card-collapsed') ? 'rotate(-90deg)' : '';
    }
    
    function updateInstructionVisibility(index) {
        const platformRadio = document.querySelector(`input[name="platform${index}"]:checked`);
        const platform = platformRadio ? platformRadio.value : 'Google';
        const instructionWrap = document.getElementById(`instructionWrap${index}`);
        if (instructionWrap) {
            instructionWrap.classList.toggle('hidden', platform !== 'Google');
        }
    }
    
    function handlePlatformChange(index, platform) {
        updateBusinessData(index, 'platform', platform);
        updateInstructionVisibility(index);
        
        const customWrap = document.getElementById(`customPlatformWrap${index}`);
        if (customWrap) {
            if (platform === 'Other') {
                customWrap.classList.remove('hidden');
                const customInput = document.getElementById(`customPlatform${index}`);
                if (customInput) customInput.focus();
            } else {
                customWrap.classList.add('hidden');
                updateBusinessData(index, 'customPlatform', '');
            }
        }
        
        // Update dynamic text descriptions
        updatePlatformTexts(index, platform, '');
    }
    
    function updatePlatformTexts(index, platform, customPlatform) {
        const displayPlatform = (platform === 'Other' && customPlatform) ? customPlatform : platform;
        
        // Update Business Name description
        const nameDesc = document.getElementById(`businessNameDesc${index}`);
        if (nameDesc) {
            nameDesc.textContent = getPlatformBusinessNameDesc(platform, customPlatform);
        }
        
        // Update Business Link description
        const linkDesc = document.getElementById(`businessLinkDesc${index}`);
        if (linkDesc) {
            linkDesc.textContent = getPlatformLinkDesc(platform, customPlatform);
        }
        
        // Update Business Link placeholder
        const linkInput = document.getElementById(`businessLink${index}`);
        if (linkInput) {
            linkInput.placeholder = getPlatformLinkPlaceholder(platform, customPlatform);
        }
    }
    
    function getPlatformBusinessNameDesc(platform, customPlatform) {
        const platformNames = {
            'Google': 'Google Maps',
            'Yelp': 'Yelp',
            'Facebook': 'Facebook',
            'Trustpilot': 'Trustpilot',
            'Other': customPlatform || 'the platform'
        };
        const name = platformNames[platform] || platform;
        return `Enter your business name exactly as it appears on ${name}.`;
    }
    
    function getPlatformLinkDesc(platform, customPlatform) {
        if (platform === 'Google') {
            return 'Paste any Google Maps link \u2014 we accept all formats (maps.google.com, share.google, maps.app.goo.gl, g.page, etc.).';
        }
        const platformNames = {
            'Yelp': 'Yelp',
            'Facebook': 'Facebook',
            'Trustpilot': 'Trustpilot',
            'Other': customPlatform || 'the platform'
        };
        const name = platformNames[platform] || platform;
        return `Paste the full URL to your business profile on ${name}.`;
    }

    function getPlatformLinkPlaceholder(platform, customPlatform) {
        const placeholders = {
            'Google': 'https://share.google/... or https://maps.app.goo.gl/...',
            'Yelp': 'https://yelp.com/biz/...',
            'Facebook': 'https://facebook.com/...',
            'Trustpilot': 'https://trustpilot.com/review/...',
            'Other': customPlatform ? `https://${customPlatform.toLowerCase().replace(/\s+/g, '')}/...` : 'https://...'
        };
        return placeholders[platform] || 'https://...';
    }
    
    function updateKeywordFlexStyle(index) {
        const flexibleLabel = document.getElementById(`keywordFlexLabel${index}_Flexible`);
        const strictLabel = document.getElementById(`keywordFlexLabel${index}_Strict`);
        const flexibleRadio = document.getElementById(`keywordFlex${index}_Flexible`);
        
        if (flexibleRadio && flexibleRadio.checked) {
            flexibleLabel.classList.add('bg-green-50', 'border-green-400');
            flexibleLabel.classList.remove('bg-gray-50', 'border-gray-200');
            strictLabel.classList.remove('bg-green-50', 'border-green-400');
            strictLabel.classList.add('bg-gray-50', 'border-gray-200');
        } else {
            strictLabel.classList.add('bg-green-50', 'border-green-400');
            strictLabel.classList.remove('bg-gray-50', 'border-gray-200');
            flexibleLabel.classList.remove('bg-green-50', 'border-green-400');
            flexibleLabel.classList.add('bg-gray-50', 'border-gray-200');
        }
    }
    
    function updateBusinessData(index, field, value) {
        businessData[index][field] = value;
        
        if (field === 'businessName') {
            const subtitle = document.getElementById(`cardSubtitle${index}`);
            if (subtitle) subtitle.textContent = value || 'Not configured';
        }
        
        saveFormData();
    }
    
    // Mirror weekly pace to W2/W3 fields for backend compatibility
    function mirrorWeeklyPace(bizIdx, value) {
        businessData[bizIdx].reviewPhaseW1 = value;
        businessData[bizIdx].reviewPhaseW2 = value;
        businessData[bizIdx].reviewPhaseW3 = value;
        saveFormData();
    }
    
    function updateBusinessReviews(index, value) {
        const reviews = parseInt(value) || MIN_REVIEWS_PER_BUSINESS;
        businessData[index].reviews = Math.max(MIN_REVIEWS_PER_BUSINESS, reviews);
        
        const cardReviews = document.getElementById(`cardReviews${index}`);
        if (cardReviews) cardReviews.textContent = `${businessData[index].reviews} reviews`;
        
        updateReviewSummary();
        saveFormData();
    }
    
    function updateReviewSummary() {
        const totalOrdered = parseInt(document.getElementById('quantity').value) || 0;
        const totalAllocated = businessData.reduce((sum, b) => sum + (b.reviews || 0), 0);
        
        const summaryEl = document.getElementById('totalAllocated');
        const statusEl = document.getElementById('allocationStatus');
        
        summaryEl.textContent = `${totalAllocated} / ${totalOrdered}`;
        
        if (totalAllocated === totalOrdered) {
            summaryEl.classList.remove('text-red-600');
            summaryEl.classList.add('text-green-600');
            statusEl.textContent = 'Allocation complete';
            statusEl.classList.remove('text-red-500');
            statusEl.classList.add('text-green-500');
        } else if (totalAllocated > totalOrdered) {
            summaryEl.classList.remove('text-green-600');
            summaryEl.classList.add('text-red-600');
            statusEl.textContent = `${totalAllocated - totalOrdered} reviews over-allocated`;
            statusEl.classList.remove('text-green-500');
            statusEl.classList.add('text-red-500');
        } else {
            summaryEl.classList.remove('text-green-600');
            summaryEl.classList.add('text-red-600');
            statusEl.textContent = `${totalOrdered - totalAllocated} reviews remaining to allocate`;
            statusEl.classList.remove('text-green-500');
            statusEl.classList.add('text-red-500');
        }
    }
    
    // ============================================================================
    // TIMELINE CALCULATION
    // ============================================================================
    
    function calculateTimeline(quantity, numBusinesses) {
        const reviewsPerWeek = numBusinesses * 2;
        const weeksNeeded = Math.ceil(quantity / reviewsPerWeek);
        
        const startDate = new Date();
        startDate.setDate(startDate.getDate() + 3);
        
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + (weeksNeeded * 7));
        
        const months = Math.ceil(weeksNeeded / 4);
        let durationText = '';
        if (months <= 1) {
            durationText = `${weeksNeeded} weeks`;
        } else if (months <= 2) {
            durationText = '1-2 months';
        } else if (months <= 3) {
            durationText = '2-3 months';
        } else {
            durationText = `${months} months`;
        }
        
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        
        return {
            startDate: startDate.toLocaleDateString('en-US', options),
            endDate: endDate.toLocaleDateString('en-US', options),
            durationText: durationText,
            weeksNeeded: weeksNeeded
        };
    }
    
    // ============================================================================
    // PREVIEW
    // ============================================================================
    
    function goToPreview() {
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        const totalOrdered = parseInt(document.getElementById('quantity').value) || 0;
        const totalAllocated = businessData.reduce((sum, b) => sum + (b.reviews || 0), 0);
        
        if (totalAllocated !== totalOrdered) {
            alert(`Please allocate exactly ${totalOrdered} reviews. Currently allocated: ${totalAllocated}`);
            return;
        }
        
        for (let i = 0; i < businessData.length; i++) {
            const b = businessData[i];
            
            if (!b.businessName) {
                alert(`Please enter business name for Business ${i + 1}`);
                return;
            }
            
            if (!b.businessLink) {
                alert(`Please enter business link for Business ${i + 1}`);
                return;
            }

            // Validate URL format only (platform-agnostic — Google / Yelp / FB / Trustpilot / Other all accepted)
            const link = b.businessLink.trim();
            let urlOk = false;
            try { new URL(link); urlOk = true; } catch (e) { urlOk = false; }
            if (!urlOk) {
                alert(`Invalid link for Business ${i + 1}. Please paste a valid URL starting with http:// or https://`);
                document.getElementById('businessLink' + i)?.focus();
                return;
            }

            if (productType === 'Rating & Review') {
                if (!b.businessType) {
                    alert(`Please enter business type for Business ${i + 1}`);
                    return;
                }
                if (!b.businessUSP) {
                    alert(`Please enter what makes your business unique for Business ${i + 1}`);
                    return;
                }
                if (!b.reviewTone) {
                    alert(`Please select review tone for Business ${i + 1}`);
                    return;
                }
                if (!b.reviewLength) {
                    alert(`Please select review length for Business ${i + 1}`);
                    return;
                }
                if (!b.businessDetails) {
                    alert(`Please enter business details for Business ${i + 1}`);
                    return;
                }
                if (!b.keywords) {
                    alert(`Please enter keywords for Business ${i + 1}`);
                    return;
                }
                if (!b.keywordFlex) {
                    alert(`Please select keyword flexibility option for Business ${i + 1}`);
                    return;
                }
                if (!b.exampleReviews) {
                    alert(`Please enter example reviews for Business ${i + 1}`);
                    return;
                }
            }
        }
        
        populatePreview();
        goToStep(5);
    }
    
    function populatePreview() {
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        const email = document.getElementById('email').value;
        const paymentEmail = document.getElementById('paymentEmail').value;
        const telegram = document.getElementById('telegram').value;
        const whatsapp = document.getElementById('whatsapp').value;
        const businessLocation = document.getElementById('businessLocation').value;
        const sbCountry = document.getElementById('sbCountry').value;
        const sbState = document.getElementById('sbState').value;
        const sbIndustry = document.getElementById('sbIndustry').value;
        const totalReviews = parseInt(document.getElementById('quantity').value) || 0;
        const numBusinesses = parseInt(document.querySelector('input[name="numBusinesses"]:checked')?.value) || 1;
        
        // Calculate timeline
        const timeline = calculateTimeline(totalReviews, numBusinesses);
        document.getElementById('preview_startDate').textContent = timeline.startDate;
        document.getElementById('preview_endDate').textContent = timeline.endDate;
        document.getElementById('preview_duration').textContent = timeline.durationText;
        document.getElementById('confirm_duration').textContent = timeline.durationText;
        document.getElementById('confirm_endDate').textContent = timeline.endDate;
        
        let html = `
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">${escapeHtml(email)}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">WhatsApp</p>
                            <p class="font-medium text-gray-900">${escapeHtml(whatsapp)}</p>
                        </div>
                        ${paymentEmail ? `<div class="col-span-2">
                            <p class="text-sm text-gray-500">Payment Email (Fanbasis/Tazapay)</p>
                            <p class="font-medium text-gray-900">${escapeHtml(paymentEmail)}</p>
                        </div>` : ''}
                        ${telegram ? `<div>
                            <p class="text-sm text-gray-500">Telegram</p>
                            <p class="font-medium text-gray-900">${escapeHtml(telegram)}</p>
                        </div>` : ''}
                        <div>
                            <p class="text-sm text-gray-500">Business Address</p>
                            <p class="font-medium text-gray-900">${escapeHtml(businessLocation) || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Country</p>
                            <p class="font-medium text-gray-900">${escapeHtml(sbCountry) || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">State / Region</p>
                            <p class="font-medium text-gray-900">${escapeHtml(sbState) || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Business Industry</p>
                            <p class="font-medium text-gray-900">${escapeHtml(sbIndustry) || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Product Type</p>
                            <p class="font-medium text-gray-900">${productType}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Reviews</p>
                            <p class="font-medium text-gray-900">${totalReviews}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        businessData.forEach((b, i) => {
            const currentRating = parseInt(b.currentRating) || 0;
            const targetRating = currentRating + b.reviews;
            const displayPlatform = b.platform === 'Other' ? (b.customPlatform || 'Other') : b.platform;
            const ratingOptionDisplay = b.ratingOption === 'mix_80_20' ? 'Mix (80% 5-Star + 20% 4-Star)' : '5 Stars Only';
            
            html += `
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mb-4">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-blue-900">Business ${i + 1}: ${escapeHtml(b.businessName)}</h3>
                            <span class="text-sm font-medium text-blue-600">${b.reviews} reviews on ${escapeHtml(displayPlatform)}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Current Rating</p>
                                <p class="font-medium text-gray-900">${currentRating > 0 ? currentRating : 'New'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Target Rating Total</p>
                                <p class="font-medium text-green-600">${targetRating}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Rating Option</p>
                                <p class="font-medium text-gray-900">${ratingOptionDisplay}</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Business Link</p>
                            <p class="font-medium text-blue-600 break-all text-sm">${escapeHtml(b.businessLink)}</p>
                        </div>
                        ${b.reviewPhaseW1 ? `
                        <div class="mb-4 bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-blue-500">Current Weekly Review Pace</p>
                            <p class="text-sm font-medium text-blue-900">${escapeHtml(b.reviewPhaseW1)}</p>
                        </div>
                        ` : ''}
                        ${productType === 'Rating & Review' ? `
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Business Type</p>
                                <p class="font-medium text-gray-900">${escapeHtml(b.businessType)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Review Tone</p>
                                <p class="font-medium text-gray-900">${b.reviewTone}</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Keywords</p>
                            <p class="font-medium text-gray-900 text-sm">${escapeHtml(b.keywords)}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('previewContent').innerHTML = html;
    }
    
    // ============================================================================
    // CONFIRM MODAL
    // ============================================================================
    
    function showConfirmModal() {
        document.querySelectorAll('.confirm-checkbox').forEach(cb => cb.checked = false);
        updateConfirmButton();
        
        // Update dynamic timeline text
        const totalReviews = businessData.reduce((sum, b) => sum + (b.reviews || 0), 0);
        const numBusinesses = businessData.length || 1;
        const timeline = calculateTimeline(totalReviews, numBusinesses);
        document.getElementById('confirmModal').classList.add('show');
    }
    
    function hideConfirmModal() {
        document.getElementById('confirmModal').classList.remove('show');
    }
    
    function updateConfirmButton() {
        const checkboxes = document.querySelectorAll('.confirm-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        document.getElementById('finalSubmitBtn').disabled = !allChecked;
    }
    
    // ============================================================================
    // FORM SUBMISSION
    // ============================================================================
    
    async function submitOrder() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';
        
        const submitBtn = document.getElementById('finalSubmitBtn');
        submitBtn.disabled = true;
        
        const productType = document.querySelector('input[name="productType"]:checked')?.value;
        
        const formData = {
            csrf_token: document.getElementById('csrf_token').value,
            referrer_url: document.getElementById('referrer_url').value || 'Direct',
            tracking_source: document.getElementById('tracking_source').value || '',
            email: document.getElementById('email').value.trim(),
            paymentEmail: document.getElementById('paymentEmail').value.trim(),
            telegram: document.getElementById('telegram').value.trim(),
            whatsapp: document.getElementById('whatsapp').value.trim(),
            businessLocation: document.getElementById('businessLocation').value.trim(),
            country: document.getElementById('sbCountry').value.trim(),
            state: document.getElementById('sbState').value.trim(),
            businessIndustry: document.getElementById('sbIndustry').value.trim(),
            productType: productType,
            ratingOption: businessData[0]?.ratingOption || '5_star_only',
            ratingMix: businessData[0]?.ratingOption === 'mix_80_20' ? 'Mix (80% 5-Star + 20% 4-Star)' : '5 Stars Only',
            reviewerGender: businessData[0]?.reviewerGender || 'mix',
            quantity: document.getElementById('quantity').value,
            numBusinesses: document.querySelector('input[name="numBusinesses"]:checked')?.value || '1',
            businesses: businessData,
            businessNames: businessData.map(b => b.businessName).join(', '),
            mapsLink: businessData[0]?.businessLink || '',
            platform: businessData[0]?.platform || 'Google',
            customPlatform: businessData[0]?.customPlatform || '',
            businessType: businessData[0]?.businessType || 'N/A',
            businessUSP: businessData[0]?.businessUSP || 'N/A',
            reviewTone: businessData[0]?.reviewTone || 'Mixed',
            reviewLength: businessData[0]?.reviewLength || 'Mixed',
            businessDetails: businessData[0]?.businessDetails || 'N/A',
            specificNames: businessData[0]?.specificNames || '',
            specificNamesPct: businessData[0]?.specificNamesPct || 'none',
            keywords: businessData[0]?.keywords || 'N/A',
            keywordFlex: businessData[0]?.keywordFlex || 'Flexible',
            exampleReviews: businessData[0]?.exampleReviews || '',
            additionalNotes: businessData[0]?.additionalNotes || '',
            currentRating: businessData[0]?.currentRating || '0',
            targetRatingTotal: (parseInt(businessData[0]?.currentRating) || 0) + (businessData[0]?.reviews || 0),
            reviewPhaseW1: businessData[0]?.reviewPhaseW1 || '',
            reviewPhaseW2: businessData[0]?.reviewPhaseW2 || '',
            reviewPhaseW3: businessData[0]?.reviewPhaseW3 || '',
            finalConsent: true
        };
        
        try {
            const response = await fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': formData.csrf_token
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                localStorage.removeItem('sbOrderForm');
                hideConfirmModal();
                
                const waMessage = encodeURIComponent(`Hi! I have already filled out the order form.\n\nEmail: ${formData.email}\nBusinesses: ${formData.businessNames}`);
                document.getElementById('whatsappBtn').href = `https://api.whatsapp.com/send?phone=6287870707202&text=${waMessage}`;
                
                // Save order context for audit modal (used after agree)
                window.sbAuditCtx = {
                    orderId: result.orderId || result.order_id || '',
                    businessName: formData.businessNames || '',
                    email: formData.email || '',
                    whatsapp: formData.whatsapp || formData.phone || '',
                    orderType: 'reviews'
                };

                goToStep(6);
                fireConfetti();
            } else {
                alert('Error: ' + (result.message || 'Submission failed'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        } finally {
            loadingOverlay.style.display = 'none';
            submitBtn.disabled = false;
        }
    }
    
    // ============================================================================
    // CONFETTI
    // ============================================================================
    
    function fireConfetti() {
        confetti({ particleCount: 100, spread: 70, origin: { x: 0, y: 0.6 } });
        confetti({ particleCount: 100, spread: 70, origin: { x: 1, y: 0.6 } });
        setTimeout(() => {
            confetti({ particleCount: 150, spread: 100, origin: { x: 0.5, y: 0.5 } });
        }, 300);
    }
    
    // ============================================================================
    // IMAGE MODAL
    // ============================================================================
    
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.add('show');
    }
    
    function hideImageModal() {
        document.getElementById('imageModal').classList.remove('show');
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideImageModal();
            hideTncConfirmModal();
            hideConfirmModal();
            hideReadyModal();
            hideNotReadyModal();
        }
    });
    
    // ============================================================================
    // LOCAL STORAGE
    // ============================================================================
    
    function saveFormData() {
        const form = document.getElementById('orderForm');
        const data = { 
            businessData: businessData,
            currentStep: currentStep,
            serviceType: currentServiceType,
            smCurrentStep: smCurrentStep,
            savedAt: new Date().toISOString()
        };
        
        form.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
            } else if (el.type === 'checkbox') {
                if (el.classList.contains('tnc-checkbox')) {
                    if (!data.tncChecked) data.tncChecked = [];
                    if (el.checked) data.tncChecked.push(el.dataset.index);
                } else if (el.classList.contains('sm-tnc-checkbox')) {
                    if (!data.smTncChecked) data.smTncChecked = [];
                    if (el.checked) data.smTncChecked.push(el.dataset.index);
                } else if (el.id) {
                    data[el.id] = el.checked;
                }
            } else if (el.id && el.id !== 'csrf_token') {
                data[el.id] = el.value;
            }
        });
        
        localStorage.setItem('sbOrderForm', JSON.stringify(data));
    }
    
    function loadFormData() {
        const saved = localStorage.getItem('sbOrderForm');
        if (!saved) return false;

        try {
            const data = JSON.parse(saved);

            // Expire after 30 days
            if (data.savedAt) {
                const savedTime = new Date(data.savedAt).getTime();
                const thirtyDays = 30 * 24 * 60 * 60 * 1000;
                if (Date.now() - savedTime > thirtyDays) {
                    localStorage.removeItem('sbOrderForm');
                    return false;
                }
            }
            let hasData = false;
            
            // Check if there's meaningful data
            if (data.businessData && data.businessData.length > 0) {
                businessData = data.businessData;
                hasData = true;
            }
            
            // Restore service type selection
            if (data.serviceType) {
                currentServiceType = data.serviceType;
                selectServiceType(data.serviceType);
                hasData = true;
            }
            
            // Restore form fields
            Object.keys(data).forEach(key => {
                if (key === 'tncChecked' && Array.isArray(data.tncChecked)) {
                    const sortedIndices = data.tncChecked.map(i => parseInt(i)).sort((a, b) => a - b);
                    sortedIndices.forEach(index => {
                        const checkbox = document.querySelector(`.tnc-checkbox[data-index="${index}"]`);
                        if (checkbox) {
                            const item = checkbox.closest('.tnc-item');
                            item.style.display = 'block';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                            checkbox.checked = true;
                            item.classList.add('checked');
                            item.querySelector('.tnc-check-icon').classList.remove('hidden');
                            
                            const nextIndex = index + 1;
                            const nextItem = document.querySelector(`.tnc-item[data-index="${nextIndex}"]`);
                            if (nextItem) {
                                nextItem.style.display = 'block';
                                nextItem.style.opacity = '1';
                                nextItem.style.transform = 'translateY(0)';
                            }
                        }
                    });
                    
                    if (sortedIndices.length > 0) hasData = true;
                    
                    const totalTnc = document.querySelectorAll('.tnc-checkbox').length;
                    const tncHint = document.getElementById('tncHint');
                    if (sortedIndices.length >= totalTnc && tncHint) {
                        tncHint.style.display = 'none';
                    }
                    
                    updateTncProgress();
                    return;
                }
                
                // Restore Social Media TNC checkboxes
                if (key === 'smTncChecked' && Array.isArray(data.smTncChecked)) {
                    data.smTncChecked.forEach(index => {
                        const checkbox = document.querySelector(`.sm-tnc-checkbox[data-index="${index}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            const item = checkbox.closest('.sm-tnc-item');
                            item.classList.add('border-purple-500', 'bg-purple-50');
                            item.classList.remove('border-purple-200');
                            const checkIcon = item.querySelector('.sm-tnc-check');
                            if (checkIcon) checkIcon.classList.remove('hidden');
                        }
                    });
                    if (data.smTncChecked.length > 0) {
                        hasData = true;
                        updateSmTncProgress();
                    }
                    return;
                }
                
                if (key === 'businessData' || key === 'currentStep' || key === 'savedAt' || key === 'serviceType' || key === 'smCurrentStep') return;
                
                const el = document.getElementById(key);
                if (el) {
                    if (el.type === 'checkbox') {
                        el.checked = data[key];
                    } else {
                        el.value = data[key];
                        if (el.value) hasData = true;
                    }
                }
                
                if (!el) {
                    const radio = document.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                    if (radio) {
                        radio.checked = true;
                        hasData = true;
                    }
                }
            });
            
            handleProductTypeChange();
            updateAllocationPreview();
            
            // Recalculate SM delivery if quantity exists
            if (data.smQuantity) {
                calculateSmDelivery();
            }
            
            // Show saved data banner if data exists
            if (hasData) {
                const banner = document.getElementById('savedDataBanner');
                if (banner) banner.classList.remove('hidden');
                
                // Show toast notification
                setTimeout(() => {
                    showToast('Your previous progress has been restored', 'info');
                }, 500);
            }
            
            return hasData;
            
        } catch (e) {
            console.error('Error loading form data:', e);
            return false;
        }
    }
    
    function clearSavedData() {
        if (confirm('Are you sure you want to clear all saved data and start fresh?')) {
            localStorage.removeItem('sbOrderForm');
            showToast('Saved data cleared', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
    
    function showToast(message, type = 'success') {
        const toast = document.getElementById('sbToast');
        const toastMsg = document.getElementById('sbToastMsg');
        const toastIcon = document.getElementById('sbToastIcon');
        
        if (!toast) return;
        
        toastMsg.textContent = message;
        toast.className = 'sb-toast sb-toast-' + type;
        
        // Update icon based on type
        if (type === 'info') {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
        } else {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        }
        
        // Show toast
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // ============================================================================
    // BONUS — FREE MARKETING AUDIT (NDA Modal + WhatsApp + Backend Log)
    // ============================================================================
    const SB_AUDIT_WA_NUMBER   = '<?php echo $SB_WA_NUMBER; ?>'; // wa.me/<number> — from central wa-config.php
    const SB_AUDIT_NDA_VERSION = '1.0';
    const SB_AUDIT_TOTAL_STEPS = 4;
    let   sbAuditCurrentStep   = 1;

    function openAuditModal() {
        const modal = document.getElementById('auditNdaModal');
        if (!modal) return;
        // Reset all state every time modal opens
        const check = document.getElementById('auditAgreeCheck');
        const cta   = document.getElementById('auditFinalCta');
        if (check) check.checked = false;
        if (cta)   { cta.classList.remove('enabled'); cta.disabled = true; cta.textContent = '✅  I AGREE — OPEN WHATSAPP  →'; }
        goToAuditStep(1);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAuditModal() {
        const modal = document.getElementById('auditNdaModal');
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function goToAuditStep(n) {
        if (n < 1) n = 1;
        if (n > SB_AUDIT_TOTAL_STEPS) n = SB_AUDIT_TOTAL_STEPS;
        sbAuditCurrentStep = n;

        // Toggle step content
        document.querySelectorAll('.audit-step-content').forEach(function(el){
            el.classList.toggle('active', parseInt(el.getAttribute('data-step'), 10) === n);
        });

        // Update step dots
        document.querySelectorAll('.audit-step-dot').forEach(function(el){
            const s = parseInt(el.getAttribute('data-step'), 10);
            el.classList.remove('active', 'done');
            if (s < n) el.classList.add('done');
            else if (s === n) el.classList.add('active');
        });

        // Update step labels
        document.querySelectorAll('.audit-step-label').forEach(function(el){
            const s = parseInt(el.getAttribute('data-step'), 10);
            el.classList.remove('active', 'done');
            if (s < n) el.classList.add('done');
            else if (s === n) el.classList.add('active');
        });

        // Update connecting lines
        document.querySelectorAll('.audit-step-line').forEach(function(el){
            const range = el.getAttribute('data-line').split('-');
            const from  = parseInt(range[0], 10);
            el.classList.toggle('done', from < n);
        });

        // Update counter
        const counterEl = document.getElementById('auditStepCurrent');
        if (counterEl) counterEl.textContent = String(n);

        // Update footer state (first/last)
        const footer = document.querySelector('.audit-modal-footer');
        if (footer) {
            footer.classList.toggle('is-first', n === 1);
            footer.classList.toggle('is-last',  n === SB_AUDIT_TOTAL_STEPS);
        }
        const backBtn = document.getElementById('auditBackBtn');
        if (backBtn) backBtn.style.visibility = (n === 1) ? 'hidden' : 'visible';

        // Scroll modal body to top on step change
        const body = document.querySelector('.audit-modal-card');
        if (body) body.scrollTop = 0;
    }

    function nextAuditStep() {
        if (sbAuditCurrentStep < SB_AUDIT_TOTAL_STEPS) {
            goToAuditStep(sbAuditCurrentStep + 1);
        }
    }

    function prevAuditStep() {
        if (sbAuditCurrentStep > 1) {
            goToAuditStep(sbAuditCurrentStep - 1);
        }
    }

    function onAuditAgreeChange() {
        const check = document.getElementById('auditAgreeCheck');
        const cta   = document.getElementById('auditFinalCta');
        if (!check || !cta) return;
        if (check.checked) { cta.classList.add('enabled'); cta.disabled = false; }
        else               { cta.classList.remove('enabled'); cta.disabled = true; }
    }

    // Close modal on ESC / backdrop click
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeAuditModal();
    });
    document.addEventListener('click', function(e){
        const overlay = document.getElementById('auditNdaModal');
        if (overlay && e.target === overlay) closeAuditModal();
    });

    async function confirmAuditAgree() {
        const check = document.getElementById('auditAgreeCheck');
        if (!check || !check.checked) return;

        const ctx = window.sbAuditCtx || {};
        const orderId      = ctx.orderId || '';
        const businessName = ctx.businessName || '';
        const email        = ctx.email || '';
        const whatsapp     = ctx.whatsapp || '';
        const orderType    = ctx.orderType || 'reviews';

        // Build prefilled WhatsApp message
        const lines = [
            'Hi Smart Buzzer! I want to claim my FREE Marketing Audit bonus.',
            '',
            'I AGREE to share view-only access to my ad accounts (Google Ads / Meta Ads / GBP) so your team can audit my funnel — cost, CPR, CPL, ROAS, and optimization status.',
            '',
            'Business: ' + (businessName || '-'),
            'Order ID: ' + (orderId || '-'),
            '',
            'I have read & agreed to the NDA terms shown on the page.'
        ];
        const waUrl = 'https://wa.me/' + SB_AUDIT_WA_NUMBER + '?text=' + encodeURIComponent(lines.join('\n'));

        // Disable CTA to prevent double-click
        const cta = document.getElementById('auditFinalCta');
        if (cta) { cta.disabled = true; cta.textContent = '⏳  Logging agreement...'; }

        // Wait briefly for log response then open WhatsApp regardless of result
        const result = await logAuditRequest({
            orderId: orderId,
            businessName: businessName,
            email: email,
            whatsapp: whatsapp,
            orderType: orderType,
            ndaVersion: SB_AUDIT_NDA_VERSION,
            agreed: true,
            csrf_token: '<?php echo $csrfToken; ?>'
        });

        if (result && result.success) {
            showToast('✅ Agreement logged. Opening WhatsApp...', 'success');
        } else {
            const reason = (result && result.message) ? result.message : 'Network error';
            showToast('⚠️ Log warning: ' + reason + '. Opening WhatsApp anyway.', 'warning');
            console.warn('[audit-log]', result);
        }

        // Always open WhatsApp — UX must not be blocked by log issues
        setTimeout(function(){
            window.open(waUrl, '_blank');
            closeAuditModal();
            if (cta) { cta.disabled = false; cta.textContent = '✅  I AGREE — OPEN WHATSAPP  →'; }
        }, 400);
    }

    async function logAuditRequest(payload) {
        try {
            const resp = await fetch('audit-log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const text = await resp.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                return { success: false, message: 'Bad server response (HTTP ' + resp.status + ')' };
            }
        } catch (err) {
            console.warn('Audit log fetch error:', err);
            return { success: false, message: 'Network error' };
        }
    }

    // ============================================================================
    // END
    // ============================================================================
    </script>
    
    <!-- Service Selector Popup Modal -->
    <div class="service-popup-overlay" id="servicePopupOverlay" onclick="closeServicePopup(event)">
        <div class="service-popup" onclick="event.stopPropagation()">
            <div class="service-popup-title">What did you order?</div>
            <div class="service-popup-subtitle">Select your purchased service so we can process your order</div>
            
            <div class="service-selector">
                <div class="service-card" onclick="selectServiceFromPopup('reviews')" id="popupCardReviews">
                    <div class="service-card-icon bg-yellow-100">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="service-card-title">Reviews Booster</div>
                    <div class="service-card-desc">Google, Yelp, Facebook reviews for your business</div>
                </div>
                <div class="service-card" onclick="selectServiceFromPopup('social_media')" id="popupCardSocialMedia">
                    <div class="service-card-icon bg-purple-100">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="service-card-title">Social Media</div>
                    <div class="service-card-desc">Instagram, TikTok, Twitter, YouTube followers & likes</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>