<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Parser;

use League\Flysystem\MountManager;
use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\StripIgnore;
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
        $scheme = $this->createFileScheme();

        return [
            'single dir' => [
                'payload' => $payload = new Payload(
                    [
                        'phpdocumentor' => [
                            'versions' => [
                                '1.0.0' => new VersionSpecification(
                                    '1.0.0',
                                    [
                                        [
                                            'source' => [
                                                'dsn' => Dsn::createFromString($scheme . __DIR__ . '/assets/project1'),
                                                'paths' => [
                                                    0 => '/src**/*',
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
                                    []
                                ),
                            ],
                        ],
                    ],
                    new ProjectDescriptorBuilder(new AssemblerFactory(), new Filter([new StripIgnore()]))
                ),
                'files_expected' => 2,
            ],
            'multiple definitions' => [
                'payload' => $payload = new Payload(
                    [
                        'phpdocumentor' => [
                            'versions' => [
                                '1.0.0' => new VersionSpecification(
                                    '1.0.0',
                                    [
                                        [
                                            'source' => [
                                                'dsn' => Dsn::createFromString($scheme . __DIR__ . '/assets/project1'),
                                                'paths' => [
                                                    0 => '/src/**/*',
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
                                                'dsn' => Dsn::createFromString($scheme . __DIR__ . '/assets/project2'),
                                                'paths' => [
                                                    0 => '/src/**/*',
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
                                    []
                                ),
                            ],
                        ],
                    ],
                    new ProjectDescriptorBuilder(new AssemblerFactory(), new Filter([new StripIgnore()]))
                ),
                'files_expected' => 4,
            ],
        ];
    }

    /**
     * Adds extra / on windows
     *
     * Because windows is using prefixes its drives with letters the first / is
     * after that letter. Since we do not know in the this test the value of __DIR__
     * an extra / is added on windows to make sure we are providing a valid scheme.
     */
    private function createFileScheme(): string
    {
        $scheme = 'file://';

        if (PHP_OS_FAMILY === 'Windows') {
            $scheme .= '/';
        }
        return $scheme;
    }
}
