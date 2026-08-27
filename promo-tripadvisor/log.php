<?php
// Combined Log.php - Logger & Viewer with Analytics Dashboard
session_start();

// Password untuk view logs
$password = "smartbuzzer2025";

// Check for "Remember Me" cookie
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_me'])) {
    if ($_COOKIE['remember_me'] === hash('sha256', $password . 'salt_key')) {
        $_SESSION['logged_in'] = true;
    }
}

// === HANDLE AJAX LOGGING REQUEST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Only log if this is a form submission with business data
    if (isset($data['businessName'])) {
        $timestamp = date('Y-m-d H:i:s');
        
        // Parse UTM from URL (match analytics.php: parse_url + parse_str only, no extra urldecode)
        $pageUrl = isset($data['pageUrl']) ? $data['pageUrl'] : (isset($data['url']) ? $data['url'] : '-');
        $utmSource = 'direct';
        $utmMedium = 'none';
        $utmCampaign = 'direct';
        $utmContent = '-';
        $utmTerm = '-';
        $placement = '-';

        if (!empty($pageUrl) && $pageUrl !== '-') {
            $parsed = parse_url($pageUrl);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                $utmSource = isset($params['utm_source']) ? trim($params['utm_source']) : 'direct';
                $utmMedium = isset($params['utm_medium']) ? trim($params['utm_medium']) : 'none';
                $utmCampaign = isset($params['utm_campaign']) ? trim($params['utm_campaign']) : 'direct';
                $utmContent = isset($params['utm_content']) ? trim($params['utm_content']) : '-';
                $utmTerm = isset($params['utm_term']) ? trim($params['utm_term']) : '-';
                $placement = isset($params['utm_placement']) ? trim($params['utm_placement']) : (isset($params['placement']) ? trim($params['placement']) : '-');
            }
        }
        
        // Get country, state, and zip from IP
        $country = '-';
        $state = '-';
        $zipCode = '-';
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = $_SERVER['HTTP_CLIENT_IP'];
            if (filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $clientIp;
            }
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $forwardedIp = trim($ipList[0]);
            if (filter_var($forwardedIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $forwardedIp;
            }
        }
        
        try {
            $geoData = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,countryCode,region,zip", false, stream_context_create([
                'http' => ['timeout' => 3]
            ]));
            if ($geoData !== false) {
                $geo = json_decode($geoData, true);
                if (isset($geo['status']) && $geo['status'] === 'success') {
                    $country = isset($geo['countryCode']) ? $geo['countryCode'] : '-';
                    $state = isset($geo['region']) ? $geo['region'] : '-';
                    $zipCode = isset($geo['zip']) ? $geo['zip'] : '-';
                }
            }
        } catch (Exception $e) {
            // Keep defaults as -
        }
        
        $businessName = isset($data['businessName']) ? $data['businessName'] : '-';
        $location = isset($data['location']) ? $data['location'] : '-';
        $businessEmail = isset($data['businessEmail']) ? $data['businessEmail'] : '-';
        $whatsapp = isset($data['whatsapp']) ? $data['whatsapp'] : '-';
        $action = isset($data['action']) ? $data['action'] : 'N/A';
        
        // Ensure WhatsApp has country code
        if ($whatsapp !== '-' && !empty($whatsapp)) {
            if (substr($whatsapp, 0, 1) !== '+') {
                $whatsapp = '+' . $whatsapp;
            }
        }
        
        // Determine package and reviews quantity
        $packageName = 'Unknown';
        $reviewsQty = '-';
        
        if (strpos(strtoupper($action), 'TRIPADVISOR_STARTER') !== false) {
            $packageName = 'Starter';
            $reviewsQty = '40';
        } elseif (strpos(strtoupper($action), 'TRIPADVISOR_GROWTH') !== false) {
            $packageName = 'Growth';
            $reviewsQty = '50';
        } elseif (strpos(strtoupper($action), 'TRIPADVISOR_PERFORMANCE') !== false) {
            $packageName = 'Performance';
            $reviewsQty = '63';
        } elseif (strpos(strtoupper($action), 'STARTER') !== false) {
            $packageName = 'Starter';
            $reviewsQty = '55';
        } elseif (strpos(strtoupper($action), 'GROWTH') !== false) {
            $packageName = 'Growth';
            $reviewsQty = '88';
        } elseif (strpos(strtoupper($action), 'PERFORMANCE') !== false) {
            $packageName = 'Performance';
            $reviewsQty = '110';
        }
        
        // Sanitize all values: strip tabs/newlines to prevent TSV corruption
        $sbSanitize = function($v) { return str_replace(["\t", "\n", "\r"], '', $v); };
        $businessName = $sbSanitize($businessName);
        $location = $sbSanitize($location);
        $businessEmail = $sbSanitize($businessEmail);
        $whatsapp = $sbSanitize($whatsapp);
        $packageName = $sbSanitize($packageName);
        $pageUrl = $sbSanitize($pageUrl);
        $utmSource = $sbSanitize($utmSource);
        $utmMedium = $sbSanitize($utmMedium);
        $utmCampaign = $sbSanitize($utmCampaign);
        $utmContent = $sbSanitize($utmContent);
        $utmTerm = $sbSanitize($utmTerm);
        $placement = $sbSanitize($placement);
        
        // Format: Timestamp | Business | Location | Email | WhatsApp | Package | PageURL | ReviewsQty | UTM_Source | UTM_Medium | UTM_Campaign | UTM_Content | Placement | State | Zip | Country | Status | UTM_Term
        $customerLine = implode("\t", [
            $timestamp,
            $businessName,
            $location,
            $businessEmail,
            $whatsapp,
            $packageName,
            $pageUrl,
            $reviewsQty,
            $utmSource,
            $utmMedium,
            $utmCampaign,
            $utmContent,
            $placement,
            $state,
            $zipCode,
            $country,
            'FORM_SUBMIT',
            $utmTerm
        ]) . "\n";
        
        $customerFile = __DIR__ . '/customer_data.log';
        $success = @file_put_contents($customerFile, $customerLine, FILE_APPEND | LOCK_EX);
        
        if ($success !== false) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data logged successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to write log'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'No business data to log'
        ]);
    }
    exit;
}

// === HANDLE DELETE REQUEST ===
if (isset($_POST['delete_entries']) && isset($_SESSION['logged_in'])) {
    $file = __DIR__ . '/customer_data.log';
    $indexesToDelete = isset($_POST['selected']) ? $_POST['selected'] : [];
    
    if (!empty($indexesToDelete) && file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines); // Reverse to match display order
        
        // Remove selected lines
        foreach ($indexesToDelete as $index) {
            if (isset($lines[$index])) {
                unset($lines[$index]);
            }
        }
        
        $lines = array_reverse($lines); // Reverse back
        file_put_contents($file, implode("\n", $lines) . "\n");
        
        $_SESSION['delete_message'] = count($indexesToDelete) . " customer data entries deleted successfully!";
    }
    
    header('Location: log.php?tab=customers');
    exit;
}

// === HANDLE TOGGLE FOLLOW-UP REQUEST ===
if (isset($_POST['toggle_followup']) && isset($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    
    $file = __DIR__ . '/customer_data.log';
    $index = intval($_POST['index']);
    
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines); // Reverse to match display order
        
        if (isset($lines[$index])) {
            $currentLine = $lines[$index];
            $lineParts = explode("\t", $currentLine);
            $numLineParts = count($lineParts);

            // Find status column index (index 16 for 17+ col format, index 14 for old format)
            $statusIdx = ($numLineParts >= 17) ? 16 : (($numLineParts >= 15) ? 14 : -1);

            if ($statusIdx >= 0 && isset($lineParts[$statusIdx])) {
                $currentStatus = trim($lineParts[$statusIdx]);

                if ($currentStatus === 'FOLLOWED_UP') {
                    // Restore original status
                    $originalStatus = (isset($lineParts[1]) && trim($lineParts[1]) === '-') ? 'CLICK_ONLY' : 'FORM_SUBMIT';
                    $lineParts[$statusIdx] = $originalStatus;
                    $followedUp = false;
                } else {
                    // Set FOLLOWED_UP
                    $lineParts[$statusIdx] = 'FOLLOWED_UP';
                    $followedUp = true;
                }

                $lines[$index] = implode("\t", $lineParts);
            } else {
                // Fallback for very old format - append status
                if (strpos($currentLine, "\tFOLLOWED_UP") !== false) {
                    $lines[$index] = str_replace("\tFOLLOWED_UP", "\tFORM_SUBMIT", $currentLine);
                    $followedUp = false;
                } else {
                    $lines[$index] = $currentLine . "\tFOLLOWED_UP";
                    $followedUp = true;
                }
            }
            
            $lines = array_reverse($lines); // Reverse back
            file_put_contents($file, implode("\n", $lines) . "\n");
            
            echo json_encode(['status' => 'success', 'followedUp' => $followedUp]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Index not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found']);
    }
    exit;
}

// === HANDLE AJAX SEARCH REQUEST ===
if (isset($_GET['ajax_search']) && isset($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    
    $file = __DIR__ . '/customer_data.log';
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
    $filterCampaign = isset($_GET['campaign']) ? $_GET['campaign'] : 'all';
    $filterPage = isset($_GET['page']) ? $_GET['page'] : 'all';
    
    $customers = [];
    
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);
        
        foreach ($lines as $index => $line) {
            $parts = explode("\t", $line);
            $numParts = count($parts);
            
            if ($numParts >= 14) {
                $followedUp = false;
                $isClickOnly = false;
                $pageUrl = $parts[6];
                
                // BUG FIX #2: Detect format (NEW: 16-18 cols, OLD: 14-15 cols)
                $utmTerm = '-';
                if ($numParts >= 16) {
                    // NEW FORMAT: Timestamp|Business|Location|Email|WhatsApp|Package|PageURL|ReviewsQty|UTM_Source|UTM_Medium|UTM_Campaign|UTM_Content|Placement|State|Zip|Country|Status|UTM_Term
                    $utmSource = $parts[8];
                    $utmMedium = $parts[9];
                    $utmCampaign = $parts[10];
                    $utmContent = $parts[11];
                    $placement = $parts[12];
                    $state = $parts[13];
                    $zipCode = $parts[14];
                    $country = $parts[15];

                    if ($numParts > 16) {
                        $flag = trim($parts[16]);
                        $followedUp = ($flag === 'FOLLOWED_UP');
                        $isClickOnly = ($flag === 'CLICK_ONLY');
                    }
                    if ($numParts >= 18) {
                        $utmTerm = trim($parts[17]);
                    }
                } else {
                    // OLD FORMAT: Parse UTM from pageUrl
                    $utmData = parseUtmFromUrl($pageUrl);
                    $utmSource = $utmData['utm_source'];
                    $utmMedium = $utmData['utm_medium'];
                    $utmCampaign = $utmData['utm_campaign'];
                    $utmContent = $utmData['utm_content'];
                    $utmTerm = $utmData['utm_term'];
                    $placement = $utmData['placement'];
                    $state = $parts[11];
                    $zipCode = $parts[12];
                    $country = $parts[13];

                    if ($numParts > 14) {
                        $flag = trim($parts[14]);
                        $followedUp = ($flag === 'FOLLOWED_UP');
                        $isClickOnly = ($flag === 'CLICK_ONLY');
                    }
                }

                $customer = [
                    'index' => $index,
                    'timestamp' => $parts[0],
                    'businessName' => $parts[1],
                    'location' => $parts[2],
                    'businessEmail' => $parts[3],
                    'whatsapp' => $parts[4],
                    'package' => $parts[5],
                    'pageUrl' => $pageUrl,
                    'reviewsQty' => $parts[7],
                    'state' => $state,
                    'zipCode' => $zipCode,
                    'country' => $country,
                    'followedUp' => $followedUp,
                    'isClickOnly' => $isClickOnly,
                    'utmSource' => $utmSource,
                    'utmMedium' => $utmMedium,
                    'utmCampaign' => $utmCampaign,
                    'utmContent' => $utmContent,
                    'utmTerm' => $utmTerm,
                    'placement' => $placement
                ];
                
                // Apply filters
                $matchesSearch = empty($searchQuery) || 
                    stripos($customer['businessName'], $searchQuery) !== false ||
                    stripos($customer['location'], $searchQuery) !== false ||
                    stripos($customer['businessEmail'], $searchQuery) !== false ||
                    stripos($customer['whatsapp'], $searchQuery) !== false ||
                    stripos($customer['utmCampaign'], $searchQuery) !== false ||
                    stripos($customer['utmSource'], $searchQuery) !== false ||
                    stripos($customer['package'], $searchQuery) !== false ||
                    stripos($customer['pageUrl'], $searchQuery) !== false;
                
                $matchesStatus = ($filterStatus === 'all') ||
                    ($filterStatus === 'pending' && !$followedUp) ||
                    ($filterStatus === 'followed' && $followedUp);
                
                $matchesCampaign = ($filterCampaign === 'all') || ($customer['utmCampaign'] === $filterCampaign);
                $matchesPage = ($filterPage === 'all') || ($customer['pageUrl'] === $filterPage);
                
                if ($matchesSearch && $matchesStatus && $matchesCampaign && $matchesPage) {
                    $customers[] = $customer;
                }
            }
        }
    }
    
    echo json_encode(['status' => 'success', 'customers' => $customers]);
    exit;
}

