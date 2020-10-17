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

namespace phpDocumentor\Descriptor\Filter;

use InvalidArgumentException;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Strips any Descriptor if their visibility is allowed according to the ProjectDescriptorBuilder.
 */
class StripOnVisibility implements FilterInterface
{
    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;

    /**
     * Initializes this filter with an instance of the builder to retrieve the latest ProjectDescriptor from.
     */
    public function __construct(ProjectDescriptorBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Filter Descriptor with based on visibility.
     */
    public function __invoke(Filterable $value) : ?Filterable
    {
        if (!$value instanceof DescriptorAbstract) {
            return $value;
        }

        // if a Descriptor is marked as 'api' and this is set as a visibility; _always_ show it; even if the visibility
        // is not set
        if (isset($value->getTags()['api'])
            && $this->builder->getProjectDescriptor()->isVisibilityAllowed(Settings::VISIBILITY_API)
        ) {
            return $value;
        }

        if (!$value instanceof VisibilityInterface) {
            return $value;
        }

        if ($this->builder->getProjectDescriptor()->isVisibilityAllowed($this->toVisibility($value->getVisibility()))) {
            return $value;
        }

        return null;
    }

    private function toVisibility(string $visibility) : int
    {
        switch ($visibility) {
            case 'public':
                return Settings::VISIBILITY_PUBLIC;
            case 'protected':
                return Settings::VISIBILITY_PROTECTED;
            case 'private':
                return Settings::VISIBILITY_PRIVATE;
        }

        throw new InvalidArgumentException($visibility . ' is not a valid visibility');
    }
}
