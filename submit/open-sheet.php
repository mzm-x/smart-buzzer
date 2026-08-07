<?php
/**
 * ============================================================================
 * File: /order/open-sheet.php
 * Smart Buzzer Spreadsheet Handler - v3.2 (Review Phase)
 * 
 * v3.2 NEW: Added Review Phase W1/W2/W3 fields to reviews sheet
 * v3.1 NEW: Added Country, State/Region, Business Industry to reviews sheet
 * v3.0 NEW: Social Media order sheet format with purple theme
 * 
 * Strategy: 1 Master Spreadsheet, 1 Tab per Order
 * - No storage quota needed
 * - Max 200 orders per spreadsheet
 * ============================================================================
 */

// ============================================================================
// ERROR HANDLING
// ============================================================================

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Fatal PHP Error',
            'debug' => [
                'error' => $error['message'],
                'file' => basename($error['file']),
                'line' => $error['line']
            ]
        ]);
    }
});

ob_start();

// ============================================================================
// CONFIGURATION
// ============================================================================

// Master Spreadsheet ID (semua order akan ditambahkan sebagai tab baru di sini)
$MASTER_SPREADSHEET_ID = '1WzOBE3ReJCjwjfqAqWEqw50wLkJdggQ0xQWr0U5dg9o';

// Paths
$CREDENTIALS_PATH = __DIR__ . '/credentials/service-account.json';
$ORDERS_FILE = __DIR__ . '/data/orders.json';
$DEBUG_LOG = __DIR__ . '/data/debug.log';

// ============================================================================
// DEBUG LOGGING
// ============================================================================

function debugLog($message) {
    global $DEBUG_LOG;
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($DEBUG_LOG, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// ============================================================================
// CORS HANDLING
// ============================================================================

function handleCORS() {
    $allowedOrigins = [
        'https://smart-buzzer.com',
        'https://www.smart-buzzer.com',
        'http://localhost',
        'http://127.0.0.1'
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        header("Access-Control-Allow-Origin: https://smart-buzzer.com");
    }
    
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

handleCORS();

// ============================================================================
// SESSION & AUTH
// ============================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ============================================================================
// VENDOR CHECK
// ============================================================================

$vendorAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($vendorAutoload)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Google API Client not installed. Run: composer require google/apiclient:^2.0'
    ]);
    exit;
}

require_once $vendorAutoload;

if (!class_exists('Google_Client')) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Google_Client class not found'
    ]);
    exit;
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Get order from JSON database
 */
function getOrder($orderId) {
    global $ORDERS_FILE;
    
    if (!file_exists($ORDERS_FILE)) {
        throw new Exception('Orders file not found');
    }
    
    $ordersData = json_decode(file_get_contents($ORDERS_FILE), true);
    
    if (!isset($ordersData['orders'])) {
        throw new Exception('Invalid orders data');
    }
    
    foreach ($ordersData['orders'] as $order) {
        if ($order['orderId'] === $orderId) {
            return $order;
        }
    }
    
    throw new Exception('Order not found: ' . $orderId);
}

/**
 * Update order in JSON database
 */
function updateOrder($orderId, $updates) {
    global $ORDERS_FILE;
    
    $fp = fopen($ORDERS_FILE, 'r+');
    if (!$fp) throw new Exception('Cannot open orders file');
    
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        throw new Exception('Cannot lock orders file');
    }
    
    $content = stream_get_contents($fp);
    $ordersData = json_decode($content, true);
    
    $found = false;
    foreach ($ordersData['orders'] as &$order) {
        if ($order['orderId'] === $orderId) {
            foreach ($updates as $key => $value) {
                $order[$key] = $value;
            }
            $order['lastUpdated'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('Order not found: ' . $orderId);
    }
    
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($ordersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * Initialize Google Sheets API client
 */
function initGoogleClient() {
    global $CREDENTIALS_PATH;
    
    $client = new Google_Client();
    $client->setApplicationName('Smart Buzzer Order System');
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
    $client->setAuthConfig($CREDENTIALS_PATH);
    $client->setAccessType('offline');
    
    return $client;
}

/**
 * Add new sheet (tab) to master spreadsheet
 */
function addOrderSheet($sheetsService, $spreadsheetId, $sheetTitle) {
    $requests = [
        new Google_Service_Sheets_Request([
            'addSheet' => [
                'properties' => [
                    'title' => $sheetTitle
                ]
            ]
        ])
    ];
    
    $batchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
        'requests' => $requests
    ]);
    
    $response = $sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchRequest);
    
    // Get the new sheet ID
    $replies = $response->getReplies();
    $sheetId = $replies[0]->getAddSheet()->getProperties()->getSheetId();
    
    return $sheetId;
}

/**
 * Populate sheet with order data
 */
