<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use function current;

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
    public function testItShouldOverwriteTheDestinationFolderBasedOnTheTargetOption() : void
    {
        $expected      = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['output' => '/tmp']]];

        $middleware       = new CommandlineOptionsMiddleware(['target' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(new Dsn($expected), $newConfiguration['phpdocumentor']['paths']['output']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldDisableTheCacheBasedOnTheForceOption() : void
    {
        $configuration = ['phpdocumentor' => ['use-cache' => true]];

        $middleware       = new CommandlineOptionsMiddleware(['force' => true]);
        $newConfiguration = $middleware($configuration);

        $this->assertFalse($newConfiguration['phpdocumentor']['use-cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheCacheFolderBasedOnTheCacheFolderOption() : void
    {
        $expected      = '/abc';
        $configuration = ['phpdocumentor' => ['paths' => ['cache' => '/tmp']]];

        $middleware       = new CommandlineOptionsMiddleware(['cache-folder' => $expected]);
        $newConfiguration = $middleware->__invoke($configuration);

        $this->assertEquals(new Path($expected), $newConfiguration['phpdocumentor']['paths']['cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheTitleBasedOnTheTitleOption() : void
    {
        $expected      = 'phpDocumentor3';
        $configuration = ['phpdocumentor' => ['title' => 'phpDocumentor2']];

        $middleware       = new CommandlineOptionsMiddleware(['title' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertSame($expected, $newConfiguration['phpdocumentor']['title']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheListOfTemplatesBasedOnTheTemplateOption() : void
    {
        $expected      = 'clean';
        $configuration = ['phpdocumentor' => ['templates' => [['name' => 'responsive']]]];

        $middleware       = new CommandlineOptionsMiddleware(['template' => $expected]);
        $newConfiguration = $middleware($configuration);

        $this->assertSame([['name' => $expected]], $newConfiguration['phpdocumentor']['templates']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddSourceFilesForDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware       = new CommandlineOptionsMiddleware(['filename' => ['./src/index.php']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'dsn' => new Dsn('file://.'),
                'paths' => [new Path('./src/index.php')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['source']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddSourceDirectoriesForDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware       = new CommandlineOptionsMiddleware(['directory' => ['./src']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'dsn' => new Dsn('file://.'),
                'paths' => [new Path('./src')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['source']
        );
    }

    public function testItShouldAddAbsoluteSourcePathsToNewApi() : void
    {
        $configuration    = Version3::buildDefault();
        $middleware       = new CommandlineOptionsMiddleware(['directory' => ['/src']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'dsn' => new Dsn('file:///src'),
                'paths' => [new Path('./')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['source']
        );
    }

    public function testItShouldAddAbsoluteSourcePathsToNewApiAndRelativeToCurrent() : void
    {
        $configuration    = Version3::buildDefault();
        $middleware       = new CommandlineOptionsMiddleware(['directory' => ['/src', './localSrc']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'dsn' => new Dsn('file:///src'),
                'paths' => [new Path('./')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['source']
        );
        $this->assertEquals(
            [
                'dsn' => new Dsn('file://.'),
                'paths' => [new Path('./localSrc')],
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][1]['source']
        );
    }


    /**
     * @covers ::__invoke
     */
    public function testItShouldRegisterExtensionsForDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $extensions       = ['php7', 'php5'];
        $middleware       = new CommandlineOptionsMiddleware(['extensions' => $extensions]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $extensions,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['extensions']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldReplaceIgnoredDirectoriesForDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware       = new CommandlineOptionsMiddleware(['ignore' => ['./src']]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'paths' => [new Path('./src')],
                'hidden' => true,
            ],
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['ignore']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheMarkersOfTheDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $markers          = ['FIXME2', 'TODOSOMETIME'];
        $middleware       = new CommandlineOptionsMiddleware(['markers' => $markers]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $markers,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['markers']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheVisibilitySetInTheDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $visibility       = ['public'];
        $middleware       = new CommandlineOptionsMiddleware(['visibility' => $visibility]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $visibility,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['visibility']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheDefaultPackageNameSetInTheDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $defaultPackageName = ['public'];
        $middleware         = new CommandlineOptionsMiddleware(['defaultpackagename' => $defaultPackageName]);
        $newConfiguration   = $middleware($configuration);

        $this->assertEquals(
            $defaultPackageName,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['default-package-name']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheWhetherToIncludeSourcecodeInTheDefaultConfiguration() : void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware       = new CommandlineOptionsMiddleware(['sourcecode' => true]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            true,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['include-source']
        );

        $middleware       = new CommandlineOptionsMiddleware(['sourcecode' => false]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            false,
            current($newConfiguration['phpdocumentor']['versions'])['api'][0]['include-source']
        );
    }

    private function givenAConfigurationWithoutApiDefinition() : array
    {
        $configuration = Version3::buildDefault();

        // wipe version so that middleware needs to re-add the api key
        unset($configuration['phpdocumentor']['versions']);
        $configuration['phpdocumentor']['versions'] = ['1.0.0' => []];

        return $configuration;
    }
}
