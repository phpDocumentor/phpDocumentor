<?php declare(strict_types=1);

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Definition\Upgradable;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Util\XmlUtils;

final class SymfonyConfigFactory
{
    private $configurationDefinitions = [];

    public function __construct()
    {
        $this->configurationDefinitions = [
            '2' => new Definition\Version2(),
            '3' => new Definition\Version3()
        ];
    }

    public function create() : array
    {
        $values = XmlUtils::loadFile(__DIR__ . '/../../../phpdoc.xml', null);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        $configuration = $this->processConfiguration($values);
        if ((string)$configuration['v'] !== (string)array_key_last($this->configurationDefinitions)) {
            throw new \RuntimeException(
                'The configuration file does not match the latest version and auto-upgrading failed. Please '
                . 'contact the maintainers and provide your configuration file or whole project to reproduce this issue'
            );
        }
        return $configuration;
    }

    /**
     * Normalizes and validates the given values.
     *
     * When this version of the configuration can be upgraded (which is detected by the Upgradable interface on the
     * Configuration definition) then it will do so and re-run this method with the upgraded values. The 'v' field will
     * tell which definition should be used; when none is provided then a version 2 configuration is assumed.
     */
    private function processConfiguration(array $values) : array
    {
        $configurationVersion = (string)$values['v'] ?? '2';

        $definition = $this->findDefinition($configurationVersion);

        $processor = new Processor();
        $configuration = $processor->processConfiguration($definition, [ $values ]);

        if ($definition instanceof Upgradable) {
            $configuration = $this->processConfiguration(
                $this->upgradeConfiguration($definition, $configuration)
            );
        }

        return $configuration;
    }

    private function findDefinition(string $configurationVersion) : ConfigurationInterface
    {
        $definition = $this->configurationDefinitions[$configurationVersion] ?? null;
        if ($definition === null) {
            throw new \RuntimeException(
                sprintf(
                    'Configuration version "%s" is not supported by this version of phpDocumentor, '
                    . 'supported versions are: %s',
                    $configurationVersion,
                    implode(', ', array_keys($this->configurationDefinitions))
                )
            );
        }

        return $definition;
    }

    /**
     * @param $definition
     * @param array $configuration
     * @return array
     */
    private function upgradeConfiguration($definition, array $configuration) : array
    {
        $upgradedConfiguration = $definition->upgrade($configuration);
        if (
            !isset($upgradedConfiguration['v'])
            || $configuration['v'] === $upgradedConfiguration['v']
        ) {
            throw new \RuntimeException(
                sprintf(
                    'Upgrading the configuration to the latest version failed, we were unable to upgrade '
                    . 'version "%s" to a later version',
                    $configuration['v']
                )
            );
        }

        $configuration = $upgradedConfiguration;
        return $configuration;
    }
}
