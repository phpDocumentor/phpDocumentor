<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

use function md5;
use function serialize;

/**
 * @coversDefaultClass \phpDocumentor\Extension\ExtensionsResource
 */
final class ExtensionsResouceTest extends TestCase
{
    use Faker;

    /**
     * @covers ::__construct
     * @covers ::getHash
     * @covers ::__toString
     */
    public function testHash(): void
    {
        $expected = md5(serialize([$this->faker()->extensionManifest()]));

        $resource = new ExtensionsResource([$this->faker()->extensionManifest()]);

        self::assertSame($expected, $resource->getHash());
    }
}
