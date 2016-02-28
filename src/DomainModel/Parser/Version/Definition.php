<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Version;

use phpDocumentor\DomainModel\Parser\Definition as DefinitionInterface;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\Definition as DocumentGroupDefinition;
use Webmozart\Assert\Assert;

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
        Assert::allIsInstanceOf($documentGroupDefinition, DocumentGroupDefinition::class);

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
