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
use phpDocumentor\Descriptor\Interfaces\DocumentationSetInterface;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\FileSystem\Path;
use phpDocumentor\Transformer\Template;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface as TwigExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

use function is_dir;
use function sprintf;

class EnvironmentFactory
{
    private Path|null $templateOverridesAt = null;

    /**
     * @param string[] $guidesTemplateBasePath,
     * @param iterable<TwigExtension> $extensions
     * @param array<string, array{path: string, keys: string[]}> $extensionTemplates indexed by the alias of the
     *     extension that provides them
     */
    public function __construct(
        private readonly LinkRenderer $renderer,
        private readonly ConverterInterface $markDownConverter,
        private readonly RelativePathToRootConverter $relativePathToRootConverter,
        private readonly PathBuilder $pathBuilder,
        private iterable $extensions,
        private readonly array $guidesTemplateBasePath,
        private readonly string $globalTemplatesPath,
        private readonly array $extensionTemplates = [],
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function withTemplateOverridesAt(Path $path): void
    {
        $this->templateOverridesAt = $path;
    }

    public function create(
        ProjectInterface $project,
        DocumentationSetInterface $documentationSet,
        Template $template,
    ): Environment {
        $loaders = [];
        if ($this->templateOverridesAt instanceof Path) {
            $loaders[] = new TwigFilesystemLoader([(string) $this->templateOverridesAt]);
        }

        $loaders[] = new FileSystemLoader($template->files(), '', 'base');
        $loaders[] = new FileSystemLoader($template->files(), 'guides', 'base');
        $loaders[] = new TwigFilesystemLoader($this->globalTemplatesPath, '');
        $loaders[] = new TwigFilesystemLoader($this->guidesTemplateBasePath);

        $extensionTemplatesLoader = $this->createExtensionTemplatesLoader();
        if ($extensionTemplatesLoader !== null) {
            $loaders[] = $extensionTemplatesLoader;
        }

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
        ProjectInterface $project,
        DocumentationSetInterface $documentationSet,
        Environment $twigEnvironment,
    ): void {
        $extension = new Extension(
            $project,
            $documentationSet,
            $this->markDownConverter,
            $this->renderer,
            $this->relativePathToRootConverter,
            $this->pathBuilder,
            new MetadataTemplateResolver($this->extensionTemplates, $this->logger),
        );
        $twigEnvironment->addExtension($extension);
    }

    private function createExtensionTemplatesLoader(): TwigFilesystemLoader|null
    {
        $loader = new TwigFilesystemLoader();
        $hasPaths = false;
        foreach ($this->extensionTemplates as $alias => $templates) {
            if (! is_dir($templates['path'])) {
                $this->logger->warning(
                    sprintf(
                        'The template path "%s" of extension "%s" is not a directory, its templates are ignored',
                        $templates['path'],
                        $alias,
                    ),
                );

                continue;
            }

            $loader->addPath($templates['path'], $alias);
            $hasPaths = true;
        }

        return $hasPaths ? $loader : null;
    }

    private function enableDebug(Environment $twigEnvironment): void
    {
        $twigEnvironment->setCache(false);
        $twigEnvironment->enableDebug();
        $twigEnvironment->enableAutoReload();
        $twigEnvironment->addExtension(new DebugExtension());
    }
}
