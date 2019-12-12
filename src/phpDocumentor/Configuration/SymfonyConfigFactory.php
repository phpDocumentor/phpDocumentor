<?php declare(strict_types=1);

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Definition\Upgradable;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Util\XmlUtils;

final class SymfonyConfigFactory
{
    private $configurationDefinitions = [];

    public function __construct(array $definitions)
    {
        $this->configurationDefinitions = $definitions;
    }

    public function create(string $filename) : array
    {
        $values = XmlUtils::loadFile($filename, null);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        $configuration = $this->processConfiguration($values);
        if ($configuration['v'] !== (string) array_key_last($this->configurationDefinitions)) {
            throw new \RuntimeException(
                'The configuration file does not match the latest version and auto-upgrading failed. Please '
                . 'contact the maintainers and provide your configuration file or whole project to reproduce this issue'
            );
        }

        $configuration['paths']['output'] = new Dsn('file://' . $configuration['paths']['output']);
        $configuration['paths']['cache'] = new Path($configuration['paths']['cache']);
        foreach ($configuration['versions'] as $versionNumber => $version) {
            foreach ($version['api'] as $key => $api) {
                $configuration['versions'][$versionNumber]['api'][$key]['source']['dsn']
                    = new Dsn($api['source']['dsn']);
                foreach ($api['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['api'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
                $configuration['versions'][$versionNumber]['api'][$key]['extensions'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['extensions']['extension'];
                $configuration['versions'][$versionNumber]['api'][$key]['markers'] =
                    $configuration['versions'][$versionNumber]['api'][$key]['markers']['markers'];
            }
            foreach ($version['guide'] as $key => $guide) {
                $configuration['versions'][$versionNumber]['guide'][$key]['source']['dsn']
                    = new Dsn($guide['source']['dsn']);
                foreach ($guide['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['guide'][$key]['source']['paths'][$subkey] =
                        new Path($path);
                }
            }
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
