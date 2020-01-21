<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides;

use Doctrine\RST\Configuration as RSTParserConfiguration;
use Doctrine\RST\Directives\Directive as Directive;
use Doctrine\RST\Kernel;
use Doctrine\RST\References\Reference;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Writer\Twig\Extension;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use Twig\Loader\FilesystemLoader;

final class KernelFactory
{
    /** @var string */
    private $globalTemplatesPath;

    /** @var string */
    private $globalCachePath;

    /** @var Directive[] */
    private $directives;

    /** @var Reference[] */
    private $references;

    /** @var LinkRenderer */
    private $linkRenderer;

    public function __construct(
        string $globalTemplatesPath,
        string $globalCachePath,
        LinkRenderer $linkRenderer,
        iterable $directives = [],
        iterable $references = []
    ) {
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->globalCachePath = $globalCachePath;
        $this->directives = $directives;
        $this->references = $references;
        $this->linkRenderer = $linkRenderer;
    }

    public function createKernel(ProjectDescriptor $projectDescriptor, BuildContext $buildContext) : Kernel
    {
        $configuration = new RSTParserConfiguration();
        $configuration->setCustomTemplateDirs([ $this->globalTemplatesPath . '/guides' ]);
        $configuration->setCacheDir(sprintf('%s/guide-cache', $this->globalCachePath));
        $configuration->abortOnError(false);

        // disable caches while developing
        $configuration->setUseCachedMetas(false);

        $configuration->addFormat(
            new HtmlFormat(
                $configuration->getTemplateRenderer(),
                $configuration->getFormat(),
                $this->globalTemplatesPath
            )
        );

        $twig = $configuration->getTemplateEngine();

        $twig->addExtension(new AssetsExtension());
        $extension = new Extension($projectDescriptor, $this->linkRenderer);
        // TODO: This setDestination option is meant to be used on a file-by-file basis. Just like this extension
        //       This may not work in this situation and we may need to discover a different root finding solution
        $extension->setDestination('docs/test');
        $twig->addExtension($extension);

        /** @var FilesystemLoader $loader */
        $loader = $twig->getLoader();
        $loader->prependPath($this->globalTemplatesPath . '/default');

        return new DocsKernel($configuration, $this->directives, $this->references, $buildContext);
    }
}
