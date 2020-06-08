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

namespace Parser;

use ArrayObject;
use InvalidArgumentException;
use phpDocumentor\Parser\FileFactory;
use phpDocumentor\Parser\Middleware\EmittingMiddleware;
use phpDocumentor\Reflection\Php\NodesFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FileFactory
 * @covers ::<private>
 */
final class FileFactoryTest extends TestCase
{
    public function testIfFileFactoryIsCreatedUsingAnArray() : void
    {
        FileFactory::createInstance(
            NodesFactory::createInstance(),
            new ArrayObject([new EmittingMiddleware()])
        );

        // if we reach this point then the FileFactory did not fail to instantiate and the middlewares
        // have been successfully registered. The ChainFactory inside the FileFactory will throw an
        // exception if it is unable to interpret the ArrayObject and EmittingMiddleware
        $this->assertTrue(true);
    }

    public function testIfFileFactoryFailsWhenPassingAnInvalidMiddlewareType() : void
    {
        // technically we are testing behaviour of the ChainFactory; however, because this is the inverse of the
        // previous test we now verify that an error should indeed be thrown and we do not run the risk of silent
        // test failures just because the previous test did not fail
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Middleware must be an instance of phpDocumentor\Reflection\Middleware\Middleware but stdClass was given'
        );

        FileFactory::createInstance(
            NodesFactory::createInstance(),
            new ArrayObject([new stdClass()])
        );
    }
}
