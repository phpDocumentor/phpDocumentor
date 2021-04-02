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

namespace phpDocumentor\Configuration;

use phpDocumentor\Dsn;
use phpDocumentor\Path;
use PHPUnit\Framework\TestCase;

use function current;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\CommandlineOptionsMiddleware
 * @covers ::__construct
 * @covers ::<private>
 */
final class CommandlineOptionsMiddlewareTest extends TestCase
{
    /** @var ConfigurationFactory */
    private $configurationFactory;

    protected function setUp(): void
    {
        $definition = new Definition\Version3('default');
        $this->configurationFactory = new ConfigurationFactory([], new SymfonyConfigFactory(['3' => $definition]));
    }

    /**
     * @dataProvider targetPathProvider
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheDestinationFolderBasedOnTheTargetOption(
        string $argument,
        string $workingDir,
        string $expected
    ): void {
        $configuration = new Configuration(['phpdocumentor' => ['paths' => ['output' => '/tmp']]]);

        $middleware = $this->createCommandlineOptionsMiddleware(['target' => $argument], $workingDir);
        $newConfiguration = $middleware($configuration);

        self::assertEquals(Dsn::createFromString($expected), $newConfiguration['phpdocumentor']['paths']['output']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldDisableTheCacheBasedOnTheForceOption(): void
    {
        $configuration = new Configuration(['phpdocumentor' => ['use-cache' => true]]);
        $middleware = $this->createCommandlineOptionsMiddleware(['force' => true]);

        $newConfiguration = $middleware($configuration);

        self::assertFalse($newConfiguration['phpdocumentor']['use-cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheCacheFolderBasedOnTheCacheFolderOption(): void
    {
        $expected = '/abc';
        $configuration = new Configuration(['phpdocumentor' => ['paths' => ['cache' => '/tmp']]]);
        $middleware = $this->createCommandlineOptionsMiddleware(['cache-folder' => $expected]);
        $newConfiguration = $middleware->__invoke($configuration);

        self::assertEquals(new Path($expected), $newConfiguration['phpdocumentor']['paths']['cache']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheTitleBasedOnTheTitleOption(): void
    {
        $expected = 'phpDocumentor3';
        $configuration = new Configuration(['phpdocumentor' => ['title' => 'phpDocumentor2']]);
        $middleware = $this->createCommandlineOptionsMiddleware(['title' => $expected]);

        $newConfiguration = $middleware($configuration);

        self::assertSame($expected, $newConfiguration['phpdocumentor']['title']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverrideTheListOfTemplatesBasedOnTheTemplateOption(): void
    {
        $expected = 'default';
        $configuration = new Configuration(['phpdocumentor' => ['templates' => [['name' => 'responsive']]]]);
        $middleware = $this->createCommandlineOptionsMiddleware(['template' => $expected]);

        $newConfiguration = $middleware($configuration);

        self::assertSame([['name' => $expected]], $newConfiguration['phpdocumentor']['templates']);
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddSourceFilesForDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware = $this->createCommandlineOptionsMiddleware(['filename' => ['./src/index.php']]);
        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('.'),
                [new Path('./src/index.php')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]->source()
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddSourceDirectoriesForDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $middleware = $this->createCommandlineOptionsMiddleware(['directory' => ['./src']]);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('.'),
                [new Path('./src')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]->source()
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldKeepSourceDirectoriesWhenNoneWereProvideOnCommandLine(): void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = $this->createCommandlineOptionsMiddleware(['directory' => []]);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('.'),
                [new Path('/**/*')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]->source()
        );
    }

    public function testItShouldAddAbsoluteSourcePathsToNewApi(): void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = $this->createCommandlineOptionsMiddleware(['directory' => ['/src']]);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('/src'),
                [new Path('./')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]->source()
        );
    }

    public function testItShouldAddAbsoluteSourcePathsToNewApiAndRelativeToCurrent(): void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = $this->createCommandlineOptionsMiddleware(['directory' => ['/src', './localSrc']]);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('/src'),
                [new Path('./')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]->source()
        );

        self::assertEquals(
            new Source(
                Dsn::createFromString('.'),
                [new Path('./localSrc')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[1]->source()
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldRegisterExtensionsForDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $extensions = ['php7', 'php5'];
        $middleware = $this->createCommandlineOptionsMiddleware(['extensions' => $extensions]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $extensions,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['extensions']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldReplaceIgnoredDirectoriesForDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $middleware = $this->createCommandlineOptionsMiddleware(['ignore' => ['./src']]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            [
                'paths' => [new Path('./src')],
                'hidden' => true,
                'symlinks' => true,
            ],
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['ignore']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheMarkersOfTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $markers = ['FIXME2', 'TODOSOMETIME'];
        $middleware = $this->createCommandlineOptionsMiddleware(['markers' => $markers]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $markers,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['markers']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheVisibilitySetInTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $visibility = ['public'];
        $middleware = $this->createCommandlineOptionsMiddleware(['visibility' => $visibility]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $visibility,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['visibility']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheEncodingSetInTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $encoding = 'iso-8859-1';
        $middleware = $this->createCommandlineOptionsMiddleware(['encoding' => $encoding]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $encoding,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['encoding']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheDefaultPackageNameSetInTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $defaultPackageName = ['public'];
        $middleware = $this->createCommandlineOptionsMiddleware(['defaultpackagename' => $defaultPackageName]);

        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            $defaultPackageName,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['default-package-name']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheWhetherToIncludeSourcecodeInTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();

        $middleware = $this->createCommandlineOptionsMiddleware(['sourcecode' => true]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            true,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['include-source']
        );

        $middleware = $this->createCommandlineOptionsMiddleware(['sourcecode' => false]);
        $newConfiguration = $middleware($configuration);

        $this->assertEquals(
            false,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['include-source']
        );
    }

    private function givenAConfigurationWithoutApiDefinition(): Configuration
    {
        $configuration = $this->givenAConfiguration();

        // wipe version so that middleware needs to re-add the api key
        unset($configuration['phpdocumentor']['versions']);
        $configuration['phpdocumentor']['versions'] = ['1.0.0' => new VersionSpecification('1.0.0', '1.0.0', [], null)];

        return $configuration;
    }

    private function givenAConfiguration(): Configuration
    {
        return $this->configurationFactory->createDefault();
    }

    public function targetPathProvider(): array
    {
        return [
            'absolute path' => [
                '/abc',
                '/opt/myProject',
                '/abc',
            ],
            'relative path in current dir' => [
                './abc',
                '/opt/myProject',
                '/opt/myProject/abc',
            ],
            'relative path directory up' => [
                '../abc',
                '/opt/myProject',
                '/opt/abc',
            ],
        ];
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldAddExampleDirToConfig(): void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = $this->createCommandlineOptionsMiddleware(['examples-dir' => '/src']);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('/src'),
                [new Path('./')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['examples']
        );
    }

    public function testItShouldAddExampleDirToConfigA(): void
    {
        $configuration = $this->givenAConfigurationWithoutApiDefinition();
        $middleware = $this->createCommandlineOptionsMiddleware(['examples-dir' => '/src']);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            new Source(
                Dsn::createFromString('/src'),
                [new Path('./')]
            ),
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['examples']
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItShouldOverwriteTheIgnoredTagsOfTheDefaultConfiguration(): void
    {
        $configuration = $this->givenAConfiguration();
        $tags = ['return', 'param'];
        $middleware = $this->createCommandlineOptionsMiddleware(['ignore-tags' => $tags]);

        $newConfiguration = $middleware($configuration);

        self::assertEquals(
            $tags,
            current($newConfiguration['phpdocumentor']['versions'])->getApi()[0]['ignore-tags']
        );
    }

    /**
     * @param array $options
     */
    private function createCommandlineOptionsMiddleware(
        array $options,
        $workingDir = '/'
    ): CommandlineOptionsMiddleware {
        return new CommandlineOptionsMiddleware(
            $options,
            $this->configurationFactory,
            $workingDir
        );
    }
}
