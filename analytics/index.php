<?php
/**
 * Smart Buzzer - Centralized Analytics Dashboard
 * Version: 1.2
 * 
 * Features:
 * - Multi-source data aggregation (7 default landing pages)
 * - Remote log fetching (customer_data.log, page_analytics.log, order_clicks.log)
 * - Backward compatible format detection (14-17 cols for customer_data, 11-13 cols for page_analytics)
 * - Timeframe filtering (default: Last 30 Days)
 * - Campaign/Ad/Source/Placement/Landing Page breakdown
 * - Full AJAX (no page refresh)
 * - Password protection
 * - Auto-creates default sources on first load
 * 
 * Default Sources: promo, promo-2, promo-b1g1, promo-rating, promo-australia, xmas, blackfriday
 */

session_start();

// ============================================
// CONFIGURATION
// ============================================
$PASSWORD = 'smartbuzzer2025';
$SOURCES_DIR = __DIR__ . '/sources';

// Filesystem base path (same cPanel - no HTTP needed)
$BASE_PATH = '/home/u387681977/domains/smart-buzzer.com/public_html';

// Default landing pages
$DEFAULT_LANDING_PAGES = [
    'promo',
    'promo-2',
    'promo-outbound',
    'promo-b1g1',
    'promo-rating',
    'promo-australia',
    'promo-california',
    'promo-tripadvisor',
    'promo-trustpilot',
    'promo-industry',
    'seasonal',
    'xmas',
    'blackfriday'
];

// ============================================
// AUTHENTICATION
// ============================================
if (isset($_POST['logout'])) {
    unset($_SESSION['analytics_authenticated']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['analytics_authenticated'] = true;
    } else {
        $loginError = 'Invalid password';
    }
}

$isAuthenticated = isset($_SESSION['analytics_authenticated']) && $_SESSION['analytics_authenticated'] === true;

// ============================================
// AJAX HANDLERS
// ============================================
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Check auth for AJAX
    if (!$isAuthenticated) {
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    
    $action = $_GET['action'];
    
    // Add source
    if ($action === 'add_source' && isset($_POST['name']) && isset($_POST['url'])) {
        $sources = loadSources($SOURCES_DIR);
        $name = trim($_POST['name']);
        $url = rtrim(trim($_POST['url']), '/') . '/';
        
        if (empty($name) || empty($url)) {
            echo json_encode(['error' => 'Name and URL are required']);
            exit;
        }
        
        // Check if name already exists
        foreach ($sources as $s) {
            if ($s['name'] === $name) {
                echo json_encode(['error' => 'Source name already exists']);
                exit;
            }
        }
        
        // Add path based on name (same cPanel)
        $path = $BASE_PATH . '/' . $name;
        $newSource = ['name' => $name, 'url' => $url, 'path' => $path, 'enabled' => true];
        saveSource($SOURCES_DIR, $newSource);
        $sources = loadSources($SOURCES_DIR);
        echo json_encode(['success' => true, 'sources' => $sources]);
        exit;
    }
    
    // Remove source
    if ($action === 'remove_source' && isset($_POST['name'])) {
        $name = trim($_POST['name']);
        deleteSource($SOURCES_DIR, $name);
        $sources = loadSources($SOURCES_DIR);
        echo json_encode(['success' => true, 'sources' => $sources]);
        exit;
    }
    
    // Toggle source enabled/disabled
    if ($action === 'toggle_source' && isset($_POST['name']) && isset($_POST['enabled'])) {
        $name = trim($_POST['name']);
        $enabled = $_POST['enabled'] === 'true' || $_POST['enabled'] === '1';
        toggleSource($SOURCES_DIR, $name, $enabled);
        $sources = loadSources($SOURCES_DIR);
        echo json_encode(['success' => true, 'sources' => $sources]);
        exit;
    }
    
    // Test source
    if ($action === 'test_source' && isset($_POST['name'])) {
        $name = trim($_POST['name']);
        $sources = loadSources($SOURCES_DIR);
        $path = null;
        
        // Find source by name
        foreach ($sources as $s) {
            if ($s['name'] === $name) {
                $path = isset($s['path']) ? $s['path'] : $BASE_PATH . '/' . $name;
                break;
            }
        }
        
        if (!$path) {
            // Fallback: construct path from name
            $path = $BASE_PATH . '/' . $name;
        }
        
        $result = testSource($path);
        echo json_encode($result);
        exit;
    }
    
    // Delete customer entries
    if ($action === 'delete_customers') {
        $entries = isset($_POST['entries']) ? json_decode($_POST['entries'], true) : [];

        if (empty($entries) || !is_array($entries)) {
            echo json_encode(['error' => 'No entries specified']);
            exit;
        }

        // Group entries by source_name
        $bySource = [];
        foreach ($entries as $entry) {
            $src = $entry['source_name'] ?? '';
            if ($src) {
                $bySource[$src][] = $entry;
            }
        }

        $sources = loadSources($SOURCES_DIR);
        $deleted = 0;

        foreach ($bySource as $srcName => $entriesToDelete) {
            // Find source path
            $srcPath = null;
            foreach ($sources as $s) {
                if ($s['name'] === $srcName) {
                    $srcPath = isset($s['path']) ? $s['path'] : $BASE_PATH . '/' . $srcName;
                    break;
                }
            }
            if (!$srcPath) $srcPath = $BASE_PATH . '/' . $srcName;

            $logFile = $srcPath . '/customer_data.log';
            if (!file_exists($logFile)) continue;

            $content = @file_get_contents($logFile);
            if ($content === false) continue;

            $lines = explode("\n", $content);
            $newLines = [];

            // Build a set of keys to delete: timestamp + business_name + whatsapp
            $deleteKeys = [];
            foreach ($entriesToDelete as $e) {
                $key = ($e['timestamp'] ?? '') . '|' . ($e['business_name'] ?? '') . '|' . ($e['whatsapp'] ?? '');
                $deleteKeys[$key] = true;
            }

            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                $parts = explode("\t", $line);
                $lineKey = ($parts[0] ?? '') . '|' . ($parts[1] ?? '') . '|' . ($parts[4] ?? '');

                if (isset($deleteKeys[$lineKey])) {
                    $deleted++;
                    unset($deleteKeys[$lineKey]); // Only delete first match per key
                } else {
                    $newLines[] = $line;
                }
            }

            // Write back
            file_put_contents($logFile, implode("\n", $newLines) . (count($newLines) > 0 ? "\n" : ''));
        }

        echo json_encode(['success' => true, 'deleted' => $deleted]);
        exit;
    }

    // Fetch all data
    if ($action === 'fetch_data') {
        $sources = loadSources($SOURCES_DIR);
        $dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : null;
        $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : null;
        $sourceFilter = isset($_POST['source_filter']) ? $_POST['source_filter'] : null;
        
        // Only include enabled sources
        $sources = array_filter($sources, function($s) {
            return isset($s['enabled']) && $s['enabled'] === true;
        });
        $sources = array_values($sources);
        
        // Filter sources if specific source selected
        if ($sourceFilter && $sourceFilter !== 'all') {
            $sources = array_filter($sources, function($s) use ($sourceFilter) {
                return $s['name'] === $sourceFilter;
            });
            $sources = array_values($sources);
        }
        
        $allData = fetchAllData($sources, $dateFrom, $dateTo);
        echo json_encode($allData);
        exit;
    }
    
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function getDefaultSources() {
    global $BASE_PATH, $DEFAULT_LANDING_PAGES;
    $sources = [];
    foreach ($DEFAULT_LANDING_PAGES as $lp) {
        $sources[] = [
            'name' => $lp,
            'path' => $BASE_PATH . '/' . $lp,
            'url' => 'https://smart-buzzer.com/' . $lp . '/',
            'enabled' => true
        ];
    }
    return $sources;
}

function loadSources($dir) {
    global $BASE_PATH, $DEFAULT_LANDING_PAGES;
    $sources = [];

    // Create directory if not exists
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Read all .json files in directory
    $files = glob($dir . '/*.json');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $source = json_decode($content, true);
        if ($source && isset($source['name'])) {
            // Default enabled to true if not set
            if (!isset($source['enabled'])) {
                $source['enabled'] = true;
            }
            // Add path if not set (backward compatibility)
            if (!isset($source['path'])) {
                $source['path'] = $BASE_PATH . '/' . $source['name'];
            }
            // Add url if not set
            if (!isset($source['url'])) {
                $source['url'] = 'https://smart-buzzer.com/' . $source['name'] . '/';
            }
            $sources[] = $source;
        }
    }

    // Self-heal: auto-create JSON for any default landing page that's missing.
    // To permanently remove a default LP, remove it from $DEFAULT_LANDING_PAGES
    // (Remove Source button alone will be undone on next page load).
    $existingNames = array_column($sources, 'name');
    foreach ($DEFAULT_LANDING_PAGES as $lp) {
        if (!in_array($lp, $existingNames, true)) {
            $newSource = [
                'name'    => $lp,
                'path'    => $BASE_PATH . '/' . $lp,
                'url'     => 'https://smart-buzzer.com/' . $lp . '/',
                'enabled' => true
            ];
            saveSource($dir, $newSource);
            $sources[] = $newSource;
        }
    }

    return $sources;
}

function saveSource($dir, $source) {
    // Create directory if not exists
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $source['name']);
    $filepath = $dir . '/' . $filename . '.json';
    
    file_put_contents($filepath, json_encode($source, JSON_PRETTY_PRINT));
    return $filepath;
}

function deleteSource($dir, $name) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    $filepath = $dir . '/' . $filename . '.json';
    
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    }
    return false;
}

function toggleSource($dir, $name, $enabled) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    $filepath = $dir . '/' . $filename . '.json';
    
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        $source = json_decode($content, true);
        $source['enabled'] = $enabled;
        file_put_contents($filepath, json_encode($source, JSON_PRETTY_PRINT));
        return true;
    }
    return false;
}

