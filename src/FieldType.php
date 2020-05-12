<?php

namespace JPNut\CodeGen;

class FieldType
{
    /**
     * @var array
     */
    private static array $typeMapping = [
        'integer'  => 'number',
        'double'   => 'number',
        'array'    => 'any[]',
        'iterable' => 'any[]',
        'mixed'    => 'any',
        'object'   => 'any',
    ];

    /**
     * @var string|null
     */
    private ?string $raw_type;

    /**
     * @var string|null
     */
    private ?string $type;

    /**
     * @var string|null
     */
    private ?string $singular;

    /**
     * @param  string|null  $type
     */
    public function __construct(?string $type)
    {
        $this->raw_type = $type;

        $type = static::$typeMapping[$type] ?? $type;

        $this->type = $type;
        $this->singular = $this->parseSingularType($type);
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    private function parseSingularType(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        if (strpos($type, '[]') !== false) {
            return str_replace('[]', '', $type);
        }

        if (strpos($type, 'iterable<') !== false) {
            return str_replace(['iterable<', '>'], ['', ''], $type);
        }

        return $type;
    }

    /**
     * @return static
     */
    public static function nullable(): self
    {
        return new static('null');
    }

    /**
     * @return bool
     */
    public function isClass(): bool
    {
        return ! is_null($this->singular) && class_exists($this->singular);
    }

    /**
     * @return string|null
     */
    public function getRawType(): ?string
    {
        return $this->raw_type;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getSingular(): ?string
    {
        return $this->singular;
    }
}
