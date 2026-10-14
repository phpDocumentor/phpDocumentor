<?php

declare(strict_types=1);

namespace phpDocumentor\WordpressHooks;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class Extension extends \phpDocumentor\Extension\Extension
{
    public function getAlias(): string
    {
        return 'wordpress-hooks';
    }

    public function getTemplatePath(): string|null
    {
        return __DIR__ . '/Resources/templates';
    }

    /** @return string[] */
    public function getMetadataKeys(): array
    {
        return ['wp_hooks'];
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.php');
    }
}
