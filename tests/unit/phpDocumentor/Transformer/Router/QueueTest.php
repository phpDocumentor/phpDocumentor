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

namespace phpDocumentor\Transformer\Router;

use Mockery as m;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    /** @var Queue */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp()
    {
        $this->fixture = new Queue();
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Queue::match
     */
    public function testFirstRuleIsReturnedForNodeBasedOnPriorityOrder()
    {
        // Arrange
        $nodeName = 'test';
        $expected = m::mock('phpDocumentor\Transformer\Router\Rule');
        $this->fixture->insert($this->givenARouterMatchingNodeWithResult($nodeName, false), 0);
        $this->fixture->insert($this->givenARouterMatchingNodeWithResult($nodeName, 'test'), 500);
        $this->fixture->insert($this->givenARouterMatchingNodeWithResult($nodeName, $expected), 1000);

        // Act
        $result = $this->fixture->match($nodeName);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Queue::match
     */
    public function testNullIsReturnedWhenNoMatchingRuleCanBeFound()
    {
        $nodeName = 'test';
        $this->fixture->insert($this->givenARouterMatchingNodeWithResult($nodeName, false), 0);

        // Act
        $result = $this->fixture->match($nodeName);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Constructs a router mock that matches the provided nodeName and upon matching returns the provided value.
     *
     * @param string $nodeName
     * @param mixed  $returns
     *
     * @return m\MockInterface|RouterAbstract
     */
    protected function givenARouterMatchingNodeWithResult($nodeName, $returns)
    {
        $router = m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
        $router->shouldReceive('match')->with($nodeName)->andReturn($returns);

        return $router;
    }
}
