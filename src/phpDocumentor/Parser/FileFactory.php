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

use IteratorAggregate;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\NodesFactory;

use function iterator_to_array;

final class FileFactory
{
    /**
     * Symfony's !tagged helper expression returns an iterable object; but the FileFactory requires an array.
     * This means that we need to unpack the middlewares iterable first as a plain array in this factory.
     *
     * @param IteratorAggregate<Middleware> $middlewares
     */
    public static function createInstance(
        DocBlockFactoryInterface $blockFactory,
        NodesFactory $nodesFactory,
        IteratorAggregate $middlewares
    ): Factory\File {
        return new Factory\File($blockFactory, $nodesFactory, iterator_to_array($middlewares));
    }
}
