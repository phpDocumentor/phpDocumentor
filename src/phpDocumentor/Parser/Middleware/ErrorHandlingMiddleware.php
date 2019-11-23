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

use Exception;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class ErrorHandlingMiddleware implements Middleware
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return object
     */
    public function execute(Command $command, callable $next)
    {
        assert($command instanceof CreateCommand);

        $filename = $command->getFile()->path();
        $this->log('Starting to parse file: ' . $filename, LogLevel::INFO);

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
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = [])
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
