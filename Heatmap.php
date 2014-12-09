<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('GateFileReader.php');

$fileReader = new GateFileReader("/Users/rtaylor3/MainLibrary.csv",",");
$fileReader->readFile();

//print_r($fileReader->coordinatesString);

?>


<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Heatmaps</title>
<style>
    html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
    }
    #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
    }
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization"></script>
<script>
// Adding 500 Data Points
var map, pointarray, heatmap;

//var taxiData = [    new google.maps.LatLng(55.9400742, -3.171136),    new google.maps.LatLng(55.9483998, -3.1893704),    new google.maps.LatLng(55.9642883, -3.1948026),    new google.maps.LatLng(55.9386042, -3.20247),    new google.maps.LatLng(55.9373498, -3.1703814)];

var postcodeData = [<?php echo $fileReader->coordinatesString ?>];

function initialize() {
    var mapOptions = {
        zoom: 13,
        center: new google.maps.LatLng(55.9427536, -3.188781),
        mapTypeId: google.maps.MapTypeId.SATELLITE
    };

    map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

    var pointArray = new google.maps.MVCArray(postcodeData);

    Heatmap = new google.maps.visualization.HeatmapLayer({
        data: pointArray
    });

    Heatmap.setMap(map);
}

function toggleHeatmap() {
    Heatmap.setMap(Heatmap.getMap() ? null : map);
}

function changeGradient() {
    var gradient = [
        'rgba(0, 255, 255, 0)',
        'rgba(0, 255, 255, 1)',
        'rgba(0, 191, 255, 1)',
        'rgba(0, 127, 255, 1)',
        'rgba(0, 63, 255, 1)',
        'rgba(0, 0, 255, 1)',
        'rgba(0, 0, 223, 1)',
        'rgba(0, 0, 191, 1)',
        'rgba(0, 0, 159, 1)',
        'rgba(0, 0, 127, 1)',
        'rgba(63, 0, 91, 1)',
        'rgba(127, 0, 63, 1)',
        'rgba(191, 0, 31, 1)',
        'rgba(255, 0, 0, 1)'
    ]
    Heatmap.set('gradient', Heatmap.get('gradient') ? null : gradient);
}

function changeRadius() {
    Heatmap.set('radius', Heatmap.get('radius') ? null : 20);
}

function changeOpacity() {
    Heatmap.set('opacity', Heatmap.get('opacity') ? null : 0.2);
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>
</head>

<body>
<div id="panel">
    <button onclick="toggleHeatmap()">Toggle Heatmap</button>
    <button onclick="changeGradient()">Change gradient</button>
    <button onclick="changeRadius()">Change radius</button>
    <button onclick="changeOpacity()">Change opacity</button>
</div>
<div id="map-canvas"></div>
</body>
</html>


