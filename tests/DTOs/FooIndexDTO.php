<?php

namespace JPNut\Tests\DTOs;

class FooIndexDTO
{
    /**
     * @var \JPNut\Tests\DTOs\FooDTO[]
     */
    public array $array_data;

    /**
     * @var iterable<\JPNut\Tests\DTOs\FooDTO>
     */
    public iterable $iterable_data;
}
