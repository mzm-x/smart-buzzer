<?php
/**
 * /outbound/api.php v2 — Apify Google Maps Scraper Backend
 * Actions: start, status, results, runs, limits, leads, stats, save_leads
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

define('APIFY_TOKEN', 'apify_api_S8VBUZeyfA9ehMNHZNtduhKiy34j0X14FMf7');
define('APIFY_ORG_ID', '2HSXfO6L1LvcRjPML');
define('ACTOR_ID', 'nwua9Gu5YrADL7ZDj');
define('WEBHOOK_URL', 'https://smart-buzzer.com/outbound/webhook.php');
define('DATA_DIR', __DIR__ . '/data');
define('LEADS_DB', DATA_DIR . '/leads_db.json');

if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);

$action = $_GET['action'] ?? '';
switch ($action) {
    case 'start':      handleStart(); break;
    case 'status':     handleStatus(); break;
    case 'results':    handleResults(); break;
    case 'runs':       handleRuns(); break;
    case 'limits':     handleLimits(); break;
    case 'leads':      handleLeads(); break;
    case 'stats':      handleStats(); break;
    case 'save_leads': handleSaveLeads(); break;
    case 'clear_leads':handleClearLeads(); break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

// ── START RUN ─────────────────────────────────────────────
function handleStart() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die(json_encode(['error' => 'POST required']));
    }

    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body || empty($body['searchTerms'])) {
        http_response_code(400);
        die(json_encode(['error' => 'searchTerms required']));
    }

    $searchTerms = array_filter(array_map('trim', explode("\n", $body['searchTerms'])));
    if (empty($searchTerms)) {
        http_response_code(400);
        die(json_encode(['error' => 'At least one search term required']));
    }

    $maxResults = min((int)($body['maxResults'] ?? 100), 1000);
    $language = $body['language'] ?? 'en';
    $location = trim($body['location'] ?? '');
    $country = trim($body['country'] ?? '');
    $state = trim($body['state'] ?? '');
    $city = trim($body['city'] ?? '');

    $actorInput = [
        'searchStringsArray' => array_values($searchTerms),
        'maxCrawledPlacesPerSearch' => $maxResults,
        'language' => $language,
        'deeperCityScrape' => true,
        'includeWebResults' => false,
        'scrapeContacts' => true,
        'scrapeDirectories' => false,
    ];

    if ($country || $state || $city) {
        $geo = [];
        if ($country) $geo['country'] = $country;
        if ($state) $geo['state'] = $state;
        if ($city) $geo['city'] = $city;
        $actorInput['geolocation'] = $geo;
    }

    if ($location) {
        if (strpos($location, 'google.com/maps') !== false || strpos($location, 'maps.app.goo.gl') !== false) {
            $actorInput['startUrls'] = [['url' => $location]];
        } else {
            $actorInput['locationQuery'] = $location;
        }
    }

    $url = 'https://api.apify.com/v2/acts/' . ACTOR_ID . '/runs?token=' . APIFY_TOKEN;
    $response = apifyRequest('POST', $url, $actorInput);

    if (!$response || !isset($response['data']['id'])) {
        http_response_code(500);
        die(json_encode(['error' => 'Failed to start Apify run', 'details' => $response]));
    }

    $runData = $response['data'];
    $runMeta = [
        'runId' => $runData['id'],
        'datasetId' => $runData['defaultDatasetId'] ?? '',
        'status' => $runData['status'] ?? 'RUNNING',
        'startedAt' => date('Y-m-d H:i:s'),
        'searchTerms' => array_values($searchTerms),
        'maxResults' => $maxResults,
        'location' => $location,
        'country' => $country,
        'state' => $state,
        'city' => $city,
        'language' => $language,
        'costUsd' => 0,
        'resultsCount' => 0,
        'emailsCount' => 0,
    ];

    file_put_contents(DATA_DIR . '/run_' . $runData['id'] . '.json', json_encode($runMeta, JSON_PRETTY_PRINT));
    setupWebhook($runData['id']);

    echo json_encode([
        'success' => true,
        'runId' => $runData['id'],
        'datasetId' => $runData['defaultDatasetId'] ?? '',
        'status' => $runData['status'] ?? 'RUNNING'
    ]);
}

// ── STATUS ────────────────────────────────────────────────
function handleStatus() {
    $runId = $_GET['runId'] ?? '';
    if (!$runId) { http_response_code(400); die(json_encode(['error' => 'runId required'])); }

    $url = 'https://api.apify.com/v2/actor-runs/' . $runId . '?token=' . APIFY_TOKEN;
    $response = apifyRequest('GET', $url);
    if (!$response || !isset($response['data'])) { http_response_code(500); die(json_encode(['error' => 'Failed'])); }

    $data = $response['data'];
    $costUsd = $data['usageTotalUsd'] ?? 0;

    // Update run file with cost and status
    $runFile = DATA_DIR . '/run_' . $runId . '.json';
    if (file_exists($runFile)) {
        $meta = json_decode(file_get_contents($runFile), true) ?: [];
        $meta['status'] = $data['status'];
        $meta['costUsd'] = $costUsd;
        if ($data['finishedAt'] ?? null) $meta['finishedAt'] = $data['finishedAt'];
        file_put_contents($runFile, json_encode($meta, JSON_PRETTY_PRINT));
    }

    echo json_encode([
        'runId' => $data['id'],
        'status' => $data['status'],
        'datasetId' => $data['defaultDatasetId'] ?? '',
        'startedAt' => $data['startedAt'] ?? '',
        'finishedAt' => $data['finishedAt'] ?? '',
        'costUsd' => $costUsd,
    ]);
}

// ── RESULTS ───────────────────────────────────────────────
function handleResults() {
    $datasetId = $_GET['datasetId'] ?? '';
    $runId = $_GET['runId'] ?? '';

    if (!$datasetId && !$runId) { http_response_code(400); die(json_encode(['error' => 'datasetId or runId required'])); }

    if (!$datasetId && $runId) {
        $url = 'https://api.apify.com/v2/actor-runs/' . $runId . '?token=' . APIFY_TOKEN;
        $r = apifyRequest('GET', $url);
        $datasetId = $r['data']['defaultDatasetId'] ?? '';
        if (!$datasetId) { http_response_code(404); die(json_encode(['error' => 'Dataset not found'])); }
    }

    $limit = min((int)($_GET['limit'] ?? 1000), 5000);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $url = 'https://api.apify.com/v2/datasets/' . $datasetId . '/items?token=' . APIFY_TOKEN . '&limit=' . $limit . '&offset=' . $offset . '&format=json';

    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['Accept: application/json']]);
    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) { http_response_code(500); die(json_encode(['error' => 'Failed to fetch', 'httpCode' => $httpCode])); }

    $items = json_decode($raw, true) ?: [];
    $leads = [];
    foreach ($items as $item) {
        $leads[] = extractLead($item, $runId);
    }

    // Update run meta with counts
    if ($runId) {
        $runFile = DATA_DIR . '/run_' . $runId . '.json';
        if (file_exists($runFile)) {
            $meta = json_decode(file_get_contents($runFile), true) ?: [];
            $meta['resultsCount'] = count($leads);
            $meta['emailsCount'] = count(array_filter($leads, fn($l) => !empty($l['email'])));
            file_put_contents($runFile, json_encode($meta, JSON_PRETTY_PRINT));
        }
    }

    // Auto-save to leads JSON file (dedup by placeId)
    autoSaveLeads($leads, $runId);

    echo json_encode(['success' => true, 'count' => count($leads), 'leads' => $leads]);
}

// ── SAVE LEADS TO DB ──────────────────────────────────────
function handleSaveLeads() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die(json_encode(['error' => 'POST required'])); }

    $body = json_decode(file_get_contents('php://input'), true);
    $newLeads = $body['leads'] ?? [];
    $runId = $body['runId'] ?? '';

    if (empty($newLeads)) { echo json_encode(['success' => true, 'saved' => 0]); return; }

    // Load existing DB
    $db = [];
    if (file_exists(LEADS_DB)) {
        $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];
    }

    // Index by placeId for dedup
    $indexed = [];
    foreach ($db as $lead) {
        $key = $lead['placeId'] ?? $lead['title'] . '_' . $lead['phone'];
        $indexed[$key] = $lead;
    }

    $added = 0;
    foreach ($newLeads as $lead) {
        $key = $lead['placeId'] ?? $lead['title'] . '_' . $lead['phone'];
        if (!isset($indexed[$key])) {
            $lead['savedAt'] = date('Y-m-d H:i:s');
            $lead['fromRunId'] = $runId;
            $indexed[$key] = $lead;
            $added++;
        }
    }

    // Write back
    $fp = fopen(LEADS_DB, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, json_encode(array_values($indexed), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    flock($fp, LOCK_UN);
    fclose($fp);

    echo json_encode(['success' => true, 'saved' => $added, 'total' => count($indexed)]);
}

// ── GET LEADS DB ──────────────────────────────────────────
function handleLeads() {
    if (!file_exists(LEADS_DB)) { echo json_encode(['leads' => [], 'total' => 0]); return; }

    $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];

    // Optional filters
    $filterState = $_GET['state'] ?? '';
    $filterCategory = $_GET['category'] ?? '';
    $filterSearch = strtolower($_GET['search'] ?? '');

    if ($filterState || $filterCategory || $filterSearch) {
        $db = array_filter($db, function($l) use ($filterState, $filterCategory, $filterSearch) {
            if ($filterState && stripos($l['state'] ?? '', $filterState) === false) return false;
            if ($filterCategory && stripos($l['categoryName'] ?? '', $filterCategory) === false) return false;
            if ($filterSearch) {
                $haystack = strtolower(($l['title'] ?? '') . ' ' . ($l['email'] ?? '') . ' ' . ($l['phone'] ?? '') . ' ' . ($l['city'] ?? ''));
                if (strpos($haystack, $filterSearch) === false) return false;
            }
            return true;
        });
        $db = array_values($db);
    }

    $total = count($db);
    $withEmail = count(array_filter($db, fn($l) => !empty($l['email'])));

    echo json_encode(['leads' => $db, 'total' => $total, 'withEmail' => $withEmail]);
}

// ── CLEAR LEADS DB ────────────────────────────────────────
function handleClearLeads() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die(json_encode(['error' => 'POST required'])); }
    if (file_exists(LEADS_DB)) file_put_contents(LEADS_DB, '[]');
    echo json_encode(['success' => true]);
}

// ── STATS (aggregated) ────────────────────────────────────
function handleStats() {
    $files = glob(DATA_DIR . '/run_*.json');
    $totalRuns = 0;
    $totalLeads = 0;
    $totalEmails = 0;
    $totalCost = 0;

    foreach ($files as $file) {
        $meta = json_decode(file_get_contents($file), true);
        if (!$meta) continue;
        $totalRuns++;
        $totalLeads += (int)($meta['resultsCount'] ?? 0);
        $totalEmails += (int)($meta['emailsCount'] ?? 0);
        $totalCost += (float)($meta['costUsd'] ?? 0);
    }

    // Also count from leads DB
    $dbLeads = 0;
    $dbEmails = 0;
    if (file_exists(LEADS_DB)) {
        $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];
        $dbLeads = count($db);
        $dbEmails = count(array_filter($db, fn($l) => !empty($l['email'])));
    }

    echo json_encode([
        'totalRuns' => $totalRuns,
        'totalLeads' => max($totalLeads, $dbLeads),
        'totalEmails' => max($totalEmails, $dbEmails),
        'totalCostUsd' => round($totalCost, 2),
        'avgCostPerRun' => $totalRuns > 0 ? round($totalCost / $totalRuns, 2) : 0,
        'dbLeads' => $dbLeads,
        'dbEmails' => $dbEmails,
    ]);
}

// ── RUNS LIST ─────────────────────────────────────────────
function handleRuns() {
    $files = glob(DATA_DIR . '/run_*.json');
    $runs = [];
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) $runs[] = $data;
    }
    usort($runs, fn($a, $b) => strcmp($b['startedAt'] ?? '', $a['startedAt'] ?? ''));
    $runs = array_slice($runs, 0, 50);
    echo json_encode(['runs' => $runs]);
}

// ── LIMITS ────────────────────────────────────────────────
function handleLimits() {
    $url = 'https://api.apify.com/v2/users/me?token=' . APIFY_TOKEN;
    $response = apifyRequest('GET', $url);
    if (!$response || !isset($response['data'])) { http_response_code(500); die(json_encode(['error' => 'Failed'])); }

    $data = $response['data'];
    $plan = $data['plan'] ?? [];
    $limits = $data['limits'] ?? [];
    $billing = $data['currentBillingPeriod'] ?? [];
    $usageUsd = $billing['usageTotalUsd'] ?? $data['usageTotalUsd'] ?? 0;
    $monthlyLimit = $plan['monthlyUsageCreditsUsd'] ?? $limits['maxMonthlyUsageUsd'] ?? 199;

    if ($usageUsd <= 0) {
        $ur = apifyRequest('GET', 'https://api.apify.com/v2/users/me/usage/monthly?token=' . APIFY_TOKEN);
        $ud = $ur['data'] ?? $ur ?? [];
        $usageUsd = $ud['usageTotalUsd'] ?? $ud['totalUsageUsd'] ?? 0;
    }

    echo json_encode([
        'plan' => ['id' => $plan['id'] ?? 'FREE', 'description' => $plan['description'] ?? 'Free', 'monthlyUsageCreditsUsd' => $monthlyLimit],
        'usageUsd' => $usageUsd,
        'monthlyLimitUsd' => $monthlyLimit,
    ]);
}

// ── AUTO-SAVE LEADS (called from handleResults) ──────────
function autoSaveLeads($newLeads, $runId = '') {
    if (empty($newLeads)) return;

    $db = [];
    if (file_exists(LEADS_DB)) {
        $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];
    }

    $indexed = [];
    foreach ($db as $lead) {
        $key = $lead['placeId'] ?? $lead['title'] . '_' . $lead['phone'];
        $indexed[$key] = $lead;
    }

    foreach ($newLeads as $lead) {
        $key = $lead['placeId'] ?? $lead['title'] . '_' . $lead['phone'];
        if (!isset($indexed[$key])) {
            $lead['savedAt'] = date('Y-m-d H:i:s');
            $lead['fromRunId'] = $runId;
            $indexed[$key] = $lead;
        }
    }

    $fp = fopen(LEADS_DB, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, json_encode(array_values($indexed), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    flock($fp, LOCK_UN);
    fclose($fp);
}

// ── HELPERS ───────────────────────────────────────────────
function apifyRequest($method, $url, $body = null) {
    $ch = curl_init($url);
    $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json']];
    if ($method === 'POST') { $opts[CURLOPT_POST] = true; if ($body) $opts[CURLOPT_POSTFIELDS] = json_encode($body); }
    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch); $err = curl_error($ch); curl_close($ch);
    return $err ? ['error' => $err] : json_decode($raw, true);
}

function setupWebhook($runId) {
    apifyRequest('POST', 'https://api.apify.com/v2/webhooks?token=' . APIFY_TOKEN, [
        'eventTypes' => ['ACTOR.RUN.SUCCEEDED', 'ACTOR.RUN.FAILED', 'ACTOR.RUN.ABORTED'],
        'condition' => ['actorRunId' => $runId],
        'requestUrl' => WEBHOOK_URL,
        'payloadTemplate' => '{"userId":{{userId}},"createdAt":{{createdAt}},"eventType":{{eventType}},"eventData":{{eventData}},"resource":{{resource}}}',
    ]);
}

function extractLead($item, $runId = '') {
    return [
        'title' => $item['title'] ?? '',
        'categoryName' => $item['categoryName'] ?? '',
        'categories' => $item['categories'] ?? [],
        'address' => $item['address'] ?? $item['street'] ?? '',
        'neighborhood' => $item['neighborhood'] ?? '',
        'city' => $item['city'] ?? '',
        'state' => $item['state'] ?? '',
        'zipCode' => $item['postalCode'] ?? '',
        'countryCode' => $item['countryCode'] ?? '',
        'phone' => $item['phone'] ?? '',
        'phoneUnformatted' => $item['phoneUnformatted'] ?? '',
        'website' => $item['website'] ?? '',
        'email' => extractEmail($item),
        'totalScore' => $item['totalScore'] ?? 0,
        'reviewsCount' => $item['reviewsCount'] ?? 0,
        'url' => $item['url'] ?? '',
        'placeId' => $item['placeId'] ?? '',
        'price' => $item['price'] ?? '',
        'permanentlyClosed' => $item['permanentlyClosed'] ?? false,
        'temporarilyClosed' => $item['temporarilyClosed'] ?? false,
        'facebook' => $item['facebooks'][0] ?? '',
        'instagram' => $item['instagrams'][0] ?? '',
        'twitter' => $item['twitters'][0] ?? '',
        'linkedin' => $item['linkedIns'][0] ?? '',
        'youtube' => $item['youtubes'][0] ?? '',
        'tiktok' => $item['tiktoks'][0] ?? '',
        'claimThisBusiness' => $item['claimThisBusiness'] ?? false,
    ];
}

function extractEmail($item) {
    if (!empty($item['email'])) return $item['email'];
    if (!empty($item['contacts']) && is_array($item['contacts'])) {
        foreach ($item['contacts'] as $c) {
            if (($c['type'] ?? '') === 'email' && !empty($c['value'])) return $c['value'];
        }
    }
    if (!empty($item['emails']) && is_array($item['emails'])) return $item['emails'][0] ?? '';
    return '';
}
