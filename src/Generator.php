<?php

namespace JPNut\CodeGen;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;

class Generator
{
    /**
     * @var \Illuminate\Routing\Router
     */
    private Router $router;

    /**
     * @var \JPNut\CodeGen\TypeRegistrar
     */
    private TypeRegistrar $registrar;

    /**
     * @var \JPNut\CodeGen\InterfaceWriter
     */
    private InterfaceWriter $interfaceWriter;

    /**
     * @var \JPNut\CodeGen\MethodWriter
     */
    private MethodWriter $methodWriter;

    /**
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    private DocBlockFactory $docBlockReader;

    /**
     * @param  \Illuminate\Routing\Router  $router
     * @param  \JPNut\CodeGen\TypeRegistrar  $registrar
     * @param  \JPNut\CodeGen\InterfaceWriter  $interfaceWriter
     * @param  \JPNut\CodeGen\MethodWriter  $methodWriter
     * @param  \phpDocumentor\Reflection\DocBlockFactory  $docBlockReader
     */
    public function __construct(
        Router $router,
        TypeRegistrar $registrar,
        InterfaceWriter $interfaceWriter,
        MethodWriter $methodWriter,
        DocBlockFactory $docBlockReader
    ) {
        $this->router          = $router;
        $this->registrar       = $registrar;
        $this->interfaceWriter = $interfaceWriter;
        $this->methodWriter    = $methodWriter;
        $this->docBlockReader  = $docBlockReader;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $results = [];

        foreach ($this->registrar->getTypes() as $type) {
            if ($type instanceof Interface_) {
                $results[] = $this->interfaceWriter->write($type);
            }
        }

        foreach ($this->registrar->getMethods() as $method) {
            $results[] = $this->methodWriter->write($method);
        }

        return join("\n\n", $results);
    }

    /**
     * @return \JPNut\CodeGen\GeneratorResult
     */
    public function generate(): GeneratorResult
    {
        array_map(
            fn(Route $route) => $this->createMethodFromRoute($route),
            array_filter(
                $this->router->getRoutes()->getIterator()->getArrayCopy(),
                fn(Route $route) => !($route->getAction('uses') instanceof Closure)
            )
        );

        return new GeneratorResult($this->toString());
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     */
    private function createMethodFromRoute(Route $route): void
    {
        $callback = Str::parseCallback($route->getAction('uses'));

        $reflectionMethod = new ReflectionMethod($callback[0], $callback[1]);

        if (($docComment = $reflectionMethod->getDocComment()) === false
            || !$this->docBlockReader->create($docComment)->hasTag('code-gen')) {
            return;
        }

        $this->registrar->registerMethod(
            $route,
            $reflectionMethod,
        );
    }
}
