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

namespace phpDocumentor\Pipeline\Stage\Parser;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FileCollector;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Parser\CollectFiles
 * @covers ::__construct
 * @covers ::<private>
 */
final class CollectFilesTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     */
    public function testFilesAreCollectedAndAddedToPayload() : void
    {
        $dns = Dsn::createFromString('file://.');
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
                                        'paths' => [0 => 'src'],
                                    ],
                                    'ignore' => [
                                        'paths' => [],
                                        'hidden' => null,
                                    ],
                                    'extensions' => ['php'],
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
