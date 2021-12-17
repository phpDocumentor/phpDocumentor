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
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Transformer\Router\Router;

use function array_merge;
use function dirname;
use function iterator_to_array;
use function str_replace;

final class RenderHandler
{
    /** @var Metas */
    private $metas;

    /** @var Renderer */
    private $renderer;

    /** @var Reference[] */
    private $references;

    /** @var Router */
    private $router;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var ReferenceBuilder */
    private $referenceRegistry;

    /** @param IteratorAggregate<Reference> $references */
    public function __construct(
        Metas $metas,
        Renderer $renderer,
        IteratorAggregate $references,
        Router $router,
        UrlGenerator $urlGenerator,
        ReferenceBuilder $referenceRegistry
    ) {
        $this->metas = $metas;
        $this->renderer = $renderer;
        $this->references = iterator_to_array($references);
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
        $this->referenceRegistry = $referenceRegistry;
    }

    public function handle(RenderCommand $command): void
    {
        $origin = $command->getOrigin();
        $destinationPath = $command->getDestinationPath();
        $environment = $this->createEnvironment($destinationPath, $origin);

        $this->render($command->getDocumentationSet(), $environment, $command->getDestination());
    }

    private function render(
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
                $documentationSet->getOutputLocation() . '/' . $this->router->generate($descriptor)
            );

            $renderedOutput = $this->renderDocument(
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
        DocumentDescriptor $descriptor,
        string $destinationPath,
        Environment $environment,
        GuideSetDescriptor $documentationSet
    ): string {
        $document = $descriptor->getDocumentNode();
        $this->referenceRegistry->scope($document);

        $environment->setCurrentFileName($descriptor->getFile());
        // TODO: We assume there is one, but there may be multiple. Handling this correctly required rework on how
        // source locations are propagated.
        $sourcePath = $documentationSet->getSource()->paths()[0];

        $environment->setCurrentAbsolutePath($sourcePath . '/' . dirname($descriptor->getFile()));

        foreach ($descriptor->getLinks() as $link => $url) {
            $environment->setLink($link, $url);
        }

        foreach ($descriptor->getVariables() as $key => $value) {
            $environment->setVariable($key, $value);
        }

        $this->renderer->setGuidesEnvironment($environment);
        $this->renderer->setDestination($destinationPath);

        return $this->renderer->renderDocument($document, $environment);
    }

    private function createEnvironment(
        string $outputFolder,
        FilesystemInterface $origin
    ): Environment {
        $environment = new Environment(
            $outputFolder,
            $origin,
            $this->metas,
            $this->urlGenerator
        );
        $this->initReferences($this->references);

        return $environment;
    }
}
