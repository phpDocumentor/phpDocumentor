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
 * Checks the typehint of the argument versus the @param tag.
 *
 * If the argument has no typehint we do not check anything. When multiple
 * type are given then the typehint needs to be one of them.
 */
class DoesArgumentTypehintMatchParam extends Constraint
{
    // @codingStandardsIgnoreStart
    /** @var string message */
    public $message = 'The type hint of the argument is incorrect for the type definition of the @param tag with argument %s in %s';
    // @codingStandardsIgnoreEnd

    public $code = 50016;

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