function populateOrderSheet($sheetsService, $spreadsheetId, $sheetTitle, $order) {
    // Define field labels and values (vertical format)
    $fields = [
        ['Order ID', $order['orderId'] ?? ''],
        ['Timestamp', $order['timestamp'] ?? ''],
        ['Business Names', $order['businessNames'] ?? ''],
        ['Email', $order['email'] ?? ''],
        ['WhatsApp', $order['whatsapp'] ?? ''],
        ['Country', $order['country'] ?? ''],
        ['State / Region', $order['state'] ?? ''],
        ['Business Industry', $order['businessIndustry'] ?? ''],
        ['Platform', $order['platform'] ?? 'Google'],
        ['Product Type', $order['productType'] ?? ''],
        ['Rating Mix', $order['ratingMix'] ?? ''],
        ['Quantity', $order['quantity'] ?? 'N/A'],
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
        ['Status', $order['status'] ?? 'Pending'],
        ['AM Notes', ''],
        ['Payment Status', $order['paymentStatus'] ?? 'Pending'],
        ['Order Value', ''],
        ['Last Updated', $order['lastUpdated'] ?? '']
    ];
    
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $fields
    ]);
    
    $params = [
        'valueInputOption' => 'RAW'
    ];
    
    // Write to the new sheet
    $range = "'" . $sheetTitle . "'!A1:B" . count($fields);
    $sheetsService->spreadsheets_values->update(
        $spreadsheetId,
        $range,
        $body,
        $params
    );
}

/**
 * Format sheet (bold labels, adjust width, text wrap)
 */
function formatOrderSheet($sheetsService, $spreadsheetId, $sheetId) {
    $requests = [
        // Bold Column A (labels)
        new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    'endRowIndex' => 31,
                    'startColumnIndex' => 0,
                    'endColumnIndex' => 1
                ],
                'cell' => [
                    'userEnteredFormat' => [
                        'textFormat' => ['bold' => true],
                        'backgroundColor' => [
                            'red' => 0.95,
                            'green' => 0.95,
                            'blue' => 0.95
                        ],
                        'verticalAlignment' => 'TOP'
                    ]
                ],
                'fields' => 'userEnteredFormat(textFormat,backgroundColor,verticalAlignment)'
            ]
        ]),
        // Text wrap for Column B (values)
        new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    'endRowIndex' => 31,
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
        // Set column widths
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
        ]),
        // Freeze first column
        new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'gridProperties' => [
                        'frozenColumnCount' => 1
                    ]
                ],
                'fields' => 'gridProperties.frozenColumnCount'
            ]
        ])
    ];
    
    $batchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
        'requests' => $requests
    ]);
    
    $sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchRequest);
}

/**
 * Populate sheet with Social Media order data
 */
function populateSocialMediaSheet($sheetsService, $spreadsheetId, $sheetTitle, $order) {
    $stayRatePercent = round(($order['stayRate'] ?? 0.8944) * 100, 2) . '%';
    
    $fields = [
        ['Order ID', $order['orderId'] ?? ''],
        ['Order Type', 'Social Media'],
        ['Timestamp', $order['timestamp'] ?? ''],
        ['Email', $order['email'] ?? ''],
        ['WhatsApp', $order['whatsapp'] ?? ''],
        ['Platform', $order['platform'] ?? ''],
        ['Service Type', $order['serviceType'] ?? ''],
        ['Profile/Post Link', $order['smLink'] ?? ''],
        ['Target Quantity', $order['quantity'] ?? 0],
        ['Fulfill Quantity', $order['fulfillQuantity'] ?? 0],
        ['Stay Rate', $stayRatePercent],
        ['Status', $order['status'] ?? 'Pending'],
        ['AM Notes', ''],
        ['Payment Status', $order['paymentStatus'] ?? 'Pending'],
        ['Order Value', ''],
        ['Last Updated', $order['lastUpdated'] ?? '']
    ];
    
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $fields
    ]);
    
    $params = [
        'valueInputOption' => 'RAW'
    ];
    
    $range = "'" . $sheetTitle . "'!A1:B" . count($fields);
    $sheetsService->spreadsheets_values->update(
        $spreadsheetId,
        $range,
        $body,
        $params
    );
}

/**
 * Format Social Media sheet (purple theme)
 */
