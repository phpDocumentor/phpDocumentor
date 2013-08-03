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

interface InterfaceInterface extends BaseInterface, ChildInterface
{
    public function setConstants(Collection $constants);

    /**
     * @return Collection
     */
    public function getConstants();

    public function setMethods(Collection $methods);

    /**
     * @return Collection
     */
    public function getMethods();

    /**
     * @return Collection
     */
    public function getInheritedMethods();
}
