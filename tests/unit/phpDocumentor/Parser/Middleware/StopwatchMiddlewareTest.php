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

use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\Middleware\StopwatchMiddleware;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use stdClass;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Component\Stopwatch\StopwatchPeriod;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\StopwatchMiddleware
 * @covers ::<private>
 * @covers ::__construct
 */
final class StopwatchMiddlewareTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /**
     * @covers ::execute
     */
    public function testThatMemoryUsageIsLogged(): void
    {
        $commandFile = new LocalFile(__FILE__);
        $command = new CreateCommand(
            $this->faker()->phpParserContext(),
            $commandFile,
            new ProjectFactoryStrategies([])
        );

        $logger = $this->prophesize(LoggerInterface::class);
        $logger
            ->log(LogLevel::DEBUG, '>> Memory after processing of file: 1.14 megabytes (+1,172 kilobytes)', [])
            ->shouldBeCalled();
        $logger
            ->log(LogLevel::DEBUG, '>> Memory after processing of file: 1.24 megabytes (+98 kilobytes)', [])
            ->shouldBeCalled();

        $stopwatch = $this->prophesize(Stopwatch::class);
        $stopwatch->lap('parser.parse')->willReturn(
            $this->givenAStopwatchEventWithMemoryTotal(1200000),
            $this->givenAStopwatchEventWithMemoryTotal(1300000)
        );

        $middleware = new StopwatchMiddleware($stopwatch->reveal(), $logger->reveal());

        $expected = new stdClass();
        // triggering twice should result in two stopwatch events where the second shows the diff between the first
        // and second
        $middleware->execute(
            $command,
            static function () use ($expected) {
                return $expected;
            }
        );
        $result = $middleware->execute(
            $command,
            static function () use ($expected) {
                return $expected;
            }
        );

        $this->assertSame($expected, $result);
    }

    private function givenAStopwatchEventWithMemoryTotal(int $memory): StopwatchEvent
    {
        $period = $this->prophesize(StopwatchPeriod::class);
        $period->getMemory()->willReturn($memory);

        $event = $this->prophesize(StopwatchEvent::class);
        $event->getPeriods()->willReturn([$period->reveal()]);

        return $event->reveal();
    }
}
