<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use Interop\Container\ContainerInterface;
use Mockery as m;

/**
 * Tests the class responsible for passing command handlers from the DIC to the CommandBus.
 * @coversDefaultClass phpDocumentor\Application\Commands\ContainerLocator
 */
class ContainerLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getHandlerForCommand
     */
    public function testIfHandlerIsReturnedForAProvidedCommandMapping()
    {
        $handler = new \stdClass();
        $containerMock = m::mock(ContainerInterface::class);
        $containerMock->shouldReceive('get')->with('stdClass')->andReturn($handler);
        $fixture = new ContainerLocator($containerMock, [\stdClass::class => 'stdClass']);

        $receivedHandler = $fixture->getHandlerForCommand(\stdClass::class);

        $this->assertSame($receivedHandler, $handler);
    }

    /**
     * @covers ::__construct
     * @covers ::getHandlerForCommand
     */
    public function testIfHandlerIsWhenACommandMappingIsNotProvided()
    {
        $handler = new DummyCommandHandler();
        $containerMock = m::mock(ContainerInterface::class);
        $containerMock->shouldReceive('get')->with(DummyCommandHandler::class)->andReturn($handler);
        $fixture = new ContainerLocator($containerMock);

        $receivedHandler = $fixture->getHandlerForCommand(DummyCommand::class);

        $this->assertSame($receivedHandler, $handler);
    }

    /**
     * @covers ::__construct
     * @covers ::getHandlerForCommand
     * @expectedException InvalidArgumentException
     */
    public function testIfAnErrorOccursIfCommandClassDoesNotExist()
    {
        $containerMock = m::mock(ContainerInterface::class);
        $fixture = new ContainerLocator($containerMock);

        $fixture->getHandlerForCommand('UnknownCommandClass');
    }

    /**
     * @covers ::__construct
     * @covers ::getHandlerForCommand
     * @expectedException InvalidArgumentException
     */
    public function testIfAnErrorOccursIfCommandHandlerClassDoesNotExist()
    {
        $containerMock = m::mock(ContainerInterface::class);
        $fixture = new ContainerLocator($containerMock, [DummyCommand::class => 'UnknownHandlerClass']);

        $fixture->getHandlerForCommand(DummyCommand::class);
    }
}
