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

namespace phpDocumentor\Transformer\Router;

use Mockery as m;

class ForFileProxyTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var Rule|m\MockInterface */
    private $ruleMock;

    /** @var ForFileProxy */
    private $fixture;

    /**
     * Initializes the fixture with mocked dependencies.
     */
    protected function setUp(): void
    {
        $this->ruleMock = m::mock('phpDocumentor\Transformer\Router\Rule');
        $this->fixture = new ForFileProxy($this->ruleMock);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\ForFileProxy::generate
     */
    public function testIfDirectorySeparatorsAreTranslated() : void
    {
        // Arrange
        $this->ruleMock->shouldReceive('generate')->with('test')->andReturn('/usr/bin/php');

        // Act
        $result = $this->fixture->generate('test', '\\');

        // Assert
        $this->assertSame('\\usr\\bin\\php', $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\ForFileProxy::generate
     */
    public function testIfNullIsReturnedIfNodeDoesNotMatch() : void
    {
        // Arrange
        $this->ruleMock->shouldReceive('generate')->with('test')->andReturn(false);

        // Act
        $result = $this->fixture->generate('test', '\\');

        // Assert
        $this->assertFalse($result);
    }
}