function formatSocialMediaSheet($sheetsService, $spreadsheetId, $sheetId) {
    $requests = [
        // Bold Column A (labels) with purple background
        new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    'endRowIndex' => 16,
                    'startColumnIndex' => 0,
                    'endColumnIndex' => 1
                ],
                'cell' => [
                    'userEnteredFormat' => [
                        'textFormat' => ['bold' => true],
                        'backgroundColor' => [
                            'red' => 0.9,
                            'green' => 0.85,
                            'blue' => 1.0
                        ],
                        'verticalAlignment' => 'TOP'
                    ]
                ],
                'fields' => 'userEnteredFormat(textFormat,backgroundColor,verticalAlignment)'
            ]
        ]),
        // Text wrap for Column B (values)
        new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    'endRowIndex' => 16,
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
        // Set column widths
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
        ]),
        // Freeze first column
        new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'gridProperties' => [
                        'frozenColumnCount' => 1
                    ]
                ],
                'fields' => 'gridProperties.frozenColumnCount'
            ]
        ])
    ];
    
    $batchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
        'requests' => $requests
    ]);
    
    $sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchRequest);
}

// ============================================================================
// MAIN PROCESS
// ============================================================================

try {
    if (ob_get_level()) ob_end_clean();
    
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    
    debugLog("=== New request started ===");
    
    // Get POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['orderId'])) {
        throw new Exception('Order ID is required');
    }
    
    $orderId = $data['orderId'];
    debugLog("Processing order: $orderId");
    
    // Get order data
    $order = getOrder($orderId);
    debugLog("Order found: " . $order['email']);
    
    // Check if already has spreadsheet
    if (!empty($order['spreadsheetId']) && !empty($order['sheetId'])) {
        debugLog("Order already has sheet, returning existing URL");
        $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$order['spreadsheetId']}/edit#gid={$order['sheetId']}";
        
        echo json_encode([
            'success' => true,
            'spreadsheetUrl' => $spreadsheetUrl,
            'spreadsheetId' => $order['spreadsheetId'],
            'sheetId' => $order['sheetId'],
            'message' => 'Sheet already exists'
        ]);
        exit;
    }
    
    // Initialize Google Client
    debugLog("Initializing Google Client...");
    $client = initGoogleClient();
    $sheetsService = new Google_Service_Sheets($client);
    debugLog("Google Client initialized");
    
    // Create sheet title from client email
    $orderType = $order['orderType'] ?? 'reviews';
    $sheetPrefix = $orderType === 'social_media' ? '[SM] ' : '';
    $sheetTitle = $sheetPrefix . ($order['email'] ?? $orderId);
    debugLog("Creating new sheet: $sheetTitle (Type: $orderType)");
    
    // Add new sheet to master spreadsheet
    global $MASTER_SPREADSHEET_ID;
    $sheetId = addOrderSheet($sheetsService, $MASTER_SPREADSHEET_ID, $sheetTitle);
    debugLog("Sheet created with ID: $sheetId");
    
    // Populate and format based on order type
    if ($orderType === 'social_media') {
        debugLog("Populating Social Media sheet...");
        populateSocialMediaSheet($sheetsService, $MASTER_SPREADSHEET_ID, $sheetTitle, $order);
        debugLog("Formatting Social Media sheet...");
        formatSocialMediaSheet($sheetsService, $MASTER_SPREADSHEET_ID, $sheetId);
    } else {
        debugLog("Populating Reviews sheet...");
        populateOrderSheet($sheetsService, $MASTER_SPREADSHEET_ID, $sheetTitle, $order);
        debugLog("Formatting Reviews sheet...");
        formatOrderSheet($sheetsService, $MASTER_SPREADSHEET_ID, $sheetId);
    }
    debugLog("Sheet populated and formatted");
    
    // Generate URL (with gid parameter to open specific sheet)
    $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$MASTER_SPREADSHEET_ID}/edit#gid={$sheetId}";
    debugLog("Sheet URL: $spreadsheetUrl");
    
    // Update order in database
    debugLog("Updating order in database...");
    updateOrder($orderId, [
        'spreadsheetId' => $MASTER_SPREADSHEET_ID,
        'sheetId' => $sheetId,
        'sheetTitle' => $sheetTitle,
        'spreadsheetUrl' => $spreadsheetUrl
    ]);
    debugLog("Order updated successfully");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'spreadsheetUrl' => $spreadsheetUrl,
        'spreadsheetId' => $MASTER_SPREADSHEET_ID,
        'sheetId' => $sheetId,
        'sheetTitle' => $sheetTitle,
        'message' => 'Sheet created successfully'
    ]);
    
    debugLog("=== Request completed successfully ===");
    
} catch (Google_Service_Exception $e) {
    if (ob_get_level()) ob_end_clean();
    
    $error = json_decode($e->getMessage(), true);
    $message = $error['error']['message'] ?? $e->getMessage();
    
    debugLog("Google API Error: $message");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Google API Error: ' . $message,
        'debug' => [
            'type' => 'Google_Service_Exception',
            'hint' => 'Check if service account has proper permissions'
        ]
    ]);
    
} catch (Exception $e) {
    if (ob_get_level()) ob_end_clean();
    
    debugLog("Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'type' => get_class($e),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ]);
}