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

use phpDocumentor\Configuration\Definition\Normalizable;
use phpDocumentor\Configuration\Definition\Upgradable;
use phpDocumentor\Configuration\Exception\UnsupportedConfigVersionException;
use phpDocumentor\Configuration\Exception\UpgradeFailedException;
use RuntimeException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Util\XmlUtils;
use Webmozart\Assert\Assert;

use function array_key_last;
use function array_keys;

/** @psalm-import-type ConfigurationMap from ConfigurationFactory */
class SymfonyConfigFactory
{
    final public const FIELD_CONFIG_VERSION = 'configVersion';
    private const DEFAULT_CONFIG_VERSION = '2';

    /** @var ConfigurationInterface[] $configurationDefinitions */
    private array $configurationDefinitions;

    /** @param ConfigurationInterface[] $definitions */
    public function __construct(array $definitions)
    {
        Assert::allIsInstanceOf($definitions, ConfigurationInterface::class);

        $this->configurationDefinitions = $definitions;
    }

    /** @return ConfigurationMap */
    public function createFromFile(string $filename): array
    {
        $values = XmlUtils::loadFile($filename);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        return $this->generateConfiguration($values);
    }

    /** @return ConfigurationMap */
    public function createDefault(): array
    {
        return $this->generateConfiguration([
            self::FIELD_CONFIG_VERSION => (string) array_key_last($this->configurationDefinitions),
        ]);
    }

    /**
     * @param array<mixed> $values
     *
     * @return ConfigurationMap
     */
    private function generateConfiguration(array $values): array
    {
        $configuration = $this->processConfiguration($values);
        if ($configuration[self::FIELD_CONFIG_VERSION] !== (string) array_key_last($this->configurationDefinitions)) {
            throw new RuntimeException(
                'The configuration file does not match the latest version and auto-upgrading failed. '
                 . 'Please contact the maintainers and provide your configuration file '
                 . 'or whole project to reproduce this issue',
            );
        }

        // prefix is needed because other parts of the application require 'phpdocumentor' as root
        // it would be nice to refactor this necessity away at some point
        return ['phpdocumentor' => $configuration];
    }

    /**
     * Normalizes and validates the given values.
     *
     * When this version of the configuration can be upgraded (which is detected by the Upgradable interface on the
     * Configuration definition) then it will do so and re-run this method with the upgraded values. The 'configVersion'
     * field will tell which definition should be used; when none is provided then a version 2 configuration is assumed.
     *
     * @param array<mixed> $values because this method is called recursively, the provided shape varies and is the
     *   output of the ConfigurationDefinition matching the prior configVersion is used.
     *
     * @return array<mixed> the recursiveness of this method means that the output of this method has the shape of the
     *   current ConfigurationDefinition's output.
     */
    private function processConfiguration(array $values): array
    {
        $configurationVersion = (string) ($values[self::FIELD_CONFIG_VERSION] ?? self::DEFAULT_CONFIG_VERSION);

        $definition = $this->findDefinition($configurationVersion);

        $processor = new Processor();
        $configuration = $processor->processConfiguration($definition, [$values]);
        if ($definition instanceof Normalizable) {
            $configuration = $definition->normalize($configuration);
        }

        if ($definition instanceof Upgradable) {
            $configuration = $this->processConfiguration(
                $this->upgradeConfiguration($definition, $configuration),
            );
        }

        return $configuration;
    }

    private function findDefinition(string $configurationVersion): ConfigurationInterface
    {
        $definition = $this->configurationDefinitions[$configurationVersion] ?? null;
        if ($definition === null) {
            throw UnsupportedConfigVersionException::create(
                $configurationVersion,
                array_keys($this->configurationDefinitions),
            );
        }

        return $definition;
    }

    /**
     * @param array<mixed> $configuration because this method is called as part of the recursive method
     *   processConfiguration, the provided shape varies and is the output of the normalisation process of provided
     *   ConfigurationDefinition.
     *
     * @return array<mixed> this method's output will match the shape of the output of the provided
     *   ConfigurationDefinition.
     */
    private function upgradeConfiguration(Upgradable $definition, array $configuration): array
    {
        $upgradedConfiguration = $definition->upgrade($configuration);
        if (
            ! isset($upgradedConfiguration[self::FIELD_CONFIG_VERSION])
            || $configuration[self::FIELD_CONFIG_VERSION] === $upgradedConfiguration[self::FIELD_CONFIG_VERSION]
        ) {
            throw UpgradeFailedException::create($configuration[self::FIELD_CONFIG_VERSION]);
        }

        return $upgradedConfiguration;
    }
}
