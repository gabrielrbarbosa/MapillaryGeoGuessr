<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../providers/MapillaryProvider.php';

use Dotenv\Dotenv;

if (isset($_GET['code'])) {
    echo getAccessToken($_GET['code'], $_GET['state']);
}

function getAccessToken($code = null, $state = null)
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    $accessToken = $_ENV['MAPILLARY_ACCESS_TOKEN'];

    if (!empty($accessToken)) {
        return $accessToken;
    }

    $provider = new MapillaryProvider([
        'clientId' => $_ENV['MAPILLARY_CLIENT_ID'],
        'clientSecret' => $_ENV['MAPILLARY_CLIENT_SECRET'],
        'redirectUri' => $_ENV['MAPILLARY_REDIRECT_URI'],
        'urlAuthorize' => 'https://www.mapillary.com/connect',
        'urlAccessToken' => 'https://graph.mapillary.com/token',
        'urlResourceOwnerDetails' => 'https://graph.mapillary.com/me'
    ]);

    if (empty($code) && empty($accessToken)) {
        $authorizationUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authorizationUrl);
        exit;
    } elseif (empty($accessToken)) {
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [ 'code' => $code ]);
            return $accessToken->getToken(); // TODO: save in db
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    return false;
}
