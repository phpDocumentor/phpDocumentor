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
class phpDocumentor_Parser_Exporter_Xml_Interface
{
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_Interface $interface,
        DOMElement $child = null
    ) {
        if ($child === null) {
            $child = new DOMElement('interface');
            $parent->appendChild($child);
        }

        $child->setAttribute('namespace', $interface->getNamespace());
        $child->setAttribute('line', $interface->getLineNumber());

        $child->appendChild(new DOMElement('name', $interface->getName()));
        $child->appendChild(
            new DOMElement(
                'full_name', $interface->expandType($interface->getName(), true)
            )
        );
        $child->appendChild(
            new DOMElement('extends', $interface->getParentClass()
                    ? $interface->expandType($interface->getParentClass(), true)
                    : '')
        );

        foreach ($interface->getParentInterfaces() as $parent_interface) {
            $child->appendChild(
                new DOMElement(
                    'implements', $interface->expandType($parent_interface, true)
                )
            );
        }

        $object = new phpDocumentor_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $interface);

        foreach ($interface->getConstants() as $constant) {
            $object = new phpDocumentor_Parser_Exporter_Xml_Constant();
            $object->export($child, $constant);
        }

        foreach ($interface->getProperties() as $property) {
            $object = new phpDocumentor_Parser_Exporter_Xml_Property();
            $object->export($child, $property);
        }

        foreach ($interface->getMethods() as $method) {
            $object = new phpDocumentor_Parser_Exporter_Xml_Method();
            $object->export($child, $method);
        }
    }
}