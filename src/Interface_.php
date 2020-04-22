<?php

namespace JPNut\CodeGen;

use JPNut\CodeGen\Contracts\Type;

class Interface_ implements Type
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var \JPNut\CodeGen\Field[]
     */
    private array $fields;

    /**
     * @param  string  $name
     * @param  array  $fields
     */
    public function __construct(string $name, array $fields = [])
    {
        $this->name   = $name;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function toReference(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param  \JPNut\CodeGen\Field  $field
     * @return \JPNut\CodeGen\Interface_
     */
    public function addField(Field $field): self
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }
}
