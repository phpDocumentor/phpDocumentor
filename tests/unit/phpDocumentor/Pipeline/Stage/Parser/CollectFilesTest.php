<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FileCollector;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Parser\CollectFiles
 * @covers ::__construct
 * @covers ::<private>
 */
final class CollectFilesTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testFilesAreCollectedAndAddedToPayload() : void
    {
        $dns = Dsn::createFromString('file://.');
        $fileCollector = $this->prophesize(FileCollector::class);
        $fileCollector->getFiles(
            Argument::exact($dns),
            Argument::exact(['src']),
            Argument::exact([
                'paths' => [],
                'hidden' => null,
            ]),
            Argument::exact(['php'])
        )->shouldBeCalled()->willReturn([]);

        $fixture = new CollectFiles($fileCollector->reveal(), new NullLogger());

        $version = new VersionSpecification(
            '1.0.0',
            [
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
            null
        );

        $payload = new Payload(
            ['phpdocumentor' => ['versions' => ['1.0.0' => $version]]],
            $this->prophesize(ProjectDescriptorBuilder::class)->reveal()
        );

        $result = $fixture($payload);
        self::assertEquals([], $result->getFiles());
    }
}
