<?php

namespace phpDocumentor\Application\Stage\Cache;

use phpDocumentor\Application\Stage\Parser\Payload;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class LoadProjectDescriptorFromCache
{
    /**
     * @var ProjectDescriptorMapper
     */
    private $descriptorMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ProjectDescriptorMapper $descriptorMapper, LoggerInterface $logger)
    {
        $this->descriptorMapper = $descriptorMapper;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload)
    {
        $configuration = $payload->getConfig();
        if ($configuration['phpdocumentor']['use-cache']) {
            $this->log('Loading project from cache');
            $this->descriptorMapper->populate($payload->getBuilder()->getProjectDescriptor());
        }

        return $payload;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
