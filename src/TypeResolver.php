<?php

namespace JPNut\CodeGen;

use Illuminate\Support\Str;
use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
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

    private ?ReflectionProperty $property = null;

    private ?ReflectionClass $class = null;

    /**
     * @param  \phpDocumentor\Reflection\DocBlockFactory  $docBlockReader
     */
    public function __construct(DocBlockFactory $docBlockReader)
    {
        $this->docBlockReader = $docBlockReader;
    }

    /**
     * @param  \ReflectionProperty  $property
     * @param  \ReflectionClass  $class
     * @return \JPNut\CodeGen\TypeDeclaration
     */
    public function fromReflectionProperty(ReflectionProperty $property, ReflectionClass $class): TypeDeclaration
    {
        /**
         * If there is no doc comment, or the doc comment does not contain the appropriate tag,
         * fallback to the type of the property.
         */
        if ($property->getDocComment() === false
            || empty($varTags = $this->docBlockReader->create($property->getDocComment())->getTagsByName('var'))
            || !(($tag = $varTags[0]) instanceof Var_)) {
            return $this->resolve(
                is_null($property->getType())
                    ? null
                    : $this->reflectionTypeToDefinition($property->getType()),
                $property,
                $class,
            );
        }

        return $this->resolve(
            (string) $tag->getType() ?? null,
            $property,
            $class,
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
            || empty($returnTags = $this->docBlockReader->create($method->getDocComment())->getTagsByName('return'))
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
     * @param  \ReflectionProperty|null  $property
     * @param  \ReflectionClass|null  $class
     * @return \JPNut\CodeGen\TypeDeclaration
     */
    public function resolve(
        ?string $definition = null,
        ?ReflectionProperty $property = null,
        ?ReflectionClass $class = null
    ): TypeDeclaration {
        $this->property = $property;
        $this->class = $class;

        $definition ??= '';

        $declaration = new TypeDeclaration($definition);

        $declaration->hasTypeDeclaration = $definition !== '';
        $declaration->isNullable = $this->resolveNullable($definition);
        $declaration->isMixed = $this->resolveIsMixed($definition);
        $declaration->isMixedArray = $this->resolveIsMixedArray($definition);
        $declaration->allowedTypes = $this->resolveAllowedTypes($definition);
        $declaration->allowedArrayTypes = $this->resolveAllowedArrayTypes($definition);

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
            if ($type->getType() === 'any[]') {
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
                    fn(string $type) => $this->mapStringToFieldType($type),
                    array_filter($types, fn(?string $type) => !is_null($type) && $type !== '')
                ),
                fn(FieldType $fieldType) => !is_null($fieldType->getType())
            )
        );
    }

    /**
     * @param  string  $type
     * @return \JPNut\CodeGen\FieldType
     */
    protected function mapStringToFieldType(string $type): FieldType
    {
        $type = $this->expandSelfAndStaticTypes(str_replace('?', '', $type));

        return new FieldType(self::$typeMapping[$type] ?? $type);
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
                    return;
                }

                if (strpos($type, '[]') !== false) {
                    return str_replace('[]', '', $type);
                }

                if (strpos($type, 'iterable<') !== false) {
                    return str_replace(['iterable<', '>'], ['', ''], $type);
                }
            },
            explode('|', $definition)
        ));
    }

    /**
     * @param  string  $type
     * @return string
     */
    protected function expandSelfAndStaticTypes(string $type): string
    {
        if ($type === 'self') {
            if (is_null($this->property)) {
                throw new InvalidArgumentException("Cannot use type 'self' without property reference");
            }

            return $this->property->getDeclaringClass()->getName();
        }

        if ($type === 'static') {
            if (is_null($this->class)) {
                throw new InvalidArgumentException("Cannot use type 'static' without class reference");
            }

            return $this->class->getName();
        }

        return $type;
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
