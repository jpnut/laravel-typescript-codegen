<?php

namespace JPNut\CodeGen;

class RouteParameter
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool
     */
    private bool $nullable;

    /**
     * @param  string  $name
     * @param  bool  $nullable
     */
    public function __construct(string $name, bool $nullable = false)
    {
        $this->name     = $name;
        $this->nullable = $nullable;
    }

    /**
     * @return string
     */
    public function toFunctionParameter(): string
    {
        if ($this->nullable) {
            return "{$this->name}?: string";
        }

        return "{$this->name}: string";
    }

    /**
     * @return string
     */
    public function toUriComponent(): string
    {
        if ($this->nullable) {
            return "\${!isNil({$this->name})?{$this->name}:\"\"}";
        }

        return "\${{$this->name}}";
    }
}
