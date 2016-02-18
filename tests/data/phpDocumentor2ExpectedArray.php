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
use phpDocumentor\DomainModel\Path;

/**
 * Expected phpDocumentor2 configuration arrays used for unit testing.
 */
final class PhpDocumentor2ExpectedArray
{
    /**
     * Provides the default phpDocumentor2 configuration array.
     *
     * @return array
     */
    public static function getDefaultArray()
    {
        return [
            'phpdocumentor' => [
                'use-cache' => true,
                'paths'     => [
                    'output' => new \phpDocumentor\DomainModel\Dsn('build/docs'),
                    'cache'  => new \phpDocumentor\DomainModel\Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => '',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src',
                                ],
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'symlinks' => true,
                                'paths'    => [],
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml',
                            ],
                            'visibility'           => ['public'],
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME',
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
     * Provides the phpDocumentor2 configuration array with multiple ignore paths.
     *
     * @return array
     */
    public static function getArrayWithMultipleIgnorePaths()
    {
        return [
            'phpdocumentor' => [
                'use-cache' => true,
                'paths'    => [
                    'output' => new \phpDocumentor\DomainModel\Dsn('build/docs'),
                    'cache'  => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src',
                                ],
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'symlinks' => true,
                                'paths'    => [
                                    0 => 'vendor/*',
                                    1 => 'logs/*',
                                ],
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml',
                            ],
                            'visibility'           => ['public'],
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME',
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
}
