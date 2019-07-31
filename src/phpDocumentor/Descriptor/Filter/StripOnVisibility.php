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

namespace phpDocumentor\Descriptor\Filter;

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
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function __invoke(?Filterable $value) : ?Filterable
    {
        if ($value instanceof VisibilityInterface
            && !$this->builder->getProjectDescriptor()->isVisibilityAllowed(
                $this->toVisibility($value->getVisibility())
            )
        ) {
            return null;
        }

        return $value;
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

        throw new \InvalidArgumentException($visibility . ' is not a valid visibility');
    }
}
