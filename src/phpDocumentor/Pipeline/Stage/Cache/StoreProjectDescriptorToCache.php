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

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Pipeline\Stage\Parser\Payload;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class StoreProjectDescriptorToCache
{
    /** @var ProjectDescriptorMapper */
    private $descriptorMapper;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ProjectDescriptorMapper $descriptorMapper, LoggerInterface $logger)
    {
        $this->descriptorMapper = $descriptorMapper;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload): Payload
    {
        $projectDescriptor = $payload->getBuilder()->getProjectDescriptor();
        $this->log('Storing cache .. ', LogLevel::NOTICE);
        $projectDescriptor->getSettings()->clearModifiedFlag();
        $this->descriptorMapper->save($projectDescriptor);
        $this->log('OK');

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
