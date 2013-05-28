<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract as BaseAssembler;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Tag\TagFactory;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock;

abstract class AssemblerAbstract extends BaseAssembler
{
    /**
     * @param DocBlock           $docBlock
     * @param DescriptorAbstract $target
     */
    protected function assembleDocBlock($docBlock, $target)
    {
        $target->setSummary($docBlock->getShortDescription());
        $target->setDescription($docBlock->getLongDescription()->getContents());

        $tagFactory = new TagFactory();

        /** @var DocBlock\Tag $tag */
        foreach ($docBlock->getTags() as $tag) {
            $tagObject = $tagFactory->create($tag);

            $existingTags = $target->getTags()->get($tag->getName(), array());
            $existingTags[] = $tagObject;
            $target->getTags()->set($tag->getName(), $existingTags);
        }
    }
}
