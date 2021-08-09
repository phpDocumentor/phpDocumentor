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
use phpDocumentor\Configuration\Exception\UnSupportedConfigVersionException;
use phpDocumentor\Configuration\Exception\UpgradeFailedException;
use phpDocumentor\Dsn;
use RuntimeException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Util\XmlUtils;

use function array_key_last;
use function array_keys;

class SymfonyConfigFactory
{
    public const FIELD_CONFIG_VERSION = 'configVersion';
    private const DEFAULT_CONFIG_VERSION = '2';

    /** @var ConfigurationInterface[] $configurationDefinitions */
    private $configurationDefinitions;

    /**
     * @param ConfigurationInterface[] $definitions
     */
    public function __construct(array $definitions)
    {
        $this->configurationDefinitions = $definitions;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @return array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool, visibility: non-empty-array<array-key, string>}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function createFromFile(string $filename): array
    {
        $values = XmlUtils::loadFile($filename);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        return $this->generateConfiguration($values);
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @return array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool, visibility: non-empty-array<array-key, string>}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function createDefault(): array
    {
        return $this->generateConfiguration([
            self::FIELD_CONFIG_VERSION => (string) array_key_last($this->configurationDefinitions),
        ]);
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array<mixed> $values
     *
     * @return array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    private function generateConfiguration(array $values): array
    {
        $configuration = $this->processConfiguration($values);
        if ($configuration[self::FIELD_CONFIG_VERSION] !== (string) array_key_last($this->configurationDefinitions)) {
            throw new RuntimeException(
                'The configuration file does not match the latest version and auto-upgrading failed. Please '
                . 'contact the maintainers and provide your configuration file or whole project to reproduce this issue'
            );
        }

        // prefix is needed because other parts of the application require 'phpdocumentor' as root
        // it would be nice to refactor this necessity away at some point
        return ['phpdocumentor' => $configuration];
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * Normalizes and validates the given values.
     *
     * When this version of the configuration can be upgraded (which is detected by the Upgradable interface on the
     * Configuration definition) then it will do so and re-run this method with the upgraded values. The 'configVersion'
     * field will tell which definition should be used; when none is provided then a version 2 configuration is assumed.
     *
     * @param array{configVersion?: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>} $values
     *
     * @return array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
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
                $this->upgradeConfiguration($definition, $configuration)
            );
        }

        return $configuration;
    }

    private function findDefinition(string $configurationVersion): ConfigurationInterface
    {
        $definition = $this->configurationDefinitions[$configurationVersion] ?? null;
        if ($definition === null) {
            throw UnSupportedConfigVersionException::create(
                $configurationVersion,
                array_keys($this->configurationDefinitions)
            );
        }

        return $definition;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{configVersion?: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>} $configuration
     *
     * @return array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    private function upgradeConfiguration(Upgradable $definition, array $configuration): array
    {
        $upgradedConfiguration = $definition->upgrade($configuration);
        if (
            !isset($upgradedConfiguration[self::FIELD_CONFIG_VERSION])
            || $configuration[self::FIELD_CONFIG_VERSION] === $upgradedConfiguration[self::FIELD_CONFIG_VERSION]
        ) {
            throw UpgradeFailedException::create($configuration[self::FIELD_CONFIG_VERSION]);
        }

        return $upgradedConfiguration;
    }
}
