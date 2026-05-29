<?php
/**
 * ============================================================================
 * File: /submit/login.php
 * Smart Buzzer AM Login - v3.3 (Simple Password Only)
 *
 * v3.3: Removed Indonesia IP restriction (all IPs can access login)
 * v3.2: Removed IP blocking & failed attempt tracking
 * v3.1: Bcrypt password hash + unified session variables with auth.php
 *
 * Features:
 * - Password only login (no username)
 * - 1-year persistent session (login forever)
 *
 * Password: smartbuzzer2025!
 *
 * Author: Smart Buzzer Development Team
 * Last Updated: March 2026
 * ============================================================================
 */

ini_set('session.gc_maxlifetime', 31536000); // 1 year
ini_set('session.cookie_lifetime', 31536000); // 1 year
session_start();

$PASSWORD_HASH = '$2y$12$qaICwvY1IwTaD7CPjZd9/.AGH1Oo7H8U9h9OIC0nNBbdci1g8mek.'; // smartbuzzer2025!

function getClientIp() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            if (strpos($ip, ',') !== false) $ip = trim(explode(',', $ip)[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

$clientIp = getClientIp();

if (isset($_SESSION['am_logged_in']) && $_SESSION['am_logged_in'] === true) { header('Location: manage'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (password_verify($password, $PASSWORD_HASH)) {
        $_SESSION['am_logged_in'] = true;
        $_SESSION['am_username'] = 'olfin';
        $_SESSION['am_name'] = 'Olfin';
        $_SESSION['am_email'] = 'olfin@smart-buzzer.com';
        $_SESSION['am_role'] = 'admin';
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['user_ip'] = $clientIp;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        session_regenerate_id(true);
        header('Location: manage');
        exit;
    } else {
        $error = "Wrong password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #fff; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            padding: 20px;
        }
        .login-box { width: 100%; max-width: 360px; }
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }
        .input-wrap { position: relative; margin-bottom: 16px; }
        .input-wrap input {
            width: 100%;
            padding: 14px 48px 14px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 15px;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s;
        }
        .input-wrap input:focus { border-color: #000; }
        .input-wrap input::placeholder { color: #a3a3a3; }
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #a3a3a3;
            padding: 4px;
        }
        .toggle-pw:hover { color: #000; }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-btn:hover { background: #333; }
        .login-btn:disabled { background: #666; cursor: wait; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.8s linear infinite; }
    </style>
</head>
<body>

    <div class="login-box">

        <?php if ($error): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="input-wrap">
                <input type="password" id="password" name="password" required autofocus placeholder="Enter password">
                <button type="button" id="togglePw" class="toggle-pw" tabindex="-1">
                    <svg id="eyeOn" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg id="eyeOff" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            <button type="submit" id="loginBtn" class="login-btn">Login</button>
        </form>
    </div>

    <script>
        const pw = document.getElementById('password');
        const toggle = document.getElementById('togglePw');
        const eyeOn = document.getElementById('eyeOn');
        const eyeOff = document.getElementById('eyeOff');
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');

        toggle.addEventListener('click', () => {
            pw.type = pw.type === 'password' ? 'text' : 'password';
            eyeOn.style.display = pw.type === 'password' ? 'block' : 'none';
            eyeOff.style.display = pw.type === 'password' ? 'none' : 'block';
        });

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.innerHTML = '<svg class="spinner" style="display:inline-block;vertical-align:middle" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        });
    </script>
</body>
</html>