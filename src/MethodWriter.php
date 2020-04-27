<?php

namespace JPNut\CodeGen;

use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use InvalidArgumentException;

class MethodWriter
{
    /**
     * @var \JPNut\CodeGen\RouteUrlGenerator
     */
    protected RouteUrlGenerator $generator;

    /**
     * @var \JPNut\CodeGen\TypeRegistrar
     */
    protected TypeRegistrar $registrar;

    /**
     * @var \JPNut\CodeGen\FieldTypeWriter
     */
    protected FieldTypeWriter $fieldTypeWriter;

    /**
     * @param  \JPNut\CodeGen\RouteUrlGenerator  $generator
     * @param  \JPNut\CodeGen\TypeRegistrar  $registrar
     * @param  \JPNut\CodeGen\FieldTypeWriter  $fieldTypeWriter
     */
    public function __construct(
        RouteUrlGenerator $generator,
        TypeRegistrar $registrar,
        FieldTypeWriter $fieldTypeWriter
    ) {
        $this->generator = $generator;
        $this->registrar = $registrar;
        $this->fieldTypeWriter = $fieldTypeWriter;
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return string
     */
    public function write(Method $method): string
    {
        return implode('', [
            'export const ',
            $this->routeToMethodName($route = $method->getRoute()),
            ' = ',
            "({$this->functionParameters($method, $route, $fields = $this->functionFields($method))}): ",
            "Promise<{$this->formatReturnType($method)}>",
            ' => ',
            $this->routeToFunctionCall($route, $fields),
            ';',
        ]);
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function routeToMethodName(Route $route): string
    {
        return str_replace(
                ' ',
                '',
                Str::camel(preg_replace('/[.-_]+/', ' ', $route->getName()))
            ).'Request';
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @param  \Illuminate\Routing\Route  $route
     * @param  string[]  $fields
     * @return string
     */
    protected function functionParameters(Method $method, Route $route, array $fields): string
    {
        return implode(', ', array_filter([
            $this->requestParameters($method, $fields),
            ...$this->routeParameters($route),
        ]));
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @param  string[]  $fields
     * @return string|null
     */
    protected function requestParameters(Method $method, array $fields): ?string
    {
        if (empty($fields) || is_null($requestType = $method->getRequestType())) {
            return null;
        }

        return '{ '.implode(', ', $fields)." }: {$this->registrar->formatName($requestType->getSingular())}";
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @return string[]
     */
    protected function routeParameters(Route $route): array
    {
        return array_map(fn (RouteParameter $rp) => $rp->toFunctionParameter(), $this->parameters($route));
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @return \JPNut\CodeGen\RouteParameter[]
     */
    protected function parameters(Route $route): array
    {
        preg_match_all('/{(.*?)}/', $route->getDomain().$route->uri, $matches);

        return array_map(function ($m) {
            return new RouteParameter($param = trim($m, '?'), $param !== $m);
        }, $matches[1]);
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return string[]
     */
    protected function functionFields(Method $method): array
    {
        if (! $method->hasRequestType()) {
            return [];
        }

        $type = $this->registrar->getType($method->getRequestType()->getSingular());

        if (! $type instanceof Interface_) {
            throw new InvalidArgumentException("Invalid request object - expected interface but got literal: {$type->name()}");
        }

        return array_map(fn (Field $field) => $field->getName(), $type->getFields());
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return string
     */
    protected function formatReturnType(Method $method): string
    {
        return empty($types = $this->getMethodReturnTypes($method))
            ? 'any'
            : implode(' | ', $types);
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  string[]  $fields
     * @return string
     */
    protected function routeToFunctionCall(Route $route, array $fields): string
    {
        $uri = $this->generator->to(
            $route,
            array_map(fn (RouteParameter $rp) => $rp->toUriComponent(), $this->parameters($route))
        );

        $method = in_array('GET', $route->methods) ? 'GET' : $route->methods[0];

        $properties = [
            'uri'    => "`{$uri}`",
            'method' => "'{$method}'",
        ];

        foreach ($fields as $field) {
            $properties[$field] = $this->registrar->requestPropertyValue($field);
        }

        return "request({ {$this->propertiesToString($properties)} })";
    }

    /**
     * @param  array  $properties
     * @return string
     */
    protected function propertiesToString(array $properties): string
    {
        return collect($properties)
            ->map(fn ($value, $key) => $key === $value ? $key : "{$key}: {$value}")
            ->join(', ');
    }

    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return string[]
     */
    protected function getMethodReturnTypes(Method $method): array
    {
        return array_unique(
            array_map(
                fn (FieldType $type) => $this->fieldTypeWriter->write($type),
                $method->getReturnTypes()
            )
        );
    }
}
