<?php

namespace Aberdeener\MinecraftOauth;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Skin
{
    private UuidInterface $_id;

    private string $_state;

    private string $_url;

    private string $_variant;

    public function __construct(array $data)
    {
        $this->_id = Uuid::fromString($data['id']);
        $this->_state = $data['state'];
        $this->_url = $data['url'];
        $this->_variant = $data['variant'];
    }

    public function id(): UuidInterface
    {
        return $this->_id;
    }

    public function state(): string
    {
        return $this->_state;
    }

    public function url(): string
    {
        return $this->_url;
    }

    public function variant(): string
    {
        return $this->_variant;
    }
}
