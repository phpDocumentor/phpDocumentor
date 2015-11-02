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

namespace phpDocumentor\ApiReference;

use InvalidArgumentException;
use League\Event\Emitter;
use phpDocumentor\DocumentGroup;
use phpDocumentor\DocumentGroupDefinition as DocumentGroupDefinitionInterface;
use phpDocumentor\DocumentGroupFactory;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\File\FlySystemAdapter;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;

final class Factory implements DocumentGroupFactory
{
    /** @var Emitter */
    private $emitter;

    /**
     * @var \phpDocumentor\ApiReference\ProjectFactoryStrategy
     */
    private $stategies;

    /**
     * @var array
     */
    private $middleware;

    /**
     * @param ProjectFactoryStrategy[] $stategies
     * @param Middleware[] $cache
     */
    public function __construct(Emitter $emitter, array $stategies = array(), array $middleware = array())
    {
        $this->stategies = $stategies;
        $this->middleware = $middleware;
        $this->emitter = $emitter;
    }

    /**
     * Creates Document group using the provided definition.
     *
     * @param DocumentGroupDefinitionInterface $definition
     * @return DocumentGroup
     */
    public function create(DocumentGroupDefinitionInterface $definition)
    {
        /** @var DocumentGroupDefinition $definition */
        if (!$this->matches($definition)) {
            throw new InvalidArgumentException('Definition must be an instance of ' . DocumentGroupDefinition::class);
        }

        $strategies = $this->stategies;
        $strategies[] = new File(
            NodesFactory::createInstance(),
            new FlySystemAdapter($definition->getFilesystem()),
            $this->middleware
        );

        $projectFactory = new ProjectFactory(
            $strategies
        );

        // TODO: Read title (My Project) from configuration
        $this->emitter->emit(new ParsingStarted($definition));
        $project = $projectFactory->create('My Project', $definition->getFiles());
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
        if ($definition instanceof DocumentGroupDefinition) {
            return true;
        }

        return false;
    }
}
