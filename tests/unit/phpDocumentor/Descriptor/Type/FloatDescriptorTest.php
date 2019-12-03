<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Type;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Type\FloatDescriptor
 */
final class FloatDescriptorTest extends TestCase
{
    /**
     * @covers ::getName
     * @covers ::__toString
     */
    public function testIfNameCanBeReturned() : void
    {
        $fixture = new FloatDescriptor();

        $this->assertSame('float', $fixture->getName());
        $this->assertSame('float', (string) $fixture);
    }
}
