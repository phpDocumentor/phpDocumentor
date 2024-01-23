<?php

declare(strict_types=1);

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Guides\Nodes\PHP\ClassDiagram;
use phpDocumentor\Guides\Nodes\PHP\ClassList;
use phpDocumentor\Guides\Nodes\PHP\ElementDescription;
use phpDocumentor\Guides\Nodes\PHP\ElementName;
use phpDocumentor\Pipeline\Attribute\Stage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function dirname;
use function phpDocumentor\Guides\DependencyInjection\template;

final class ApplicationExtension extends Extension implements PrependExtensionInterface
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

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'guides',
            [
                'templates' => [
                    template(ClassList::class, 'body/php/class-list.html.twig'),
                    template(ClassDiagram::class, 'body/uml.html.twig'),
                    template(ElementName::class, 'body/php/element-name.html.twig'),
                    template(ElementDescription::class, 'body/php/element-description.html.twig'),
                ],
            ],
        );
    }
}
