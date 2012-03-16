<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 *
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_Method
{
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_Method $method
    ) {
        $child = new DOMElement('method');
        $parent->appendChild($child);

        $child->setAttribute('final', $method->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $method->isAbstract() ? 'true' : 'false');
        $child->setAttribute('static', $method->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $method->getVisibility());

        $object = new phpDocumentor_Parser_Exporter_Xml_Function();
        $object->export($child, $method, $child);
    }
}