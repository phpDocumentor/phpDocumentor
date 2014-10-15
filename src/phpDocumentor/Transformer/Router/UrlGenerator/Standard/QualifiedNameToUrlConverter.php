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

/**
 * Service class used to convert Qualified names into URL paths for the Standard Router.
 */
class QualifiedNameToUrlConverter
{
    /**
     * Converts the provided FQCN into a file name by replacing all slashes and underscores with dots.
     *
     * @param string $fqcn
     *
     * @return string
     */
    public function fromPackage($fqcn)
    {
        $name = str_replace(array('\\', '_'), '.', ltrim($fqcn, '\\'));

        // convert root namespace to default; default is a keyword and no namespace CAN be named as such
        if ($name === '') {
            $name = 'default';
        }

        return $name;
    }

    /**
     * Converts the provided FQCN into a file name by replacing all slashes with dots.
     *
     * @param string $fqnn
     *
     * @return string
     */
    public function fromNamespace($fqnn)
    {
        $name = str_replace('\\', '.', ltrim($fqnn, '\\'));

        // convert root namespace to default; default is a keyword and no namespace CAN be named as such
        if ($name === '') {
            $name = 'default';
        }

        return $name;
    }

    /**
     * Converts the provided FQCN into a file name by replacing all slashes with dots.
     *
     * @param string $fqcn
     *
     * @return string
     */
    public function fromClass($fqcn)
    {
        return str_replace('\\', '.', ltrim($fqcn, '\\'));
    }

    /**
     * Converts the given path to a valid url.
     *
     * @param string $path
     *
     * @return string
     */
    public function fromFile($path)
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