function testSource($basePath) {
    // Use filesystem path directly (faster, no 403 issues)
    $customerDataPath = $basePath . '/customer_data.log';
    $pageAnalyticsPath = $basePath . '/page_analytics.log';
    $orderClicksPath = $basePath . '/order_clicks.log';
    
    $result = [
        'customer_data' => false,
        'page_analytics' => false,
        'order_clicks' => false,
        'customer_data_count' => 0,
        'page_analytics_count' => 0,
        'order_clicks_count' => 0
    ];
    
    // Test customer_data.log (14-17 columns - pricing clicks)
    if (file_exists($customerDataPath)) {
        $result['customer_data'] = true;
        $lines = @file($customerDataPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $result['customer_data_count'] = $lines ? count($lines) : 0;
    }
    
    // Test page_analytics.log (11-13 columns - behavior analytics)
    if (file_exists($pageAnalyticsPath)) {
        $result['page_analytics'] = true;
        $lines = @file($pageAnalyticsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $result['page_analytics_count'] = $lines ? count($lines) : 0;
    }
    
    // Test order_clicks.log (7 columns - simple click tracking)
    if (file_exists($orderClicksPath)) {
        $result['order_clicks'] = true;
        $lines = @file($orderClicksPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $result['order_clicks_count'] = $lines ? count($lines) : 0;
    }
    
    $result['success'] = $result['customer_data'] || $result['page_analytics'] || $result['order_clicks'];
    return $result;
}

function fetchAllData($sources, $dateFrom = null, $dateTo = null) {
    global $BASE_PATH;
    $allClicks = [];
    $sourceStats = [];
    
    foreach ($sources as $source) {
        $sourceName = $source['name'];
        // Use filesystem path (faster, no 403 issues)
        $basePath = isset($source['path']) ? $source['path'] : $BASE_PATH . '/' . $sourceName;
        
        $sourceStats[$sourceName] = [
            'status' => 'error',
            'submissions' => 0,
            'error' => null
        ];
        
        // Fetch customer_data.log (supports 14-17 columns)
        // OLD FORMAT: 14-15 cols (without utm_content, placement)
        // NEW FORMAT: 17 cols (with utm_content, placement, status)
        $customerDataPath = $basePath . '/customer_data.log';
        
        if (file_exists($customerDataPath)) {
            $content = @file_get_contents($customerDataPath);
            
            if ($content !== false) {
                $sourceStats[$sourceName]['status'] = 'ok';
                $lines = array_filter(explode("\n", trim($content)));
                
                foreach ($lines as $line) {
                    $parts = explode("\t", $line);
                    $numParts = count($parts);
                    
                    // Support both NEW format (17 cols) and OLD format (14-15 cols)
                    if ($numParts >= 14) {
                        $timestamp = $parts[0];
                        $package = $parts[5];
                        $pageUrl = $parts[6];
                        
                        // Parse date for filtering
                        $date = substr($timestamp, 0, 10);
                        
                        // Apply date filter
                        if ($dateFrom && $date < $dateFrom) continue;
                        if ($dateTo && $date > $dateTo) continue;
                        
                        // Detect format and get UTM + country
                        if ($numParts >= 16) {
                            // NEW FORMAT (17+ columns)
                            // 0:Timestamp|1:Business|2:Location|3:Email|4:WhatsApp|5:Package|6:PageURL|7:ReviewsQty|8:UTM_Source|9:UTM_Medium|10:UTM_Campaign|11:UTM_Content|12:Placement|13:State|14:Zip|15:Country|16:Status|17:UTM_Term
                            $utmSource = $parts[8];
                            $utmMedium = $parts[9];
                            $utmCampaign = $parts[10];
                            $utmContent = $parts[11];
                            $placement = $parts[12];
                            $country = $parts[15];
                            $utmTerm = ($numParts >= 18) ? $parts[17] : '-';
                        } else {
                            // OLD FORMAT (14-15 columns) - parse UTM from URL
                            $utmData = parseUtmFromUrl($pageUrl);
                            $utmSource = $utmData['utm_source'];
                            $utmMedium = $utmData['utm_medium'];
                            $utmCampaign = $utmData['utm_campaign'];
                            $utmContent = $utmData['utm_content'];
                            $utmTerm = $utmData['utm_term'];
                            $placement = $utmData['placement'];
                            $country = $parts[13];
                        }
                        
                        // Skip Indonesia
                        if ($country === 'ID') continue;

                        // Skip CLICK_ONLY entries - only count form submissions (v3.1)
                        if ($numParts >= 17) {
                            $status = trim($parts[16]);
                            if ($status === 'CLICK_ONLY') continue;
                        }

                        // Extract customer fields for customer list
                        $businessName = isset($parts[1]) ? $parts[1] : '-';
                        $email = isset($parts[3]) ? $parts[3] : '-';
                        $whatsapp = isset($parts[4]) ? $parts[4] : '-';
                        $reviewsQty = isset($parts[7]) ? $parts[7] : '-';
                        $state = ($numParts >= 14) ? $parts[13] : '-';
                        $zip = ($numParts >= 15) ? $parts[14] : '-';
                        $status = ($numParts >= 17) ? trim($parts[16]) : 'FORM_SUBMIT';

                        $allClicks[] = [
                            'timestamp' => $timestamp,
                            'date' => $date,
                            'source_name' => $sourceName,
                            'event' => 'ORDER_' . strtoupper($package) . '_CLICK',
                            'package' => $package,
                            'page_url' => $pageUrl,
                            'utm_source' => $utmSource,
                            'utm_medium' => $utmMedium,
                            'utm_campaign' => $utmCampaign,
                            'utm_content' => $utmContent,
                            'utm_term' => $utmTerm,
                            'placement' => $placement,
                            'country' => $country,
                            'business_name' => $businessName,
                            'email' => $email,
                            'whatsapp' => $whatsapp,
                            'reviews_qty' => $reviewsQty,
                            'state' => $state,
                            'zip' => $zip,
                            'status' => $status
                        ];
                        
                        $sourceStats[$sourceName]['submissions']++;
                    }
                }
            } else {
                $sourceStats[$sourceName]['error'] = 'Could not read log file';
            }
        } else {
            $sourceStats[$sourceName]['error'] = 'Log file not found';
        }
    }

    // Read page_analytics.log from each source for funnel metrics
    $totalPageViews = 0;
    $uniqueIPs = [];

    foreach ($sources as $source) {
        $sourceName = $source['name'];
        $basePath = isset($source['path']) ? $source['path'] : $BASE_PATH . '/' . $sourceName;
        $pageAnalyticsPath = $basePath . '/page_analytics.log';

        if (file_exists($pageAnalyticsPath)) {
            $paContent = @file_get_contents($pageAnalyticsPath);
            if ($paContent !== false) {
                $paLines = array_filter(explode("\n", trim($paContent)));
                foreach ($paLines as $paLine) {
                    $paParts = explode("\t", $paLine);
                    if (count($paParts) >= 6) {
                        $paEvent = isset($paParts[1]) ? trim($paParts[1]) : '';
                        if ($paEvent !== 'PAGE_VIEW') continue;

                        // Date filter
                        $paDate = substr($paParts[0], 0, 10);
                        if ($dateFrom && $paDate < $dateFrom) continue;
                        if ($dateTo && $paDate > $dateTo) continue;

                        // Skip Indonesia
                        if (count($paParts) >= 13) {
                            $paCountry = trim($paParts[12]);
                            if ($paCountry === 'ID') continue;
                        }

                        $totalPageViews++;
                        // Unique visitor key: prefer IP (col 11), fallback to Session ID (col 5)
                        // Rationale: IP often logged as '-' when ip-api lookup fails on hosted PHP,
                        // causing Qualified Leads to be drastically undercounted (can drop below MBP).
                        // Session ID is always populated per page load, giving a safer unique-visitor proxy.
                        $visitorKey = '';
                        if (count($paParts) >= 12) {
                            $ip = trim($paParts[11]);
                            if (!empty($ip) && $ip !== '-') {
                                $visitorKey = 'ip:' . $ip;
                            }
                        }
                        if ($visitorKey === '' && count($paParts) >= 6) {
                            $sess = trim($paParts[5]);
                            if (!empty($sess) && $sess !== '-') {
                                $visitorKey = 'ss:' . $sess;
                            }
                        }
                        if ($visitorKey !== '') {
                            $uniqueIPs[$visitorKey] = true;
                        }
                    }
                }
            }
        }
    }

    // Deduplicate Meet Buying Power by email
    $uniqueEmails = [];
    foreach ($allClicks as $click) {
        $email = trim($click['email']);
        if (!empty($email) && $email !== '-') {
            $uniqueEmails[$email] = true;
        }
    }

    // Aggregate data
    $aggregated = aggregateData($allClicks);
    $aggregated['source_stats'] = $sourceStats;
    $aggregated['total_clicks'] = count($allClicks);
    $aggregated['total_page_views'] = $totalPageViews;
    $aggregated['unique_sessions'] = count($uniqueIPs);
    $aggregated['unique_submissions'] = count($uniqueEmails);

    // Build customer list (sorted by timestamp descending)
    usort($allClicks, function($a, $b) {
        return strcmp($b['timestamp'], $a['timestamp']);
    });
    $aggregated['customers'] = $allClicks;

    return $aggregated;
}

function parseUtmFromUrl($url) {
    $result = [
        'utm_source' => 'direct',
        'utm_medium' => 'none',
        'utm_campaign' => 'direct',
        'utm_content' => '-',
        'utm_term' => '-',
        'placement' => '-'
    ];

    $parsed = parse_url($url);
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $params);

        if (isset($params['utm_source']) && !empty($params['utm_source'])) {
            $result['utm_source'] = $params['utm_source'];
        }
        if (isset($params['utm_medium']) && !empty($params['utm_medium'])) {
            $result['utm_medium'] = $params['utm_medium'];
        }
        if (isset($params['utm_campaign']) && !empty($params['utm_campaign'])) {
            $result['utm_campaign'] = $params['utm_campaign'];
        }
        if (isset($params['utm_content']) && !empty($params['utm_content'])) {
            $result['utm_content'] = $params['utm_content'];
        }
        if (isset($params['utm_term']) && !empty($params['utm_term'])) {
            $result['utm_term'] = $params['utm_term'];
        }
        // Check utm_placement first, fallback to placement
        if (isset($params['utm_placement']) && !empty($params['utm_placement'])) {
            $result['placement'] = $params['utm_placement'];
        } elseif (isset($params['placement']) && !empty($params['placement'])) {
            $result['placement'] = $params['placement'];
        }
    }
    
    return $result;
}

function aggregateData($clicks) {
    $campaigns = [];      // UTM Campaign (Campaign Name)
    $ads = [];            // UTM Content (Ad Name)
    $sources = [];        // UTM Source
    $placements = [];     // Placement
    $landingPages = [];   // Source Name (LP)
    $dailyClicks = [];    // Daily clicks for chart
    $dailyClicksPerLP = []; // Daily clicks per LP for trend chart

    foreach ($clicks as $click) {
        $campaign = trim($click['utm_campaign']);
        $content = trim($click['utm_content']);
        $source = trim($click['utm_source']);
        $placement = trim($click['placement']);
        $lpName = $click['source_name'];

        // Track daily clicks
        $date = $click['date'];
        if (!isset($dailyClicks[$date])) {
            $dailyClicks[$date] = 0;
        }
        $dailyClicks[$date]++;

        // Track daily clicks per LP
        if (!isset($dailyClicksPerLP[$lpName])) {
            $dailyClicksPerLP[$lpName] = [];
        }
        if (!isset($dailyClicksPerLP[$lpName][$date])) {
            $dailyClicksPerLP[$lpName][$date] = 0;
        }
        $dailyClicksPerLP[$lpName][$date]++;

        // Campaigns (UTM Campaign) — primary grouping
        if (!empty($campaign) && $campaign !== 'direct' && $campaign !== '-') {
            if (!isset($campaigns[$campaign])) {
                $campaigns[$campaign] = [
                    'clicks' => 0,
                    'ads' => [],
                    'lps' => [],
                    'sources' => [],
                    'placements' => []
                ];
            }
            $campaigns[$campaign]['clicks']++;

            // Track ads under this campaign
            if (!empty($content) && $content !== '-') {
                if (!isset($campaigns[$campaign]['ads'][$content])) {
                    $campaigns[$campaign]['ads'][$content] = 0;
                }
                $campaigns[$campaign]['ads'][$content]++;
            }

            // Track LPs
            if (!in_array($lpName, $campaigns[$campaign]['lps'])) {
                $campaigns[$campaign]['lps'][] = $lpName;
            }

            // Track sources per campaign (for cross-filtering)
            if (!empty($source) && $source !== 'direct' && $source !== '-') {
                if (!isset($campaigns[$campaign]['sources'][$source])) {
                    $campaigns[$campaign]['sources'][$source] = 0;
                }
                $campaigns[$campaign]['sources'][$source]++;
            }

            // Track placements per campaign (for cross-filtering)
            if (!empty($placement) && $placement !== '-') {
                if (!isset($campaigns[$campaign]['placements'][$placement])) {
                    $campaigns[$campaign]['placements'][$placement] = 0;
                }
                $campaigns[$campaign]['placements'][$placement]++;
            }
        }

        // Ads (UTM Content) - global
        if (!empty($content) && $content !== '-') {
            if (!isset($ads[$content])) {
                $ads[$content] = ['clicks' => 0, 'campaign' => $campaign];
            }
            $ads[$content]['clicks']++;
        }

        // Sources (UTM Source)
        if (!empty($source) && $source !== 'direct' && $source !== '-') {
            if (!isset($sources[$source])) {
                $sources[$source] = 0;
            }
            $sources[$source]++;
        }

        // Placements
        if (!empty($placement) && $placement !== '-') {
            if (!isset($placements[$placement])) {
                $placements[$placement] = 0;
            }
            $placements[$placement]++;
        }

        // Landing Pages
        if (!isset($landingPages[$lpName])) {
            $landingPages[$lpName] = 0;
        }
        $landingPages[$lpName]++;
    }

    // Sort all by clicks descending
    uasort($campaigns, function($a, $b) { return $b['clicks'] - $a['clicks']; });
    uasort($ads, function($a, $b) { return $b['clicks'] - $a['clicks']; });
    arsort($sources);
    arsort($placements);
    arsort($landingPages);

    // Sort ads within each campaign
    foreach ($campaigns as &$camp) {
        arsort($camp['ads']);
        arsort($camp['sources']);
        arsort($camp['placements']);
    }

    // Sort daily clicks by date ascending
    ksort($dailyClicks);

    // Sort daily clicks per LP by date ascending
    foreach ($dailyClicksPerLP as &$lpData) {
        ksort($lpData);
    }

    return [
        'campaigns' => $campaigns,
        'ads' => $ads,
        'sources' => $sources,
        'placements' => $placements,
        'landing_pages' => $landingPages,
        'daily_clicks' => $dailyClicks,
        'daily_clicks_per_lp' => $dailyClicksPerLP,
        'unique_campaigns' => count($campaigns),
        'unique_placements' => count($placements)
    ];
}

// Load sources for initial page
$sources = loadSources($SOURCES_DIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centralized Analytics - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <style>
        :root {
            --blue-primary: #0066FF;
            --blue-hover: #0052CC;
            --blue-light: #E6F2FF;
            --green-success: #00875A;
            --green-light: #E3FCEF;
            --red-warning: #DE350B;
            --orange-highlight: #FF991F;
            --orange-light: #FFF4E6;
            --purple: #6554C0;
            --purple-light: #EAE6FF;
            --background: #F7F8FA;
            --card-bg: #FFFFFF;
            --border: #DFE1E6;
            --text-primary: #172B4D;
            --text-secondary: #5E6C84;
            --text-muted: #8993A4;
            --shadow-sm: 0 1px 3px rgba(9, 30, 66, 0.12);
            --shadow-md: 0 4px 8px rgba(9, 30, 66, 0.15);
            --gold: #FFD700;
            --gold-bg: #FFF9E6;
            --silver: #C0C0C0;
            --silver-bg: #F7F8FA;
            --bronze: #CD7F32;
            --bronze-bg: #FDF4EC;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.5;
        }
        
        /* Login Form */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-box {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-box h1 {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .login-box p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }
        
        .login-box input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 16px;
        }
        
        .login-box input[type="password"]:focus {
            outline: none;
            border-color: var(--blue-primary);
            box-shadow: 0 0 0 3px var(--blue-light);
        }
        
        .login-box button {
            width: 100%;
            padding: 12px 24px;
            background: var(--blue-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .login-box button:hover {
            background: var(--blue-hover);
        }
        
        .login-error {
            color: var(--red-warning);
            margin-bottom: 16px;
            padding: 12px;
            background: #FFEBE6;
            border-radius: 8px;
        }
        
        /* Main Layout */
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--blue-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--blue-hover);
        }
        
        .btn-secondary {
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--background);
        }
        
        .btn-danger {
            background: var(--red-warning);
            color: white;
        }
        
        .btn-danger:hover {
            background: #BF2600;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Timeframe Picker */
        .timeframe-picker {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }
        
        .timeframe-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 12px;
        }
        
        .timeframe-quick {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .timeframe-quick button {
            padding: 8px 16px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .timeframe-quick button:hover,
        .timeframe-quick button.active {
            background: var(--blue-primary);
            color: white;
            border-color: var(--blue-primary);
        }
        
        .timeframe-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .timeframe-custom input[type="date"] {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
        }
        
        .timeframe-custom input[type="date"]:focus {
            outline: none;
            border-color: var(--blue-primary);
        }
        
        /* Source Filter */
        .source-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .source-filter label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
        }
        
        .source-filter select {
            padding: 10px 16px;
            border: 2px solid var(--blue-primary);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            background: var(--blue-light);
            color: var(--blue-primary);
            cursor: pointer;
            min-width: 160px;
            transition: all 0.2s;
        }
        
        .source-filter select:hover {
            background: var(--blue-primary);
            color: white;
        }
        
        .source-filter select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.2);
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--blue-primary);
        }
        
        .stat-card.green { border-left-color: var(--green-success); }
        .stat-card.orange { border-left-color: var(--orange-highlight); }
        .stat-card.purple { border-left-color: var(--purple); }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .stat-card .label {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 4px;
        }
        
        .stat-card .sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        
        /* Daily Clicks Chart */
        .chart-section {
            margin-bottom: 24px;
        }
        
        .chart-container {
            position: relative;
            height: 280px;
            padding: 20px;
        }
        
        .chart-canvas-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        #dailyClicksChart {
            width: 100% !important;
            height: 100% !important;
        }
        
        .chart-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: var(--text-muted);
            flex-direction: column;
            gap: 8px;
        }
        
        .chart-empty .icon {
            font-size: 32px;
        }
        
        /* LP Trend Chart */
        .lp-trend-section {
            margin-bottom: 24px;
        }
        
        .lp-trend-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .lp-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }
        
        .lp-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        
        .lp-legend-item:hover {
            background: var(--background);
        }
        
        .lp-legend-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--blue-primary);
        }
        
        .lp-legend-item .legend-color {
            width: 20px;
            height: 3px;
            border-radius: 2px;
        }
        
        .lp-legend-item .legend-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .lp-legend-item.disabled .legend-label {
            color: var(--text-muted);
            text-decoration: line-through;
        }
        
        .lp-trend-canvas-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
            margin-top: 16px;
        }
        
        #lpTrendChart {
            width: 100% !important;
            height: 100% !important;
        }
        
        .lp-trend-tooltip {
            position: absolute;
            background: rgba(23, 43, 77, 0.95);
            color: #fff;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            pointer-events: none;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            display: none;
        }
        
        .lp-trend-tooltip .tooltip-date {
            font-weight: 600;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .lp-trend-tooltip .tooltip-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        
        .lp-trend-tooltip .tooltip-item:last-child {
            margin-bottom: 0;
        }
        
        .lp-trend-tooltip .tooltip-color {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .lp-trend-tooltip .tooltip-name {
            flex: 1;
        }
        
        .lp-trend-tooltip .tooltip-value {
            font-weight: 600;
        }
        
        /* Campaign Breakdown */
        .campaign-section {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .campaign-section {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-body {
            padding: 0;
        }
        
        /* Campaign List */
        .campaign-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .campaign-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .campaign-item:hover {
            background: var(--background);
        }
        
        .campaign-item.active {
            background: var(--blue-light);
            border-left: 3px solid var(--blue-primary);
        }
        
        .campaign-item:last-child {
            border-bottom: none;
        }
        
        .campaign-name {
            font-size: 14px;
            color: var(--text-primary);
            max-width: 70%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .campaign-clicks {
            font-weight: 600;
            color: var(--blue-primary);
        }
        
        /* Campaign Detail */
        .campaign-detail {
            padding: 20px;
        }
        
        .campaign-detail-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .campaign-detail-header .icon {
            font-size: 24px;
        }
        
        .campaign-detail-header h3 {
            font-size: 18px;
            font-weight: 600;
        }
        
        .campaign-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .campaign-stats span {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .campaign-stats .highlight {
            color: var(--blue-primary);
            font-weight: 600;
        }
        
        .ranking-section {
            margin-bottom: 20px;
        }
        
        .ranking-section h4 {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ranking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: var(--background);
        }
        
        .ranking-item.gold {
            background: var(--gold-bg);
        }
        
        .ranking-item.silver {
            background: var(--silver-bg);
        }
        
        .ranking-item.bronze {
            background: var(--bronze-bg);
        }
        
        .ranking-item .rank {
            font-weight: 700;
            margin-right: 12px;
            min-width: 24px;
        }
        
        .ranking-item.gold .rank { color: var(--gold); }
        .ranking-item.silver .rank { color: var(--silver); }
        .ranking-item.bronze .rank { color: var(--bronze); }
        
        .ranking-item .name {
            flex: 1;
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .ranking-item .clicks {
            font-weight: 600;
            color: var(--text-primary);
            margin-left: 12px;
        }
        
        /* Source & Placement & LP Section */
        .source-placement-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .source-placement-section {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .source-placement-section {
                grid-template-columns: 1fr;
            }
        }
        
        .simple-table {
            width: 100%;
        }
        
        .simple-table th,
        .simple-table td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .simple-table th {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            background: var(--background);
        }

        .simple-table td {
            font-size: 14px;
        }

        .simple-table tr:last-child td {
            border-bottom: none;
        }

        .simple-table tr:hover td {
            background: var(--background);
        }

        /* Resizable columns */
        #customerTable { table-layout: fixed; }
        #customerTable th {
            position: relative;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        #customerTable td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .col-resize-handle {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            cursor: col-resize;
            background: transparent;
            z-index: 10;
        }
        .col-resize-handle:hover,
        .col-resize-handle.active {
            background: var(--blue-primary);
            opacity: 0.4;
        }
        body.col-resizing { cursor: col-resize !important; user-select: none !important; }
        body.col-resizing * { cursor: col-resize !important; }
        
        /* Sources Manager */
        .sources-section {
            margin-top: 24px;
        }
        
        .add-source-form {
            display: flex;
            gap: 12px;
            padding: 20px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }
        
        .add-source-form input {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }
        
        .add-source-form input:focus {
            outline: none;
            border-color: var(--blue-primary);
        }
        
        .add-source-form input[name="source_name"] {
            width: 150px;
        }
        
        .add-source-form input[name="source_url"] {
            flex: 1;
            min-width: 250px;
        }
        
        .source-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            gap: 16px;
        }
        
        .source-item:last-child {
            border-bottom: none;
        }
        
        .source-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .source-name {
            font-weight: 600;
            min-width: 100px;
        }
        
        .source-url {
            flex: 1;
            color: var(--text-secondary);
            font-size: 13px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .source-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .source-status.ok {
            color: var(--green-success);
        }
        
        .source-status.error {
            color: var(--red-warning);
        }
        
        .source-actions {
            display: flex;
            gap: 8px;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .loading-overlay.hidden {
            display: none;
        }
        
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--border);
            border-top-color: var(--blue-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--background);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
        
        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 16px 24px;
            background: var(--text-primary);
            color: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            z-index: 1001;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s;
        }
        
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .toast.success {
            background: var(--green-success);
        }
        
        .toast.error {
            background: var(--red-warning);
        }

        /* Tabs */
        .tab-bar {
            display: flex;
            gap: 0;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border);
        }
        .tab-btn {
            padding: 12px 24px;
            border: none;
            background: none;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .tab-btn:hover { color: var(--text-primary); }
        .tab-btn.active {
            color: var(--blue-primary);
            border-bottom-color: var(--blue-primary);
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* Meta-style Date Picker */
        .date-picker-wrapper { position: relative; }
        .date-picker-trigger {
            white-space: nowrap;
            font-size: 13px !important;
            padding: 8px 14px !important;
        }
        .date-picker-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.18);
            z-index: 500;
            min-width: 480px;
            border: 1px solid var(--border);
        }
        .date-picker-dropdown.open { display: block; }
        .dp-layout { display: flex; }
        .dp-presets {
            width: 180px;
            border-right: 1px solid var(--border);
            padding: 8px 0;
            max-height: 380px;
            overflow-y: auto;
        }
        .dp-preset {
            padding: 10px 20px;
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.15s;
        }
        .dp-preset:hover { background: var(--background); }
        .dp-preset.active {
            background: var(--blue-light);
            color: var(--blue-primary);
            font-weight: 600;
        }
        .dp-custom {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dp-custom-row {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .dp-field { flex: 1; }
        .dp-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .dp-field input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }
        .dp-field input[type="date"]:focus {
            outline: none;
            border-color: var(--blue-primary);
            box-shadow: 0 0 0 3px var(--blue-light);
        }
        .dp-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        @media (max-width: 600px) {
            .date-picker-dropdown { min-width: 300px; right: -60px; }
            .dp-layout { flex-direction: column; }
            .dp-presets { width: 100%; border-right: none; border-bottom: 1px solid var(--border); max-height: 200px; }
        }
    </style>
</head>
<body>

<?php if (!$isAuthenticated): ?>
<!-- Login Form -->
<div class="login-container">
    <div class="login-box">
        <h1>🔐 Centralized Analytics</h1>
        <p>Smart Buzzer Dashboard</p>
        
        <?php if (isset($loginError)): ?>
        <div class="login-error"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="password" name="password" placeholder="Enter password" autofocus>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Dashboard -->
<div class="loading-overlay hidden" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<div class="toast" id="toast"></div>

<div class="dashboard">
    <!-- Header -->
    <div class="header">
        <h1>📊 Centralized Analytics</h1>
        <div class="header-actions">
            <!-- Meta-style Date Picker -->
            <div class="date-picker-wrapper">
                <button class="btn btn-secondary date-picker-trigger" id="datePickerTrigger" onclick="toggleDatePicker()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="datePickerLabel">Last 30 Days</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="date-picker-dropdown" id="datePickerDropdown">
                    <div class="dp-layout">
                        <div class="dp-presets">
                            <div class="dp-preset active" data-tf="today">Today</div>
                            <div class="dp-preset" data-tf="yesterday">Yesterday</div>
                            <div class="dp-preset" data-tf="7days">Last 7 days</div>
                            <div class="dp-preset" data-tf="14days">Last 14 days</div>
                            <div class="dp-preset" data-tf="28days">Last 28 days</div>
                            <div class="dp-preset" data-tf="30days">Last 30 days</div>
                            <div class="dp-preset" data-tf="thisweek">This week</div>
                            <div class="dp-preset" data-tf="lastweek">Last week</div>
                            <div class="dp-preset" data-tf="thismonth">This month</div>
                            <div class="dp-preset" data-tf="lastmonth">Last month</div>
                            <div class="dp-preset" data-tf="all">All Time</div>
                        </div>
                        <div class="dp-custom">
                            <div class="dp-custom-row">
                                <div class="dp-field">
                                    <label>From</label>
                                    <input type="date" id="dateFrom">
                                </div>
                                <div class="dp-field">
                                    <label>To</label>
                                    <input type="date" id="dateTo">
                                </div>
                            </div>
                            <div class="dp-actions">
                                <button class="btn btn-secondary btn-sm" onclick="closeDatePicker()">Cancel</button>
                                <button class="btn btn-primary btn-sm" onclick="applyDatePicker()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="source-filter">
                <select id="sourceFilter" onchange="applySourceFilter()">
                    <option value="all">All Sources</option>
                    <?php foreach ($sources as $source): ?>
                    <?php if (isset($source['enabled']) && $source['enabled']): ?>
                    <option value="<?php echo htmlspecialchars($source['name']); ?>"><?php echo htmlspecialchars($source['name']); ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary" onclick="refreshData()">🔄 Refresh</button>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" value="1" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('dashboard', this)">Dashboard</button>
        <button class="tab-btn" onclick="switchTab('customers', this)">Customers</button>
        <button class="tab-btn" onclick="switchTab('sources', this)">Sources</button>
    </div>

    <!-- TAB: Dashboard -->
    <div class="tab-panel active" id="tab-dashboard">

    <!-- Funnel Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-card">
            <div class="value" id="funnelLeads">0</div>
            <div class="label">Leads</div>
            <div class="sub">Total page views (all LPs)</div>
        </div>
        <div class="stat-card green">
            <div class="value" id="funnelQualified">0</div>
            <div class="label">Qualified Leads</div>
            <div class="sub" id="funnelQualifiedRate">0% of leads</div>
        </div>
        <div class="stat-card purple">
            <div class="value" id="funnelMBP">0</div>
            <div class="label">Meet Buying Power</div>
            <div class="sub" id="funnelMBPRate">0% of qualified</div>
        </div>
    </div>

    <!-- Funnel Visual -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">Conversion Funnel</div>
        <div class="card-body" style="padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div style="flex: 1; text-align: center; padding: 20px; background: var(--blue-light); border-radius: 12px; min-width: 150px;">
                    <div style="font-size: 28px; font-weight: 700; color: var(--blue-primary);" id="funnelLeads2">0</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Leads</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Page Views</div>
                </div>
                <div style="font-size: 20px; color: var(--text-muted);">→</div>
                <div style="flex: 1; text-align: center; padding: 20px; background: var(--green-light); border-radius: 12px; min-width: 150px;">
                    <div style="font-size: 28px; font-weight: 700; color: var(--green-success);" id="funnelQualified2">0</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Qualified Leads</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Unique Visitors</div>
                </div>
                <div style="font-size: 20px; color: var(--text-muted);">→</div>
                <div style="flex: 1; text-align: center; padding: 20px; background: var(--purple-light); border-radius: 12px; min-width: 150px;">
                    <div style="font-size: 28px; font-weight: 700; color: var(--purple);" id="funnelMBP2">0</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Meet Buying Power</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Unique Emails</div>
                </div>
            </div>
            <div style="display: flex; justify-content: center; gap: 24px; margin-top: 16px; flex-wrap: wrap;">
                <span style="font-size: 13px; color: var(--text-secondary);" id="funnelRate1">Leads → Qualified: 0%</span>
                <span style="font-size: 13px; color: var(--text-secondary);" id="funnelRate2">Qualified → Submit: 0%</span>
                <span style="font-size: 13px; color: var(--text-secondary);" id="funnelRate3">Leads → Submit: 0%</span>
            </div>
        </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="value" id="totalClicks">0</div>
            <div class="label">Total Submissions</div>
            <div class="sub" id="sourcesCount">from 0 sources</div>
        </div>
        <div class="stat-card orange">
            <div class="value" id="campaignsCount">0</div>
            <div class="label">Campaigns</div>
            <div class="sub">UTM Campaign</div>
        </div>
        <div class="stat-card purple">
            <div class="value" id="placementsCount">0</div>
            <div class="label">Placements</div>
            <div class="sub">FB/IG Placements</div>
        </div>
    </div>
    
    <!-- Daily Clicks Chart -->
    <div class="chart-section">
        <div class="card">
            <div class="card-header">📈 Daily Submissions</div>
            <div class="card-body">
                <div class="chart-container">
                    <div class="chart-canvas-wrapper">
                        <canvas id="dailyClicksChart"></canvas>
                    </div>
                    <div class="chart-empty" id="chartEmpty" style="display: none;">
                        <span class="icon">📊</span>
                        <span>No data for selected period</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- LP Trend Chart (Multi-line) -->
    <div class="lp-trend-section">
        <div class="card">
            <div class="card-header lp-trend-header">
                <span>📊 LP Submissions Trend</span>
                <div class="lp-legend" id="lpLegend">
                    <!-- Legend items will be generated by JavaScript -->
                </div>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div class="lp-trend-canvas-wrapper">
                    <canvas id="lpTrendChart"></canvas>
                    <div class="lp-trend-tooltip" id="lpTrendTooltip"></div>
                </div>
                <div class="chart-empty" id="lpTrendEmpty" style="display: none;">
                    <span class="icon">📊</span>
                    <span>No data for selected period</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Campaign Breakdown -->
    <div class="campaign-section">
        <div class="card">
            <div class="card-header">📢 Campaign (UTM Campaign)</div>
            <div class="card-body">
                <div class="campaign-list" id="campaignList">
                    <div class="empty-state">
                        <div class="icon">📊</div>
                        <h3>No data yet</h3>
                        <p>Add sources and refresh to see campaigns</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">🏆 Winning Breakdown</div>
            <div class="card-body">
                <div class="campaign-detail" id="campaignDetail">
                    <div class="empty-state">
                        <div class="icon">👆</div>
                        <h3>Select a campaign</h3>
                        <p>Click on a campaign to see top ads</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Source & Placement -->
    <div class="source-placement-section">
        <div class="card">
            <div class="card-header">🔗 By Source</div>
            <div class="card-body">
                <table class="simple-table" id="sourceTable">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th style="text-align: right;">Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">📍 By Placement</div>
            <div class="card-body">
                <table class="simple-table" id="placementTable">
                    <thead>
                        <tr>
                            <th>Placement</th>
                            <th style="text-align: right;">Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">🌐 By Landing Page</div>
            <div class="card-body">
                <table class="simple-table" id="lpTable">
                    <thead>
                        <tr>
                            <th>Landing Page</th>
                            <th style="text-align: right;">Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    </div><!-- END TAB: Dashboard -->

    <!-- TAB: Customers -->
    <div class="tab-panel" id="tab-customers">
        <div class="stats-grid" style="margin-bottom: 24px; grid-template-columns: repeat(2, 1fr);">
            <div class="stat-card">
                <div class="value" id="custTabTotal">0</div>
                <div class="label">Total Entries</div>
                <div class="sub">All LP sources</div>
            </div>
            <div class="stat-card green">
                <div class="value" id="custTabUnique">0</div>
                <div class="label">Unique by Email</div>
                <div class="sub" id="custTabUniqueSub">deduped — case-insensitive, skips empty</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" style="justify-content: space-between;">
                <span>👥 All Customers</span>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button class="btn btn-sm" id="deleteSelectedBtn" onclick="deleteSelectedCustomers()" style="display: none; background: #EF4444; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Delete Selected (<span id="selectedCount">0</span>)</button>
                    <select id="campaignFilter" onchange="filterCustomerList()" style="padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; background: white; cursor: pointer; min-width: 180px;">
                        <option value="">All Campaigns</option>
                    </select>
                    <input type="text" id="customerSearch" placeholder="Search business, email, WhatsApp..." oninput="filterCustomerList()" style="padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; width: 280px;">
                    <button class="btn btn-secondary btn-sm" onclick="exportCustomerCSV()">Export CSV</button>
                </div>
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <table class="simple-table" id="customerTable">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllCustomers" onchange="toggleSelectAllCustomers(this)" title="Select All"></th>
                            <th>Date</th>
                            <th>Business Name</th>
                            <th>WhatsApp</th>
                            <th>Email</th>
                            <th>Package</th>
                            <th>Reviews</th>
                            <th>LP</th>
                            <th>UTM Campaign</th>
                            <th>UTM Content (Ad)</th>
                            <th>UTM Term (Adset)</th>
                            <th>Placement</th>
                            <th>State</th>
                            <th>Country</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <tr>
                            <td colspan="15" style="text-align: center; color: var(--text-muted);">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div id="customerPagination" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; border-top: 1px solid var(--border);">
                    <span id="customerCount" style="font-size: 13px; color: var(--text-secondary);">0 customers</span>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary btn-sm" id="prevPageBtn" onclick="customerPage(-1)" disabled>Prev</button>
                        <span id="pageInfo" style="font-size: 13px; color: var(--text-secondary); line-height: 30px;">Page 1</span>
                        <button class="btn btn-secondary btn-sm" id="nextPageBtn" onclick="customerPage(1)">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- END TAB: Customers -->

    <!-- TAB: Sources -->
    <div class="tab-panel" id="tab-sources">
    <div class="sources-section" style="margin-top: 0;">
        <div class="card">
            <div class="card-header">⚙️ Data Sources</div>
            <div class="card-body">
                <div class="add-source-form">
                    <input type="text" name="source_name" id="sourceName" placeholder="Name (e.g. promo)">
                    <input type="text" name="source_url" id="sourceUrl" placeholder="Base URL (e.g. https://smart-buzzer.com/promo/)">
                    <button class="btn btn-primary" onclick="addSource()">+ Add Source</button>
                </div>
                
                <div id="sourcesList">
                    <?php if (empty($sources)): ?>
                    <div class="empty-state">
                        <div class="icon">📡</div>
                        <h3>No sources configured</h3>
                        <p>Add a source URL to start aggregating data</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($sources as $source): ?>
                    <div class="source-item" data-name="<?php echo htmlspecialchars($source['name']); ?>">
                        <input type="checkbox" class="source-checkbox" <?php echo (isset($source['enabled']) && $source['enabled']) ? 'checked' : ''; ?> onchange="toggleSourceEnabled('<?php echo htmlspecialchars($source['name']); ?>', this.checked)">
                        <span class="source-name"><?php echo htmlspecialchars($source['name']); ?></span>
                        <span class="source-url"><?php echo htmlspecialchars($source['url']); ?></span>
                        <span class="source-status" id="status-<?php echo htmlspecialchars($source['name']); ?>">
                            ⏳ Checking...
                        </span>
                        <div class="source-actions">
                            <button class="btn btn-secondary btn-sm" onclick="testSourceBtn('<?php echo htmlspecialchars($source['name']); ?>')">Test</button>
                            <button class="btn btn-danger btn-sm" onclick="removeSource('<?php echo htmlspecialchars($source['name']); ?>')">Remove</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div><!-- END TAB: Sources -->
</div>

<script>
// Global data store
let dashboardData = null;
let currentCampaign = null;
let currentTimeframe = 'all';
let dateFrom = null;
let dateTo = null;
let sourceFilter = 'all';
let crossFilterCampaign = null;

// ============================================================================
// TABS
// ============================================================================
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    if (btn) btn.classList.add('active');
    // Re-render charts if switching to dashboard (canvas resize)
    if (tabName === 'dashboard' && dashboardData) {
        setTimeout(() => {
            updateDailyClicksChart();
            updateLpTrendChart();
        }, 50);
    }
}

// ============================================================================
// META-STYLE DATE PICKER
// ============================================================================
function toggleDatePicker() {
    const dd = document.getElementById('datePickerDropdown');
    dd.classList.toggle('open');
}

function closeDatePicker() {
    document.getElementById('datePickerDropdown').classList.remove('open');
}

function applyDatePicker() {
    dateFrom = document.getElementById('dateFrom').value || null;
    dateTo = document.getElementById('dateTo').value || null;
    // Update label
    if (dateFrom && dateTo) {
        document.getElementById('datePickerLabel').textContent = formatDisplayDate(dateFrom) + ' - ' + formatDisplayDate(dateTo);
    }
    // Clear active preset
    document.querySelectorAll('.dp-preset').forEach(p => p.classList.remove('active'));
    closeDatePicker();
    refreshData();
}

function formatDisplayDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
}

// Init date picker preset clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dp-preset').forEach(el => {
        el.addEventListener('click', function() {
            document.querySelectorAll('.dp-preset').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const tf = this.dataset.tf;
            setTimeframe(tf);
            document.getElementById('datePickerLabel').textContent = this.textContent;
            closeDatePicker();
        });
    });

    // Close picker on outside click
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('.date-picker-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeDatePicker();
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Default to Last 30 Days on load
    setTimeframe('30days');
    document.getElementById('datePickerLabel').textContent = 'Last 30 days';
    document.querySelector('.dp-preset[data-tf="30days"]').classList.add('active');

    // Initialize LP Trend tooltip
    initLpTrendTooltip();
});

