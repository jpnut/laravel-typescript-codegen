<?php

namespace JPNut\Tests\Requests;

use JPNut\Tests\DTOs\UpdateFooDTO;
use Illuminate\Foundation\Http\FormRequest;
use JPNut\CodeGen\Contracts\CodeGenRequest;

class UpdateFooRequest extends FormRequest implements CodeGenRequest
{
    /**
     * @return \JPNut\Tests\DTOs\UpdateFooDTO
     * @code-gen body
     */
    public function data(): UpdateFooDTO
    {
        return new UpdateFooDTO();
    }
}
