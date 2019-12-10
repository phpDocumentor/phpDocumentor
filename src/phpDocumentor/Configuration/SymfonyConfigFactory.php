<?php declare(strict_types=1);

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Definition\Upgradable;
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

        $processor = new Processor();

        $configuration = $processor->processConfiguration(
            $definition,
            [ $values ]
        );

        if ($definition instanceof Upgradable) {
            $upgradedConfiguration = $definition->upgrade($configuration);
            if (!isset($upgradedConfiguration['v']) || $configurationVersion === $upgradedConfiguration['v']) {
                throw new \RuntimeException(
                    sprintf(
                        'Upgrading the configuration to the latest version failed, we were unable to upgrade '
                        . 'version "%s" to a later version',
                        $configurationVersion
                    )
                );
            }

            $configuration = $upgradedConfiguration;
        }

        return $configuration;
    }
}
