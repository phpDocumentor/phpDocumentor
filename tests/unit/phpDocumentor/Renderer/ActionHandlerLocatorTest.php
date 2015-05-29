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

namespace phpDocumentor\Renderer;

use Mockery as m;
use DI\Container;

/**
 * Tests the functionality for the ActionHandlerLocator class.
 */
class ActionHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIfLocatorRetrievesAnActionWithHanderAppendedToClassName()
    {
        $action        = m::mock(Action::class);
        $containerMock = m::mock(Container::class);
        $handlerMock   = new \stdClass();

        $containerMock->shouldReceive('get')->with(get_class($action) . 'Handler')->andReturn($handlerMock);

        $fixture = new ActionHandlerLocator($containerMock);
        $result  = $fixture->locate($action);

        $this->assertSame($handlerMock, $result);
    }
}
