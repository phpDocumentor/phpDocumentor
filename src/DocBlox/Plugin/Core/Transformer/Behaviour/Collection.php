<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Collection object for a set of Behaviours.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Collection extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /** @var DocBlox_Transformer */
    protected $transformer = array();

    /** @var DocBlox_Transformer_Behaviour_Abstract[] */
    protected $behaviours = array();

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param DocBlox_Transformer                       $transformer
     * @param DocBlox_Transformer_Behaviour_Abstract[] $behaviours
     */
    public function __construct(DocBlox_Transformer $transformer, array $behaviours)
    {
        $this->transformer = $transformer;

        foreach($behaviours as $behaviour) {
            $behaviour->setTransformer($transformer);
        }
        $this->behaviours  = $behaviours;
    }

    /**
     * Adds a behaviour to a collection
     *
     * @param DocBlox_Transformer_Behaviour_Abstract $behaviour
     *
     * @return void
     */
    public function addBehaviour(
        DocBlox_Transformer_Behaviour_Abstract $behaviour)
    {
        $this->behaviours[] = $behaviour;
    }

    /**
     * Removes a behaviour from the collection
     *
     * @param DocBlox_Transformer_Behaviour_Abstract $behaviour
     *
     * @return void
     */
    public function removeBehaviour(
        DocBlox_Transformer_Behaviour_Abstract $behaviour)
    {
        foreach ($this->behaviours as $key => $thisBehaviour) {
            if ($behaviour == $thisBehaviour) {
                unset($this->behaviours[$key]);
            }
        }
    }

    /**
     * Executes the behaviour on the given dataset,
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        foreach($this->behaviours as $behaviour) {
            $xml = $behaviour->process($xml);
        }

        return $xml;
    }

}