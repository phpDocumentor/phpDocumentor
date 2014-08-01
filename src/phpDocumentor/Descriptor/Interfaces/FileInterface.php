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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Describes the public interface for a description of a File.
 */
interface FileInterface extends ElementInterface, ContainerInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return void
     */
    public function setSource($source);

    /**
     * @return string|null
     */
    public function getSource();

    /**
     * @return Collection
     */
    public function getNamespaceAliases();

    /**
     * @return Collection
     */
    public function getIncludes();

    /**
     * @return Collection
     */
    public function getErrors();
}
