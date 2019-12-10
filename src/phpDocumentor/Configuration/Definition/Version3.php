<?php declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Version3 implements ConfigurationInterface
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
            ->children()
                ->integerNode('v')->end()
                ->arrayNode('paths')
                    ->children()
                        ->scalarNode('output')->end()
                        ->scalarNode('cache')->end()
                    ->end()
                ->end()
                ->arrayNode('versions')
                    ->useAttributeAsKey('number')
                    ->prototype('array')
                        ->fixXmlConfig('api', 'apis')
                        ->fixXmlConfig('guide', 'guides')
                        ->children()
                            ->scalarNode('folder')->end()
                            ->append($this->apiSection())
                            ->append($this->guideSection())
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->fixXmlConfig('parameter')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
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
        $treebuilder = new TreeBuilder('apis');

        return $treebuilder->getRootNode()
            ->prototype('array')
                ->children()
                    ->enumNode('format')
                        ->info('In which language is your code written?')
                        ->values(['php'])
                        ->defaultValue('php')
                    ->end()
                    ->enumNode('visibility')
                        ->info('What is the deepest level of visibility to include in the documentation?')
                        ->defaultValue('public')
                        ->values([
                            'api', // only include elements tagged with the `@api` tag
                            'public', // include the previous category and all methods, properties and constants that are public
                            'protected', // include the previous category and all methods, properties and constants that are protected
                            'private', // include the previous category and all methods, properties and constants that are private
                            'hidden' // include the previous category and all elements tagged with `@hidden`
                        ])
                    ->end()
                    ->scalarNode('default_package_name')
                        ->info(
                            'When your source code is grouped using the @package tag; what is the name of the '
                            . 'default package when none is provided?'
                        )
                        ->defaultValue('Application')
                    ->end()
                    ->append($this->source())
                    ->arrayNode('ignore')
                        ->fixXmlConfig('path')
                        ->children()
                            ->booleanNode('hidden')->end()
                            ->booleanNode('symlinks')->end()
                            ->append($this->paths())
                        ->end()
                    ->end()
                    ->arrayNode('extensions')
                        ->children()
                            ->arrayNode('extension')
                                ->beforeNormalization()->castToArray()->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('markers')
                        ->fixXmlConfig('marker')
                        ->children()
                            ->arrayNode('markers')
                                ->beforeNormalization()->castToArray()->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function guideSection(): ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('guides');

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