// Show toast notification
function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000);
}

// Show/hide loading
function setLoading(show) {
    document.getElementById('loadingOverlay').classList.toggle('hidden', !show);
}

// Set timeframe
function fmtDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

function setTimeframe(tf) {
    currentTimeframe = tf;
    const today = new Date();
    const todayStr = fmtDate(today);

    switch(tf) {
        case 'today':
            dateFrom = todayStr; dateTo = todayStr; break;
        case 'yesterday': {
            const d = new Date(today); d.setDate(d.getDate()-1);
            dateFrom = fmtDate(d); dateTo = dateFrom; break;
        }
        case '7days': {
            const d = new Date(today); d.setDate(d.getDate()-7);
            dateFrom = fmtDate(d); dateTo = todayStr; break;
        }
        case '14days': {
            const d = new Date(today); d.setDate(d.getDate()-14);
            dateFrom = fmtDate(d); dateTo = todayStr; break;
        }
        case '28days': {
            const d = new Date(today); d.setDate(d.getDate()-28);
            dateFrom = fmtDate(d); dateTo = todayStr; break;
        }
        case '30days': {
            const d = new Date(today); d.setDate(d.getDate()-30);
            dateFrom = fmtDate(d); dateTo = todayStr; break;
        }
        case 'thisweek': {
            const d = new Date(today);
            const day = d.getDay(); // 0=Sun
            d.setDate(d.getDate() - day);
            dateFrom = fmtDate(d); dateTo = todayStr; break;
        }
        case 'lastweek': {
            const d = new Date(today);
            const day = d.getDay();
            d.setDate(d.getDate() - day - 7);
            dateFrom = fmtDate(d);
            const e = new Date(d); e.setDate(e.getDate()+6);
            dateTo = fmtDate(e); break;
        }
        case 'thismonth':
            dateFrom = todayStr.substring(0,8) + '01'; dateTo = todayStr; break;
        case 'lastmonth': {
            const d = new Date(today.getFullYear(), today.getMonth()-1, 1);
            dateFrom = fmtDate(d);
            const e = new Date(today.getFullYear(), today.getMonth(), 0);
            dateTo = fmtDate(e); break;
        }
        case 'all': default:
            dateFrom = null; dateTo = null; break;
    }

    // Update custom inputs
    document.getElementById('dateFrom').value = dateFrom || '';
    document.getElementById('dateTo').value = dateTo || '';
    
    refreshData();
}

