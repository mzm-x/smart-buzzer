<?php
/**
 * Smart Buzzer — Central WhatsApp Number Config (single source of truth)
 * ----------------------------------------------------------------------
 * Every landing page includes this file and uses $SB_WA_NUMBER (for wa.me /
 * api.whatsapp.com links) and $SB_WA_DISPLAY (human-readable text).
 *
 * Edit the number from the dashboard: /analytics/  →  "WhatsApp Number" panel
 * (writes wa-config.json). The defaults below are the safe fallback if the
 * JSON is missing or invalid, so LP WhatsApp links never break.
 *
 * Do NOT hardcode the number in landing pages anymore — change it here / via
 * the dashboard and all pages follow automatically.
 */

// Safe fallback defaults (used if wa-config.json is missing/invalid)
$SB_WA_NUMBER  = '628979133204';
$SB_WA_DISPLAY = '+62 897-9133-204';

$__sb_wa_json = __DIR__ . '/wa-config.json';
if (is_readable($__sb_wa_json)) {
    $__sb_wa = json_decode((string)file_get_contents($__sb_wa_json), true);
    if (is_array($__sb_wa)) {
        if (!empty($__sb_wa['wa_number']) && preg_match('/^[0-9]{6,20}$/', $__sb_wa['wa_number'])) {
            $SB_WA_NUMBER = $__sb_wa['wa_number'];
        }
        if (!empty($__sb_wa['wa_display'])) {
            $SB_WA_DISPLAY = preg_replace('/[\t\r\n<>]/', '', (string)$__sb_wa['wa_display']);
        }
    }
}
