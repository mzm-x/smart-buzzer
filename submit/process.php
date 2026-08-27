<?php
/**
 * ============================================================================
 * File: /submit/process.php
 * Smart Buzzer Form Submission Handler - v3.2 (Review Phase)
 * 
 * v3.2 NEW: Added Review Phase W1/W2/W3 fields to sheet, analytics log
 * v3.1 NEW: Added Country, State, Business Industry fields to sheet & analytics
 * v3.0 NEW: Added Social Media order type handling with separate validation
 * v2.5 NEW: Added Rating Option field (5_star_only / mix_80_20)
 * v2.4 NEW: Added Business Location field storage
 * v2.3 NEW: Support "Other" platform with customPlatform field
 * v2.2 FIX: Handle multiple businesses - write each business data separately
 * v2.1 FIX: Handle duplicate sheet names by appending orderId
 * 
 * v2.0 FEATURES:
 * - Auto-create Google Sheet tab on order submission
 * - No need for AM to click "Open File"
 * ============================================================================
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/mailer.php';   // auto order-confirmation email (v3.3)

// ============================================================================
// CONFIGURATION
// ============================================================================

$MASTER_SPREADSHEET_ID = '1WzOBE3ReJCjwjfqAqWEqw50wLkJdggQ0xQWr0U5dg9o';
$CREDENTIALS_PATH = __DIR__ . '/credentials/service-account.json';

// ============================================================================
// RATE LIMITING
// ============================================================================

function isRateLimited() {
    $dataDir = defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/data';
    $rateLimitFile = $dataDir . '/rate_limit.json';
    
    $ip = getClientIp();
    $currentTime = time();
    $windowSize = 3600;
    $maxRequests = 10;
    
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $rateLimitData = [];
    if (file_exists($rateLimitFile)) {
        $rateLimitData = json_decode(file_get_contents($rateLimitFile), true) ?? [];
    }
    
    foreach ($rateLimitData as $key => $timestamps) {
        $rateLimitData[$key] = array_filter($timestamps, function($t) use ($currentTime, $windowSize) {
            return ($currentTime - $t) < $windowSize;
        });
        if (empty($rateLimitData[$key])) {
            unset($rateLimitData[$key]);
        }
    }
    
    $requestCount = count($rateLimitData[$ip] ?? []);
    
    if ($requestCount >= $maxRequests) {
        return true;
    }
    
    if (!isset($rateLimitData[$ip])) {
        $rateLimitData[$ip] = [];
    }
    $rateLimitData[$ip][] = $currentTime;
    
    file_put_contents($rateLimitFile, json_encode($rateLimitData), LOCK_EX);
    
    return false;
}

// ============================================================================
// GOOGLE SHEETS FUNCTIONS
// ============================================================================

/**
 * Get display platform name (handles "Other" with customPlatform)
 */
function getDisplayPlatform($platform, $customPlatform = '') {
    if ($platform === 'Other' && !empty($customPlatform)) {
        return $customPlatform;
    }
    return $platform ?: 'Google';
}

/**
 * Create sheet tab for new order
 * v2.2: Handle multiple businesses - write each business separately
 */
