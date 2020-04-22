<?php

namespace JPNut\Tests\Requests;

use JPNut\Tests\DTOs\BarRequestParamsDTO;
use Illuminate\Foundation\Http\FormRequest;
use JPNut\CodeGen\Contracts\CodeGenRequest;

class QueryParamsRequest extends FormRequest implements CodeGenRequest
{
    /**
     * @return \JPNut\Tests\DTOs\BarRequestParamsDTO
     * @code-gen queryParams
     */
    public function params(): BarRequestParamsDTO
    {
        return new BarRequestParamsDTO;
    }
}
