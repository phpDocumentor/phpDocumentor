<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace Parser\Middleware;

use phpDocumentor\Parser\Middleware\StopwatchMiddleware;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Component\Stopwatch\StopwatchPeriod;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\StopwatchMiddleware
 * @covers ::<private>
 * @covers ::__construct
 */
final class StopwatchMiddlewareTest extends TestCase
{
    /**
     * @covers ::execute
     */
    public function testThatMemoryUsageIsLogged()
    {
        $commandFile = new LocalFile(__FILE__);
        $command = new CreateCommand($commandFile, new ProjectFactoryStrategies([]));

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

        // triggering twice should result in two stopwatch events where the second shows the diff between the first
        // and second
        $middleware->execute($command, function () {
            return 'result';
        });
        $result = $middleware->execute($command, function () {
            return 'result';
        });

        $this->assertSame('result', $result);
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
