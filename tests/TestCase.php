<?php

namespace JPNut\CodeGen\Tests;

use JPNut\CodeGen\CodeGenServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CodeGenServiceProvider::class,
        ];
    }
}
