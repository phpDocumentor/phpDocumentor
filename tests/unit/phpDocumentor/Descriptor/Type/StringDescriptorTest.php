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

use Mockery\Adapter\Phpunit\MockeryTestCase;

class StringDescriptorTest extends MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Descriptor\Type\StringDescriptor::getName
     * @covers \phpDocumentor\Descriptor\Type\StringDescriptor::__toString
     */
    public function testIfNameCanBeReturned() : void
    {
        $fixture = new StringDescriptor();

        $this->assertSame('string', $fixture->getName());
        $this->assertSame('string', (string) $fixture);
    }
}
