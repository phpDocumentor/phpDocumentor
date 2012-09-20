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

use \phpDocumentor\Reflection\FileReflector;

/**
 * Exports the collected reflection information of a file to the given DOMElement.
 */
class FileExporter
{
    /**
     * Whether to include the source of the file in the export.
     *
     * @var bool
     */
    public $include_source = false;

    /**
     * Export the given file to the provided parent element.
     *
     * @param \DOMElement   $parent Element to augment.
     * @param FileReflector $file   Element to export.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $file
    ) {
        $child = new \DOMElement('file');
        $parent->appendChild($child);

        $child->setAttribute('path', ltrim($file->getFilename(), './'));
        $child->setAttribute('hash', $file->getHash());

        $object = new DocBlockExporter();
        $object->export($child, $file);

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = new \DOMElement('namespace-alias', $namespace);
            $child->appendChild($alias_obj);
            $alias_obj->setAttribute('name', $alias);
        }

        /** @var \phpDocumentor\Reflection\IncludeReflector $include */
        foreach ($file->getIncludes() as $include) {
            $include->setDefaultPackageName($file->getDefaultPackageName());
            $object = new IncludeExporter();
            $object->export($child, $include);
        }

        /** @var \phpDocumentor\Reflection\ConstantReflector $constant */
        foreach ($file->getConstants() as $constant) {
            $constant->setDefaultPackageName($file->getDefaultPackageName());
            $object = new ConstantExporter();
            $object->export($child, $constant);
        }

        /** @var \phpDocumentor\Reflection\FunctionReflector $function */
        foreach ($file->getFunctions() as $function) {
            $function->setDefaultPackageName($file->getDefaultPackageName());
            $object = new FunctionExporter();
            $object->export($child, $function);
        }

        /** @var \phpDocumentor\Reflection\InterfaceReflector $interface */
        foreach ($file->getInterfaces() as $interface) {
            $interface->setDefaultPackageName($file->getDefaultPackageName());
            $object = new InterfaceExporter();
            $object->export($child, $interface);
        }

        /** @var \phpDocumentor\Reflection\ClassReflector $class */
        foreach ($file->getClasses() as $class) {
            $class->setDefaultPackageName($file->getDefaultPackageName());
            $object = new ClassExporter();
            $object->export($child, $class);
        }

        // add markers
        if (count($file->getMarkers()) > 0) {
            $markers = new \DOMElement('markers');
            $child->appendChild($markers);

            foreach ($file->getMarkers() as $marker) {
                $marker_obj = new \DOMElement(
                    strtolower($marker[0]),
                    htmlspecialchars(trim($marker[1]))
                );
                $markers->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $marker[2]);
            }
        }

        if (count($file->getParseErrors()) > 0) {
            $parse_errors = new \DOMElement('parse_markers');
            $child->appendChild($parse_errors);

            foreach ($file->getParseErrors() as $error) {
                $marker_obj = new \DOMElement(
                    strtolower($error[0]),
                    htmlspecialchars(trim($error[1]))
                );
                $parse_errors->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $error[2]);
                $marker_obj->setAttribute('code', $error[3]);
            }
        }

        // if we want to include the source for each file; append a new
        // element 'source' which contains a compressed, encoded version
        // of the source
        if ($this->include_source) {
            $child->appendChild(
                new \DOMElement(
                    'source',
                    base64_encode(gzcompress($file->getContents()))
                )
            );
        }
    }
}