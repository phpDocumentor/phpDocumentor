<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File as ReflectedFile;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use function base64_encode;
use function serialize;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\CacheMiddleware
 * @covers ::__construct
 * @covers ::<private>
 */
final class CacheMiddlewareTest extends TestCase
{
    /**
     * @covers ::execute
     */
    public function testReturnsCachedResponseIfFileContentsIsTheSame() : void
    {
        $file = $this->givenFileWithContent('file.php', 'cached content');
        $reflectedFile = new ReflectedFile('abc', $file->url());

        $cacheInterface = new ArrayAdapter();
        $this->whenFileIsAlreadyInCache($cacheInterface, $reflectedFile);

        $middleware = new CacheMiddleware($cacheInterface, new NullLogger());

        $response = $middleware->execute(
            new CreateCommand(new File\LocalFile($file->url()), new ProjectFactoryStrategies([])),
            function () : void {
                $this->fail('If we entered the next state; then caching failed');
            }
        );

        $this->assertEquals($reflectedFile, $response);
    }

    /**
     * @covers ::execute
     */
    public function testCachesResponseWhenReturningAnUncachedFile() : void
    {
        $file = $this->givenFileWithContent('file.php', 'cached content');
        $reflectedFile = new ReflectedFile('abc', $file->url());

        $cacheInterface = new ArrayAdapter();

        $middleware = new CacheMiddleware($cacheInterface, new NullLogger());

        $response = $middleware->execute(
            new CreateCommand(new File\LocalFile($file->url()), new ProjectFactoryStrategies([])),
            static function () use ($reflectedFile) {
                return $reflectedFile;
            }
        );

        $this->assertTrue(
            $cacheInterface->hasItem('0d3c97a4f869de131219802426e09961-c1af748a4386d756f9b87703cf3b33c8')
        );
        $this->assertEquals($reflectedFile, $response);
    }

    private function givenFileWithContent(string $name, string $content) : vfsStreamFile
    {
        $file = new vfsStreamFile($name);
        $file->setContent($content);
        $directory = vfsStream::setup();
        $directory->addChild($file);

        return $file;
    }

    private function whenFileIsAlreadyInCache(
        ArrayAdapter $cacheInterface,
        ReflectedFile $reflectedFile
    ) : void {
        $cacheInterface->get(
            '0d3c97a4f869de131219802426e09961-c1af748a4386d756f9b87703cf3b33c8',
            static function () use ($reflectedFile) {
                return base64_encode(serialize($reflectedFile));
            }
        );
    }
}
