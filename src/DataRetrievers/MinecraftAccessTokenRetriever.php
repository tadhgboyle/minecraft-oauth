<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use GuzzleHttp\Exception\GuzzleException;

class MinecraftAccessTokenRetriever extends DataRetriever
{
    public function expectedResponseKeys(): array
    {
        return [
            'username',
            'roles',
            'access_token',
            'token_type',
            'expires_in',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws ResponseValidationException
     */
    public function retrieve(
        string $userHash,
        string $xtxsToken
    ): string {
        $response = $this->client->post('https://api.minecraftservices.com/authentication/login_with_xbox', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'identityToken' => 'XBL3.0 x='.$userHash.';'.$xtxsToken,
            ],
        ]);

        $responseJson = $this->parseJson($response);

        $this->validateResponseJson($responseJson);

        return $responseJson['access_token'];
    }
}
