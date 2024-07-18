<?php

declare(strict_types=1);

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Guides\Renderer\UrlGenerator;
use phpDocumentor\Guides\Renderer\UrlGenerator\UrlGeneratorInterface;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Transformer\Writer\RenderGuide;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class GuidesCommandsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->addAliases([TemplateRenderer::class => RenderGuide::class]);
        $container->addAliases([UrlGeneratorInterface::class => UrlGenerator::class]);
        foreach ($container->findTaggedServiceIds('phpdoc.guides.command') as $id => $_tags) {
            $container->getDefinition($id)->addTag('tactician.handler', ['typehints' => true]);
        }
    }
}
