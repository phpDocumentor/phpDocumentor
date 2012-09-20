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

use \phpDocumentor\Reflection\FunctionReflector;

/**
 * Exports the given reflected file to a DOMElement.
 */
class FunctionExporter
{
    /**
     * Export this function definition to the given parent DOMElement.
     *
     * @param \DOMElement       $parent   Element to augment.
     * @param FunctionReflector $function Element to export.
     * @param \DOMElement       $child    if supplied this element will be
     *     augmented instead of freshly added.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $function,
        \DOMElement $child = null
    ) {
        if (!$child) {
            $child = new \DOMElement('function');
            $parent->appendChild($child);
        }

        $child->setAttribute(
            'namespace',
            $function->getNamespace()
            ? $function->getNamespace()
            : $parent->getAttribute('namespace')
        );
        $child->setAttribute('line', $function->getLineNumber());

        $short_name = method_exists($function, 'getShortName')
            ? $function->getShortName() : $function->getName();

        $child->appendChild(new \DOMElement('name', $short_name));
        $child->appendChild(
            new \DOMElement('full_name', $function->getName())
        );

        $object = new DocBlockExporter();
        $function->setDefaultPackageName($parent->getAttribute('package'));
        $object->export($child, $function);

        foreach ($function->getArguments() as $argument) {
            $object = new ArgumentExporter();
            $object->export($child, $argument);
        }
    }
}