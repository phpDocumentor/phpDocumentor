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

interface PropertyInterface extends BaseInterface
{
    public function setDefault($default);

    public function getDefault();

    public function setStatic($static);

    public function isStatic();

    public function setTypes(array $types);

    /**
     * @return string[]
     */
    public function getTypes();

    public function setVisibility($visibility);

    public function getVisibility();
}
