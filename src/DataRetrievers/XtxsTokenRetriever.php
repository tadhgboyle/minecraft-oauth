<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use Aberdeener\MinecraftOauth\Exceptions\XtxsTokenRetrievalException;
use GuzzleHttp\Exception\GuzzleException;

class XtxsTokenRetriever extends DataRetriever
{
    public function expectedResponseKeys(): array
    {
        return [
            'IssueInstant',
            'NotAfter',
            'Token',
            'DisplayClaims',
            'DisplayClaims.xui',
            'DisplayClaims.xui.0.uhs',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws ResponseValidationException
     * @throws XtxsTokenRetrievalException
     */
    public function retrieve(
        string $xbl_token
    ): string {
        $response = $this->client->post('https://xsts.auth.xboxlive.com/xsts/authorize', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'Properties' => [
                    'SandboxId' => 'RETAIL',
                    'UserTokens' => [
                        $xbl_token,
                    ],
                ],
                'RelyingParty' => 'rp://api.minecraftservices.com/',
                'TokenType' => 'JWT',
            ],
        ]);

        $responseJson = $this->parseJson($response);

        if ($response->getStatusCode() === 401 && array_key_exists('XErr', $responseJson)) {
            throw XtxsTokenRetrievalException::withXErr($responseJson['XErr']);
        }

        $this->validateResponseJson($responseJson);

        return $responseJson['Token'];
    }
}
