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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ApiSetDescriptor
 * @covers ::<private>
 * @covers ::__construct
 */
final class ApiSetDescriptorTest extends TestCase
{
    use Faker;

    /**
     * @covers ::setFiles
     * @covers ::getFiles
     */
    public function testContainsAListingOfFilesInThisSet(): void
    {
        $source = self::faker()->source();
        $set = new ApiSetDescriptor('api', $source, 'output', ApiSpecification::createDefault());
        $files = new Collection([self::faker()->fileDescriptor()]);

        self::assertEquals(new Collection(), $set->getFiles());

        $set->setFiles($files);

        self::assertSame($files, $set->getFiles());
    }

    /**
     * @covers ::setIndexes
     * @covers ::getIndexes
     */
    public function testCanHaveASeriesOfIndexes(): void
    {
        $source = self::faker()->source();
        $set = new ApiSetDescriptor('api', $source, 'output', ApiSpecification::createDefault());
        $indexes = new Collection(
            [
                'elements' => Collection::fromInterfaceString(
                    ElementInterface::class,
                    [self::faker()->fileDescriptor()],
                ),
            ],
        );

        $expectedDefault = Collection::fromInterfaceString(
            ElementInterface::class,
            ['elements' => Collection::fromInterfaceString(ElementInterface::class)],
        );

        self::assertEquals($expectedDefault, $set->getIndexes());

        $set->setIndexes($indexes);

        self::assertSame($indexes, $set->getIndexes());
    }

    /** @covers ::findElement */
    public function testCanFetchAnElementBasedOnItsFqsen(): void
    {
        $source = self::faker()->source();
        $set = new ApiSetDescriptor('api', $source, 'output', ApiSpecification::createDefault());

        $fqsen = self::faker()->fqsen();
        $descriptor = self::faker()->classDescriptor($fqsen);

        self::assertEquals(new Collection(), $set->getIndexes()['elements']);

        $set->getIndexes()['elements']->set((string) $fqsen, $descriptor);

        self::assertSame($descriptor, $set->findElement($fqsen));
    }
}
