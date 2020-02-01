<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Flyfinder\Specification\SpecificationInterface;

/**
 * Interface for Specifications used to filter the FileSystem.
 */
interface SpecificationFactoryInterface
{
    /**
     * Creates a SpecificationInterface object based on the ignore and extension parameters.
     *
     * @param list<string> $paths
     * @param array<string, null|bool|array<string>> $ignore
     * @param list<string> $extensions
     *
     * @phpstan-param array<int, string> $paths
     * @phpstan-param array<int, string> $extensions
     */
    public function create(array $paths, array $ignore, array $extensions) : SpecificationInterface;
}
