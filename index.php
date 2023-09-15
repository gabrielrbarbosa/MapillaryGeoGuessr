<?php require 'mapillary.php'?>
<!DOCTYPE html>
<html>
<head>
    <title>Mapillary GeoGuessr</title>
    <link rel="stylesheet" href="https://unpkg.com/mapillary-js@4.1.0/dist/mapillary.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <link rel="stylesheet" href="style.css"/>
</head>
<body style="margin: 0;">
<div id="mapillary-container" style="width: 100%; height: 100vh;"></div>
<div id="guess-box">
    <div id="guess-map"></div>
    <button id="guess-button" onclick="calculateDistance()" disabled>GUESS</button>
    <p id="distance-display" style="display:none"></p>
</div>
<script src="https://unpkg.com/mapillary-js@4.1.0/dist/mapillary.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let selectedPlaces = <?=$selectedPlaces?>;
    let accessToken = <?=$accessToken?>;
</script>
<script src="game.js"></script>
</body>
</html>