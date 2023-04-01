<?php

namespace Aberdeener\MinecraftOauth\DataRetrievers;

use Aberdeener\MinecraftOauth\Exceptions\ResponseValidationException;
use GuzzleHttp\Client;
use JsonException;
use Psr\Http\Message\ResponseInterface;

abstract class DataRetriever
{
    protected Client $client;

    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    /**
     * @throws ResponseValidationException
     */
    public function validateResponseJson(array $responseData): bool
    {
        $original_data = $responseData;

        foreach ($this->expectedResponseKeys() as $expected_key) {
            $data = $original_data;
            if (str_contains($expected_key, '.')) {
                $nested_keys = explode('.', $expected_key);
                foreach ($nested_keys as $nested_key) {
                    if (is_numeric($nested_key)) {
                        $nested_key = (int) $nested_key;
                    }
                    if (! isset($data[$nested_key])) {
                        throw new ResponseValidationException("Nested key $expected_key ($nested_key) not found");
                    }
                    $data = $data[$nested_key];
                }
            } elseif (! isset($data[$expected_key])) {
                throw new ResponseValidationException("Key $expected_key not found");
            }
        }

        return true;
    }

    abstract public function expectedResponseKeys(): array;

    /**
     * @throws ResponseValidationException
     */
    public function parseJson(ResponseInterface $response): array
    {
        try {
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ResponseValidationException('Invalid JSON body');
        }

        return $data;
    }
}
