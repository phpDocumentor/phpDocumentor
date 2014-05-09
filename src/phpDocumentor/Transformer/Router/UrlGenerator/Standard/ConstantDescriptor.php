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

class ConstantDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Descriptor\ConstantDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        $prefix = ($node->getParent() instanceof Descriptor\FileDescriptor || ! $node->getParent())
            ? $this->getUrlPathPrefixForGlobalConstants($node)
            : $this->getUrlPathPrefixForClassConstants($node);

        return $prefix . '.html#constant_' . $node->getName();
    }

    /**
     * Returns the first part of the URL path that is specific to global constants.
     *
     * @param Descriptor\ConstantDescriptor $node
     * @return string
     */
    private function getUrlPathPrefixForGlobalConstants($node)
    {
        $namespaceName = $this->convertFqcnToFilename($node->getNamespace());

        // convert root namespace to default; default is a keyword and no namespace CAN be named as such
        if ($namespaceName === '') {
            $namespaceName = 'default';
        }

        return '/namespaces/' . $namespaceName;
    }

    /**
     * Returns the first part of the URL path that is specific to class constants.
     *
     * @param Descriptor\ConstantDescriptor $node
     *
     * @return string
     */
    private function getUrlPathPrefixForClassConstants($node)
    {
        $className = $node->getParent()->getFullyQualifiedStructuralElementName();

        return '/classes/' . $this->convertFqcnToFilename($className);
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
