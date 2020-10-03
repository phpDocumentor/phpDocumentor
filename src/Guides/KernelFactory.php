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

use IteratorAggregate;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\HTML\HTMLFormat;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\LaTeX\LaTeXFormat;
use phpDocumentor\Guides\RestructuredText\References\Reference;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TwigTemplateRenderer;
use phpDocumentor\Guides\RestructuredText\Twig\AssetsExtension;
use Psr\Log\LoggerInterface;
use Twig\Environment;

final class KernelFactory
{
    /** @var string */
    private $globalTemplatesPath;

    /** @var IteratorAggregate<Directive> */
    private $directives;

    /** @var IteratorAggregate<Reference> */
    private $references;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        string $globalTemplatesPath,
        IteratorAggregate $directives,
        IteratorAggregate $references
    ) {
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->directives = $directives;
        $this->references = $references;
        $this->logger = $logger;
    }

    public function createKernel(BuildContext $buildContext, Environment $environment) : Kernel
    {
        $templateRenderer = new TemplateRenderer($environment, 'guides');

        $configuration = new Configuration();
        $configuration->setTemplateRenderer($templateRenderer);
        $configuration->setCacheDir($buildContext->getCachePath());
        $configuration->setUseCachedMetas($buildContext->isCacheEnabled());

        $configuration->addFormat(
            new HTMLFormat(
                $templateRenderer,
                $this->globalTemplatesPath,
                $buildContext->getDestinationPath()
            )
        );
        $configuration->addFormat(new LaTeXFormat($templateRenderer));

        $environment->addExtension(new AssetsExtension());

        return new Kernel($configuration, $this->directives, $this->references, $buildContext, $this->logger);
    }
}
