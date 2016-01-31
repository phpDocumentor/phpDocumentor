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

namespace phpDocumentor\DomainModel\Documentation\Api;

use InvalidArgumentException;
use League\Event\Emitter;
use phpDocumentor\DomainModel\Documentation\Api\ParsingCompleted;
use phpDocumentor\DomainModel\Documentation\Api\ParsingStarted;
use phpDocumentor\DomainModel\Documentation\Api\Api;
use phpDocumentor\DomainModel\Documentation\Api\Definition;
use phpDocumentor\DomainModel\Documentation\DocumentGroup;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\Definition as DocumentGroupDefinitionInterface;
use phpDocumentor\DomainModel\Documentation\DocumentGroupFactory;
use phpDocumentor\Reflection\ProjectFactory;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\File\FlySystemAdapter;

final class Factory implements DocumentGroupFactory
{
    /** @var Emitter */
    private $emitter;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @param Emitter $emitter
     * @param ProjectFactory $projectFactory
     */
    public function __construct(Emitter $emitter, ProjectFactory $projectFactory)
    {
        $this->emitter = $emitter;
        $this->projectFactory = $projectFactory;
    }

    /**
     * Creates Document group using the provided definition.
     *
     * @param DocumentGroupDefinitionInterface $definition
     * @return DocumentGroup
     */
    public function create(DocumentGroupDefinitionInterface $definition)
    {
        /** @var Definition $definition */
        if (!$this->matches($definition)) {
            throw new InvalidArgumentException('Definition must be an instance of ' . Definition::class);
        }

        // TODO: Read title (My Project) from configuration
        $this->emitter->emit(new ParsingStarted($definition));
        $project = $this->projectFactory->create('My Project', $definition->getFiles());
        $this->emitter->emit(new ParsingCompleted($definition));

        return new Api($definition->getFormat(), $project);
    }

    /**
     * Will return true when this factory can handle the provided definition.
     *
     * @param DocumentGroupDefinitionInterface $definition
     * @return boolean
     */
    public function matches(DocumentGroupDefinitionInterface $definition)
    {
        if ($definition instanceof Definition) {
            return true;
        }

        return false;
    }
}
