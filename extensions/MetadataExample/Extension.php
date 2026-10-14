<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\MetadataExample;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Test extension that attaches metadata to every reflected element and renders it with its own template.
 */
class Extension extends \phpDocumentor\Extension\Extension
{
    public function getAlias(): string
    {
        return 'metadata-example';
    }

    public function getTemplatePath(): string|null
    {
        return __DIR__ . '/Resources/templates';
    }

    /** @return string[] */
    public function getMetadataKeys(): array
    {
        return ['metadata_example', 'metadata_second', 'metadata_notemplate'];
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.php');
    }
}
