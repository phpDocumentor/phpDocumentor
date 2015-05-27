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
 * Validates whether a function has a default param type in the docblock present.
 */
class IsParamTypeNotAnIdeDefault extends Constraint
{
    /** @var string message */
    public $message = 'The type for the param tag %s in %s is either "type" or "unknown"; isn\'t this an IDE default?';

    public $code = 50018;

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
