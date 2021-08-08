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

namespace phpDocumentor\Parser;

use Flyfinder\Specification\SpecificationInterface;
use phpDocumentor\Path;

/**
 * Interface for Specifications used to filter the FileSystem.
 */
interface SpecificationFactoryInterface
{
    /**
     * Creates a SpecificationInterface object based on the ignore and extension parameters.
     *
     * @param list<string|Path> $paths
     * @param array<string, bool|array<string>|null> $ignore
     * @param list<string> $extensions
     */
    public function create(array $paths, array $ignore, array $extensions): SpecificationInterface;
}
