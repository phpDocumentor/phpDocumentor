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
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use PHPUnit\Framework\TestCase;

use function array_merge;
use function array_replace_recursive;

/** @coversDefaultClass \phpDocumentor\Configuration\Definition\Version3 */
final class Version3Test extends TestCase
{
    private const DEFAULT_TEMPLATE_NAME = 'default';

    /** @dataProvider provideTestConfiguration */
    public function testLoadingADefaultConfigWorks($inputConfig, $expectedConfig): void
    {
        $configuration = new Version3(self::DEFAULT_TEMPLATE_NAME);
        $node = $configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    public function testNormalizingTheOutputTransformsTheConfig(): void
    {
        $definition = new Version3(self::DEFAULT_TEMPLATE_NAME);
        $configuration = self::defaultConfigurationOutput();
        $expected = self::defaultConfigurationOutput();
        $expected['paths']['output'] = Dsn::createFromString($expected['paths']['output']);
        $expected['paths']['cache'] = new Path($expected['paths']['cache']);
        $expected['versions']['1.0.0']['api'] = $expected['versions']['1.0.0']['apis'];
        unset($expected['versions']['1.0.0']['apis']);
        $expected['versions']['1.0.0']['api'][0]['ignore-tags']
            = $expected['versions']['1.0.0']['api'][0]['ignore-tags']['ignore_tags'];
        $expected['versions']['1.0.0']['api'][0]['extensions']
            = $expected['versions']['1.0.0']['api'][0]['extensions']['extensions'];
        $expected['versions']['1.0.0']['api'][0]['markers']
            = $expected['versions']['1.0.0']['api'][0]['markers']['markers'];
        $expected['versions']['1.0.0']['api'][0]['visibility']
            = $expected['versions']['1.0.0']['api'][0]['visibilities'];
        unset($expected['versions']['1.0.0']['api'][0]['visibilities']);
        $expected['templates'][0]['location'] = null;

        $configuration = $definition->normalize($configuration);

        self::assertEquals($expected, $configuration);
    }

    public function testNormalizingAPopulatedTemplateLocationGivesAPath(): void
    {
        $definition = new Version3(self::DEFAULT_TEMPLATE_NAME);
        $configuration = self::defaultConfigurationOutput();
        $configuration['templates'][0]['location'] = 'data/templates';

        $result = $definition->normalize($configuration);

        self::assertInstanceOf(Path::class, $result['templates'][0]['location']);
        self::assertSame('data/templates', (string) $result['templates'][0]['location']);
    }

    public static function provideTestConfiguration(): array
    {
        return [
            'default configuration' => [[], self::defaultConfigurationOutput()],
            'configuration with title' => [
                ['title' => 'My project'],
                array_merge(self::defaultConfigurationOutput(), ['title' => 'My project']),
            ],
            'configuration with other destination' => [
                ['paths' => ['output' => '/tmp']],
                array_replace_recursive(self::defaultConfigurationOutput(), ['paths' => ['output' => '/tmp']]),
            ],
            'configuration with provided source' => [
                [
                    'paths' => ['output' => '/tmp'],
                    'version' => [
                        [
                            'number' => 'latest',
                            'api' => [
                                [
                                    'source' => [
                                        'dsn' => 'file:///tmp',
                                        'path' => ['src'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                self::defaultConfigurationWithOneApiWithOverriddenSource('latest', 'file:///tmp', ['src']),
            ],
            'configuration with example' => [
                [
                    'paths' => ['output' => '/tmp'],
                    'version' => [
                        [
                            'number' => 'latest',
                            'api' => [
                                [
                                    'examples' => [
                                        'dsn' => 'file:///tmp',
                                        'path' => ['src'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                self::defaultConfigurationWithOneApiWithOverriddenExamples('latest', 'file:///tmp', ['src']),
            ],
            'minimal configuration' => [
                [
                    'versions' => [
                        '1.0.0' => [
                            'apis' => [
                                [],
                            ],
                        ],
                    ],
                ],
                self::defaultConfigurationOutput(),
            ],
            'check version respresentation' => [
                [
                    'paths' => ['output' => '/tmp'],
                    'version' => [
                        [
                            'number' => '3.10',
                            'api' => [
                                [
                                    'examples' => [
                                        'dsn' => 'file:///tmp',
                                        'path' => ['src'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                self::defaultConfigurationWithOneApiWithOverriddenExamples('3.10', 'file:///tmp', ['src']),
            ],
        ];
    }

    private static function defaultConfigurationOutput(): array
    {
        return [
            SymfonyConfigFactory::FIELD_CONFIG_VERSION => '3',
            'title' => 'Documentation',
            'use_cache' => true,
            'paths' => [
                'output' => '.phpdoc/build',
                'cache' => '.phpdoc/cache',
            ],
            'versions' => [
                '1.0.0' => [
                    'number' => '1.0.0',
                    'folder' => '',
                    'apis' => [
                        [
                            'format' => 'php',
                            'visibilities' => ['public', 'protected', 'private'],
                            'default-package-name' => 'Application',
                            'encoding' => 'utf-8',
                            'source' => [
                                'dsn' => '.',
                                'paths' => ['/**/*'],
                            ],
                            'ignore' => [
                                'hidden' => true,
                                'symlinks' => true,
                                'paths' => [],
                            ],
                            'extensions' => [
                                'extensions' => ['php', 'php3', 'phtml'],
                            ],
                            'include-source' => false,
                            'validate' => false,
                            'markers' => ['markers' => ['TODO', 'FIXME']],
                            'ignore-tags' => ['ignore_tags' => []],
                            'output' => '.',
                            'ignore-packages' => false,
                        ],
                    ],
                    'guides' => [],
                ],
            ],
            'settings' => [],
            'templates' => [
                [
                    'name' => self::DEFAULT_TEMPLATE_NAME,
                    'parameters' => [],
                ],
            ],
        ];
    }

    private static function defaultConfigurationWithOneApiWithOverriddenSource(
        string $versionString,
        string $dsn,
        array $paths,
    ): array {
        $configuration = array_replace_recursive(
            self::defaultConfigurationOutput(),
            [
                'paths' => ['output' => '/tmp'],
            ],
        );

        // for the version we do want to check whether the defaults are kept; so instead of using array_replace,
        // we first extract the default key from our described default and change it to resemble our expected end-state
        $version = $configuration['versions']['1.0.0'];
        $version['apis'][0]['source'] = [
            'dsn' => $dsn,
            'paths' => $paths,
        ];
        unset($configuration['versions']['1.0.0']);

        $version['number'] = $versionString;
        $configuration['versions'][$versionString] = $version;

        return $configuration;
    }

    private static function defaultConfigurationWithOneApiWithOverriddenExamples(
        string $versionString,
        string $dsn,
        array $paths,
    ): array {
        $configuration = array_replace_recursive(
            self::defaultConfigurationOutput(),
            [
                'paths' => ['output' => '/tmp'],
            ],
        );

        // for the version we do want to check whether the defaults are kept; so instead of using array_replace,
        // we first extract the default key from our described default and change it to resemble our expected end-state
        $version = $configuration['versions']['1.0.0'];
        $version['apis'][0]['examples'] = [
            'dsn' => $dsn,
            'paths' => $paths,
        ];
        unset($configuration['versions']['1.0.0']);

        $version['number'] = $versionString;
        $configuration['versions'][$versionString] = $version;

        return $configuration;
    }
}
