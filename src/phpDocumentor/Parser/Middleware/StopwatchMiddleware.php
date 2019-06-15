<?php
declare(strict_types=1);

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

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchMiddleware implements Middleware
{
    /** @var int $memory amount of memory used */
    private $memory = 0;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Stopwatch $stopwatch, LoggerInterface $logger)
    {
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
    }

    /**
     * Executes this middleware class.
     *
     * @return object
     */
    public function execute(Command $command, callable $next)
    {
        $result = $next($command);

        $lap = $this->stopwatch->lap('parser.parse');
        $oldMemory = $this->memory;
        $periods = $lap->getPeriods();
        $memory = end($periods)->getMemory();

        $differenceInMemory = $memory - $oldMemory;
        $this->log(
            sprintf(
                '>> Memory after processing of file: %s megabytes (%s kilobytes)',
                $this->formatMemoryInMegabytes($memory),
                (($differenceInMemory >= 0) ? '+' : '-') . $this->formatMemoryInKilobytes($differenceInMemory)
            ),
            LogLevel::DEBUG
        );

        $this->memory = $memory;

        return $result;
    }

    /**
     * Dispatches a logging request.
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = [])
    {
        $this->logger->log($priority, $message, $parameters);
    }

    private function formatMemoryInMegabytes(int $memory): string
    {
        return number_format($memory / 1024 / 1024, 2);
    }

    private function formatMemoryInKilobytes(int $memory): string
    {
        return number_format($memory / 1024);
    }
}
