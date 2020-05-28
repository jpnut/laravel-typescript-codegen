<?php

namespace JPNut\CodeGen\Tests\DTOs;

use JsonSerializable;

class SerializableDTO implements JsonSerializable
{
    /**
     * @return string|int
     */
    public function jsonSerialize()
    {
        return 'foo';
    }
}
