<?php

namespace JPNut\Tests\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JPNut\CodeGen\Contracts\CodeGenRequest;
use JPNut\Tests\DTOs\BarRequestParamsDTO;

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
