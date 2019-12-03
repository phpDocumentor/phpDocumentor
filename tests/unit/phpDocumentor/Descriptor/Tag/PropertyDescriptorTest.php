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

namespace phpDocumentor\Descriptor\Tag;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the PropertyDescriptor class.
 */
class PropertyDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_NAME = 'variableName';

    /** @var PropertyDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new PropertyDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract::setVariableName
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract::getVariableName
     */
    public function testSetAndGetVariableName() : void
    {
        $this->assertEmpty($this->fixture->getVariableName());

        $this->fixture->setVariableName(self::EXAMPLE_NAME);
        $result = $this->fixture->getVariableName();

        $this->assertSame(self::EXAMPLE_NAME, $result);
    }
}
