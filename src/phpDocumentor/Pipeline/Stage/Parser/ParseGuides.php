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
use phpDocumentor\Guides\DocumentCollector;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Pipeline\Stage\Payload;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ParseGuides
{
    /** @param EventDispatcherInterface&EventDispatcher $eventDispatcher */
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly LoggerInterface $logger,
        private readonly FlySystemFactory $flySystemFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(Payload $payload): Payload
    {
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
        $sourcePath = (string) ($guideDocumentationSet->getSource()->paths()[0] ?? '');

        $listener = new DocumentCollector($guideDocumentationSet);

        $this->eventDispatcher->addListener(PostParseDocument::class, $listener);
        $this->commandBus->handle(
            new ParseDirectoryCommand(
                $origin,
                $sourcePath,
                $guideDocumentationSet->getInputFormat(),
                $guideDocumentationSet->getGuidesProjectNode(),
            ),
        );
        $this->eventDispatcher->removeListener(PostParseDocument::class, $listener);

        return $payload;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $priority   The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
