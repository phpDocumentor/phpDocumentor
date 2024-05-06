<?php

declare(strict_types=1);

namespace integration\phpDocumentor\Configuration;

use phpDocumentor\Configuration\ConfigurationFactory;
use phpDocumentor\Configuration\Definition\Version2;
use phpDocumentor\Configuration\Definition\Version3;
use phpDocumentor\Configuration\PathNormalizingMiddleware;
use phpDocumentor\Configuration\SymfonyConfigFactory;
use phpDocumentor\UriFactory;
use PHPUnit\Framework\TestCase;

class ConfigurationLoadingTest extends TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testConfigLoaded(string $configFile)
    {
        $factory = new ConfigurationFactory(
            [],
            new SymfonyConfigFactory([
                '2' => new Version2('default'),
                '3' => new Version3('default')
            ])
        );

        $factory->addMiddleware(new PathNormalizingMiddleware());

        //Don't care about the result of this factory, we just want to see if it doesn't error.
        self::assertNotEmpty($factory->fromUri(UriFactory::createUri($configFile)));

        if (str_starts_with($configFile, 'phpDocumentor3')) {
            $xml = new \DOMDocument();
            $xml->load($configFile);
            self::assertTrue($xml->schemaValidate(__DIR__ . '/../../../../data/xsd/phpdoc.xsd'));
        }
    }

    public static function configProvider(): iterable
    {
        $directoryIterator = new \DirectoryIterator(__DIR__ . '/data');

        foreach ($directoryIterator as $file) {
            if ($file->isFile()) {
                yield $file->getFilename() => [
                    $file->getPathname()
                ];
            }
        }
    }
}
