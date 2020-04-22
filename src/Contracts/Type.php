<?php

namespace JPNut\CodeGen\Contracts;

interface Type
{
    public function name(): string;

    public function toReference(): string;
}
