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

namespace phpDocumentor\Transformer\Behaviour;

use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Collection object for a set of Behaviours.
 */
class Collection extends BehaviourAbstract implements \Countable
{
    /** @var BehaviourAbstract[] the list of behaviours that can be exposed using this collection */
    protected $behaviours = array();

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param BehaviourAbstract[] $behaviours  List of behaviours to process.
     */
    public function __construct(array $behaviours = array())
    {
        $this->behaviours  = $behaviours;
    }

    /**
     * Adds a behaviour to a collection
     *
     * @param BehaviourAbstract $behaviour Behaviour to add to the collection.
     *
     * @return void
     */
    public function addBehaviour(BehaviourAbstract $behaviour)
    {
        $this->behaviours[] = $behaviour;
    }

    /**
     * Removes a behaviour from the collection
     *
     * @param BehaviourAbstract $behaviour Behaviour to remove from the collection.
     *
     * @return void
     */
    public function removeBehaviour(BehaviourAbstract $behaviour)
    {
        foreach ($this->behaviours as $key => $thisBehaviour) {
            if ($behaviour == $thisBehaviour) {
                unset($this->behaviours[$key]);
            }
        }
    }

    /**
     * Executes the behaviour on the given object model,
     *
     * @param ProjectDescriptor $project
     *
     * @return ProjectDescriptor
     */
    public function process(ProjectDescriptor $project)
    {
        foreach ($this->behaviours as $behaviour) {
            $project = $behaviour->process($project);
        }

        return $project;
    }

    /**
     * Count the number of behaviours in this collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->behaviours);
    }
}
