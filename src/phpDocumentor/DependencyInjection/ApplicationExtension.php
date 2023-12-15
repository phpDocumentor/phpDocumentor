<?php

declare(strict_types=1);

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Pipeline\Attribute\Stage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function dirname;

final class ApplicationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__, 3) . '/config'),
        );

        $loader->load('services.yaml');

        $container->registerAttributeForAutoconfiguration(
            Stage::class,
            static function (ChildDefinition $definition, Stage $attribute): void {
                $definition->addTag($attribute->name, ['priority' => $attribute->priority]);
            },
        );
    }
}
