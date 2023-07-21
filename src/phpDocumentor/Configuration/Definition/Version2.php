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

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Configuration\SymfonyConfigFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function array_map;
use function array_merge;
use function array_values;
use function explode;
use function getcwd;
use function implode;
use function str_ends_with;

/**
 * @psalm-type ConfigurationMap = array<mixed>
 * @psalm-import-type BaseConfiguration from Version3 as UpgradedConfiguration
 */
final class Version2 implements ConfigurationInterface, Upgradable
{
    public function __construct(
        /** @var string This is injected so that the name of the default template can be defined globally in the app */
        private readonly string $defaultTemplateName,
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treebuilder = new TreeBuilder('phpdocumentor');

        $treebuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode(SymfonyConfigFactory::FIELD_CONFIG_VERSION)->defaultValue('2')->end()
                ->scalarNode('title')->defaultValue('Documentation')->end()
                ->arrayNode('parser')
                    ->addDefaultsIfNotSet()
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('default-package-name')->defaultValue('Application')->end()
                        ->scalarNode('visibility')
                            ->defaultValue(implode(',', ['public', 'protected', 'private']))
                            ->info('What is the deepest level of visibility to include in the documentation?')
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('target')->defaultValue('build/api')->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('level')->defaultValue('error')->end()
                    ->end()
                ->end()
                ->arrayNode('transformations')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('template')
                    ->children()
                        ->arrayNode('templates')
                            ->fixXmlConfig('parameter')
                            ->useAttributeAsKey('name')
                            ->defaultValue([$this->defaultTemplateName => ['name' => $this->defaultTemplateName]])
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
                    ->addDefaultsIfNotSet()
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
                            ->defaultValue([getcwd()])
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
     * Upgrades the version 2 configuration to the version 3 pre-normalized configuration.
     *
     * @param ConfigurationMap $values
     *
     * @return UpgradedConfiguration
     */
    public function upgrade(array $values): array
    {
        return [
            SymfonyConfigFactory::FIELD_CONFIG_VERSION => '3',
            'title' => $values['title'],
            'paths' => [
                'output' => $values['transformer']['target'],
                'cache' => $values['parser']['target'],
            ],
            'version' => [
                [
                    'number' => '1.0.0',
                    'api' => [
                        [
                            'default-package-name' => $values['parser']['default-package-name'],
                            'source' => [
                                'paths' => array_map(
                                    fn ($value) => $this->convertSingleStarPathEndingIntoGlobPattern($value),
                                    array_merge($values['files']['files'], $values['files']['directories']),
                                ),
                            ],
                            'ignore' => [
                                'paths' => array_map(
                                    fn ($value) => $this->convertSingleStarPathEndingIntoGlobPattern($value),
                                    $values['files']['ignores'],
                                ),
                            ],
                            'extensions' => [
                                'extensions' => $values['parser']['extensions']['extensions'],
                            ],
                            'visibilities' => $values['parser']['visibility'] ? explode(
                                ',',
                                (string) $values['parser']['visibility'],
                            ) : null,
                            'markers' => [
                                'markers' => $values['parser']['markers']['items'],
                            ],
                        ],
                    ],
                ],
            ],
            'templates' => array_values($values['transformations']['templates']),
        ];
    }

    /**
     * Make a `/*` ending backwards compatible for v2.
     *
     * In phpDocumentor 3 we started adopting the glob pattern with globstar extension to properly define patterns
     * matching file paths. This is incompatible with phpDocumentor 2, that interpreted a * to mean any number of
     * characters, including the path separator.
     *
     * To ensure this behaviour is properly translated, this method will detect if a path ends with /*, and if it is
     * not a globstar pattern, we convert it to one. This matches the behaviour in phpDocumentor 2 without user
     * interaction.
     *
     * @link https://www.gnu.org/software/bash/manual/html_node/Pattern-Matching.html
     */
    private function convertSingleStarPathEndingIntoGlobPattern(string $path): string
    {
        if (str_ends_with($path, '/*') && ! str_ends_with($path, '**/*')) {
            $path .= '*/*';
        }

        return $path;
    }
}
