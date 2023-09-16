<?php

use League\OAuth2\Client\Provider\GenericProvider;

class MapillaryProvider extends GenericProvider
{
    protected function getAccessTokenRequest(array $params)
    {
        $request = parent::getAccessTokenRequest($params);
        return $request->withHeader('Authorization', 'OAuth ' . $this->clientSecret);
    }
}