<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\EmittingMiddleware
 * @covers ::<private>
 */
final class EmittingMiddlewareTest extends TestCase
{
    /**
     * @covers ::execute
     */
    public function testEmitsPreParsingEvent() : void
    {
        $filename = __FILE__;
        $command = new CreateCommand(new LocalFile($filename), new ProjectFactoryStrategies([]));

        Dispatcher::getInstance()->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($filename) {
                $this->assertSame($event->getFile(), $filename);
            }
        );

        $middleware = new EmittingMiddleware();
        $result = $middleware->execute(
            $command,
            function (CreateCommand $receivedCommand) use ($command) {
                $this->assertSame($command, $receivedCommand);

                return 'result';
            }
        );

        $this->assertSame('result', $result);
    }
}
