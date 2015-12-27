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

namespace phpDocumentor\Reflection\Middleware;

use League\Event\Emitter;
use Mockery as m;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass phpDocumentor\Reflection\Middleware\LoggingMiddleware
 */
final class LoggingMiddlewareTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testExecute()
    {
        $emitterMock = m::mock(Emitter::class);
        $emitterMock->shouldReceive('emit');
        $command = new CreateCommand(new LocalFile(__FILE__), new ProjectFactoryStrategies([]));

        $fixture = new LoggingMiddleware($emitterMock);

        $fixture->execute(
            $command,
            function () {
            }
        );
    }
}
