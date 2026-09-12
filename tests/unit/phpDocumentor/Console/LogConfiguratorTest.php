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

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function array_filter;
use function array_values;
use function basename;
use function chdir;
use function getcwd;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const DIRECTORY_SEPARATOR;

/** @coversDefaultClass \phpDocumentor\Console\LogConfigurator */
final class LogConfiguratorTest extends TestCase
{
    /** @return iterable<string, array{array<string, string|bool|null>}> */
    public static function noFileHandlerCases(): iterable
    {
        yield 'missing option' => [[]];
        yield 'empty string'   => [['--log' => '']];
    }

    /**
     * @param array<string, string|bool|null> $options
     *
     * @dataProvider noFileHandlerCases
     */
    public function testAttachSkipsFileHandlerWhenLogOptionHasNoUsableValue(array $options): void
    {
        $logger = new MonologLogger('test');
        $event = $this->createConsoleEvent($options);

        LogConfigurator::attach($logger, $event);

        self::assertCount(0, $this->streamHandlers($logger));
    }

    public function testAttachSkipsFileHandlerWhenQuiet(): void
    {
        $this->withTempLogFile(function (string $logFile): void {
            $logger = new MonologLogger('test');
            $event = $this->createConsoleEvent(['--log' => $logFile], OutputInterface::VERBOSITY_QUIET);

            LogConfigurator::attach($logger, $event);

            self::assertCount(0, $this->streamHandlers($logger));
        });
    }

    /** @link https://github.com/phpDocumentor/phpDocumentor/issues/1872 */
    public function testAttachDoesNotDuplicateHandlersOnNestedCommands(): void
    {
        $this->withTempLogFile(function (string $logFile): void {
            $logger = new MonologLogger('test');
            $event = $this->createConsoleEvent(['--log' => $logFile]);

            LogConfigurator::attach($logger, $event);
            LogConfigurator::attach($logger, $event);

            self::assertCount(1, $this->streamHandlers($logger));
            self::assertCount(1, array_filter(
                $logger->getHandlers(),
                static fn ($h) => $h instanceof ConsoleLogHandler,
            ));
        });
    }

    public function testAttachPushesDistinctStreamHandlerForDifferentLogFiles(): void
    {
        $this->withTempLogFile(function (string $first): void {
            $this->withTempLogFile(function (string $second) use ($first): void {
                $logger = new MonologLogger('test');

                LogConfigurator::attach($logger, $this->createConsoleEvent(['--log' => $first]));
                LogConfigurator::attach($logger, $this->createConsoleEvent(['--log' => $second]));

                self::assertCount(2, $this->streamHandlers($logger));
            });
        });
    }

    public function testAttachDeduplicatesStreamHandlerForRelativePaths(): void
    {
        if (DIRECTORY_SEPARATOR !== '/') {
            self::markTestSkipped('Monolog canonicalisation mixes / and \\ on Windows, breaking path equality.');
        }

        $dir = sys_get_temp_dir();
        $tempFile = tempnam($dir, 'phpdoc-log-test');
        self::assertIsString($tempFile);
        $name = basename($tempFile);

        $cwd = getcwd();
        if ($cwd === false) {
            self::markTestSkipped('Unable to resolve the current working directory.');
        }

        try {
            chdir($dir);

            try {
                $canonicalCwd = getcwd();
                self::assertIsString($canonicalCwd);
                $absolute = $canonicalCwd . '/' . $name;

                $logger = new MonologLogger('test');
                LogConfigurator::attach($logger, $this->createConsoleEvent(['--log' => $name]));
                LogConfigurator::attach($logger, $this->createConsoleEvent(['--log' => $absolute]));

                self::assertCount(1, $this->streamHandlers($logger));
            } finally {
                chdir($cwd);
            }
        } finally {
            @unlink($tempFile);
        }
    }

    /** @return array<string, array{int, Level}> */
    public static function verbosityLevelMap(): array
    {
        return [
            'normal'       => [OutputInterface::VERBOSITY_NORMAL, Level::Notice],
            'verbose'      => [OutputInterface::VERBOSITY_VERBOSE, Level::Info],
            'very verbose' => [OutputInterface::VERBOSITY_VERY_VERBOSE, Level::Debug],
            'debug'        => [OutputInterface::VERBOSITY_DEBUG, Level::Debug],
        ];
    }

    /** @dataProvider verbosityLevelMap */
    public function testAttachMapsVerbosityToFileLogLevel(int $verbosity, Level $expected): void
    {
        $this->withTempLogFile(function (string $logFile) use ($verbosity, $expected): void {
            $logger = new MonologLogger('test');
            $event = $this->createConsoleEvent(['--log' => $logFile], $verbosity);

            LogConfigurator::attach($logger, $event);

            $streams = $this->streamHandlers($logger);
            self::assertCount(1, $streams);
            self::assertSame($expected, $streams[0]->getLevel());
        });
    }

    /** @param callable(string): void $callback */
    private function withTempLogFile(callable $callback): void
    {
        $logFile = tempnam(sys_get_temp_dir(), 'phpdoc-log-test');
        self::assertIsString($logFile);

        try {
            $callback($logFile);
        } finally {
            @unlink($logFile);
        }
    }

    /** @return list<StreamHandler> */
    private function streamHandlers(MonologLogger $logger): array
    {
        return array_values(array_filter(
            $logger->getHandlers(),
            static fn ($h) => $h instanceof StreamHandler,
        ));
    }

    /** @param array<string, string|bool|null> $options */
    private function createConsoleEvent(
        array $options,
        int $verbosity = OutputInterface::VERBOSITY_NORMAL,
    ): ConsoleEvent {
        $definition = new InputDefinition([
            new InputOption('log', null, InputOption::VALUE_OPTIONAL),
        ]);

        $input = new ArrayInput($options, $definition);
        $output = new BufferedOutput($verbosity);

        return new ConsoleEvent(new Command('t'), $input, $output);
    }
}
