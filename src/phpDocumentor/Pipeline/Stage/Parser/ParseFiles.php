<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Parser\Parser;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use function current;

final class ParseFiles
{
    /** @var Parser */
    private $parser;

    /** @var LoggerInterface */
    private $logger;

    /** @var AdapterInterface */
    private $filesCache;

    /** @var AdapterInterface */
    private $descriptorsCache;

    public function __construct(
        Parser $parser,
        LoggerInterface $logger,
        AdapterInterface $filesCache,
        AdapterInterface $descriptorsCache
    ) {
        $this->parser = $parser;
        $this->logger = $logger;
        $this->filesCache = $filesCache;
        $this->descriptorsCache = $descriptorsCache;
    }

    public function __invoke(Payload $payload) : Payload
    {
        /*
         * For now settings of the first api are used.
         * We need to change this later, when we accept more different things
        */
        $apiConfig = current($payload->getApiConfigs());

        $builder = $payload->getBuilder();
        $builder->setVisibility($apiConfig);
        $builder->setMarkers($apiConfig['markers']);
        $builder->setIncludeSource($apiConfig['include-source']);

        if ($builder->getProjectDescriptor()->getSettings()->isModified()) {
            $this->filesCache->clear();
            $this->descriptorsCache->clear();
            $this->log(
                'One of the project\'s settings have changed, forcing a complete rebuild',
                LogLevel::NOTICE
            );
        }

        $this->parser->setEncoding($apiConfig['encoding']);
        $this->parser->setMarkers($apiConfig['markers']);
        $this->parser->setIgnoredTags($apiConfig['ignore-tags']);
        $this->parser->setValidate($apiConfig['validate']);
        $this->parser->setDefaultPackageName($apiConfig['default-package-name']);

        $this->log('Parsing files', LogLevel::NOTICE);
        $project = $this->parser->parse($payload->getFiles());
        $payload->getBuilder()->build($project);

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
