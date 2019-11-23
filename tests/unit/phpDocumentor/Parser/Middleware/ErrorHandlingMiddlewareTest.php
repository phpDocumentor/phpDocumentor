<?php declare(strict_types=1);

namespace Parser\Middleware;

use phpDocumentor\Parser\Middleware\ErrorHandlingMiddleware;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\ErrorHandlingMiddleware
 * @covers ::<private>
 * @covers ::__construct
 */
final class ErrorHandlingMiddlewareTest extends TestCase
{
    /**
     * @covers ::execute
     */
    public function testThatParsingStartIsLogged()
    {
        $filename = __FILE__;
        $command = new CreateCommand(new LocalFile($filename), new ProjectFactoryStrategies([]));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(LogLevel::INFO, 'Starting to parse file: ' . __FILE__, [])->shouldBeCalled();

        $middleware = new ErrorHandlingMiddleware($logger->reveal());
        $result = $middleware->execute(
            $command,
            function (CreateCommand $receivedCommand) use ($command) {
                $this->assertSame($command, $receivedCommand);

                return 'result';
            }
        );

        $this->assertSame('result', $result);
    }

    /**
     * @covers ::execute
     */
    public function testThatAnErrorIsLogged()
    {
        $filename = __FILE__;
        $command = new CreateCommand(new LocalFile($filename), new ProjectFactoryStrategies([]));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(LogLevel::INFO, 'Starting to parse file: ' . __FILE__, [])->shouldBeCalled();
        $logger->log(
            LogLevel::ALERT,
            '  Unable to parse file "' . __FILE__ . '", an error was detected: this is a test',
            []
        )->shouldBeCalled();

        $middleware = new ErrorHandlingMiddleware($logger->reveal());
        $result = $middleware->execute(
            $command,
            function (CreateCommand $receivedCommand) use ($command) {
                throw new \Exception('this is a test');
            }
        );

        $this->assertNull($result);
    }
}
