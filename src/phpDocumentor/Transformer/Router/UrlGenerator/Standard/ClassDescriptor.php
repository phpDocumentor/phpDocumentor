<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use phpDocumentor\Descriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

class ClassDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Descriptor\ClassDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        return ($node instanceof Descriptor\DescriptorAbstract)
            ? '/classes/' . $this->convertFqcnToFilename($node->getFullyQualifiedStructuralElementName()) .'.html'
            : false;
    }

    /**
     * Converts the provided FQCN into a file name by replacing all slashes with dots.
     *
     * @param string $fqcn
     *
     * @return string
     */
    private function convertFqcnToFilename($fqcn)
    {
        return str_replace('\\', '.', ltrim($fqcn, '\\'));
    }
}
