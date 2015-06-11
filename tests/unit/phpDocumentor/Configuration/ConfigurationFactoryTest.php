<?php

namespace phpDocumentor\Configuration;

use phpDocumentor\Uri;

/**
 * Test case for ConfigurationFactory
 * @coversDefaultClass phpDocumentor\Configuration\ConfigurationFactory
 */
final class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testItRequiresAUri()
    {
        $uri                  = new Uri('file://foo');
        $configurationFactory = new ConfigurationFactory($uri);

        $this->assertClassHasAttribute('uri', ConfigurationFactory::class);
        $this->assertAttributeInstanceOf(Uri::class, 'uri', $configurationFactory);
    }
}
