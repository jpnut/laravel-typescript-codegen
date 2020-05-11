<?php

namespace JPNut\CodeGen;

use ReflectionClass;
use ReflectionProperty;
use JsonSerializable;
use ReflectionMethod;
use Illuminate\Routing\Route;
use InvalidArgumentException;
use JPNut\CodeGen\Contracts\Type;
use JPNut\CodeGen\Contracts\CodeGenRequest;
use phpDocumentor\Reflection\DocBlockFactory;

class TypeRegistrar
{
    /**
     * @var \JPNut\CodeGen\Contracts\Type[]
     */
    protected array $types = [];

    /**
     * @var \JPNut\CodeGen\Method[]
     */
    protected array $methods = [];

    /**
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    protected DocBlockFactory $docBlockReader;

    /**
     * @var \JPNut\CodeGen\TypeResolver
     */
    protected TypeResolver $resolver;

    /**
     * @var string[]
     */
    protected array $request_properties;

    /**
     * @var array
     */
    private array $default_literals;

    /**
     * @param  \phpDocumentor\Reflection\DocBlockFactory  $docBlockReader
     * @param  array  $request_properties
     * @param  array  $default_literals
     */
    public function __construct(DocBlockFactory $docBlockReader, array $request_properties, array $default_literals)
    {
        $this->docBlockReader = $docBlockReader;
        $this->resolver = new TypeResolver($docBlockReader);
        $this->request_properties = $this->parseRequestProperties($request_properties);
        $this->default_literals = $default_literals;

        $this->addDefaultTypes();
    }

    /**
     * @param  \JPNut\CodeGen\Contracts\Type  $type
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    public function addType(Type $type): self
    {
        $this->types[$type->name()] = $type;

        return $this;
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  \ReflectionMethod  $reflectionMethod
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    public function registerMethod(Route $route, ReflectionMethod $reflectionMethod): self
    {
        return $this->generateMethodTypes($method = $this->createMethod($route, $reflectionMethod))
            ->addMethod($method);
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    public function addMethod(Method $method): self
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @param  string  $name
     * @return string
     */
    public function generateTypesFromClass(string $name): string
    {
        if (! class_exists($name)) {
            throw new InvalidArgumentException("Cannot generate types for class '{$name}': Class does not exist.");
        }

        $class = new ReflectionClass($name);

        if ($this->has($name = $class->getName())) {
            return $name;
        }

        if ($this->classIsSerializable($class)) {
            $methodDeclaration = $this->resolver->fromReflectionMethod(
                $class->getMethod('jsonSerialize'),
            );

            $this->addType(new Literal($name, $methodDeclaration->allowedTypes));

            return $name;
        }

        /**
         * Need to add the type now to ensure nested references to the same object do not cause infinite loops.
         */
        $this->addType($interface = new Interface_($name));

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($this->shouldIgnoreProperty($property)) {
                continue;
            }

            $propertyDeclaration = $this->resolver->fromReflectionProperty($property);

            $interface->addField(
                new Field(
                    $property->getName(),
                    $propertyDeclaration->allowedTypes,
                    $propertyDeclaration->isNullable
                )
            );

            $this->generateDeclarationSubTypes($propertyDeclaration, $name);
        }

