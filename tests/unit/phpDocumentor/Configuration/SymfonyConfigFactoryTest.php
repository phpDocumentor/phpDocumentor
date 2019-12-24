<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use org\bovigo\vfs\vfsStream;
use phpDocumentor\Configuration\Definition\Upgradable;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use RuntimeException;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @coversDefaultClass
 */
final class SymfonyConfigFactoryTest extends TestCase
{
    use Faker;

    public function testGetDefaultConfig() : void
    {
        $fixture = $this->createConfigFactoryWithTestDefinition();
        $this->assertArrayHasKey(SymfonyConfigFactory::FIELD_CONFIG_VERSION, $fixture->createDefault());
    }

    public function testThrowsExeceptionWhenUpgradeFails() : void
    {
        $this->expectException(RuntimeException::class);

        $configMock = $this->prophesize(ConfigurationInterface::class);
        $configMock->willImplement(Upgradable::class);
        $configMock->getConfigTreeBuilder()->willReturn($this->faker()->configTreeBuilder('test'));
        $configMock->upgrade(Argument::any())->willReturn([]);

        $this->fixture = new SymfonyConfigFactory(['test' => $configMock->reveal()]);
        $this->assertArrayHasKey(SymfonyConfigFactory::FIELD_CONFIG_VERSION, $this->fixture->createDefault());
    }

    public function testThrowsExceptionWhenConfigVersionIsNotSupported() : void
    {
        $this->expectException(RuntimeException::class);

        $root = vfsStream::setup();
        $configFile = vfsStream::newFile('config.xml')->withContent(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <configVersion>foo</configVersion>
</phpdocumentor>
XML
)->at($root);

        $fixture = $this->createConfigFactoryWithTestDefinition();
        $fixture->createFromFile($configFile->url());
    }

    private function createConfigFactoryWithTestDefinition() : SymfonyConfigFactory
    {
        $configMock = $this->prophesize(ConfigurationInterface::class);
        $configMock->getConfigTreeBuilder()->willReturn($this->faker()->configTreeBuilder('test'));
        return new SymfonyConfigFactory(['test' => $configMock->reveal()]);
    }
}
