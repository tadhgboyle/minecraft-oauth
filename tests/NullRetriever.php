<?php

namespace Tests;

use Aberdeener\MinecraftOauth\DataRetrievers\DataRetriever;
use GuzzleHttp\Client;

class NullRetriever extends DataRetriever {

    private array $expectedKeys;

    public function __construct(?Client $client = null, array $expectedKeys = []) {
        parent::__construct($client ?? new Client());

        $this->expectedKeys = $expectedKeys;
    }

    public function expectedResponseKeys(): array {
        return $this->expectedKeys;
    }
}
