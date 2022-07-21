<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

final class UsedByBuilderTest extends TestCase
{
    use Faker;

    public function testCounterPartOfUsesWillGetTagUsedBy(): void
    {
        $class = $this->faker()->classDescriptor($this->faker()->fqsen());
        $counterClass = $this->faker()->classDescriptor($this->faker()->fqsen());

        $usesTag = new UsesDescriptor('uses');
        $usesTag->setReference($counterClass);
        $class->getTags()->set('uses', Collection::fromClassString(UsesDescriptor::class, [$usesTag]));

        $projectDescriptor = new ProjectDescriptor('test');
        $projectDescriptor->setIndexes(
            new Collection(
                ['elements' => Collection::fromClassString(ClassDescriptor::class, [$class, $counterClass])]
            )
        );

        $pass = new UsedByBuilder();
        $pass->execute($projectDescriptor);

        $usedBy = $counterClass->getTags()->fetch(
            'used-by',
            Collection::fromClassString(UsesDescriptor::class)
        );

        self::assertSame($class, $usedBy->get(0)->getReference());
    }
}
