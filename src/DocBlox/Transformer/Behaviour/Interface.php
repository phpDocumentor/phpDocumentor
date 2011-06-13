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
interface DocBlox_Transformer_Behaviour_Interface
{
    /**
     * Executes the behaviour on the given dataset,
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml);

    /**
     * Sets the logger for this behaviour or removes it when $log is null.
     *
     * @param DocBlox_Core_Log|null $log
     *
     * @return void
     */
    public function setLogger(DocBlox_Core_Log $log = null);
}