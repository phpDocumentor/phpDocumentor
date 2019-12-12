<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Dsn;
use phpDocumentor\Path;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Version3 implements ConfigurationInterface, Normalizable
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('phpdocumentor');

        $treebuilder->getRootNode()
            ->fixXmlConfig('version')
            ->fixXmlConfig('template')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('v')->end()
                ->scalarNode('title')->defaultValue('my-doc')->end()
                ->booleanNode('use-cache')->defaultTrue()->end()
                ->arrayNode('paths')
                    ->children()
                        ->scalarNode('output')->end()
                        ->scalarNode('cache')->end()
                    ->end()
                ->end()
                ->arrayNode('versions')
                    ->useAttributeAsKey('number')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('folder')->defaultValue('')->end()
                            ->append($this->apiSection())
                            ->append($this->guideSection())
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->fixXmlConfig('parameter')
                    ->useAttributeAsKey('name')
                    ->defaultValue(['clean' => ['name' => 'clean']])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('location')->end()
                            ->arrayNode('parameters')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('value')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treebuilder;
    }

    private function apiSection(): ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('api');

        return $treebuilder->getRootNode()
            ->prototype('array')
                ->normalizeKeys(false)
                ->children()
                    ->enumNode('format')
                        ->info('In which language is your code written?')
                        ->values(['php'])
                        ->defaultValue('php')
                    ->end()
                    ->arrayNode('visibility')
                        ->defaultValue(['public', 'protected', 'private'])
                        ->prototype('enum')
                            ->info('What is the deepest level of visibility to include in the documentation?')
                            ->values([
                                'api', // only include elements tagged with the `@api` tag
                                'public', // include the previous category and all methods, properties and constants that are public
                                'protected', // include the previous category and all methods, properties and constants that are protected
                                'private', // include the previous category and all methods, properties and constants that are private
                                'hidden' // include the previous category and all elements tagged with `@hidden`
                            ])
                        ->end()
                    ->end()
                    ->scalarNode('default-package-name')
                        ->defaultValue('Application')
                        ->info(
                            'When your source code is grouped using the @package tag; what is the name of the '
                            . 'default package when none is provided?'
                        )
                    ->end()
                    ->scalarNode('encoding')
                        ->defaultValue('utf-8')
                    ->end()
                    ->append($this->source())
                    ->arrayNode('ignore')
                        ->addDefaultsIfNotSet()
                        ->fixXmlConfig('path')
                        ->children()
                            ->booleanNode('hidden')->defaultTrue()->end()
                            ->booleanNode('symlinks')->defaultTrue()->end()
                            ->append($this->paths())
                        ->end()
                    ->end()
                    ->arrayNode('ignore-tags')
                        ->defaultValue([])
                        ->beforeNormalization()->castToArray()->end()
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('extensions')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('extension')
                                ->defaultValue(['php', 'php3', 'phtml'])
                                ->beforeNormalization()->castToArray()->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->booleanNode('include-source')
                        ->defaultFalse()
                    ->end()
                    ->booleanNode('validate')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('markers')
                        ->addDefaultsIfNotSet()
                        ->fixXmlConfig('marker')
                        ->children()
                            ->arrayNode('markers')
                                ->defaultValue(['TODO', 'FIXME'])
                                ->beforeNormalization()->castToArray()->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function normalize(array $configuration): array
    {
        $configuration['paths']['output'] = new Dsn('file://' . $configuration['paths']['output']);
        $configuration['paths']['cache'] = new Path($configuration['paths']['cache']);
        foreach ($configuration['versions'] as $versionNumber => $version) {
            foreach ($version['api'] as $key => $api) {
                $configuration['versions'][$versionNumber]['api'][$key]['source']['dsn']
                    = new Dsn($api['source']['dsn']);
                foreach ($api['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['api'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
                $configuration['versions'][$versionNumber]['api'][$key]['extensions'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['extensions']['extension'];
                $configuration['versions'][$versionNumber]['api'][$key]['markers'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['markers']['markers'];
            }
            foreach ($version['guide'] as $key => $guide) {
                $configuration['versions'][$versionNumber]['guide'][$key]['source']['dsn']
                    = new Dsn($guide['source']['dsn']);
                foreach ($guide['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['guide'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
            }
        }

        return $configuration;
    }

    private function guideSection(): ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('guide');

        return $treebuilder->getRootNode()
            ->prototype('array')
                ->children()
                    ->enumNode('format')
                        ->values(['rst'])
                        ->defaultValue('rst')
                    ->end()
                    ->append($this->source())
                ->end()
            ->end();
    }

    private function source(): ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('source');

        return $treebuilder->getRootNode()
            ->fixXmlConfig('path')
            ->children()
                ->scalarNode('dsn')->defaultValue('file://.')->end()
                ->append($this->paths())
            ->end();
    }

    private function paths(): ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('paths');

        return $treebuilder->getRootNode()
            ->requiresAtLeastOneElement()
            ->beforeNormalization()->castToArray()->end()
            ->prototype('scalar')->end();
    }
}
