<?php

namespace JPNut\CodeGen\Tests\Requests;

use JPNut\CodeGen\Tests\DTOs\UpdateFooDTO;
use Illuminate\Foundation\Http\FormRequest;
use JPNut\CodeGen\Contracts\CodeGenRequest;

class UpdateFooRequest extends FormRequest implements CodeGenRequest
{
    /**
     * @return \JPNut\CodeGen\Tests\DTOs\UpdateFooDTO
     * @code-gen-property body
     */
    public function data(): UpdateFooDTO
    {
        return new UpdateFooDTO();
    }
}
