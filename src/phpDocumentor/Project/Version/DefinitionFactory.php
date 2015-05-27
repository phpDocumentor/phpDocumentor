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

use phpDocumentor\DocumentGroupDefinitionFactory;
use phpDocumentor\DocumentGroupFormat;
use phpDocumentor\Project\DocumentGroup\Definition as DocumentGroupDefinition;
use phpDocumentor\Project\VersionNumber;

/**
 * Factory for Version definition.
 * Will use the registered factories to create the configured DocumentGroup\Definitions.
 */
class DefinitionFactory implements \phpDocumentor\DefinitionFactory
{
    /**
     * @var DocumentGroupDefinitionFactory[]
     */
    private $documentGroupDefinitionFactories;

    /**
     * Creates a full provisioned version definition
     *
     * @param array $options
     * @return Definition
     */
    public function create(array $options)
    {
        $documentGroups = $this->createDocumentGroupDefinitions($options);

        return new Definition(
            new VersionNumber($options['version']),
            $documentGroups
        );
    }

    /**
     * creates a set of DocumentGroups as configured in the options.
     *
     * @param array $options
     * @return DocumentGroupDefinition[]
     */
    private function createDocumentGroupDefinitions(array $options)
    {
        $documentGroups = array();

        foreach ($options as $documentGroupType => $documentGroupOptions) {
            if(is_array($documentGroupOptions)) {
                $factory = $this->findFactory($documentGroupType, $documentGroupOptions['format']);
                if ($factory !== null) {
                    $documentGroups[] = $factory->create($documentGroupOptions);
                }
            }
        }
        return $documentGroups;
    }

    /**
     * @param string $type
     * @param string $format
     * @return null|DocumentGroupDefinitionFactory
     */
    private function findFactory($type, $format)
    {
        if (isset($this->documentGroupDefinitionFactories[$type][$format])) {
            return $this->documentGroupDefinitionFactories[$type][$format];
        }

        return null;
    }

    /**
     * Register a factory for later usage for a given type and format.
     * Will override registered factories.
     * The combination of type and format will identify a certain documentGroup
     *
     * @param string $type
     * @param DocumentGroupFormat $format
     * @param DocumentGroupDefinitionFactory $factory
     */
    public function registerDocumentGroupDefinitionFactory($type, DocumentGroupFormat $format, DocumentGroupDefinitionFactory $factory)
    {
        $this->documentGroupDefinitionFactories[$type][(string)$format] = $factory;
    }
}
