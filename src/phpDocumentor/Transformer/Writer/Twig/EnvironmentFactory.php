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
use phpDocumentor\FileSystem\Path;
use phpDocumentor\Guides\Graphs\Twig\UmlExtension;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Template;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

class EnvironmentFactory
{
    private Path|null $templateOverridesAt = null;

    /** @param string[] $guidesTemplateBasePath */
    public function __construct(
        private readonly LinkRenderer $renderer,
        private readonly ConverterInterface $markDownConverter,
        private readonly AssetsExtension $assetsExtension,
        private readonly UmlExtension $umlExtension,
        private readonly RelativePathToRootConverter $relativePathToRootConverter,
        private readonly PathBuilder $pathBuilder,
        private readonly array $guidesTemplateBasePath,
    ) {
    }

    public function withTemplateOverridesAt(Path $path): void
    {
        $this->templateOverridesAt = $path;
    }

    public function create(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Template $template,
    ): Environment {
        $loaders = [];
        if ($this->templateOverridesAt instanceof Path) {
            $loaders[] = new TwigFilesystemLoader([(string) $this->templateOverridesAt]);
        }

        $loaders[] = new FileSystemLoader($template->files(), '', 'base');
        $loaders[] = new FileSystemLoader($template->files(), 'guides', 'base');
        $loaders[] = new TwigFilesystemLoader($this->guidesTemplateBasePath);

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
        Environment $twigEnvironment,
    ): void {
        $extension = new Extension(
            $project,
            $documentationSet,
            $this->markDownConverter,
            $this->renderer,
            $this->relativePathToRootConverter,
            $this->pathBuilder,
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
