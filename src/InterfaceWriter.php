<?php

namespace JPNut\CodeGen;

class InterfaceWriter
{
    /**
     * @var \JPNut\CodeGen\TypeRegistrar
     */
    protected TypeRegistrar $registrar;

    /**
     * @var \JPNut\CodeGen\FieldTypeWriter
     */
    protected FieldTypeWriter $fieldTypeWriter;

    /**
     * @param  \JPNut\CodeGen\TypeRegistrar  $registrar
     * @param  \JPNut\CodeGen\FieldTypeWriter  $fieldTypeWriter
     */
    public function __construct(TypeRegistrar $registrar, FieldTypeWriter $fieldTypeWriter)
    {
        $this->registrar = $registrar;
        $this->fieldTypeWriter = $fieldTypeWriter;
    }

    /**
     * @param  \JPNut\CodeGen\Interface_  $interface
     * @return string
     */
    public function write(Interface_ $interface): string
    {
        return implode("\n", [
            "export interface {$this->registrar->formatName($interface->name())} {",
            ...$this->mapFieldsToDefinitions($interface),
            '}',
        ]);
    }

    /**
     * @param  \JPNut\CodeGen\Interface_  $interface
     * @return string[]
     */
    protected function mapFieldsToDefinitions(Interface_ $interface): array
    {
        return array_map(
            fn (Field $field) => $this->getFieldDefinition($field),
            array_values($interface->getFields())
        );
    }

    /**
     * @param  \JPNut\CodeGen\Field  $field
     * @return string
     */
    protected function getFieldDefinition(Field $field): string
    {
        return "  {$this->getFieldName($field)}: {$this->getCombinedFieldTypes($field)};";
    }

    /**
     * @param  \JPNut\CodeGen\Field  $field
     * @return string
     */
    protected function getFieldName(Field $field): string
    {
        return $field->getName().($field->nullable() ? '?' : '');
    }

    /**
     * @param  \JPNut\CodeGen\Field  $field
     * @return string
     */
    protected function getCombinedFieldTypes(Field $field): string
    {
        return empty($field->getTypes())
            ? 'any'
            : implode(' | ', $this->getFieldTypes($field));
    }

    /**
     * @param  \JPNut\CodeGen\Field  $field
     * @return string[]
     */
    protected function getFieldTypes(Field $field): array
    {
        return array_unique(
            array_map(
                fn (FieldType $type) => $this->fieldTypeWriter->write($type),
                $field->nullable() ? $this->addNullableType($field->getTypes()) : $field->getTypes()
            )
        );
    }

    /**
     * @param  \JPNut\CodeGen\FieldType[]  $getTypes
     * @return \JPNut\CodeGen\FieldType[]
     */
    protected function addNullableType(array $getTypes): array
    {
        return array_merge(
            $getTypes,
            [FieldType::nullable()]
        );
    }
}
