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

use phpDocumentor\Configuration\SymfonyConfigFactory;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Version3 implements ConfigurationInterface, Normalizable
{
    /** @var string This is injected so that the name of the default template can be defined globally in the app */
    private $defaultTemplateName;

    public function __construct(string $defaultTemplateName)
    {
        $this->defaultTemplateName = $defaultTemplateName;
    }

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('phpdocumentor');

        $treebuilder->getRootNode()
            ->fixXmlConfig('version')
            ->fixXmlConfig('setting')
            ->fixXmlConfig('template')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode(SymfonyConfigFactory::FIELD_CONFIG_VERSION)->defaultValue('3')->end()
                ->scalarNode('title')->defaultValue('Documentation')->end()
                ->booleanNode('use-cache')->defaultTrue()->end()
                ->arrayNode('paths')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('output')->defaultValue('build/api')->end()
                        ->scalarNode('cache')->defaultValue('build/api-cache')->end()
                    ->end()
                ->end()
                ->arrayNode('versions')
                    ->useAttributeAsKey('number')
                    ->addDefaultChildrenIfNoneSet('1.0.0')
                    ->prototype('array')
                        ->fixXmlConfig('api', 'apis')
                        ->fixXmlConfig('guide')
                        ->children()
                            ->scalarNode('folder')->defaultValue('')->end()
                            ->append($this->apiSection())
                            ->append($this->guideSection())
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('settings')
                    ->children()
                        ->scalarNode('name')->end()
                        ->scalarNode('value')->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultChildrenIfNoneSet(1)
                    ->fixXmlConfig('parameter')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->defaultValue($this->defaultTemplateName)->end()
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

    public function normalize(array $configuration) : array
    {
        $configuration['configVersion'] = (string) $configuration['configVersion'];
        $configuration['paths']['output'] = Dsn::createFromString($configuration['paths']['output']);
        $configuration['paths']['cache'] = new Path($configuration['paths']['cache']);

        foreach ($configuration['versions'] as $versionNumber => $version) {
            // for array normalization to work, I need to use fixXmlConfig; but that doesn't seem to work
            // when you want to keep the key the same (api => api) but requires the plural to be differently named.
            // the rest of the app doesn't use a plural; so I undo that pluralisation here.
            $configuration['versions'][$versionNumber]['api'] = $configuration['versions'][$versionNumber]['apis'];
            unset($configuration['versions'][$versionNumber]['apis']);

            foreach ($version['apis'] as $key => $api) {
                $configuration['versions'][$versionNumber]['api'][$key]['source']['dsn']
                    = Dsn::createFromString($api['source']['dsn']);
                foreach ($api['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['api'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
                $configuration['versions'][$versionNumber]['api'][$key]['extensions'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['extensions']['extensions'];
                $configuration['versions'][$versionNumber]['api'][$key]['markers'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['markers']['markers'];
                // for array normalization to work, I need to use fixXmlConfig; but that doesn't seem to work
                // when you want to keep the key the same (api => api) but requires the plural to be differently named.
                // the rest of the app doesn't use a plural; so I undo that pluralisation here.
                $configuration['versions'][$versionNumber]['api'][$key]['visibility'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['visibilities'];
                unset($configuration['versions'][$versionNumber]['api'][$key]['visibilities']);
            }

            foreach ($version['guides'] as $key => $guide) {
                $configuration['versions'][$versionNumber]['guides'][$key]['source']['dsn']
                    = Dsn::createFromString($guide['source']['dsn']);
                foreach ($guide['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['guides'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
            }
        }

        return $configuration;
    }

    private function apiSection() : ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('apis');

        return $treebuilder->getRootNode()
            ->addDefaultChildrenIfNoneSet(1)
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->normalizeKeys(false)
                ->fixXmlConfig('visibility', 'visibilities')
                ->children()
                    ->enumNode('format')
                        ->info('In which language is your code written?')
                        ->values(['php'])
                        ->defaultValue('php')
                    ->end()
                    ->arrayNode('visibilities')
                        ->prototype('enum')
                            ->info('What is the deepest level of visibility to include in the documentation?')
                            ->values([
                                'api', // only include elements tagged with the `@api` tag
                                'public', // include all methods, properties and constants that are public
                                'protected', // include  all methods, properties and constants that are protected
                                'private', // include all methods, properties and constants that are private
                                'hidden', // include all elements tagged with `@hidden`
                            ])
                        ->end()
                        ->defaultValue(['public', 'protected', 'private'])
                    ->end()
                    ->scalarNode('default-package-name')
                        ->info(
                            'When your source code is grouped using the @package tag; what is the name of the '
                            . 'default package when none is provided?'
                        )
                        ->defaultValue('Application')
                    ->end()
                    ->scalarNode('encoding')->defaultValue('utf-8')->end()
                    ->append($this->source(['/**/*']))
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
                        ->fixXmlConfig('extension')
                        ->children()
                            ->arrayNode('extensions')
                                ->defaultValue(['php', 'php3', 'phtml'])
                                ->beforeNormalization()->castToArray()->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->booleanNode('include-source')
                        ->defaultTrue()
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

    private function guideSection() : ArrayNodeDefinition
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

    private function source(array $defaultPaths = []) : ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('source');

        return $treebuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('path')
            ->children()
                ->scalarNode('dsn')->defaultValue('.')->end()
                ->append($this->paths($defaultPaths))
            ->end();
    }

    private function paths(array $defaultValue = []) : ArrayNodeDefinition
    {
        $treebuilder = new TreeBuilder('paths');

        return $treebuilder->getRootNode()
            ->beforeNormalization()->castToArray()->end()
            ->defaultValue($defaultValue)
            ->prototype('scalar')->end();
    }
}
