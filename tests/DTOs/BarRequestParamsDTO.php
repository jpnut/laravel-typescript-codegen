<?php

namespace JPNut\Tests\DTOs;

class BarRequestParamsDTO
{
    public int $count;

    public string $filter;

    /**
     * @var string[]
     */
    public array $sort;
}
