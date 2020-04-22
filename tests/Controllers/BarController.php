<?php

namespace JPNut\Tests\Controllers;

use Illuminate\Http\Request;
use JPNut\Tests\Requests\QueryParamsRequest;

class BarController
{
    /**
     * @return string
     * @code-gen
     */
    public function scalarReturnType(): string
    {
        return 'bar';
    }

    /**
     * @return string|null
     * @code-gen
     */
    public function optionalScalarReturnType(): ?string
    {
        return null;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $id
     * @return int|null
     * @code-gen
     */
    public function optionalParameter(Request $request, ?int $id): ?int
    {
        return $id;
    }

    /**
     * @param  \JPNut\Tests\Requests\QueryParamsRequest  $request
     * @return mixed
     * @code-gen
     */
    public function queryParams(QueryParamsRequest $request)
    {
    }

    /**
     * @return string|null
     * @code-gen
     */
    public function httpOnly(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     * @code-gen
     */
    public function httpsOnly(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     * @code-gen
     */
    public function domain(): ?string
    {
        return null;
    }
}
