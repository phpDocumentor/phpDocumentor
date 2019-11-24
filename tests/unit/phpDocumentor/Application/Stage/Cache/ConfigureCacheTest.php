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

namespace phpDocumentor\Application\Stage\Cache;

use \Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Application\Stage\Payload;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Path;
use Stash\Pool;

class ConfigureCacheTest extends MockeryTestCase
{
    /**
     * @dataProvider cacheDirProvider
     * @throws \Exception
     */
    public function testInvokeWithCachePath($configuredPath) : void
    {
        $configuration = [
            'phpdocumentor' => [
                'paths' => [
                    'cache' => $configuredPath,
                ],
            ],
        ];

        $cacheStorage = m::mock(Pool::class);
        $cacheStorage->shouldReceive('setDriver')
            ->with(m::type(\Stash\Driver\FileSystem::class))
            ->once();

        $stage = new ConfigureCache($cacheStorage);

        $payload = new Payload($configuration, m::mock(ProjectDescriptorBuilder::class));

        self::assertSame(
            $payload,
            $stage($payload)
        );
    }

    public function cacheDirProvider() : array
    {
        return [
            [
                '/tmp/cache',
            ],
            [
                'cache/relative',
            ],
            [
                new Path('/tmp/cache'),
            ],
            [
                new Path('cache/relative'),
            ],
        ];
    }
}