function createOrderSheet($order) {
    global $MASTER_SPREADSHEET_ID, $CREDENTIALS_PATH;
    
    // Check if vendor exists
    $vendorAutoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($vendorAutoload)) {
        logActivity("Vendor autoload not found, skipping sheet creation", 'warning');
        return null;
    }
    
    require_once $vendorAutoload;
    
    if (!class_exists('Google_Client')) {
        logActivity("Google_Client not found, skipping sheet creation", 'warning');
        return null;
    }
    
    try {
        $client = new Google_Client();
        $client->setApplicationName('Smart Buzzer Order System');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($CREDENTIALS_PATH);
        $client->setAccessType('offline');
        
        $sheetsService = new Google_Service_Sheets($client);
        
        // Sheet title = client email (fallback to orderId if duplicate)
        $baseTitle = $order['email'] ?? $order['orderId'];
        $sheetTitle = $baseTitle;
        
        // Try to add new sheet, handle duplicate name
        $sheetId = null;
        $attempts = 0;
        $maxAttempts = 3;
        
        while ($sheetId === null && $attempts < $maxAttempts) {
            try {
                $requests = [
                    new Google_Service_Sheets_Request([
                        'addSheet' => [
                            'properties' => ['title' => $sheetTitle]
                        ]
                    ])
                ];
                
                $batchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);
                
                $response = $sheetsService->spreadsheets->batchUpdate($MASTER_SPREADSHEET_ID, $batchRequest);
                $sheetId = $response->getReplies()[0]->getAddSheet()->getProperties()->getSheetId();
                
            } catch (Google_Service_Exception $e) {
                $error = json_decode($e->getMessage(), true);
                $errorMessage = $error['error']['message'] ?? '';
                
                // Check if duplicate sheet name error
                if (strpos($errorMessage, 'already exists') !== false || strpos($errorMessage, 'duplicate') !== false) {
                    $attempts++;
                    // Append orderId to make unique
                    $sheetTitle = $baseTitle . ' (' . $order['orderId'] . ')';
                    logActivity("Sheet name exists, trying: $sheetTitle", 'info');
                } else {
                    throw $e; // Re-throw other errors
                }
            }
        }
        
        if ($sheetId === null) {
            logActivity("Failed to create sheet after $maxAttempts attempts", 'error');
            return null;
        }
        
        // Check if multi-business order
        $businesses = $order['businesses'] ?? [];
        $numBusinesses = count($businesses);
        
        if ($numBusinesses <= 1) {
            // Single business - original format
            $displayPlatform = getDisplayPlatform($order['platform'] ?? 'Google', $order['customPlatform'] ?? '');
            $ratingOptionDisplay = ($order['ratingOption'] ?? '5_star_only') === 'mix_80_20' ? 'Mix (80% 5-Star + 20% 4-Star)' : '5 Stars Only';
            $genderMap = ['mix' => 'Mix Gender', 'male' => 'Male Only', 'female' => 'Female Only'];
            $genderKey = $order['reviewerGender'] ?? ($order['businesses'][0]['reviewerGender'] ?? 'mix');
            $reviewerGenderDisplay = $genderMap[$genderKey] ?? 'Mix Gender';
            $fields = [
                ['Order ID', $order['orderId'] ?? ''],
                ['Timestamp', $order['timestamp'] ?? ''],
                ['Business Names', $order['businessNames'] ?? ''],
                ['Email', $order['email'] ?? ''],
                ['Payment Email (Fanbasis/Tazapay)', $order['paymentEmail'] ?? ''],
                ['Telegram', $order['telegram'] ?? ''],
                ['WhatsApp', $order['whatsapp'] ?? ''],
                ['Business Location', $order['businessLocation'] ?? ''],
                ['Country', $order['country'] ?? ''],
                ['State / Region', $order['state'] ?? ''],
                ['Business Industry', $order['businessIndustry'] ?? ''],
                ['Platform', $displayPlatform],
                ['Product Type', $order['productType'] ?? ''],
                ['Rating Option', $ratingOptionDisplay],
                ['Rating Mix', $order['ratingMix'] ?? ''],
                ['Quantity', $order['quantity'] ?? 'N/A'],
                ['Gender', $reviewerGenderDisplay],
                ['Number of Businesses', $order['numBusinesses'] ?? '1'],
                ['Current Rating', $order['currentRating'] ?? 'N/A'],
                ['Target Rating Total', $order['targetRatingTotal'] ?? 'N/A'],
                ['Business Link', $order['mapsLink'] ?? ''],
                ['Business Type', $order['businessType'] ?? 'N/A'],
                ['Delivery Speed', $order['reviewPhaseW1'] ?? 'N/A'],
                ['Business USP', $order['businessUSP'] ?? 'N/A'],
                ['Review Tone', $order['reviewTone'] ?? 'N/A'],
                ['Review Length', $order['reviewLength'] ?? 'N/A'],
                ['Business Details', $order['businessDetails'] ?? 'N/A'],
                ['Specific Names', $order['specificNames'] ?? 'N/A'],
                ['Keywords', $order['keywords'] ?? 'N/A'],
                ['Keyword Flexibility', $order['keywordFlex'] ?? 'N/A'],
                ['Example Reviews', $order['exampleReviews'] ?? 'N/A'],
                ['Additional Notes', $order['additionalNotes'] ?? 'N/A'],
                ['Status', 'Pending'],
                ['AM Notes', ''],
                ['Payment Status', 'Pending'],
                ['Order Value', ''],
                ['Last Updated', date('Y-m-d H:i:s')]
            ];
        } else {
            // Multi-business - write each business separately
            $fields = [];
            $businessLabels = ['First', 'Second', 'Third', 'Fourth', 'Fifth'];
            
            foreach ($businesses as $index => $business) {
                $label = ($businessLabels[$index] ?? ($index + 1)) . ' Business';
                $currentRating = intval($business['currentRating'] ?? 0);
                $reviews = intval($business['reviews'] ?? 0);
                $targetTotal = $currentRating + $reviews;
                $businessPlatform = getDisplayPlatform($business['platform'] ?? 'Google', $business['customPlatform'] ?? '');
                $bizRatingOption = ($business['ratingOption'] ?? '5_star_only') === 'mix_80_20' ? 'Mix (80% 5-Star + 20% 4-Star)' : '5 Stars Only';
                $genderMap = ['mix' => 'Mix Gender', 'male' => 'Male Only', 'female' => 'Female Only'];
                $bizGenderDisplay = $genderMap[$business['reviewerGender'] ?? 'mix'] ?? 'Mix Gender';

                // Add header for each business
                $fields[] = [$label, ''];
                $fields[] = ['Order ID', $order['orderId'] ?? ''];
                $fields[] = ['Timestamp', $order['timestamp'] ?? ''];
                $fields[] = ['Business Name', $business['businessName'] ?? ''];
                $fields[] = ['Email', $order['email'] ?? ''];
                $fields[] = ['Payment Email (Fanbasis/Tazapay)', $order['paymentEmail'] ?? ''];
                $fields[] = ['Telegram', $order['telegram'] ?? ''];
                $fields[] = ['WhatsApp', $order['whatsapp'] ?? ''];
                $fields[] = ['Business Location', $order['businessLocation'] ?? ''];
                $fields[] = ['Country', $order['country'] ?? ''];
                $fields[] = ['State / Region', $order['state'] ?? ''];
                $fields[] = ['Business Industry', $order['businessIndustry'] ?? ''];
                $fields[] = ['Platform', $businessPlatform];
                $fields[] = ['Product Type', $order['productType'] ?? ''];
                $fields[] = ['Rating Option', $bizRatingOption];
                $fields[] = ['Rating Mix', $order['ratingMix'] ?? ''];
                $fields[] = ['Quantity', $business['reviews'] ?? 'N/A'];
                $fields[] = ['Gender', $bizGenderDisplay];
                $fields[] = ['Current Rating', $business['currentRating'] ?? 'N/A'];
                $fields[] = ['Target Rating Total', $targetTotal];
                $fields[] = ['Business Link', $business['businessLink'] ?? ''];
                $fields[] = ['Business Type', $business['businessType'] ?? 'N/A'];
                $fields[] = ['Delivery Speed', $business['reviewPhaseW1'] ?? 'N/A'];
                $fields[] = ['Business USP', $business['businessUSP'] ?? 'N/A'];
                $fields[] = ['Review Tone', $business['reviewTone'] ?? 'N/A'];
                $fields[] = ['Review Length', $business['reviewLength'] ?? 'N/A'];
                $fields[] = ['Business Details', $business['businessDetails'] ?? 'N/A'];
                $fields[] = ['Specific Names', $business['specificNames'] ?? 'N/A'];
                $fields[] = ['Keywords', $business['keywords'] ?? 'N/A'];
                $fields[] = ['Keyword Flexibility', $business['keywordFlex'] ?? 'N/A'];
                $fields[] = ['Example Reviews', $business['exampleReviews'] ?? 'N/A'];
                $fields[] = ['Additional Notes', $business['additionalNotes'] ?? 'N/A'];
                $fields[] = ['Status', 'Pending'];
                $fields[] = ['AM Notes', ''];
                $fields[] = ['Payment Status', 'Pending'];
                $fields[] = ['Order Value', ''];
                $fields[] = ['Last Updated', date('Y-m-d H:i:s')];
                
                // Add empty row between businesses (except last)
                if ($index < $numBusinesses - 1) {
                    $fields[] = ['', ''];
                }
            }
        }
        
        $body = new Google_Service_Sheets_ValueRange(['values' => $fields]);
        $range = "'" . $sheetTitle . "'!A1:B" . count($fields);
        $sheetsService->spreadsheets_values->update(
            $MASTER_SPREADSHEET_ID,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
        
        // Format sheet
        $rowCount = count($fields);
        $formatRequests = [
            // Bold Column A + background
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => $rowCount,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 1
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'textFormat' => ['bold' => true],
                            'backgroundColor' => ['red' => 0.95, 'green' => 0.95, 'blue' => 0.95],
                            'verticalAlignment' => 'TOP'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(textFormat,backgroundColor,verticalAlignment)'
                ]
            ]),
            // Text wrap Column B
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => $rowCount,
                        'startColumnIndex' => 1,
                        'endColumnIndex' => 2
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'wrapStrategy' => 'WRAP',
                            'verticalAlignment' => 'TOP'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(wrapStrategy,verticalAlignment)'
                ]
            ]),
            // Column A width
            new Google_Service_Sheets_Request([
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => 0,
                        'endIndex' => 1
                    ],
                    'properties' => ['pixelSize' => 180],
                    'fields' => 'pixelSize'
                ]
            ]),
            // Column B width
            new Google_Service_Sheets_Request([
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => 1,
                        'endIndex' => 2
                    ],
                    'properties' => ['pixelSize' => 400],
                    'fields' => 'pixelSize'
                ]
            ]),
            // Freeze first column
            new Google_Service_Sheets_Request([
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => $sheetId,
                        'gridProperties' => ['frozenColumnCount' => 1]
                    ],
                    'fields' => 'gridProperties.frozenColumnCount'
                ]
            ])
        ];
        
        $formatBatch = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $formatRequests
        ]);
        $sheetsService->spreadsheets->batchUpdate($MASTER_SPREADSHEET_ID, $formatBatch);
        
        $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$MASTER_SPREADSHEET_ID}/edit#gid={$sheetId}";
        
        logActivity("Sheet created for order {$order['orderId']}: $sheetTitle", 'info');
        
        return [
            'spreadsheetId' => $MASTER_SPREADSHEET_ID,
            'sheetId' => $sheetId,
            'sheetTitle' => $sheetTitle,
            'spreadsheetUrl' => $spreadsheetUrl
        ];
        
    } catch (Exception $e) {
        logActivity("Sheet creation failed for {$order['orderId']}: " . $e->getMessage(), 'error');
        return null;
    }
}

