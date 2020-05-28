<?php

namespace JPNut\CodeGen\Tests\Controllers;

use Illuminate\Http\Request;
use JPNut\CodeGen\Tests\DTOs\FooDTO;
use JPNut\CodeGen\Tests\DTOs\FooIndexDTO;
use JPNut\CodeGen\Tests\Requests\UpdateFooRequest;

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
     * @return \JPNut\CodeGen\Tests\DTOs\FooDTO
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
