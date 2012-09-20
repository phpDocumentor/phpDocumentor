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

use phpDocumentor\Reflection\InterfaceReflector;

/**
 * Exports the details of an interface to XML.
 */
class InterfaceExporter
{
    /**
     * Export this interface definition to the given parent DOMElement.
     *
     * @param \DOMElement        $parent    Element to augment.
     * @param InterfaceReflector $interface Element to log export.
     * @param \DOMElement        $child     if supplied this element will be
     *     augmented instead of freshly added.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $interface, \DOMElement $child = null
    ) {
        if ($child === null) {
            $child = new \DOMElement('interface');
            $parent->appendChild($child);
        }

        $child->setAttribute('namespace', $interface->getNamespace());
        $child->setAttribute('line', $interface->getLineNumber());

        $short_name = method_exists($interface, 'getShortName')
            ? $interface->getShortName() : $interface->getName();

        $child->appendChild(new \DOMElement('name', $short_name));
        $child->appendChild(
            new \DOMElement('full_name', $interface->getName())
        );

        foreach ($interface->getParentInterfaces() as $parent_interface) {
            $child->appendChild(
                new \DOMElement('extends', $parent_interface)
            );
        }

        $object = new DocBlockExporter();
        $object->export($child, $interface);

        foreach ($interface->getConstants() as $constant) {
            $object = new ConstantExporter();
            $constant->setDefaultPackageName($interface->getDefaultPackageName());
            $object->export($child, $constant);
        }

        foreach ($interface->getProperties() as $property) {
            $object = new PropertyExporter();
            $property->setDefaultPackageName($interface->getDefaultPackageName());
            $object->export($child, $property);
        }

        foreach ($interface->getMethods() as $method) {
            $object = new MethodExporter();
            $method->setDefaultPackageName($interface->getDefaultPackageName());
            $object->export($child, $method);
        }
    }
}
