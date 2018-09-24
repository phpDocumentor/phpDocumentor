<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\CommandlineOptionsMiddleware
 * @covers ::__construct
 * @covers ::<private>
 */
final class CommandlineOptionsMiddlewareTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheDestinationFolderBasedOnTheTargetOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['output' => '/tmp']]];

        $middleware = new CommandlineOptionsMiddleware(['target' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(new Dsn($expected), $newConfiguration['phpdocumentor']['paths']['output']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldDisableTheCacheBasedOnTheForceOption()
    {
        $configuration = ['phpdocumentor' => ['use-cache' => true]];

        $middleware = new CommandlineOptionsMiddleware(['force' => true]);
        $newConfiguration = $middleware($configuration);

        $this->assertFalse($newConfiguration['phpdocumentor']['use-cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheCacheFolderBasedOnTheCacheFolderOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['cache' => '/tmp']]];

        $middleware = new CommandlineOptionsMiddleware(['cache-folder' => $expected]);
        $newConfiguration = $middleware->__invoke($configuration);

        $this->assertEquals(new Path($expected), $newConfiguration['phpdocumentor']['paths']['cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheTitleBasedOnTheTitleOption()
    {
        $expected = 'phpDocumentor3';
        $configuration = ['phpdocumentor' => ['title' => 'phpDocumentor2']];

        $middleware = new CommandlineOptionsMiddleware(['title' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertSame($expected, $newConfiguration['phpdocumentor']['title']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheListOfTemplatesBasedOnTheTemplateOption()
    {
        $expected = 'clean';
        $configuration = ['phpdocumentor' => ['templates' => ['responsive']]];

        $middleware = new CommandlineOptionsMiddleware(['template' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertSame([$expected], $newConfiguration['phpdocumentor']['templates']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddSourceDirectoriesForDefaultConfiguration()
    {
        $configuration = Version3::buildDefault();

        $middleware = new CommandlineOptionsMiddleware(['directory' => ['./src']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'dsn' => new Dsn('file://.'),
                'paths' => [new Path('./src')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['source']
        );
    }
}
