<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\DependencyInjection;

use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class NodeRenderersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $config = new Configuration();

        $count = 0;
        foreach ($config->htmlNodeTemplates() as $node => $template) {
            $container->setDefinition(
                'phpdoc.guides.noderenderer.html.' . $count++,
                (
                    new Definition(
                        TemplateNodeRenderer::class,
                        [
                            '$template' => $template,
                            '$nodeClass' => $node,
                        ]
                    )
                )->setAutowired(true)->addTag('phpdoc.guides.noderenderer.html')
            );
        }
    }
}
