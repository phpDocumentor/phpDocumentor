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

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\ConverterInterface;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Graphs\Twig\UmlExtension;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Template;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class EnvironmentFactory
{
    private LinkRenderer $renderer;
    private ?Path $templateOverridesAt = null;
    private ConverterInterface $markDownConverter;
    private AssetsExtension $assetsExtension;
    private UmlExtension $umlExtension;
    private RelativePathToRootConverter $relativePathToRootConverter;
    private PathBuilder $pathBuilder;

    public function __construct(
        LinkRenderer $renderer,
        ConverterInterface $markDownConverter,
        AssetsExtension $assetsExtension,
        UmlExtension $umlExtension,
        RelativePathToRootConverter $relativePathToRootConverter,
        PathBuilder $pathBuilder,
        private readonly array $guidesTemplateBasePath
    ) {
        $this->renderer = $renderer;
        $this->markDownConverter = $markDownConverter;
        $this->assetsExtension = $assetsExtension;
        $this->umlExtension = $umlExtension;
        $this->relativePathToRootConverter = $relativePathToRootConverter;
        $this->pathBuilder = $pathBuilder;
    }

    public function withTemplateOverridesAt(Path $path): void
    {
        $this->templateOverridesAt = $path;
    }

    public function create(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Template $template
    ): Environment {
        $mountManager = $template->files();

        $loaders = [];
        if ($this->templateOverridesAt instanceof Path) {
            $loaders[] = new FilesystemLoader([(string) $this->templateOverridesAt]);
        }

        $loaders[] = new FlySystemLoader($mountManager->getFilesystem('template'), '', 'base');
        $loaders[] = new FlySystemLoader($mountManager->getFilesystem('template'), 'guides', 'base');
        $loaders[] = new FlySystemLoader($mountManager->getFilesystem('templates'));
        $loaders[] = new FilesystemLoader($this->guidesTemplateBasePath);

        $env = new Environment(new ChainLoader($loaders));

        $this->addPhpDocumentorExtension($project, $documentationSet, $env);
        $this->enableDebug($env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     */
    private function addPhpDocumentorExtension(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Environment $twigEnvironment
    ): void {
        $extension = new Extension(
            $project,
            $documentationSet,
            $this->markDownConverter,
            $this->renderer,
            $this->relativePathToRootConverter,
            $this->pathBuilder
        );
        $twigEnvironment->addExtension($extension);
        $twigEnvironment->addExtension($this->assetsExtension);
        $twigEnvironment->addExtension($this->umlExtension);
    }

    private function enableDebug(Environment $twigEnvironment): void
    {
        $twigEnvironment->setCache(false);
        $twigEnvironment->enableDebug();
        $twigEnvironment->enableAutoReload();
        $twigEnvironment->addExtension(new DebugExtension());
    }
}
