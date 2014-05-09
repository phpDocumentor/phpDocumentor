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

class FileDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Descriptor\FileDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        return '/files/' . $this->convertFilePathToUrl($node->getPath()) .'.html';
    }

    /**
     * Converts the given path to a valid url.
     *
     * @param string $path
     *
     * @return string
     */
    private function convertFilePathToUrl($path)
    {
        $path = $this->removeFileExtensionFromPath($path);

        return str_replace(array('/', '\\'), '.', ltrim($path, '/'));
    }

    /**
     * Removes the file extension from the provided path.
     *
     * @param string $path
     *
     * @return string
     */
    private function removeFileExtensionFromPath($path)
    {
        if (strrpos($path, '.') !== false) {
            $path = substr($path, 0, strrpos($path, '.'));
        }

        return $path;
    }
}
