<?php

namespace JPNut\CodeGen;

use JPNut\CodeGen\Contracts\Type;

class Literal implements Type
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var \JPNut\CodeGen\FieldType[]
     */
    private array $types;

    /**
     * @param  string  $name
     * @param  \JPNut\CodeGen\FieldType[]  $types
     */
    public function __construct(string $name, array $types)
    {
        $this->name  = $name;
        $this->types = $types;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function toReference(): string
    {
        return join(' | ', array_map(fn(FieldType $type) => $type->getType(), $this->types));
    }
}
