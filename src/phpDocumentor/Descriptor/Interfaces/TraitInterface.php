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

interface TraitInterface extends BaseInterface
{
    public function setProperties(Collection $properties);

    /**
     * @return Collection
     */
    public function getProperties();

    /**
     * @return Collection
     */
    public function getInheritedProperties();

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