        return $name;
    }

    /**
     * @param  string|null  $name
     * @return bool
     */
    public function has(?string $name): bool
    {
        return ! is_null($name) && isset($this->types[$name]);
    }

    /**
     * @param  \ReflectionMethod  $method
     * @return \JPNut\CodeGen\FieldType|null
     */
    public function getMethodRequestType(ReflectionMethod $method): ?FieldType
    {
        foreach ($method->getParameters() as $parameter) {
            if (class_exists($requestType = $parameter->getType()->getName()) && $this->isCodeGenRequest($requestType)) {
                return $this->createFieldType($requestType);
            }
        }

        return null;
    }

    /**
     * @param  \ReflectionMethod  $reflectionMethod
     * @return \JPNut\CodeGen\FieldType[]
     */
    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        return $this->resolver->fromReflectionMethod($reflectionMethod)->allowedTypes;
    }

    /**
     * @return \JPNut\CodeGen\Contracts\Type[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param  string  $name
     * @return \JPNut\CodeGen\Contracts\Type
     */
    public function getType(string $name): Type
    {
        if (class_exists($name)) {
            $name = (new ReflectionClass($name))->getName();
        }

        if (! $this->has($name)) {
            throw new InvalidArgumentException("Type with name {$name} not found.");
        }

        return $this->types[$name];
    }

    /**
     * @return \JPNut\CodeGen\Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param  string  $name
     * @return string
     */
    public function formatName(string $name): string
    {
        return str_replace('\\', '', $name);
    }

    /**
     * @return array
     */
    public function getRequestProperties(): array
    {
        return $this->request_properties;
    }

    /**
     * @param  string  $field
     * @return string
     */
    public function requestPropertyValue(string $field): string
    {
        if (! isset($this->request_properties[$field])) {
            throw new InvalidArgumentException("Request Field not found: {$field}");
        }

        return $this->request_properties[$field];
    }

    /**
     * @param  \JPNut\CodeGen\TypeDeclaration  $declaration
     * @param  string  $name
     */
    protected function generateDeclarationSubTypes(TypeDeclaration $declaration, string $name): void
    {
        foreach ($declaration->allowedTypes as $allowedType) {
            if ($allowedType->isClass() && (($class = $allowedType->getSingular()) !== $name)) {
                $this->generateTypesFromClass($class);
            }
        }

        foreach ($declaration->allowedArrayTypes as $allowedArrayType) {
            if ($allowedArrayType->isClass() && (($class = $allowedArrayType->getSingular()) !== $name)) {
                $this->generateTypesFromClass($class);
            }
        }
    }

    /**
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    protected function addDefaultTypes(): self
    {
        foreach ($this->default_literals as $name => $types) {
            $this->addType(new Literal(
                $name,
                array_map(fn (string $type) => new FieldType($type), $types)
            ));
        }

        return $this;
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    protected function generateMethodTypes(Method $method): self
    {
        return $this->addMethodRequestType($method)
            ->addMethodReturnTypes($method);
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    protected function addMethodReturnTypes(Method $method): self
    {
        foreach ($method->getReturnTypes() as $returnType) {
            if (! $returnType->isClass()) {
                continue;
            }

            $this->generateTypesFromClass($returnType->getSingular());
        }

        return $this;
    }

    /**
     * @param  \ReflectionClass  $class
     * @return bool
     */
    protected function classIsSerializable(ReflectionClass $class): bool
    {
        return isset(class_implements($class->getName())[JsonSerializable::class]);
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return \JPNut\CodeGen\TypeRegistrar
     */
    protected function addMethodRequestType(Method $method): self
    {
        if (! $method->hasRequestType() || $this->has($className = $method->getRequestType()->getSingular())) {
            return $this;
        }

        $this->addType(
            new Interface_(
                $className,
                $this->getRequestMethodFields(new ReflectionClass($className))
            )
        );

        return $this;
    }

    /**
     * @param  \ReflectionClass  $class
     * @return \JPNut\CodeGen\Field[]
     */
    protected function getRequestMethodFields(ReflectionClass $class): array
    {
        $fields = [];

        foreach ($class->getMethods() as $method) {
            if ($method->isPublic()
                && ! is_null($field = $this->requestMethodCodeGenType($method))
                && isset($this->request_properties[$field])) {
                $fields[$field] = new Field(
                    $field,
                    ($methodDeclaration = $this->resolver->fromReflectionMethod($method))->allowedTypes,
                    $methodDeclaration->isNullable,
                );

                $this->generateDeclarationSubTypes($methodDeclaration, $class->getName());
            }
        }

        return $fields;
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  \ReflectionMethod  $reflectionMethod
     * @return \JPNut\CodeGen\Method
     */
    protected function createMethod(Route $route, ReflectionMethod $reflectionMethod): Method
    {
        return new Method(
            $route,
            $reflectionMethod,
            $this->getMethodRequestType($reflectionMethod),
            $this->getMethodReturnTypes($reflectionMethod),
        );
    }

    /**
     * @param  string  $requestType
     * @return bool
     */
    protected function isCodeGenRequest(string $requestType): bool
    {
        return isset(class_implements($requestType)[CodeGenRequest::class]);
    }

    /**
     * @param  string  $type
     * @return \JPNut\CodeGen\FieldType
     */
    protected function createFieldType(string $type): FieldType
    {
        return new FieldType($type);
    }

    /**
     * @param  \ReflectionMethod  $method
     * @return string|null
     */
    protected function requestMethodCodeGenType(ReflectionMethod $method): ?string
    {
        if ($method->getDocComment() === false
            || empty($codeGenTags = $this->docBlockReader->create($method->getDocComment())->getTagsByName('code-gen-property'))
            || ! (($tag = $codeGenTags[0]) instanceof CodeGenPropertyTag)) {
            return null;
        }

        return (string) $tag->getDescription();
    }

    /**
     * @param  array  $request_properties
     * @return string[]
     */
    protected function parseRequestProperties(array $request_properties): array
    {
        $properties = [];

        foreach ($request_properties as $key => $property) {
            if (! is_string($property)) {
                throw new InvalidArgumentException('Request Property value must be a string');
            }

            if (is_numeric($key)) {
                $properties[$property] = $property;

                continue;
            }

            $properties[$key] = $property;
        }

        return $properties;
    }

    /**
     * @param  \ReflectionProperty  $property
     * @return bool
     */
    protected function shouldIgnoreProperty(ReflectionProperty $property): bool
    {
        if ($property->getDocComment() === false
            || empty($codeGenTags = $this->docBlockReader->create($property->getDocComment())->getTagsByName('code-gen-ignore'))
            || ! (($tag = $codeGenTags[0]) instanceof CodeGenIgnoreTag)) {
            return false;
        }

        return true;
    }
}
