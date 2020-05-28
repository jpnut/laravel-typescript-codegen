<?php

namespace JPNut\CodeGen\Tests\DTOs;

class FooIndexDTO
{
    /**
     * @var \JPNut\CodeGen\Tests\DTOs\FooDTO[]
     */
    public array $array_data;

    /**
     * @var iterable<\JPNut\CodeGen\Tests\DTOs\FooDTO>
     */
    public iterable $iterable_data;
}
