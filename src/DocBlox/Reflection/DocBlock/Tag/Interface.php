<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Reflection class for a DocBloxk Tag declaration.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Herman J. Radtke III <hermanradtke@gmail.com>
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

