<?php declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides;

use Doctrine\RST\Configuration as RSTParserConfiguration;
use Doctrine\RST\Kernel;
use phpDocumentor\Guides\Directive as SymfonyDirectives;
use phpDocumentor\Guides\Reference as SymfonyReferences;
use phpDocumentor\Guides\Twig\AssetsExtension;

/**
 * Class KernelFactory.
 */
final class KernelFactory
{
    public static function createKernel(BuildContext $buildContext, ?UrlChecker $urlChecker = null): Kernel
    {
        $configuration = new RSTParserConfiguration();
        $configuration->setCustomTemplateDirs([__DIR__.'/Templates']);
        $configuration->setTheme($buildContext->getTheme());
        $configuration->setCacheDir(sprintf('%s/var/cache', $buildContext->getCacheDir()));
        $configuration->abortOnError(false);

        if ($buildContext->getDisableCache()) {
            $configuration->setUseCachedMetas(false);
        }

        $configuration->addFormat(
            new SymfonyHTMLFormat(
                $configuration->getTemplateRenderer(),
                $configuration->getFormat(),
                $urlChecker
            )
        );

        if ($parseSubPath = $buildContext->getParseSubPath()) {
            $configuration->setBaseUrl($buildContext->getSymfonyDocUrl());
            $configuration->setBaseUrlEnabledCallable(
                static function (string $path) use ($parseSubPath): bool {
                    return 0 !== strpos($path, $parseSubPath);
                }
            );
        }

        $twig = $configuration->getTemplateEngine();
        $twig->addExtension(new AssetsExtension($buildContext->getOutputDir()));

        return new DocsKernel(
            $configuration,
            self::getDirectives(),
            self::getReferences($buildContext),
            $buildContext
        );
    }

    private static function getDirectives(): array
    {
        return [
            new SymfonyDirectives\AdmonitionDirective(),
            new SymfonyDirectives\CautionDirective(),
            new SymfonyDirectives\CodeBlockDirective(),
            new SymfonyDirectives\ConfigurationBlockDirective(),
            new SymfonyDirectives\DeprecatedDirective(),
            new SymfonyDirectives\IndexDirective(),
            new SymfonyDirectives\RoleDirective(),
            new SymfonyDirectives\NoteDirective(),
            new SymfonyDirectives\SeeAlsoDirective(),
            new SymfonyDirectives\SidebarDirective(),
            new SymfonyDirectives\TipDirective(),
            new SymfonyDirectives\TopicDirective(),
            new SymfonyDirectives\WarningDirective(),
            new SymfonyDirectives\VersionAddedDirective(),
            new SymfonyDirectives\BestPracticeDirective(),
            new SymfonyDirectives\GlossaryDirective(),
        ];
    }

    private static function getReferences(BuildContext $buildContext): array
    {
        return [
            new SymfonyReferences\ClassReference($buildContext->getSymfonyApiUrl()),
            new SymfonyReferences\MethodReference($buildContext->getSymfonyApiUrl()),
            new SymfonyReferences\NamespaceReference($buildContext->getSymfonyApiUrl()),
            new SymfonyReferences\PhpFunctionReference($buildContext->getPhpDocUrl()),
            new SymfonyReferences\PhpMethodReference($buildContext->getPhpDocUrl()),
            new SymfonyReferences\PhpClassReference($buildContext->getPhpDocUrl()),
            new SymfonyReferences\TermReference(),
            new SymfonyReferences\LeaderReference(),
            new SymfonyReferences\MergerReference(),
            new SymfonyReferences\DeciderReference(),
        ];
    }
}
