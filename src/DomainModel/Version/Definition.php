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

namespace phpDocumentor\DomainModel\Version;

use phpDocumentor\Definition as DefinitionInterface;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\Definition as DocumentGroupDefinition;
use phpDocumentor\DomainModel\Version\Number;

/**
 * An aggregate of documentGroupDefinitions that belong to a version.
 */
final class Definition implements DefinitionInterface
{
    /** @var Number */
    private $versionNumber;

    /** @var DocumentGroupDefinition[] */
    private $documentGroupDefinitions;

    /**
     * Initializes the object with passed values.
     *
     * @param Number $versionNumber
     * @param DocumentGroupDefinition[] $documentGroupDefinition
     */
    public function __construct(Number $versionNumber, array $documentGroupDefinition = array())
    {
        $this->versionNumber = $versionNumber;
        $this->documentGroupDefinitions = $documentGroupDefinition;
    }

    /**
     * Returns all document Group definitions that are involved in this version.
     *
     * @return DocumentGroupDefinition[]
     */
    public function getDocumentGroupDefinitions()
    {
        return $this->documentGroupDefinitions;
    }

    /**
     * Returns the VersionNumber of this version definition.
     *
     * @return Number
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }
}
