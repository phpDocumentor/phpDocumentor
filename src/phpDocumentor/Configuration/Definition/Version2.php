<?php declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
                ->arrayNode('parser')
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('default-package-name')->defaultValue('Application')->end()
                        ->scalarNode('target')->defaultValue('build/api-cache')->end()
                    ->end()
                ->end()
                ->arrayNode('transformer')
                    ->children()
                        ->scalarNode('target')->defaultValue('build/api')->end()
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
                    ->children()
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
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
