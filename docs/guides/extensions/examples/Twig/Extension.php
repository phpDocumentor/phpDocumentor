<?php

declare(strict_types=1);

namespace MyExtension\Twig;

use phpDocumentor\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class Extension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->addDefinitions([
            (new Definition(MyExtension::class))->addTag('twig.extension'),
        ]);
    }
}
