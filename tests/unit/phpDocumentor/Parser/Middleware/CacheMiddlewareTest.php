<?php
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

use Mockery as m;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\File as SourceFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\StrategyContainer;
use Stash\Item;
use Stash\Pool;

/**
 * @coversDefaultClass phpDocumentor\parser\Middleware\CacheMiddleware
 * @covers ::<private>
 * @covers ::__construct
 */
final class CacheMiddlewareTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers ::execute
     * @uses phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses phpDocumentor\Reflection\Php\File
     */
    public function testCacheIsUsed()
    {
        $file = new File('hash', 'myFile.php');
        $item = new Item();
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
            ->andReturn($item)
            ->with($file);

        $poolMock->shouldReceive('getItem->get')
            ->never();

        $poolMock->shouldReceive('save')->with($item);

        $sourceFile = m::mock(SourceFile::class);
        $sourceFile->shouldReceive('path')->andReturn('myFile.php');
        $stategies = m::mock(StrategyContainer::class);
        $command = new CreateCommand($sourceFile, $stategies);
        $fixture = new CacheMiddleware($poolMock, m::mock(Parser::class));

        $result = $fixture->execute($command, function () use ($file) {
            return $file;
        });

        $this->assertSame($file, $result);
    }

    /**
     * @covers ::execute
     * @uses phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses phpDocumentor\Reflection\Php\File
     */
    public function testChecksHash()
    {
        $cachedFile = new File('OldHash', 'myFile.php');
        $freshFile = new File('NewHash', 'myFile.php');
        $item = new Item();
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
            ->with($freshFile)
            ->andReturn($item);

        $poolMock->shouldReceive('getItem->get')
            ->once()
            ->andReturn($cachedFile);

        $poolMock->shouldReceive('save')->with($item);

        $sourceFile = m::mock(SourceFile::class);
        $sourceFile->shouldReceive('path')->andReturn('myFile.php');
        $sourceFile->shouldReceive('md5')
            ->andReturn('NewHash');
        $stategies = m::mock(StrategyContainer::class);
        $parser = m::mock(Parser::class);
        $parser->shouldReceive('isForced')->andReturn(false);

        $command = new CreateCommand($sourceFile, $stategies);
        $fixture = new CacheMiddleware($poolMock, $parser);

        $result = $fixture->execute($command, function () use ($freshFile) {
            return $freshFile;
        });

        $this->assertSame($freshFile, $result);
    }
}
