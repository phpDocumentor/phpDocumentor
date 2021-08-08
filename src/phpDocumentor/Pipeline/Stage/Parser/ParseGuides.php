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

use League\Tactician\CommandBus;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\RestructuredText\ParseDirectoryCommand;
use phpDocumentor\Parser\FlySystemFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class ParseGuides
{
    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    /** @var iterable<Format> */
    private $outputFormats;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /**
     * @param iterable<Format> $outputFormats
     */
    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger,
        FlySystemFactory $flySystemFactory,
        iterable $outputFormats
    ) {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->outputFormats = $outputFormats;
        $this->flySystemFactory = $flySystemFactory;
    }

    public function __invoke(Payload $payload): Payload
    {
        if (($payload->getConfig()['phpdocumentor']['settings']['guides.enabled'] ?? false) !== true) {
            return $payload;
        }

        /*
         * For now settings of the first guides are used.
         * We need to change this later, when we accept more different things
         */
        $version = $payload->getBuilder()->getProjectDescriptor()->getVersions()->get(0);
        $guideDocumentationSet = null;
        foreach ($version->getDocumentationSets() as $set) {
            if ($set instanceof GuideSetDescriptor) {
                $guideDocumentationSet = $set;
                break;
            }
        }

        if ($guideDocumentationSet === null) {
            return $payload;
        }

        $this->log('Parsing guides', LogLevel::NOTICE);

        $dsn = $guideDocumentationSet->getSource()->dsn();
        $origin = $this->flySystemFactory->create($dsn);
        $directory = $guideDocumentationSet->getSource()->paths()[0] ?? '';

        $configuration = new Configuration(
            $guideDocumentationSet->getInputFormat(),
            $this->outputFormats
        );
        $configuration->setOutputFolder($guideDocumentationSet->getOutput());

        $this->commandBus->handle(
            new ParseDirectoryCommand($guideDocumentationSet, $configuration, $origin, (string) $directory)
        );

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
