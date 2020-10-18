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

use phpDocumentor\Parser\Middleware\ReEncodingMiddleware;
use phpDocumentor\Parser\Parser;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function current;

final class ParseFiles
{
    /** @var Parser */
    private $parser;

    /** @var LoggerInterface */
    private $logger;

    /** @var ReEncodingMiddleware */
    private $reEncodingMiddleware;

    public function __construct(
        Parser $parser,
        LoggerInterface $logger,
        ReEncodingMiddleware $reEncodingMiddleware
    ) {
        $this->parser = $parser;
        $this->logger = $logger;
        $this->reEncodingMiddleware = $reEncodingMiddleware;
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
        $builder->setIgnoredTags($apiConfig['ignore-tags']);
        $this->reEncodingMiddleware->withEncoding($apiConfig['encoding']);

        $this->parser->setMarkers($apiConfig['markers']);
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
