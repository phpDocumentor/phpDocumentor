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

namespace phpDocumentor\Console;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Utils;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;

/** @internal */
final class LogConfigurator
{
    public static function attach(MonologLogger $logger, ConsoleEvent $event): void
    {
        $output = $event->getOutput();
        if (self::findHandler($logger, ConsoleLogHandler::class) === null) {
            $logger->pushHandler(new ConsoleLogHandler(new SymfonyStyle($event->getInput(), $output)));
        }

        $logFile = $event->getInput()->getOption('log');
        if (! is_string($logFile) || $logFile === '' || $output->getVerbosity() === OutputInterface::VERBOSITY_QUIET) {
            return;
        }

        $canonicalLogFile = Utils::canonicalizePath($logFile);
        foreach ($logger->getHandlers() as $existing) {
            if ($existing instanceof StreamHandler && $existing->getUrl() === $canonicalLogFile) {
                return;
            }
        }

        $logger->pushHandler(new StreamHandler($logFile, self::levelForVerbosity($output->getVerbosity())));
    }

    private static function findHandler(MonologLogger $logger, string $class): HandlerInterface|null
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof $class) {
                return $handler;
            }
        }

        return null;
    }

    private static function levelForVerbosity(int $verbosity): Level
    {
        return match (true) {
            $verbosity >= OutputInterface::VERBOSITY_VERY_VERBOSE => Level::Debug,
            $verbosity >= OutputInterface::VERBOSITY_VERBOSE => Level::Info,
            default => Level::Notice,
        };
    }
}
