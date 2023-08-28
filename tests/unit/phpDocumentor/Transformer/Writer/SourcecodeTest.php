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

namespace phpDocumentor\Transformer\Writer;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Sourcecode
 * @covers ::__construct
 * @covers ::<private>
 */
final class SourcecodeTest extends MockeryTestCase
{
    use Faker;
    use ProphecyTrait;

    private Sourcecode $sourceCode;

    protected function setUp(): void
    {
        $pathGenerator = $this->prophesize(PathGenerator::class);
        $pathGenerator->generate(
            Argument::type(FileDescriptor::class),
            Argument::type(Transformation::class),
        )->willReturn((string) $this->faker()->path());
        $this->sourceCode = new Sourcecode(
            $pathGenerator->reveal(),
        );
        $this->transformer = new Transformer(
            new Collection([]),
            new NullLogger(),
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
        );
        $this->transformer->setDestination(new Filesystem(new MemoryAdapter()));
    }

    /** @covers ::transform */
    public function testNoInteractionWithTransformationWhenSourceIsIncluded(): void
    {
        $transformation = $this->prophesize(Transformation::class);
        $transformation->getTransformer()->willReturn($this->transformer)->shouldBeCalled();

        $api = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->giveProjectDescriptor($api);

        $this->sourceCode->transform($transformation->reveal(), $projectDescriptor, $api);
    }

    private function giveProjectDescriptor(ApiSetDescriptor $apiDescriptor): ProjectDescriptor
    {
        $projectDescriptor = $this->faker()->projectDescriptor();
        $versionDescriptor = $this->faker()->versionDescriptor([$apiDescriptor]);
        $projectDescriptor->getVersions()->add($versionDescriptor);
        $apiDescriptor->setFiles(
            DescriptorCollection::fromClassString(
                DocumentationSetDescriptor::class,
                [$this->faker()->fileDescriptor()],
            ),
        );

        return $projectDescriptor;
    }
}
