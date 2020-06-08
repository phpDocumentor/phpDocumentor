<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides;

use Doctrine\RST\Configuration as RSTParserConfiguration;
use Doctrine\RST\Directives\Directive as Directive;
use Doctrine\RST\Kernel;
use Doctrine\RST\References\Reference;
use IteratorAggregate;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Writer\Twig\Extension;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use Twig\Loader\FilesystemLoader;

final class KernelFactory
{
    /** @var string */
    private $globalTemplatesPath;

    /** @var IteratorAggregate<Directive> */
    private $directives;

    /** @var IteratorAggregate<Reference> */
    private $references;

    /** @var LinkRenderer */
    private $linkRenderer;

    public function __construct(
        string $globalTemplatesPath,
        LinkRenderer $linkRenderer,
        IteratorAggregate $directives,
        IteratorAggregate $references
    ) {
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->directives = $directives;
        $this->references = $references;
        $this->linkRenderer = $linkRenderer;
    }

    public function createKernel(ProjectDescriptor $projectDescriptor, BuildContext $buildContext) : Kernel
    {
        $configuration = new RSTParserConfiguration();
        $configuration->setCustomTemplateDirs([$this->globalTemplatesPath . '/guides']);
        $configuration->setCacheDir($buildContext->getCachePath());
        $configuration->abortOnError(false);
        $configuration->setUseCachedMetas($buildContext->isCacheEnabled());

        $configuration->addFormat(
            new HtmlFormat(
                $configuration->getTemplateRenderer(),
                $configuration->getFormat(),
                $this->globalTemplatesPath,
                $buildContext->getDestinationPath()
            )
        );

        $twig = $configuration->getTemplateEngine();

        $twig->addExtension(new AssetsExtension());
        $twig->addExtension(new Extension($projectDescriptor, $this->linkRenderer));

        /** @var FilesystemLoader $loader */
        $loader = $twig->getLoader();
        $loader->prependPath($this->globalTemplatesPath . '/' . $buildContext->getTemplate());

        return new DocsKernel($configuration, $this->directives, $this->references, $buildContext);
    }
}
