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

namespace phpDocumentor\FlowService\Guide;

use InvalidArgumentException;
use League\Tactician\CommandBus;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\DocumentCollector;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Metas;
use phpDocumentor\FlowService\Parser as ParserInterface;
use phpDocumentor\FileSystem\FlySystemFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class Parser implements ParserInterface
{
    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    /** @var FlySystemFactory */
    private $flySystemFactory;
    /** @var EventDispatcherInterface&EventDispatcher */
    private EventDispatcherInterface $eventDispatcher;
    private Metas $metas;

    /** @param EventDispatcherInterface&EventDispatcher $eventDispatcher */
    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger,
        FlySystemFactory $flySystemFactory,
        EventDispatcherInterface $eventDispatcher,
        Metas $metas
    ) {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->flySystemFactory = $flySystemFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->metas = $metas;
    }

    public function operate(DocumentationSetDescriptor $documentationSet): void
    {
        if (!$documentationSet instanceof GuideSetDescriptor) {
            throw new InvalidArgumentException('Invalid documentation set');
        }

        $this->log('Parsing guides', LogLevel::NOTICE);

        $dsn = $documentationSet->getSource()->dsn();
        $origin = $this->flySystemFactory->create($dsn);
        $sourcePath = (string) ($documentationSet->getSource()->paths()[0] ?? '');

        $listener = new DocumentCollector(
            $this->metas,
            $documentationSet,
            $this->logger
        );

        $this->eventDispatcher->addListener(PostParseDocument::class, $listener);
        $this->commandBus->handle(
            new ParseDirectoryCommand($origin, $sourcePath, $documentationSet->getInputFormat())
        );
        $this->eventDispatcher->removeListener(PostParseDocument::class, $listener);
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
