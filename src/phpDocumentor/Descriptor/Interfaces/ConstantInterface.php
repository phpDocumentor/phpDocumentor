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

interface ConstantInterface extends BaseInterface
{
    public function setTypes(array $types);

    /**
     * @return array[]
     */
    public function getTypes();

    public function setValue($value);

    public function getValue();
}
