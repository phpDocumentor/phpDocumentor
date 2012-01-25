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
class phpDocumentor_Plugin_Core_Transformer_Behaviour_Collection extends
    phpDocumentor_Transformer_Behaviour_Abstract
{
    /** @var phpDocumentor_Transformer */
    protected $transformer = array();

    /** @var phpDocumentor_Transformer_Behaviour_Abstract[] */
    protected $behaviours = array();

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param phpDocumentor_Transformer                      $transformer Object that
     *     executes the transformation and contains the meta-data.
     * @param phpDocumentor_Transformer_Behaviour_Abstract[] $behaviours  List of
     *     behaviours to process.
     */
    public function __construct(phpDocumentor_Transformer $transformer, array $behaviours)
    {
        $this->transformer = $transformer;

        foreach ($behaviours as $behaviour) {
            $behaviour->setTransformer($transformer);
        }

        $this->behaviours  = $behaviours;
    }

    /**
     * Adds a behaviour to a collection
     *
     * @param phpDocumentor_Transformer_Behaviour_Abstract $behaviour Behaviour to add
     *     to the collection.
     *
     * @return void
     */
    public function addBehaviour(
        phpDocumentor_Transformer_Behaviour_Abstract $behaviour
    ) {
        $this->behaviours[] = $behaviour;
    }

    /**
     * Removes a behaviour from the collection
     *
     * @param phpDocumentor_Transformer_Behaviour_Abstract $behaviour Behaviour to
     *     remove from the collection.
     *
     * @return void
     */
    public function removeBehaviour(
        phpDocumentor_Transformer_Behaviour_Abstract $behaviour
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
     * @param DOMDocument $xml Structure source to apply the behaviours on.
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        foreach ($this->behaviours as $behaviour) {
            $xml = $behaviour->process($xml);
        }

        return $xml;
    }

}