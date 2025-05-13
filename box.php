<?php
$boxId = "6762cff70d72520008a6bb2a";
$apiBox = "https://api.opensensemap.org/boxes/$boxId";
$boxData = json_decode(file_get_contents($apiBox), true);
 
if (!$boxData) {
    echo "<div class='alert alert-danger'>Failed to fetch data.</div>";
    return;
}
 
$coords = $boxData['loc'][0]['geometry']['coordinates'];
$lat = $coords[1];
$lng = $coords[0];
 
echo "<div class='row mb-3'>
<div class='col-md-6'>
<h3>{$boxData['name']}</h3>
<p><strong>Location:</strong> Latitude: <span id='lat'>{$lat}</span>, Longitude: <span id='lng'>{$lng}</span></p>
</div>
<div class='col-md-6'>
<div id='map'></div>
</div>
</div>";
 
echo "<div class='row'>";
$chartScripts = "";
 
foreach ($boxData['sensors'] as $sensor) {
    $title = htmlspecialchars($sensor['title']);
    $unit = htmlspecialchars($sensor['unit']);
    $sensorId = $sensor['_id'];
    $lastValue = isset($sensor['lastMeasurement']) ? $sensor['lastMeasurement']['value'] : 'N/A';
    $lastTime = isset($sensor['lastMeasurement']) ? $sensor['lastMeasurement']['createdAt'] : 'No data';
 
    // Fetch latest 10 data points
    $apiData = "https://api.opensensemap.org/boxes/$boxId/data/$sensorId?quantity=10";
    $measurements = json_decode(file_get_contents($apiData), true);
 
    $values = [];
    $labels = [];
    if (is_array($measurements)) {
        foreach ($measurements as $m) {
            $values[] = floatval($m['value']);
            $labels[] = date('H:i:s', strtotime($m['createdAt']));
        }
    }
 
    echo "<div class='col-md-6'>
<div class='card'>
<div class='card-body'>
<h5 class='card-title'>$title</h5>
<h6 class='card-subtitle mb-2 text-muted'>Last: $lastValue $unit</h6>
<canvas id='chart_$sensorId' height='150'></canvas>
</div>
</div>
</div>";
 
    $chartScripts .= "<script data-chart='true'>
        new Chart(document.getElementById('chart_$sensorId'), {
            type: 'line',
            data: {
                labels: " . json_encode($labels) . ",
                datasets: [{
                    label: '$title',
                    data: " . json_encode($values) . ",
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } }
            }
        });
</script>";
}
echo "</div>";
echo $chartScripts;
?>





