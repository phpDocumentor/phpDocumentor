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

namespace phpDocumentor\Reflection\Php\Factory\File;

use Mockery as m;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\StrategyContainer;
use Stash\Pool;

class CacheMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testCacheIsUsed()
    {
        $file = new File('hash', 'myFile.php');
        $poolMock = m::mock(Pool::class);
        $poolMock->shouldReceive('getItem')
            ->andReturnSelf();

        $poolMock->shouldReceive('getItem->isMiss')
            ->once()
            ->andReturn(true);

        $poolMock->shouldReceive('getItem->lock')
            ->once();

        $poolMock->shouldReceive('getItem->set')
            ->once()
            ->with($file);

        $poolMock->shouldReceive('getItem->get')
            ->once()
            ->andReturn($file);

        $adapterMock = m::mock(Adapter::class);
        $stategies = m::mock(StrategyContainer::class);
        $command = new CreateCommand($adapterMock, 'myFile.php', $stategies);
        $fixture = new CacheMiddleware($poolMock);

        $result = $fixture->execute($command, function() use($file) { return $file; });

        $this->assertSame($file, $result);
    }
}
