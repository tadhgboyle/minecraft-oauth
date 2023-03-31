<?php

namespace Aberdeener\MinecraftOauthProfile;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MinecraftProfile {

    private UuidInterface $_uuid;
    private string $_username;
    private array $_skins;
    private array $_capes;

    public function __construct(array $data) {
        $this->_uuid = Uuid::fromString($data['id']);
        $this->_username = $data['name'];
        $this->_skins = array_map(static fn($data) => new Skin($data), $data['skins']);
        $this->_capes = array_map(static fn($data) => new Cape($data), $data['capes']);
    }

    public function uuid(): UuidInterface {
        return $this->_uuid;
    }

    public function username(): string {
        return $this->_username;
    }

    /**
     * @return Skin[]
     */
    public function skins(): array {
        return $this->_skins;
    }

    /**
     * @return Cape[]
     */
    public function capes(): array {
        return $this->_capes;
    }
}
