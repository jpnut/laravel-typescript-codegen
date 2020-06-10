<?php

namespace JPNut\CodeGen\Tests\DTOs;

class FooDTO
{
    public string $string;

    public int $integer;

    public bool $boolean;

    public float $float;

    public iterable $iterable;

    public array $array;

    public object $object;

    public FooDTO $child;

    public ?FooDTO $nullable_child;

    /**
     * @var mixed
     */
    public $mixed;

    /**
     * @var mixed|null
     */
    public $nullable_mixed;

    /**
     * @var mixed[]
     */
    public $mixed_array;

    /**
     * @var string
     */
    public $soft_string;

    /**
     * @var int
     */
    public $soft_integer;

    /**
     * @var bool
     */
    public $soft_boolean;

    /**
     * @var float
     */
    public $soft_float;

    /**
     * @var iterable
     */
    public $soft_iterable;

    /**
     * @var array
     */
    public $soft_array;

    /**
     * @var object
     */
    public $soft_object;

    /**
     * @var \JPNut\CodeGen\Tests\DTOs\FooDTO
     */
    public $soft_child;

    /**
     * @var \JPNut\CodeGen\Tests\DTOs\FooDTO|null
     */
    public $soft_nullable_child;

    /**
     * @var int|float|float
     */
    public $number;

    /**
     * @var \JPNut\CodeGen\Tests\DTOs\SerializableDTO
     */
    public SerializableDTO $serializable_object;

    /**
     * @var \JPNut\CodeGen\Tests\DTOs\IteratorDTO
     */
    public IteratorDTO $iterator_object;

    public ?self $nullable_self;

    /**
     * @var self|null
     */
    public $soft_nullable_self;

    /**
     * @var static|null
     */
    public $soft_nullable_static;

    public $unknown_property;

    /**
     * @code-gen-ignore
     */
    public string $ignored_property;

    protected string $protected_property;

    private string $private_property;
}
