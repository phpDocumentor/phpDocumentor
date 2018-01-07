<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use Psr\Log\LogLevel;
use phpDocumentor\Reflection\Middleware\Middleware;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchMiddleware implements Middleware
{
    /** @var int $memory amount of memory used */
    private $memory = 0;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * StopwatchMiddleware constructor.
     * @param Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Executes this middle ware class.
     *
     * @param $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        $result = $next($command);

        if ($this->stopwatch) {
            $lap = $this->stopwatch->lap('parser.parse');
            $oldMemory = $this->memory;
            $periods = $lap->getPeriods();
            $memory = end($periods)->getMemory();

            $this->log(
                '>> Memory after processing of file: ' . number_format($memory / 1024 / 1024, 2)
                . ' megabytes (' . (($memory - $oldMemory >= 0)
                    ? '+'
                    : '-') . number_format(($memory - $oldMemory) / 1024)
                . ' kilobytes)',
                LogLevel::DEBUG
            );

            $this->memory = $memory;
        }

        return $result;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $message  The message to log.
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     *
     * @return void
     */
    protected function log($message, $priority = LogLevel::INFO, $parameters = array())
    {
        Dispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this)
                ->setContext($parameters)
                ->setMessage($message)
                ->setPriority($priority)
        );
    }
}
