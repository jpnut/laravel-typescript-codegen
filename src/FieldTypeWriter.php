<?php

namespace JPNut\CodeGen;

class FieldTypeWriter
{
    /**
     * @var \JPNut\CodeGen\TypeRegistrar
     */
    private TypeRegistrar $registrar;

    /**
     * @param  \JPNut\CodeGen\TypeRegistrar  $registrar
     */
    public function __construct(TypeRegistrar $registrar)
    {
        $this->registrar = $registrar;
    }

    /**
     * @param  \JPNut\CodeGen\FieldType  $fieldType
     * @return string
     */
    public function write(FieldType $fieldType): string
    {
        return $this->qualifyIterable(
            $fieldType,
            $fieldType->isClass()
                ? $this->registrar->formatName($this->registrar->getType($fieldType->getSingular())->toReference())
                : $fieldType->getSingular());
    }

    /**
     * @param  \JPNut\CodeGen\FieldType  $fieldType
     * @param  string  $value
     * @return string
     */
    private function qualifyIterable(FieldType $fieldType, string $value): string
    {
        return str_replace(
            ['iterable<', '>'],
            ['Array<', '>'],
            str_replace($fieldType->getSingular(), $value, $fieldType->getType())
        );
    }
}
