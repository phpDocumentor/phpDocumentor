<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocBlock;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class StripIgnoredTagsTest extends TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;

    /** @var ObjectProphecy */
    private $builder;

    /** @var StripIgnoredTags */
    private $fixture;

    protected function setUp() : void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new StripIgnoredTags($this->builder->reveal());
    }

    public function testIgnoresNonTagDescriptors() : void
    {
        $object = new class implements Filterable {
            public function getName() : string
            {
                return 'someTag';
            }

            public function getDescription() : ?DocBlock\DescriptionDescriptor
            {
                return null;
            }

            public function setErrors(Collection $errors) : void
            {
            }
        };

        self::assertSame($object, ($this->fixture)($object));
    }

    public function testFiltersIgnoredTags() : void
    {
        $object = new TagDescriptor('someTag');

        $this->builder->getIgnoredTags()->willReturn(['someTag']);

        self::assertNull(($this->fixture)($object));
    }
}
