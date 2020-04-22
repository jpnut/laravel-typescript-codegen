<?php

namespace JPNut\CodeGen;

class TypeDeclaration
{
    /**
     * @var bool
     */
    public bool $isNullable = false;

    /**
     * @var bool
     */
    public bool $isMixedArray = false;

    /**
     * @var \JPNut\CodeGen\FieldType[]
     */
    public array $allowedTypes = [];

    /**
     * @var \JPNut\CodeGen\FieldType[]
     */
    public array $allowedArrayTypes = [];

    /**
     * @var bool
     */
    public bool $hasTypeDeclaration;

    /**
     * @var bool
     */
    public bool $isMixed;

    /**
     * @var string
     */
    private string $definition;

    /**
     * @param  string  $definition
     */
    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }
}
