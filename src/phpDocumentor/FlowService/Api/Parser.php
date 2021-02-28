<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService\Api;

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\FlowService\FlowService;
use phpDocumentor\Parser\FileCollector;
use phpDocumentor\Parser\Parser as ApiParser;
use phpDocumentor\Parser\Middleware\ReEncodingMiddleware;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\Project;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class Parser implements FlowService
{
    /** @var ApiParser */
    private $parser;

    /** @var LoggerInterface */
    private $logger;

    /** @var ReEncodingMiddleware */
    private $reEncodingMiddleware;

    /** @var FileCollector */
    private $fileCollector;

    /** @var ApiSetDescriptorBuilder */
    private $builder;

    public function __construct(
        ApiSetDescriptorBuilder $builder,
        FileCollector $fileCollector,
        ApiParser $parser,
        LoggerInterface $logger,
        ReEncodingMiddleware $reEncodingMiddleware
    ) {
        $this->parser = $parser;
        $this->logger = $logger;
        $this->reEncodingMiddleware = $reEncodingMiddleware;
        $this->fileCollector = $fileCollector;
        $this->builder = $builder;
    }

    public function operate(DocumentationSetDescriptor $documentationSet): void
    {
        if (!$documentationSet instanceof ApiSetDescriptor) {
            throw new \InvalidArgumentException('Invalid documentation set');
        }

        $this->builder->reset();
        $this->builder->setApiSpecification($documentationSet->getSettings());

        $files = $this->collectFiles($documentationSet->getSettings());
        $project = $this->createProject($documentationSet->getSettings(), $files);

        $this->builder->setProject($project);
        $this->builder->createDescriptors($documentationSet);
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