/**
 * Create sheet tab for Social Media order
 * v3.0 NEW: Separate sheet format for social media orders
 */
function createSocialMediaOrderSheet($order) {
    global $MASTER_SPREADSHEET_ID, $CREDENTIALS_PATH;
    
    $vendorAutoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($vendorAutoload)) {
        logActivity("Vendor autoload not found, skipping SM sheet creation", 'warning');
        return null;
    }
    
    require_once $vendorAutoload;
    
    if (!class_exists('Google_Client')) {
        logActivity("Google_Client not found, skipping SM sheet creation", 'warning');
        return null;
    }
    
    try {
        $client = new Google_Client();
        $client->setApplicationName('Smart Buzzer Order System');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($CREDENTIALS_PATH);
        $client->setAccessType('offline');
        
        $sheetsService = new Google_Service_Sheets($client);
        
        $baseTitle = '[SM] ' . ($order['email'] ?? $order['orderId']);
        $sheetTitle = $baseTitle;
        
        $sheetId = null;
        $attempts = 0;
        $maxAttempts = 3;
        
        while ($sheetId === null && $attempts < $maxAttempts) {
            try {
                $requests = [
                    new Google_Service_Sheets_Request([
                        'addSheet' => [
                            'properties' => ['title' => $sheetTitle]
                        ]
                    ])
                ];
                
                $batchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);
                
                $response = $sheetsService->spreadsheets->batchUpdate($MASTER_SPREADSHEET_ID, $batchRequest);
                $sheetId = $response->getReplies()[0]->getAddSheet()->getProperties()->getSheetId();
                
            } catch (Google_Service_Exception $e) {
                $error = json_decode($e->getMessage(), true);
                $errorMessage = $error['error']['message'] ?? '';
                
                if (strpos($errorMessage, 'already exists') !== false || strpos($errorMessage, 'duplicate') !== false) {
                    $attempts++;
                    $sheetTitle = $baseTitle . ' (' . $order['orderId'] . ')';
                    logActivity("SM Sheet name exists, trying: $sheetTitle", 'info');
                } else {
                    throw $e;
                }
            }
        }
        
        if ($sheetId === null) {
            logActivity("Failed to create SM sheet after $maxAttempts attempts", 'error');
            return null;
        }
        
        // Social Media specific fields
        $stayRatePercent = round(($order['stayRate'] ?? 0.8944) * 100, 2) . '%';
        $fields = [
            ['Order ID', $order['orderId'] ?? ''],
            ['Order Type', 'Social Media'],
            ['Timestamp', $order['timestamp'] ?? ''],
            ['Email', $order['email'] ?? ''],
            ['Payment Email (Fanbasis/Tazapay)', $order['paymentEmail'] ?? ''],
            ['Telegram', $order['telegram'] ?? ''],
            ['WhatsApp', $order['whatsapp'] ?? ''],
            ['Platform', $order['platform'] ?? ''],
            ['Service Type', $order['serviceType'] ?? ''],
            ['Profile/Post Link', $order['smLink'] ?? ''],
            ['Target Quantity', $order['quantity'] ?? 0],
            ['Fulfill Quantity', $order['fulfillQuantity'] ?? 0],
            ['Stay Rate', $stayRatePercent],
            ['Status', 'Pending'],
            ['AM Notes', ''],
            ['Payment Status', 'Pending'],
            ['Order Value', ''],
            ['Last Updated', date('Y-m-d H:i:s')]
        ];
        
        $body = new Google_Service_Sheets_ValueRange(['values' => $fields]);
        $range = "'" . $sheetTitle . "'!A1:B" . count($fields);
        $sheetsService->spreadsheets_values->update(
            $MASTER_SPREADSHEET_ID,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
        
        // Format sheet
        $rowCount = count($fields);
        $formatRequests = [
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => $rowCount,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 1
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'textFormat' => ['bold' => true],
                            'backgroundColor' => ['red' => 0.9, 'green' => 0.85, 'blue' => 1.0],
                            'verticalAlignment' => 'TOP'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(textFormat,backgroundColor,verticalAlignment)'
                ]
            ]),
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => $rowCount,
                        'startColumnIndex' => 1,
                        'endColumnIndex' => 2
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'wrapStrategy' => 'WRAP',
                            'verticalAlignment' => 'TOP'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(wrapStrategy,verticalAlignment)'
                ]
            ]),
            new Google_Service_Sheets_Request([
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => 0,
                        'endIndex' => 1
                    ],
                    'properties' => ['pixelSize' => 180],
                    'fields' => 'pixelSize'
                ]
            ]),
            new Google_Service_Sheets_Request([
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => 1,
                        'endIndex' => 2
                    ],
                    'properties' => ['pixelSize' => 400],
                    'fields' => 'pixelSize'
                ]
            ])
        ];
        
        $formatBatch = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $formatRequests
        ]);
        $sheetsService->spreadsheets->batchUpdate($MASTER_SPREADSHEET_ID, $formatBatch);
        
        $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$MASTER_SPREADSHEET_ID}/edit#gid={$sheetId}";
        
        logActivity("SM Sheet created for order {$order['orderId']}: $sheetTitle", 'info');
        
        return [
            'spreadsheetId' => $MASTER_SPREADSHEET_ID,
            'sheetId' => $sheetId,
            'sheetTitle' => $sheetTitle,
            'spreadsheetUrl' => $spreadsheetUrl
        ];
        
    } catch (Exception $e) {
        logActivity("SM Sheet creation failed for {$order['orderId']}: " . $e->getMessage(), 'error');
        return null;
    }
}

