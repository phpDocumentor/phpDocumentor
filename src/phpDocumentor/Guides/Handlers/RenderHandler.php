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

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Transformer\Router\Router;

use function dirname;
use function str_replace;

final class RenderHandler
{
    /** @var Metas */
    private $metas;

    /** @var Renderer */
    private $renderer;

    /** @var Router */
    private $router;

    /** @var UrlGenerator */
    private $urlGenerator;

    public function __construct(
        Metas $metas,
        Renderer $renderer,
        Router $router,
        UrlGenerator $urlGenerator
    ) {
        $this->metas = $metas;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(RenderCommand $command): void
    {
        $origin = $command->getOrigin();
        $destinationPath = $command->getDestinationPath();
        $environment = $this->createEnvironment($destinationPath, $origin, $command->getTargetFileFormat(), $command->getDestination());

        $this->render($command->getDocumentationSet(), $environment, $command->getDestination());
    }

    private function render(
        GuideSetDescriptor $documentationSet,
        RenderContext $environment,
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

            $environment->setDestinationPath($destinationPath);
            $renderedOutput = $this->renderDocument(
                $descriptor,
                $environment,
                $documentationSet
            );
            $destination->put($destinationPath, $renderedOutput);
        }
    }

    private function renderDocument(
        DocumentDescriptor $descriptor,
        RenderContext $environment,
        GuideSetDescriptor $documentationSet
    ): string {
        $document = $descriptor->getDocumentNode();

        $environment->setDocument($document);
        $environment->setCurrentFileName($descriptor->getFile());
        // TODO: We assume there is one, but there may be multiple. Handling this correctly required rework on how
        // source locations are propagated.
        $sourcePath = $documentationSet->getSource()->paths()[0];

        $environment->setCurrentAbsolutePath($sourcePath . '/' . dirname($descriptor->getFile()));

        foreach ($descriptor->getLinks() as $link => $url) {
            $environment->setLink($link, $url);
        }

        return $this->renderer->renderDocument($document, $environment);
    }

    private function createEnvironment(
        string $outputFolder,
        FilesystemInterface $origin,
        string $outputFormat,
        FilesystemInterface $destination
    ): RenderContext {
        return new RenderContext(
            $outputFolder,
            $origin,
            $destination,
            $this->metas,
            $this->urlGenerator,
            $outputFormat
        );
    }
}
