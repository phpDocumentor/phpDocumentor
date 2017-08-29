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
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\Php\Argument;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ArgumentAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgumentAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new ArgumentAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testCreateArgumentDescriptorFromReflector()
    {
        // Arrange
        $name = 'goodArgument';
        $type = 'boolean';

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame([$type], $descriptor->getTypes());
        $this->assertSame(null, $descriptor->getDefault());
        $this->assertSame(false, $descriptor->isByReference());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::overwriteTypeAndDescriptionFromParamTag
     */
    public function testIfTypeAndDescriptionAreSetFromParamDescriptor()
    {
        // Arrange
        $name = 'goodArgument';
        $type = 'boolean';

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);

        // Mock a paramDescriptor
        $paramDescriptorTagMock = m::mock('phpDocumentor\Descriptor\Tag\ParamDescriptor');
        $paramDescriptorTagMock->shouldReceive('getVariableName')->once()->andReturn($name);
        $paramDescriptorTagMock->shouldReceive('getDescription')->once()->andReturn('Is this a good argument, or nah?');
        $paramDescriptorTagMock->shouldReceive('getTypes')->once()->andReturn([$type]);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, array($paramDescriptorTagMock));

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame([$type], $descriptor->getTypes());
        $this->assertSame(null, $descriptor->getDefault());
        $this->assertSame(false, $descriptor->isByReference());
    }

    /**
     * @param $name
     * @param $type
     * @return Argument
     */
    protected function givenAnArgumentReflectorWithNameAndType($name, $type)
    {
        $argument = new Argument($name);
        $argument->addType($type);

        return $argument;
    }
}
