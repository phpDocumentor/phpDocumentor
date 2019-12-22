<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Parser;

use League\Flysystem\MountManager;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemCollector;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Parser\SpecificationFactory;
use Psr\Log\NullLogger;

class CollectFilesIntegrationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider payloadProvider
     */
    public function testCollectFilesFromMultipleApiDefinitions(Payload $payload, int $filesExpected)
    {
        $fixture = new CollectFiles(
            new FlySystemCollector(
                new SpecificationFactory(),
                new FlySystemFactory(new MountManager())
            ),
            new NullLogger()
        );

        $result = $fixture($payload);

        self::assertCount($filesExpected, $result->getFiles());
    }

    public function payloadProvider()
    {
        return [
            'single dir' => [
                'payload' => $payload = new Payload(
                    [
                        'phpdocumentor' => [
                            'versions' => [
                                '1.0.0' => [
                                    'api' => [
                                        [
                                            'source' => [
                                                'dsn' => new Dsn('file://' . __DIR__ . '/assets/project1'),
                                                'paths' => [
                                                    0 => 'src',
                                                ],
                                            ],
                                            'ignore' => [
                                                'paths' => [],
                                                'hidden' => null,
                                            ],
                                            'extensions' => [
                                                'php',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    new ProjectDescriptorBuilder(new AssemblerFactory(), new Filter(new ClassFactory()))
                ),
                'files_expected' => 2,
            ],
            'multiple definitions' => [
                'payload' => $payload = new Payload(
                    [
                        'phpdocumentor' => [
                            'versions' => [
                                '1.0.0' => [
                                    'api' => [
                                        [
                                            'source' => [
                                                'dsn' => new Dsn('file://' . __DIR__ . '/assets/project1'),
                                                'paths' => [
                                                    0 => 'src',
                                                ],
                                            ],
                                            'ignore' => [
                                                'paths' => [],
                                                'hidden' => null,
                                            ],
                                            'extensions' => [
                                                'php',
                                            ],
                                        ],
                                        [
                                            'source' => [
                                                'dsn' => new Dsn('file://' . __DIR__ . '/assets/project2'),
                                                'paths' => [
                                                    0 => 'src',
                                                ],
                                            ],
                                            'ignore' => [
                                                'paths' => [],
                                                'hidden' => null,
                                            ],
                                            'extensions' => [
                                                'php',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    new ProjectDescriptorBuilder(new AssemblerFactory(), new Filter(new ClassFactory()))
                ),
                'files_expected' => 4,
            ]
        ];
    }
}