// (applyCustomTimeframe removed - replaced by Meta-style date picker)

// Apply source filter
function applySourceFilter() {
    sourceFilter = document.getElementById('sourceFilter').value;
    refreshData();
}

// Refresh data from all sources
async function refreshData() {
    setLoading(true);
    crossFilterCampaign = null; // Reset cross-filter on data refresh
    
    try {
        const formData = new FormData();
        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);
        if (sourceFilter && sourceFilter !== 'all') formData.append('source_filter', sourceFilter);
        
        const response = await fetch('?action=fetch_data', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error, 'error');
            setLoading(false);
            return;
        }
        
        dashboardData = data;
        updateDashboard();
        showToast('Data refreshed successfully', 'success');
        
    } catch (error) {
        showToast('Failed to fetch data: ' + error.message, 'error');
    }
    
    setLoading(false);
}

// Update dashboard with data
function updateDashboard() {
    if (!dashboardData) return;
    
    // Update stats
    document.getElementById('totalClicks').textContent = dashboardData.total_clicks || 0;
    document.getElementById('campaignsCount').textContent = dashboardData.unique_campaigns || 0;
    document.getElementById('placementsCount').textContent = dashboardData.unique_placements || 0;

    // Update funnel
    const totalPV = dashboardData.total_page_views || 0;
    const uniqueSess = dashboardData.unique_sessions || 0;
    const totalSubs = dashboardData.unique_submissions || 0;

    document.getElementById('funnelLeads').textContent = totalPV.toLocaleString();
    document.getElementById('funnelLeads2').textContent = totalPV.toLocaleString();
    document.getElementById('funnelQualified').textContent = uniqueSess.toLocaleString();
    document.getElementById('funnelQualified2').textContent = uniqueSess.toLocaleString();
    document.getElementById('funnelMBP').textContent = totalSubs.toLocaleString();
    document.getElementById('funnelMBP2').textContent = totalSubs.toLocaleString();

    const qualRate = totalPV > 0 ? ((uniqueSess / totalPV) * 100).toFixed(1) : '0.0';
    const mbpRate = uniqueSess > 0 ? ((totalSubs / uniqueSess) * 100).toFixed(1) : '0.0';
    const overallRate = totalPV > 0 ? ((totalSubs / totalPV) * 100).toFixed(1) : '0.0';

    document.getElementById('funnelQualifiedRate').textContent = qualRate + '% of leads';
    document.getElementById('funnelMBPRate').textContent = mbpRate + '% of qualified';
    document.getElementById('funnelRate1').textContent = 'Leads \u2192 Qualified: ' + qualRate + '%';
    document.getElementById('funnelRate2').textContent = 'Qualified \u2192 Submit: ' + mbpRate + '%';
    document.getElementById('funnelRate3').textContent = 'Leads \u2192 Submit: ' + overallRate + '%';
    
    // Count active sources
    const sourceStats = dashboardData.source_stats || {};
    const activeSources = Object.values(sourceStats).filter(s => s.status === 'ok').length;
    const totalSources = Object.keys(sourceStats).length;
    document.getElementById('sourcesCount').textContent = `from ${activeSources}/${totalSources} sources`;
    
    // Update source statuses
    Object.keys(sourceStats).forEach(name => {
        const statusEl = document.getElementById('status-' + name);
        if (statusEl) {
            const stat = sourceStats[name];
            if (stat.status === 'ok') {
                statusEl.className = 'source-status ok';
                statusEl.textContent = `✅ ${stat.submissions} submissions`;
            } else {
                statusEl.className = 'source-status error';
                statusEl.textContent = `❌ ${stat.error || 'Error'}`;
            }
        }
    });
    
    // Update campaign list
    updateCampaignList();
    
    // Update daily clicks chart
    updateDailyClicksChart();
    
    // Update LP Trend chart (multi-line)
    updateLpLegend();
    updateLpTrendChart();
    
    // Update source table
    updateSourceTable();
    
    // Update placement table
    updatePlacementTable();
    
    // Update LP table
    updateLPTable();

    // Update customer list
    updateCustomerList();
}

