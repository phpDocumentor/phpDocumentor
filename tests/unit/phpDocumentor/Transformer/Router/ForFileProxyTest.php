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

class ForFileProxyTest extends \PHPUnit_Framework_TestCase
{
    /** @var Rule|m\MockInterface */
    private $ruleMock;

    /** @var ForFileProxy */
    private $fixture;

    /**
     * Initializes the fixture with mocked dependencies.
     */
    public function setUp()
    {
        $this->ruleMock = m::mock('phpDocumentor\Transformer\Router\Rule');
        $this->fixture = new ForFileProxy($this->ruleMock);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\ForFileProxy::__construct
     */
    public function testIfDependenciesAreRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->ruleMock, 'rule', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\ForFileProxy::generate
     */
    public function testIfDirectorySeparatorsAreTranslated()
    {
        // Arrange
        $this->ruleMock->shouldReceive('generate')->with('test')->andReturn('/usr/bin/php');

        // Act
        $result = $this->fixture->generate('test', '\\');

        // Assert
        $this->assertSame('\\usr\\bin\\php', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\ForFileProxy::generate
     */
    public function testIfNullIsReturnedIfNodeDoesNotMatch()
    {
        // Arrange
        $this->ruleMock->shouldReceive('generate')->with('test')->andReturn(false);

        // Act
        $result = $this->fixture->generate('test', '\\');

        // Assert
        $this->assertFalse($result);
    }
}
