<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OpenSenseMap Dashboard</title>
 
    <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Styles -->
<style>
        body { padding: 20px; background-color: #f8f9fa; }
        #map { height: 300px; margin-bottom: 20px; }
        .card { margin-bottom: 20px; }
        #countdown { font-weight: bold; color: #dc3545; }
</style>
</head>
<body>
 
<?php include 'menu.php'; ?>
 
<div class="container">
<h1 class="mb-3">OpenSenseMap Dashboard</h1>
<p>Next refresh in: <span id="countdown">15</span> seconds</p>
<div id="sensorContent">
<!-- Content will be loaded here via AJAX -->
</div>
</div>
 
<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
<!-- JavaScript Logic -->
<script>
let countdown = 15;
let countdownElement = document.getElementById('countdown');
 
// Function to update countdown timer
function updateCountdown() {
    countdown--;
    if (countdown <= 0) {
        countdown = 15;
        fetchSensorData();
    }
    countdownElement.textContent = countdown;
}
 
// Load sensor data using AJAX
function fetchSensorData() {
    fetch('box.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('sensorContent').innerHTML = html;
            evalChartScripts();
            initMap();
        });
}
 
// Re-run Chart.js initialization
function evalChartScripts() {
    const scripts = document.querySelectorAll("script[data-chart='true']");
    scripts.forEach(script => {
        const newScript = document.createElement("script");
        newScript.text = script.text;
        document.body.appendChild(newScript);
        script.remove();
    });
}
 
// Re-initialize Leaflet map
function initMap() {
    const lat = parseFloat(document.getElementById('lat').textContent);
    const lng = parseFloat(document.getElementById('lng').textContent);
    const map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup("Sensor Location").openPopup();
}
 
// First load
fetchSensorData();
setInterval(updateCountdown, 1000);
</script>
 
</body>
</html>



