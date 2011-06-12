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
 * @package    Transformation
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Collection implements
    DocBlox_Transformer_Behaviour_Interface
{
    /** @var DocBlox_Transformer_Behaviour_Interface[] */
    protected $behaviours = array();

    /**
     * Initializes the list of Behaviours to execute each request.
     *
     * @param DocBlox_Transformer_Behaviour_Interface[] $behaviours
     */
    function __construct(array $behaviours)
    {
        $this->behaviours = $behaviours;
    }

    /**
     * Sets the logger for each behaviour.
     *
     * @param DocBlox_Core_Log|null $log
     *
     * @return void
     */
    public function setLogger(DocBlox_Core_Log $log = null)
    {
        foreach ($this->behaviours as $behaviour) {
            $behaviour->setLogger($log);
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