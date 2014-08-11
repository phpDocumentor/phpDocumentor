<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;

/**
 * This class collects data from the example tag definition of the Reflection library, tries to find the correlating
 * example file on disk and creates a complete Descriptor from that.
 */
class ExampleAssembler extends AssemblerAbstract
{
    /** @var Finder */
    private $finder;

    /**
     * Initializes this assembler with the means to find the example file on disk.
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param ExampleTag $data
     *
     * @throws \InvalidArgumentException if the provided parameter is not of type ExampleTag; the interface won't let
     *   up typehint the signature.
     *
     * @return ExampleDescriptor
     */
    public function create($data)
    {
        if (! $data instanceof ExampleTag) {
            throw new \InvalidArgumentException(
                'The ExampleAssembler expected an ExampleTag object to base the descriptor on'
            );
        }

        $descriptor = new ExampleDescriptor($data->getName());
        $descriptor->setFilePath((string) $data->getFilePath());
        $descriptor->setStartingLine($data->getStartingLine());
        $descriptor->setLineCount($data->getLineCount());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setExample($this->finder->find($descriptor));

        return $descriptor;
    }
}
