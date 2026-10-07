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

namespace phpDocumentor\GuidesDirectives;

use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionsList;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

use function file_exists;
use function phpDocumentor\Guides\DependencyInjection\template;

class Extension extends \phpDocumentor\Extension\Extension
{
    public function getAlias(): string
    {
        return 'phpdoc:guides-directives';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            require_once __DIR__ . '/vendor/autoload.php';
        }

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.php');
        $baseDirs = $container->getParameter('phpdoc.guides.base_template_paths');
        $baseDirs[] = __DIR__ . '/Resources/templates/html';
        $container->setParameter('phpdoc.guides.base_template_paths', $baseDirs);
        $templates = $container->getParameter('phpdoc.guides.node_templates');
        $templates[] = template(DirectiveOptionsList::class, 'directive-list-options.html.twig');
        $container->setParameter('phpdoc.guides.node_templates', $templates);
    }
}