// ============================================================================
// SUBMISSION LOGGING FOR ANALYTICS
// ============================================================================

function logSubmission($data) {
    $dataDir = defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/data';
    $logFile = $dataDir . '/submissions_log.json';
    
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Get tracking source (priority: tracking_source > referrer)
    $trackingSource = $data['tracking_source'] ?? '';
    $referrer = $data['referrer'] ?? 'Direct';
    $source = 'Direct';
    
    // If tracking_source is set from URL params, use it directly
    if (!empty($trackingSource)) {
        $source = $trackingSource;
    }
    // Otherwise try to detect from referrer
    elseif (!empty($referrer) && $referrer !== 'Direct' && strpos($referrer, 'smart-buzzer.com/submit') === false) {
        $parsed = parse_url($referrer);
        $path = $parsed['path'] ?? '';
        $host = $parsed['host'] ?? '';
        
        // Identify source from referrer
        if (strpos($host, 'smart-buzzer.com') !== false) {
            if (strpos($path, '/promo-australia') !== false) $source = 'Australia Landing';
            elseif (strpos($path, '/promo') !== false) $source = 'Promo Page';
            elseif (strpos($path, '/google-uk') !== false) $source = 'UK Landing';
            elseif (strpos($path, '/google-eu') !== false) $source = 'EU Landing';
            elseif (strpos($path, '/google') !== false) $source = 'Main Google Page';
            else $source = 'smart-buzzer.com' . $path;
        } elseif (strpos($host, 'google') !== false) {
            $source = 'Google Search';
        } elseif (strpos($host, 'facebook') !== false) {
            $source = 'Facebook';
        } elseif (strpos($host, 'instagram') !== false) {
            $source = 'Instagram';
        } elseif (strpos($host, 'linkedin') !== false) {
            $source = 'LinkedIn';
        } elseif (strpos($host, 'twitter') !== false || strpos($host, 'x.com') !== false) {
            $source = 'Twitter/X';
        } elseif (!empty($host)) {
            // Show full path for external sites
            $source = $host . $path;
        }
    }
    
    // Detect device type from user agent
    $userAgent = $data['userAgent'] ?? '';
    $device = 'Desktop';
    if (preg_match('/mobile|android|iphone|ipad|tablet/i', $userAgent)) {
        $device = preg_match('/ipad|tablet/i', $userAgent) ? 'Tablet' : 'Mobile';
    }
    
    $logEntry = [
        'orderId' => $data['orderId'],
        'timestamp' => $data['timestamp'],
        'ip' => $data['ip'],
        'referrer' => $referrer,
        'source' => $source,
        'businessName' => $data['businessName'],
        'email' => $data['email'],
        'paymentEmail' => $data['paymentEmail'] ?? '',
        'telegram' => $data['telegram'] ?? '',
        'country' => $data['country'] ?? '',
        'state' => $data['state'] ?? '',
        'businessIndustry' => $data['businessIndustry'] ?? '',
        'quantity' => $data['quantity'],
        'platform' => $data['platform'],
        'device' => $device
    ];
    
    // Load existing logs
    $logs = [];
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        $logs = json_decode($content, true) ?? [];
    }
    
    // Add new entry
    $logs[] = $logEntry;
    
    // Keep only last 1000 entries
    if (count($logs) > 1000) {
        $logs = array_slice($logs, -1000);
    }
    
    // Save
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ============================================================================
// REQUEST VALIDATION
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (isRateLimited()) {
    jsonResponse(['success' => false, 'message' => 'Too many requests'], 429);
}

