<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\Builder\Scanner;

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
        $configuration = $command->getConfiguration();

        $extension = $configuration->getSourceFileExtension();
        $parseQueue = $this->scanner->scan($command->getOrigin(), $command->getDirectory(), $extension);

        $this->parseQueueProcessor->process(
            $configuration,
            $parseQueue,
            $command->getOrigin(),
            $command->getDirectory()
        );
    }
}
