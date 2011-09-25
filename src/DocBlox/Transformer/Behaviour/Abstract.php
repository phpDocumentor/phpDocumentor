<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformation
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
abstract class DocBlox_Transformer_Behaviour_Abstract
    extends DocBlox_Transformer_Abstract
{
    /** @var \DocBlox_Transformer $transformer */
    protected $transformer = null;

    /**
     * Executes the behaviour on the given dataset,
     *
     * @param \DOMDocument $xml
     *
     * @return \DOMDocument
     */
    abstract public function process(DOMDocument $xml);

    /**
     * Sets the transformer used for this behaviour.
     *
     * @param \DocBlox_Transformer $transformer
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer that hosts this behaviour.
     *
     * @return \DocBlox_Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}