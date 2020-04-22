<?php

namespace JPNut\Tests\DTOs;

use JsonSerializable;

class SerializableDTO implements JsonSerializable
{
    /**
     * @return string|integer
     */
    public function jsonSerialize()
    {
        return "foo";
    }
}
