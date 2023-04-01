<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class DataRetrieverTest extends \PHPUnit\Framework\TestCase {

    public function test_parse_json(): void
    {
        // happy path
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '{"a": 1, "b": 2}'),
        ]))]);

        $dataRetriever = new NullRetriever($client, []);

        $this->assertEquals(['a' => 1, 'b' => 2], $dataRetriever->parseJson($client->get('')));

        // sad path
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '{"a": 1, "b":'),
        ]))]);

        $dataRetriever = new NullRetriever($client, []);

        $this->expectExceptionMessage('Invalid JSON body');
        $dataRetriever->parseJson($client->get(''));
    }

    public function test_validate_response_json(): void
    {
        $dataRetriever = new NullRetriever(null, []);
        $this->assertTrue($dataRetriever->validateResponseJson([]));

        $dataRetriever = new NullRetriever(null, ['a', 'b']);
        $this->assertTrue($dataRetriever->validateResponseJson(['a' => 1, 'b' => 2]));

        $dataRetriever = new NullRetriever(null, ['a', 'b', 'b.c', 'b.d']);
        $this->assertTrue($dataRetriever->validateResponseJson(['a' => 1, 'b' => ['c' => 2, 'd' => 3]]));

        $dataRetriever = new NullRetriever(null, ['a', 'b', 'b.0.c', 'b.0.d']);
        $this->assertTrue($dataRetriever->validateResponseJson(['a' => 1, 'b' => [['c' => 2, 'd' => 3]]]));
    }

    public function test_validate_response_json_throws_when_missing_key(): void
    {
        $dataRetriever = new NullRetriever(null, ['a', 'b']);
        $this->expectExceptionMessage('Key b not found');
        $dataRetriever->validateResponseJson(['a' => 1]);
    }

    public function test_validate_response_json_throws_when_missing_nested_key(): void
    {
        $dataRetriever = new NullRetriever(null, ['a', 'b', 'b.c', 'b.d']);
        $this->expectExceptionMessage('Nested key b.d (d) not found');
        $dataRetriever->validateResponseJson(['a' => 1, 'b' => ['c' => 2]]);
    }

    public function test_validate_response_json_throws_when_missing_numeric_key(): void
    {
        $dataRetriever = new NullRetriever(null, ['a', 'b', 'b.0.c', 'b.0.d']);
        $this->expectExceptionMessage('Nested key b.0.d (d) not found');
        $dataRetriever->validateResponseJson(['a' => 1, 'b' => [['c' => 2]]]);
    }
}
