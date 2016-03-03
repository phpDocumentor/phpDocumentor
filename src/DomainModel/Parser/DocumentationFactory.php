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

namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\Definition;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroupFactory;
use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\Parser\Version\Definition as VersionDefinition;
use phpDocumentor\DomainModel\Parser\FactoryNotFoundException;

/**
 * Factory to create Documentation objects out of Project\Version\Definition.
 * Will use DocumentationGroupFactories to create the DocumentationGroups.
 * DocumentationGroupFactories can be registered using the addDocumentationGroupFactory method
 */
final class DocumentationFactory
{
    /**
     * Registered factories to create DocumentGroup objects.
     *
     * @var DocumentGroupFactory[]
     */
    private $documentGroupFactories;

    /**
     * Creates Documentation object.
     *
     * @param VersionDefinition $versionDefinition
     * @return Documentation
     * @throws FactoryNotFoundException when a DocumentGroupDefinition did not match any of the registered factories.
     */
    public function create(VersionDefinition $versionDefinition)
    {
        $documentGroups = array();

        foreach ($versionDefinition->getDocumentGroupDefinitions() as $definition) {
            $documentGroups[] = $this->createDocumentGroup($definition);
        }

        return new Documentation($versionDefinition->getVersionNumber(), $documentGroups);
    }

    /**
     * Find matching factory and use it to create DocumentGroup.
     *
     * @param Definition $definition
     *
     * @return DocumentGroup
     * @throws FactoryNotFoundException when a DocumentGroupDefinition did not match any of the registered factories.
     */
    private function createDocumentGroup(Definition $definition)
    {
        foreach ($this->documentGroupFactories as $factory) {
            if ($factory->matches($definition)) {
                return $factory->create($definition);
            }
        }

        throw new FactoryNotFoundException('No factory matches document group');
    }

    /**
     * Add DocumentGroupFactory to internal register for use during creation of Documentation object.
     *
     * @param $documentGroupFactory
     * @return void
     */
    public function addDocumentGroupFactory(DocumentGroupFactory $documentGroupFactory)
    {
        $this->documentGroupFactories[] = $documentGroupFactory;
    }
}
