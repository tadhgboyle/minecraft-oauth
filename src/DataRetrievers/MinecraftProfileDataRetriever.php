<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use GuzzleHttp\Exception\GuzzleException;

class MinecraftProfileDataRetriever extends DataRetriever
{
    public function expectedResponseKeys(): array
    {
        return [
            'id',
            'name',
            'skins',
            'capes',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws ResponseValidationException
     */
    public function retrieve(string $minecraftAccessToken): array
    {
        $response = $this->client->get('https://api.minecraftservices.com/minecraft/profile', [
            'headers' => [
                'Authorization' => 'Bearer '.$minecraftAccessToken,
            ],
        ]);

        $responseJson = $this->parseJson($response);

        $this->validateResponseJson($responseJson);

        return $responseJson;
    }
}
