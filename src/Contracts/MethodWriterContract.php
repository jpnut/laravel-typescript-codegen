<?php

namespace JPNut\CodeGen\Contracts;

use JPNut\CodeGen\Method;

interface MethodWriterContract
{
    /**
     * @param  \JPNut\CodeGen\Method  $method
     * @return string
     */
    public function write(Method $method): string;
}
