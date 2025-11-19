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

namespace phpDocumentor\Pipeline\Stage\Cache;

use League\Tactician\CommandBus;
use phpDocumentor\Guides\Handlers\PersistCacheCommand;
use phpDocumentor\Pipeline\Stage\Payload;
use Psr\Log\LoggerInterface;

final class StoreGuidesToCache
{
    public function __construct(private readonly CommandBus $commandBus, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Payload $payload): Payload
    {
        $configuration = $payload->getConfig();

        $this->logger->info('Storing cache .. ');

        $this->commandBus->handle(
            new PersistCacheCommand(
                ((string) $configuration['phpdocumentor']['paths']['cache']) . '/guides',
                $configuration['phpdocumentor']['use_cache'],
            ),
        );

        $this->logger->info('OK');

        return $payload;
    }
}
