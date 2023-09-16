<?php
require_once '../vendor/autoload.php';

function getRandomPlaces($numberOfPlaces = 5, $lastDays = 30)
{
    try {
        $client = new GuzzleHttp\Client();
        $last30Days = (new DateTime())->modify("-{$lastDays} days")->format('Y-m-d\TH:i:s\Z');
        $tomorrow = (new DateTime())->modify('+1 day')->format('Y-m-d\TH:i:s\Z');

        $url = "https://graph.mapillary.com/images?fields=id,geometry,camera_type,captured_at"
            . "&start_captured_at={$last30Days}&end_captured_at={$tomorrow}&limit=1000";
        $response = $client->get($url, [
            'headers' => ['Authorization' => 'Bearer ' . getAccessToken()]
        ]);

        $body = $response->getBody();
        $json = json_decode($body, true);
        $images = $json['data'];

        $panoramicImages = array_filter($images, function ($image) {
            return $image['camera_type'] === 'spherical';
        });

        $selectedPlaces = [];

        while (count($selectedPlaces) < $numberOfPlaces && !empty($panoramicImages)) {
            $randomIndex = array_rand($panoramicImages);
            $selectedPlaces[] = $panoramicImages[$randomIndex];
            unset($panoramicImages[$randomIndex]);
        }

        return json_encode($selectedPlaces);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
