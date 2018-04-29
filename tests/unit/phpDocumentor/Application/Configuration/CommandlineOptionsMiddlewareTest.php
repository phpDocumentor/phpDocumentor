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

namespace phpDocumentor\Application\Configuration;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\Application\Configuration\CommandlineOptionsMiddleware
 * @covers ::__construct
 * @covers ::<private>
 */
final class CommandlineOptionsMiddlewareTest extends MockeryTestCase
{
    /** @var CommandlineOptionsMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->middleware = new CommandlineOptionsMiddleware();
    }

    /**
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheDestinationFolderBasedOnTheTargetOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['output' => '/tmp']]];
        $this->middleware->provideOptions(['target' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertEquals(new Dsn($expected), $newConfiguration['phpdocumentor']['paths']['output']);
    }

    /**
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheCacheFolderBasedOnTheCacheFolderOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['cache' => '/tmp']]];
        $this->middleware->provideOptions(['cache-folder' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertEquals(new Path($expected), $newConfiguration['phpdocumentor']['paths']['cache']);
    }

    /**
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function testItShouldDisableTheCacheBasedOnTheForceOption()
    {
        $configuration = ['phpdocumentor' => ['use-cache' => true]];
        $this->middleware->provideOptions(['force' => true]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertFalse($newConfiguration['phpdocumentor']['use-cache']);
    }

    /**
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheTitleBasedOnTheTitleOption()
    {
        $expected = 'phpDocumentor3';
        $configuration = ['phpdocumentor' => ['title' => 'phpDocumentor2']];
        $this->middleware->provideOptions(['title' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertSame($expected, $newConfiguration['phpdocumentor']['title']);
    }

    /**
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheListOfTemplatesBasedOnTheTemplateOption()
    {
        $expected = 'clean';
        $configuration = ['phpdocumentor' => ['templates' => ['responsive']]];
        $this->middleware->provideOptions(['template' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertSame([$expected], $newConfiguration['phpdocumentor']['templates']);
    }
}
