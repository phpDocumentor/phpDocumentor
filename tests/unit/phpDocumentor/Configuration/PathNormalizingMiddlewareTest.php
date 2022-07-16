<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use League\Uri\Uri;
use phpDocumentor\Path;
use PHPUnit\Framework\TestCase;

use function dirname;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\PathNormalizingMiddleware
 * @covers ::__invoke
 * @covers ::<private>
 */
final class PathNormalizingMiddlewareTest extends TestCase
{
    /** @var ConfigurationFactory */
    private $configurationFactory;

    protected function setUp(): void
    {
        $definition = new Definition\Version3('default');
        $this->configurationFactory = new ConfigurationFactory([], new SymfonyConfigFactory(['3' => $definition]));
    }

    public function testNoConfigUriLeavesConfigUnchanged(): void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = new PathNormalizingMiddleware();
        $outputConfig = $middleware($configuration, null);

        self::assertEquals($configuration, $outputConfig);
    }

    /**
     * @dataProvider pathProvider
     */
    public function testNormalizedIgnoreToGlob(string $input, string $output): void
    {
        $configuration = $this->givenAConfiguration();
        $configuration['phpdocumentor']['versions']['1.0.0']->api[0]->setIgnore(['paths' => [$input]]);

        $middleware = new PathNormalizingMiddleware();
        $outputConfig = $middleware($configuration, Uri::createFromString('./config.xml'));

        self::assertEquals(
            [$output],
            $outputConfig['phpdocumentor']['versions']['1.0.0']->getApi()[0]['ignore']['paths']
        );
    }

    /**
     * @dataProvider cachePathProvider
     */
    public function testNormalizeCachePath(string $input, string $output, string $configPath): void
    {
        $configuration = $this->givenAConfiguration();
        $configuration['phpdocumentor']['paths']['cache'] = new Path($input);

        $middleware = new PathNormalizingMiddleware();
        $outputConfig = $middleware(
            $configuration,
            Uri::createFromString($configPath)
        );

        self::assertSame($output, (string) $outputConfig['phpdocumentor']['paths']['cache']);
    }

    /**
     * @dataProvider templateLocationProvider
     */
    public function testNormalizeTemplateLocations(?string $input, ?string $output, string $configPath): void
    {
        $configuration = $this->givenAConfiguration();
        $configuration['phpdocumentor']['templates'][0]['location'] = $input ? new Path($input) : null;

        $middleware = new PathNormalizingMiddleware();
        $outputConfig = $middleware(
            $configuration,
            Uri::createFromString($configPath)
        );

        $resultingPath = $outputConfig['phpdocumentor']['templates'][0]['location'];
        if ($resultingPath instanceof Path) {
            $resultingPath = (string) $resultingPath;
        }

        self::assertSame($output, $resultingPath);
    }

    public function testDsnResolvedByConfigPath(): void
    {
        $configuration = $this->givenAConfiguration();

        $middleware = new PathNormalizingMiddleware();

        $outputConfig = $middleware(
            $configuration,
            Uri::createFromString('/data/phpDocumentor/config.xml')
        );

        self::assertSame(
            '/data/phpDocumentor/',
            (string) $outputConfig['phpdocumentor']['versions']['1.0.0']->api[0]['source']['dsn']
        );
    }

    public function templateLocationProvider(): array
    {
        $configLocation = '/data/phpdocumentor/config.xml';

        return [
            'Omitted locations are not normalized' => [null, null, $configLocation],
            'Relative paths are made absolute, relative from config' => [
                'data/templates',
                dirname($configLocation) . '/data/templates',
                $configLocation,
            ],
            'Absolute paths are kept' => [
                '/home/user/myproject/data/templates',
                '/home/user/myproject/data/templates',
                $configLocation,
            ],
        ];
    }

    public function cachePathProvider(): array
    {
        return [
            'Absolute paths are not normalized' => [
                '/opt/myProject',
                '/opt/myProject',
                '/data/phpdocumentor/config.xml',
            ],
            'Absolute windows paths are normalized' => [
                'D:\opt\myProject',
                'D:/opt/myProject',
                '/data/phpdocumentor/config.xml',
            ],
            'Absolute paths could contain special characters' => [
                '/opt/#myProject/with a space',
                '/opt/%23myProject/with%20a%20space',
                '/data/phpdocumentor/config.xml',
            ],
            'Absolute windows paths could contain hashes' => [
                'D:\opt\#myProject',
                'D:/opt/%23myProject',
                '/data/phpdocumentor/config.xml',
            ],
            'Relative unix paths are changed to an absolute path with the config folder as prefix' => [
                '.phpdoc/cache',
                '/data/phpdocumentor/.phpdoc/cache',
                '/data/phpdocumentor/config.xml',
            ],
            'Relative paths may contain spaces' => [
                '.phpdoc/my cache',
                '/data/phpdocumentor/.phpdoc/my%20cache',
                '/data/phpdocumentor/config.xml',
            ],
            'Relative paths may contain hashes' => [
                '.phpdoc/#cache',
                '/data/phpdocumentor/.phpdoc/%23cache',
                '/data/phpdocumentor/config.xml',
            ],
            'Relative paths on Windows are changed to an absolute path with the config folder as prefix' => [
                '.phpdoc\cache',
                'd:/data/phpdocumentor/.phpdoc/cache',
                'D:\data\phpdocumentor\config.xml',
            ],
        ];
    }

    public function pathProvider(): array
    {
        return [
            [
                'src',
                '/src/**/*',
            ],
            [
                '.',
                '/**/*',
            ],
            [
                './src',
                '/src/**/*',
            ],
            [
                '/src/*',
                '/src/*',
            ],
            [
                'src/dir/test.php',
                '/src/dir/test.php',
            ],
        ];
    }

    private function givenAConfiguration(): Configuration
    {
        return $this->configurationFactory->createDefault();
    }
}
