<?php
// API endpoints
$num_api_url = "https://num.proportalxc.workers.dev/";
$rc_api_url = "https://org.proportalxc.workers.dev/";
$ig_api_url = "https://instagram-api-ashy.vercel.app/api/ig-profile.php";
$adhar_api_url = "https://mu-beige-six.vercel.app/api/adhar/";

// Handle AJAX requests
if(isset($_GET['type'])) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
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
        
        // Validate JSON response
        json_decode($response);
        if(json_last_error() === JSON_ERROR_NONE) {
            echo $response;
        } else {
            echo json_encode(['error' => 'Invalid response from API']);
        }
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a0a;
            min-height: 100vh;
            padding: 20px;
            color: #0f0;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .dev-header {
            background: rgba(0,255,0,0.05);
            border: 1px solid #0f0;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .dev-info { display: flex; align-items: center; gap: 15px; }
        .dev-avatar {
            width: 50px; height: 50px;
            background: #0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
        }
        .social-links { display: flex; gap: 10px; }
        .social-icon {
            width: 40px; height: 40px;
            border: 1px solid #0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f0;
            text-decoration: none;
        }
        .glitch-wrapper { text-align: center; margin: 30px 0; }
        .glitch-text {
            font-size: 2.5em;
            text-shadow: 0 0 10px #0f0;
            animation: glitch 3s infinite;
        }
        @keyframes glitch {
            0% { transform: skew(0deg); }
            5% { transform: skew(5deg); }
            10% { transform: skew(0deg); }
        }
        .tab-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab-button {
            flex: 1;
            padding: 15px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            cursor: pointer;
            border-radius: 10px;
            font-weight: bold;
        }
        .tab-button.active {
            background: #0f0;
            color: #000;
        }
        .terminal-box {
            background: rgba(0,0,0,0.9);
            border: 2px solid #0f0;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .input-field {
            width: 100%;
            padding: 15px;
            background: rgba(0,255,0,0.05);
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New';
            border-radius: 10px;
            margin: 10px 0;
        }
        .hack-button {
            width: 100%;
            padding: 15px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            font-weight: bold;
            cursor: pointer;
            border-radius: 10px;
            margin: 10px 0;
        }
        .hack-button:hover {
            background: #0f0;
            color: #000;
        }
        .results-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .hacker-card {
            background: rgba(0,255,0,0.05);
            border: 1px solid #0f0;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }
        .card-field {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
            margin: 5px 0;
        }
        .field-label { color: #0f0; }
        .field-value { color: #fff; }
        .copy-btn {
            background: transparent;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 2px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        .status-bar {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #0f0;
            border-radius: 5px;
        }
        .error-message {
            color: #f00;
            border: 1px solid #f00;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dev-header">
            <div class="dev-info">
                <div class="dev-avatar">KP</div>
                <div>
                    <div class="dev-name">KEERTHU POOJARY</div>
                    <div>Lead Developer & Security Researcher</div>
                </div>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-telegram"></i></a>
            </div>
        </div>

        <div class="glitch-wrapper">
            <div class="glitch-text">⚡ OSINT TOOL</div>
            <div>NUMBER INFO | VEHICLE RC | INSTAGRAM | AADHAAR</div>
        </div>

        <div class="tab-container">
            <button class="tab-button active" onclick="switchTab('number')">NUMBER</button>
            <button class="tab-button" onclick="switchTab('rc')">VEHICLE RC</button>
            <button class="tab-button" onclick="switchTab('ig')">INSTAGRAM</button>
            <button class="tab-button" onclick="switchTab('adhar')">AADHAAR</button>
        </div>

        <div id="tab-number-content" class="terminal-box">
            <div class="terminal-header">root@number:~#</div>
            <input type="text" id="numberInput" class="input-field" placeholder="Enter mobile number...">
            <button class="hack-button" onclick="fetchNumberInfo()">[ SEARCH NUMBER ]</button>
            <div id="numberResults" class="results-container"></div>
        </div>

        <div id="tab-rc-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">root@vehicle-rc:~#</div>
            <input type="text" id="rcInput" class="input-field" placeholder="Enter RC number..." value="KA19HV4003">
            <button class="hack-button" onclick="fetchRCInfo()">[ SEARCH RC ]</button>
            <div id="rcResults" class="results-container"></div>
        </div>

        <div id="tab-ig-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">root@instagram:~#</div>
            <input type="text" id="igInput" class="input-field" placeholder="Enter username...">
            <button class="hack-button" onclick="fetchIGInfo()">[ SEARCH INSTAGRAM ]</button>
            <div id="igResults" class="results-container"></div>
        </div>

        <div id="tab-adhar-content" class="terminal-box" style="display:none;">
            <div class="terminal-header">root@aadhar:~#</div>
            <input type="text" id="adharInput" class="input-field" placeholder="Enter 12-digit Aadhar...">
            <button class="hack-button" onclick="fetchAdharInfo()">[ SEARCH AADHAR ]</button>
            <div id="adharResults" class="results-container"></div>
        </div>

        <div class="status-bar">
            <span id="statusText">SYSTEM READY</span>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.terminal-box').forEach(b => b.style.display = 'none');
            document.getElementById('tab-' + tab + '-content').style.display = 'block';
            event.target.classList.add('active');
        }

        async function fetchNumberInfo() {
            const number = document.getElementById('numberInput').value;
            const resultsDiv = document.getElementById('numberResults');
            resultsDiv.innerHTML = '<div class="hacker-card">Searching...</div>';
            
            try {
                const res = await fetch('?type=number&mobile=' + number);
                const data = await res.json();
                displayResults(data, resultsDiv);
            } catch(e) {
                resultsDiv.innerHTML = '<div class="error-message">Error: ' + e.message + '</div>';
            }
        }

        async function fetchRCInfo() {
            const rc = document.getElementById('rcInput').value;
            const resultsDiv = document.getElementById('rcResults');
            resultsDiv.innerHTML = '<div class="hacker-card">Searching...</div>';
            
            try {
                const res = await fetch('?type=rc&rc=' + rc);
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    displayRCResults(data, resultsDiv);
                } catch(e) {
                    resultsDiv.innerHTML = '<div class="error-message">Invalid response from server</div>';
                }
            } catch(e) {
                resultsDiv.innerHTML = '<div class="error-message">Error: ' + e.message + '</div>';
            }
        }

        async function fetchIGInfo() {
            const username = document.getElementById('igInput').value;
            const resultsDiv = document.getElementById('igResults');
            resultsDiv.innerHTML = '<div class="hacker-card">Searching...</div>';
            
            try {
                const res = await fetch('?type=ig&username=' + username);
                const data = await res.json();
                resultsDiv.innerHTML = '<div class="hacker-card">' + JSON.stringify(data) + '</div>';
            } catch(e) {
                resultsDiv.innerHTML = '<div class="error-message">Error: ' + e.message + '</div>';
            }
        }

        async function fetchAdharInfo() {
            const number = document.getElementById('adharInput').value;
            const resultsDiv = document.getElementById('adharResults');
            resultsDiv.innerHTML = '<div class="hacker-card">Searching...</div>';
            
            try {
                const res = await fetch('?type=adhar&number=' + number);
                const data = await res.json();
                resultsDiv.innerHTML = '<div class="hacker-card">' + JSON.stringify(data) + '</div>';
            } catch(e) {
                resultsDiv.innerHTML = '<div class="error-message">Error: ' + e.message + '</div>';
            }
        }

        function displayResults(data, div) {
            if(data.success && data.result?.results) {
                let html = '';
                data.result.results.forEach(r => {
                    html += '<div class="hacker-card">';
                    html += '<div class="card-field"><span class="field-label">Name:</span><span class="field-value">' + (r.name || 'N/A') + '</span></div>';
                    html += '<div class="card-field"><span class="field-label">Mobile:</span><span class="field-value">' + (r.mobile || 'N/A') + '</span></div>';
                    html += '<div class="card-field"><span class="field-label">Address:</span><span class="field-value">' + (r.address || 'N/A') + '</span></div>';
                    html += '</div>';
                });
                div.innerHTML = html;
            } else {
                div.innerHTML = '<div class="error-message">No data found</div>';
            }
        }

        function displayRCResults(data, div) {
            if(data.error) {
                div.innerHTML = '<div class="error-message">RC not found</div>';
                return;
            }
            
            let html = '<div class="hacker-card">';
            const reg = data.data?.registration_identity_matrix;
            if(reg) {
                html += '<div class="card-field"><span class="field-label">Reg No:</span><span class="field-value">' + (reg.official_registration_id || 'N/A') + '</span></div>';
                html += '<div class="card-field"><span class="field-label">Owner:</span><span class="field-value">' + (reg.registered_owner_name || 'N/A') + '</span></div>';
                html += '<div class="card-field"><span class="field-label">Vehicle:</span><span class="field-value">' + (reg.vehicle_class || 'N/A') + '</span></div>';
            }
            html += '</div>';
            div.innerHTML = html;
        }
    </script>
</body>
</html>
