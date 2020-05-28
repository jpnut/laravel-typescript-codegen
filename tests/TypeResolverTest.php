<?php

namespace JPNut\CodeGen\Tests;

use JPNut\CodeGen\FieldType;
use JPNut\CodeGen\TypeResolver;
use phpDocumentor\Reflection\DocBlockFactory;

class TypeResolverTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_handle_null_definition()
    {
        $resolver = new TypeResolver(DocBlockFactory::createInstance([]));

        $declaration = $resolver->resolve(null);

        $this->assertEquals(false, $declaration->hasTypeDeclaration);
        $this->assertEquals(true, $declaration->isNullable);
        $this->assertEquals(false, $declaration->isMixed);
        $this->assertEquals(false, $declaration->isMixedArray);
        $this->assertEmpty($declaration->allowedTypes);
        $this->assertEmpty($declaration->allowedArrayTypes);
    }

    /**
     * @test
     */
    public function it_can_parse_mixed_array()
    {
        $resolver = new TypeResolver(DocBlockFactory::createInstance([]));

        $declaration = $resolver->resolve('array|Foo[]');

        $this->assertEquals(true, $declaration->hasTypeDeclaration);
        $this->assertEquals(false, $declaration->isNullable);
        $this->assertEquals(false, $declaration->isMixed);
        $this->assertEquals(true, $declaration->isMixedArray);
        $this->assertEquals(
            ['any[]', 'Foo[]'],
            array_map(fn (FieldType $type) => $type->getType(), $declaration->allowedTypes)
        );
        $this->assertEquals(
            ['Foo'],
            array_map(fn (FieldType $type) => $type->getType(), $declaration->allowedArrayTypes)
        );
    }
}
