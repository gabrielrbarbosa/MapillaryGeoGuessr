<?php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];
$accessToken = $_ENV['ACCESS_TOKEN'];

$client = new GuzzleHttp\Client();
$queryParams = [
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => 'read',
    'state' => 'your_state',
];
if (empty($_GET['code']) && empty($accessToken)) {
    header('Location: https://www.mapillary.com/connect?' . http_build_query($queryParams));
} else if (empty($accessToken)){
    $authorizationCode = $_GET['code'];
    $tokenExchangeUrl = 'https://graph.mapillary.com/token';
    $tokenRequest = [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $authorizationCode,
        ],
        'headers' => [
            'Authorization' => 'OAuth ' . $clientSecret,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
    ];

    try {
        $response = $client->post($tokenExchangeUrl, $tokenRequest);
        $responseData = json_decode($response->getBody(), true);
        $accessToken = $responseData['access_token'];
        echo 'Access Token: ' . $accessToken;
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

try {
    $last30Days = (new DateTime())->modify('-30 days')->format('Y-m-d\TH:i:s\Z');
    $tomorrow = (new DateTime())->modify('+1 day')->format('Y-m-d\TH:i:s\Z');
    $response = $client->get("https://graph.mapillary.com/images?fields=id,geometry,camera_type,captured_at&start_captured_at={$last30Days}&end_captured_at={$tomorrow}&limit=1000", [
        'headers' => ['Authorization' => 'Bearer ' . $accessToken]
    ]);
    $body = $response->getBody();
    $json = json_decode($body, true);
    // echo "<pre>";
    // var_dump($json);

    $images = $json['data'];
    $panoramicImages = array_filter($images, function ($image) {
        return $image['camera_type'] === 'spherical';
    });

    $selectedPlaces = [];
    while (count($selectedPlaces) < 5 && !empty($panoramicImages)) {
        $randomIndex = array_rand($panoramicImages);
        $selectedPlaces[] = $panoramicImages[$randomIndex];
        unset($panoramicImages[$randomIndex]);
    }
    $selectedPlaces = json_encode($selectedPlaces);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}