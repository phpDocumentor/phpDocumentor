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

namespace phpDocumentor;

use phpDocumentor\Project\Version\Definition;

/**
 * Factory to create Documentation objects out of Project\Version\Definition.
 * Will use DocumentationGroupFactories to create the DocumentationGroups.
 * DocumentationGroupFactories can be registered using the addDocumentationGroupFactory method
 */
final class DocumentationFactory
{
    /**
     * @var DocumentGroupFactory[]
     */
    private $documentGroupFactories;

    /**
     * Creates Documentation object.
     *
     * @param Definition $versionDefinition
     * @return Documentation
     * @throws Exception
     */
    public function create(Definition $versionDefinition)
    {
        $documentGroups = array();

        foreach ($versionDefinition->getDocumentGroupDefinitions() as $definition) {
            $documentGroups[] = $this->createDocumentGroup($definition);
        }

        return new Documentation(null, $versionDefinition->getVersionNumber(), $documentGroups);
    }

    /**
     * Find matching factory and use it to create DocumentGroup.
     *
     * @param DocumentGroupDefinition $definition
     * @return DocumentGroup
     * @throws Exception
     */
    private function createDocumentGroup(DocumentGroupDefinition $definition)
    {
        foreach ($this->documentGroupFactories as $factory) {
            if ($factory->matches($definition)) {
                return $factory->create($definition);
            }
        }

        throw new Exception('No factory matches document group');
    }

    /**
     * Add DocumentGroupFactory to internal register for use during creation of Documentation object.
     *
     * @param $documentGroupFactory
     */
    public function addDocumentGroupFactory(DocumentGroupFactory $documentGroupFactory)
    {
        $this->documentGroupFactories[] = $documentGroupFactory;
    }
}
