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

        /** @var DocumentDescriptor $descriptor */
        foreach ($command->getDocumentationSet()->getDocuments() as $descriptor) {
            // TODO: This is a hack; I want to rework path handling for guides as the Environment, for example,
            //       has a plethora of 'em.
            $destinationPath = str_replace(
                '//',
                '/',
                $command->getDocumentationSet()->getOutputLocation() . '/' . $this->router->generate($descriptor)
            );

            $document = $descriptor->getDocumentNode();

            $environment = RenderContext::forDocument(
                $document,
                $origin,
                $command->getDestination(),
                $destinationPath,
                $this->metas,
                $this->urlGenerator,
                $command->getTargetFileFormat()
            );

            foreach ($descriptor->getLinks() as $link => $url) {
                $environment->setLink($link, $url);
            }

            $environment->getDestination()->put($destinationPath, $this->renderer->renderDocument($document, $environment));
        }
    }
}
