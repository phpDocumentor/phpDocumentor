<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Definition\Normalizable;
use phpDocumentor\Configuration\Definition\Upgradable;
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

    public function createFromFile(string $filename) : array
    {
        $values = XmlUtils::loadFile($filename, null);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        return $this->generateConfiguration($values);
    }

    public function createDefault(): array
    {
        return $this->generateConfiguration(['v' => (string) array_key_last($this->configurationDefinitions)]);
    }

    private function generateConfiguration(array $values) : array
    {
        $configuration = $this->processConfiguration($values);
        if ($configuration['v'] !== (string) array_key_last($this->configurationDefinitions)) {
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
        $configurationVersion = (string) $values['v'] ?? '2';

        $definition = $this->findDefinition($configurationVersion);

        $processor = new Processor();
        $configuration = $processor->processConfiguration($definition, [$values]);
        if ($definition instanceof Normalizable) {
            $configuration = $definition->normalize($configuration);
        }

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

    private function upgradeConfiguration(ConfigurationInterface $definition, array $configuration) : array
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

        return $upgradedConfiguration;
    }
}
