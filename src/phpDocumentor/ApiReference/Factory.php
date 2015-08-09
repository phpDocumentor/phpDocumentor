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

use phpDocumentor\DocumentGroup;
use phpDocumentor\DocumentGroupDefinition as DocumentGroupDefinitionInterface;
use phpDocumentor\DocumentGroupFactory;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Php\Factory\File\FlySystemAdapter;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Factory as ProjectFactoryStrategy;
use phpDocumentor\Reflection\PrettyPrinter;

final class Factory implements DocumentGroupFactory
{
    /**
     * Creates Document group using the provided definition.
     *
     * @param DocumentGroupDefinitionInterface $definition
     * @return DocumentGroup
     */
    public function create(DocumentGroupDefinitionInterface $definition)
    {
        if ($definition instanceof DocumentGroupDefinition) {

            $projectFactory = new ProjectFactory(
                [
                    new ProjectFactoryStrategy\Argument(new PrettyPrinter()),
                    new ProjectFactoryStrategy\Class_(),
                    new ProjectFactoryStrategy\Constant(new PrettyPrinter()),
                    new ProjectFactoryStrategy\DocBlock(DocBlockFactory::createInstance()),
                    new ProjectFactoryStrategy\File(
                        NodesFactory::createInstance(),
                        new FlySystemAdapter($definition->getFilesystem())
                    ),
                    new ProjectFactoryStrategy\Function_(),
                    new ProjectFactoryStrategy\Interface_(),
                    new ProjectFactoryStrategy\Method(),
                    new ProjectFactoryStrategy\Property(new PrettyPrinter()),
                    new ProjectFactoryStrategy\Trait_(),
                ]
            );

            $project = $projectFactory->create('My Project', $definition->getFiles());

            return new Api($definition->getFormat(), $project);
        }
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
