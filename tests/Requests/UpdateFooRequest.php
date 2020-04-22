<?php

namespace JPNut\Tests\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JPNut\CodeGen\Contracts\CodeGenRequest;
use JPNut\Tests\DTOs\UpdateFooDTO;

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
