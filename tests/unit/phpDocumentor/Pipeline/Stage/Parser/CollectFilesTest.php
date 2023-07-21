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
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FileCollector;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Parser\CollectFiles
 * @covers ::__construct
 * @covers ::<private>
 */
final class CollectFilesTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @covers ::__invoke */
    public function testFilesAreCollectedAndAddedToPayload(): void
    {
        $fileCollector = $this->prophesize(FileCollector::class);
        $fileCollector->getFiles(
            Argument::type(Dsn::class),
            Argument::any(),
            Argument::any(),
            Argument::exact(['php']),
        )->shouldBeCalled()->willReturn([]);

        $fixture = new CollectFiles($fileCollector->reveal(), new NullLogger());

        $version = new VersionSpecification(
            '1.0.0',
            [
                $this->faker()->apiSpecification(),
            ],
            null,
        );

        $config = ['phpdocumentor' => ['versions' => ['1.0.0' => $version]]];
        $builder = $this->prophesize(ProjectDescriptorBuilder::class)->reveal();
        $apiSet = $this->faker()->apiSetDescriptor();
        $version = $this->faker()->versionDescriptor([$apiSet]);

        $payload = new ApiSetPayload($config, $builder, $version, $apiSet);

        $result = $fixture($payload);

        self::assertSame([], $result->getFiles());
    }
}
