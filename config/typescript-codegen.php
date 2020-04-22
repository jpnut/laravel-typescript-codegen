<?php

use JPNut\CodeGen\InterfaceWriter;
use JPNut\CodeGen\MethodWriter;

return [
    /**
     * The interface and method writers must implement the
     * InterfaceWriterContract and MethodWriterContract
     * respectively, exposing a public "write" method which
     * takes the interface/method as a parameter and outputs
     * a string.
     */
    'writers' => [
        'interface' => InterfaceWriter::class,

        'method' => MethodWriter::class,
    ],

    /**
     * The stub will be used to generate the schema file.
     * The default stub provided by this package defines all
     * the necessary helper methods and types that may be used
     * in a generated schema. You may override this stub, but
     * make sure to include a line containing '{{ Schema }}',
     * as this will be used to insert the generated schema.
     */
    'stub'    => __DIR__.'/../src/stubs/schema.stub',
];
