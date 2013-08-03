<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Behaviour;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformer;

/**
 * Collection object for a set of Behaviours.
 */
abstract class BehaviourAbstract
{
    /** @var Transformer $transformer */
    protected $transformer = null;

    /**
     * Executes the behaviour on the given dataset,
     *
     * @param ProjectDescriptor $project document containing the source structure.
     *
     * @return ProjectDescriptor
     */
    abstract public function process(ProjectDescriptor $project);

    /**
     * Sets the transformer used for this behaviour.
     *
     * @param Transformer $transformer Transformer responsible for output.
     *
     * @return void
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer that hosts this behaviour.
     *
     * @return Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
