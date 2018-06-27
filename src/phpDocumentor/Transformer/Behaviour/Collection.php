<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
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
    protected $behaviours = [];

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param BehaviourAbstract[] $behaviours  List of behaviours to process.
     */
    public function __construct(array $behaviours = [])
    {
        $this->behaviours = $behaviours;
    }

    /**
     * Adds a behaviour to a collection
     *
     * @param BehaviourAbstract $behaviour Behaviour to add to the collection.
     */
    public function addBehaviour(BehaviourAbstract $behaviour)
    {
        $this->behaviours[] = $behaviour;
    }

    /**
     * Removes a behaviour from the collection
     *
     * @param BehaviourAbstract $behaviour Behaviour to remove from the collection.
     */
    public function removeBehaviour(BehaviourAbstract $behaviour)
    {
        foreach ($this->behaviours as $key => $thisBehaviour) {
            if ($behaviour === $thisBehaviour) {
                unset($this->behaviours[$key]);
            }
        }
    }

    /**
     * Executes the behaviour on the given object model,
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
