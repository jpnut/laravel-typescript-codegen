<?php

namespace JPNut\CodeGen\Contracts;

use JPNut\CodeGen\Interface_;

interface InterfaceWriterContract
{
    /**
     * @param  \JPNut\CodeGen\Interface_  $interface
     * @return string
     */
    public function write(Interface_ $interface): string;
}
