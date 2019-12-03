<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration\Factory;

use phpDocumentor\Dsn;
use phpDocumentor\Path;
use function getcwd;

/**
 * Expected phpDocumentor2 configuration arrays used for unit testing.
 */
final class Version2ExpectedArray
{
    /**
     * Provides the default phpDocumentor2 configuration array.
     *
     * @return array
     */
    public static function getDefaultArray() : array
    {
        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('build/docs'),
                    'cache' => new Path('build/cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => [0 => 'src'],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'symlinks' => true,
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
                                'encoding' => 'utf-8',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    ['name' => 'clean'],
                ],
            ],
        ];
    }

    /**
     * Provides the phpDocumentor2 configuration array with multiple ignore paths.
     *
     * @return array
     */
    public static function getArrayWithMultipleIgnorePaths() : array
    {
        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('build/docs'),
                    'cache' => new Path('/build/cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => [0 => 'src'],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'symlinks' => true,
                                    'paths' => [
                                        0 => 'vendor/*',
                                        1 => 'logs/*',
                                    ],
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
                                'encoding' => 'utf-8',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    ['name' => 'clean'],
                ],
            ],
        ];
    }

    public static function getCustomTargetConfig() : array
    {
        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('build/api/docs'),
                    'cache' => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => [0 => 'src'],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'symlinks' => true,
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
                                'encoding' => 'utf-8',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    ['name' => 'clean'],
                ],
            ],
        ];
    }

    /**
     * Provides a configuration with visibility
     *
     * @return array
     */
    public static function getDefinedVisibility() : array
    {
        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('build/docs'),
                    'cache' => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => [0 => 'src'],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'symlinks' => true,
                                    'paths' => [],
                                ],
                                'extensions' => [
                                    0 => 'php',
                                    1 => 'php3',
                                    2 => 'phtml',
                                ],
                                'visibility' => ['public', 'protected'],
                                'default-package-name' => 'Default',
                                'include-source' => false,
                                'markers' => [
                                    0 => 'TODO',
                                    1 => 'FIXME',
                                ],
                                'encoding' => 'utf-8',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    ['name' => 'clean'],
                ],
            ],
        ];
    }

    /**
     * Provides the phpDocumentor2 configuration with encoding.
     *
     * @return array
     */
    public static function getCustomEncoding() : array
    {
        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('build/docs'),
                    'cache' => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            [
                                'format' => 'php',
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => [0 => 'src'],
                                ],
                                'ignore' => [
                                    'hidden' => true,
                                    'symlinks' => true,
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
                                'encoding' => 'ISO-8859-1',
                                'ignore-tags' => [],
                                'validate' => false,
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    ['name' => 'clean'],
                ],
            ],
        ];
    }
}
