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
                ->floatNode('v')->defaultValue('2')->end()
                ->arrayNode('parser')
                    ->children()
                        ->scalarNode('default_package_name')->defaultValue('Application')->end()
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
     * @inheritDoc
     */
    public function upgrade(array $values) : array
    {
        // TODO: Implement upgrade() method.

        return $values;
    }
}
