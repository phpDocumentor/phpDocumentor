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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract as BaseAssembler;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Builder\Reflector\Docblock\DescriptionAssemblerReducer;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;

use function array_values;
use function count;
use function reset;
use function stripcslashes;
use function trim;

/**
 * @template TDescriptor of Descriptor
 * @template TInput of object
 * @extends  BaseAssembler<TDescriptor, TInput>
 */
abstract class AssemblerAbstract extends BaseAssembler
{
    /** @var AssemblerReducer[] */
    private $reducers;

    public function __construct(AssemblerReducer ...$reducers)
    {
        $this->reducers = $reducers;
    }

    /**
     * @param TInput $data
     *
     * @return TDescriptor|null
     */
    public function create(object $data)
    {
        $descriptor = $this->buildDescriptor($data);

        foreach ($this->reducers as $reducer) {
            if ($reducer instanceof BaseAssembler) {
                $reducer->setBuilder($this->getBuilder());
            }

            $descriptor = $reducer->create($data, $descriptor);
        }

        return $descriptor;
    }

    /**
     * @param TInput $data
     *
     * @return TDescriptor|null
     */
    protected function buildDescriptor(object $data)
    {
        return null;
    }

    /**
     * Assemble DocBlock.
     */
    protected function assembleDocBlock(?DocBlock $docBlock, DescriptorAbstract $target): void
    {
        if (!$docBlock) {
            return;
        }

        $target->setSummary($docBlock->getSummary());

        $reducer = new DescriptionAssemblerReducer();
        $reducer->setBuilder($this->getBuilder());
        $target = $reducer->create($docBlock, $target);

        foreach ($docBlock->getTags() as $tag) {
            $tagDescriptor = $this->builder->buildDescriptor($tag, TagDescriptor::class);

            // allow filtering of tags
            if (!$tagDescriptor) {
                continue;
            }

            $target->getTags()
                ->fetch($tag->getName(), new Collection())
                ->add($tagDescriptor);
        }
    }

    /**
     * Extracts the package from the DocBlock.
     */
    protected function extractPackageFromDocBlock(?DocBlock $docBlock): ?string
    {
        $packageTags = $docBlock ? $docBlock->getTagsByName('package') : [];
        if (count($packageTags) === 0) {
            return null;
        }

        /** @var DocBlock\Tags\Generic $tag */
        $tag = reset($packageTags);

        return trim((string) $tag->getDescription());
    }

    /**
     * @deprecated the functionality in this method has been moved to the Compound type in the latest unreleased
     * version of the TypeResolver library
     */
    public static function deduplicateTypes(?Type $type): ?Type
    {
        if ($type instanceof Compound) {
            $normalizedTypes = [];

            foreach ($type->getIterator() as $typePart) {
                $normalizedTypes[(string) $typePart] = $typePart;
            }

            return new Compound(array_values($normalizedTypes));
        }

        return $type;
    }

    protected function pretifyValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return stripcslashes($value);
    }
}
