<?php

namespace JPNut\CodeGen;

use Illuminate\Routing\Route;
use ReflectionMethod;

class Method
{
    /**
     * @var \Illuminate\Routing\Route
     */
    private Route $route;

    /**
     * @var \ReflectionMethod
     */
    private ReflectionMethod $reflectionMethod;

    /**
     * @var \JPNut\CodeGen\FieldType|null
     */
    private ?FieldType $request_type;

    /**
     * @var \JPNut\CodeGen\FieldType[]
     */
    private array $return_types;

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  \ReflectionMethod  $reflectionMethod
     * @param  \JPNut\CodeGen\FieldType|null  $request_type
     * @param  \JPNut\CodeGen\FieldType[]  $return_types
     */
    public function __construct(
        Route $route,
        ReflectionMethod $reflectionMethod,
        ?FieldType $request_type = null,
        array $return_types = []
    ) {
        $this->route            = $route;
        $this->reflectionMethod = $reflectionMethod;
        $this->request_type     = $request_type;
        $this->return_types     = $return_types;
    }

    /**
     * @return bool
     */
    public function hasRequestType(): bool
    {
        return !is_null($this->request_type);
    }

    /**
     * @return \JPNut\CodeGen\FieldType|null
     */
    public function getRequestType(): ?FieldType
    {
        return $this->request_type;
    }

    /**
     * @return \Illuminate\Routing\Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return \JPNut\CodeGen\FieldType[]
     */
    public function getReturnTypes(): array
    {
        return $this->return_types;
    }
}
