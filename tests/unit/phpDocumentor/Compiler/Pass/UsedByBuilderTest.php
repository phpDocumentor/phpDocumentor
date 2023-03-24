<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

final class UsedByBuilderTest extends TestCase
{
    use Faker;

    private UsedByBuilder $pass;

    protected function setUp(): void
    {
        $this->pass = new UsedByBuilder();
    }

    public function testCounterPartOfUsesWillGetTagUsedBy(): void
    {
        $class = $this->faker()->classDescriptor($this->faker()->fqsen());
        $counterClass = $this->faker()->classDescriptor($this->faker()->fqsen());

        $usesTag = new UsesDescriptor('uses');
        $usesTag->setReference($counterClass);
        $class->getTags()->set('uses', Collection::fromClassString(UsesDescriptor::class, [$usesTag]));

        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor->getIndex('elements')->add($class);
        $apiSetDescriptor->getIndex('elements')->add($counterClass);

        $this->pass->__invoke($apiSetDescriptor);

        $usedBy = $counterClass->getTags()->fetch(
            'used-by',
            Collection::fromClassString(UsesDescriptor::class)
        );

        self::assertSame($class, $usedBy->get(0)->getReference());
    }
}
