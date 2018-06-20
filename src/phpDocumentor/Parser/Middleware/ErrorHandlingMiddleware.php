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

use Exception;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use Psr\Log\LogLevel;

final class ErrorHandlingMiddleware implements Middleware
{
    /**
     * Executes this middle ware class.
     *
     * @param callable $next
     * @return object
     */
    public function execute(Command $command, callable $next)
    {
        $filename = $command->getFile()->path();
        $this->log('Starting to parse file: ' . $filename);

        try {
            return $next($command);
        } catch (Exception $e) {
            $this->log(
                '  Unable to parse file "' . $filename . '", an error was detected: ' . $e->getMessage(),
                LogLevel::ALERT
            );
        }

        return null;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $message  The message to log.
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log($message, $priority = LogLevel::INFO, $parameters = [])
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
