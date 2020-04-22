<?php

namespace JPNut\CodeGen;

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;

final class CodeGenTag extends BaseTag implements StaticMethod
{
    /**
     * @see Formatter
     *
     * @var string
     */
    protected $name = 'code-gen';

    /**
     * @param  Description  $description  Used to enable code generation for the associated method.
     *
     * @see BaseTag for the declaration of the description property and getDescription method.
     */
    public function __construct(Description $description = null)
    {
        $this->description = $description;
    }

    /**
     * @param  string  $body
     * @param  DescriptionFactory  $descriptionFactory
     * @param  Context|null  $context
     * @return \JPNut\CodeGen\CodeGenTag
     *
     * @see Tag::create() for more information on this method's workings.
     * @see Tag for the interface declaration of the `create` method.
     */
    public static function create(
        string $body,
        DescriptionFactory $descriptionFactory = null,
        Context $context = null
    ): self {
        return new static($descriptionFactory->create($body, $context));
    }

    /**
     * This method is used to reconstitute a DocBlock into its original form by the {@see Serializer}. It should
     * feature all parts of the tag so that the serializer can put it back together.
     */
    public function __toString(): string
    {
        return (string) $this->description;
    }
}
