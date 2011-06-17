<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Tag interface which forces any tag to provide a __toXML method in order to fill the structure file.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
interface DocBlox_Reflection_DocBlock_Tag_Interface
{
    /**
     * Represent a tag instance in xml format.
     *
     * @param SimpleXMLElement $xml Relative root of xml document
     */
    public function __toXml(SimpleXMLElement $xml);
}

