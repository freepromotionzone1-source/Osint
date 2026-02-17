<?php
// Disable HTML error output – we always return JSON for API calls
ini_set('display_errors', 0);
error_reporting(E_ALL); // Keep logging errors, but don't display

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ------------------------------------------------------------------
// API endpoints
// ------------------------------------------------------------------
$num_api_url = "https://num.proportalxc.workers.dev/";
$rc_api_url = "https://org.proportalxc.workers.dev/";
$ig_api_url = "https://instagram-api-ashy.vercel.app/api/ig-profile.php";
$adhar_api_url = "https://mu-beige-six.vercel.app/api/adhar/";

// ------------------------------------------------------------------
// Enhanced API caller with JSON cleaning and logging
// ------------------------------------------------------------------
function callAPI($url, $timeout = 15) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return ['error' => 'CURL Error: ' . $curlError, 'http_code' => 500];
    }
    
    // --- Clean the response ---
    // 1. Remove UTF-8 BOM
    $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
    // 2. Convert to valid UTF-8 (discard invalid sequences)
    $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
    
    // 3. Try to extract JSON object/array if surrounded by other text
    $jsonStart = strpos($response, '{');
    if ($jsonStart === false) $jsonStart = strpos($response, '[');
    $jsonEnd = strrpos($response, '}');
    if ($jsonEnd === false) $jsonEnd = strrpos($response, ']');
    
    if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
        $response = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
    }
    
    // 4. Validate JSON
    json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Optional: log the problematic response for debugging
        // file_put_contents(__DIR__ . '/api_error.log', date('Y-m-d H:i:s') . " URL: $url\nINVALID JSON:\n$response\n\n", FILE_APPEND);
        
        return [
            'error' => 'Invalid JSON response from API',
            'http_code' => $httpCode,
            'raw_sample' => substr($response, 0, 500) // first 500 chars for debugging
        ];
    }
    
    return [
        'success' => true,
        'data' => $response,
        'http_code' => $httpCode
    ];
}

