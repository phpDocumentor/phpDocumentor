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
use phpDocumentor\Dsn;
use phpDocumentor\Path;

interface MiddlewareInterface
{
    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string|Dsn, cache: string|Path}, versions?: array<string, array{api: array<int, array{ignore-tags: array, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibillity: string, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}}>, apis: array, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}} $configuration
     *
     * @return array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string|Dsn, cache: string|Path}, versions?: array<string, array{api: array<int, array{ignore-tags: array, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibillity: string, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}}>, apis: array, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function __invoke(array $configuration, ?UriInterface $uri = null) : array;
}
