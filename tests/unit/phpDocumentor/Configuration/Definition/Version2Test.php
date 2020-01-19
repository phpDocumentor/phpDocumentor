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
use PHPUnit\Framework\TestCase;
use function getcwd;

final class Version2Test extends TestCase
{
    private const DEFAULT_TEMPLATE_NAME = 'default';

    /**
     * @dataProvider provideTestConfiguration
     */
    public function testLoadingADefaultConfigWorks(array $inputConfig, array $expectedConfig) : void
    {
        $configuration = new Version2(self::DEFAULT_TEMPLATE_NAME);
        $node = $configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    public function testThatConfigurationCanBeUpgradedToAVersion3InputArray() : void
    {
        $configuration = new Version2(self::DEFAULT_TEMPLATE_NAME);

        $upgradedConfiguration = $configuration->upgrade($this->defaultConfigurationOutput());

        $this->assertSame(
            [
                SymfonyConfigFactory::FIELD_CONFIG_VERSION => '3',
                'paths' => [
                    'output' => 'build/api',
                    'cache' => 'build/api-cache',
                ],
                'version' => [
                    [
                        'number' => '1.0.0',
                        'api' => [
                            [
                                'default-package-name' => 'Application',
                                'source' => [
                                    'paths' => [getcwd()],
                                ],
                                'ignore' => [
                                    'paths' => [],
                                ],
                                'extensions' => [
                                    'extensions' => ['php', 'php3', 'phtml'],
                                ],
                                'markers' => ['markers' => ['TODO', 'FIXME']],
                            ],
                        ],
                    ],
                ],
            ],
            $upgradedConfiguration
        );
    }

    public function provideTestConfiguration() : array
    {
        return [
            'default configuration' => [[], $this->defaultConfigurationOutput()],
        ];
    }

    private function defaultConfigurationOutput() : array
    {
        return [
            SymfonyConfigFactory::FIELD_CONFIG_VERSION => '2',
            'title' => 'Documentation',
            'parser' => [
                'default-package-name' => 'Application',
                'visibility' => ['public', 'protected', 'private'],
                'target' => 'build/api-cache',
                'encoding' => 'utf-8',
                'extensions' => ['extensions' => ['php', 'php3', 'phtml']],
                'markers' => ['items' => ['TODO', 'FIXME']],
            ],
            'files' => [
                'ignore-hidden' => true,
                'ignore-symlinks' => true,
                'directories' => [getcwd()],
                'files' => [],
                'ignores' => [],
            ],
            'transformer' => ['target' => 'build/api'],
            'transformations' => [
                'templates' => [
                    self::DEFAULT_TEMPLATE_NAME => ['name' => self::DEFAULT_TEMPLATE_NAME],
                ],
            ],
            'logging' => ['level' => 'error'],
        ];
    }
}
