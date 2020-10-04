<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\Reflector\Docblock\DescriptionAssemblerReducer;

/**
 * @template TDescriptor of \phpDocumentor\Descriptor\TagDescriptor
 * @template TInput of \phpDocumentor\Reflection\DocBlock\Tag
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
