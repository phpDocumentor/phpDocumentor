<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Reflection\DocBlock\Description;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Tag\InheritDescriptionTagDescriptor
 */
final class InheritDescriptionTagDescriptorTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     *
     * @covers ::create
     * @covers ::__construct
     * @covers ::getDescription
     * @covers ::render
     * @covers ::__toString
     */
    public function testDescriptionIsSet() : void
    {
        $desciption = new Description('foo');
        $tag = InheritDescriptionTagDescriptor::create('ignored', $desciption);

        self::assertSame($desciption, $tag->getDescription());
        self::assertSame('foo', $tag->render());
        self::assertSame('foo', (string) $tag);
    }
}
