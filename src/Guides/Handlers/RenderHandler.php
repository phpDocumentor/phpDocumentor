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

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use IteratorAggregate;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Documents;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Renderer;
use Psr\Log\LoggerInterface;
use function array_merge;
use function dirname;
use function get_class;
use function iterator_to_array;
use function sprintf;

final class RenderHandler
{
    /** @var Documents */
    private $documents;

    /** @var Metas */
    private $metas;

    /** @var Renderer */
    private $renderer;

    /** @var LoggerInterface */
    private $logger;

    /** @var Reference[] */
    private $references;

    /** @param IteratorAggregate<Reference> $references */
    public function __construct(
        Metas $metas,
        Documents $documents,
        Renderer $renderer,
        LoggerInterface $logger,
        IteratorAggregate $references
    ) {
        $this->metas = $metas;
        $this->documents = $documents;
        $this->renderer = $renderer;
        $this->logger = $logger;
        $this->references = iterator_to_array($references);
    }

    public function handle(RenderCommand $command) : void
    {
        $environment = new Environment(
            $command->getConfiguration(),
            $this->renderer,
            $this->logger,
            $command->getDestination(),
            $this->metas
        );

        $nodeRendererFactory = $command->getConfiguration()->getFormat()->getNodeRendererFactory($environment);
        $environment->setNodeRendererFactory($nodeRendererFactory);
        $this->render($environment, $command->getDestination());
    }

    private function render(Environment $environment, FilesystemInterface $destination) : void
    {
        $this->initReferences($environment, $this->references);
        foreach ($this->documents->getAll() as $file => $document) {
            $target = $this->getTargetOf($file);

            $directory = dirname($target);

            if ($destination->has($directory)) {
                $destination->createDir($directory);
            }

            $environment->setCurrentFileName($file);
            $environment->setCurrentDirectory($directory);

            /** @var FullDocumentNodeRenderer $renderer */
            $renderer = $environment->getNodeRendererFactory()->get(get_class($document));
            $this->renderer->setGuidesEnvironment($environment);
            $this->renderer->setDestination($environment->getUrl());

            $destination->put($target, $renderer->renderDocument($document, $environment));
        }
    }

    /**
     * @param array<Reference> $references
     */
    private function initReferences(Environment $environment, array $references) : void
    {
        $references = array_merge(
            [
                new Doc(),
                new Doc('ref', true),
            ],
            $references
        );

        foreach ($references as $reference) {
            $environment->registerReference($reference);
        }
    }

    private function getTargetOf(string $file) : string
    {
        $metaEntry = $this->metas->get($file);

        if ($metaEntry === null) {
            throw new InvalidArgumentException(sprintf('Could not find target file for %s', $file));
        }

        return $metaEntry->getUrl();
    }
}
