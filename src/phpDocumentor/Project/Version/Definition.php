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

use phpDocumentor\Definition as DefinitionInterface;
use phpDocumentor\DocumentGroupDefinition;
use phpDocumentor\Project\VersionNumber;

/**
 * An aggregate of documentGroupDefinitions that belong to a version.
 */
final class Definition implements DefinitionInterface
{

    /**
     * @var VersionNumber
     */
    private $versionNumber;

    /**
     * @var DocumentGroupDefinition[]
     */
    private $documentGroupDefinitions;

    /**
     * Initializes the object with passed values.
     *
     * @param VersionNumber $versionNumber
     * @param DocumentGroupDefinition[] $documentGroupDefinition
     */
    public function __construct(VersionNumber $versionNumber, array $documentGroupDefinition = array())
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
     * @return VersionNumber
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }
}
