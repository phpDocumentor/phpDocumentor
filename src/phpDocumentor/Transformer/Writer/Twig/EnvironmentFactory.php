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
use phpDocumentor\Path;
use phpDocumentor\Transformer\Template;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface as TwigExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class EnvironmentFactory
{
    private Path|null $templateOverridesAt = null;

    /**
     * @param string[] $guidesTemplateBasePath,
     * @param iterable<TwigExtension> $extensions
     */
    public function __construct(
        private readonly LinkRenderer $renderer,
        private readonly ConverterInterface $markDownConverter,
        private readonly RelativePathToRootConverter $relativePathToRootConverter,
        private readonly PathBuilder $pathBuilder,
        private iterable $extensions,
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
        foreach ($this->extensions as $extension) {
            $env->addExtension($extension);
        }

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
    }

    private function enableDebug(Environment $twigEnvironment): void
    {
        $twigEnvironment->setCache(false);
        $twigEnvironment->enableDebug();
        $twigEnvironment->enableAutoReload();
        $twigEnvironment->addExtension(new DebugExtension());
    }
}
