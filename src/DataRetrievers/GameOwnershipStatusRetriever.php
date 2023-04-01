<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\GameOwnershipCheckException;
use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use GuzzleHttp\Exception\GuzzleException;

class GameOwnershipStatusRetriever extends DataRetriever
{
    public function expectedResponseKeys(): array
    {
        return [
            'items',
            'signature',
            'keyId',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws ResponseValidationException
     * @throws GameOwnershipCheckException
     */
    public function check(string $minecraftAccessToken): void
    {
        $response = $this->client->get('https://api.minecraftservices.com/entitlements/mcstore', [
            'headers' => [
                'Authorization' => 'Bearer '.$minecraftAccessToken,
            ],
        ]);

        $responseJson = $this->parseJson($response);

        $this->validateResponseJson($responseJson);

        if (count($responseJson['items']) === 0) {
            throw new GameOwnershipCheckException();
        }
    }
}
