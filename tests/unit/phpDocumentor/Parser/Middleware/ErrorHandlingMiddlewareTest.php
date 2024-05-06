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

namespace Parser\Middleware;

use Exception;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\Middleware\ErrorHandlingMiddleware;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\ErrorHandlingMiddleware
 
 * @covers ::__construct
 */
final class ErrorHandlingMiddlewareTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @covers ::execute */
    public function testThatParsingStartIsLogged(): void
    {
        $filename = __FILE__;
        $expected = new File('abc', $filename);
        $command = new CreateCommand(
            self::faker()->phpParserContext(),
            new LocalFile($filename),
            new ProjectFactoryStrategies([]),
        );

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(LogLevel::INFO, 'Starting to parse file: ' . __FILE__, [])->shouldBeCalled();

        $middleware = new ErrorHandlingMiddleware($logger->reveal());
        $result = $middleware->execute(
            $command,
            function (CreateCommand $receivedCommand) use ($command, $expected) {
                $this->assertSame($command, $receivedCommand);

                return $expected;
            },
        );

        $this->assertSame($expected, $result);
    }

    /** @covers ::execute */
    public function testThatAnErrorIsLogged(): void
    {
        $filename = __FILE__;
        $command = new CreateCommand(
            self::faker()->phpParserContext(),
            new LocalFile($filename),
            new ProjectFactoryStrategies([]),
        );

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(LogLevel::INFO, 'Starting to parse file: ' . __FILE__, [])->shouldBeCalled();
        $logger->log(
            LogLevel::ALERT,
            '  Unable to parse file "' . __FILE__ . '", an error was detected: this is a test',
            [],
        )->shouldBeCalled();
        $logger->log(
            LogLevel::NOTICE,
            Argument::containingString('  -- Found in '),
            [],
        )->shouldBeCalled();
        $logger->log(LogLevel::DEBUG, Argument::any(), [])->shouldBeCalled();

        $middleware = new ErrorHandlingMiddleware($logger->reveal());

        /** @var File $result */
        $result = $middleware->execute(
            $command,
            static function (CreateCommand $receivedCommand): never {
                throw new Exception('this is a test');
            },
        );

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame('', $result->getHash());
        $this->assertSame($filename, $result->getPath());
    }
}
