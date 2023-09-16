<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use League\OAuth2\Client\Provider\GenericProvider;

function getAccessToken()
{
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $accessToken = $_ENV['ACCESS_TOKEN']; // TODO: save in db

    $provider = new GenericProvider([
        'clientId' => $_ENV['CLIENT_ID'],
        'clientSecret' => $_ENV['CLIENT_SECRET'],
        'redirectUri' => $_ENV['REDIRECT_URI'],
        'urlAuthorize' => 'https://www.mapillary.com/connect',
        'urlAccessToken' => 'https://graph.mapillary.com/token',
        'urlResourceOwnerDetails' => 'https://graph.mapillary.com/me'
    ]);

    if (empty($_GET['code']) && empty($accessToken)) {
        $authorizationUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authorizationUrl);
        exit;
    } elseif (empty($accessToken)) {
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
            return $accessToken->getToken();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    return false;
}
