<?php
/**
 * /tp-outbound/api.php — Apify Tripadvisor Scraper Backend
 * Actor: maxcopell/tripadvisor (dbEyMBriog95Fv8CW)
 * Actions: start, status, results, runs, limits, leads, stats, save_leads, clear_leads
 *
 * Differs from /outbound/ (Google Maps):
 *  - Input mapped to Tripadvisor schema (query + include* toggles + currency)
 *  - Leads auto-tagged with a rating x review SEGMENT (s1..s4/mid)
 *  - Leads auto-tagged with a cityTier (tourist / nontourist / other)
 *  - Dedup key = Tripadvisor place id (stored as placeId)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

define('APIFY_TOKEN', 'apify_api_S8VBUZeyfA9ehMNHZNtduhKiy34j0X14FMf7');
define('APIFY_ORG_ID', '2HSXfO6L1LvcRjPML');
define('ACTOR_ID', 'dbEyMBriog95Fv8CW');
define('WEBHOOK_URL', 'https://smart-buzzer.com/tp-outbound/webhook.php');
define('DATA_DIR', __DIR__ . '/data');
define('LEADS_DB', DATA_DIR . '/leads_db.json');

// Target city classification (from the target-segment doc)
$GLOBALS['TOURIST_CITIES']    = ['new york', 'las vegas', 'orlando', 'miami', 'san francisco'];
$GLOBALS['NONTOURIST_CITIES'] = ['houston', 'phoenix', 'philadelphia', 'dallas', 'san antonio'];

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
    if (!$body) { http_response_code(400); die(json_encode(['error' => 'Invalid body'])); }

    $query = trim($body['query'] ?? '');
    $startUrl = trim($body['url'] ?? '');
    if ($query === '' && $startUrl === '') {
        http_response_code(400);
        die(json_encode(['error' => 'Target city (query) or a Tripadvisor URL is required']));
    }

    // Content types (at least one must be on)
    $wantRest  = !empty($body['restaurants']);
    $wantHotel = !empty($body['hotels']);
    $wantAttr  = !empty($body['attractions']);
    if (!$wantRest && !$wantHotel && !$wantAttr) {
        // default to all if the caller sent none
        $wantRest = $wantHotel = $wantAttr = true;
    }

    $maxResults = min(max((int)($body['maxResults'] ?? 50), 1), 1000);
    $language   = $body['language'] ?? 'en';
    $currency   = $body['currency'] ?? 'USD';
    $enrich     = !empty($body['enrichEmails']);

    $contentTypes = [];
    if ($wantRest)  $contentTypes[] = 'Restaurants';
    if ($wantHotel) $contentTypes[] = 'Hotels';
    if ($wantAttr)  $contentTypes[] = 'Things to Do';

    $actorInput = [
        'maxItemsPerQuery'    => $maxResults,
        'includeRestaurants'  => $wantRest,
        'includeHotels'       => $wantHotel,
        'includeAttractions'  => $wantAttr,
        'includeTags'         => true,
        'includeNearbyResults'=> false,
        'includePriceOffers'  => false,
        'language'            => $language,
        'currency'            => $currency,
        'maxPhotosPerPlace'   => 0,
    ];

    // Start URL (Tripadvisor listing/category page) vs plain location query
    if ($startUrl !== '' && strpos($startUrl, 'tripadvisor.') !== false) {
        $actorInput['startUrls'] = [['url' => $startUrl]];
    } else {
        $actorInput['query'] = ($query !== '') ? $query : $startUrl;
    }

    // Optional paid email enrichment (default OFF)
    if ($enrich) {
        $actorInput['maximumLeadsEnrichmentRecords'] = $maxResults;
        $actorInput['verifyLeadsEnrichmentEmails'] = true;
    } else {
        $actorInput['maximumLeadsEnrichmentRecords'] = 0;
    }

    $url = 'https://api.apify.com/v2/acts/' . ACTOR_ID . '/runs?token=' . APIFY_TOKEN;
    $response = apifyRequest('POST', $url, $actorInput);

    if (!$response || !isset($response['data']['id'])) {
        http_response_code(500);
        die(json_encode(['error' => 'Failed to start Apify run', 'details' => $response]));
    }

    $runData = $response['data'];
    $runMeta = [
        'runId'        => $runData['id'],
        'datasetId'    => $runData['defaultDatasetId'] ?? '',
        'status'       => $runData['status'] ?? 'RUNNING',
        'startedAt'    => date('Y-m-d H:i:s'),
        'query'        => $query !== '' ? $query : $startUrl,
        'contentTypes' => $contentTypes,
        'maxResults'   => $maxResults,
        'enrichEmails' => $enrich,
        'language'     => $language,
        'currency'     => $currency,
        'costUsd'      => 0,
        'resultsCount' => 0,
        'emailsCount'  => 0,
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

    // Load the run meta once (for query -> cityTier fallback)
    $runMeta = [];
    if ($runId) {
        $runFile = DATA_DIR . '/run_' . $runId . '.json';
        if (file_exists($runFile)) $runMeta = json_decode(file_get_contents($runFile), true) ?: [];
    }
    $runQuery = $runMeta['query'] ?? '';

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
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 45, CURLOPT_HTTPHEADER => ['Accept: application/json']]);
    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) { http_response_code(500); die(json_encode(['error' => 'Failed to fetch', 'httpCode' => $httpCode])); }

    $items = json_decode($raw, true) ?: [];
    $leads = [];
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        $leads[] = extractLead($item, $runId, $runQuery);
    }

    // Update run meta with counts
    if ($runId && file_exists(DATA_DIR . '/run_' . $runId . '.json')) {
        $runFile = DATA_DIR . '/run_' . $runId . '.json';
        $meta = json_decode(file_get_contents($runFile), true) ?: [];
        $meta['resultsCount'] = count($leads);
        $meta['emailsCount'] = count(array_filter($leads, fn($l) => !empty($l['email'])));
        file_put_contents($runFile, json_encode($meta, JSON_PRETTY_PRINT));
    }

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

    $db = [];
    if (file_exists(LEADS_DB)) $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];

    $indexed = [];
    foreach ($db as $lead) {
        $key = dedupKey($lead);
        $indexed[$key] = $lead;
    }

    $added = 0;
    foreach ($newLeads as $lead) {
        $key = dedupKey($lead);
        if (!isset($indexed[$key])) {
            $lead['savedAt'] = date('Y-m-d H:i:s');
            $lead['fromRunId'] = $runId;
            $indexed[$key] = $lead;
            $added++;
        }
    }

    writeLeadsDb(array_values($indexed));
    echo json_encode(['success' => true, 'saved' => $added, 'total' => count($indexed)]);
}

// ── GET LEADS DB ──────────────────────────────────────────
function handleLeads() {
    if (!file_exists(LEADS_DB)) { echo json_encode(['leads' => [], 'total' => 0, 'withEmail' => 0]); return; }

    $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];

    $filterState    = $_GET['state'] ?? '';
    $filterType     = $_GET['type'] ?? '';       // Restaurant / Hotel / Things to Do
    $filterSegment  = $_GET['segment'] ?? '';    // s1..s4 / mid
    $filterTier     = $_GET['tier'] ?? '';       // tourist / nontourist / other
    $filterSearch   = strtolower($_GET['search'] ?? '');

    if ($filterState || $filterType || $filterSegment || $filterTier || $filterSearch) {
        $db = array_filter($db, function($l) use ($filterState, $filterType, $filterSegment, $filterTier, $filterSearch) {
            if ($filterState && stripos($l['state'] ?? '', $filterState) === false) return false;
            if ($filterType && stripos($l['contentType'] ?? '', $filterType) === false) return false;
            if ($filterSegment && ($l['segment'] ?? '') !== $filterSegment) return false;
            if ($filterTier && ($l['cityTier'] ?? '') !== $filterTier) return false;
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
    $totalRuns = 0; $totalLeads = 0; $totalEmails = 0; $totalCost = 0;

    foreach ($files as $file) {
        $meta = json_decode(file_get_contents($file), true);
        if (!$meta) continue;
        $totalRuns++;
        $totalLeads += (int)($meta['resultsCount'] ?? 0);
        $totalEmails += (int)($meta['emailsCount'] ?? 0);
        $totalCost += (float)($meta['costUsd'] ?? 0);
    }

    $dbLeads = 0; $dbEmails = 0; $dbUnique = 0;
    $seg = ['s1' => 0, 's2' => 0, 's3' => 0, 's4' => 0, 'mid' => 0];
    if (file_exists(LEADS_DB)) {
        $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];
        $dbLeads = count($db);
        $dbEmails = count(array_filter($db, fn($l) => !empty($l['email'])));
        $uniq = [];
        foreach ($db as $l) {
            $s = $l['segment'] ?? 'mid';
            if (isset($seg[$s])) $seg[$s]++;
            $em = strtolower(trim($l['email'] ?? ''));
            if ($em !== '') $uniq[$em] = 1;
        }
        $dbUnique = count($uniq);
    }

    echo json_encode([
        'totalRuns' => $totalRuns,
        'totalLeads' => max($totalLeads, $dbLeads),
        'totalEmails' => max($totalEmails, $dbEmails),
        'totalCostUsd' => round($totalCost, 2),
        'avgCostPerRun' => $totalRuns > 0 ? round($totalCost / $totalRuns, 2) : 0,
        'dbLeads' => $dbLeads,
        'dbEmails' => $dbEmails,
        'dbUniqueEmails' => $dbUnique,
        'segments' => $seg,
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
    if (file_exists(LEADS_DB)) $db = json_decode(file_get_contents(LEADS_DB), true) ?: [];

    $indexed = [];
    foreach ($db as $lead) {
        $key = dedupKey($lead);
        $indexed[$key] = $lead;
    }

    foreach ($newLeads as $lead) {
        $key = dedupKey($lead);
        if (!isset($indexed[$key])) {
            $lead['savedAt'] = date('Y-m-d H:i:s');
            $lead['fromRunId'] = $runId;
            $indexed[$key] = $lead;
        }
    }

    writeLeadsDb(array_values($indexed));
}

function writeLeadsDb($rows) {
    $fp = fopen(LEADS_DB, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    flock($fp, LOCK_UN);
    fclose($fp);
}

function dedupKey($lead) {
    $pid = $lead['placeId'] ?? '';
    if ($pid !== '') return 'id_' . $pid;
    return 'nm_' . strtolower(($lead['title'] ?? '') . '_' . ($lead['phone'] ?? ''));
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

/**
 * Map one Tripadvisor dataset item -> lead row.
 * Tripadvisor output uses: name, type, category, addressObj{city,state,postalcode,country},
 * phone, website, webUrl, email, rating, numberOfReviews, priceLevel, id.
 */
