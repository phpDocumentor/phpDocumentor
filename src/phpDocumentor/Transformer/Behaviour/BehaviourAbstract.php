<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformation
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Behaviour;

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
abstract class BehaviourAbstract
    extends \phpDocumentor\Transformer\TransformerAbstract
{
    /** @var \phpDocumentor\Transformer\Transformer $transformer */
    protected $transformer = null;

    /**
     * Executes the behaviour on the given dataset,
     *
     * @param \DOMDocument $xml document containing the source structure.
     *
     * @return \DOMDocument
     */
    abstract public function process(\DOMDocument $xml);

    /**
     * Sets the transformer used for this behaviour.
     *
     * @param \phpDocumentor\Transformer\Transformer $transformer Transformer
     *     responsible for output.
     *
     * @return void
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer that hosts this behaviour.
     *
     * @return \phpDocumentor\Transformer\Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}