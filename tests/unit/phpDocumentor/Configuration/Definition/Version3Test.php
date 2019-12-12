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
            'default configuration'   => [
                [],
                [
                    'v' => '3',
                    'title' => 'my-doc',
                    'use-cache' => true,
                    'versions' => [
                        '1.0.0' => [
                            'folder' => '',
                            'number' => '1.0.0',
                            'api' => [
                                [
                                    'format' => 'php',
                                    'visibility' => ['public', 'protected', 'private'],
                                    'default-package-name' => 'Application',
                                    'encoding' => 'utf-8',
                                    'source' => [
                                        'dsn' => 'file://.',
                                        'paths' => [
                                            '.'
                                        ]
                                    ],
                                    'ignore' => [
                                        'hidden' => true,
                                        'symlinks' => true,
                                    ],
                                    'extensions' => [
                                        'extensions' => ['php', 'php3', 'phtml']
                                    ],
                                    'include-source' => false,
                                    'validate' => false,
                                    'markers' => [ 'markers' => ['TODO', 'FIXME']]
                                ]
                            ]
                        ]
                    ],
                    'templates' => [
                        self::DEFAULT_TEMPLATE_NAME => [
                            'name' => self::DEFAULT_TEMPLATE_NAME
                        ]
                    ]
                ]
            ],
        ];
    }
}
