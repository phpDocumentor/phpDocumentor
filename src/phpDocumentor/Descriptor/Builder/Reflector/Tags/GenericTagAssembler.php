<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\Reflector\Docblock\DescriptionAssemblerReducer;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;

class GenericTagAssembler extends AssemblerAbstract
{
    public function __construct(AssemblerReducer ...$reducers)
    {
        $reducers[] = new DescriptionAssemblerReducer();
        parent::__construct(...$reducers);
    }

    /**
     * @param Tag $data
     */
    protected function buildDescriptor(object $data) : TagDescriptor
    {
        return new TagDescriptor($data->getName());
    }
}
