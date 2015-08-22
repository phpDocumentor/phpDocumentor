<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Expected phpDocumentor3 configuration arrays used for unit testing.
 */
final class PhpDocumentor3ExpectedArrays
{
    /**
     * Provides the default phpDocumentor3 configuration array.
     *
     * @return array
     */
    public static function getDefaultArray()
    {
        return [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml'
                            ],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME'
                            ]
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ]
            ]
        ];
    }

    /**
     * Provides a phpDocumentor3 configuration array that contains empty extensions and empty markers.
     *
     * @return array
     */
    public static function getArrayWithEmptyExtensionsAndMarkers()
    {
        return [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => '',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => false,
                                'paths'    => [],
                            ],
                            'extensions'           => [],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => []
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ]
            ]
        ];
    }

    /**
     * Provides a phpDocumentor3 configuration array that contains multiple versions.
     *
     * @return array
     */
    public static function getArrayWithMultipleVersions()
    {
        return [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml'
                            ],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME'
                            ]
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ],
                    '2.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml'
                            ],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME'
                            ]
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                    1 => [
                        'location' => 'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean'
                    ]
                ]
            ]
        ];
    }
}
