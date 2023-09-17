let gameOver = false;
let mapIndex = score = roundScore = 0;
let mapillaryImageLocation, guessMarker, viewer, roundResultMap, guessMap, roundResultModal, gameOverModal;

window.onload = function () {
    let {Viewer} = mapillary;
    viewer = new Viewer({
        accessToken: accessToken,
        container: 'mapillary-container',
        component: {
            cover: false,
            bearing: false,
            direction: true,
            sequence: true,
            zoom: false,
        },
    });

    // Initialize Mapillary JS on first random place
    moveToNextImage();

    // Guess Map
    guessMap = L.map('guess-map').setView([0, -50], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(guessMap);
    guessMap.on('click', function (e) {
        const clickedCoords = e.latlng;
        if (guessMarker) {
            guessMap.removeLayer(guessMarker);
        }
        guessMarker = L.marker(clickedCoords).addTo(guessMap);
        document.getElementById('guess-button').removeAttribute('disabled');
    });
}

function moveToNextImage() {
    if (mapIndex < selectedPlaces.length) {
        if (guessMarker) {
            guessMap.removeLayer(guessMarker);
        }
        viewer.moveTo(selectedPlaces[mapIndex]['id']);
        mapillaryImageLocation = [selectedPlaces[mapIndex]['geometry']['coordinates'][1], selectedPlaces[mapIndex]['geometry']['coordinates'][0]];
    } else {
        gameOver = true;
        document.getElementById('finalScore').textContent = score;

        if (!gameOverModal) {
            let gameOverModal = new bootstrap.Modal(document.getElementById('gameOverModal'));
        }
        gameOverModal.show();
    }
}

function showRoundResultModal(guessLat, guessLng, imageLat, imageLng, roundScore, distance) {
    // Create or reset the map inside the modal
    if (!roundResultMap) {
        roundResultMap = L.map('roundResultMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(roundResultMap);
    } else {
        // If the map already exists, reset its view and clear its layers
        roundResultMap.setView([0, 0], 2);
        roundResultMap.eachLayer(function (layer) {
            roundResultMap.removeLayer(layer);
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(roundResultMap);
    }

    const greenIcon = L.icon({
        iconUrl: 'marker-green.png',
        iconSize: [25, 41],
        iconAnchor: [12.5, 41],
    });

    // Mark the correct and guessed locations
    L.marker([imageLat, imageLng], {icon: greenIcon}).addTo(roundResultMap);
    L.marker([guessLat, guessLng]).addTo(roundResultMap);

    // Draw a line between the correct and guessed locations
    const latlngs = [
        [guessLat, guessLng],
        [imageLat, imageLng]
    ];
    L.polyline(latlngs, {color: getColor(distance), dashArray: '10, 10'}).addTo(roundResultMap);

    // Adjust map view to show both markers and the line
    const group = new L.featureGroup([L.marker([imageLat, imageLng]), L.marker([guessLat, guessLng]), L.polyline(latlngs)]);
    const centerLat = (guessLat + imageLat) / 2;
    const centerLng = (guessLng + imageLng) / 2;
    roundResultMap.setView([centerLat, centerLng], 2);

    document.getElementById('roundScoreValue').textContent = roundScore;
    document.getElementById('totalScore').textContent = score;
    document.getElementById('distanceValue').textContent = distance.toFixed(2);

    // Show the modal
    if (!roundResultModal) {
        roundResultModal = new bootstrap.Modal(document.getElementById('roundResultModal'));
    }
    roundResultModal.show();

    // Wait for the modal to be shown, then resize the map to fit the modal
    setTimeout(() => window.dispatchEvent(new Event('resize')), 200);
}

function getColor(distance) {
    const maxDistance = 100; // You can adjust this value based on your preferences
    const percent = Math.min(distance / maxDistance, 1); // Ensure it's between 0 and 1
    const hue = ((1 - percent) * 120).toString(10); // 0 to 120 (green to red)
    return `hsl(${hue}, 100%, 50%)`; // Convert to HSL color format
}

// Function to calculate and display distance
function calculateDistance() {
    if (guessMarker && mapillaryImageLocation && !gameOver) {
        const guessLat = guessMarker.getLatLng().lat;
        const guessLng = guessMarker.getLatLng().lng;
        const imageLat = mapillaryImageLocation[0];
        const imageLng = mapillaryImageLocation[1];
        const distance = calculateHaversineDistance(guessLat, guessLng, imageLat, imageLng);

        roundScore = calculateRoundScore(distance);
        score += roundScore;
        mapIndex++;
        showRoundResultModal(guessLat, guessLng, imageLat, imageLng, roundScore, distance);
    }
}

// Haversine formula to calculate distance between two points on Earth
function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of Earth in kilometers
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Calculate score for each round, default max score is 5000
function calculateRoundScore(distance, scoreFactor = 5000) {
    if (distance * 1000 < 25) return 5000;

    const mapFactor = scoreFactor || 2000;
    const power = (distance * -1) / mapFactor;
    const score = 5000 * Math.pow(Math.E, power);

    if (score < 0) return 0;

    return Math.round(score);
}