// === HELPER FUNCTION: Parse UTM from URL ===
function parseUtmFromUrl($url) {
    $result = [
        'utm_source' => 'direct',
        'utm_medium' => 'none',
        'utm_campaign' => 'direct',
        'utm_content' => '-',
        'utm_term' => '-',
        'placement' => '-'
    ];

    if (empty($url) || $url === '-') {
        return $result;
    }

    // parse_url + parse_str only (parse_str auto-decodes, no extra urldecode needed)
    $parsed = parse_url($url);

    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $params);
        $result['utm_source'] = isset($params['utm_source']) ? trim($params['utm_source']) : 'direct';
        $result['utm_medium'] = isset($params['utm_medium']) ? trim($params['utm_medium']) : 'none';
        $result['utm_campaign'] = isset($params['utm_campaign']) ? trim($params['utm_campaign']) : 'direct';
        $result['utm_content'] = isset($params['utm_content']) ? trim($params['utm_content']) : '-';
        $result['utm_term'] = isset($params['utm_term']) ? trim($params['utm_term']) : '-';
        $result['placement'] = isset($params['utm_placement']) ? trim($params['utm_placement']) : (isset($params['placement']) ? trim($params['placement']) : '-');
    }

    return $result;
}

// === LOGIN HANDLING ===
if (isset($_POST['password']) && !isset($_SESSION['logged_in'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['logged_in'] = true;
        
        // Set cookie if "Remember Me" is checked
        if (isset($_POST['remember'])) {
            $cookieValue = hash('sha256', $password . 'salt_key');
            setcookie('remember_me', $cookieValue, time() + (60 * 60 * 24 * 60), '/'); // 60 days
        }
        
        header('Location: log.php');
        exit;
    } else {
        $error = "Incorrect password!";
    }
}

// === HANDLE CSV EXPORT ===
if (isset($_GET['export']) && isset($_SESSION['logged_in'])) {
    $file = __DIR__ . '/customer_data.log';
    
    if (file_exists($file)) {
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
        $filterCampaign = isset($_GET['campaign']) ? $_GET['campaign'] : 'all';
        $filterPage = isset($_GET['page']) ? $_GET['page'] : 'all';
        
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);
        
        $customers = [];
        foreach ($lines as $line) {
            $parts = explode("\t", $line);
            $numParts = count($parts);
            
            if ($numParts >= 14) {
                $followedUp = false;
                $isClickOnly = false;
                $pageUrl = $parts[6];
                
                // BUG FIX #5: Detect format (NEW: 16-18 cols, OLD: 14-15 cols)
                $utmTerm = '-';
                if ($numParts >= 16) {
                    // NEW FORMAT
                    $utmSource = $parts[8];
                    $utmMedium = $parts[9];
                    $utmCampaign = $parts[10];
                    $utmContent = $parts[11];
                    $placement = $parts[12];
                    $state = $parts[13];
                    $zipCode = $parts[14];
                    $country = $parts[15];

                    if ($numParts > 16) {
                        $flag = trim($parts[16]);
                        $followedUp = ($flag === 'FOLLOWED_UP');
                        $isClickOnly = ($flag === 'CLICK_ONLY');
                    }
                    if ($numParts >= 18) {
                        $utmTerm = trim($parts[17]);
                    }
                } else {
                    // OLD FORMAT
                    $utmData = parseUtmFromUrl($pageUrl);
                    $utmSource = $utmData['utm_source'];
                    $utmMedium = $utmData['utm_medium'];
                    $utmCampaign = $utmData['utm_campaign'];
                    $utmContent = $utmData['utm_content'];
                    $utmTerm = $utmData['utm_term'];
                    $placement = $utmData['placement'];
                    $state = $parts[11];
                    $zipCode = $parts[12];
                    $country = $parts[13];

                    if ($numParts > 14) {
                        $flag = trim($parts[14]);
                        $followedUp = ($flag === 'FOLLOWED_UP');
                        $isClickOnly = ($flag === 'CLICK_ONLY');
                    }
                }

                $customer = [
                    'timestamp' => $parts[0],
                    'businessName' => $parts[1],
                    'location' => $parts[2],
                    'businessEmail' => $parts[3],
                    'whatsapp' => $parts[4],
                    'package' => $parts[5],
                    'pageUrl' => $pageUrl,
                    'reviewsQty' => $parts[7],
                    'state' => $state,
                    'zipCode' => $zipCode,
                    'country' => $country,
                    'followedUp' => $followedUp,
                    'isClickOnly' => $isClickOnly,
                    'utmSource' => $utmSource,
                    'utmMedium' => $utmMedium,
                    'utmCampaign' => $utmCampaign,
                    'utmContent' => $utmContent,
                    'utmTerm' => $utmTerm,
                    'placement' => $placement
                ];
                
                // Apply filters
                $matchesSearch = empty($searchQuery) || 
                    stripos($customer['businessName'], $searchQuery) !== false ||
                    stripos($customer['location'], $searchQuery) !== false ||
                    stripos($customer['businessEmail'], $searchQuery) !== false ||
                    stripos($customer['whatsapp'], $searchQuery) !== false;
                
                $matchesStatus = ($filterStatus === 'all') ||
                    ($filterStatus === 'pending' && !$followedUp) ||
                    ($filterStatus === 'followed' && $followedUp);
                
                $matchesCampaign = ($filterCampaign === 'all') || ($customer['utmCampaign'] === $filterCampaign);
                $matchesPage = ($filterPage === 'all') || ($customer['pageUrl'] === $filterPage);
                
                if ($matchesSearch && $matchesStatus && $matchesCampaign && $matchesPage) {
                    $customers[] = $customer;
                }
            }
        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customer_data_' . date('Y-m-d_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Timestamp', 'Business Name', 'Location', 'Email', 'WhatsApp', 'Package', 'Page URL', 'Reviews Qty', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content (Ad)', 'UTM Term (Adset)', 'Placement', 'State', 'Zip Code', 'Country', 'Type', 'Followed Up']);
        
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['timestamp'],
                $customer['businessName'],
                $customer['location'],
                $customer['businessEmail'],
                $customer['whatsapp'],
                $customer['package'],
                $customer['pageUrl'],
                $customer['reviewsQty'],
                $customer['utmSource'],
                $customer['utmMedium'],
                $customer['utmCampaign'],
                $customer['utmContent'],
                $customer['utmTerm'],
                $customer['placement'],
                $customer['state'],
                $customer['zipCode'],
                $customer['country'],
                $customer['isClickOnly'] ? 'Click Only' : 'Form Submit',
                $customer['followedUp'] ? 'Yes' : 'No'
            ]);
        }
        
        fclose($output);
        exit;
    }
}

