<?php
// Password Protection
session_start();
$PASSWORD = 'smartbuzzer2025!';

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['email_extractor_auth'] = true;
    } else {
        $loginError = true;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['email_extractor_auth']);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Check authentication
if (!isset($_SESSION['email_extractor_auth']) || $_SESSION['email_extractor_auth'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Email Extractor</title>
        <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-box {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 40px;
                width: 100%;
                max-width: 400px;
                text-align: center;
            }
            .login-box h1 {
                color: #fff;
                font-size: 24px;
                margin-bottom: 10px;
            }
            .login-box p {
                color: #8892b0;
                font-size: 14px;
                margin-bottom: 30px;
            }
            .login-box input {
                width: 100%;
                padding: 14px 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.05);
                color: #fff;
                font-size: 16px;
                margin-bottom: 20px;
            }
            .login-box input:focus {
                outline: none;
                border-color: #4facfe;
            }
            .login-box input::placeholder { color: #8892b0; }
            .login-box button {
                width: 100%;
                padding: 14px;
                border: none;
                border-radius: 8px;
                background: linear-gradient(90deg, #4facfe, #00f2fe);
                color: #1a1a2e;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .login-box button:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(79, 172, 254, 0.4);
            }
            .error {
                background: rgba(255, 107, 107, 0.2);
                border: 1px solid rgba(255, 107, 107, 0.5);
                color: #ff6b6b;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>🔐 Email Extractor</h1>
            <p>Enter password to access</p>
            <?php if (isset($loginError)): ?>
                <div class="error">Incorrect password. Please try again.</div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" autofocus required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Extractor - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            color: #8892b0;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 32px;
            color: #4facfe;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #8892b0;
            font-size: 14px;
        }
        
        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #4facfe;
        }
        
        .search-box::placeholder {
            color: #8892b0;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: #1a1a2e;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(79, 172, 254, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(90deg, #11998e, #38ef7d);
            color: #1a1a2e;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(56, 239, 125, 0.4);
        }
        
        .source-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .source-tag {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }
        
        .source-tag:hover {
            border-color: #4facfe;
        }
        
        .source-tag.active {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: #1a1a2e;
            border-color: transparent;
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table-header h2 {
            font-size: 16px;
            color: #fff;
        }
        
        .table-scroll {
            max-height: 500px;
            overflow-y: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        th {
            background: rgba(255, 255, 255, 0.05);
            color: #8892b0;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: rgba(79, 172, 254, 0.1);
        }
        
        td {
            font-size: 14px;
            color: #e0e0e0;
        }
        
        .email-cell {
            color: #4facfe;
            font-family: monospace;
        }
        
        .source-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(79, 172, 254, 0.2);
            color: #4facfe;
        }
        
        .copy-btn {
            padding: 4px 10px;
            border: none;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .copy-btn:hover {
            background: #4facfe;
            color: #1a1a2e;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #8892b0;
        }
        
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            background: #38ef7d;
            color: #1a1a2e;
            font-weight: 600;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
            }
            
            th, td {
                padding: 10px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Email Extractor</h1>
            <p>Extract customer emails from all landing page sources</p>
            <a href="?logout=1" style="display: inline-block; margin-top: 15px; padding: 8px 20px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: #8892b0; text-decoration: none; font-size: 13px; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#ff6b6b'; this.style.color='#ff6b6b';" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='#8892b0';">🚪 Logout</a>
        </div>
        
        <?php
        // Configuration - Landing page sources
        $BASE_PATH = '/home/u387681977/domains/smart-buzzer.com/public_html';
        
        $sources = [
            'blackfriday' => $BASE_PATH . '/blackfriday',
            'promo-2' => $BASE_PATH . '/promo-2',
            'promo-australia' => $BASE_PATH . '/promo-australia',
            'promo-b1g1' => $BASE_PATH . '/promo-b1g1',
            'promo-rating' => $BASE_PATH . '/promo-rating',
            'promo' => $BASE_PATH . '/promo',
            'xmas' => $BASE_PATH . '/xmas'
        ];
        
        // Collect all emails
        $allEmails = [];
        $sourceStats = [];
        $totalRecords = 0;
        
        foreach ($sources as $name => $path) {
            $logFile = $path . '/customer_data.log';
            $sourceStats[$name] = 0;
            
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                
                foreach ($lines as $line) {
                    $columns = explode("\t", $line);
                    
                    // Email is at index 3 (4th column)
                    if (isset($columns[3]) && !empty(trim($columns[3])) && $columns[3] !== '-') {
                        $email = trim($columns[3]);
                        
                        // Basic email validation
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $timestamp = isset($columns[0]) ? $columns[0] : 'N/A';
                            $package = isset($columns[5]) ? $columns[5] : 'N/A';
                            
                            $allEmails[] = [
                                'email' => $email,
                                'source' => $name,
                                'timestamp' => $timestamp,
                                'package' => $package
                            ];
                            
                            $sourceStats[$name]++;
                            $totalRecords++;
                        }
                    }
                }
            }
        }
        
        // Get unique emails
        $uniqueEmails = [];
        $seenEmails = [];
        foreach ($allEmails as $record) {
            $emailLower = strtolower($record['email']);
            if (!isset($seenEmails[$emailLower])) {
                $seenEmails[$emailLower] = true;
                $uniqueEmails[] = $record;
            }
        }
        
        $uniqueCount = count($uniqueEmails);
        ?>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo number_format($totalRecords); ?></h3>
                <p>Total Records</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($uniqueCount); ?></h3>
                <p>Unique Emails</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($sources); ?></h3>
                <p>Active Sources</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalRecords - $uniqueCount; ?></h3>
                <p>Duplicates</p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="controls">
            <input type="text" class="search-box" id="searchBox" placeholder="Search emails...">
            <button class="btn btn-primary" onclick="copyAllEmails()">📋 Copy All Emails</button>
            <button class="btn btn-success" onclick="exportCSV()">📥 Export CSV</button>
        </div>
        
        <!-- Source Filters -->
        <div class="source-filters">
            <span class="source-tag active" data-source="all" onclick="filterSource('all', this)">
                All (<?php echo $uniqueCount; ?>)
            </span>
            <?php foreach ($sourceStats as $source => $count): ?>
                <?php if ($count > 0): ?>
                <span class="source-tag" data-source="<?php echo $source; ?>" onclick="filterSource('<?php echo $source; ?>', this)">
                    <?php echo $source; ?> (<?php echo $count; ?>)
                </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <!-- Email Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Email List</h2>
                <span id="visibleCount"><?php echo $uniqueCount; ?> emails</span>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Package</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="emailTable">
                        <?php if (empty($uniqueEmails)): ?>
                            <tr>
                                <td colspan="6" class="no-data">No emails found</td>
                            </tr>
                        <?php else: ?>
                            <?php $index = 1; foreach ($uniqueEmails as $record): ?>
                            <tr data-source="<?php echo htmlspecialchars($record['source']); ?>" data-email="<?php echo htmlspecialchars(strtolower($record['email'])); ?>">
                                <td><?php echo $index++; ?></td>
                                <td class="email-cell"><?php echo htmlspecialchars($record['email']); ?></td>
                                <td><span class="source-badge"><?php echo htmlspecialchars($record['source']); ?></span></td>
                                <td><?php echo htmlspecialchars($record['package']); ?></td>
                                <td><?php echo htmlspecialchars(substr($record['timestamp'], 0, 10)); ?></td>
                                <td><button class="copy-btn" onclick="copyEmail('<?php echo htmlspecialchars($record['email']); ?>')">Copy</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast">Copied to clipboard!</div>
    
    <script>
        // Store all emails for export
        const allEmailsData = <?php echo json_encode($uniqueEmails); ?>;
        
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterTable();
        });
        
        // Filter by source
        function filterSource(source, element) {
            // Update active state
            document.querySelectorAll('.source-tag').forEach(tag => tag.classList.remove('active'));
            element.classList.add('active');
            
            // Store selected source
            document.getElementById('searchBox').dataset.source = source;
            filterTable();
        }
        
        // Combined filter function
        function filterTable() {
            const searchTerm = document.getElementById('searchBox').value.toLowerCase();
            const selectedSource = document.getElementById('searchBox').dataset.source || 'all';
            
            const rows = document.querySelectorAll('#emailTable tr[data-source]');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const email = row.dataset.email;
                const source = row.dataset.source;
                
                const matchesSearch = email.includes(searchTerm);
                const matchesSource = selectedSource === 'all' || source === selectedSource;
                
                if (matchesSearch && matchesSource) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('visibleCount').textContent = visibleCount + ' emails';
        }
        
        // Copy single email
        function copyEmail(email) {
            navigator.clipboard.writeText(email);
            showToast('Email copied!');
        }
        
        // Copy all visible emails
        function copyAllEmails() {
            const rows = document.querySelectorAll('#emailTable tr[data-source]');
            const emails = [];
            
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    emails.push(row.dataset.email);
                }
            });
            
            navigator.clipboard.writeText(emails.join('\n'));
            showToast(emails.length + ' emails copied!');
        }
        
        // Export to CSV
        function exportCSV() {
            const selectedSource = document.getElementById('searchBox').dataset.source || 'all';
            const searchTerm = document.getElementById('searchBox').value.toLowerCase();
            
            let filteredData = allEmailsData;
            
            if (selectedSource !== 'all') {
                filteredData = filteredData.filter(item => item.source === selectedSource);
            }
            
            if (searchTerm) {
                filteredData = filteredData.filter(item => item.email.toLowerCase().includes(searchTerm));
            }
            
            // Create CSV content
            let csv = 'Email,Source,Package,Date\n';
            filteredData.forEach(item => {
                csv += `"${item.email}","${item.source}","${item.package}","${item.timestamp}"\n`;
            });
            
            // Download
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'smart-buzzer-emails-' + new Date().toISOString().slice(0,10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showToast('CSV exported!');
        }
        
        // Toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }
    </script>
</body>
</html>