$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    jsonResponse(['success' => false, 'message' => 'Invalid request format'], 400);
}

// ============================================================================
// CSRF VALIDATION
// ============================================================================

$csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($csrfToken !== null && !empty($_SESSION['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        logActivity("CSRF token mismatch from IP: " . getClientIp(), 'warning');
        jsonResponse(['success' => false, 'message' => 'Security validation failed'], 403);
    }
}

if ($csrfToken === null) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    $validOrigin = (
        (strpos($origin, $host) !== false) ||
        (strpos($referer, $host) !== false) ||
        empty($origin)
    );
    
    if (!$validOrigin && !empty($origin)) {
        logActivity("Invalid origin: $origin from IP: " . getClientIp(), 'warning');
        jsonResponse(['success' => false, 'message' => 'Invalid request origin'], 403);
    }
}

unset($data['csrf_token']);

// ============================================================================
// DETECT ORDER TYPE (reviews or social_media)
// ============================================================================

$orderType = $data['orderType'] ?? 'reviews';

// ============================================================================
// SOCIAL MEDIA VALIDATION (if orderType is social_media)
// ============================================================================

if ($orderType === 'social_media') {
    // Required fields for social media
    $smRequiredFields = ['platform', 'serviceType', 'smLink', 'quantity', 'email', 'whatsapp'];
    
    foreach ($smRequiredFields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && empty(trim($data[$field])))) {
            jsonResponse(['success' => false, 'message' => "Please fill in: $field"], 400);
        }
    }
    
    if (!validateEmail($data['email'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
    }
    
    // Validate platform
    $validSmPlatforms = ['Instagram', 'TikTok', 'Twitter', 'YouTube', 'Other'];
    if (!in_array($data['platform'], $validSmPlatforms)) {
        jsonResponse(['success' => false, 'message' => 'Invalid platform'], 400);
    }
    
    // Validate service type
    $validServiceTypes = ['Followers', 'Likes'];
    if (!in_array($data['serviceType'], $validServiceTypes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid service type'], 400);
    }
    
    // Validate quantity
    if (intval($data['quantity']) <= 0) {
        jsonResponse(['success' => false, 'message' => 'Invalid quantity'], 400);
    }
    
    // Calculate fulfill quantity if not provided
    $stayRate = defined('SOCIAL_MEDIA_STAY_RATE') ? SOCIAL_MEDIA_STAY_RATE : 0.8944;
    if (!isset($data['fulfillQuantity']) || intval($data['fulfillQuantity']) <= 0) {
        $data['fulfillQuantity'] = (int) ceil(intval($data['quantity']) / $stayRate);
    }
    $data['stayRate'] = $stayRate;
    
    // ============================================================================
    // PROCESS SOCIAL MEDIA ORDER
    // ============================================================================
    
    try {
        $result = addOrder($data);
        
        if ($result['success']) {
            logActivity("SM Order submitted: {$result['orderId']} from " . getClientIp(), 'info');
            
            // Log for analytics
            logSubmission([
                'orderId' => $result['orderId'],
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => getClientIp(),
                'referrer' => $data['referrer_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'Direct',
                'tracking_source' => $data['tracking_source'] ?? '',
                'businessName' => '[SM] ' . ($data['platform'] ?? 'Unknown'),
                'businessLocation' => '',
                'email' => $data['email'] ?? '',
                'quantity' => $data['quantity'] ?? 0,
                'platform' => 'Social Media - ' . ($data['platform'] ?? ''),
                'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
            
            // Create Google Sheet
            $orderData = array_merge($data, [
                'orderId' => $result['orderId'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $sheetResult = createSocialMediaOrderSheet($orderData);
            
            if ($sheetResult) {
                updateOrderSheet($result['orderId'], $sheetResult);
            }
            
            // ============================================
            // AUTO EMAIL: order confirmation (sent after the response is
            // flushed — never delays or blocks the customer's submit)
            // ============================================
            sbQueueOrderConfirmation(array_merge($orderData, [
                'orderType'  => 'social_media',
                'smPlatform' => $data['platform'] ?? '',
                'smService'  => $data['service'] ?? 'Social Media',
                'smLink'     => $data['smLink'] ?? ($data['link'] ?? ''),
            ]));
            // ============================================

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            jsonResponse([
                'success' => true,
                'orderId' => $result['orderId'],
                'message' => 'Social Media order submitted successfully!',
                'spreadsheetUrl' => $sheetResult['spreadsheetUrl'] ?? null,
                'csrf_token' => $_SESSION['csrf_token']
            ], 200);
        } else {
            logActivity("SM Order failed: {$result['message']} from " . getClientIp(), 'error');
            jsonResponse(['success' => false, 'message' => $result['message']], 500);
        }
        
    } catch (Exception $e) {
        logActivity("SM Order error: " . $e->getMessage() . " from " . getClientIp(), 'error');
        jsonResponse(['success' => false, 'message' => 'Error processing order'], 500);
    }
    
    exit; // Stop here for social media orders
}

// ============================================================================
// REVIEWS ORDER VALIDATION (existing flow)
// ============================================================================

$requiredFields = ['email', 'whatsapp', 'productType', 'ratingMix', 'mapsLink', 'finalConsent'];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || (is_string($data[$field]) && empty(trim($data[$field])))) {
        jsonResponse(['success' => false, 'message' => "Please fill in: $field"], 400);
    }
}

if (!validateEmail($data['email'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
}

$validProductTypes = ['Rating Only', 'Rating & Review'];
if (!in_array($data['productType'], $validProductTypes)) {
    jsonResponse(['success' => false, 'message' => 'Invalid product type'], 400);
}

$validRatingMix = ['4-5 Star Mix', '5 Star Only', '5 Stars Only', 'Mix (80% 5-Star + 20% 4-Star)'];
if (!in_array($data['ratingMix'], $validRatingMix)) {
    jsonResponse(['success' => false, 'message' => 'Invalid rating mix'], 400);
}

// Validate customPlatform when platform is "Other"
if (isset($data['platform']) && $data['platform'] === 'Other') {
    if (empty(trim($data['customPlatform'] ?? ''))) {
        jsonResponse(['success' => false, 'message' => 'Please specify platform name when selecting Other'], 400);
    }
}

// Also validate businesses array for "Other" platform
if (isset($data['businesses']) && is_array($data['businesses'])) {
    foreach ($data['businesses'] as $index => $business) {
        if (isset($business['platform']) && $business['platform'] === 'Other') {
            if (empty(trim($business['customPlatform'] ?? ''))) {
                $bizNum = $index + 1;
                jsonResponse(['success' => false, 'message' => "Please specify platform name for Business $bizNum"], 400);
            }
        }
    }
}

if (!validateUrl($data['mapsLink'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid business link. Please paste a valid URL.'], 400);
}

if ($data['productType'] === 'Rating & Review') {
    $reviewRequiredFields = ['quantity', 'businessType', 'businessUSP', 'reviewTone', 'reviewLength', 'businessDetails', 'keywords', 'keywordFlex'];
    
    foreach ($reviewRequiredFields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && empty(trim($data[$field])))) {
            jsonResponse(['success' => false, 'message' => "Please fill in: $field"], 400);
        }
    }
    
    $minQuantity = defined('MIN_ORDER_QUANTITY') ? MIN_ORDER_QUANTITY : 55;
    if (intval($data['quantity']) < $minQuantity) {
        jsonResponse(['success' => false, 'message' => "Minimum order: $minQuantity reviews"], 400);
    }
    
    if (intval($data['quantity']) > 1000) {
        jsonResponse(['success' => false, 'message' => 'Maximum 1000 reviews'], 400);
    }

    // Per-business floor. The wizard blocks this client-side, but a split order can carry a
    // total that clears $minQuantity while an individual business sits under it.
    if (isset($data['businesses']) && is_array($data['businesses'])) {
        foreach ($data['businesses'] as $index => $business) {
            if (!isset($business['reviews'])) continue;
            if (intval($business['reviews']) < $minQuantity) {
                $bizNum = $index + 1;
                jsonResponse(['success' => false,
                    'message' => "Business $bizNum has fewer than $minQuantity reviews. Minimum is $minQuantity per business."], 400);
            }
        }
    }
}

if ($data['finalConsent'] !== true && $data['finalConsent'] !== 'on' && $data['finalConsent'] !== 1) {
    jsonResponse(['success' => false, 'message' => 'Please agree to terms'], 400);
}

// ============================================================================
// PROCESS ORDER
// ============================================================================

try {
    $result = addOrder($data);
    
    if ($result['success']) {
        logActivity("Order submitted: {$result['orderId']} from " . getClientIp(), 'info');
        
        // ============================================
        // LOG SUBMISSION FOR ANALYTICS
        // ============================================
        $logPlatform = getDisplayPlatform($data['platform'] ?? 'Google', $data['customPlatform'] ?? '');
        logSubmission([
            'orderId' => $result['orderId'],
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => getClientIp(),
            'referrer' => $data['referrer_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'Direct',
            'tracking_source' => $data['tracking_source'] ?? '',
            'businessName' => $data['businessNames'] ?? 'Unknown',
            'businessLocation' => $data['businessLocation'] ?? '',
            'country' => $data['country'] ?? '',
            'state' => $data['state'] ?? '',
            'businessIndustry' => $data['businessIndustry'] ?? '',
            'email' => $data['email'] ?? '',
            'quantity' => $data['quantity'] ?? 0,
            'platform' => $logPlatform,
            'reviewPhaseW1' => $data['reviewPhaseW1'] ?? '',
            'reviewPhaseW2' => $data['reviewPhaseW2'] ?? '',
            'reviewPhaseW3' => $data['reviewPhaseW3'] ?? '',
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
        // ============================================
        
        // ============================================
        // AUTO CREATE GOOGLE SHEET TAB
        // ============================================
        $orderData = array_merge($data, [
            'orderId' => $result['orderId'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        $sheetResult = createOrderSheet($orderData);
        
        // Update order with sheet info if successful
        if ($sheetResult) {
            updateOrderSheet($result['orderId'], $sheetResult);
        }
        // ============================================
        
        // ============================================
        // AUTO EMAIL: order confirmation (sent after the response is
        // flushed — never delays or blocks the customer's submit)
        // ============================================
        sbQueueOrderConfirmation(array_merge($orderData, ['orderType' => 'reviews']));
        // ============================================

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        jsonResponse([
            'success' => true,
            'orderId' => $result['orderId'],
            'message' => 'Order submitted successfully!',
            'spreadsheetUrl' => $sheetResult['spreadsheetUrl'] ?? null,
            'csrf_token' => $_SESSION['csrf_token']
        ], 200);
    } else {
        logActivity("Order failed: {$result['message']} from " . getClientIp(), 'error');
        jsonResponse(['success' => false, 'message' => $result['message']], 500);
    }
    
} catch (Exception $e) {
    logActivity("Order error: " . $e->getMessage() . " from " . getClientIp(), 'error');
    jsonResponse(['success' => false, 'message' => 'Error processing order'], 500);
}

// ============================================================================
// HELPER: Update order with sheet info
// ============================================================================

function updateOrderSheet($orderId, $sheetData) {
    $dataDir = defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/data';
    $ordersFile = $dataDir . '/orders.json';
    
    if (!file_exists($ordersFile)) return;
    
    $fp = fopen($ordersFile, 'r+');
    if (!$fp) return;
    
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return;
    }
    
    $content = stream_get_contents($fp);
    $ordersData = json_decode($content, true);
    
    foreach ($ordersData['orders'] as &$order) {
        if ($order['orderId'] === $orderId) {
            $order['spreadsheetId'] = $sheetData['spreadsheetId'];
            $order['sheetId'] = $sheetData['sheetId'];
            $order['sheetTitle'] = $sheetData['sheetTitle'];
            $order['spreadsheetUrl'] = $sheetData['spreadsheetUrl'];
            break;
        }
    }
    
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($ordersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}