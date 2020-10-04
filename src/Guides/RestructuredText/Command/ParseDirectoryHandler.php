<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\Builder\Scanner;
use phpDocumentor\Guides\RestructuredText\Configuration;

final class ParseDirectoryHandler
{
    /** @var Scanner */
    private $scanner;

    /** @var ParseQueueProcessor */
    private $parseQueueProcessor;

    public function __construct(Scanner $scanner, ParseQueueProcessor $parseQueueProcessor)
    {
        $this->scanner = $scanner;
        $this->parseQueueProcessor = $parseQueueProcessor;
    }

    public function handle(ParseDirectoryCommand $command)
    {
        $kernel = $command->getKernel();

        $extension = $kernel->getConfiguration()->getSourceFileExtension();
        $parseQueue = $this->scanner->scan($command->getDirectory(), $extension);

        $this->parseQueueProcessor->process(
            $kernel,
            $parseQueue,
            $command->getDirectory()
        );
    }
}