// Update campaign list
function updateCampaignList() {
    const campaigns = dashboardData.campaigns || {};
    const listEl = document.getElementById('campaignList');
    
    if (Object.keys(campaigns).length === 0) {
        listEl.innerHTML = `
            <div class="empty-state">
                <div class="icon">📊</div>
                <h3>No campaigns found</h3>
                <p>No data in selected timeframe</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    let first = true;
    
    Object.keys(campaigns).forEach(name => {
        const camp = campaigns[name];
        const lps = camp.lps ? camp.lps.join(', ') : '';
        const isActive = first ? 'active' : '';
        
        html += `
            <div class="campaign-item ${isActive}" onclick="selectCampaign('${escapeHtml(name)}', this)">
                <span class="campaign-name" title="${escapeHtml(name)} (${escapeHtml(lps)})">${escapeHtml(name)} ${lps ? '(' + escapeHtml(lps) + ')' : ''}</span>
                <span class="campaign-clicks">${camp.clicks}</span>
            </div>
        `;
        
        if (first) {
            currentCampaign = name;
            first = false;
        }
    });
    
    listEl.innerHTML = html;
    
    // Show first campaign detail
    if (currentCampaign) {
        updateCampaignDetail(currentCampaign);
    }
}

// Select campaign
function selectCampaign(name, el) {
    currentCampaign = name;
    crossFilterCampaign = name; // Enable cross-filtering
    
    // Update active state
    document.querySelectorAll('.campaign-item').forEach(item => item.classList.remove('active'));
    if (el) el.classList.add('active');
    
    updateCampaignDetail(name);
    
    // Update source and placement tables with cross-filter
    updateSourceTable();
    updatePlacementTable();
    
    // Show filter indicator
    updateCrossFilterIndicator();
}

// Reset cross-filter
function resetCrossFilter() {
    crossFilterCampaign = null;
    updateSourceTable();
    updatePlacementTable();
    updateCrossFilterIndicator();
    showToast('Filter reset - showing all data', 'success');
}

// Update cross-filter indicator
function updateCrossFilterIndicator() {
    const sourceHeader = document.querySelector('#sourceTable').closest('.card').querySelector('.card-header');
    const placementHeader = document.querySelector('#placementTable').closest('.card').querySelector('.card-header');
    
    // Remove existing indicators
    document.querySelectorAll('.cross-filter-badge').forEach(el => el.remove());
    document.querySelectorAll('.reset-filter-btn').forEach(el => el.remove());
    
    if (crossFilterCampaign) {
        const badge = `<span class="cross-filter-badge" style="background: var(--orange-light); color: var(--orange-highlight); padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px;">Filtered: ${escapeHtml(crossFilterCampaign.substring(0, 20))}${crossFilterCampaign.length > 20 ? '...' : ''}</span>`;
        const resetBtn = `<button class="reset-filter-btn" onclick="resetCrossFilter()" style="background: var(--red-warning); color: white; border: none; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; cursor: pointer;">Reset</button>`;
        
        sourceHeader.innerHTML = '🔗 By Source' + badge + resetBtn;
        placementHeader.innerHTML = '📍 By Placement' + badge + resetBtn;
    } else {
        sourceHeader.innerHTML = '🔗 By Source';
        placementHeader.innerHTML = '📍 By Placement';
    }
}

// Update campaign detail
function updateCampaignDetail(name) {
    const campaigns = dashboardData.campaigns || {};
    const camp = campaigns[name];
    
    if (!camp) {
        document.getElementById('campaignDetail').innerHTML = `
            <div class="empty-state">
                <div class="icon">❌</div>
                <h3>Campaign not found</h3>
            </div>
        `;
        return;
    }
    
    // Build ads ranking (UTM Content)
    let adsHtml = '';
    let rank = 1;
    Object.keys(camp.ads).slice(0, 5).forEach(ad => {
        const clicks = camp.ads[ad];
        const rankClass = rank === 1 ? 'gold' : (rank === 2 ? 'silver' : (rank === 3 ? 'bronze' : ''));
        adsHtml += `
            <div class="ranking-item ${rankClass}">
                <span class="rank">${rank}.</span>
                <span class="name" title="${escapeHtml(ad)}">${escapeHtml(ad)}</span>
                <span class="clicks">${clicks} submissions</span>
            </div>
        `;
        rank++;
    });
    
    if (!adsHtml) {
        adsHtml = '<div style="color: var(--text-muted); padding: 12px;">No ads data</div>';
    }
    
    const lps = camp.lps ? camp.lps.join(', ') : '-';
    
    document.getElementById('campaignDetail').innerHTML = `
        <div class="campaign-detail-header">
            <span class="icon">🏆</span>
            <h3>${escapeHtml(name)}</h3>
        </div>
        <div class="campaign-stats">
            <span><span class="highlight">${camp.clicks}</span> submissions</span>
            <span>Sources: ${escapeHtml(lps)}</span>
        </div>
        
        <div class="ranking-section">
            <h4>📝 Top Ads (UTM Content):</h4>
            ${adsHtml}
        </div>
    `;
}

// Update source table
function updateSourceTable() {
    let sources = {};
    
    // Check if cross-filter is active
    if (crossFilterCampaign && dashboardData.campaigns && dashboardData.campaigns[crossFilterCampaign]) {
        sources = dashboardData.campaigns[crossFilterCampaign].sources || {};
    } else {
        sources = dashboardData.sources || {};
    }
    
    const tbody = document.querySelector('#sourceTable tbody');
    
    if (Object.keys(sources).length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td></tr>';
        return;
    }
    
    // Sort by clicks descending
    const sorted = Object.entries(sources).sort((a, b) => b[1] - a[1]);
    
    let html = '';
    sorted.forEach(([name, clicks]) => {
        html += `
            <tr>
                <td>${escapeHtml(name)}</td>
                <td style="text-align: right; font-weight: 600;">${clicks}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Update placement table
function updatePlacementTable() {
    let placements = {};
    
    // Check if cross-filter is active
    if (crossFilterCampaign && dashboardData.campaigns && dashboardData.campaigns[crossFilterCampaign]) {
        placements = dashboardData.campaigns[crossFilterCampaign].placements || {};
    } else {
        placements = dashboardData.placements || {};
    }
    
    const tbody = document.querySelector('#placementTable tbody');
    
    if (Object.keys(placements).length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td></tr>';
        return;
    }
    
    // Sort by clicks descending
    const sorted = Object.entries(placements).sort((a, b) => b[1] - a[1]);
    
    let html = '';
    sorted.forEach(([name, clicks]) => {
        html += `
            <tr>
                <td>${escapeHtml(name)}</td>
                <td style="text-align: right; font-weight: 600;">${clicks}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Update LP table
function updateLPTable() {
    const lps = dashboardData.landing_pages || {};
    const tbody = document.querySelector('#lpTable tbody');
    
    if (Object.keys(lps).length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align: center; color: var(--text-muted);">No data</td></tr>';
        return;
    }
    
    let html = '';
    Object.keys(lps).forEach(name => {
        html += `
            <tr>
                <td>${escapeHtml(name)}</td>
                <td style="text-align: right; font-weight: 600;">${lps[name]}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Add source
async function addSource() {
    const name = document.getElementById('sourceName').value.trim();
    const url = document.getElementById('sourceUrl').value.trim();
    
    if (!name || !url) {
        showToast('Please enter both name and URL', 'error');
        return;
    }
    
    setLoading(true);
    
    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('url', url);
        
        const response = await fetch('?action=add_source', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error, 'error');
        } else {
            showToast('Source added successfully', 'success');
            // Clear inputs
            document.getElementById('sourceName').value = '';
            document.getElementById('sourceUrl').value = '';
            // Reload page to show new source
            location.reload();
        }
        
    } catch (error) {
        showToast('Failed to add source: ' + error.message, 'error');
    }
    
    setLoading(false);
}

// Remove source
async function removeSource(name) {
    if (!confirm(`Remove source "${name}"?`)) return;
    
    setLoading(true);
    
    try {
        const formData = new FormData();
        formData.append('name', name);
        
        const response = await fetch('?action=remove_source', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error, 'error');
        } else {
            showToast('Source removed', 'success');
            location.reload();
        }
        
    } catch (error) {
        showToast('Failed to remove source: ' + error.message, 'error');
    }
    
    setLoading(false);
}

// Toggle source enabled/disabled
async function toggleSourceEnabled(name, enabled) {
    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('enabled', enabled ? 'true' : 'false');
        
        const response = await fetch('?action=toggle_source', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error, 'error');
        } else {
            showToast(`Source "${name}" ${enabled ? 'enabled' : 'disabled'}`, 'success');
            // Refresh data to reflect changes
            refreshData();
        }
        
    } catch (error) {
        showToast('Failed to toggle source: ' + error.message, 'error');
    }
}

