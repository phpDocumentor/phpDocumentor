<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ArgumentAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgumentAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new ArgumentAssembler();
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @return void
     */
    public function testCreateArgumentDescriptorFromReflector()
    {
        $name = 'goodArgument';
        $type = 'boolean';

        $argumentReflectorMock = m::mock('phpDocumentor\Reflection\FunctionReflector\ArgumentReflector');
        $argumentReflectorMock->shouldReceive('getName')->andReturn($name);
        $argumentReflectorMock->shouldReceive('getType')->andReturn($type);
        $argumentReflectorMock->shouldReceive('getDefault')->andReturn(false); // Turns out its a bad argument ;)
        $argumentReflectorMock->shouldReceive('isByRef')->andReturn(false);

        $descriptor = $this->fixture->create($argumentReflectorMock);

        $this->assertSame($name, $descriptor->getName());
        $this->assertSame(array($type), $descriptor->getTypes());
        $this->assertSame(false, $descriptor->getDefault());
    }
}
