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
     *
     * @var (\phpDocumentor\Path|string)[] $paths
     * @var (\phpDocumentor\Path|string)[] $ignore
     * @var string[] $extensions
     */
    public function create(array $paths, array $ignore, array $extensions): SpecificationInterface
    {
        $pathSpec = null;
        foreach ($paths as $path) {
            if ($pathSpec === null) {
                $pathSpec = $this->inPath((string) $path);
                continue;
            }

            $pathSpec = $this->orSpec($this->inPath($path), $pathSpec);
        }

        $ignoreSpec = null;
        foreach ($ignore['paths'] ?? [] as $path) {
            if ($ignoreSpec === null) {
                $ignoreSpec = $this->inPath((string) $path);
                continue;
            }

            $ignoreSpec = $this->orSpec($this->inPath((string) $path), $ignoreSpec);
        }

        if (($ignore['hidden'] ?? false) === true) {
            $ignoreSpec = $ignoreSpec === null
                ? new IsHidden()
                : $this->orSpec(new IsHidden(), $ignoreSpec);
        }

        $result = new HasExtension($extensions);
        if ($ignoreSpec !== null) {
            $result = $this->andSpec($result, $this->notSpec($ignoreSpec));
        }
        if ($pathSpec !== null) {
            $result = $this->andSpec($pathSpec, $result);
        }

        return $result;
    }

    private function inPath(string $path): InPath
    {
        return new InPath(new Path((string) $path));
    }

    private function orSpec(SpecificationInterface $or, SpecificationInterface $spec): SpecificationInterface
    {
        return new OrSpecification($spec, $or);
    }

    private function notSpec(SpecificationInterface $ignoreSpec): SpecificationInterface
    {
        return new NotSpecification($ignoreSpec);
    }

    private function andSpec(SpecificationInterface $spec, SpecificationInterface $spec2): SpecificationInterface
    {
        return new AndSpecification($spec, $spec2);
    }
}
