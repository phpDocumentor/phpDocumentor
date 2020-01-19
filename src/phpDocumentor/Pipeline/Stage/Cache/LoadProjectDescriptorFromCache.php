<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Cache;

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Pipeline\Stage\Parser\Payload;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class LoadProjectDescriptorFromCache
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

    public function __invoke(Payload $payload) : Payload
    {
        $configuration = $payload->getConfig();
        if ($configuration['phpdocumentor']['use-cache']
            && !$payload->getBuilder()->getProjectDescriptor()->getSettings()->isModified()
        ) {
            $this->log('Loading project from cache');
            $this->descriptorMapper->populate($payload->getBuilder()->getProjectDescriptor());
        }

        return $payload;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []) : void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
