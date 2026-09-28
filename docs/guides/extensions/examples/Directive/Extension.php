<?php

declare(strict_types=1);

namespace phpDocumentor\Example;

use phpDocumentor\Example\Nodes\HelloNode;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

use function phpDocumentor\Guides\DependencyInjection\template;

class Extension extends \phpDocumentor\Extension\Extension
{
    public function getAlias(): string
    {
        return 'phpdoc:example';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.php');

        // Make our own templates directory known to the Twig loader
        $baseDirs = $container->getParameter('phpdoc.guides.base_template_paths');
        $baseDirs[] = __DIR__ . '/Resources/templates/html';
        $container->setParameter('phpdoc.guides.base_template_paths', $baseDirs);

        // Tell the renderer which template belongs to our node
        $templates = $container->getParameter('phpdoc.guides.node_templates');
        $templates[] = template(HelloNode::class, 'hello.html.twig');
        $container->setParameter('phpdoc.guides.node_templates', $templates);
    }
}
