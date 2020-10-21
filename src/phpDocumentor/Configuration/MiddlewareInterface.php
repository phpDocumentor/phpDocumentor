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

namespace phpDocumentor\Configuration;

use League\Uri\Contracts\UriInterface;

interface MiddlewareInterface
{
    /**
     * @param array<string, array<string, array<string, mixed>>> $configuration
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function __invoke(array $configuration, ?UriInterface $uri = null) : array;
}
