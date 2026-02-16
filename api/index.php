<?php
// This file handles all requests on Vercel

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Your original code starts here
$num_api_url = "https://num.proportalxc.workers.dev/";
$rc_api_url = "https://org.proportalxc.workers.dev/";
$ig_api_url = "https://instagram-api-ashy.vercel.app/api/ig-profile.php";
$adhar_api_url = "https://mu-beige-six.vercel.app/api/adhar/";

// Handle AJAX requests
if(isset($_GET['type'])) {
    header('Content-Type: application/json');
    
    if($_GET['type'] == 'number' && isset($_GET['mobile'])) {
        $mobile = urlencode($_GET['mobile']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $num_api_url . "?mobile=" . $mobile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
        exit;
    }
    
    if($_GET['type'] == 'rc' && isset($_GET['rc'])) {
        $rc = strtolower(urlencode($_GET['rc']));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rc_api_url . "?rc=" . $rc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
        exit;
    }
    
    if($_GET['type'] == 'ig' && isset($_GET['username'])) {
        $username = urlencode($_GET['username']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ig_api_url . "?username=" . $username);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
        exit;
    }
    
    if($_GET['type'] == 'adhar' && isset($_GET['number'])) {
        $number = urlencode($_GET['number']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $adhar_api_url . $number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Osint Tool</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Courier New', monospace;
            background:#0a0a0a;
            min-height:100vh;
            padding:20px;
            position:relative;
            overflow-x:hidden;
            color:#0f0;
        }
        #matrix-bg {
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            z-index:-1;
            opacity:0.15;
        }
        .container {
            width:100%;
            max-width:900px;
            margin:0 auto;
            position:relative;
            z-index:1;
        }
        .dev-header {
            background:rgba(0,255,0,0.05);
            border:1px solid #0f0;
            border-radius:10px;
            padding:15px 20px;
            margin-bottom:20px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            backdrop-filter:blur(5px);
            animation:glowPulse 2s infinite;
        }
        @keyframes glowPulse { 0%,100% {box-shadow:0 0 10px rgba(0,255,0,0.3);} 50% {box-shadow:0 0 20px rgba(0,255,0,0.6);} }
        .dev-info { display:flex; align-items:center; gap:15px; }
        .dev-avatar {
            width:50px; height:50px;
            background:linear-gradient(45deg,#0f0,#00ff88);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:24px; color:#000; font-weight:bold;
            border:2px solid #fff; box-shadow:0 0 20px #0f0;
        }
        .dev-details { color:#0f0; }
        .dev-name { font-size:1.3em; font-weight:bold; text-transform:uppercase; letter-spacing:2px; margin-bottom:5px; }
        .dev-title { font-size:0.8em; opacity:0.7; display:flex; align-items:center; gap:10px; }
        .dev-title i { color:#0f0; font-size:1.2em; }
        .social-links { display:flex; gap:15px; }
        .social-icon {
            width:40px; height:40px;
            border:1px solid #0f0; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            color:#0f0; text-decoration:none; font-size:1.2em;
            transition:all 0.3s; position:relative; overflow:hidden;
        }
        .social-icon::before {
            content:''; position:absolute; top:50%; left:50%;
            width:0; height:0; background:rgba(0,255,0,0.3); border-radius:50%;
            transform:translate(-50%,-50%); transition:width 0.3s,height 0.3s;
        }
        .social-icon:hover::before { width:100px; height:100px; }
        .social-icon:hover { background:#0f0; color:#000; transform:translateY(-3px); box-shadow:0 5px 20px #0f0; }
        .glitch-wrapper { text-align:center; margin-bottom:30px; }
        .glitch-text {
            font-size:2.5em; font-weight:bold; text-transform:uppercase; color:#0f0;
            text-shadow:0 0 10px #0f0, 2px 2px 0 #ff00de, -2px -2px 0 #00ffff;
            animation:glitch 3s infinite; letter-spacing:5px;
        }
        .sub-glitch { color:#0f0; font-size:0.9em; opacity:0.7; margin-top:5px; }
        @keyframes glitch {
            0% {transform:skew(0deg);} 5% {transform:skew(5deg);} 10% {transform:skew(0deg);}
            15% {transform:skew(-5deg);} 20% {transform:skew(0deg);} 100% {transform:skew(0deg);}
        }
        .tab-container { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
        .tab-button {
            flex:1; min-width:150px; padding:15px;
            background:transparent; border:2px solid #0f0; color:#0f0;
            font-family:'Courier New',monospace; font-size:1.1em; font-weight:bold;
            text-transform:uppercase; letter-spacing:2px; cursor:pointer;
            border-radius:10px; transition:all 0.3s;
            display:flex; align-items:center; justify-content:center; gap:10px;
        }
        .tab-button i { font-size:1.2em; }
        .tab-button:hover { background:rgba(0,255,0,0.1); box-shadow:0 0 20px rgba(0,255,0,0.3); }
        .tab-button.active { background:#0f0; color:#000; box-shadow:0 0 30px #0f0; }
        .terminal-box {
            background:rgba(10,10,10,0.95); border:2px solid #0f0; border-radius:15px;
            padding:25px; box-shadow:0 0 30px rgba(0,255,0,0.3);
            backdrop-filter:blur(10px); position:relative; overflow:hidden; margin-bottom:20px;
        }
        .terminal-box::before {
            content:""; position:absolute; top:0; left:0; width:100%; height:2px;
            background:linear-gradient(90deg,transparent,#0f0,transparent);
            animation:scan 3s linear infinite;
        }
        @keyframes scan { 0% {transform:translateX(-100%);} 100% {transform:translateX(100%);} }
        .terminal-header { display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #0f0; }
        .terminal-dots { display:flex; gap:8px; }
        .dot { width:12px; height:12px; border-radius:50%; }
        .dot.red { background:#ff5f56; }
        .dot.yellow { background:#ffbd2e; }
        .dot.green { background:#27c93f; }
        .terminal-title { color:#0f0; font-size:0.9em; margin-left:auto; text-transform:uppercase; letter-spacing:2px; }
        .input-group { position:relative; margin-bottom:20px; }
        .input-label { color:#0f0; font-size:0.9em; margin-bottom:8px; display:block; text-transform:uppercase; letter-spacing:2px; }
        .input-field {
            width:100%; padding:15px 20px; background:rgba(0,255,0,0.05);
            border:2px solid #0f0; border-radius:10px; color:#0f0;
            font-family:'Courier New',monospace; font-size:1.1em; outline:none;
            transition:all 0.3s;
        }
        .input-field:focus { background:rgba(0,255,0,0.1); box-shadow:0 0 20px rgba(0,255,0,0.3); }
        .input-field::placeholder { color:rgba(0,255,0,0.3); }
        .hack-button {
            width:100%; padding:15px; background:transparent; border:2px solid #0f0; color:#0f0;
            font-family:'Courier New',monospace; font-size:1.2em; font-weight:bold;
            text-transform:uppercase; letter-spacing:3px; cursor:pointer;
            border-radius:10px; position:relative; overflow:hidden; transition:all 0.3s;
            margin-bottom:25px;
        }
        .hack-button:hover { background:#0f0; color:#000; box-shadow:0 0 30px #0f0; }
        .hack-button.loading { cursor:not-allowed; opacity:0.7; animation:pulse 1.5s infinite; }
        @keyframes pulse { 0%,100% {opacity:0.7;} 50% {opacity:1;} }
        .results-container { max-height:500px; overflow-y:auto; padding-right:10px; }
        .results-container::-webkit-scrollbar { width:5px; }
        .results-container::-webkit-scrollbar-track { background:rgba(0,255,0,0.1); }
        .results-container::-webkit-scrollbar-thumb { background:#0f0; border-radius:5px; }
        .hacker-card {
            background:rgba(0,255,0,0.05); border:1px solid #0f0; border-radius:10px;
            padding:15px; margin-bottom:15px; animation:slideIn 0.3s ease;
            backdrop-filter:blur(5px); position:relative;
        }
        @keyframes slideIn { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }
        .card-title {
            color:#0f0; font-size:1.1em; margin-bottom:15px; padding-bottom:5px;
            border-bottom:1px solid #0f0; text-transform:uppercase; letter-spacing:2px;
            display:flex; align-items:center; gap:10px;
        }
        .card-field { display:grid; grid-template-columns:200px 1fr; gap:10px; margin-bottom:10px; font-size:0.95em; }
        .field-label { color:#0f0; text-transform:uppercase; letter-spacing:1px; font-size:0.85em; opacity:0.9; }
        .field-value { color:#fff; word-break:break-word; display:flex; align-items:center; gap:10px; }
        .copy-btn {
            background:transparent; border:1px solid #0f0; color:#0f0;
            padding:3px 10px; border-radius:5px; cursor:pointer; font-size:0.8em;
            transition:all 0.3s; display:inline-flex; align-items:center; gap:5px;
        }
        .copy-btn:hover { background:#0f0; color:#000; }
        .copy-btn.copied { background:#0f0; color:#000; }
        .status-bar {
            margin-top:20px; padding:10px; background:rgba(0,255,0,0.05);
            border:1px solid #0f0; border-radius:5px; color:#0f0; font-size:0.9em;
            display:flex; justify-content:space-between;
        }
        .typing-effect::after { content:"█"; animation:blink 1s infinite; }
        @keyframes blink { 0%,50% {opacity:1;} 51%,100% {opacity:0;} }
        .error-message {
            background:rgba(255,0,0,0.1); border:1px solid #f00; border-radius:10px;
            padding:20px; color:#f00; text-align:center; font-size:1.1em;
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
                    <div class="dev-title"><i class="fas fa-code"></i> Lead Developer & Security Researcher</div>
                </div>
            </div>
            <div class="social-links">
                <a href="https://instagram.com/_keerthu__poojary_" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="https://t.me/Bunny_2050" target="_blank" class="social-icon"><i class="fab fa-github"></i></a>
                <a href="https://t.me/Bunny_2050" target="_blank" class="social-icon"><i class="fab fa-telegram"></i></a>
            </div>
        </div>

        <div class="glitch-wrapper">
            <div class="glitch-text">OSISINT TOOL</div>
            <div class="sub-glitch">NUMBER INFO | VEHICLE RC | INSTAGRAM | AADHAR</div>
        </div>

        <div class="tab-container">
            <button class="tab-button active" onclick="switchTab('number')"><i class="fas fa-phone"></i> NUMBER</button>
            <button class="tab-button" onclick="switchTab('rc')"><i class="fas fa-car"></i> VEHICLE RC</button>
            <button class="tab-button" onclick="switchTab('ig')"><i class="fab fa-instagram"></i> INSTAGRAM</button>
            <button class="tab-button" onclick="switchTab('adhar')"><i class="fas fa-id-card"></i> AADHAAR</button>
        </div>

        <!-- Tab contents -->
        <div id="tab-number-content" class="terminal-box">
            <div class="terminal-header">
                <div class="terminal-dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
                <div class="terminal-title">ROOT@NUMBER-INFO :~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> MOBILE NUMBER</label>
                <input type="text" id="numberInput" class="input-field" placeholder="Enter 10-digit number..." onkeypress="if(event.key==='Enter') fetchNumberInfo()">
            </div>
            <button class="hack-button" onclick="fetchNumberInfo()"><span>[ SEARCH NUMBER ]</span></button>
            <div id="numberResults" class="results-container"></div>
        </div>

        <div id="tab-rc-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">
                <div class="terminal-dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
                <div class="terminal-title">ROOT@VEHICLE-RC :~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> RC NUMBER</label>
                <input type="text" id="rcInput" class="input-field" placeholder="e.g. KA19HV4003" value="KA19HV4003" onkeypress="if(event.key==='Enter') fetchRCInfo()">
            </div>
            <button class="hack-button" onclick="fetchRCInfo()"><span>[ SEARCH RC ]</span></button>
            <div id="rcResults" class="results-container"></div>
        </div>

        <div id="tab-ig-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">
                <div class="terminal-dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
                <div class="terminal-title">ROOT@INSTAGRAM :~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> INSTAGRAM USERNAME</label>
                <input type="text" id="igInput" class="input-field" placeholder="Enter username..." onkeypress="if(event.key==='Enter') fetchIGInfo()">
            </div>
            <button class="hack-button" onclick="fetchIGInfo()"><span>[ SEARCH INSTAGRAM ]</span></button>
            <div id="igResults" class="results-container"></div>
        </div>

        <div id="tab-adhar-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">
                <div class="terminal-dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
                <div class="terminal-title">ROOT@AADHAR-INFO :~#</div>
            </div>
            <div class="input-group">
                <label class="input-label">> AADHAR NUMBER</label>
                <input type="text" id="adharInput" class="input-field" placeholder="Enter 12-digit Aadhaar..." maxlength="12" onkeypress="if(event.key==='Enter') fetchAdharInfo()">
            </div>
            <button class="hack-button" onclick="fetchAdharInfo()"><span>[ SEARCH AADHAR ]</span></button>
            <div id="adharResults" class="results-container"></div>
        </div>

        <div class="status-bar">
            <span id="statusText">WAITING FOR INPUT...</span>
            <span class="typing-effect"></span>
        </div>

        <div style="text-align:center; margin-top:30px; color:rgba(0,255,0,0.4); font-size:0.9em;">
            Developed with ❤️ by Keerthu Poojary © 2026
        </div>
    </div>

    <script>
        // Matrix rain background
        const canvas = document.getElementById('matrix-bg');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()_+-=[]{}|;:,.<>?/\\';
        const fontSize = 14;
        const columns = canvas.width / fontSize;
        const drops = Array(Math.floor(columns)).fill(1);

        function draw() {
            ctx.fillStyle = 'rgba(0,0,0,0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#0f0';
            ctx.font = fontSize + 'px monospace';
            drops.forEach((y, i) => {
                const text = chars[Math.floor(Math.random() * chars.length)];
                const x = i * fontSize;
                ctx.fillText(text, x, y * fontSize);
                if (y * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            });
        }
        setInterval(draw, 33);
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
            document.querySelectorAll('.terminal-box').forEach(box => box.style.display = 'none');
            document.getElementById(`tab-${tab}-content`).style.display = 'block';
            document.getElementById('statusText').textContent = `${tab.toUpperCase()} MODE ACTIVE`;
        }

        // Copy to clipboard helper
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = 'COPIED!';
                btn.style.background = '#0f0';
                btn.style.color = '#000';
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.style.background = 'transparent';
                    btn.style.color = '#0f0';
                }, 1500);
            });
        }

        function escapeHtml(text) {
            return text.replace(/[&<>"']/g, m => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[m]));
        }

        function showError(container, msg) {
            container.innerHTML = `<div class="error-message">ERROR: ${escapeHtml(msg)}</div>`;
        }

        // Fetch functions would go here (fetchNumberInfo, fetchRCInfo, etc.)
        // They are missing in the visible part of your screenshot, but normally follow this pattern:
        /*
        async function fetchRCInfo() {
            const rc = document.getElementById('rcInput').value.trim().toLowerCase();
            if (!rc) return showError(document.getElementById('rcResults'), 'Please enter RC number');
            
            const btn = document.querySelector('#tab-rc-content .hack-button');
            btn.classList.add('loading');
            btn.querySelector('span').textContent = 'QUERYING...';

            try {
                const res = await fetch(`?type=rc&rc=${encodeURIComponent(rc)}`);
                const text = await res.text();
                let json;
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    // Attempt to fix trailing garbage
                    const fixed = text.substring(0, text.lastIndexOf('}') + 1);
                    json = JSON.parse(fixed);
                }
                // render results...
            } catch (err) {
                showError(document.getElementById('rcResults'), err.message);
            } finally {
                btn.classList.remove('loading');
                btn.querySelector('span').textContent = '[ SEARCH RC ]';
            }
        }
        */

        // Initial state
        switchTab('rc');  // since your screenshot shows VEHICLE RC active
    </script>
</body>
</html>
