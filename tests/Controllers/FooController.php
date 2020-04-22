<?php

namespace JPNut\Tests\Controllers;

use Illuminate\Http\Request;
use JPNut\Tests\DTOs\FooDTO;
use JPNut\Tests\DTOs\FooIndexDTO;
use JPNut\Tests\Requests\UpdateFooRequest;

class FooController
{
    /**
     * @code-gen
     */
    public function index(): FooIndexDTO
    {
        return new FooIndexDTO;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $foo
     * @return \JPNut\Tests\DTOs\FooDTO
     * @code-gen
     */
    public function fetch(Request $request, int $foo): FooDTO
    {
        return new FooDTO;
    }

    /**
     * @code-gen
     */
    public function update(UpdateFooRequest $request, int $foo): FooDTO
    {
        return new FooDTO;
    }

    /**
     * @code-gen
     */
    public function delete()
    {

    }

    public function create(): FooDTO
    {
        return new FooDTO;
    }
}
