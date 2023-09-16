window.onload = function () {
    let gameOver = false;
    let timer = 10000;
    let mapIndex, score, roundScore = 0;
    let mapillaryImageLocation, guessMarker;
    let {Viewer} = mapillary;
    let viewer = new Viewer({
        accessToken: accessToken,
        container: 'mapillary-container',
        component: {
            cover: false
        }
    });

    // Initialize Mapillary JS on first random place
    moveToNextImage();

    // Guess Map
    const guessMap = L.map('guess-map').setView([0, -50], 2);
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
        viewer.moveTo(selectedPlaces[mapIndex]['id']);
        mapillaryImageLocation = [selectedPlaces[mapIndex]['geometry']['coordinates'][1], selectedPlaces[mapIndex]['geometry']['coordinates'][0]];
    } else {
        gameOver = true;
        alert('Game Over! Score: ' + score);
    }
}

// Function to calculate and display distance
function calculateDistance() {
    if (guessMarker && mapillaryImageLocation && !gameOver) {
        const guessLat = guessMarker.getLatLng().lat;
        const guessLng = guessMarker.getLatLng().lng;
        const imageLat = mapillaryImageLocation[0];
        const imageLng = mapillaryImageLocation[1];
        const distance = calculateHaversineDistance(guessLat, guessLng, imageLat, imageLng);
        document.getElementById('distance-display').innerHTML = `Distance: ${distance.toFixed(2)} km`;

        roundScore = calculateRoundScore(distance);
        alert(`Distance: ${distance.toFixed(2)} km Score: ${roundScore}`);
        score += roundScore;
        mapIndex++;
        moveToNextImage();
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