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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Reflection\DocBlock;

abstract class AssemblerAbstract extends BaseAssembler
{
    /**
     * @param DocBlock           $docBlock
     * @param DescriptorAbstract $target
     */
    protected function assembleDocBlock($docBlock, $target)
    {
        if (!$docBlock) {
            return;
        }

        $target->setSummary($docBlock->getShortDescription());
        $target->setDescription($docBlock->getLongDescription()->getContents());

        /** @var DocBlock\Tag $tag */
        foreach ($docBlock->getTags() as $tag) {
            $tagDescriptor = $this->builder->buildDescriptor($tag);

            // allow filtering of tags
            if (!$tagDescriptor) {
                continue;
            }

            $target->getTags()
                ->get($tag->getName(), new Collection())
                ->add($tagDescriptor);
        }
    }

    /**
     * Extracts the package from the DocBlock.
     *
     * @param DocBlock $docBlock
     *
     * @return string|null
     */
    protected function extractPackageFromDocBlock($docBlock)
    {
        $packageTaqs = $docBlock ? $docBlock->getTagsByName('package') : null;
        if (! $packageTaqs) {
            return null;
        }

        /** @var DocBlock\Tag $tag */
        $tag = reset($packageTaqs);

        return trim($tag->getContent());
    }
}
