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

use Mockery as m;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\File as SourceFile;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\StrategyContainer;
use Stash\Item;
use Stash\Pool;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\CacheMiddleware
 * @covers ::<private>
 * @covers ::__construct
 */
final class CacheMiddlewareTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers ::execute
     * @uses \phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses \phpDocumentor\Reflection\Php\File
     */
    public function testCachedFileIsReturnedWhenValid() : void
    {
        $commandFile = new SourceFile\LocalFile(__FILE__);
        $file = new File($commandFile->md5(), __FILE__);

        $poolMock = m::mock(Pool::class);
        $poolMock->shouldReceive('getItem')->andReturnSelf();
        $poolMock->shouldReceive('getItem->isMiss')->andReturn(false);
        $poolMock->shouldReceive('getItem->get')->andReturn($file);
        $poolMock->shouldReceive('getItem->lock')->never();
        $poolMock->shouldReceive('getItem->set')->never();
        $poolMock->shouldReceive('save')->never();

        $command = new CreateCommand($commandFile, new ProjectFactoryStrategies([]));
        $parserMock = m::mock(Parser::class);
        $parserMock->shouldReceive('isForced')->andReturn(false);

        $fixture = new CacheMiddleware($poolMock, $parserMock);

        $result = $fixture->execute($command, function () {
            $this->fail('Parsing should not be done, the cached item should be returned');
        });

        $this->assertSame($file, $result);
    }

    /**
     * @covers ::execute
     * @uses \phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses \phpDocumentor\Reflection\Php\File
     */
    public function testCachedFileIsUpdatedWhenForced() : void
    {
        $commandFile = new SourceFile\LocalFile(__FILE__);
        $file = new File($commandFile->md5(), __FILE__);
        $item = new Item();

        $poolMock = m::mock(Pool::class);
        $poolMock->shouldReceive('getItem')->andReturnSelf();
        $poolMock->shouldReceive('getItem->isMiss')->andReturn(false);
        $poolMock->shouldReceive('getItem->get')->andReturn($file);
        $poolMock->shouldReceive('getItem->lock')->once();
        $poolMock->shouldReceive('getItem->set')->andReturn($item)->with($file);
        $poolMock->shouldReceive('save')->with($item);

        $command = new CreateCommand($commandFile, new ProjectFactoryStrategies([]));
        $parserMock = m::mock(Parser::class);
        $parserMock->shouldReceive('isForced')->andReturn(true);

        $fixture = new CacheMiddleware($poolMock, $parserMock);

        $result = $fixture->execute($command, function () use ($file) {
            return $file;
        });

        $this->assertSame($file, $result);
    }

    /**
     * @covers ::execute
     * @uses \phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses \phpDocumentor\Reflection\Php\File
     */
    public function testCacheIsUpdatedOnAMiss() : void
    {
        $file = new File('hash', 'myFile.php');
        $item = new Item();
        $poolMock = m::mock(Pool::class);
        $poolMock->shouldReceive('getItem')->andReturnSelf();
        $poolMock->shouldReceive('getItem->isMiss')->andReturn(true);
        $poolMock->shouldReceive('getItem->lock')->once();
        $poolMock->shouldReceive('getItem->set')->andReturn($item)->with($file);
        $poolMock->shouldReceive('getItem->get')->never();
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
     * @uses \phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses \phpDocumentor\Reflection\Php\File
     */
    public function testCacheFileIfItIsNotInThePool() : void
    {
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
            ->andReturn(null);

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

    /**
     * @covers ::execute
     * @uses \phpDocumentor\Reflection\Php\Factory\File\CreateCommand
     * @uses \phpDocumentor\Reflection\Php\File
     */
    public function testCacheNewFileIfHashMismatches() : void
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
