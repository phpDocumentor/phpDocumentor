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
use phpDocumentor\Guides\Twig\AssetsExtension;

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

    public function __construct(
        string $globalTemplatesPath,
        string $globalCachePath,
        array $directives = [],
        array $references = []
    ) {
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->globalCachePath = $globalCachePath;
        $this->directives = $directives;
        $this->references = $references;
    }

    public function createKernel(BuildContext $buildContext) : Kernel
    {
        $configuration = new RSTParserConfiguration();
        $configuration->setCustomTemplateDirs([$this->globalTemplatesPath]);
        $configuration->setCacheDir(sprintf('%s/guide-cache', $this->globalCachePath));
        $configuration->abortOnError(false);

        if ($buildContext->getDisableCache()) {
            $configuration->setUseCachedMetas(false);
        }

        $configuration->addFormat(new HtmlFormat($configuration->getTemplateRenderer(), $configuration->getFormat()));

        if ($parseSubPath = $buildContext->getParseSubPath()) {
            $configuration->setBaseUrl($buildContext->getSymfonyDocUrl());
            $configuration->setBaseUrlEnabledCallable(
                static function (string $path) use ($parseSubPath): bool {
                    return 0 !== strpos($path, $parseSubPath);
                }
            );
        }

        $twig = $configuration->getTemplateEngine();
        $twig->addExtension(new AssetsExtension());

        return new DocsKernel(
            $configuration,
            $this->directives,
            $this->references,
            $buildContext
        );
    }
}
