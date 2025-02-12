<?php

declare(strict_types=1);

namespace phpDocumentor\Console;

use Monolog\Handler\HandlerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

use function strtolower;

final class ConsoleLogHandler implements HandlerInterface
{
    /** @var array<string, int> */
    private array $verbosityLevelMap = [
        LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
    ];

    public function __construct(private OutputInterface&StyleInterface $output)
    {
    }

    public function isHandling(array $record): bool
    {
        return true;
    }

    public function handle(array $record): bool
    {
        if ($this->output->getVerbosity() >= $this->verbosityLevelMap[strtolower($record['level_name'])]) {
            $this->output->text($record['message']);
        }

        return true;
    }

    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->output->error($record);
        }
    }

    public function close(): void
    {
    }
}
