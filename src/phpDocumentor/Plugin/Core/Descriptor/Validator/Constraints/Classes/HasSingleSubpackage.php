<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes;

use Symfony\Component\Validator\Constraint;

/**
 * Validates whether a file, class, interface or trait has not more than 1 subpackage tags.
 */
class HasSingleSubpackage extends Constraint
{
    /** @var string message phpDocumentor uses codes that are defined in a messages.[lang].php file in the a plugin. */
    public $message = 'PPC:ERR-50002';

    public $code = 50002;

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
