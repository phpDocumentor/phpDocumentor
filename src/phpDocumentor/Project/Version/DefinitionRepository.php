<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Project\Version;


final class DefinitionRepository
{
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
     * @param $configurationFactory
     * @param DefinitionFactory $definitionFactory
     */
    public function __construct($configurationFactory, DefinitionFactory $definitionFactory)
    {
        $this->configurationFactory = $configurationFactory;
        $this->definitionFactory = $definitionFactory;
    }

    /**
     * Fetch one specific version. Will return null when version doesn't exist
     *
     * @param $versionNumber
     * @return null|Definition
     */
    public function fetch($versionNumber)
    {
        $config = $this->configurationFactory->get();
        if (isset($config['versions'][$versionNumber])) {
            return $this->definitionFactory->create(
                array_merge(
                    ['version' => $versionNumber],
                    $config['versions'][$versionNumber]
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
        if (isset($config['versions'])) {
            foreach ($config['versions'] as $version => $options ) {
                $definitions[] = $this->fetch($version);
            }
        }

        return $definitions;
    }
}