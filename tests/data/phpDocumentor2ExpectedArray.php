<?php

final class PhpDocumentor2ExpectedArray
{
    public static function getDefaultArray()
    {
        return [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'build/docs',
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
                                'hidden'   => true,
                                'symlinks' => true,
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
                            ],
                        ],
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
}
