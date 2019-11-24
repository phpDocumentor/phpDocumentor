<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Stage\Parser;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FileCollector;
use Psr\Log\NullLogger;

final class CollectFilesTest extends MockeryTestCase
{
    public function testFilesAreCollectedAndAddedToPayload()
    {
        $dns = new Dsn('file://.');
        $fileCollector = m::mock(FileCollector::class);
        $fileCollector->expects('getFiles')
            ->with(
                $dns,
                ['src'],
                [
                    'paths' => [],
                    'hidden' => null,
                ],
                ['php']
            )
            ->andReturn([]);

        $fixture = new CollectFiles($fileCollector, new NullLogger());

        $payload = new Payload(
            [
                'phpdocumentor' => [
                    'versions' => [
                        '1.0.0' => [
                            'api' => [
                                [
                                    'source' => [
                                        'dsn' => $dns,
                                        'paths' => [
                                            0 => 'src',
                                        ],
                                    ],
                                    'ignore' => [
                                        'paths' => [],
                                        'hidden' => null,
                                    ],
                                    'extensions' => [
                                        'php'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            m::mock(ProjectDescriptorBuilder::class)
        );

        $result = $fixture($payload);
        self::assertEquals([], $result->getFiles());
    }
}
