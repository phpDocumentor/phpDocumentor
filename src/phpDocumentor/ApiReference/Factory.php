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
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Middleware\LoggingMiddleware;
use phpDocumentor\Reflection\Php\Factory\File\FlySystemAdapter;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Factory as ProjectFactoryStrategy;
use phpDocumentor\Reflection\PrettyPrinter;

final class Factory implements DocumentGroupFactory
{
    /** @var Emitter */
    private $emitter;

    public function __construct(Emitter $emitter)
    {
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

        // TODO: move this to a custom factory so that we can inject the factory in this class and then have the
        // middleware as dependency of that factory.
        $projectFactory = new ProjectFactory(
            [
                new ProjectFactoryStrategy\Argument(new PrettyPrinter()),
                new ProjectFactoryStrategy\Class_(),
                new ProjectFactoryStrategy\Constant(new PrettyPrinter()),
                new ProjectFactoryStrategy\DocBlock(DocBlockFactory::createInstance()),
                new ProjectFactoryStrategy\File(
                    NodesFactory::createInstance(),
                    new FlySystemAdapter($definition->getFilesystem()),
                    [new LoggingMiddleware($this->emitter)]
                ),
                new ProjectFactoryStrategy\Function_(),
                new ProjectFactoryStrategy\Interface_(),
                new ProjectFactoryStrategy\Method(),
                new ProjectFactoryStrategy\Property(new PrettyPrinter()),
                new ProjectFactoryStrategy\Trait_(),
            ]
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
