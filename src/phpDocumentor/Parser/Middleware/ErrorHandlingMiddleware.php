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

namespace phpDocumentor\Parser\Middleware;

use PhpParser\Error as PhpParserError;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use Webmozart\Assert\Assert;

final class ErrorHandlingMiddleware implements Middleware
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /** @param callable(Command): object $next */
    public function execute(Command $command, callable $next): object
    {
        Assert::isInstanceOf($command, CreateCommand::class);

        $filename = $command->getFile()->path();
        $this->log('Starting to parse file: ' . $filename);

        try {
            return $next($command);
        } catch (Throwable $e) {
            $sourceLine = $this->findSourceLine($e);
            $lineInfo = $sourceLine !== null ? ' on line ' . $sourceLine : '';

            $this->log(
                '  Unable to parse file "' . $filename . '"' . $lineInfo . ', an error was detected: ' . $e->getMessage(),
                LogLevel::ALERT,
            );
            $this->log('  ' . $e->getTraceAsString(), LogLevel::DEBUG);
        }

        // when an error occurs, return an empty file with an empty hash; this means phpDocumentor will try to
        // re-parse the file every time
        return new File('', $command->getFile()->path());
    }

    /**
     * Walks the exception chain looking for a PhpParser\Error, which carries
     * the line number in the source file being parsed.
     */
    private function findSourceLine(Throwable $e): int|null
    {
        $current = $e;
        while ($current !== null) {
            if ($current instanceof PhpParserError && $current->getStartLine() > 0) {
                return $current->getStartLine();
            }

            $current = $current->getPrevious();
        }

        return null;
    }

    /**
     * Dispatches a logging request.
     *
     * @param mixed[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
