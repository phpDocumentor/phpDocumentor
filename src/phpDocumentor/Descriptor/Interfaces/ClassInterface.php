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
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface ClassInterface extends BaseInterface, ChildInterface
{
    public function setInterfaces(Collection $interfaces);

    /**
     * @return Collection
     */
    public function getInterfaces();

    public function setFinal($final);

    public function isFinal();

    public function setAbstract($abstract);

    public function isAbstract();

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

    public function setProperties(Collection $properties);

    /**
     * @return Collection
     */
    public function getProperties();

    /**
     * @return Collection
     */
    public function getInheritedProperties();
}
