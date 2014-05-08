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
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions;

use Symfony\Component\Validator\Constraint;

/**
 * Validates whether a function has a default return type in the docblock present.
 */
class DoesArgumentNameMatchParam extends Constraint
{
    /** @var string message phpDocumentor uses codes that are defined in a messages.[lang].php file in the a plugin. */
    public $message = 'PPC:ERR-50014';

    public $code = 50014;

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
