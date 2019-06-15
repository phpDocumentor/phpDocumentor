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

namespace phpDocumentor\Parser;

use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;
use Flyfinder\Specification\SpecificationInterface;
use phpDocumentor\Parser\SpecificationFactoryInterface as FactoryInterface;

/**
 * Factory class to build Specification used by FlyFinder when reading files to process.
 */
final class SpecificationFactory implements FactoryInterface
{
    /**
     * Creates a SpecificationInterface object based on the ignore and extension parameters.
     */
    public function create(array $paths, array $ignore, array $extensions): SpecificationInterface
    {
        $pathSpec = null;
        foreach ($paths as $path) {
            $pathSpec = $this->orSpec($this->inPath($path), $pathSpec);
        }

        $ignoreSpec = null;
        if (isset($ignore['paths'])) {
            foreach ($ignore['paths'] as $path) {
                $ignoreSpec = $this->orSpec($this->inPath($path), $ignoreSpec);
            }
        }

        if (isset($ignore['hidden']) && $ignore['hidden'] === true) {
            $ignoreSpec = $this->orSpec(new IsHidden(), $ignoreSpec);
        }

        return $this->andSpec(
            $pathSpec,
            $this->andSpec(new HasExtension($extensions), $this->notSpec($ignoreSpec))
        );
    }

    /**
     * Will return an OrSpecification when $or and $spec are both not null.
     *
     * @param SpecificationInterface|null $spec
     * @return OrSpecification|SpecificationInterface
     */
    private function orSpec(SpecificationInterface $or, SpecificationInterface $spec = null): SpecificationInterface
    {
        if ($spec === null) {
            return $or;
        }

        return new OrSpecification($spec, $or);
    }

    /**
     * Creates an InPath specification.
     *
     * @param string $path
     */
    private function inPath($path): InPath
    {
        return new InPath(new Path((string) $path));
    }

    private function notSpec(SpecificationInterface $ignoreSpec = null)
    {
        if ($ignoreSpec === null) {
            return null;
        }

        return new NotSpecification($ignoreSpec);
    }

    private function andSpec(SpecificationInterface $spec = null, SpecificationInterface $spec2 = null)
    {
        if ($spec === null) {
            return $spec2;
        }

        if ($spec2 === null) {
            return $spec;
        }

        return new AndSpecification($spec, $spec2);
    }
}
