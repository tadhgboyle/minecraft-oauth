<?php

namespace Aberdeener\MinecraftOauth;

use GuzzleHttp\Client as HttpClient;
use RuntimeException;

class MinecraftLinker {

    private HttpClient $client;
    private bool $throwException;

    public function __construct(HttpClient $client = null, bool $throwException = true)
    {
        $this->client = $client ?? new HttpClient();
        $this->throwException = $throwException;
    }

    public function fetchMinecraftProfile(
        string $clientId,
        string $clientCecret,
        string $code,
        string $redirectUri
    ): ?MinecraftProfile {
        $accessToken = $this->getAccessToken(
            $clientId,
            $clientCecret,
            $code,
            urlencode($redirectUri),
        );

        if (!$accessToken) {
            if ($this->throwException) {
                throw new RuntimeException('Failed to get access token');
            }

            return null;
        }

        [$xbl_token, $user_hash] = $this->authenticateWithXboxLive($accessToken);

        $xtxs_token = $this->getXtxsToken($xbl_token);

        $minecraft_access_token = $this->authenticateWithMinecraft($user_hash, $xtxs_token);

        if (!$this->checkGameOwnership($minecraft_access_token)) {
            if ($this->throwException) {
                throw new RuntimeException('User does not own Minecraft');
            }

            return null;
        }

        return new MinecraftProfile(
            $this->getMinecraftProfile($minecraft_access_token)
        );
    }

    private function getAccessToken($clientId, $clientSecret, $code, $redirectUri): ?string
    {
        $response = $this->client->post('https://login.live.com/oauth20_token.srf', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => "client_id=$clientId&client_secret=$clientSecret&code=$code&grant_type=authorization_code&redirect_uri=$redirectUri",
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['access_token'];
    }

    private function authenticateWithXboxLive(string $accessToken): array
    {
        $response = $this->client->post('https://user.auth.xboxlive.com/user/authenticate', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'Properties' => [
                    'AuthMethod' => 'RPS',
                    'SiteName' => 'user.auth.xboxlive.com',
                    'RpsTicket' => 'd=' . $accessToken,
                ],
                'RelyingParty' => 'http://auth.xboxlive.com',
                'TokenType' => 'JWT',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return [$data['Token'], $data['DisplayClaims']['xui'][0]['uhs']];
    }

    private function getXtxsToken(string $xbl_token): string
    {
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

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['Token'];
    }

    private function authenticateWithMinecraft(string $user_hash, string $xtxs_token): string
    {
        $response = $this->client->post('https://api.minecraftservices.com/authentication/login_with_xbox', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'identityToken' => 'XBL3.0 x=' . $user_hash . ';' . $xtxs_token,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['access_token'];
    }

    private function checkGameOwnership(string $minecraft_access_token): bool
    {
        $response = $this->client->get('https://api.minecraftservices.com/entitlements/mcstore', [
            'headers' => [
                'Authorization' => 'Bearer ' . $minecraft_access_token,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return count($data['items']) > 0;
    }

    private function getMinecraftProfile(string $minecraft_access_token): array
    {
        $response = $this->client->get('https://api.minecraftservices.com/minecraft/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $minecraft_access_token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
