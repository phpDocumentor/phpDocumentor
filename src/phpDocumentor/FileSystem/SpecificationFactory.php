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

namespace phpDocumentor\FileSystem;

use Flyfinder\Specification\Glob;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\SpecificationInterface;
use phpDocumentor\FileSystem\SpecificationFactoryInterface as FactoryInterface;

/**
 * Factory class to build Specification used by FlyFinder when reading files to process.
 */
final class SpecificationFactory implements FactoryInterface
{
    /**
     * Creates a SpecificationInterface object based on the ignore and extension parameters.
     *
     * @param list<string|Path> $paths
     * @param array{paths: list<string|Path>, hidden: bool} $ignore
     * @param list<string> $extensions
     */
    public function create(array $paths, array $ignore, array $extensions): SpecificationInterface
    {
        /** @var ?Glob $pathSpec */
        $pathSpec = null;
        foreach ($paths as $path) {
            if ($path instanceof Path) {
                $path = (string) $path;
            }

            if ($pathSpec === null) {
                $pathSpec = new Glob($path);
                continue;
            }

            $pathSpec = $pathSpec->orSpecification(new Glob($path));
        }

        /** @var ?Glob $ignoreSpec */
        $ignoreSpec = null;
        foreach ($ignore['paths'] ?? [] as $path) {
            if ($ignoreSpec === null) {
                $ignoreSpec = new Glob($path);
                continue;
            }

            $ignoreSpec = $ignoreSpec->orSpecification(new Glob($path));
        }

        if (($ignore['hidden'] ?? false) === true) {
            $ignoreSpec = $ignoreSpec === null
                ? new IsHidden()
                : $ignoreSpec->orSpecification(new IsHidden());
        }

        $result = new HasExtension($extensions);
        if ($ignoreSpec !== null) {
            $result = $result->andSpecification(new NotSpecification($ignoreSpec));
        }

        if ($pathSpec !== null) {
            $result = $result->andSpecification($pathSpec);
        }

        return $result;
    }
}
