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

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Parser\FileCollector;
use Psr\Log\LoggerInterface;

use function count;

final class CollectFiles
{
    public function __construct(
        private readonly FileCollector $fileCollector,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ApiSetPayload $payload): ApiSetPayload
    {
        $apiConfig = $payload->getApiSet()->getSettings();
        $this->logger->info('Collecting files from ' . $apiConfig->source()->dsn());

        $files = $this->fileCollector->getFiles(
            $apiConfig->source()->dsn(),
            $apiConfig->source()->globPatterns(),
            $apiConfig['ignore'],
            $apiConfig['extensions'],
        );

        $payload = $payload->withFiles($files);

        $this->logger->info('OK');

        if (count($payload->getFiles()) === 0) {
            $this->logger->warning('Your documentationset seems to be empty!');
        }

        return $payload;
    }
}
