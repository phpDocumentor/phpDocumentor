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
use Psr\Log\LogLevel;

use function count;

final class CollectFiles
{
    /** @var FileCollector */
    private $fileCollector;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(FileCollector $fileCollector, LoggerInterface $logger)
    {
        $this->fileCollector = $fileCollector;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload): Payload
    {
        foreach ($payload->getApiConfigs() as $apiConfig) {
            $this->log('Collecting files from ' . $apiConfig->source()->dsn());

            $files = $this->fileCollector->getFiles(
                $apiConfig->source()->dsn(),
                $apiConfig->source()->globPatterns(),
                $apiConfig['ignore'],
                $apiConfig['extensions']
            );

            $payload = $payload->withFiles($files);
        }

        $this->log('OK');

        if (count($payload->getFiles()) === 0) {
            $this->log('Your project seems to be empty!', LogLevel::WARNING);
            $this->log('Where are the files??!!!', LogLevel::DEBUG);
        }

        return $payload;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
