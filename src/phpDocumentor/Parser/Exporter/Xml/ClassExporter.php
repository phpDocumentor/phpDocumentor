<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

use phpDocumentor\Reflection\ClassReflector;

/**
 * Exports a class element's attributes and properties to a child of the given
 * parent.
 */
class ClassExporter
{

    /**
     * Exports the given reflection object to the parent XML element.
     *
     * This method creates a new child element on the given parent XML element
     * and takes the properties of the Reflection argument and sets the
     * elements and attributes on the child.
     *
     * If a child DOMElement is provided then the properties and attributes are
     * set on this but the child element is not appended onto the parent. This
     * is the responsibility of the invoker. Essentially this means that the
     * $parent argument is ignored in this case.
     *
     * @param \DOMElement    $parent The parent element to augment.
     * @param ClassReflector $class  The data source.
     * @param \DOMElement    $child  Optional: child element to use instead of
     *     creating a new one on the $parent.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $class, \DOMElement $child = null
    ) {
        if (!$child) {
            $child = new \DOMElement('class');
            $parent->appendChild($child);
        }

        $child->setAttribute('final', $class->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $class->isAbstract() ? 'true' : 'false');

        $child->appendChild(
            new \DOMElement('extends', $class->getParentClass())
        );

        $interfaces = method_exists($class, 'getInterfaces')
            ? $class->getInterfaces()
            : $class->getParentInterfaces();
        foreach ($interfaces as $interface) {
            $child->appendChild(
                new \DOMElement('implements', $interface)
            );
        }

        $object = new InterfaceExporter();
        $object->export($child, $class, $child);
    }
}
