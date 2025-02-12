<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function md5;

/** @coversDefaultClass \phpDocumentor\Parser\Middleware\EmittingMiddleware */
final class EmittingMiddlewareTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    public function testEmitsPreParsingEvent(): void
    {
        // start with a clean dispatcher
        $eventDispatcher = $this->prophesize(EventDispatcher::class);
        $eventDispatcher->dispatch(
            Argument::that(
                function (PreFileEvent $event) {
                    $this->assertSame($event->getFile(), __FILE__);

                    return true;
                },
            ),
            Argument::any(),
        )->willReturnArgument();

        $filename = __FILE__;
        $file = new FileDescriptor(md5('result'));
        $file->setPath($filename);

        $command = new CreateCommand(
            self::faker()->phpParserContext(),
            new LocalFile($filename),
            new ProjectFactoryStrategies([]),
        );

        $middleware = new EmittingMiddleware($eventDispatcher->reveal());
        $result = $middleware->execute(
            $command,
            function (CreateCommand $receivedCommand) use ($command, $file) {
                $this->assertSame($command, $receivedCommand);

                return $file;
            },
        );

        $this->assertSame($file, $result);
    }
}
