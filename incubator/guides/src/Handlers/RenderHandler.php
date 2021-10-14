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

use IteratorAggregate;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\OutputFormats;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Transformer\Router\Router;
use Psr\Log\LoggerInterface;

use function array_merge;
use function dirname;
use function get_class;
use function iterator_to_array;
use function str_replace;

final class RenderHandler
{
    /** @var Metas */
    private $metas;

    /** @var Renderer */
    private $renderer;

    /** @var LoggerInterface */
    private $logger;

    /** @var Reference[] */
    private $references;

    /** @var Router */
    private $router;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var ReferenceBuilder */
    private $referenceRegistry;

    /** @var OutputFormats */
    private $outputFormats;

    /** @param IteratorAggregate<Reference> $references */
    public function __construct(
        Metas $metas,
        Renderer $renderer,
        LoggerInterface $logger,
        IteratorAggregate $references,
        Router $router,
        UrlGenerator $urlGenerator,
        ReferenceBuilder $referenceRegistry,
        OutputFormats $outputFormats
    ) {
        $this->metas = $metas;
        $this->renderer = $renderer;
        $this->logger = $logger;
        $this->references = iterator_to_array($references);
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
        $this->referenceRegistry = $referenceRegistry;
        $this->outputFormats = $outputFormats;
    }

    public function handle(RenderCommand $command): void
    {
        $origin = $command->getOrigin();
        $initialHeaderLevel = $command->getDocumentationSet()->getInitialHeaderLevel();
        $destinationPath = $command->getDestinationPath();
        $targetFileFormat = $command->getTargetFileFormat();

        $environment = $this->createEnvironment($destinationPath, $initialHeaderLevel, $origin);

        $format = $this->outputFormats->get($targetFileFormat);
        $nodeRendererFactory = $format->getNodeRendererFactory();
        $environment->setNodeRendererFactory($nodeRendererFactory);

        $this->render($nodeRendererFactory, $command->getDocumentationSet(), $environment, $command->getDestination());
    }

    private function render(
        NodeRendererFactory $nodeRendererFactory,
        GuideSetDescriptor $documentationSet,
        Environment $environment,
        FilesystemInterface $destination
    ): void {
        /** @var DocumentDescriptor $descriptor */
        foreach ($documentationSet->getDocuments() as $descriptor) {
            // TODO: This is a hack; I want to rework path handling for guides as the Environment, for example,
            //       has a plethora of 'em.
            $destinationPath = str_replace(
                '//',
                '/',
                $documentationSet->getOutput() . '/' . $this->router->generate($descriptor)
            );

            $renderedOutput = $this->renderDocument(
                $nodeRendererFactory,
                $descriptor,
                $destinationPath,
                $environment,
                $documentationSet
            );
            $destination->put($destinationPath, $renderedOutput);
        }
    }

    /**
     * @param array<Reference> $references
     */
    private function initReferences(array $references): void
    {
        $references = array_merge(
            [
                new Doc(),
                new Doc('ref', true),
            ],
            $references
        );

        foreach ($references as $reference) {
            $this->referenceRegistry->registerTypeOfReference($reference);
        }
    }

    private function renderDocument(
        NodeRendererFactory $nodeRendererFactory,
        DocumentDescriptor $descriptor,
        string $destinationPath,
        Environment $environment,
        GuideSetDescriptor $documentationSet
    ): string {
        $document = $descriptor->getDocumentNode();
        $this->referenceRegistry->scope($document);

        $directory = dirname($destinationPath);

        $environment->setCurrentFileName($descriptor->getFile());
        // TODO: We assume there is one, but there may be multiple. Handling this correctly required rework on how
        // source locations are propagated.
        $sourcePath = $documentationSet->getSource()->paths()[0];

        $environment->setCurrentAbsolutePath($sourcePath . '/' . dirname($descriptor->getFile()));
        $environment->setCurrentDirectory($directory);

        foreach ($descriptor->getLinks() as $link => $url) {
            $environment->setLink($link, $url);
        }

        foreach ($descriptor->getVariables() as $key => $value) {
            $environment->setVariable($key, $value);
        }

        /** @var FullDocumentNodeRenderer $renderer */
        $renderer = $nodeRendererFactory->get(get_class($document));
        $this->renderer->setGuidesEnvironment($environment);
        $this->renderer->setDestination($destinationPath);

        return $renderer->renderDocument($document, $environment);
    }

    private function createEnvironment(
        string $outputFolder,
        int $initialHeaderLevel,
        FilesystemInterface $origin
    ): Environment {
        $environment = new Environment(
            $outputFolder,
            $initialHeaderLevel,
            $this->renderer,
            $this->logger,
            $origin,
            $this->metas,
            $this->urlGenerator
        );
        $this->initReferences($this->references);

        return $environment;
    }
}
