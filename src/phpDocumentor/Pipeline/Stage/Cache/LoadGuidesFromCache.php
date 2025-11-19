<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Cache;

use League\Tactician\CommandBus;
use phpDocumentor\Guides\Handlers\LoadCacheCommand;
use phpDocumentor\Pipeline\Stage\Payload;
use Psr\Log\LoggerInterface;

final class LoadGuidesFromCache
{
    public function __construct(private readonly CommandBus $commandBus, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Payload $payload): Payload
    {
        $configuration = $payload->getConfig();
        $useCache = $configuration['phpdocumentor']['use_cache'];
        if ($useCache && ! $payload->getBuilder()->getProjectDescriptor()->getSettings()->isModified()) {
            $this->logger->info('Loading project from cache');

            $cacheFolder = $configuration['phpdocumentor']['paths']['cache'];
            $this->commandBus->handle(
                new LoadCacheCommand(
                    ((string) $cacheFolder) . '/guides',
                    $useCache,
                ),
            );
        }

        return $payload;
    }
}
