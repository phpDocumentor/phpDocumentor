<?php

declare(strict_types=1);

namespace phpDocumentor\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class GuidesCommandsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        dump('hier');
        foreach ($container->findTaggedServiceIds('phpdoc.guides.command') as $id => $tags) {
            $container->getDefinition($id)->addTag('tactician.handler', ['typehints' => true]);
        }
    }
}
