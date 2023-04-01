<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use GuzzleHttp\Exception\GuzzleException;

class AccessTokenRetriever extends DataRetriever
{
    public function expectedResponseKeys(): array
    {
        return [
            'token_type',
            'expires_in',
            'scope',
            'access_token',
            'refresh_token',
            'user_id',
            'foci',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws ResponseValidationException
     */
    public function retrieve(
        string $clientId,
        string $clientSecret,
        string $code,
        string $redirectUri
    ): string {
        $response = $this->client->post('https://login.live.com/oauth20_token.srf', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => "client_id=$clientId&client_secret=$clientSecret&code=$code&grant_type=authorization_code&redirect_uri=$redirectUri",
        ]);

        $responseJson = $this->parseJson($response);

        $this->validateResponseJson($responseJson);

        return $responseJson['access_token'];
    }
}
