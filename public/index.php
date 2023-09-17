<?php
require_once '../controllers/auth.php';
require_once '../controllers/mapillary.php';

$accessToken = getAccessToken();
$selectedPlaces = getRandomPlaces($accessToken);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Mapillary GeoGuessr</title>
    <link rel="stylesheet" href="https://unpkg.com/mapillary-js@4.1.0/dist/mapillary.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"/>
    <link rel="icon" href="marker-green.png" type="image/x-icon">
</head>
<body class="m-0">
<div id="mapillary-container" class="w-100 vh-100"></div>
<div id="guess-box" class="position-absolute bottom-0 end-0">
    <div id="guess-map" class="d-flex justify-content-end"></div>
    <button id="guess-button" class="btn btn-success position-absolute bottom-0 w-100 rounded-0" onclick="calculateDistance()">
        <i class="bi bi-crosshair me-1"></i> GUESS
    </button>

    <div class="modal fade" id="roundResultModal" tabindex="-1" aria-labelledby="roundResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roundResultModalLabel">Round Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="roundResultMap" style="width: 100%; height: 400px;"></div>
                    <div id="roundScore" class="mt-3 text-center">
                        <strong>Round Score:</strong> <span id="roundScoreValue">0</span>
                    </div>
                    <div id="guessedDistance" class="mt-2 text-center">
                        <strong>Distance:</strong> <span id="distanceValue">0</span> km
                    </div>
                    <div class="mt-3 text-center">
                        <strong>Total Score:</strong> <span id="totalScore">0</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="moveToNextImage()">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gameOverModal" tabindex="-1" aria-labelledby="gameOverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gameOverModalLabel">Game Over</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Your Score: <span id="finalScore">0</span></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="location.reload()">Finish</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/mapillary-js@4.1.0/dist/mapillary.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let selectedPlaces = JSON.parse('<?=$selectedPlaces?>');
    let accessToken = '<?=$accessToken?>';
</script>
<script src="game.js"></script>
</body>
</html>
