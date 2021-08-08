<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\Reflector\Docblock\DescriptionAssemblerReducer;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * @template TDescriptor of TagDescriptor
 * @template TInput of Tag
 * @extends  AssemblerAbstract<TDescriptor, TInput>
 */
abstract class BaseTagAssembler extends AssemblerAbstract
{
    public function __construct(AssemblerReducer ...$reducers)
    {
        $reducers[] = new DescriptionAssemblerReducer();
        parent::__construct(...$reducers);
    }
}
