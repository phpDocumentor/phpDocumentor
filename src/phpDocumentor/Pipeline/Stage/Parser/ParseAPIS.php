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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Parser\FileCollector;
use phpDocumentor\Parser\Middleware\ReEncodingMiddleware;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\Project;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function current;

final class ParseAPIS
{
    /** @var Parser */
    private $parser;

    /** @var LoggerInterface */
    private $logger;

    /** @var ReEncodingMiddleware */
    private $reEncodingMiddleware;

    /** @var FileCollector */
    private $fileCollector;

    public function __construct(
        FileCollector $fileCollector,
        Parser $parser,
        LoggerInterface $logger,
        ReEncodingMiddleware $reEncodingMiddleware
    ) {
        $this->parser = $parser;
        $this->logger = $logger;
        $this->reEncodingMiddleware = $reEncodingMiddleware;
        $this->fileCollector = $fileCollector;
    }

    public function __invoke(Payload $payload) : Payload
    {
        /** @var VersionSpecification $version */
        $version = current($payload->getConfig()['phpdocumentor']['versions']);
        $builder = $payload->getBuilder();

        foreach ($version->getApi() as $apiSpecification) {
            $files = $this->collectFiles($apiSpecification);
            $project = $this->createProject($apiSpecification, $files);

            $builder->setApiSpecification($apiSpecification);
            $builder->setVisibility($apiSpecification->calculateVisiblity());
            $builder->createApiDocumentationSet($project);
        }

        return $payload;
    }

    /** @return File[] */
    private function collectFiles(ApiSpecification $apiSpecification): array
    {
        $this->log('Collecting files from ' . $apiSpecification['source']['dsn']);

        $files = $this->fileCollector->getFiles(
            $apiSpecification['source']['dsn'],
            $apiSpecification['source']['paths'],
            $apiSpecification['ignore'],
            $apiSpecification['extensions']
        );

        if (count($files) === 0) {
            $this->log('Your project seems to be empty!', LogLevel::WARNING);
            $this->log('Where are the files??!!!', LogLevel::DEBUG);
        }

        return $files;
    }

    /** @param File[] $files */
    private function createProject(ApiSpecification $apiSpecification, array $files): Project
    {
        $this->reEncodingMiddleware->withEncoding($apiSpecification['encoding']);
        $this->parser->setMarkers($apiSpecification['markers']);
        $this->parser->setValidate($apiSpecification['validate']);
        $this->parser->setDefaultPackageName($apiSpecification['default-package-name']);

        $this->log('Parsing files', LogLevel::NOTICE);
        return $this->parser->parse($files);
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
