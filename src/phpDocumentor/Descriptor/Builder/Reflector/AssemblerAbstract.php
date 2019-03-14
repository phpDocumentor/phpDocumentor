<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract as BaseAssembler;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;

abstract class AssemblerAbstract extends BaseAssembler
{
    /**
     * Assemble DocBlock.
     *
     * @param DocBlock|null      $docBlock
     * @param DescriptorAbstract $target
     */
    protected function assembleDocBlock($docBlock, $target)
    {
        if (!$docBlock) {
            return;
        }

        $target->setSummary($docBlock->getSummary());
        $target->setDescription($docBlock->getDescription());

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
        $packageTags = $docBlock ? $docBlock->getTagsByName('package') : [];
        if (count($packageTags) === 0) {
            return null;
        }

        /** @var DocBlock\Tags\Generic $tag */
        $tag = reset($packageTags);

        return trim((string) $tag->getDescription());
    }

    public static function deduplicateTypes(?Type $type): ?Type
    {

        if ($type instanceof Compound) {
            $normalizedTypes = [];
            foreach ($type as $typePart) {
                $normalizedTypes[(string)$typePart] = $typePart;
            }

            return new Compound(array_values($normalizedTypes));
        }

        return $type;
    }
}
