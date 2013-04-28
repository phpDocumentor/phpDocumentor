<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Interface representing the common interface for all elements that can contain sub-elements.
 */
interface ContainerInterface
{
    /**
     * @return Collection
     */
    public function getConstants();

    /**
     * @return Collection
     */
    public function getFunctions();

    /**
     * @return Collection
     */
    public function getClasses();

    /**
     * @return Collection
     */
    public function getInterfaces();

    /**
     * @return Collection
     */
    public function getTraits();
}
