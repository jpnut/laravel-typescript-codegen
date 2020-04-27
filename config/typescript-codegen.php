<?php

use JPNut\CodeGen\MethodWriter;
use JPNut\CodeGen\InterfaceWriter;

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

    /**
     * You can determine which request properties you would like
     * to generate types for by changing the following option. By
     * default, only methods which are tagged as 'body' will be
     * generated, though you can add more (e.g. headers, query-
     * params etc.). To generate types for these properties, you
     * will still need add '@code-gen' followed by the property
     * name to the doc block of the method.
     *
     * If you wish to pass the value as is, pass the property name
     * as a value in the array. If you wish to map the value of the
     * property, pass the name of the property as the key, and the
     * mapped value of the property as the value.
     */
    'request_properties' => [
        'body' => 'JSON.stringify(body)',
    ]
];
