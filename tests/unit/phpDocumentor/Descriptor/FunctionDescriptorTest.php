<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\FunctionDescriptor
 */
class FunctionDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var FunctionDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new FunctionDescriptor();
    }

    /**
     * @covers ::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'arguments', $this->fixture);
    }

    /**
     * @covers ::setArguments
     * @covers ::getArguments
     */
    public function testSettingAndGettingArguments()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $mockInstance = m::mock('phpDocumentor\Descriptor\Collection');
        $mock = &$mockInstance;

        $this->fixture->setArguments($mock);

        $this->assertSame($mockInstance, $this->fixture->getArguments());
    }

    /**
     * @covers ::getResponse
     * @covers ::setReturnType
     */
    public function testSettingAndGettingReturnType()
    {
        $stringType = new String_();
        $this->fixture->setReturnType($stringType);

        $this->assertSame('return', $this->fixture->getResponse()->getName());
        $this->assertSame($stringType, $this->fixture->getResponse()->getType());
    }
}
