<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions;

use Symfony\Component\Validator\Constraint;

/**
 * Validates whether a function has a default return type in the docblock present.
 */
class AreAllArgumentsValid extends Constraint
{
    /**
     * Returns that the constraint can be put onto classes.
     *
     * @return string
     */
    public function getTargets()
    {
        return array(self::CLASS_CONSTRAINT);
    }
}
