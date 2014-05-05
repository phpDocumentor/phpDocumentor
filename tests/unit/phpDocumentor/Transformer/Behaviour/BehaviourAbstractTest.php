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
 * Tests for the Behavioural base class.
 */
class BehaviourAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Behaviour\BehaviourAbstract::setTransformer
     * @covers phpDocumentor\Transformer\Behaviour\BehaviourAbstract::getTransformer
     */
    public function testSettingAndRetrievingTheTransformer()
    {
        // Arrange
        $transformerMock = m::mock('\phpDocumentor\Transformer\Transformer');
        /** @var m\MockInterface|\phpDocumentor\Transformer\Behaviour\BehaviourAbstract $fixture */
        $fixture = m::mock('phpDocumentor\Transformer\Behaviour\BehaviourAbstract[process]');

        // Act
        $fixture->setTransformer($transformerMock);
        $result = $fixture->getTransformer();

        // Assert
        $this->assertSame($transformerMock, $result);
    }
}
