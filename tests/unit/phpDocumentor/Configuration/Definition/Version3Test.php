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

use PHPUnit\Framework\TestCase;

final class Version3Test extends TestCase
{
    const DEFAULT_TEMPLATE_NAME = 'clean';

    /**
     * @dataProvider provideTestConfiguration
     */
    public function testLoadingADefaultConfigWorks($inputConfig, $expectedConfig)
    {
        $configuration = new Version3(self::DEFAULT_TEMPLATE_NAME);
        $node = $configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    public function provideTestConfiguration()
    {
        return [
            'default configuration' => [[], $this->defaultConfigurationOutput()],
            'configuration with title' => [
                ['title' => 'My project'],
                array_merge($this->defaultConfigurationOutput(), ['title' => 'My project']),
            ],
            'configuration with other destination' => [
                ['paths' => ['output' => '/tmp']],
                array_replace_recursive($this->defaultConfigurationOutput(), ['paths' => ['output' => '/tmp']]),
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
                                        'path' => [
                                            'src',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                $this->defaultConfigurationWithOneApiWithOverriddenSource('latest', 'file:///tmp', ['src']),
            ],
            'minimal configuration' => [
                [
                    'versions' => [
                        "1.0.0" => [
                            'api' => [
                                [],
                            ],
                        ],
                    ],
                ],
                $this->defaultConfigurationOutput(),
            ],
        ];
    }

    /**
     * @return array
     */
    private function defaultConfigurationOutput() : array
    {
        return [
            'v' => '3',
            'title' => 'my-doc',
            'use-cache' => true,
            'paths' => [
                'output' => 'build/api',
                'cache' => 'build/api-cache',
            ],
            'versions' => [
                '1.0.0' => [
                    'folder' => '',
                    'api' => [
                        [
                            'format' => 'php',
                            'visibility' => ['public', 'protected', 'private'],
                            'default-package-name' => 'Application',
                            'encoding' => 'utf-8',
                            'source' => [
                                'dsn' => 'file://.',
                                'paths' => [
                                    '.',
                                ],
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
                            'ignore-tags' => [],
                        ],
                    ],
                    'guide' => [],
                ],
            ],
            'templates' => [
                [
                    'name' => self::DEFAULT_TEMPLATE_NAME
                ],
            ],
        ];
    }

    private function defaultConfigurationWithOneApiWithOverriddenSource(
        string $versionString,
        string $dsn,
        array $paths
    ) : array {
        $configuration = array_replace_recursive(
            $this->defaultConfigurationOutput(),
            [
                'paths' => ['output' => '/tmp'],
            ]
        );

        // for the version we do want to check whether the defaults are kept; so instead of using array_replace,
        // we first extract the default key from our described default and change it to resemble our expected end-state
        $version = $configuration['versions']['1.0.0'];
        $version['api'][0]['source'] = [
            'dsn' => $dsn,
            'paths' => $paths,
        ];
        unset($configuration['versions']['1.0.0']);
        $configuration['versions'][$versionString] = $version;

        return $configuration;
    }
}