// Test source
async function testSourceBtn(name) {
    const statusEl = document.getElementById('status-' + name);
    if (statusEl) {
        statusEl.className = 'source-status';
        statusEl.textContent = '⏳ Testing...';
    }
    
    try {
        const formData = new FormData();
        formData.append('name', name);
        
        const response = await fetch('?action=test_source', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (statusEl) {
            if (data.success) {
                statusEl.className = 'source-status ok';
                statusEl.textContent = '✅ ' + data.customer_data_count + ' submissions';
            } else {
                statusEl.className = 'source-status error';
                statusEl.textContent = '❌ Not found';
            }
        }
        
    } catch (error) {
        if (statusEl) {
            statusEl.className = 'source-status error';
            statusEl.textContent = '❌ Error';
        }
    }
}

// Daily Clicks Chart
let dailyChart = null;

function updateDailyClicksChart() {
    const dailyClicks = dashboardData.daily_clicks || {};
    const canvas = document.getElementById('dailyClicksChart');
    const emptyEl = document.getElementById('chartEmpty');
    const ctx = canvas.getContext('2d');
    
    const dates = Object.keys(dailyClicks);
    const clicks = Object.values(dailyClicks);
    
    if (dates.length === 0) {
        canvas.style.display = 'none';
        emptyEl.style.display = 'flex';
        return;
    }
    
    canvas.style.display = 'block';
    emptyEl.style.display = 'none';
    
    // Clear and resize canvas
    const container = canvas.parentElement;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = container.offsetWidth * dpr;
    canvas.height = 240 * dpr;
    canvas.style.width = container.offsetWidth + 'px';
    canvas.style.height = '240px';
    ctx.scale(dpr, dpr);
    
    const width = container.offsetWidth;
    const height = 240;
    const padding = { top: 20, right: 20, bottom: 50, left: 50 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Calculate max value
    const maxClicks = Math.max(...clicks, 1);
    const yStep = Math.ceil(maxClicks / 5);
    const yMax = yStep * 5;
    
    // Draw grid lines
    ctx.strokeStyle = '#E8E8E8';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = padding.top + (chartH / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(width - padding.right, y);
        ctx.stroke();
    }
    
    // Draw Y axis labels
    ctx.fillStyle = '#8993A4';
    ctx.font = '11px -apple-system, BlinkMacSystemFont, sans-serif';
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';
    for (let i = 0; i <= 5; i++) {
        const val = yMax - (yStep * i);
        const y = padding.top + (chartH / 5) * i;
        ctx.fillText(val.toString(), padding.left - 8, y);
    }
    
    // Calculate bar width
    const barWidth = Math.max(12, Math.min(40, (chartW / dates.length) - 8));
    const barGap = (chartW - (barWidth * dates.length)) / (dates.length + 1);
    
    // Draw bars
    const gradient = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
    gradient.addColorStop(0, '#0066FF');
    gradient.addColorStop(1, '#4D94FF');
    
    dates.forEach((date, i) => {
        const x = padding.left + barGap + i * (barWidth + barGap);
        const barH = (clicks[i] / yMax) * chartH;
        const y = padding.top + chartH - barH;
        
        // Bar
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.roundRect(x, y, barWidth, barH, [4, 4, 0, 0]);
        ctx.fill();
        
        // X axis label (date)
        ctx.fillStyle = '#8993A4';
        ctx.font = '10px -apple-system, BlinkMacSystemFont, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        
        const displayDate = formatChartDate(date);
        ctx.save();
        ctx.translate(x + barWidth / 2, height - padding.bottom + 8);
        ctx.rotate(-45 * Math.PI / 180);
        ctx.fillText(displayDate, 0, 0);
        ctx.restore();
        
        // Value on top of bar
        if (clicks[i] > 0) {
            ctx.fillStyle = '#172B4D';
            ctx.font = 'bold 10px -apple-system, BlinkMacSystemFont, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.fillText(clicks[i].toString(), x + barWidth / 2, y - 4);
        }
    });
}

function formatChartDate(dateStr) {
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${d.getDate()} ${months[d.getMonth()]}`;
}

// LP Trend Chart (Multi-line)
const lpColors = {
    'promo': { line: '#0066FF', bg: 'rgba(0, 102, 255, 0.1)' },
    'promo-2': { line: '#FF6B35', bg: 'rgba(255, 107, 53, 0.1)' },
    'promo-australia': { line: '#00C49A', bg: 'rgba(0, 196, 154, 0.1)' },
    'promo-b1g1': { line: '#9B59B6', bg: 'rgba(155, 89, 182, 0.1)' },
    'promo-rating': { line: '#F39C12', bg: 'rgba(243, 156, 18, 0.1)' },
    'blackfriday': { line: '#2C3E50', bg: 'rgba(44, 62, 80, 0.1)' },
    'xmas': { line: '#C0392B', bg: 'rgba(192, 57, 43, 0.1)' }
};

const defaultColors = [
    { line: '#0066FF', bg: 'rgba(0, 102, 255, 0.1)' },
    { line: '#FF6B35', bg: 'rgba(255, 107, 53, 0.1)' },
    { line: '#00C49A', bg: 'rgba(0, 196, 154, 0.1)' },
    { line: '#9B59B6', bg: 'rgba(155, 89, 182, 0.1)' },
    { line: '#F39C12', bg: 'rgba(243, 156, 18, 0.1)' },
    { line: '#2C3E50', bg: 'rgba(44, 62, 80, 0.1)' },
    { line: '#C0392B', bg: 'rgba(192, 57, 43, 0.1)' },
    { line: '#1ABC9C', bg: 'rgba(26, 188, 156, 0.1)' },
    { line: '#E91E63', bg: 'rgba(233, 30, 99, 0.1)' },
    { line: '#3F51B5', bg: 'rgba(63, 81, 181, 0.1)' }
];

let lpVisibility = {};
let lpTrendData = {};

function getLpColor(lpName, index) {
    if (lpColors[lpName]) return lpColors[lpName];
    return defaultColors[index % defaultColors.length];
}

function updateLpLegend() {
    const legendEl = document.getElementById('lpLegend');
    const dailyClicksPerLP = dashboardData.daily_clicks_per_lp || {};
    const lpNames = Object.keys(dailyClicksPerLP);
    
    if (lpNames.length === 0) {
        legendEl.innerHTML = '';
        return;
    }
    
    // Initialize visibility for new LPs
    lpNames.forEach(lp => {
        if (lpVisibility[lp] === undefined) {
            lpVisibility[lp] = true;
        }
    });
    
    let html = '';
    lpNames.forEach((lp, index) => {
        const color = getLpColor(lp, index);
        const isChecked = lpVisibility[lp] ? 'checked' : '';
        const disabledClass = lpVisibility[lp] ? '' : 'disabled';
        
        html += `
            <label class="lp-legend-item ${disabledClass}">
                <input type="checkbox" ${isChecked} onchange="toggleLpVisibility('${lp}', this.checked)">
                <span class="legend-color" style="background: ${color.line}"></span>
                <span class="legend-label">${lp}</span>
            </label>
        `;
    });
    
    legendEl.innerHTML = html;
}

function toggleLpVisibility(lpName, visible) {
    lpVisibility[lpName] = visible;
    
    // Update legend item class
    const legendItems = document.querySelectorAll('.lp-legend-item');
    legendItems.forEach(item => {
        const label = item.querySelector('.legend-label').textContent;
        if (label === lpName) {
            if (visible) {
                item.classList.remove('disabled');
            } else {
                item.classList.add('disabled');
            }
        }
    });
    
    updateLpTrendChart();
}

function updateLpTrendChart() {
    const canvas = document.getElementById('lpTrendChart');
    const emptyEl = document.getElementById('lpTrendEmpty');
    const tooltipEl = document.getElementById('lpTrendTooltip');
    const ctx = canvas.getContext('2d');
    
    const dailyClicksPerLP = dashboardData.daily_clicks_per_lp || {};
    const lpNames = Object.keys(dailyClicksPerLP).filter(lp => lpVisibility[lp]);
    
    // Get all unique dates
    const allDates = new Set();
    Object.values(dailyClicksPerLP).forEach(data => {
        Object.keys(data).forEach(date => allDates.add(date));
    });
    const dates = Array.from(allDates).sort();
    
    if (dates.length === 0 || lpNames.length === 0) {
        canvas.style.display = 'none';
        emptyEl.style.display = 'flex';
        return;
    }
    
    canvas.style.display = 'block';
    emptyEl.style.display = 'none';
    
    // Store data for tooltip
    lpTrendData = { dates, dailyClicksPerLP, lpNames };
    
    // Clear and resize canvas
    const container = canvas.parentElement;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = container.offsetWidth * dpr;
    canvas.height = 300 * dpr;
    canvas.style.width = container.offsetWidth + 'px';
    canvas.style.height = '300px';
    ctx.scale(dpr, dpr);
    
    const width = container.offsetWidth;
    const height = 300;
    const padding = { top: 20, right: 50, bottom: 50, left: 50 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Calculate max value across all visible LPs
    let maxClicks = 0;
    lpNames.forEach(lp => {
        dates.forEach(date => {
            const val = dailyClicksPerLP[lp]?.[date] || 0;
            if (val > maxClicks) maxClicks = val;
        });
    });
    maxClicks = Math.max(maxClicks, 1);
    const yStep = Math.ceil(maxClicks / 5);
    const yMax = yStep * 5;
    
    // Draw grid lines
    ctx.strokeStyle = '#E8E8E8';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = padding.top + (chartH / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(width - padding.right, y);
        ctx.stroke();
    }
    
    // Draw Y axis labels
    ctx.fillStyle = '#8993A4';
    ctx.font = '11px -apple-system, BlinkMacSystemFont, sans-serif';
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';
    for (let i = 0; i <= 5; i++) {
        const val = yMax - (yStep * i);
        const y = padding.top + (chartH / 5) * i;
        ctx.fillText(val.toString(), padding.left - 8, y);
    }
    
    // Calculate X positions
    const xStep = chartW / (dates.length - 1 || 1);
    
    // Draw X axis labels
    ctx.fillStyle = '#8993A4';
    ctx.font = '10px -apple-system, BlinkMacSystemFont, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    
    // Show limited labels to avoid overlap
    const labelStep = Math.ceil(dates.length / 15);
    dates.forEach((date, i) => {
        if (i % labelStep === 0 || i === dates.length - 1) {
            const x = padding.left + i * xStep;
            ctx.save();
            ctx.translate(x, height - padding.bottom + 8);
            ctx.rotate(-45 * Math.PI / 180);
            ctx.fillText(formatChartDate(date), 0, 0);
            ctx.restore();
        }
    });
    
    // Draw lines for each LP
    lpNames.forEach((lp, lpIndex) => {
        const color = getLpColor(lp, Object.keys(dailyClicksPerLP).indexOf(lp));
        const data = dailyClicksPerLP[lp] || {};
        
        // Draw line
        ctx.strokeStyle = color.line;
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.beginPath();
        
        let started = false;
        dates.forEach((date, i) => {
            const val = data[date] || 0;
            const x = padding.left + i * xStep;
            const y = padding.top + chartH - (val / yMax) * chartH;
            
            if (!started) {
                ctx.moveTo(x, y);
                started = true;
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.stroke();
        
        // Draw dots
        ctx.fillStyle = color.line;
        dates.forEach((date, i) => {
            const val = data[date] || 0;
            const x = padding.left + i * xStep;
            const y = padding.top + chartH - (val / yMax) * chartH;
            
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fill();
        });
    });
    
    // Store chart dimensions for tooltip
    canvas.chartDimensions = { padding, chartW, chartH, xStep, yMax, dates };
}

// LP Trend Chart Tooltip Handler
function initLpTrendTooltip() {
    const canvas = document.getElementById('lpTrendChart');
    const tooltipEl = document.getElementById('lpTrendTooltip');
    
    canvas.addEventListener('mousemove', function(e) {
        if (!lpTrendData.dates || lpTrendData.dates.length === 0) return;
        
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const dims = canvas.chartDimensions;
        
        if (!dims) return;
        
        // Check if mouse is in chart area
        if (x < dims.padding.left || x > dims.padding.left + dims.chartW ||
            y < dims.padding.top || y > dims.padding.top + dims.chartH) {
            tooltipEl.style.display = 'none';
            return;
        }
        
        // Find nearest date index
        const relX = x - dims.padding.left;
        const dateIndex = Math.round(relX / dims.xStep);
        
        if (dateIndex < 0 || dateIndex >= lpTrendData.dates.length) {
            tooltipEl.style.display = 'none';
            return;
        }
        
        const date = lpTrendData.dates[dateIndex];
        const dailyClicksPerLP = lpTrendData.dailyClicksPerLP;
        const visibleLPs = lpTrendData.lpNames;
        
        // Build tooltip content
        let html = `<div class="tooltip-date">${formatChartDate(date)}</div>`;
        
        visibleLPs.forEach((lp, index) => {
            const color = getLpColor(lp, Object.keys(dailyClicksPerLP).indexOf(lp));
            const value = dailyClicksPerLP[lp]?.[date] || 0;
            
            html += `
                <div class="tooltip-item">
                    <span class="tooltip-color" style="background: ${color.line}"></span>
                    <span class="tooltip-name">${lp}</span>
                    <span class="tooltip-value">${value}</span>
                </div>
            `;
        });
        
        tooltipEl.innerHTML = html;
        tooltipEl.style.display = 'block';
        
        // Position tooltip
        let tooltipX = e.clientX - rect.left + 15;
        let tooltipY = e.clientY - rect.top - 10;
        
        // Prevent tooltip from going off-screen
        if (tooltipX + tooltipEl.offsetWidth > rect.width) {
            tooltipX = e.clientX - rect.left - tooltipEl.offsetWidth - 15;
        }
        if (tooltipY + tooltipEl.offsetHeight > rect.height) {
            tooltipY = rect.height - tooltipEl.offsetHeight - 10;
        }
        
        tooltipEl.style.left = tooltipX + 'px';
        tooltipEl.style.top = tooltipY + 'px';
    });
    
    canvas.addEventListener('mouseleave', function() {
        tooltipEl.style.display = 'none';
    });
}

// Handle window resize for chart
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
        if (dashboardData && dashboardData.daily_clicks) {
            updateDailyClicksChart();
        }
        if (dashboardData && dashboardData.daily_clicks_per_lp) {
            updateLpTrendChart();
        }
    }, 250);
});

// ============================================================================
// CUSTOMER LIST
// ============================================================================

let customerCurrentPage = 1;
const customerPageSize = 25;
let filteredCustomers = [];

function countUniqueEmails(list) {
    const set = new Set();
    list.forEach(c => {
        const email = (c.email || '').trim().toLowerCase();
        if (email && email !== '-') set.add(email);
    });
    return set.size;
}

function updateCustomerList() {
    const customers = dashboardData.customers || [];
    filteredCustomers = customers;

    // Update customer tab stat cards
    const custTotal = document.getElementById('custTabTotal');
    const custUnique = document.getElementById('custTabUnique');
    const custUniqueSub = document.getElementById('custTabUniqueSub');
    if (custTotal) custTotal.textContent = customers.length;

    // Populate campaign dropdown (only on fresh data load)
    populateCampaignFilter(customers);

    // Apply campaign filter
    const campaignVal = (document.getElementById('campaignFilter').value || '');
    if (campaignVal) {
        if (campaignVal === '__direct__') {
            filteredCustomers = filteredCustomers.filter(c => !c.utm_campaign || c.utm_campaign === '-' || c.utm_campaign === 'direct');
        } else {
            filteredCustomers = filteredCustomers.filter(c => (c.utm_campaign || '') === campaignVal);
        }
    }

    // Apply search filter
    const search = (document.getElementById('customerSearch').value || '').toLowerCase();
    if (search) {
        filteredCustomers = filteredCustomers.filter(c => {
            return (c.business_name || '').toLowerCase().includes(search) ||
                   (c.email || '').toLowerCase().includes(search) ||
                   (c.whatsapp || '').toLowerCase().includes(search) ||
                   (c.package || '').toLowerCase().includes(search) ||
                   (c.utm_campaign || '').toLowerCase().includes(search) ||
                   (c.utm_term || '').toLowerCase().includes(search) ||
                   (c.utm_content || '').toLowerCase().includes(search) ||
                   (c.source_name || '').toLowerCase().includes(search);
        });
    }

    // Update filtered count in stat card
    if (custTotal && (campaignVal || search)) {
        custTotal.textContent = filteredCustomers.length + ' / ' + customers.length;
    }

    // Unique by email (on filtered view so it respects campaign/search filters)
    if (custUnique) {
        const uniqAll = countUniqueEmails(customers);
        const uniqFiltered = countUniqueEmails(filteredCustomers);
        if (campaignVal || search) {
            custUnique.textContent = uniqFiltered + ' / ' + uniqAll;
            if (custUniqueSub) custUniqueSub.textContent = 'filtered / all — dedupe by email';
        } else {
            custUnique.textContent = uniqAll;
            if (custUniqueSub) {
                const dupes = customers.length - uniqAll;
                const emptyEmails = customers.filter(c => !((c.email || '').trim()) || (c.email || '').trim() === '-').length;
                custUniqueSub.textContent = dupes + ' duplicate row(s), ' + emptyEmails + ' without email';
            }
        }
    }

    customerCurrentPage = 1;
    renderCustomerPage();
}

let campaignFilterPopulated = false;
function populateCampaignFilter(customers) {
    const sel = document.getElementById('campaignFilter');
    if (!sel) return;
    // Collect unique campaigns
    const campaigns = {};
    customers.forEach(c => {
        const camp = (c.utm_campaign || '').trim();
        if (camp && camp !== '-' && camp !== 'direct') {
            campaigns[camp] = (campaigns[camp] || 0) + 1;
        }
    });
    // Sort by count descending
    const sorted = Object.entries(campaigns).sort((a, b) => b[1] - a[1]);
    // Preserve current selection
    const current = sel.value;
    sel.innerHTML = '<option value="">All Campaigns (' + customers.length + ')</option>';
    sorted.forEach(([name, count]) => {
        const opt = document.createElement('option');
        opt.value = name;
        opt.textContent = name + ' (' + count + ')';
        sel.appendChild(opt);
    });
    // Add "direct / no UTM" option
    const directCount = customers.filter(c => !c.utm_campaign || c.utm_campaign === '-' || c.utm_campaign === 'direct').length;
    if (directCount > 0) {
        const opt = document.createElement('option');
        opt.value = '__direct__';
        opt.textContent = 'Direct / No UTM (' + directCount + ')';
        sel.appendChild(opt);
    }
    sel.value = current;
}

function filterCustomerList() {
    updateCustomerList();
}

function customerPage(dir) {
    const totalPages = Math.ceil(filteredCustomers.length / customerPageSize) || 1;
    customerCurrentPage += dir;
    if (customerCurrentPage < 1) customerCurrentPage = 1;
    if (customerCurrentPage > totalPages) customerCurrentPage = totalPages;
    renderCustomerPage();
}

function renderCustomerPage() {
    const tbody = document.getElementById('customerTableBody');
    const totalPages = Math.ceil(filteredCustomers.length / customerPageSize) || 1;
    const start = (customerCurrentPage - 1) * customerPageSize;
    const pageItems = filteredCustomers.slice(start, start + customerPageSize);

    document.getElementById('customerCount').textContent = filteredCustomers.length + ' customers';
    document.getElementById('pageInfo').textContent = 'Page ' + customerCurrentPage + ' / ' + totalPages;
    document.getElementById('prevPageBtn').disabled = customerCurrentPage <= 1;
    document.getElementById('nextPageBtn').disabled = customerCurrentPage >= totalPages;

    // Reset select all checkbox
    document.getElementById('selectAllCustomers').checked = false;

    if (pageItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="15" style="text-align: center; color: var(--text-muted);">No customers found</td></tr>';
        updateDeleteBtn();
        return;
    }

    let html = '';
    pageItems.forEach((c, idx) => {
        const date = (c.timestamp || '').substring(0, 16);
        const campaign = c.utm_campaign && c.utm_campaign !== 'direct' ? c.utm_campaign : '-';
        const content = c.utm_content && c.utm_content !== '-' ? c.utm_content : '-';
        const utmTerm = c.utm_term && c.utm_term !== '-' ? c.utm_term : '-';
        const placement = c.placement && c.placement !== '-' ? c.placement : '-';
        const rowKey = escapeHtml((c.timestamp || '') + '|' + (c.source_name || '') + '|' + (c.business_name || '') + '|' + (c.whatsapp || ''));

        html += '<tr>';
        html += '<td style="text-align: center;"><input type="checkbox" class="customer-check" data-timestamp="' + escapeHtml(c.timestamp || '') + '" data-source="' + escapeHtml(c.source_name || '') + '" data-biz="' + escapeHtml(c.business_name || '') + '" data-wa="' + escapeHtml(c.whatsapp || '') + '" onchange="updateDeleteBtn()"></td>';
        html += '<td style="white-space: nowrap; font-size: 12px;">' + escapeHtml(date) + '</td>';
        html += '<td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + escapeHtml(c.business_name || '-') + '">' + escapeHtml(c.business_name || '-') + '</td>';
        html += '<td style="font-size: 13px;">' + escapeHtml(c.whatsapp || '-') + '</td>';
        html += '<td style="font-size: 13px; max-width: 160px; overflow: hidden; text-overflow: ellipsis;">' + escapeHtml(c.email || '-') + '</td>';
        html += '<td>' + escapeHtml(c.package || '-') + '</td>';
        html += '<td style="text-align: center; font-weight: 600;">' + escapeHtml(c.reviews_qty || '-') + '</td>';
        html += '<td><span style="background: var(--blue-light); color: var(--blue-primary); padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">' + escapeHtml(c.source_name || '-') + '</span></td>';
        html += '<td style="font-size: 13px; max-width: 160px; overflow: hidden; text-overflow: ellipsis;" title="' + escapeHtml(campaign) + '">' + escapeHtml(campaign) + '</td>';
        html += '<td style="font-size: 13px; max-width: 140px; overflow: hidden; text-overflow: ellipsis;" title="' + escapeHtml(content) + '">' + escapeHtml(content) + '</td>';
        html += '<td style="font-size: 13px; max-width: 140px; overflow: hidden; text-overflow: ellipsis;" title="' + escapeHtml(utmTerm) + '">' + escapeHtml(utmTerm) + '</td>';
        html += '<td style="font-size: 12px;">' + escapeHtml(placement) + '</td>';
        html += '<td>' + escapeHtml(c.state || '-') + '</td>';
        html += '<td>' + escapeHtml(c.country || '-') + '</td>';
        html += '<td style="white-space: nowrap;">';
        html += '<a href="#" onclick="openCustFollowupWA(this); return false;" data-biz="' + escapeHtml(c.business_name || '-') + '" data-email="' + escapeHtml(c.email || '-') + '" data-pkg="' + escapeHtml(c.package || '-') + '" data-reviews="' + escapeHtml(c.reviews_qty || '-') + '" data-wa="' + escapeHtml(c.whatsapp || '') + '" style="padding: 4px 10px; border: 1px solid #25D366; color: #25D366; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; margin-right: 4px; display: inline-block;">Follow Up</a>';
        html += '<button onclick="copyCustEmail(this)" data-biz="' + escapeHtml(c.business_name || '-') + '" data-email="' + escapeHtml(c.email || '-') + '" data-pkg="' + escapeHtml(c.package || '-') + '" data-reviews="' + escapeHtml(c.reviews_qty || '-') + '" style="padding: 4px 10px; border: 1px solid var(--blue-primary); color: var(--blue-primary); background: white; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;">Copy Email</button>';
        html += '</td>';
        html += '</tr>';
    });

    tbody.innerHTML = html;
    updateDeleteBtn();
}

function exportCustomerCSV() {
    if (!filteredCustomers || filteredCustomers.length === 0) {
        showToast('No data to export', 'error');
        return;
    }

    const headers = ['Date', 'Business Name', 'WhatsApp', 'Email', 'Package', 'Reviews', 'LP Source', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content', 'UTM Term (Adset)', 'Placement', 'State', 'Zip', 'Country'];

    let csv = headers.join(',') + '\n';
    filteredCustomers.forEach(c => {
        const row = [
            '"' + (c.timestamp || '') + '"',
            '"' + (c.business_name || '').replace(/"/g, '""') + '"',
            '"' + (c.whatsapp || '') + '"',
            '"' + (c.email || '') + '"',
            '"' + (c.package || '') + '"',
            c.reviews_qty || '',
            '"' + (c.source_name || '') + '"',
            '"' + (c.utm_source || '') + '"',
            '"' + (c.utm_medium || '') + '"',
            '"' + (c.utm_campaign || '') + '"',
            '"' + (c.utm_content || '') + '"',
            '"' + (c.utm_term || '') + '"',
            '"' + (c.placement || '') + '"',
            '"' + (c.state || '') + '"',
            '"' + (c.zip || '') + '"',
            '"' + (c.country || '') + '"'
        ];
        csv += row.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'smart_buzzer_customers_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    URL.revokeObjectURL(url);
    showToast('CSV exported: ' + filteredCustomers.length + ' rows', 'success');
}

// Select All / Deselect All checkboxes
function toggleSelectAllCustomers(masterCheckbox) {
    const checks = document.querySelectorAll('.customer-check');
    checks.forEach(cb => cb.checked = masterCheckbox.checked);
    updateDeleteBtn();
}

function updateDeleteBtn() {
    const checked = document.querySelectorAll('.customer-check:checked');
    const btn = document.getElementById('deleteSelectedBtn');
    const count = document.getElementById('selectedCount');
    if (checked.length > 0) {
        btn.style.display = 'inline-block';
        count.textContent = checked.length;
    } else {
        btn.style.display = 'none';
    }
}

async function deleteSelectedCustomers() {
    const checked = document.querySelectorAll('.customer-check:checked');
    if (checked.length === 0) return;

    if (!confirm('Delete ' + checked.length + ' selected customer(s)? This cannot be undone.')) return;

    const entries = [];
    checked.forEach(cb => {
        entries.push({
            timestamp: cb.getAttribute('data-timestamp'),
            source_name: cb.getAttribute('data-source'),
            business_name: cb.getAttribute('data-biz'),
            whatsapp: cb.getAttribute('data-wa')
        });
    });

    try {
        const formData = new FormData();
        formData.append('entries', JSON.stringify(entries));

        const response = await fetch('?action=delete_customers', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.error) {
            showToast('Error: ' + result.error, 'error');
            return;
        }

        showToast('Deleted ' + result.deleted + ' customer(s)', 'success');

        // Remove deleted entries from local data
        const deleteKeys = new Set(entries.map(e => e.timestamp + '|' + e.source_name + '|' + e.business_name + '|' + e.whatsapp));
        dashboardData.customers = (dashboardData.customers || []).filter(c => !deleteKeys.has((c.timestamp || '') + '|' + (c.source_name || '') + '|' + (c.business_name || '') + '|' + (c.whatsapp || '')));
        updateCustomerList();
    } catch (err) {
        showToast('Delete failed: ' + err.message, 'error');
    }
}

// Follow Up via WhatsApp
function openCustFollowupWA(el) {
    var biz = el.getAttribute('data-biz') || '-';
    var email = el.getAttribute('data-email') || '-';
    var pkg = el.getAttribute('data-pkg') || '-';
    var reviews = el.getAttribute('data-reviews') || '-';
    var wa = el.getAttribute('data-wa') || '';
    var phone = wa.replace(/[^0-9+]/g, '').replace(/^\+/, '');
    if (!phone) { alert('No WhatsApp number for this customer.'); return; }
    var msg = 'Hi ' + biz + ',\n\n'
        + 'Thank you for your interest in our GGL Rvws service!\n\n'
        + 'We noticed you selected:\n'
        + '- Package: ' + pkg + '\n'
        + '- Reviews: ' + reviews + '\n'
        + '- Email: ' + email + '\n\n'
        + 'Would you like to proceed with the payment?';
    var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(msg);
    window.open(url, '_blank');
}

// Copy Email
function copyCustEmail(el) {
    var biz = el.getAttribute('data-biz') || '-';
    var pkg = el.getAttribute('data-pkg') || '-';
    var reviews = el.getAttribute('data-reviews') || '-';
    var subject = 'Quick question about your ' + pkg + ' package';
    var body = 'Hi ' + biz + ',\n\n'
        + 'I noticed you were looking at our ' + pkg + ' package (' + reviews + ' reviews) — great choice!\n\n'
        + 'Just wanted to check in and see if you had any questions before getting started. Most of our clients see their first reviews go live within 24 hours of approval.\n\n'
        + 'If you\'re ready, I can get your campaign set up right away.\n\n'
        + 'Looking forward to hearing from you!\n\n'
        + 'Best,\nSmart Buzzer Team';
    var text = 'Subject: ' + subject + '\n\n' + body;
    navigator.clipboard.writeText(text).then(function() {
        var orig = el.textContent;
        el.textContent = 'Copied!';
        el.style.background = 'var(--green-success)';
        el.style.color = 'white';
        el.style.borderColor = 'var(--green-success)';
        setTimeout(function() {
            el.textContent = orig;
            el.style.background = 'white';
            el.style.color = 'var(--blue-primary)';
            el.style.borderColor = 'var(--blue-primary)';
        }, 1500);
    });
}

// Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================================================
// RESIZABLE COLUMNS
// ============================================================================
(function() {
    const table = document.getElementById('customerTable');
    if (!table) return;

    // Default widths per column
    const defaultWidths = [40, 130, 170, 130, 160, 130, 70, 80, 160, 140, 140, 140, 60, 60, 160];

    function initResizable() {
        const ths = table.querySelectorAll('thead th');
        // Load saved widths
        let saved = null;
        try { saved = JSON.parse(localStorage.getItem('sb_col_widths')); } catch(e) {}

        ths.forEach((th, i) => {
            // Set initial width
            const w = (saved && saved[i]) ? saved[i] : (defaultWidths[i] || 120);
            th.style.width = w + 'px';

            // Remove old handles
            th.querySelectorAll('.col-resize-handle').forEach(h => h.remove());

            // Add resize handle
            const handle = document.createElement('div');
            handle.className = 'col-resize-handle';
            th.appendChild(handle);

            let startX, startW;

            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                startX = e.clientX;
                startW = th.offsetWidth;
                handle.classList.add('active');
                document.body.classList.add('col-resizing');

                function onMove(ev) {
                    const diff = ev.clientX - startX;
                    const newW = Math.max(40, startW + diff);
                    th.style.width = newW + 'px';
                }

                function onUp() {
                    handle.classList.remove('active');
                    document.body.classList.remove('col-resizing');
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                    // Save widths
                    const widths = Array.from(table.querySelectorAll('thead th')).map(t => t.offsetWidth);
                    try { localStorage.setItem('sb_col_widths', JSON.stringify(widths)); } catch(e) {}
                }

                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            });
        });
    }

    // Double-click to auto-fit (reset to default)
    table.addEventListener('dblclick', function(e) {
        if (e.target.classList.contains('col-resize-handle')) {
            const th = e.target.parentElement;
            const idx = Array.from(th.parentElement.children).indexOf(th);
            th.style.width = (defaultWidths[idx] || 120) + 'px';
            const widths = Array.from(table.querySelectorAll('thead th')).map(t => t.offsetWidth);
            try { localStorage.setItem('sb_col_widths', JSON.stringify(widths)); } catch(e) {}
        }
    });

    // Init on DOM ready
    initResizable();

    // Re-init after table re-renders (MutationObserver)
    const observer = new MutationObserver(function() { initResizable(); });
    const thead = table.querySelector('thead');
    if (thead) observer.observe(thead, { childList: true, subtree: true });
})();
</script>

<?php endif; ?>

</body>
</html>