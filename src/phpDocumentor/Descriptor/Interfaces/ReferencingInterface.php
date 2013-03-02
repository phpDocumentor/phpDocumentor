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

/**
 * Determines whether a Descriptor contains any references to other classes and contains a method to null them.
 */
interface ReferencingInterface
{
    /**
     * References to child Descriptors/objects should be assigned a null when the containing object is nulled.
     *
     * In this method should all references to objects be assigned the value null; this will clear the references
     * of child objects from other objects.
     *
     * For example:
     *
     *     A class should NULL its constants, properties and methods as they are contained WITHIN the class and become
     *     orphans if not nulled.
     *
     * @return void
     */
    public function clearReferences();
}
