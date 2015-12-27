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
use phpDocumentor\Reflection\File as SourceFile;
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
            ->never();

        $sourceFile = m::mock(SourceFile::class);
        $sourceFile->shouldReceive('path')->andReturn('myFile.php');
        $stategies = m::mock(StrategyContainer::class);
        $command = new CreateCommand($sourceFile, $stategies);
        $fixture = new CacheMiddleware($poolMock);

        $result = $fixture->execute($command, function () use ($file) {
            return $file;
        });

        $this->assertSame($file, $result);
    }

    public function testChecksHash()
    {
        $cachedFile = new File('OldHash', 'myFile.php');
        $freshFile = new File('NewHash', 'myFile.php');
        $poolMock = m::mock(Pool::class);
        $poolMock->shouldReceive('getItem')
            ->andReturnSelf();

        $poolMock->shouldReceive('getItem->isMiss')
            ->once()
            ->andReturn(false);

        $poolMock->shouldReceive('getItem->lock')
            ->once();

        $poolMock->shouldReceive('getItem->set')
            ->once()
            ->with($freshFile);


        $poolMock->shouldReceive('getItem->get')
            ->once()
            ->andReturn($cachedFile);

        $sourceFile = m::mock(SourceFile::class);
        $sourceFile->shouldReceive('path')->andReturn('myFile.php');
        $sourceFile->shouldReceive('md5')
            ->andReturn('NewHash');
        $stategies = m::mock(StrategyContainer::class);
        $command = new CreateCommand($sourceFile, $stategies);
        $fixture = new CacheMiddleware($poolMock);

        $result = $fixture->execute($command, function () use ($freshFile) {
            return $freshFile;
        });

        $this->assertSame($freshFile, $result);
    }
}
