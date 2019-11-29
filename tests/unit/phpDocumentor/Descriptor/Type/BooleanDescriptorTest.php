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

class BooleanDescriptorTest extends MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Descriptor\Type\BooleanDescriptor::getName
     * @covers \phpDocumentor\Descriptor\Type\BooleanDescriptor::__toString
     */
    public function testIfNameCanBeReturned() : void
    {
        $fixture = new BooleanDescriptor();

        $this->assertSame('boolean', $fixture->getName());
        $this->assertSame('boolean', (string) $fixture);
    }
}