// ------------------------------------------------------------------
// Handle AJAX requests
// ------------------------------------------------------------------
if (isset($_GET['type'])) {
    header('Content-Type: application/json');
    
    // ---------- Number Info ----------
    if ($_GET['type'] == 'number' && isset($_GET['mobile'])) {
        $mobile = urlencode($_GET['mobile']);
        $result = callAPI($num_api_url . "?mobile=" . $mobile);
        
        if (isset($result['error'])) {
            // Return error with optional debug info (remove raw_sample in production)
            echo json_encode([
                'success' => false,
                'error' => $result['error'],
                'debug' => isset($result['raw_sample']) ? $result['raw_sample'] : null
            ]);
        } else {
            echo $result['data'];
        }
        exit;
    }
    
    // ---------- RC Info ----------
    if ($_GET['type'] == 'rc' && isset($_GET['rc'])) {
        $rc = strtolower(urlencode($_GET['rc']));
        $result = callAPI($rc_api_url . "?rc=" . $rc);
        
        if (isset($result['error'])) {
            echo json_encode([
                'success' => false,
                'error' => $result['error'],
                'debug' => isset($result['raw_sample']) ? $result['raw_sample'] : null
            ]);
        } else {
            echo $result['data'];
        }
        exit;
    }
    
    // ---------- Instagram ----------
    if ($_GET['type'] == 'ig' && isset($_GET['username'])) {
        $username = urlencode($_GET['username']);
        $result = callAPI($ig_api_url . "?username=" . $username);
        
        if (isset($result['error'])) {
            echo json_encode([
                'success' => false,
                'error' => $result['error'],
                'debug' => isset($result['raw_sample']) ? $result['raw_sample'] : null
            ]);
        } else {
            echo $result['data'];
        }
        exit;
    }
    
    // ---------- Aadhar ----------
    if ($_GET['type'] == 'adhar' && isset($_GET['number'])) {
        $number = urlencode($_GET['number']);
        $result = callAPI($adhar_api_url . $number);
        
        if (isset($result['error'])) {
            echo json_encode([
                'success' => false,
                'error' => $result['error'],
                'debug' => isset($result['raw_sample']) ? $result['raw_sample'] : null
            ]);
        } else {
            echo $result['data'];
        }
        exit;
    }
    
    // If type is unknown
    echo json_encode(['success' => false, 'error' => 'Invalid request type']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="monetag" content="c07757dbf7f3bb0fe9d777664f27dff4">
    <title>⚡ Osint Tool</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ... (all your existing CSS, unchanged) ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a0a;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        #matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.15;
        }
        
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .dev-header {
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(5px);
            animation: glowPulse 2s infinite;
        }
        
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(0, 255, 0, 0.3); }
            50% { box-shadow: 0 0 20px rgba(0, 255, 0, 0.6); }
        }
        
        .dev-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .dev-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #0f0, #00ff88);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #000;
            font-weight: bold;
            border: 2px solid #fff;
            box-shadow: 0 0 20px #0f0;
        }
        
        .dev-details {
            color: #0f0;
        }
        
        .dev-name {
            font-size: 1.3em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .dev-title {
            font-size: 0.8em;
            opacity: 0.7;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dev-title i {
            color: #0f0;
            font-size: 1.2em;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border: 1px solid #0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f0;
            text-decoration: none;
            font-size: 1.2em;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .social-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(0, 255, 0, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        
        .social-icon:hover::before {
            width: 100px;
            height: 100px;
        }
        
        .social-icon:hover {
            background: #0f0;
            color: #000;
            transform: translateY(-3px);
            box-shadow: 0 5px 20px #0f0;
        }
        
        .glitch-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .glitch-text {
            font-size: 2.5em;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f0;
            text-shadow: 
                0 0 10px #0f0,
                2px 2px 0 #ff00de,
                -2px -2px 0 #00ffff;
            animation: glitch 3s infinite;
            letter-spacing: 5px;
        }
        
        .sub-glitch {
            color: #0f0;
            font-size: 0.9em;
            opacity: 0.7;
            margin-top: 5px;
        }
        
        @keyframes glitch {
            0% { transform: skew(0deg); }
            5% { transform: skew(5deg); }
            10% { transform: skew(0deg); }
            15% { transform: skew(-5deg); }
            20% { transform: skew(0deg); }
            100% { transform: skew(0deg); }
        }
        
        .tab-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .tab-button {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .tab-button i {
            font-size: 1.2em;
        }
        
        .tab-button:hover {
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }
        
        .tab-button.active {
            background: #0f0;
            color: #000;
            box-shadow: 0 0 30px #0f0;
        }
        
        .terminal-box {
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid #0f0;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.3);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .terminal-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #0f0, transparent);
            animation: scan 3s linear infinite;
        }
        
        @keyframes scan {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .terminal-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #0f0;
        }
        
        .terminal-dots {
            display: flex;
            gap: 8px;
        }
        
        .dot.red { background: #ff5f56; width: 12px; height: 12px; border-radius: 50%; }
        .dot.yellow { background: #ffbd2e; width: 12px; height: 12px; border-radius: 50%; }
        .dot.green { background: #27c93f; width: 12px; height: 12px; border-radius: 50%; }
        
        .terminal-title {
            color: #0f0;
            font-size: 0.9em;
            margin-left: auto;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .input-label {
            color: #0f0;
            font-size: 0.9em;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .input-field {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 255, 0, 0.05);
            border: 2px solid #0f0;
            border-radius: 10px;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            outline: none;
            transition: all 0.3s;
        }
        
        .input-field:focus {
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }
        
        .input-field::placeholder {
            color: rgba(0, 255, 0, 0.3);
        }
        
        .hack-button {
            width: 100%;
            padding: 15px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            cursor: pointer;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 25px;
        }
        
        .hack-button:hover {
            background: #0f0;
            color: #000;
            box-shadow: 0 0 30px #0f0;
        }
        
        .hack-button.loading {
            cursor: not-allowed;
            opacity: 0.7;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        .results-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .results-container::-webkit-scrollbar {
            width: 5px;
        }
        
        .results-container::-webkit-scrollbar-track {
            background: rgba(0, 255, 0, 0.1);
        }
        
        .results-container::-webkit-scrollbar-thumb {
            background: #0f0;
            border-radius: 5px;
        }
        
        .hacker-card {
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transform-origin: top;
            animation: slideIn 0.3s ease;
            backdrop-filter: blur(5px);
            position: relative;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-title {
            color: #0f0;
            font-size: 1.1em;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #0f0;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-field {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.95em;
        }
        
        .field-label {
            color: #0f0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85em;
            opacity: 0.9;
        }
        
        .field-value {
            color: #fff;
            word-break: break-word;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .copy-btn {
            background: transparent;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 3px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8em;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .copy-btn:hover {
            background: #0f0;
            color: #000;
        }
        
        .copy-btn.copied {
            background: #0f0;
            color: #000;
        }
        
        .status-bar {
            margin-top: 20px;
            padding: 10px;
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            border-radius: 5px;
            color: #0f0;
            font-size: 0.9em;
            display: flex;
            justify-content: space-between;
        }
        
        .typing-effect::after {
            content: "█";
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #f00;
            border-radius: 10px;
            padding: 20px;
            color: #f00;
            text-align: center;
            font-size: 1.1em;
        }
        
        .stats-mini {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            border-radius: 5px;
            color: #0f0;
            font-size: 0.9em;
            flex-wrap: wrap;
        }
        
        .stats-mini span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .section-header {
            color: #0f0;
            font-size: 1.1em;
            margin: 15px 0 10px;
            padding-left: 10px;
            border-left: 3px solid #0f0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .ig-profile {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(0, 255, 0, 0.02);
            border-radius: 10px;
            border: 1px solid #0f0;
        }
        
        .ig-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #0f0;
        }
        
        .ig-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .ig-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .stat-box {
            text-align: center;
        }
        
        .stat-number {
            color: #0f0;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .stat-label {
            color: rgba(0, 255, 0, 0.7);
            font-size: 0.8em;
            text-transform: uppercase;
        }
        
        .badge {
            padding: 3px 8px;
            border: 1px solid #0f0;
            border-radius: 5px;
            font-size: 0.8em;
        }
        
        .badge.duplicate {
            border-color: #ff0;
            color: #ff0;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) {
            .card-field {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            
            .dev-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .ig-profile {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .ig-stats {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <canvas id="matrix-bg"></canvas>
    <div class="container">
        <div class="dev-header">
            <div class="dev-info">
                <div class="dev-avatar">KP</div>
                <div class="dev-details">
                    <div class="dev-name">KEERTHU POOJARY</div>
                    <div class="dev-title">
                        <i class="fas fa-code"></i> Lead Developer & Security Researcher
                    </div>
                </div>
            </div>
            <div class="social-links">
                <a href="https://instagram.com/_keerthu__poojary_" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://t.me/Bunny_2050" target="_blank" class="social-icon">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://t.me/Bunny_2050" target="_blank" class="social-icon">
                    <i class="fab fa-telegram"></i>
                </a>
            </div>
        </div>

        <div class="glitch-wrapper">
            <div class="glitch-text">⚡ Osint TOOL</div>
            <div class="sub-glitch">NUMBER INFO | VEHICLE RC | INSTAGRAM | AADHAR</div>
        </div>

        <div class="tab-container">
            <button class="tab-button active" onclick="switchTab('number')" id="tab-number">
                <i class="fas fa-phone"></i> NUMBER
            </button>
            <button class="tab-button" onclick="switchTab('rc')" id="tab-rc">
                <i class="fas fa-car"></i> VEHICLE RC
            </button>
            <button class="tab-button" onclick="switchTab('ig')" id="tab-ig">
                <i class="fab fa-instagram"></i> INSTAGRAM
            </button>
            <button class="tab-button" onclick="switchTab('adhar')" id="tab-adhar">
                <i class="fas fa-id-card"></i> AADHAAR
            </button>
        </div>

        <div id="tab-number-content" class="terminal-box">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </div>
                <div class="terminal-title">root@number-info:~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> MOBILE NUMBER</label>
                <input type="text" id="numberInput" class="input-field" placeholder="Enter mobile number..." maxlength="15" value="" onkeypress="if(event.key==='Enter') fetchNumberInfo()">
            </div>
            <button id="numberBtn" class="hack-button" onclick="fetchNumberInfo()">
                <span id="numberBtnText">[ SEARCH NUMBER ]</span>
            </button>
            <div id="numberResults" class="results-container"></div>
        </div>

        <div id="tab-rc-content" class="terminal-box" style="display: none;">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </div>
                <div class="terminal-title">root@vehicle-rc:~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> RC NUMBER</label>
                <input type="text" id="rcInput" class="input-field" placeholder="Enter RC number (e.g., KA19HG4665)" value="" onkeypress="if(event.key==='Enter') fetchRCInfo()">
            </div>
            <button id="rcBtn" class="hack-button" onclick="fetchRCInfo()">
                <span id="rcBtnText">[ SEARCH RC ]</span>
            </button>
            <div id="rcResults" class="results-container"></div>
        </div>

        <div id="tab-ig-content" class="terminal-box" style="display: none;">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </div>
                <div class="terminal-title">root@instagram:~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> INSTAGRAM USERNAME</label>
                <input type="text" id="igInput" class="input-field" placeholder="Enter username..." value="" onkeypress="if(event.key==='Enter') fetchIGInfo()">
            </div>
            <button id="igBtn" class="hack-button" onclick="fetchIGInfo()">
                <span id="igBtnText">[ SEARCH INSTAGRAM ]</span>
            </button>
            <div id="igResults" class="results-container"></div>
        </div>

        <div id="tab-adhar-content" class="terminal-box" style="display: none;">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </div>
                <div class="terminal-title">root@aadhar-info:~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> AADHAR NUMBER</label>
                <input type="text" id="adharInput" class="input-field" placeholder="Enter 12-digit Aadhar number..." maxlength="12" pattern="\d*" value="" onkeypress="if(event.key==='Enter') fetchAdharInfo()">
            </div>
            <button id="adharBtn" class="hack-button" onclick="fetchAdharInfo()">
                <span id="adharBtnText">[ SEARCH AADHAR ]</span>
            </button>
            <div id="adharResults" class="results-container"></div>
        </div>

        <div class="status-bar">
            <span id="statusText">SYSTEM READY</span>
            <span class="typing-effect"></span>
        </div>

        <div style="text-align: center; margin-top: 20px; color: rgba(0,255,0,0.5); font-size: 0.8em;">
            <i class="fas fa-crown"></i> Developed with <i class="fas fa-heart" style="color: #0f0;"></i> by Keerthu Poojary &copy; 2026
        </div>
    </div>

    <script src="https://quge5.com/88/tag.min.js" data-zone="211995" async data-cfasync="false"></script>

    <script>
        // Matrix background effect
        const canvas = document.getElementById('matrix-bg');
        const ctx = canvas.getContext('2d');
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        
        const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
        const matrixArray = matrix.split("");
        const fontSize = 10;
        const columns = canvas.width / fontSize;
        const drops = [];
        for(let x = 0; x < columns; x++) {
            drops[x] = 1;
        }
        
        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#0f0';
            ctx.font = fontSize + 'px monospace';
            for(let i = 0; i < drops.length; i++) {
                const text = matrixArray[Math.floor(Math.random() * matrixArray.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }
        setInterval(drawMatrix, 35);
        
        window.addEventListener('resize', () => {
            resizeCanvas();
        });

        // Tab switching
        function switchTab(tab) {
            document.getElementById('tab-number').classList.remove('active');
            document.getElementById('tab-rc').classList.remove('active');
            document.getElementById('tab-ig').classList.remove('active');
            document.getElementById('tab-adhar').classList.remove('active');
            document.getElementById('tab-' + tab).classList.add('active');
            
            document.getElementById('tab-number-content').style.display = 'none';
            document.getElementById('tab-rc-content').style.display = 'none';
            document.getElementById('tab-ig-content').style.display = 'none';
            document.getElementById('tab-adhar-content').style.display = 'none';
            document.getElementById('tab-' + tab + '-content').style.display = 'block';
            
            document.getElementById('statusText').innerHTML = tab.toUpperCase() + ' MODE ACTIVE';
        }

        // Utility functions
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;")
                .replace(/\n/g, '\\n');
        }

        function formatLabel(key) {
            return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
        }

        function copyToClipboard(text, button) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> COPIED';
                button.classList.add('copied');
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('copied');
                }, 2000);
            } catch (err) {
                alert('Failed to copy text');
            }
            
            document.body.removeChild(textarea);
        }

        function showError(element, message) {
            element.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-triangle"></i> ⚠ ' + message + '</div>';
            document.getElementById('statusText').innerHTML = 'ERROR: ' + message;
        }

        // Number info functions
        async function fetchNumberInfo() {
            const number = document.getElementById('numberInput').value.trim();
            const resultsDiv = document.getElementById('numberResults');
            const btn = document.getElementById('numberBtn');
            const btnText = document.getElementById('numberBtnText');
            const statusText = document.getElementById('statusText');
            
            if (!number || !/^\d+$/.test(number)) {
                showError(resultsDiv, 'INVALID MOBILE NUMBER');
                return;
            }
            
            btn.classList.add('loading');
            btnText.innerHTML = 'SEARCHING...';
            statusText.innerHTML = 'SEARCHING NUMBER DATABASE...';
            resultsDiv.innerHTML = '';
            
            try {
                const response = await fetch('?type=number&mobile=' + encodeURIComponent(number));
                const responseText = await response.text();
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Invalid JSON:', responseText.substring(0, 200));
                    showError(resultsDiv, 'API returned invalid data format');
                    return;
                }
                
                if (!data.success || !data.result?.results?.length) {
                    showError(resultsDiv, 'NO DATA FOUND');
                } else {
                    displayNumberResults(data, resultsDiv);
                    statusText.innerHTML = `FOUND ${data.result.results.length} RECORDS (${data.result.search_time})`;
                }
            } catch (error) {
                showError(resultsDiv, 'NETWORK ERROR: ' + error.message);
            } finally {
                btn.classList.remove('loading');
                btnText.innerHTML = '[ SEARCH NUMBER ]';
            }
        }

        function displayNumberResults(data, resultsDiv) {
            const results = data.result.results;
            const targetNumber = document.getElementById('numberInput').value.trim();
            
            const primary = results.filter(r => r.mobile === targetNumber);
            const associated = results.filter(r => r.mobile !== targetNumber && r.alt === targetNumber);
            const others = results.filter(r => r.mobile !== targetNumber && r.alt !== targetNumber);
            
            const uniqueOthers = [];
            const seen = new Set();
            others.forEach(r => {
                const key = `${r.mobile}-${r.name}`;
                if (!seen.has(key)) {
                    seen.add(key);
                    uniqueOthers.push(r);
                }
            });
            
            let html = '<div class="stats-mini">' +
                '<span><i class="fas fa-database"></i> TOTAL: ' + results.length + '</span>' +
                '<span><i class="fas fa-user"></i> PRIMARY: ' + primary.length + '</span>' +
                '<span><i class="fas fa-users"></i> ASSOCIATED: ' + associated.length + '</span>' +
                '<span><i class="fas fa-clock"></i> TIME: ' + data.result.search_time + '</span>' +
                '</div>';
            
            if (primary.length > 0) {
                html += '<div class="section-header">▶ PRIMARY RECORDS</div>';
                primary.forEach(result => { html += createNumberCard(result); });
            }
            
            if (associated.length > 0) {
                html += '<div class="section-header">▶ ASSOCIATED RECORDS</div>';
                associated.forEach(result => { html += createNumberCard(result); });
            }
            
            if (uniqueOthers.length > 0) {
                html += '<div class="section-header">▶ RELATED RECORDS</div>';
                uniqueOthers.forEach(result => { html += createNumberCard(result); });
            }
            
            resultsDiv.innerHTML = html;
        }

        function createNumberCard(result) {
            const fields = [
                { label: 'MOBILE', value: result.mobile, icon: 'fa-phone' },
                { label: 'NAME', value: result.name, icon: 'fa-user' },
                { label: 'FATHER NAME', value: result.fname, icon: 'fa-user-tie' },
                { label: 'ADDRESS', value: result.address, icon: 'fa-map-marker-alt' },
                { label: 'ALTERNATE', value: result.alt, icon: 'fa-phone-alt' },
                { label: 'CIRCLE', value: result.circle, icon: 'fa-satellite-dish' },
                { label: 'EMAIL', value: result.email, icon: 'fa-envelope' },
                { label: 'ID', value: result.id, icon: 'fa-id-card' }
            ];
            
            let html = '<div class="hacker-card">';
            
            fields.forEach(field => {
                if (field.value && field.value.toString().trim() !== '') {
                    html += '<div class="card-field">' +
                        '<div class="field-label"><i class="fas ' + field.icon + '"></i> ' + field.label + ':</div>' +
                        '<div class="field-value">' + escapeHtml(field.value) + 
                        '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(field.value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                        '</div></div>';
                }
            });
            
            html += '</div>';
            return html;
        }

        // RC info functions
        async function fetchRCInfo() {
            let rc = document.getElementById('rcInput').value.trim();
            const resultsDiv = document.getElementById('rcResults');
            const btn = document.getElementById('rcBtn');
            const btnText = document.getElementById('rcBtnText');
            const statusText = document.getElementById('statusText');
            
            if (!rc) {
                showError(resultsDiv, 'ENTER RC NUMBER');
                return;
            }
            
            rc = rc.toLowerCase();
            btn.classList.add('loading');
            btnText.innerHTML = 'SEARCHING...';
            statusText.innerHTML = 'FETCHING VEHICLE DETAILS...';
            resultsDiv.innerHTML = '';
            
            try {
                const response = await fetch('?type=rc&rc=' + encodeURIComponent(rc));
                const responseText = await response.text();
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Invalid JSON response:', responseText.substring(0, 300));
                    
                    // Try to extract JSON if there's extra text
                    const jsonMatch = responseText.match(/\{.*\}/s);
                    if (jsonMatch) {
                        try {
                            data = JSON.parse(jsonMatch[0]);
                        } catch (e2) {
                            showError(resultsDiv, 'API returned invalid data format');
                            return;
                        }
                    } else {
                        showError(resultsDiv, 'API returned invalid data format');
                        return;
                    }
                }
                
                if (data.error || !data.data) {
                    showError(resultsDiv, 'RC NOT FOUND OR API ERROR');
                } else {
                    displayRCResults(data, resultsDiv);
                    const reg = data.data?.["registration_identity_matrix"]?.["official_registration_id"] || rc.toUpperCase();
                    statusText.innerHTML = 'VEHICLE FOUND: ' + reg;
                }
            } catch (error) {
                showError(resultsDiv, 'NETWORK ERROR: ' + error.message);
            } finally {
                btn.classList.remove('loading');
                btnText.innerHTML = '[ SEARCH RC ]';
            }
        }

        function displayRCResults(data, resultsDiv) {
            const vehicleData = data.data || {};
            let html = '';
            
            // Registration Identity
            if (vehicleData["registration_identity_matrix"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-id-card"></i> REGISTRATION IDENTITY</div>';
                
                Object.entries(vehicleData["registration_identity_matrix"]).forEach(([key, value]) => {
                    if (!key.includes('seal') && !key.includes('auth') && !key.includes('token') && value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Ownership Details
            if (vehicleData["ownership_profile_analytics"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-user"></i> OWNERSHIP DETAILS</div>';
                
                Object.entries(vehicleData["ownership_profile_analytics"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Technical Specifications
            if (vehicleData["technical_structural_blueprint"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-cog"></i> TECHNICAL SPECIFICATIONS</div>';
                
                Object.entries(vehicleData["technical_structural_blueprint"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Insurance Details
            if (vehicleData["insurance_security_audit_report"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-shield-alt"></i> INSURANCE DETAILS</div>';
                
                Object.entries(vehicleData["insurance_security_audit_report"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Lifecycle Timeline
            if (vehicleData["lifecycle_compliance_timeline"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-calendar-alt"></i> LIFECYCLE TIMELINE</div>';
                
                Object.entries(vehicleData["lifecycle_compliance_timeline"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Financial & Legal
            if (vehicleData["financial_legal_encumbrance_vault"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-gavel"></i> FINANCIAL & LEGAL</div>';
                
                Object.entries(vehicleData["financial_legal_encumbrance_vault"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Performance Matrix
            if (vehicleData["ai_performance_valuation_matrix"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-chart-line"></i> PERFORMANCE MATRIX</div>';
                
                Object.entries(vehicleData["ai_performance_valuation_matrix"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            // Regional Info
            if (vehicleData["regional_transport_intelligence_grid"]) {
                html += '<div class="hacker-card">' +
                    '<div class="card-title"><i class="fas fa-map-marker-alt"></i> REGIONAL INFO</div>';
                
                Object.entries(vehicleData["regional_transport_intelligence_grid"]).forEach(([key, value]) => {
                    if (value) {
                        html += '<div class="card-field">' +
                            '<div class="field-label">' + formatLabel(key) + ':</div>' +
                            '<div class="field-value">' + (escapeHtml(value) || 'N/A') + 
                            '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                            '</div></div>';
                    }
                });
                html += '</div>';
            }
            
            if (html === '') {
                html = '<div class="error-message">NO VEHICLE DATA FOUND</div>';
            }
            
            resultsDiv.innerHTML = html;
        }

        // Instagram functions
        async function fetchIGInfo() {
            const username = document.getElementById('igInput').value.trim();
            const resultsDiv = document.getElementById('igResults');
            const btn = document.getElementById('igBtn');
            const btnText = document.getElementById('igBtnText');
            const statusText = document.getElementById('statusText');
            
            if (!username) {
                showError(resultsDiv, 'ENTER USERNAME');
                return;
            }
            
            btn.classList.add('loading');
            btnText.innerHTML = 'SEARCHING...';
            statusText.innerHTML = 'FETCHING INSTAGRAM PROFILE...';
            resultsDiv.innerHTML = '';
            
            try {
                const response = await fetch('?type=ig&username=' + encodeURIComponent(username));
                const responseText = await response.text();
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Invalid JSON:', responseText.substring(0, 200));
                    showError(resultsDiv, 'API returned invalid data format');
                    return;
                }
                
                if (data.status !== 'ok' || !data.profile) {
                    showError(resultsDiv, 'PROFILE NOT FOUND');
                } else {
                    displayIGResults(data.profile, resultsDiv);
                    statusText.innerHTML = '@' + data.profile.username + ' - ' + (data.profile.followers || 0) + ' FOLLOWERS';
                }
            } catch (error) {
                showError(resultsDiv, 'NETWORK ERROR: ' + error.message);
            } finally {
                btn.classList.remove('loading');
                btnText.innerHTML = '[ SEARCH INSTAGRAM ]';
            }
        }

        function displayIGResults(profile, resultsDiv) {
            let html = '<div class="hacker-card">';
            
            // Profile header
            html += '<div class="ig-profile">' +
                '<div class="ig-avatar">' +
                '<img src="' + (profile.profile_pic_url_hd || 'https://via.placeholder.com/100') + '" alt="Profile" onerror="this.src=\'https://via.placeholder.com/100\'">' +
                '</div>' +
                '<div style="flex:1;">' +
                '<div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; flex-wrap:wrap;">' +
                '<span style="color:#0f0; font-size:1.3em;">@' + profile.username + '</span>';
            
            if (profile.is_verified) {
                html += '<span class="badge" style="background:rgba(0,255,0,0.2); border-color:#0f0;"><i class="fas fa-check-circle"></i> VERIFIED</span>';
            }
            
            if (profile.is_private) {
                html += '<span class="badge" style="background:rgba(255,255,0,0.2); border-color:#ff0; color:#ff0;"><i class="fas fa-lock"></i> PRIVATE</span>';
            }
            
            html += '</div>' +
                '<div style="color:#fff; font-size:1.1em; margin-bottom:5px;">' + escapeHtml(profile.full_name) + '</div>' +
                '<div style="color:rgba(0,255,0,0.7); font-size:0.9em; margin-bottom:10px;">' + escapeHtml(profile.biography) + '</div>' +
                '<div class="ig-stats">' +
                '<div class="stat-box"><div class="stat-number">' + (profile.posts || 0) + '</div><div class="stat-label">POSTS</div></div>' +
                '<div class="stat-box"><div class="stat-number">' + (profile.followers || 0) + '</div><div class="stat-label">FOLLOWERS</div></div>' +
                '<div class="stat-box"><div class="stat-number">' + (profile.following || 0) + '</div><div class="stat-label">FOLLOWING</div></div>' +
                '</div></div></div>';
            
            // Profile details
            html += '<div class="card-title" style="margin-top:15px;"><i class="fas fa-info-circle"></i> PROFILE DETAILS</div>';
            
            const details = [
                { label: 'USER ID', value: profile.id, icon: 'fa-id-card' },
                { label: 'ACCOUNT TYPE', value: profile.is_business_account ? 'Business' : profile.is_professional_account ? 'Professional' : 'Personal', icon: 'fa-user-tag' },
                { label: 'CREATED', value: profile.account_creation_year || 'N/A', icon: 'fa-calendar' },
                { label: 'CATEGORY', value: profile.category_name, icon: 'fa-tag' }
            ];
            
            details.forEach(d => {
                if (d.value) {
                    html += '<div class="card-field">' +
                        '<div class="field-label"><i class="fas ' + d.icon + '"></i> ' + d.label + ':</div>' +
                        '<div class="field-value">' + escapeHtml(d.value) + 
                        '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(d.value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                        '</div></div>';
                }
            });
            
            if (profile.external_url) {
                html += '<div class="card-field">' +
                    '<div class="field-label"><i class="fas fa-link"></i> WEBSITE:</div>' +
                    '<div class="field-value">' +
                    '<a href="' + profile.external_url + '" target="_blank" style="color:#0f0;">' + profile.external_url + '</a>' +
                    '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(profile.external_url).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                    '</div></div>';
            }
            
            html += '</div>';
            resultsDiv.innerHTML = html;
        }

        // Aadhar functions
        async function fetchAdharInfo() {
            const number = document.getElementById('adharInput').value.trim();
            const resultsDiv = document.getElementById('adharResults');
            const btn = document.getElementById('adharBtn');
            const btnText = document.getElementById('adharBtnText');
            const statusText = document.getElementById('statusText');
            
            if (!number || !/^\d{12}$/.test(number)) {
                showError(resultsDiv, 'ENTER VALID 12-DIGIT AADHAR NUMBER');
                return;
            }
            
            btn.classList.add('loading');
            btnText.innerHTML = 'SEARCHING...';
            statusText.innerHTML = 'FETCHING AADHAR LINKED MOBILE NUMBERS...';
            resultsDiv.innerHTML = '';
            
            try {
                const response = await fetch('?type=adhar&number=' + encodeURIComponent(number));
                const responseText = await response.text();
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Invalid JSON:', responseText.substring(0, 200));
                    showError(resultsDiv, 'API returned invalid data format');
                    return;
                }
                
                if (!data || data.status !== 'success' || !data.results || data.results.length === 0) {
                    showError(resultsDiv, 'NO RECORDS FOUND FOR THIS AADHAR');
                } else {
                    displayAdharResults(data, resultsDiv);
                    statusText.innerHTML = 'FOUND ' + data.total_found + ' RECORDS FOR AADHAR: ' + number;
                }
            } catch (error) {
                showError(resultsDiv, 'NETWORK ERROR: ' + error.message);
            } finally {
                btn.classList.remove('loading');
                btnText.innerHTML = '[ SEARCH AADHAR ]';
            }
        }

        function displayAdharResults(data, resultsDiv) {
            const results = data.results || [];
            const targetAadhar = document.getElementById('adharInput').value.trim();
            
            let html = '<div class="stats-mini">' +
                '<span><i class="fas fa-database"></i> TOTAL: ' + (data.total_found || results.length) + '</span>' +
                '<span><i class="fas fa-id-card"></i> AADHAR: ' + targetAadhar + '</span>' +
                '</div>';
            
            if (results.length > 0) {
                html += '<div class="section-header">▶ LINKED MOBILE RECORDS</div>';
                
                const seenMobiles = new Set();
                results.forEach((result, index) => {
                    if (seenMobiles.has(result.mobile)) {
                        html += createAadharRecordCard(result, index + 1, true);
                    } else {
                        seenMobiles.add(result.mobile);
                        html += createAadharRecordCard(result, index + 1, false);
                    }
                });
            }
            
            resultsDiv.innerHTML = html;
        }

        function createAadharRecordCard(record, index, isDuplicate = false) {
            let html = '<div class="hacker-card">' +
                '<div class="card-title"><i class="fas fa-mobile-alt"></i> RECORD #' + index;
            
            if (isDuplicate) {
                html += '<span class="badge duplicate"><i class="fas fa-copy"></i> DUPLICATE MOBILE</span>';
            }
            
            html += '</div>';
            
            const fields = [
                { label: 'NAME', value: record.name, icon: 'fa-user' },
                { label: 'FATHER NAME', value: record.fname, icon: 'fa-user-tie' },
                { label: 'MOBILE', value: record.mobile, icon: 'fa-phone' },
                { label: 'ALTERNATE', value: record.alt, icon: 'fa-phone-alt' },
                { label: 'ADDRESS', value: record.address, icon: 'fa-map-marker-alt' },
                { label: 'EMAIL', value: record.email, icon: 'fa-envelope' },
                { label: 'AADHAR ID', value: record.id, icon: 'fa-id-card' }
            ];
            
            fields.forEach(field => {
                if (field.value && field.value.toString().trim() !== '') {
                    let valueDisplay = escapeHtml(field.value);
                    if (field.label === 'MOBILE' || field.label === 'ALTERNATE') {
                        valueDisplay = '<span style="color:#0f0; font-weight:bold;">' + valueDisplay + '</span>';
                    }
                    
                    html += '<div class="card-field">' +
                        '<div class="field-label"><i class="fas ' + field.icon + '"></i> ' + field.label + ':</div>' +
                        '<div class="field-value">' + valueDisplay + 
                        '<button class="copy-btn" onclick="copyToClipboard(\'' + escapeHtml(field.value).replace(/'/g, "\\'") + '\', this)"><i class="fas fa-copy"></i> COPY</button>' +
                        '</div></div>';
                }
            });
            
            html += '</div>';
            return html;
        }

        // Initialize
        setTimeout(() => {
            document.getElementById('statusText').innerHTML = 'NUMBER INFO MODE ACTIVE';
        }, 2000);
    </script>
</body>
</html>
