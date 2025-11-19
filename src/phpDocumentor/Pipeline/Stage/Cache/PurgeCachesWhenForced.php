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

use phpDocumentor\Pipeline\Stage\Payload;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

final class PurgeCachesWhenForced
{
    public function __construct(
        private readonly AdapterInterface $filesCache,
        private readonly AdapterInterface $descriptorsCache,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Payload $payload): Payload
    {
        $this->logger->info('Checking whether to purge cache');
        if (
            ! $payload->getConfig()['phpdocumentor']['use_cache']
            || $payload->getBuilder()->getProjectDescriptor()->getSettings()->isModified()
        ) {
            $this->logger->info('Purging cache');
            $this->filesCache->clear();
            $this->descriptorsCache->clear();
        }

        return $payload;
    }
}
