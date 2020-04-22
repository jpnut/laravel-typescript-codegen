<?php

namespace JPNut\CodeGen;

class Field
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
     * @var bool
     */
    private bool $nullable;

    /**
     * @param  string  $name
     * @param  \JPNut\CodeGen\FieldType[]  $types
     * @param  bool  $nullable
     */
    public function __construct(string $name, array $types, bool $nullable)
    {
        $this->name = $name;
        $this->types = $types;
        $this->nullable = $nullable;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \JPNut\CodeGen\FieldType[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return bool
     */
    public function nullable(): bool
    {
        return $this->nullable;
    }
}
