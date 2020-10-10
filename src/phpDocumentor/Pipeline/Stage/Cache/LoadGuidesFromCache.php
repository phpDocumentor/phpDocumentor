<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Cache;

use League\Tactician\CommandBus;
use phpDocumentor\Guides\LoadCacheCommand;
use phpDocumentor\Pipeline\Stage\Parser\Payload;
use Psr\Log\LoggerInterface;

final class LoadGuidesFromCache
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Payload $payload) : Payload
    {
        $configuration = $payload->getConfig();
        if ($configuration['phpdocumentor']['settings']['guides.enabled'] === true) {
            if ($configuration['phpdocumentor']['use-cache']
                && !$payload->getBuilder()->getProjectDescriptor()->getSettings()->isModified()
            ) {
                $this->logger->info('Loading project from cache');
                $this->commandBus->handle(new LoadCacheCommand(
                    ((string) $configuration['phpdocumentor']['paths']['cache']) . '/guides',
                    $configuration['phpdocumentor']['use-cache'])
                );
            }
        }

        return $payload;
    }
}
