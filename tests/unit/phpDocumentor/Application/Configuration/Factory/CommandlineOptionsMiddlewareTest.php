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

namespace phpDocumentor\Application\Configuration\Factory;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\Application\Configuration\Factory\CommandlineOptionsMiddleware
 * @covers ::__construct
 * @covers ::<private>
 */
final class CommandlineOptionsMiddlewareTest extends MockeryTestCase
{
    /** @var CommandlineOptionsMiddleware */
    private $middleware;

    public function setUp()
    {
        $this->middleware = new CommandlineOptionsMiddleware();
    }

    /**
     * @test
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function itShouldOverwriteTheDestinationFolderBasedOnTheTargetOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['output' => '/tmp']]];
        $this->middleware->provideOptions(['target' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertEquals(new Dsn($expected), $newConfiguration['phpdocumentor']['paths']['output']);
    }

    /**
     * @test
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function itShouldOverwriteTheCacheFolderBasedOnTheCacheFolderOption()
    {
        $expected = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['cache' => '/tmp']]];
        $this->middleware->provideOptions(['cache-folder' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertEquals(new Path($expected), $newConfiguration['phpdocumentor']['paths']['cache']);
    }

    /**
     * @test
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function itShouldDisableTheCacheBasedOnTheForceOption()
    {
        $configuration = ['phpdocumentor' => ['use-cache' => true]];
        $this->middleware->provideOptions(['force' => true]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertFalse($newConfiguration['phpdocumentor']['use-cache']);
    }

    /**
     * @test
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function itShouldOverrideTheTitleBasedOnTheTitleOption()
    {
        $expected = 'phpDocumentor3';
        $configuration = ['phpdocumentor' => ['title' => 'phpDocumentor2']];
        $this->middleware->provideOptions(['title' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertSame($expected, $newConfiguration['phpdocumentor']['title']);
    }

    /**
     * @test
     * @covers ::provideOptions
     * @covers ::__invoke
     */
    public function itShouldOverrideTheListOfTemplatesBasedOnTheTemplateOption()
    {
        $expected = 'clean';
        $configuration = ['phpdocumentor' => ['templates' => ['responsive']]];
        $this->middleware->provideOptions(['template' => $expected]);
        $newConfiguration = $this->middleware->__invoke($configuration);

        $this->assertSame([$expected], $newConfiguration['phpdocumentor']['templates']);
    }
}
