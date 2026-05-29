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
use phpDocumentor\Descriptor\Interfaces\DocBlock\TagInterface;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * @template TDescriptor of TagInterface
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
