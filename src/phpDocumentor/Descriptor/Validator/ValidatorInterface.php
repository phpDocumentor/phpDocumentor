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

namespace phpDocumentor\Descriptor\Validator;

use phpDocumentor\Reflection\BaseReflector;

/**
 * Interface dictating how a validator should be formed.
 */
interface ValidatorInterface
{
    public function validate($element);
}
