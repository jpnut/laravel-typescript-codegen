<?php

namespace JPNut\CodeGen;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

class TypeResolver
{
    /**
     * @var array
     */
    protected static array $typeMapping = [
        'int'   => 'integer',
        'bool'  => 'boolean',
        'float' => 'double',
        'void'  => 'null',
    ];

    /**
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    private DocBlockFactory $docBlockReader;

    /**
     * @param  \phpDocumentor\Reflection\DocBlockFactory  $docBlockReader
     */
    public function __construct(DocBlockFactory $docBlockReader)
    {
        $this->docBlockReader = $docBlockReader;
    }

    /**
     * @param  \ReflectionProperty  $property
     * @return \JPNut\CodeGen\TypeDeclaration
     */
    public function fromReflectionProperty(ReflectionProperty $property): TypeDeclaration
    {
        /**
         * If there is no doc comment, or the doc comment does not contain the appropriate tag,
         * fallback to the type of the property.
         */
        if ($property->getDocComment() === false
            || empty ($varTags = $this->docBlockReader->create($property->getDocComment())->getTagsByName('var'))
            || !(($tag = $varTags[0]) instanceof Var_)) {
            return $this->resolve(
                is_null($property->getType())
                    ? null
                    : $this->reflectionTypeToDefinition($property->getType())
            );
        }

        return $this->resolve(
            (string) $tag->getType() ?? null
        );
    }

    /**
     * @param  \ReflectionMethod  $method
     * @return \JPNut\CodeGen\TypeDeclaration
     */
    public function fromReflectionMethod(ReflectionMethod $method): TypeDeclaration
    {
        /**
         * If there is no doc comment, or the doc comment does not contain the appropriate tag,
         * fallback to the return type of the method.
         */
        if ($method->getDocComment() === false
            || empty ($returnTags = $this->docBlockReader->create($method->getDocComment())->getTagsByName('return'))
            || !(($tag = $returnTags[0]) instanceof Return_)) {
            return $this->resolve(
                is_null($method->getReturnType())
                    ? null
                    : $this->reflectionTypeToDefinition($method->getReturnType())
            );
        }

        return $this->resolve(
            (string) $tag->getType() ?? null
        );
    }

    /**
     * @param  string|null  $definition
     * @return \JPNut\CodeGen\TypeDeclaration
     */
    public function resolve(?string $definition = null): TypeDeclaration
    {
        $definition ??= '';

        $declaration = new TypeDeclaration($definition);

        $declaration->hasTypeDeclaration = $definition !== '';
        $declaration->isNullable         = $this->resolveNullable($definition);
        $declaration->isMixed            = $this->resolveIsMixed($definition);
        $declaration->isMixedArray       = $this->resolveIsMixedArray($definition);
        $declaration->allowedTypes       = $this->resolveAllowedTypes($definition);
        $declaration->allowedArrayTypes  = $this->resolveAllowedArrayTypes($definition);

        return $declaration;
    }

    /**
     * @param  string  $definition
     * @return bool
     */
    protected function resolveNullable(string $definition): bool
    {
        if (!$definition) {
            return true;
        }

        if (Str::contains($definition, ['mixed', 'null', '?'])) {
            return true;
        }

        return false;
    }

    /**
     * @param  string  $definition
     * @return bool
     */
    protected function resolveIsMixed(string $definition): bool
    {
        return Str::contains($definition, ['mixed']);
    }

    /**
     * @param  string  $definition
     * @return bool
     */
    protected function resolveIsMixedArray(string $definition): bool
    {
        $types = $this->normaliseTypes(...explode('|', $definition));

        foreach ($types as $type) {
            if ($type->getType() === "any[]") {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string|null  ...$types
     * @return \JPNut\CodeGen\FieldType[]
     */
    protected function normaliseTypes(?string ...$types): array
    {
        return array_values(
            array_filter(
                array_map(
                    fn(string $type) => new FieldType(str_replace('?', '', self::$typeMapping[$type] ?? $type)),
                    array_filter($types, fn(?string $type) => !is_null($type) && $type !== '')
                ),
                fn(FieldType $fieldType) => !is_null($fieldType->getType())
            )
        );
    }

    /**
     * @param  string  $definition
     * @return \JPNut\CodeGen\FieldType[]
     */
    protected function resolveAllowedTypes(string $definition): array
    {
        return $this->normaliseTypes(...explode('|', $definition));
    }

    /**
     * @param  string  $definition
     * @return \JPNut\CodeGen\FieldType[]
     */
    protected function resolveAllowedArrayTypes(string $definition): array
    {
        return $this->normaliseTypes(...array_map(
            function (string $type) {
                if (!$type) {
                    return null;
                }

                if (strpos($type, '[]') !== false) {
                    return str_replace('[]', '', $type);
                }

                if (strpos($type, 'iterable<') !== false) {
                    return str_replace(['iterable<', '>'], ['', ''], $type);
                }

                return null;
            },
            explode('|', $definition)
        ));
    }

    /**
     * @param  \ReflectionType  $type
     * @return string|null
     */
    private function reflectionTypeToDefinition(?ReflectionType $type): ?string
    {
        if (is_null($type)) {
            return null;
        }

        if ($type->allowsNull()) {
            return "?{$type->getName()}";
        }

        return $type->getName();
    }
}
