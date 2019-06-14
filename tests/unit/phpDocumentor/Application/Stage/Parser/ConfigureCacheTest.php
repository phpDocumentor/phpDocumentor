<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage\Parser;

use \Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Path;
use Zend\Cache\Storage\StorageInterface;

class ConfigureCacheTest extends MockeryTestCase
{
    /**
     * @dataProvider cacheDirProvider
     * @throws \Exception
     */
    public function testInvokeWithCachePath($configuredPath, $expectedPath)
    {
        $configuration = [
            'phpdocumentor' => [
                'paths' => [
                    'cache' => $configuredPath,
                ],
            ],
        ];

        $cacheStorage = m::mock(StorageInterface::class);
        $cacheStorage->shouldReceive('getOptions->setCacheDir')->withArgs([$expectedPath])->once();

        $stage = new ConfigureCache($cacheStorage);

        self::assertSame($configuration, $stage($configuration));
    }

    public function cacheDirProvider()
    {
        return [
            [
                '/tmp/cache',
                '/tmp/cache',
            ],
            [
                'cache/relative',
                getcwd() . DIRECTORY_SEPARATOR . 'cache/relative',
            ],
            [
                new Path('/tmp/cache'),
                '/tmp/cache',
            ],
            [
                new Path('cache/relative'),
                getcwd() . DIRECTORY_SEPARATOR . 'cache/relative',
            ],
        ];
    }
}
