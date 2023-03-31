<?php

namespace Aberdeener\MicrosoftMinecraftOauthProfile;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Cape {

    private UuidInterface $_id;
    private string $_state;
    private string $_url;
    private string $_alias;

    public function __construct(array $data) {
        $this->_id = Uuid::fromString($data['id']);
        $this->_state = $data['state'];
        $this->_url = $data['url'];
        $this->_alias = $data['alias'];
    }

    public function id(): UuidInterface {
        return $this->_id;
    }

    public function state(): string {
        return $this->_state;
    }

    public function url(): string {
        return $this->_url;
    }

    public function alias(): string {
        return $this->_alias;
    }
}
