<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour;

/**
 * Collection object for a set of Behaviours.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Collection
    extends \phpDocumentor\Transformer\Behaviour\BehaviourAbstract
{
    /** @var \phpDocumentor\Transformer\Transformer */
    protected $transformer = null;

    /** @var \phpDocumentor\Transformer\Behaviour\BehaviourAbstract[] */
    protected $behaviours = array();

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param \phpDocumentor\Transformer\Transformer          $transformer Object
     *     that executes the transformation and contains the meta-data.
     * @param \phpDocumentor\Transformer\Behaviour\BehaviourAbstract[] $behaviours  List of
     *     behaviours to process.
     */
    public function __construct(
        \phpDocumentor\Transformer\Transformer $transformer, array $behaviours
    ) {
        $this->transformer = $transformer;

        foreach ($behaviours as $behaviour) {
            $behaviour->setTransformer($transformer);
        }

        $this->behaviours  = $behaviours;
    }

    /**
     * Adds a behaviour to a collection
     *
     * @param \phpDocumentor\Transformer\Behaviour\BehaviourAbstract $behaviour Behaviour to add
     *     to the collection.
     *
     * @return void
     */
    public function addBehaviour(
        \phpDocumentor\Transformer\Behaviour\BehaviourAbstract $behaviour
    ) {
        $this->behaviours[] = $behaviour;
    }

    /**
     * Removes a behaviour from the collection
     *
     * @param \phpDocumentor\Transformer\Behaviour\BehaviourAbstract $behaviour
     *     Behaviour to remove from the collection.
     *
     * @return void
     */
    public function removeBehaviour(
        \phpDocumentor\Transformer\Behaviour\BehaviourAbstract $behaviour
    ) {
        foreach ($this->behaviours as $key => $thisBehaviour) {
            if ($behaviour == $thisBehaviour) {
                unset($this->behaviours[$key]);
            }
        }
    }

    /**
     * Executes the behaviour on the given structure xml source,
     *
     * @param \DOMDocument $xml Structure source to apply the behaviours on.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        foreach ($this->behaviours as $behaviour) {
            $xml = $behaviour->process($xml);
        }

        return $xml;
    }

}