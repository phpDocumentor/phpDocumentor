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
        $source = $this->faker()->source();
        $set = new ApiSetDescriptor('api', $source, 'output', ApiSpecification::createDefault());
        $files = new Collection([new FileDescriptor('hash')]);

        self::assertEquals(new Collection(), $set->getFiles());

        $set->setFiles($files);

        self::assertSame($files, $set->getFiles());
    }
}
