<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration\Factory;

/**
 * Expected phpDocumentor3 configuration arrays used for unit testing.
 */
final class Version3ExpectedArrays
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
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api' => [
                            0 => [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => 'src',
                                    ],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'paths' => [
                                        0 => 'src/ServiceDefinitions.php',
                                    ],
                                ],
                                'extensions' => [
                                    0 => 'php',
                                    1 => 'php3',
                                    2 => 'phtml',
                                ],
                                'visibility' => ['public'],
                                'default-package-name' => 'Default',
                                'include-source' => false,
                                'markers' => [
                                    0 => 'TODO',
                                    1 => 'FIXME',
                                ],
                            ],
                        ],
                        'guide' => [
                            0 => [
                                'format' => 'rst',
                                'source' => [
                                    'dsn' => 'file://../phpDocumentor/phpDocumentor2',
                                    'paths' => [
                                        0 => 'docs',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ],
            ],
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
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '' => [
                        'folder' => '',
                        'api' => [
                            0 => [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => '.',
                                    ],
                                ],
                                'ignore' => [
                                    'hidden' => false,
                                    'paths' => [],
                                ],
                                'extensions' => [],
                                'visibility' => ['public'],
                                'default-package-name' => 'Default',
                                'include-source' => false,
                                'markers' => [],
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ],
            ],
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
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => 'earliest',
                    ],
                    '2.0.0' => [
                        'folder' => 'latest',
                    ],
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ],
            ],
        ];
    }

    public static function getArrayWithMultipleApis()
    {
        return [
            'phpdocumentor' => [
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api' => [
                            0 => [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => 'src',
                                    ],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'paths' => [
                                        0 => 'src/ServiceDefinitions.php',
                                    ],
                                ],
                                'extensions' => [
                                    0 => 'php',
                                    1 => 'php3',
                                    2 => 'phtml',
                                ],
                                'visibility' => ['public'],
                                'default-package-name' => 'Default',
                                'include-source' => true,
                                'markers' => [
                                    0 => 'TODO',
                                    1 => 'FIXME',
                                ],
                            ],
                            1 => [
                                'format' => 'php3',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => 'src',
                                    ],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'paths' => [
                                        0 => 'src/ServiceDefinitions.php',
                                    ],
                                ],
                                'extensions' => [
                                    0 => 'php',
                                    1 => 'php3',
                                    2 => 'phtml',
                                ],
                                'visibility' => ['public'],
                                'default-package-name' => 'Default',
                                'include-source' => false,
                                'markers' => [
                                    0 => 'TODO',
                                    1 => 'FIXME',
                                ],
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ],
            ],
        ];
    }

    public static function getArrayWithMultipleGuides()
    {
        return [
            'phpdocumentor' => [
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'guide' => [
                            0 => [
                                'format' => 'rst',
                                'source' => [
                                    'dsn' => 'file://../phpDocumentor/phpDocumentor2',
                                    'paths' => [
                                        0 => 'docs',
                                    ],
                                ],
                            ],
                            1 => [
                                'format' => 'rst',
                                'source' => [
                                    'dsn' => 'file://../phpDocumentor/phpDocumentor3',
                                    'paths' => [
                                        0 => 'docs',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => 'clean',
                    ],
                ],
            ],
        ];
    }

    public static function getArrayWithMultipleTemplates()
    {
        return [
            'phpdocumentor' => [
                'use-cache' => true,
                'paths' => [
                    'output' => 'file://build/docs',
                    'cache' => '/tmp/phpdoc-doc-cache',
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api' => [
                            0 => [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => 'src',
                                    ],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'paths' => [],
                                ],
                                'extensions' => [
                                    0 => 'php',
                                    1 => 'php3',
                                    2 => 'phtml',
                                ],
                                'visibility' => ['public', 'protected', 'private'],
                                'default-package-name' => 'Default',
                                'include-source' => false,
                                'markers' => [
                                    0 => 'TODO',
                                    1 => 'FIXME',
                                ],
                                'encoding' => 'utf8',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                        'guide' => [
                            0 => [
                                'format' => 'rst',
                                'source' => [
                                    'dsn' => 'file://.',
                                    'paths' => [
                                        0 => 'docs',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    0 => [
                        'name' => 'clean',
                        'location' =>
                            'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean',
                    ],
                    1 => [
                        'name' => 'tainted',
                        'location' =>
                            'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/tainted',
                    ],
                ],
            ],
        ];
    }
}
