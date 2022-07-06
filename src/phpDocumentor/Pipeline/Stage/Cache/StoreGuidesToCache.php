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
    /** @var LoggerInterface */
    private $logger;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload): Payload
    {
        $configuration = $payload->getConfig();

        if (($configuration['phpdocumentor']['settings']['guides.enabled'] ?? false) === true) {
            $this->logger->info('Storing cache .. ');

            $this->commandBus->handle(
                new PersistCacheCommand(
                    ((string) $configuration['phpdocumentor']['paths']['cache']) . '/guides',
                    $configuration['phpdocumentor']['use-cache']
                )
            );

            $this->logger->info('OK');
        }

        return $payload;
    }
}
