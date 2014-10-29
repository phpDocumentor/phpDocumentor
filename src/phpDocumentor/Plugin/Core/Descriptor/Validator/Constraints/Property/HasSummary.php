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
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Property;

use Symfony\Component\Validator\Constraint;

/**
 * Detects whether a property has a summary, or an `@var` tag with a description.
 */
class HasSummary extends Constraint
{
    /** @var string message phpDocumentor uses codes that are defined in a messages.[lang].php file in the a plugin. */
    public $message = 'PPC:ERR-50007';

    public $code = 50007;

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