function extractLead($item, $runId = '', $runQuery = '') {
    $addr = is_array($item['addressObj'] ?? null) ? $item['addressObj'] : [];
    $city    = $addr['city'] ?? '';
    $state   = $addr['state'] ?? '';
    $zip     = $addr['postalcode'] ?? '';
    $country = $addr['country'] ?? '';

    $rating  = isset($item['rating']) ? (float)$item['rating'] : 0;
    $reviews = (int)($item['numberOfReviews'] ?? 0);

    $seg  = classifySegment($rating, $reviews);
    $tier = classifyCityTier($city, $runQuery);

    $type = strtoupper($item['type'] ?? $item['category'] ?? '');
    $contentType = 'Place';
    if (strpos($type, 'HOTEL') !== false)       $contentType = 'Hotel';
    elseif (strpos($type, 'RESTAURANT') !== false) $contentType = 'Restaurant';
    elseif (strpos($type, 'ATTRACTION') !== false) $contentType = 'Things to Do';

    return [
        'title'            => $item['name'] ?? '',
        'contentType'      => $contentType,
        'categoryName'     => $item['category'] ?? '',
        'subcategories'    => $item['subcategories'] ?? [],
        'address'          => $item['address'] ?? '',
        'city'             => $city,
        'state'            => $state,
        'zipCode'          => $zip,
        'country'          => $country,
        'phone'            => $item['phone'] ?? '',
        'website'          => $item['website'] ?? '',
        'email'            => extractEmail($item),
        'rating'           => $rating,
        'reviewsCount'     => $reviews,
        'ratingHistogram'  => $item['ratingHistogram'] ?? null,
        'priceLevel'       => $item['priceLevel'] ?? '',
        'priceRange'       => $item['priceRange'] ?? '',
        'rankingString'    => $item['rankingString'] ?? '',
        'hotelClass'       => $item['hotelClass'] ?? '',
        'url'              => $item['webUrl'] ?? '',
        'placeId'          => (string)($item['id'] ?? ''),
        'latitude'         => $item['latitude'] ?? null,
        'longitude'        => $item['longitude'] ?? null,
        'image'            => $item['image'] ?? '',
        'segment'          => $seg[0],
        'segmentLabel'     => $seg[1],
        'cityTier'         => $tier,
    ];
}

