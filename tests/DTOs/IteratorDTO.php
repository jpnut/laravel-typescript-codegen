<?php

namespace JPNut\CodeGen\Tests\DTOs;

class IteratorDTO implements \Iterator
{
    private $collection = ['foo', 'bar', 'baz'];

    private $position = 0;

    public function current(): string
    {
        return $this->collection[$this->position];
    }

    public function next()
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->collection);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}
