<?php declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Version2 implements ConfigurationInterface, Upgradable
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('phpdocumentor');

        $treebuilder->getRootNode()
            ->children()
                ->scalarNode('v')->defaultValue('2')->end()
                ->scalarNode('title')->defaultValue('my-doc')->end()
                ->arrayNode('parser')
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('default-package-name')->defaultValue('Application')->end()
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
                        ->scalarNode('target')->defaultValue('build/api-cache')->end()
                        ->scalarNode('encoding')
                            ->defaultValue('utf-8')
                        ->end()
                        ->arrayNode('extensions')
                            ->addDefaultsIfNotSet()
                            ->fixXmlConfig('extension')
                            ->children()
                                ->arrayNode('extensions')
                                    ->defaultValue(['php', 'php3', 'phtml'])
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('markers')
                            ->addDefaultsIfNotSet()
                            ->fixXmlConfig('item')
                            ->children()
                                ->arrayNode('items')
                                    ->defaultValue(['TODO', 'FIXME'])
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('transformer')
                    ->children()
                        ->scalarNode('target')->defaultValue('build/api')->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->children()
                        ->scalarNode('level')->defaultValue('error')->end()
                    ->end()
                ->end()
                ->arrayNode('transformations')
                    ->fixXmlConfig('template')
                    ->children()
                        ->arrayNode('templates')
                            ->fixXmlConfig('parameter')
                            ->useAttributeAsKey('name')
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
                    ->end()
                ->end()
                ->arrayNode('files')
                    ->fixXmlConfig('file', 'files')
                    ->fixXmlConfig('directory', 'directories')
                    ->fixXmlConfig('ignore', 'ignores')
                    ->normalizeKeys(false)
                    ->children()
                        ->booleanNode('ignore-hidden')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('ignore-symlinks')
                            ->defaultTrue()
                        ->end()
                        ->arrayNode('directories')
                            ->beforeNormalization()->castToArray()->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('files')
                            ->beforeNormalization()->castToArray()->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('ignores')
                            ->beforeNormalization()->castToArray()->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treebuilder;
    }

    /**
     * Upgrades the version 2 configuration to the version 3 configuration.
     *
     * @todo not all options are included yet; finish this
     *
     * @inheritDoc
     */
    public function upgrade(array $values) : array
    {
        return [
            'v' => '3',
            'paths' => [
                'output' => $values['transformer']['target'],
                'cache' => $values['parser']['target']
            ],
            'version' => [
                [
                    'number' => '1.0.0',
                    'api' => [
                        [
                            'default-package-name' => $values['parser']['default-package-name'],
                            'source' => [
                                'path' => array_merge($values['files']['files'], $values['files']['directories'])
                            ],
                            'ignore' => [
                                'paths' => $values['files']['ignores']
                            ],
                            'extensions' => [
                                'extension' => $values['parser']['extensions']['extensions']
                            ],
                            'markers' => [
                                'marker' => $values['parser']['markers']['items']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
