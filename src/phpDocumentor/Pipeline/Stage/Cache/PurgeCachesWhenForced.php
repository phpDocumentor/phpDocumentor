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
    /** @var AdapterInterface  */
    private $filesCache;

    /** @var AdapterInterface  */
    private $descriptorsCache;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(
        AdapterInterface $filesCache,
        AdapterInterface $descriptorsCache,
        LoggerInterface $logger
    ) {
        $this->filesCache = $filesCache;
        $this->descriptorsCache = $descriptorsCache;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload): Payload
    {
        $this->logger->info('Checking whether to purge cache');
        if (
            !$payload->getConfig()['phpdocumentor']['use-cache']
            || $payload->getBuilder()->getProjectDescriptor()->getSettings()->isModified()
        ) {
            $this->logger->info('Purging cache');
            $this->filesCache->clear();
            $this->descriptorsCache->clear();
        }

        return $payload;
    }
}