// === SHOW LOGIN PAGE IF NOT LOGGED IN ===
if (!isset($_SESSION['logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Smart Buzzer - Login</title>
        <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.2);
                width: 100%;
                max-width: 400px;
            }
            h2 {
                text-align: center;
                color: #172B4D;
                margin-bottom: 30px;
                font-size: 28px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 8px;
                color: #5E6C84;
                font-weight: 600;
            }
            input[type="password"] {
                width: 100%;
                padding: 12px;
                border: 2px solid #DFE1E6;
                border-radius: 6px;
                font-size: 15px;
                transition: all 0.2s;
            }
            input[type="password"]:focus {
                outline: none;
                border-color: #0066FF;
            }
            .remember-me {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
            }
            .remember-me input {
                margin-right: 8px;
            }
            .remember-me label {
                margin: 0;
                font-weight: 400;
                cursor: pointer;
            }
            button {
                width: 100%;
                padding: 12px;
                background: #0066FF;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }
            button:hover {
                background: #0052CC;
            }
            .error {
                background: #FFEBE6;
                color: #DE350B;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 20px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2><img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" style="height: 28px; vertical-align: middle; margin-right: 8px;">Dashboard</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me for 60 days</label>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// === PARSE ANALYTICS DATA ===
$analyticsFile = __DIR__ . '/page_analytics.log';
$analyticsData = [];
$dateFilter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '7days';

// Global date range - applies to ALL tabs
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Use same date for Campaign Breakdown (unified)
$cbStartDate = $startDate;
$cbEndDate = $endDate;

if (file_exists($analyticsFile)) {
    $lines = file($analyticsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $parts = explode("\t", $line);
        if (count($parts) >= 11) {
            $timestamp = $parts[0];
            $date = substr($timestamp, 0, 10);
            
            // Date filtering
            if ($date >= $startDate && $date <= $endDate) {
                $eventData = json_decode($parts[3], true) ?: [];
                
                $analyticsData[] = [
                    'timestamp' => $parts[0],
                    'event_type' => $parts[1],
                    'page_url' => $parts[2],
                    'data' => $eventData,
                    'device' => $parts[4],
                    'session_id' => $parts[5],
                    'campaign' => $parts[6],
                    'source' => $parts[7],
                    'medium' => $parts[8],
                    'ip' => $parts[9],
                    'country' => $parts[10]
                ];
            }
        }
    }
}

// Calculate analytics metrics
$pageViews = 0;
$pricingClicks = 0;
$starterClicks = 0;
$growthClicks = 0;
$performanceClicks = 0;
$formSubmits = 0;
$timeOnPageData = [];
$scrollDepth = ['25' => 0, '50' => 0, '75' => 0, '100' => 0];
$deviceStats = ['Desktop' => ['views' => 0, 'clicks' => 0], 'Mobile' => ['views' => 0, 'clicks' => 0], 'Tablet' => ['views' => 0, 'clicks' => 0]];
$hourlyStats = array_fill(0, 24, ['views' => 0, 'clicks' => 0]);
$dailyStats = [];
$returnVisitors = 0;
$newVisitors = 0;
$exitPages = [];
$uniqueSessions = [];
$uniqueIPs = [];
$sessionsWithClicks = [];
// Session-deduped funnel. Keyed by session_id so lookups stay O(1) on big logs.
$sessionsWithViews = [];
$sessionsWithSubmits = [];
$externalClicks = [];
$sessionPackageClicks = []; // Track which package each session clicked

foreach ($analyticsData as $event) {
    $eventType = $event['event_type'];
    $device = $event['device'];
    $hour = (int)date('G', strtotime($event['timestamp']));
    $date = substr($event['timestamp'], 0, 10);
    $sessionId = $event['session_id'];
    $ip = $event['ip'];
    
    // Track unique sessions
    if (!in_array($sessionId, $uniqueSessions)) {
        $uniqueSessions[] = $sessionId;
    }
    
    // Track unique IPs (for unique users)
    if (!in_array($ip, $uniqueIPs)) {
        $uniqueIPs[] = $ip;
    }
    
    // Initialize daily stats
    if (!isset($dailyStats[$date])) {
        $dailyStats[$date] = ['views' => 0, 'clicks' => 0];
    }
    
    // Page views
    if ($eventType === 'PAGE_VIEW') {
        $pageViews++;
        $sessionsWithViews[$sessionId] = true;
        $dailyStats[$date]['views']++;
        if (isset($deviceStats[$device])) {
            $deviceStats[$device]['views']++;
        }
        $hourlyStats[$hour]['views']++;
    }
    
    // Pricing clicks
    if (strpos($eventType, 'ORDER_') === 0 && strpos($eventType, '_CLICK') !== false) {
        $pricingClicks++;
        $dailyStats[$date]['clicks']++;
        
        if (isset($deviceStats[$device])) {
            $deviceStats[$device]['clicks']++;
        }
        $hourlyStats[$hour]['clicks']++;
        
        // Track sessions with clicks
        if (!in_array($sessionId, $sessionsWithClicks)) {
            $sessionsWithClicks[] = $sessionId;
        }
        
        // Track which package this session clicked
        if ($eventType === 'ORDER_STARTER_CLICK') {
            $starterClicks++;
            if (!isset($sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId] = [];
            }
            if (!in_array('starter', $sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId][] = 'starter';
            }
        }
        if ($eventType === 'ORDER_GROWTH_CLICK') {
            $growthClicks++;
            if (!isset($sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId] = [];
            }
            if (!in_array('growth', $sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId][] = 'growth';
            }
        }
        if ($eventType === 'ORDER_PERFORMANCE_CLICK') {
            $performanceClicks++;
            if (!isset($sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId] = [];
            }
            if (!in_array('performance', $sessionPackageClicks[$sessionId])) {
                $sessionPackageClicks[$sessionId][] = 'performance';
            }
        }
    }
    
    // Form submissions (COMPLETE ORDER click)
    if ($eventType === 'ORDER_SUBMIT') {
        $formSubmits++;
        $sessionsWithSubmits[$sessionId] = true;
    }

    // External link clicks (header/footer)
    if ($eventType === 'EXTERNAL_LINK_CLICK') {
        $location = isset($event['data']['location']) ? $event['data']['location'] : 'unknown';
        if (!isset($externalClicks[$location])) {
            $externalClicks[$location] = 0;
        }
        $externalClicks[$location]++;
    }
    
    // Time on page
    if ($eventType === 'TIME_ON_PAGE' && isset($event['data']['duration'])) {
        $timeOnPageData[] = (int)$event['data']['duration'];
    }
    
    // Scroll depth
    if (strpos($eventType, 'SCROLL_DEPTH_') === 0) {
        $depth = str_replace('SCROLL_DEPTH_', '', $eventType);
        if (isset($scrollDepth[$depth])) {
            $scrollDepth[$depth]++;
        }
    }
    
    // Return visitors
    if ($eventType === 'RETURN_VISITOR') {
        $returnVisitors++;
    }
    
    // Exit pages
    if ($eventType === 'EXIT_PAGE' && isset($event['data']['exit_url'])) {
        $exitUrl = $event['data']['exit_url'];
        if (!isset($exitPages[$exitUrl])) {
            $exitPages[$exitUrl] = 0;
        }
        $exitPages[$exitUrl]++;
    }
}

// Calculate new visitors
$newVisitors = max(0, $pageViews - $returnVisitors);

// Calculate unique metrics
$uniquePageViews = count($uniqueSessions);
$uniqueUsers = count($uniqueIPs); // Unique users based on IP addresses

// Calculate unique sessions per package
$uniqueSessionsStarter = 0;
$uniqueSessionsGrowth = 0;
$uniqueSessionsPerformance = 0;

foreach ($sessionPackageClicks as $sessionId => $packages) {
    if (in_array('starter', $packages)) $uniqueSessionsStarter++;
    if (in_array('growth', $packages)) $uniqueSessionsGrowth++;
    if (in_array('performance', $packages)) $uniqueSessionsPerformance++;
}

// Calculate bounce rate (sessions without pricing clicks)
$bouncedSessions = count($uniqueSessions) - count($sessionsWithClicks);
$bounceRate = count($uniqueSessions) > 0 ? ($bouncedSessions / count($uniqueSessions)) * 100 : 0;

// Calculate averages
$avgTimeOnPage = !empty($timeOnPageData) ? array_sum($timeOnPageData) / count($timeOnPageData) : 0;
$conversionRate = $pageViews > 0 ? ($formSubmits / $pageViews) * 100 : 0;
$clickRate = $pageViews > 0 ? ($pricingClicks / $pageViews) * 100 : 0;

// ===== SESSION-DEDUPED FUNNEL =====
// One person reloading the page 8 times is 1 visitor, not 8. These are the numbers
// the funnel displays; the raw event totals above are kept as the small captions.
$uniqueVisitors   = count($sessionsWithViews);
$uniqueClickers   = count($sessionsWithClicks);
$uniqueSubmitters = count($sessionsWithSubmits);

// Step-to-step rates (each stage against the one before it)
$uClickRate  = $uniqueVisitors > 0 ? ($uniqueClickers / $uniqueVisitors) * 100 : 0;
$uSubmitRate = $uniqueClickers > 0 ? ($uniqueSubmitters / $uniqueClickers) * 100 : 0;
// Overall visitor -> submit rate
$uConversionRate = $uniqueVisitors > 0 ? ($uniqueSubmitters / $uniqueVisitors) * 100 : 0;

// Calculate week-over-week comparison
$thisWeekStart = date('Y-m-d', strtotime('-6 days'));
$thisWeekEnd = date('Y-m-d');
$lastWeekStart = date('Y-m-d', strtotime('-13 days'));
$lastWeekEnd = date('Y-m-d', strtotime('-7 days'));

$thisWeekViews = 0;
$thisWeekClicks = 0;
$lastWeekViews = 0;
$lastWeekClicks = 0;

foreach ($dailyStats as $date => $stats) {
    if ($date >= $thisWeekStart && $date <= $thisWeekEnd) {
        $thisWeekViews += $stats['views'];
        $thisWeekClicks += $stats['clicks'];
    }
    if ($date >= $lastWeekStart && $date <= $lastWeekEnd) {
        $lastWeekViews += $stats['views'];
        $lastWeekClicks += $stats['clicks'];
    }
}

$viewsChange = $lastWeekViews > 0 ? (($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100 : 0;
$clicksChange = $lastWeekClicks > 0 ? (($thisWeekClicks - $lastWeekClicks) / $lastWeekClicks) * 100 : 0;

// Sort exit pages
arsort($exitPages);

// Campaign Performance Analysis
$campaignStats = [];
$sourceStats = [];
$stateStats = [];
$countryStats = [];

foreach ($analyticsData as $event) {
    $eventType = $event['event_type'];
    $campaign = $event['campaign'];
    $source = $event['source'];
    $country = $event['country'];
    
    // Initialize arrays
    if (!isset($campaignStats[$campaign])) {
        $campaignStats[$campaign] = ['views' => 0, 'clicks' => 0];
    }
    if (!isset($sourceStats[$source])) {
        $sourceStats[$source] = ['views' => 0, 'clicks' => 0];
    }
    if (!isset($countryStats[$country])) {
        $countryStats[$country] = ['views' => 0, 'clicks' => 0];
    }
    
    // Count events
    if ($eventType === 'PAGE_VIEW') {
        $campaignStats[$campaign]['views']++;
        $sourceStats[$source]['views']++;
        $countryStats[$country]['views']++;
    }
    
    if (strpos($eventType, 'ORDER_') === 0 && strpos($eventType, '_CLICK') !== false) {
        $campaignStats[$campaign]['clicks']++;
        $sourceStats[$source]['clicks']++;
        $countryStats[$country]['clicks']++;
    }
}

// Calculate conversion rates and sort
foreach ($campaignStats as $key => $stats) {
    $campaignStats[$key]['conversion'] = $stats['views'] > 0 ? ($stats['clicks'] / $stats['views']) * 100 : 0;
}
foreach ($sourceStats as $key => $stats) {
    $sourceStats[$key]['conversion'] = $stats['views'] > 0 ? ($stats['clicks'] / $stats['views']) * 100 : 0;
}
foreach ($countryStats as $key => $stats) {
    $countryStats[$key]['conversion'] = $stats['views'] > 0 ? ($stats['clicks'] / $stats['views']) * 100 : 0;
}

// Sort by clicks
uasort($campaignStats, function($a, $b) { return $b['clicks'] - $a['clicks']; });
uasort($sourceStats, function($a, $b) { return $b['clicks'] - $a['clicks']; });
uasort($countryStats, function($a, $b) { return $b['clicks'] - $a['clicks']; });

// Get top performers
$topPackage = 'N/A';
$topPackageClicks = 0;
if ($starterClicks >= $growthClicks && $starterClicks >= $performanceClicks) {
    $topPackage = 'Starter';
    $topPackageClicks = $starterClicks;
} elseif ($growthClicks >= $performanceClicks) {
    $topPackage = 'Growth';
    $topPackageClicks = $growthClicks;
} else {
    $topPackage = 'Performance';
    $topPackageClicks = $performanceClicks;
}

$topCampaign = !empty($campaignStats) ? array_key_first($campaignStats) : 'N/A';
$topCampaignClicks = !empty($campaignStats) ? $campaignStats[$topCampaign]['clicks'] : 0;

$topSource = !empty($sourceStats) ? array_key_first($sourceStats) : 'N/A';
$topSourceClicks = !empty($sourceStats) ? $sourceStats[$topSource]['clicks'] : 0;

$topCountry = !empty($countryStats) ? array_key_first($countryStats) : 'N/A';
$topCountryClicks = !empty($countryStats) ? $countryStats[$topCountry]['clicks'] : 0;

// === PARSE CUSTOMER DATA ===
$file = __DIR__ . '/customer_data.log';
$customers = [];
$allCampaigns = ['all' => 'All Campaigns'];
$allPages = ['all' => 'All Pages'];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines);
    
    foreach ($lines as $line) {
        $parts = explode("\t", $line);
        $numParts = count($parts);
        
        if ($numParts >= 14) {
            $followedUp = false;
            $isClickOnly = false;
            $pageUrl = $parts[6];
            
            // Detect format based on column count
            // NEW FORMAT: 16-17 columns (with utm_content and placement)
            // OLD FORMAT: 14-15 columns (without utm_content and placement)
            
            $utmTerm = '-';
            if ($numParts >= 16) {
                // NEW FORMAT: Timestamp|Business|Location|Email|WhatsApp|Package|PageURL|ReviewsQty|UTM_Source|UTM_Medium|UTM_Campaign|UTM_Content|Placement|State|Zip|Country|Status|UTM_Term
                $utmSource = $parts[8];
                $utmMedium = $parts[9];
                $utmCampaign = $parts[10];
                $utmContent = $parts[11];
                $placement = $parts[12];
                $state = $parts[13];
                $zipCode = $parts[14];
                $country = $parts[15];

                if ($numParts > 16) {
                    $flag = trim($parts[16]);
                    $followedUp = ($flag === 'FOLLOWED_UP');
                    $isClickOnly = ($flag === 'CLICK_ONLY');
                }
                if ($numParts >= 18) {
                    $utmTerm = trim($parts[17]);
                }
            } else {
                // OLD FORMAT: Timestamp|Business|Location|Email|WhatsApp|Package|PageURL|ReviewsQty|Campaign|Medium|Source|State|Zip|Country|Status
                // Parse UTM from pageUrl for backward compatibility
                $utmData = parseUtmFromUrl($pageUrl);
                $utmSource = $utmData['utm_source'];
                $utmMedium = $utmData['utm_medium'];
                $utmCampaign = $utmData['utm_campaign'];
                $utmContent = $utmData['utm_content'];
                $utmTerm = $utmData['utm_term'];
                $placement = $utmData['placement'];
                $state = $parts[11];
                $zipCode = $parts[12];
                $country = $parts[13];

                if ($numParts > 14) {
                    $flag = trim($parts[14]);
                    $followedUp = ($flag === 'FOLLOWED_UP');
                    $isClickOnly = ($flag === 'CLICK_ONLY');
                }
            }

            $customer = [
                'timestamp' => $parts[0],
                'businessName' => $parts[1],
                'location' => $parts[2],
                'businessEmail' => $parts[3],
                'whatsapp' => $parts[4],
                'package' => $parts[5],
                'pageUrl' => $pageUrl,
                'reviewsQty' => $parts[7],
                'state' => $state,
                'zipCode' => $zipCode,
                'country' => $country,
                'followedUp' => $followedUp,
                'isClickOnly' => $isClickOnly,
                'utmSource' => $utmSource,
                'utmMedium' => $utmMedium,
                'utmCampaign' => $utmCampaign,
                'utmContent' => $utmContent,
                'utmTerm' => $utmTerm,
                'placement' => $placement
            ];
            
            $customers[] = $customer;
            
            // Use utmCampaign for campaign filter
            if (!empty($utmCampaign) && $utmCampaign !== '-' && !in_array($utmCampaign, $allCampaigns)) {
                $allCampaigns[$utmCampaign] = $utmCampaign;
            }
            if (!in_array($customer['pageUrl'], $allPages)) {
                $allPages[$customer['pageUrl']] = $customer['pageUrl'];
            }
        }
    }
}

// Filter customers
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$filterCampaign = isset($_GET['campaign']) ? $_GET['campaign'] : 'all';
$filterPage = isset($_GET['page']) ? $_GET['page'] : 'all';

$filteredCustomers = [];
foreach ($customers as $customer) {
    // Apply global date filter
    $customerDate = substr($customer['timestamp'], 0, 10);
    $matchesDate = ($customerDate >= $startDate && $customerDate <= $endDate);
    
    $matchesSearch = empty($searchQuery) || 
        stripos($customer['businessName'], $searchQuery) !== false ||
        stripos($customer['location'], $searchQuery) !== false ||
        stripos($customer['businessEmail'], $searchQuery) !== false ||
        stripos($customer['whatsapp'], $searchQuery) !== false;
    
    $matchesStatus = ($filterStatus === 'all') ||
        ($filterStatus === 'pending' && !$customer['followedUp']) ||
        ($filterStatus === 'followed' && $customer['followedUp']);
    
    $matchesCampaign = ($filterCampaign === 'all') || ($customer['utmCampaign'] === $filterCampaign);
    $matchesPage = ($filterPage === 'all') || ($customer['pageUrl'] === $filterPage);
    
    if ($matchesDate && $matchesSearch && $matchesStatus && $matchesCampaign && $matchesPage) {
        $filteredCustomers[] = $customer;
    }
}

// Calculate customer stats (filtered by date)
$totalOrders = count($filteredCustomers);
$totalReviews = array_sum(array_map(function($c) {
    return is_numeric($c['reviewsQty']) ? (int)$c['reviewsQty'] : 0;
}, $filteredCustomers));
$followedUpCount = count(array_filter($filteredCustomers, function($c) { return $c['followedUp']; }));
$pendingCount = $totalOrders - $followedUpCount;

// === CAMPAIGN BREAKDOWN DATA ===
// Parse all customer data for campaign breakdown with date filter
$cbData = [];
$cbUtmSources = [];
$cbUtmMediums = [];
$cbUtmCampaigns = [];
$cbUtmContents = [];
$cbUtmTerms = [];
$cbPlacements = [];

// NEW: Hierarchical data - Campaign (UTM Campaign) → Ad (UTM Content)
$cbHierarchy = [];

foreach ($customers as $customer) {
    $customerDate = substr($customer['timestamp'], 0, 10);

    // Apply date filter for campaign breakdown
    if ($customerDate >= $cbStartDate && $customerDate <= $cbEndDate) {
        $med = trim($customer['utmMedium']);
        $camp = trim($customer['utmCampaign']);
        $cont = trim($customer['utmContent']);
        $term = isset($customer['utmTerm']) ? trim($customer['utmTerm']) : '-';
        $place = trim($customer['placement']);
        $src = trim($customer['utmSource']);
        $isFormSubmit = !$customer['isClickOnly'];
        
        // UTM Source - skip empty or invalid
        if (!empty($src) && $src !== '-' && $src !== '') {
            if (!isset($cbUtmSources[$src])) {
                $cbUtmSources[$src] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbUtmSources[$src]['clicks']++;
            if ($isFormSubmit) {
                $cbUtmSources[$src]['formSubmits']++;
            }
        }
        
        // Placement - skip empty or invalid
        if (!empty($place) && $place !== '-' && $place !== '') {
            if (!isset($cbPlacements[$place])) {
                $cbPlacements[$place] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbPlacements[$place]['clicks']++;
            if ($isFormSubmit) {
                $cbPlacements[$place]['formSubmits']++;
            }
        }
        
        // Build Hierarchical Data: Medium → Campaign → Content
        if (!empty($med) && $med !== '-' && $med !== '') {
            // Initialize Medium (Campaign level)
            if (!isset($cbHierarchy[$med])) {
                $cbHierarchy[$med] = [
                    'clicks' => 0,
                    'formSubmits' => 0,
                    'adsets' => [],  // UTM Campaign
                    'ads' => []      // UTM Content
                ];
            }
            $cbHierarchy[$med]['clicks']++;
            if ($isFormSubmit) {
                $cbHierarchy[$med]['formSubmits']++;
            }
            
            // Track Adsets (UTM Campaign) under this Medium
            if (!empty($camp) && $camp !== '-' && $camp !== '') {
                if (!isset($cbHierarchy[$med]['adsets'][$camp])) {
                    $cbHierarchy[$med]['adsets'][$camp] = ['clicks' => 0, 'formSubmits' => 0];
                }
                $cbHierarchy[$med]['adsets'][$camp]['clicks']++;
                if ($isFormSubmit) {
                    $cbHierarchy[$med]['adsets'][$camp]['formSubmits']++;
                }
            }
            
            // Track Ads (UTM Content) under this Medium
            if (!empty($cont) && $cont !== '-' && $cont !== '') {
                if (!isset($cbHierarchy[$med]['ads'][$cont])) {
                    $cbHierarchy[$med]['ads'][$cont] = ['clicks' => 0, 'formSubmits' => 0];
                }
                $cbHierarchy[$med]['ads'][$cont]['clicks']++;
                if ($isFormSubmit) {
                    $cbHierarchy[$med]['ads'][$cont]['formSubmits']++;
                }
            }
        }
        
        // Keep flat structures for backward compatibility
        if (!empty($med) && $med !== '-' && $med !== '') {
            if (!isset($cbUtmMediums[$med])) {
                $cbUtmMediums[$med] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbUtmMediums[$med]['clicks']++;
            if ($isFormSubmit) {
                $cbUtmMediums[$med]['formSubmits']++;
            }
        }
        
        if (!empty($camp) && $camp !== '-' && $camp !== '') {
            if (!isset($cbUtmCampaigns[$camp])) {
                $cbUtmCampaigns[$camp] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbUtmCampaigns[$camp]['clicks']++;
            if ($isFormSubmit) {
                $cbUtmCampaigns[$camp]['formSubmits']++;
            }
        }
        
        if (!empty($cont) && $cont !== '-' && $cont !== '') {
            if (!isset($cbUtmContents[$cont])) {
                $cbUtmContents[$cont] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbUtmContents[$cont]['clicks']++;
            if ($isFormSubmit) {
                $cbUtmContents[$cont]['formSubmits']++;
            }
        }

        if (!empty($term) && $term !== '-' && $term !== '') {
            if (!isset($cbUtmTerms[$term])) {
                $cbUtmTerms[$term] = ['clicks' => 0, 'formSubmits' => 0];
            }
            $cbUtmTerms[$term]['clicks']++;
            if ($isFormSubmit) {
                $cbUtmTerms[$term]['formSubmits']++;
            }
        }
    }
}

// Sort hierarchical data by clicks
uasort($cbHierarchy, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });

// Sort adsets and ads within each campaign
foreach ($cbHierarchy as $med => &$data) {
    uasort($data['adsets'], function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
    uasort($data['ads'], function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
}
unset($data);

// Sort flat arrays
uasort($cbUtmSources, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
uasort($cbUtmMediums, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
uasort($cbUtmCampaigns, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
uasort($cbUtmContents, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
uasort($cbUtmTerms, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });
uasort($cbPlacements, function($a, $b) { return intval($b['clicks']) - intval($a['clicks']); });

// Get active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'analytics';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Buzzer - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
            background: #F7F8FA;
            color: #172B4D;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(9,30,66,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            color: #172B4D;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #0066FF;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0052CC;
        }
        
        .btn-danger {
            background: #DE350B;
            color: white;
        }
        
        .btn-danger:hover {
            background: #BF2600;
        }
        
        .btn-success {
            background: #00875A;
            color: white;
        }
        
        .btn-success:hover {
            background: #006644;
        }
        
        .btn-secondary {
            background: #DFE1E6;
            color: #172B4D;
        }
        
        .btn-secondary:hover {
            background: #C1C7D0;
        }
        
        .tabs {
            background: white;
            padding: 0 30px;
            box-shadow: 0 2px 4px rgba(9,30,66,0.08);
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .tab-btn {
            padding: 15px 0;
            border: none;
            background: none;
            font-size: 15px;
            font-weight: 600;
            color: #5E6C84;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .tab-btn.active {
            color: #0066FF;
            border-bottom-color: #0066FF;
        }
        
        .container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 30px;
        }
        
        .global-date-picker {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #DFE1E6;
            box-shadow: 0 2px 4px rgba(9,30,66,0.08);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(9,30,66,0.12);
            border-left: 4px solid;
        }
        
        .stat-card.blue { border-left-color: #0066FF; }
        .stat-card.green { border-left-color: #00875A; }
        .stat-card.orange { border-left-color: #FF991F; }
        .stat-card.purple { border-left-color: #6554C0; }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #5E6C84;
        }
        
        .stat-change {
            font-size: 13px;
            margin-top: 8px;
            font-weight: 600;
        }
        
        .stat-change.up { color: #00875A; }
        .stat-change.down { color: #DE350B; }
        
        .card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(9,30,66,0.12);
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .date-picker-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .date-picker-container input {
            padding: 8px 12px;
            border: 2px solid #DFE1E6;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .date-picker-container input:focus {
            outline: none;
            border-color: #0066FF;
        }
        
        .date-presets {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .preset-option {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }
        
        .preset-option:hover {
            background: #F7F8FA;
        }
        
        .preset-option input[type="radio"] {
            margin-right: 10px;
            cursor: pointer;
        }
        
        .preset-option input[type="radio"]:checked + span {
            font-weight: 600;
            color: #0066FF;
        }
        
        .preset-option span {
            font-size: 14px;
            color: #172B4D;
        }
        
        .date-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s;
        }
        
        .date-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .date-modal-content {
            background-color: white;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 8px 32px rgba(9, 30, 66, 0.25);
            animation: slideDown 0.3s;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .date-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #DFE1E6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .date-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #172B4D;
        }
        
        .date-modal-close {
            font-size: 28px;
            font-weight: 300;
            color: #5E6C84;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s;
        }
        
        .date-modal-close:hover {
            color: #172B4D;
        }
        
        .date-modal-body {
            padding: 24px;
        }
        
        .date-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #DFE1E6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .pricing-table th {
            text-align: left;
            padding: 12px;
            background: #F7F8FA;
            font-size: 14px;
            font-weight: 600;
            color: #5E6C84;
        }
        
        .pricing-table td {
            padding: 12px;
            border-bottom: 1px solid #F7F8FA;
        }
        
        .pricing-table tr:hover {
            background: #F7F8FA;
        }
        
        .pricing-table .highlight {
            background: #E6F2FF;
            font-weight: 600;
        }
        
        /* UNIQUE: Campaign Breakdown Source & Placement Tables */
        .cb-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .cb-breakdown-table th:first-child,
        .cb-breakdown-table td:first-child {
            width: 55%;
        }
        
        .cb-breakdown-table th:nth-child(2),
        .cb-breakdown-table td:nth-child(2),
        .cb-breakdown-table th:nth-child(3),
        .cb-breakdown-table td:nth-child(3),
        .cb-breakdown-table th:nth-child(4),
        .cb-breakdown-table td:nth-child(4) {
            width: 15%;
            text-align: right;
        }
        
        .cb-breakdown-table th {
            text-align: left;
            padding: 12px;
            background: #F7F8FA;
            font-size: 14px;
            font-weight: 600;
            color: #5E6C84;
        }
        
        .cb-breakdown-table td {
            padding: 12px;
            border-bottom: 1px solid #F7F8FA;
        }
        
        .cb-breakdown-table tr:hover {
            background: #F7F8FA;
        }
        
        .cb-breakdown-table .utm-cell {
            text-align: left;
        }
        
        .progress-bar {
            background: #F7F8FA;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin: 8px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: #0066FF;
            transition: width 0.3s;
        }
        
        .progress-fill.green { background: #00875A; }
        .progress-fill.orange { background: #FF991F; }
        .progress-fill.purple { background: #6554C0; }
        
        .funnel {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 20px 0;
        }
        
        .funnel-step {
            flex: 1;
            text-align: center;
            padding: 20px;
            background: #F7F8FA;
            border-radius: 8px;
        }
        
        .funnel-value {
            font-size: 28px;
            font-weight: 700;
            color: #0066FF;
        }
        
        .funnel-label {
            font-size: 14px;
            color: #5E6C84;
            margin-top: 8px;
        }
        
        .funnel-arrow {
            font-size: 24px;
            color: #5E6C84;
        }
        
        .hourly-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-top: 15px;
        }
        
        .hourly-cell {
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            background: #F7F8FA;
            font-size: 12px;
        }
        
        .hourly-cell.high {
            background: #00875A;
            color: white;
        }
        
        .hourly-cell.medium {
            background: #FF991F;
            color: white;
        }
        
        .hourly-cell.low {
            background: #DFE1E6;
            color: #5E6C84;
        }
        
        .device-grid {
            display: grid;
            gap: 15px;
            margin-top: 15px;
        }
        
        .device-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .device-info {
            flex: 1;
        }
        
        .device-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .device-stats {
            font-size: 13px;
            color: #5E6C84;
        }
        
        .device-conversion {
            font-size: 18px;
            font-weight: 700;
            color: #0066FF;
        }
        
        .exit-list {
            list-style: none;
            margin-top: 15px;
        }
        
        .exit-item {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid #F7F8FA;
        }
        
        .exit-item:last-child {
            border-bottom: none;
        }
        
        .exit-url {
            color: #172B4D;
        }
        
        .exit-count {
            font-weight: 600;
            color: #5E6C84;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .empty-state h4 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        
        .empty-state p {
            color: #5E6C84;
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filters input,
        .filters select {
            padding: 8px 12px;
            border: 2px solid #DFE1E6;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .filters input:focus,
        .filters select:focus {
            outline: none;
            border-color: #0066FF;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1400px;
        }
        
        thead {
            background: #F7F8FA;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #5E6C84;
            white-space: nowrap;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #F7F8FA;
            font-size: 13px;
        }
        
        tr:hover {
            background: #F7F8FA;
        }
        
        .followup-btn {
            padding: 8px 16px;
            border: 2px solid #25D366;
            background: white;
            color: #25D366;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .followup-btn:hover {
            background: #25D366;
            color: white;
        }
        
        .followup-btn.followed {
            background: #00875A;
            border-color: #00875A;
            color: white;
        }

        .copyemail-btn {
            padding: 8px 16px;
            border: 2px solid #0066FF;
            background: white;
            color: #0066FF;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            margin-left: 6px;
        }

        .copyemail-btn:hover {
            background: #0066FF;
            color: white;
        }
        
        .delete-btn-container {
            display: none;
            margin-bottom: 15px;
        }
        
        .delete-btn-container.show {
            display: block;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #E3FCEF;
            color: #006644;
            border-left: 4px solid #00875A;
        }
        
        .click-only-row {
            background: #FFF4E6 !important;
        }
        
        .click-only-badge {
            background: #FF991F;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 5px;
            font-weight: 600;
        }
        
        .utm-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .utm-cell:hover {
            overflow: visible;
            white-space: normal;
            word-break: break-all;
        }
        
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        
        /* Side-by-Side Campaign Performance */
        .campaign-performance-wrapper {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 0;
            min-height: 400px;
            border: 1px solid #DFE1E6;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            margin-bottom: 24px;
        }
        
        .campaign-list {
            background: #F7F8FA;
            border-right: 1px solid #DFE1E6;
            overflow-y: auto;
            max-height: 500px;
        }
        
        .campaign-list-header {
            padding: 16px 20px;
            border-bottom: 1px solid #DFE1E6;
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .campaign-list-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #172B4D;
        }
        
        .campaign-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            cursor: pointer;
            border-bottom: 1px solid #EBECF0;
            transition: all 0.15s;
        }
        
        .campaign-list-item:hover {
            background: #fff;
        }
        
        .campaign-list-item.active {
            background: #fff;
            border-left: 3px solid #0066FF;
            padding-left: 17px;
        }
        
        .campaign-list-item .name {
            font-size: 13px;
            color: #172B4D;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
            margin-right: 12px;
        }
        
        .campaign-list-item.active .name {
            font-weight: 600;
            color: #0066FF;
        }
        
        .campaign-list-item .clicks {
            font-size: 14px;
            font-weight: 700;
            color: #172B4D;
            min-width: 40px;
            text-align: right;
        }
        
        .campaign-detail {
            padding: 20px 24px;
            overflow-y: auto;
            max-height: 500px;
        }
        
        .campaign-detail-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #EBECF0;
        }
        
        .campaign-detail-header .selected-name {
            font-size: 18px;
            font-weight: 700;
            color: #172B4D;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .campaign-detail-header .selected-name .trophy {
            font-size: 22px;
        }
        
        .campaign-detail-stats {
            display: flex;
            gap: 24px;
        }
        
        .campaign-detail-stat {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .campaign-detail-stat .value {
            font-size: 20px;
            font-weight: 700;
            color: #0066FF;
        }
        
        .campaign-detail-stat .label {
            font-size: 12px;
            color: #5E6C84;
        }
        
        .campaign-detail-section {
            margin-bottom: 24px;
        }
        
        .campaign-detail-section h5 {
            font-size: 14px;
            font-weight: 600;
            color: #5E6C84;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .winning-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .winning-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            background: #F7F8FA;
            border-radius: 6px;
            gap: 12px;
        }
        
        .winning-item.rank-1 {
            background: linear-gradient(135deg, #FFF9E6 0%, #FFF4CC 100%);
            border: 1px solid #FFE066;
        }
        
        .winning-item.rank-2 {
            background: linear-gradient(135deg, #F7F8FA 0%, #EBECF0 100%);
            border: 1px solid #DFE1E6;
        }
        
        .winning-item.rank-3 {
            background: linear-gradient(135deg, #FDF4EC 0%, #FAEADB 100%);
            border: 1px solid #F0C9A0;
        }
        
        .winning-item .rank-num {
            font-size: 16px;
            font-weight: 700;
            min-width: 24px;
        }
        
        .winning-item.rank-1 .rank-num { color: #B8860B; }
        .winning-item.rank-2 .rank-num { color: #666; }
        .winning-item.rank-3 .rank-num { color: #A0522D; }
        
        .winning-item .item-name {
            flex: 1;
            font-size: 13px;
            color: #172B4D;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .winning-item .item-clicks {
            font-size: 14px;
            font-weight: 600;
            color: #172B4D;
        }
        
        .winning-item .item-conv {
            font-size: 12px;
            color: #5E6C84;
            min-width: 50px;
            text-align: right;
        }
        
        @media (max-width: 992px) {
            .campaign-performance-wrapper {
                grid-template-columns: 1fr;
            }
            
            .campaign-list {
                border-right: none;
                border-bottom: 1px solid #DFE1E6;
                max-height: 250px;
            }
            
            .campaign-detail {
                max-height: none;
            }
        }
        
        .two-col-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .two-col-grid .card {
            min-width: 0;
            overflow: hidden;
        }
        
        @media (max-width: 992px) {
            .two-col-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .three-col-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        @media (max-width: 1200px) {
            .three-col-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .three-col-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .funnel {
                flex-direction: column;
            }
            
            .funnel-arrow {
                transform: rotate(90deg);
            }
            
            .hourly-grid {
                grid-template-columns: repeat(4, 1fr);
            }
            
            .card form > div {
                grid-template-columns: 1fr !important;
            }
            
            .card form > div > div:first-child {
                border-right: none !important;
                border-bottom: 1px solid #DFE1E6;
                padding-right: 0 !important;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            
            .date-modal-content {
                width: 95%;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .date-modal-body > div {
                grid-template-columns: 1fr !important;
            }
            
            .date-modal-body > div > div:first-child {
                border-right: none !important;
                border-bottom: 1px solid #DFE1E6;
                padding-right: 0 !important;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            
            #dateRangeDisplay {
                min-width: auto !important;
            }
            
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
            
            .breakdown-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                min-width: 1800px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <a href="log.php" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center;">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" style="height: 32px; vertical-align: middle; margin-right: 10px;">Dashboard
            </a>
        </h1>
        <div class="header-actions">
            <button onclick="location.reload()" class="btn btn-secondary">Refresh</button>
            <a href="?logout=1" class="btn btn-danger">Logout</a>
        </div>
    </div>
    
    <div class="tabs">
        <button class="tab-btn <?php echo $activeTab === 'analytics' ? 'active' : ''; ?>" onclick="switchTab('analytics')" data-tab="analytics">Analytics</button>
        <button class="tab-btn <?php echo $activeTab === 'customers' ? 'active' : ''; ?>" onclick="switchTab('customers')" data-tab="customers">Customers</button>
        <button class="tab-btn <?php echo $activeTab === 'campaigns' ? 'active' : ''; ?>" onclick="switchTab('campaigns')" data-tab="campaigns">Campaign Breakdown</button>
    </div>
    
    <!-- Global Date Range Picker -->
    <div class="global-date-picker">
        <form method="GET" id="globalDateFilterForm">
            <input type="hidden" name="tab" id="currentTab" value="<?php echo $activeTab; ?>">
            <input type="hidden" name="start_date" id="startDate" value="<?php echo $startDate; ?>">
            <input type="hidden" name="end_date" id="endDate" value="<?php echo $endDate; ?>">
            
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; justify-content: center;">
                <label style="font-weight: 600; color: #172B4D;">📅 Date Range:</label>
                <input type="text" id="dateRangeDisplay" readonly 
                       value="<?php echo $startDate; ?> to <?php echo $endDate; ?>" 
                       onclick="openDateModal()"
                       style="padding: 10px 16px; border: 2px solid #DFE1E6; border-radius: 6px; font-size: 14px; min-width: 280px; cursor: pointer; background: white;">
                <button type="submit" class="btn btn-primary">Apply</button>
                <button type="button" class="btn btn-secondary" onclick="resetDateFilter()">Reset</button>
            </div>
        </form>
    </div>
    
    <!-- Date Range Modal -->
    <div id="dateModal" class="date-modal">
        <div class="date-modal-content">
            <div class="date-modal-header">
                <h3>Select Date Range</h3>
                <span class="date-modal-close" onclick="closeDateModal()">&times;</span>
            </div>
            
            <div class="date-modal-body">
                <div style="display: grid; grid-template-columns: 200px 1fr; gap: 20px;">
                    <!-- Preset Options -->
                    <div style="border-right: 1px solid #DFE1E6; padding-right: 20px;">
                        <div style="margin-bottom: 10px; font-weight: 600; color: #172B4D; font-size: 13px;">QUICK SELECT</div>
                        <div class="date-presets">
                            <label class="preset-option">
                                <input type="radio" name="preset" value="today" onchange="applyPreset('today')">
                                <span>Today</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="yesterday" onchange="applyPreset('yesterday')">
                                <span>Yesterday</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="last7" onchange="applyPreset('last7')">
                                <span>Last 7 days</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="last14" onchange="applyPreset('last14')">
                                <span>Last 14 days</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="last28" onchange="applyPreset('last28')">
                                <span>Last 28 days</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="last30" onchange="applyPreset('last30')">
                                <span>Last 30 days</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="thisweek" onchange="applyPreset('thisweek')">
                                <span>This week</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="lastweek" onchange="applyPreset('lastweek')">
                                <span>Last week</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="thismonth" onchange="applyPreset('thismonth')">
                                <span>This month</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="lastmonth" onchange="applyPreset('lastmonth')">
                                <span>Last month</span>
                            </label>
                            <label class="preset-option">
                                <input type="radio" name="preset" value="custom" onchange="applyPreset('custom')">
                                <span>Custom Range</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Calendar -->
                    <div>
                        <div style="margin-bottom: 10px; font-weight: 600; color: #172B4D; font-size: 13px;">CUSTOM DATE RANGE</div>
                        <input type="text" id="dateRangePicker" style="width: 100%; padding: 12px; border: 2px solid #DFE1E6; border-radius: 6px; font-size: 14px;">
                        
                        <div style="margin-top: 20px; padding: 15px; background: #F7F8FA; border-radius: 6px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 12px; color: #5E6C84; margin-bottom: 4px;">FROM</div>
                                    <div id="modalStartDate" style="font-size: 16px; font-weight: 600;"><?php echo date('M d, Y', strtotime($startDate)); ?></div>
                                </div>
                                <div style="font-size: 20px; color: #5E6C84;">→</div>
                                <div>
                                    <div style="font-size: 12px; color: #5E6C84; margin-bottom: 4px;">TO</div>
                                    <div id="modalEndDate" style="font-size: 16px; font-weight: 600;"><?php echo date('M d, Y', strtotime($endDate)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="date-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDateModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyDateRange()">Apply Range</button>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- ANALYTICS TAB -->
        <div id="analytics-tab" class="tab-content <?php echo $activeTab === 'analytics' ? 'active' : ''; ?>">
            <?php if (empty($analyticsData)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📈</div>
                    <h4>No analytics data yet</h4>
                    <p>Analytics will appear here once users start visiting your landing page</p>
                </div>
            <?php else: ?>
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-value"><?php echo number_format($pageViews); ?></div>
                        <div class="stat-label">Page Views</div>
                        <?php if ($viewsChange != 0): ?>
                            <div class="stat-change <?php echo $viewsChange >= 0 ? 'up' : 'down'; ?>">
                                <?php echo ($viewsChange >= 0 ? '↑' : '↓') . ' ' . abs(round($viewsChange, 1)); ?>% vs last week
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-value"><?php echo number_format($formSubmits); ?></div>
                        <div class="stat-label">Form Submissions</div>
                        <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;"><?php echo number_format($pricingClicks); ?> pricing clicks</div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-value"><?php echo number_format($conversionRate, 1); ?>%</div>
                        <div class="stat-label">Conversion Rate</div>
                        <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;">Page Views → Form Submit</div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-value"><?php echo gmdate("i:s", $avgTimeOnPage); ?></div>
                        <div class="stat-label">Avg. Time on Page</div>
                    </div>
                </div>
                
                <!-- Daily Trend Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Trend</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
                
                <!-- Conversion Funnel -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Conversion Funnel</h3>
                    </div>
                    <div style="font-size: 12px; color: #5E6C84; margin: -6px 0 14px;">
                        Deduplicated by session &mdash; one visitor counts once no matter how many times they reload or click. Raw event totals in grey.
                    </div>
                    <div class="funnel">
                        <div class="funnel-step">
                            <div class="funnel-value"><?php echo number_format($uniqueVisitors); ?></div>
                            <div class="funnel-label">Unique Visitors</div>
                            <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;">100% &middot; <?php echo number_format($pageViews); ?> page views</div>
                        </div>
                        <div class="funnel-arrow">→</div>
                        <div class="funnel-step">
                            <div class="funnel-value"><?php echo number_format($uniqueClickers); ?></div>
                            <div class="funnel-label">Unique Pricing Clicks</div>
                            <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;"><?php echo number_format($uClickRate, 1); ?>% of visitors &middot; <?php echo number_format($pricingClicks); ?> clicks</div>
                        </div>
                        <div class="funnel-arrow">→</div>
                        <div class="funnel-step">
                            <div class="funnel-value"><?php echo number_format($uniqueSubmitters); ?></div>
                            <div class="funnel-label">Unique Submits</div>
                            <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;"><?php echo number_format($uSubmitRate, 1); ?>% of clickers &middot; <?php echo number_format($formSubmits); ?> submits</div>
                        </div>
                        <div class="funnel-arrow">→</div>
                        <div class="funnel-step">
                            <div class="funnel-value" style="color: #00875A;"><?php echo number_format($uConversionRate, 1); ?>%</div>
                            <div class="funnel-label">Conversion Rate</div>
                            <div style="font-size: 12px; color: #5E6C84; margin-top: 4px;">Unique visitors → submits</div>
                        </div>
                    </div>
                </div>
                
                <!-- Row 1: Pricing Performance (full width) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pricing Performance</h3>
                    </div>
                    <table class="pricing-table">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Total Clicks</th>
                                <th>Unique Sessions</th>
                                <th>% of Engaged</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalUniqueClickers = count($sessionsWithClicks);
                            $packages = [
                                'Starter' => ['clicks' => $starterClicks, 'sessions' => $uniqueSessionsStarter],
                                'Growth' => ['clicks' => $growthClicks, 'sessions' => $uniqueSessionsGrowth],
                                'Performance' => ['clicks' => $performanceClicks, 'sessions' => $uniqueSessionsPerformance]
                            ];
                            foreach ($packages as $name => $data):
                                $pctEngaged = $totalUniqueClickers > 0 ? ($data['sessions'] / $totalUniqueClickers) * 100 : 0;
                                $isHighest = ($data['clicks'] === max($starterClicks, $growthClicks, $performanceClicks));
                            ?>
                                <tr class="<?php echo $isHighest ? 'highlight' : ''; ?>">
                                    <td><strong><?php echo $name; ?></strong></td>
                                    <td><?php echo number_format($data['clicks']); ?></td>
                                    <td><?php echo number_format($data['sessions']); ?></td>
                                    <td><?php echo number_format($pctEngaged, 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Row 2: Three columns - Scroll Depth, Device, Visitor -->
                <div class="three-col-grid">
                    <!-- Scroll Depth -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Scroll Depth</h3>
                        </div>
                        <div style="padding: 5px 0;">
                            <?php
                            // Use pageViews as base for percentage calculation
                            $scrollTotal = max(1, $pageViews);
                            $depths = ['25', '50', '75', '100']; // Order from most to least (funnel)
                            foreach ($depths as $depth):
                                $count = $scrollDepth[$depth];
                                $percentage = $scrollTotal > 0 ? ($count / $scrollTotal) * 100 : 0;
                            ?>
                                <div style="margin-bottom: 16px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <span style="font-size: 14px; color: #5E6C84;"><?php echo $depth; ?>% Scroll</span>
                                        <span style="font-size: 14px;"><strong><?php echo number_format($count); ?></strong> <span style="color: #5E6C84;">(<?php echo number_format($percentage, 1); ?>%)</span></span>
                                    </div>
                                    <div class="progress-bar" style="height: 8px;">
                                        <div class="progress-fill green" style="width: <?php echo min(100, $percentage); ?>%; height: 100%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Device Breakdown -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Device Conversion</h3>
                        </div>
                        <div class="device-grid">
                            <?php foreach ($deviceStats as $device => $stats): 
                                $deviceConv = $stats['views'] > 0 ? ($stats['clicks'] / $stats['views']) * 100 : 0;
                            ?>
                                <div class="device-item">
                                    <div class="device-info">
                                        <div class="device-name"><?php echo $device; ?></div>
                                        <div class="device-stats"><?php echo $stats['views']; ?> views → <?php echo $stats['clicks']; ?> clicks</div>
                                        <div class="progress-bar">
                                            <div class="progress-fill purple" style="width: <?php echo min(100, $deviceConv); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="device-conversion"><?php echo number_format($deviceConv, 1); ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Visitor Type -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Visitor Type</h3>
                        </div>
                        <div class="device-grid">
                            <div class="device-item">
                                <div class="device-info">
                                    <div class="device-name">New Visitors</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $pageViews > 0 ? ($newVisitors / $pageViews) * 100 : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="device-conversion"><?php echo number_format($newVisitors); ?></div>
                            </div>
                            <div class="device-item">
                                <div class="device-info">
                                    <div class="device-name">Return Visitors</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill orange" style="width: <?php echo $pageViews > 0 ? ($returnVisitors / $pageViews) * 100 : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="device-conversion"><?php echo number_format($returnVisitors); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hourly Heatmap -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Best Time to Convert</h3>
                    </div>
                    <div class="hourly-grid">
                        <?php 
                        for ($h = 0; $h < 24; $h++):
                            $hourViews = $hourlyStats[$h]['views'];
                            $hourClicks = $hourlyStats[$h]['clicks'];
                            $hourConv = $hourViews > 0 ? ($hourClicks / $hourViews) * 100 : 0;
                            
                            $class = 'low';
                            if ($hourConv >= 15) $class = 'high';
                            elseif ($hourConv >= 8) $class = 'medium';
                        ?>
                            <div class="hourly-cell <?php echo $class; ?>">
                                <div><?php echo $h; ?>:00</div>
                                <div><?php echo number_format($hourConv, 1); ?>%</div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Campaign Performance -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📊 Campaign Performance</h3>
                        <span style="color: #5E6C84; font-size: 13px;">Top 5 campaigns by clicks</span>
                    </div>
                    <?php if (empty($campaignStats)): ?>
                        <p style="color: #5E6C84; text-align: center;">No campaign data available</p>
                    <?php else: ?>
                        <table class="pricing-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Views</th>
                                    <th>Clicks</th>
                                    <th>Conv Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($campaignStats, 0, 5, true) as $campaign => $stats): 
                                    $isTop = $campaign === $topCampaign;
                                ?>
                                    <tr class="<?php echo $isTop ? 'highlight' : ''; ?>">
                                        <td><strong><?php echo htmlspecialchars($campaign); ?></strong></td>
                                        <td><?php echo number_format($stats['views']); ?></td>
                                        <td><?php echo number_format($stats['clicks']); ?></td>
                                        <td><?php echo number_format($stats['conversion'], 1); ?>%</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill green" style="width: <?php echo min(100, $stats['conversion'] * 5); ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Geographic Performance -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">🌍 Geographic Performance</h3>
                        <span style="color: #5E6C84; font-size: 13px;">Top 5 countries by clicks</span>
                    </div>
                    <?php if (empty($countryStats)): ?>
                        <p style="color: #5E6C84; text-align: center;">No geographic data available</p>
                    <?php else: ?>
                        <table class="pricing-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Views</th>
                                    <th>Clicks</th>
                                    <th>Conv Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($countryStats, 0, 5, true) as $country => $stats): 
                                    $isTop = $country === $topCountry;
                                ?>
                                    <tr class="<?php echo $isTop ? 'highlight' : ''; ?>">
                                        <td><strong><?php echo htmlspecialchars($country); ?></strong></td>
                                        <td><?php echo number_format($stats['views']); ?></td>
                                        <td><?php echo number_format($stats['clicks']); ?></td>
                                        <td><?php echo number_format($stats['conversion'], 1); ?>%</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill purple" style="width: <?php echo min(100, $stats['conversion'] * 5); ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- CUSTOMERS TAB -->
        <div id="customers-tab" class="tab-content <?php echo $activeTab === 'customers' ? 'active' : ''; ?>">
            <?php if (isset($_SESSION['delete_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['delete_message'];
                    unset($_SESSION['delete_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-value"><?php echo number_format($totalOrders); ?></div>
                    <div class="stat-label">Total Entries</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-value"><?php echo number_format($totalReviews); ?></div>
                    <div class="stat-label">Total Reviews Ordered</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-value"><?php echo number_format($followedUpCount); ?></div>
                    <div class="stat-label">Already Followed Up</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-value"><?php echo number_format($pendingCount); ?></div>
                    <div class="stat-label">Pending Follow-up</div>
                </div>
            </div>
            
            <!-- Filters -->
            <form method="GET" class="filters" id="customerFilters">
                <input type="hidden" name="tab" value="customers">
                <input type="hidden" name="start_date" value="<?php echo $startDate; ?>">
                <input type="hidden" name="end_date" value="<?php echo $endDate; ?>">
                <input type="text" name="search" id="searchInput" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                <select name="status" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="followed" <?php echo $filterStatus === 'followed' ? 'selected' : ''; ?>>Followed Up</option>
                </select>
                <select name="campaign" id="campaignFilter">
                    <option value="all">All Campaigns</option>
                    <?php foreach ($allCampaigns as $camp): ?>
                        <option value="<?php echo htmlspecialchars($camp); ?>" <?php echo $filterCampaign === $camp ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($camp); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?tab=customers&export=1&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo $filterStatus !== 'all' ? '&status=' . $filterStatus : ''; ?><?php echo $filterCampaign !== 'all' ? '&campaign=' . urlencode($filterCampaign) : ''; ?>" class="btn btn-success">Export CSV</a>
            </form>
            
            <div id="searchResults"></div>

            <form method="POST" id="deleteForm">
                <div class="delete-btn-container" id="deleteBtnContainer">
                    <button type="submit" name="delete_entries" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete selected entries?')">Delete Selected</button>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Timestamp</th>
                                <th>Business Name</th>
                                <th>Location</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Package</th>
                                <th>Reviews</th>
                                <th>UTM Source</th>
                                <th>UTM Medium</th>
                                <th>UTM Campaign</th>
                                <th>UTM Content (Ad)</th>
                                <th>UTM Term (Adset)</th>
                                <th>Placement</th>
                                <th>State</th>
                                <th>Zip</th>
                                <th>Country</th>
                                <th>Follow Up</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredCustomers)): ?>
                                <tr>
                                    <td colspan="18" style="text-align: center; padding: 40px;">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">📋</div>
                                            <h4>No customer data found</h4>
                                            <p>Customer orders will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filteredCustomers as $index => $customer): 
                                    $isClickOnly = isset($customer['isClickOnly']) && $customer['isClickOnly'];
                                    $rowStyle = $isClickOnly ? 'background: #FFF4E6;' : '';
                                    $clickBadge = $isClickOnly ? '<span class="click-only-badge">CLICK</span>' : '';
                                ?>
                                    <tr style="<?php echo $rowStyle; ?>">
                                        <td><input type="checkbox" name="selected[]" value="<?php echo $index; ?>" class="row-checkbox"></td>
                                        <td><?php echo htmlspecialchars($customer['timestamp']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['businessName']); ?><?php echo $clickBadge; ?></td>
                                        <td><?php echo htmlspecialchars($customer['location']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['businessEmail']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['whatsapp']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['package']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['reviewsQty']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['utmSource']); ?>"><?php echo htmlspecialchars($customer['utmSource']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['utmMedium']); ?>"><?php echo htmlspecialchars($customer['utmMedium']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['utmCampaign']); ?>"><?php echo htmlspecialchars($customer['utmCampaign']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['utmContent']); ?>"><?php echo htmlspecialchars($customer['utmContent']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['utmTerm']); ?>"><?php echo htmlspecialchars($customer['utmTerm']); ?></td>
                                        <td class="utm-cell" title="<?php echo htmlspecialchars($customer['placement']); ?>"><?php echo htmlspecialchars($customer['placement']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['state']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['zipCode']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['country']); ?></td>
                                        <td>
                                            <a href="#" class="followup-btn"
                                                onclick="openFollowupWA(this); return false;"
                                                data-biz="<?php echo htmlspecialchars($customer['businessName']); ?>"
                                                data-email="<?php echo htmlspecialchars($customer['businessEmail']); ?>"
                                                data-pkg="<?php echo htmlspecialchars($customer['package']); ?>"
                                                data-reviews="<?php echo htmlspecialchars($customer['reviewsQty']); ?>"
                                                data-wa="<?php echo htmlspecialchars($customer['whatsapp']); ?>">
                                                <span class="text">Follow Up</span>
                                            </a>
                                            <button type="button" class="copyemail-btn"
                                                onclick="copyFollowupEmail(this)"
                                                data-biz="<?php echo htmlspecialchars($customer['businessName']); ?>"
                                                data-email="<?php echo htmlspecialchars($customer['businessEmail']); ?>"
                                                data-pkg="<?php echo htmlspecialchars($customer['package']); ?>"
                                                data-reviews="<?php echo htmlspecialchars($customer['reviewsQty']); ?>">
                                                Copy Email
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        
        <!-- CAMPAIGN BREAKDOWN TAB -->
        <div id="campaigns-tab" class="tab-content <?php echo $activeTab === 'campaigns' ? 'active' : ''; ?>">
            <?php 
            $totalCbClicks = array_sum(array_column($cbHierarchy, 'clicks'));
            $totalCbSubmits = array_sum(array_column($cbHierarchy, 'formSubmits'));
            ?>
            
            <!-- Summary Stats -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-value"><?php echo number_format($totalCbClicks); ?></div>
                    <div class="stat-label">Total Pricing Clicks</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-value"><?php echo number_format($totalCbSubmits); ?></div>
                    <div class="stat-label">Form Submissions</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-value"><?php echo count($cbHierarchy); ?></div>
                    <div class="stat-label">Campaigns</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-value"><?php echo count($cbPlacements); ?></div>
                    <div class="stat-label">Placements</div>
                </div>
            </div>
            
            <?php if (empty($cbHierarchy)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <h4>No campaign data for selected date range</h4>
                    <p>Try selecting a different date range</p>
                </div>
            <?php else: ?>
            
            <?php 
            // Prepare hierarchical data as JSON for JavaScript
            $cbHierarchyJson = [];
            foreach ($cbHierarchy as $medName => $medData) {
                $cbHierarchyJson[$medName] = [
                    'clicks' => intval($medData['clicks']),
                    'submits' => intval($medData['formSubmits']),
                    'adsets' => [],
                    'ads' => []
                ];
                foreach ($medData['adsets'] as $asName => $asData) {
                    $cbHierarchyJson[$medName]['adsets'][] = [
                        'name' => $asName,
                        'clicks' => intval($asData['clicks']),
                        'submits' => intval($asData['formSubmits'])
                    ];
                }
                foreach ($medData['ads'] as $adName => $adData) {
                    $cbHierarchyJson[$medName]['ads'][] = [
                        'name' => $adName,
                        'clicks' => intval($adData['clicks']),
                        'submits' => intval($adData['formSubmits'])
                    ];
                }
            }
            ?>
            
            <!-- Campaign Performance - Side by Side -->
            <div class="campaign-performance-wrapper">
                <!-- Left: Campaign List -->
                <div class="campaign-list">
                    <div class="campaign-list-header">
                        <h4>Campaign (UTM Campaign)</h4>
                    </div>
                    <?php 
                    $isFirst = true;
                    foreach ($cbHierarchy as $mediumName => $mediumData): 
                        $clicks = intval($mediumData['clicks']);
                    ?>
                        <div class="campaign-list-item <?php echo $isFirst ? 'active' : ''; ?>" 
                             onclick="selectCampaign(this, '<?php echo htmlspecialchars(addslashes($mediumName)); ?>')"
                             data-campaign="<?php echo htmlspecialchars($mediumName); ?>">
                            <span class="name" title="<?php echo htmlspecialchars($mediumName); ?>"><?php echo htmlspecialchars($mediumName); ?></span>
                            <span class="clicks"><?php echo number_format($clicks); ?></span>
                        </div>
                    <?php 
                    $isFirst = false;
                    endforeach; 
                    ?>
                </div>
                
                <!-- Right: Winning Breakdown -->
                <div class="campaign-detail" id="campaignDetail">
                    <?php 
                    // Get first campaign data for initial display
                    $firstKey = array_key_first($cbHierarchy);
                    $firstData = $cbHierarchy[$firstKey];
                    $firstClicks = intval($firstData['clicks']);
                    $firstSubmits = intval($firstData['formSubmits']);
                    $firstConv = $firstClicks > 0 ? ($firstSubmits / $firstClicks) * 100 : 0;
                    ?>
                    
                    <div class="campaign-detail-header">
                        <div class="selected-name">
                            <span class="trophy">🏆</span>
                            <span id="selectedCampaignName"><?php echo htmlspecialchars($firstKey); ?></span>
                        </div>
                        <div class="campaign-detail-stats">
                            <div class="campaign-detail-stat">
                                <span class="value" id="selectedClicks"><?php echo number_format($firstClicks); ?></span>
                                <span class="label">clicks</span>
                            </div>
                            <div class="campaign-detail-stat">
                                <span class="value" id="selectedSubmits"><?php echo number_format($firstSubmits); ?></span>
                                <span class="label">submits</span>
                            </div>
                            <div class="campaign-detail-stat">
                                <span class="value" id="selectedConv"><?php echo number_format($firstConv, 1); ?>%</span>
                                <span class="label">conv</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Adsets -->
                    <div class="campaign-detail-section">
                        <h5>🎯 Top Adsets:</h5>
                        <div class="winning-list" id="adsetsList">
                            <?php 
                            $rank = 0;
                            foreach (array_slice($firstData['adsets'], 0, 5, true) as $adsetName => $adsetData): 
                                $rank++;
                                $adsetClicks = intval($adsetData['clicks']);
                                $adsetSubmits = intval($adsetData['formSubmits']);
                                $adsetConv = $adsetClicks > 0 ? ($adsetSubmits / $adsetClicks) * 100 : 0;
                            ?>
                                <div class="winning-item <?php echo $rank <= 3 ? 'rank-'.$rank : ''; ?>">
                                    <span class="rank-num"><?php echo $rank; ?>.</span>
                                    <span class="item-name" title="<?php echo htmlspecialchars($adsetName); ?>"><?php echo htmlspecialchars($adsetName); ?></span>
                                    <span class="item-clicks"><?php echo number_format($adsetClicks); ?> clicks</span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($firstData['adsets'])): ?>
                                <p style="color: #8993A4; font-size: 13px; padding: 10px;">No adset data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Top Ads -->
                    <div class="campaign-detail-section">
                        <h5>📝 Top Ads:</h5>
                        <div class="winning-list" id="adsList">
                            <?php 
                            $rank = 0;
                            foreach (array_slice($firstData['ads'], 0, 5, true) as $adName => $adData): 
                                $rank++;
                                $adClicks = intval($adData['clicks']);
                                $adSubmits = intval($adData['formSubmits']);
                                $adConv = $adClicks > 0 ? ($adSubmits / $adClicks) * 100 : 0;
                            ?>
                                <div class="winning-item <?php echo $rank <= 3 ? 'rank-'.$rank : ''; ?>">
                                    <span class="rank-num"><?php echo $rank; ?>.</span>
                                    <span class="item-name" title="<?php echo htmlspecialchars($adName); ?>"><?php echo htmlspecialchars($adName); ?></span>
                                    <span class="item-clicks"><?php echo number_format($adClicks); ?> clicks</span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($firstData['ads'])): ?>
                                <p style="color: #8993A4; font-size: 13px; padding: 10px;">No ad data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Store hierarchy data for JavaScript -->
            <script>
                var cbHierarchyData = <?php echo json_encode($cbHierarchyJson); ?>;
            </script>
            
            <!-- Secondary Cards: Source & Placement -->
            <div class="two-col-grid">
                <!-- By Source -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">🔗 By Source</h3>
                    </div>
                    <table class="cb-breakdown-table">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Clicks</th>
                                <th>Submits</th>
                                <th>Conv %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($cbUtmSources, 0, 10, true) as $name => $data): 
                                $clicks = intval($data['clicks']);
                                $submits = intval($data['formSubmits']);
                                $conv = $clicks > 0 ? ($submits / $clicks) * 100 : 0;
                            ?>
                                <tr>
                                    <td class="utm-cell" title="<?php echo htmlspecialchars($name); ?>"><strong><?php echo htmlspecialchars($name); ?></strong></td>
                                    <td><?php echo number_format($clicks); ?></td>
                                    <td><?php echo number_format($submits); ?></td>
                                    <td><?php echo number_format($conv, 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($cbUtmSources)): ?>
                                <tr><td colspan="4" style="text-align: center; color: #8993A4;">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- By Placement -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📍 By Placement</h3>
                    </div>
                    <table class="cb-breakdown-table">
                        <thead>
                            <tr>
                                <th>Placement</th>
                                <th>Clicks</th>
                                <th>Submits</th>
                                <th>Conv %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($cbPlacements, 0, 10, true) as $name => $data): 
                                $clicks = intval($data['clicks']);
                                $submits = intval($data['formSubmits']);
                                $conv = $clicks > 0 ? ($submits / $clicks) * 100 : 0;
                            ?>
                                <tr>
                                    <td class="utm-cell" title="<?php echo htmlspecialchars($name); ?>"><strong><?php echo htmlspecialchars($name); ?></strong></td>
                                    <td><?php echo number_format($clicks); ?></td>
                                    <td><?php echo number_format($submits); ?></td>
                                    <td><?php echo number_format($conv, 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($cbPlacements)): ?>
                                <tr><td colspan="4" style="text-align: center; color: #8993A4;">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- UTM Term (Adset) Table -->
            <div class="two-col-grid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">🏷 By UTM Term (Adset)</h3>
                    </div>
                    <table class="cb-breakdown-table">
                        <thead>
                            <tr>
                                <th>UTM Term</th>
                                <th>Clicks</th>
                                <th>Submits</th>
                                <th>Conv %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($cbUtmTerms, 0, 10, true) as $name => $data):
                                $clicks = intval($data['clicks']);
                                $submits = intval($data['formSubmits']);
                                $conv = $clicks > 0 ? ($submits / $clicks) * 100 : 0;
                            ?>
                                <tr>
                                    <td class="utm-cell" title="<?php echo htmlspecialchars($name); ?>"><strong><?php echo htmlspecialchars($name); ?></strong></td>
                                    <td><?php echo number_format($clicks); ?></td>
                                    <td><?php echo number_format($submits); ?></td>
                                    <td><?php echo number_format($conv, 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($cbUtmTerms)): ?>
                                <tr><td colspan="4" style="text-align: center; color: #8993A4;">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div></div>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ============ CAMPAIGN SIDE-BY-SIDE FUNCTIONS ============
        function selectCampaign(element, campaignName) {
            // Remove active from all items
            document.querySelectorAll('.campaign-list-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active to clicked item
            element.classList.add('active');
            
            // Get data for this campaign
            if (typeof cbHierarchyData !== 'undefined' && cbHierarchyData[campaignName]) {
                const data = cbHierarchyData[campaignName];
                const clicks = data.clicks;
                const submits = data.submits;
                const conv = clicks > 0 ? ((submits / clicks) * 100).toFixed(1) : '0.0';
                
                // Update header
                document.getElementById('selectedCampaignName').textContent = campaignName;
                document.getElementById('selectedClicks').textContent = clicks.toLocaleString();
                document.getElementById('selectedSubmits').textContent = submits.toLocaleString();
                document.getElementById('selectedConv').textContent = conv + '%';
                
                // Update Adsets list
                const adsetsList = document.getElementById('adsetsList');
                if (data.adsets.length > 0) {
                    let adsetsHtml = '';
                    data.adsets.slice(0, 5).forEach((adset, index) => {
                        const rank = index + 1;
                        const rankClass = rank <= 3 ? 'rank-' + rank : '';
                        adsetsHtml += `
                            <div class="winning-item ${rankClass}">
                                <span class="rank-num">${rank}.</span>
                                <span class="item-name" title="${escapeHtml(adset.name)}">${escapeHtml(adset.name)}</span>
                                <span class="item-clicks">${adset.clicks.toLocaleString()} clicks</span>
                            </div>
                        `;
                    });
                    adsetsList.innerHTML = adsetsHtml;
                } else {
                    adsetsList.innerHTML = '<p style="color: #8993A4; font-size: 13px; padding: 10px;">No adset data</p>';
                }
                
                // Update Ads list
                const adsList = document.getElementById('adsList');
                if (data.ads.length > 0) {
                    let adsHtml = '';
                    data.ads.slice(0, 5).forEach((ad, index) => {
                        const rank = index + 1;
                        const rankClass = rank <= 3 ? 'rank-' + rank : '';
                        adsHtml += `
                            <div class="winning-item ${rankClass}">
                                <span class="rank-num">${rank}.</span>
                                <span class="item-name" title="${escapeHtml(ad.name)}">${escapeHtml(ad.name)}</span>
                                <span class="item-clicks">${ad.clicks.toLocaleString()} clicks</span>
                            </div>
                        `;
                    });
                    adsList.innerHTML = adsHtml;
                } else {
                    adsList.innerHTML = '<p style="color: #8993A4; font-size: 13px; padding: 10px;">No ad data</p>';
                }
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // ============ ANALYTICS DATE MODAL FUNCTIONS ============
        function openDateModal() {
            document.getElementById('dateModal').classList.add('active');
        }
        
        function closeDateModal() {
            document.getElementById('dateModal').classList.remove('active');
        }
        
        function applyDateRange() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            document.getElementById('dateRangeDisplay').value = start + ' to ' + end;
            closeDateModal();
        }
        
        function applyPreset(preset) {
            const today = new Date();
            let startDate, endDate;
            
            switch(preset) {
                case 'today':
                    startDate = new Date(today);
                    endDate = new Date(today);
                    break;
                case 'yesterday':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 1);
                    endDate = new Date(today);
                    endDate.setDate(today.getDate() - 1);
                    break;
                case 'last7':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 6);
                    endDate = new Date(today);
                    break;
                case 'last14':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 13);
                    endDate = new Date(today);
                    break;
                case 'last28':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 27);
                    endDate = new Date(today);
                    break;
                case 'last30':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 29);
                    endDate = new Date(today);
                    break;
                case 'thisweek':
                    const dayOfWeek = today.getDay();
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - dayOfWeek);
                    endDate = new Date(today);
                    break;
                case 'lastweek':
                    const currentDayOfWeek = today.getDay();
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - currentDayOfWeek - 7);
                    endDate = new Date(today);
                    endDate.setDate(today.getDate() - currentDayOfWeek - 1);
                    break;
                case 'thismonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today);
                    break;
                case 'lastmonth':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'custom':
                    return;
            }
            
            if (startDate && endDate) {
                updateModalDateDisplay(startDate, endDate);
                if (flatpickrInstance) {
                    flatpickrInstance.setDate([startDate, endDate], false);
                }
            }
        }
        
        function updateModalDateDisplay(start, end) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            document.getElementById('modalStartDate').textContent = start.toLocaleDateString('en-US', options);
            document.getElementById('modalEndDate').textContent = end.toLocaleDateString('en-US', options);
            document.getElementById('startDate').value = formatDate(start);
            document.getElementById('endDate').value = formatDate(end);
        }
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        function resetDateFilter() {
            const currentTab = document.getElementById('currentTab').value || 'analytics';
            window.location.href = '?tab=' + currentTab;
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const dateModal = document.getElementById('dateModal');
            if (event.target === dateModal) {
                closeDateModal();
            }
        }
        
        // ============ FLATPICKR INSTANCES ============
        let flatpickrInstance;
        
        flatpickrInstance = flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: ["<?php echo $startDate; ?>", "<?php echo $endDate; ?>"],
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    updateModalDateDisplay(selectedDates[0], selectedDates[1]);
                    document.querySelector('input[name="preset"][value="custom"]').checked = true;
                }
            }
        });
        
        // ============ DAILY TREND CHART ============
        <?php if (!empty($dailyStats)): ?>
        const dailyData = <?php echo json_encode($dailyStats); ?>;
        const dates = Object.keys(dailyData).sort();
        const views = dates.map(d => dailyData[d].views);
        const clicks = dates.map(d => dailyData[d].clicks);
        
        const ctx = document.getElementById('dailyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Page Views',
                            data: views,
                            borderColor: '#0066FF',
                            backgroundColor: 'rgba(0, 102, 255, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Pricing Clicks',
                            data: clicks,
                            borderColor: '#00875A',
                            backgroundColor: 'rgba(0, 135, 90, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        <?php endif; ?>
        
        // ============ TAB SWITCHING ============
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update the hidden tab input for the global date form
            document.getElementById('currentTab').value = tabName;
            
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            // Preserve date params
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            if (startDate) url.searchParams.set('start_date', startDate);
            if (endDate) url.searchParams.set('end_date', endDate);
            history.pushState({tab: tabName}, '', url);
        }

        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.tab) {
                const tabButtons = document.querySelectorAll('.tab-btn');
                tabButtons.forEach(btn => {
                    if (btn.dataset.tab === e.state.tab) {
                        btn.click();
                    }
                });
            }
        });

        // ============ CHECKBOX & DELETE ============
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleDeleteButton();
        });

        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleDeleteButton);
        });

        function toggleDeleteButton() {
            const anyChecked = document.querySelectorAll('.row-checkbox:checked').length > 0;
            const deleteContainer = document.getElementById('deleteBtnContainer');
            if (anyChecked) {
                deleteContainer.classList.add('show');
            } else {
                deleteContainer.classList.remove('show');
            }
        }

        // ============ FOLLOW-UP → WHATSAPP ============
        function openFollowupWA(el) {
            var biz = el.getAttribute('data-biz') || '-';
            var email = el.getAttribute('data-email') || '-';
            var pkg = el.getAttribute('data-pkg') || '-';
            var reviews = el.getAttribute('data-reviews') || '-';
            var wa = el.getAttribute('data-wa') || '';
            // Clean phone number: keep digits and leading +
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

        // ============ COPY EMAIL ============
        function copyFollowupEmail(el) {
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
                el.style.background = '#0066FF';
                el.style.color = '#fff';
                setTimeout(function() {
                    el.textContent = orig;
                    el.style.background = '';
                    el.style.color = '';
                }, 1500);
            }).catch(function() {
                // Fallback
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                el.textContent = 'Copied!';
                setTimeout(function() { el.textContent = 'Copy Email'; }, 1500);
            });
        }

        // ============ LOGOUT ============
        <?php
        if (isset($_GET['logout'])) {
            session_destroy();
            setcookie('remember_me', '', time() - 3600, '/');
            echo 'window.location.href = "log.php";';
        }
        ?>
        
        // ============ AJAX LIVE SEARCH ============
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const campaignFilter = document.getElementById('campaignFilter');
        const pageFilter = document.getElementById('pageFilter');
        const customerFilters = document.getElementById('customerFilters');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 300);
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });
        }
        
        if (statusFilter) statusFilter.addEventListener('change', performSearch);
        if (campaignFilter) campaignFilter.addEventListener('change', performSearch);
        if (pageFilter) pageFilter.addEventListener('change', performSearch);
        
        function performSearch() {
            const search = searchInput ? searchInput.value : '';
            const status = statusFilter ? statusFilter.value : 'all';
            const campaign = campaignFilter ? campaignFilter.value : 'all';
            const page = pageFilter ? pageFilter.value : 'all';
            
            const params = new URLSearchParams({
                ajax_search: '1',
                search: search,
                status: status,
                campaign: campaign,
                page: page
            });
            
            fetch('log.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateCustomerTable(data.customers);
                    }
                })
                .catch(error => console.error('Search error:', error));
        }
        
        function updateCustomerTable(customers) {
            const tbody = document.querySelector('#customers-tab table tbody');
            if (!tbody) return;
            
            if (customers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="17" style="text-align: center; padding: 40px;">
                            <div class="empty-state">
                                <div class="empty-state-icon">🔍</div>
                                <h4>No results found</h4>
                                <p>Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            customers.forEach((customer, index) => {
                const isClickOnly = customer.isClickOnly;
                const rowStyle = isClickOnly ? 'background: #FFF4E6;' : '';
                const clickBadge = isClickOnly ? '<span class="click-only-badge">CLICK</span>' : '';
                
                const escape = (str) => {
                    const div = document.createElement('div');
                    div.textContent = str;
                    return div.innerHTML;
                };
                
                html += `
                    <tr style="${rowStyle}">
                        <td><input type="checkbox" name="selected[]" value="${index}" class="row-checkbox"></td>
                        <td>${escape(customer.timestamp)}</td>
                        <td>${escape(customer.businessName)}${clickBadge}</td>
                        <td>${escape(customer.location)}</td>
                        <td>${escape(customer.businessEmail)}</td>
                        <td>${escape(customer.whatsapp)}</td>
                        <td>${escape(customer.package)}</td>
                        <td>${escape(customer.reviewsQty)}</td>
                        <td class="utm-cell" title="${escape(customer.utmSource)}">${escape(customer.utmSource)}</td>
                        <td class="utm-cell" title="${escape(customer.utmMedium)}">${escape(customer.utmMedium)}</td>
                        <td class="utm-cell" title="${escape(customer.utmCampaign)}">${escape(customer.utmCampaign)}</td>
                        <td class="utm-cell" title="${escape(customer.utmContent)}">${escape(customer.utmContent)}</td>
                        <td class="utm-cell" title="${escape(customer.utmTerm || '-')}">${escape(customer.utmTerm || '-')}</td>
                        <td class="utm-cell" title="${escape(customer.placement)}">${escape(customer.placement)}</td>
                        <td>${escape(customer.state)}</td>
                        <td>${escape(customer.zipCode)}</td>
                        <td>${escape(customer.country)}</td>
                        <td>
                            <a href="#" class="followup-btn"
                                onclick="openFollowupWA(this); return false;"
                                data-biz="${escape(customer.businessName)}"
                                data-email="${escape(customer.businessEmail)}"
                                data-pkg="${escape(customer.package)}"
                                data-reviews="${escape(customer.reviewsQty)}"
                                data-wa="${escape(customer.whatsapp)}">
                                <span class="text">Follow Up</span>
                            </a>
                            <button type="button" class="copyemail-btn"
                                onclick="copyFollowupEmail(this)"
                                data-biz="${escape(customer.businessName)}"
                                data-email="${escape(customer.businessEmail)}"
                                data-pkg="${escape(customer.package)}"
                                data-reviews="${escape(customer.reviewsQty)}">
                                Copy Email
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', toggleDeleteButton);
            });
        }
    </script>
</body>
</html>
<?php
exit;
?>