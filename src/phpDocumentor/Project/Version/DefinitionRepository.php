<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Project\Version;
use phpDocumentor\ConfigurationFactory;

/**
 * Repository providing version definitions.
 */
final class DefinitionRepository
{
    /**
     * Factory class to create configuration.
     *
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * Factory class to create version definitions
     *
     * @var DefinitionFactory
     */
    private $definitionFactory;

    /**
     * Initializes the repository.
     *
     * @param ConfigurationFactory $configurationFactory
     * @param DefinitionFactory $definitionFactory
     */
    public function __construct(ConfigurationFactory $configurationFactory, DefinitionFactory $definitionFactory)
    {
        $this->configurationFactory = $configurationFactory;
        $this->definitionFactory = $definitionFactory;
    }

    /**
     * Fetch one specific version. Will return null when version doesn't exist
     *
     * @param string $versionNumber
     * @return null|Definition
     */
    public function fetch($versionNumber)
    {
        $config = $this->configurationFactory->get();
        if (isset($config['phpdocumentor']['versions'][$versionNumber])) {
            return $this->definitionFactory->create(
                array_merge(
                    ['version' => $versionNumber],
                    $config['phpdocumentor']['versions'][$versionNumber]
                )
            );
        }

        return null;
    }

    /**
     * Fetch all versions defined in the config
     *
     * @return Definition[]
     */
    public function fetchAll()
    {
        $definitions = array();
        $config = $this->configurationFactory->get();
        if (isset($config['phpdocumentor']['versions'])) {
            foreach ($config['phpdocumentor']['versions'] as $version => $options ) {
                $definitions[] = $this->fetch($version);
            }
        }

        return $definitions;
    }
}
