<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use League\Uri\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\PathNormalizingMiddelware
 * @covers ::__invoke
 * @covers ::<private>
 */
final class PathNormalizingMiddelwareTest extends TestCase
{
    /** @var ConfigurationFactory */
    private $configurationFactory;

    protected function setUp() : void
    {
        $definition = new Definition\Version3('clean');
        $this->configurationFactory = new ConfigurationFactory([], new SymfonyConfigFactory(['3' => $definition]));
    }

    public function testNoConfigUriLeavesConfigUnchanged() : void
    {
        $configuration = $this->givenAConfiguration();
        $middleware = new PathNormalizingMiddelware();
        $outputConfig = $middleware($configuration, null);

        self::assertEquals($configuration, $outputConfig);
    }

    /**
     * @dataProvider pathProvider
     */
    public function testNormalizedPathsToGlob(string $input, string $output) : void
    {
        $configuration = $this->givenAConfiguration();
        $configuration['phpdocumentor']['versions']['1.0.0']['api'][0]['source']['paths'] = [$input];

        $middleware = new PathNormalizingMiddelware();
        $outputConfig = $middleware($configuration, Uri::createFromString('./config.xml'));

        self::assertEquals(
            [$output],
            $outputConfig['phpdocumentor']['versions']['1.0.0']['api'][0]['source']['paths']
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testNormalizedIgnoreToGlob(string $input, string $output) : void
    {
        $configuration = $this->givenAConfiguration();
        $configuration['phpdocumentor']['versions']['1.0.0']['api'][0]['ignore']['paths'] = [$input];

        $middleware = new PathNormalizingMiddelware();
        $outputConfig = $middleware($configuration, Uri::createFromString('./config.xml'));

        self::assertEquals(
            [$output],
            $outputConfig['phpdocumentor']['versions']['1.0.0']['api'][0]['ignore']['paths']
        );
    }

    public function pathProvider() : array
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

    private function givenAConfiguration() : array
    {
        return $this->configurationFactory->createDefault()->getArrayCopy();
    }
}