/**
 * Rating x review segmentation — exhaustive 4-way (no Mid).
 * Single cutoffs: rating >= 4.0 = high, reviews >= 50 = high.
 * Every lead lands in exactly one of the 4 quadrants.
 */
function classifySegment($rating, $reviews) {
    $r = (float)$rating; $n = (int)$reviews;
    $hiR = $r >= 4.0; $hiN = $n >= 50;
    if (!$hiR && !$hiN) return ['s1', 'Low Rating / Low Review'];
    if (!$hiR && $hiN)  return ['s2', 'Low Rating / High Review'];
    if ($hiR && !$hiN)  return ['s3', 'High Rating / Low Review'];
    return ['s4', 'High Rating / High Review'];
}

/**
 * Tourist vs Non-Tourist target-city classification.
 * Uses the lead's own city first, then falls back to the run's query city.
 */
function classifyCityTier($city, $query = '') {
    $c = strtolower(trim($city));
    foreach ($GLOBALS['TOURIST_CITIES'] as $t)    if ($c !== '' && strpos($c, $t) !== false) return 'tourist';
    foreach ($GLOBALS['NONTOURIST_CITIES'] as $t) if ($c !== '' && strpos($c, $t) !== false) return 'nontourist';
    $q = strtolower(trim($query));
    foreach ($GLOBALS['TOURIST_CITIES'] as $t)    if ($q !== '' && strpos($q, $t) !== false) return 'tourist';
    foreach ($GLOBALS['NONTOURIST_CITIES'] as $t) if ($q !== '' && strpos($q, $t) !== false) return 'nontourist';
    return 'other';
}

/**
 * Email extraction. Base Tripadvisor data rarely has an email; the paid leads
 * enrichment add-on attaches contact records. We check the base field first,
 * then scan enrichment/contact subtrees for the first valid email.
 */
function extractEmail($item) {
    if (!empty($item['email']) && is_string($item['email']) && strpos($item['email'], '@') !== false) {
        return $item['email'];
    }
    if (!empty($item['emails']) && is_array($item['emails'])) {
        foreach ($item['emails'] as $e) if (is_string($e) && strpos($e, '@') !== false) return $e;
    }
    foreach ($item as $k => $v) {
        if (preg_match('/lead|contact|enrich|email/i', $k) && (is_array($v) || is_string($v))) {
            $e = firstEmail($v, 0);
            if ($e !== '') return $e;
        }
    }
    return '';
}

function firstEmail($node, $depth) {
    if ($depth > 5) return '';
    if (is_string($node)) {
        return preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $node, $m) ? $m[0] : '';
    }
    if (is_array($node)) {
        foreach ($node as $v) { $e = firstEmail($v, $depth + 1); if ($e !== '') return $e; }
    }
    return '';
}
