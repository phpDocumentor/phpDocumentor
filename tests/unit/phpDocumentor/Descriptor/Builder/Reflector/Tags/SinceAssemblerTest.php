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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\Tag\SinceDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Since;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\Tags\SinceAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\SinceAssembler
 
 */
final class SinceAssemblerTest extends TestCase
{
    use ProphecyTrait;

    private SinceAssembler $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private ObjectProphecy $builderMock;

    protected function setUp(): void
    {
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new SinceAssembler();
        $this->fixture->setBuilder($this->builderMock->reveal());
    }

    /**
     * @covers ::create
     * @covers ::buildDescriptor
     */
    public function testCreateSinceDescriptorFromSinceTag(): void
    {
        $name = 'since';
        $description = 'a since tag';
        $version = '1.0.0';

        $sinceTagMock = $this->givenASinceTag($version, $description);

        /** @var SinceDescriptor $descriptor */
        $descriptor = $this->fixture->create($sinceTagMock);

        self::assertSame($name, $descriptor->getName());
        self::assertSame($description, (string) $descriptor->getDescription());
        self::assertSame($version, $descriptor->getVersion());
        self::assertSame([], $descriptor->getErrors()->getAll());
    }

    /**
     * @covers ::create
     * @covers ::buildDescriptor
     */
    public function testCreateSinceDescriptorFromSinceTagWithEmptyVersion(): void
    {
        $name = 'since';
        $description = 'a since tag';

        $tag = $this->givenASinceTag(null, $description);

        /** @var SinceDescriptor $descriptor */
        $descriptor = $this->fixture->create($tag);

        self::assertSame($name, $descriptor->getName());
        self::assertSame($description, (string) $descriptor->getDescription());
        self::assertSame('', $descriptor->getVersion());
        self::assertSame([], $descriptor->getErrors()->getAll());
    }

    private function givenASinceTag(string|null $version, string $description): Since
    {
        return new DocBlock\Tags\Since(
            $version,
            new DocBlock\Description($description),
        );
    }
}
