<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Behaviour;

use Mockery as m;

/**
 * Tests for the Behaviour Collection class.
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    protected $fixture;

    /**
     * Initializes a bare fixture to test against.
     */
    public function setUp()
    {
        $this->fixture = new Collection();
    }

    /**
     * @covers phpDocumentor\Transformer\Behaviour\Collection::__construct
     */
    public function testIfProvidedBehavioursAreRegistered()
    {
        // Arrange
        $expected = array('a' => 'b');

        // Act
        $fixture = new Collection($expected);

        // Assert
        $this->assertAttributeSame($expected, 'behaviours', $fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Behaviour\Collection::addBehaviour
     */
    public function testAddABehaviour()
    {
        // Arrange
        $behaviourMock = m::mock('phpDocumentor\Transformer\Behaviour\BehaviourAbstract');

        // Act
        $this->fixture->addBehaviour($behaviourMock);

        // Assert
        $this->assertAttributeSame(array($behaviourMock), 'behaviours', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Behaviour\Collection::removeBehaviour
     */
    public function testRemoveABehaviour()
    {
        // Arrange
        $behaviourMock = m::mock('phpDocumentor\Transformer\Behaviour\BehaviourAbstract');
        $this->fixture->addBehaviour($behaviourMock);

        // Act
        $this->fixture->removeBehaviour($behaviourMock);

        // Assert
        $this->assertAttributeSame(array(), 'behaviours', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Behaviour\Collection::process
     */
    public function testProcessAllBehaviours()
    {
        // Arrange
        $projectMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $behaviourMock = m::mock('phpDocumentor\Transformer\Behaviour\BehaviourAbstract');
        $behaviourMock->shouldReceive('process')->with($projectMock)->andReturn($projectMock);
        $this->fixture->addBehaviour($behaviourMock);

        // Act
        $result = $this->fixture->process($projectMock);

        // Assert
        $this->assertSame($projectMock, $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Behaviour\Collection::count
     */
    public function testCountBehavioursInCollection()
    {
        // Arrange
        $behaviourMock = m::mock('phpDocumentor\Transformer\Behaviour\BehaviourAbstract');
        $this->fixture->addBehaviour($behaviourMock);

        // Assert
        $this->assertCount(1, $this->fixture);
        $this->assertSame(1, count($this->fixture));
        $this->assertSame(1, $this->fixture->count());
    }
}
