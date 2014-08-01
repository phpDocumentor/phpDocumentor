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

namespace phpDocumentor\Configuration\Merger\Annotation;

/**
 * Declares that, when merging objects, the property with this annotation should be replaced and not merged with the
 * property of the same name in the secondary object.
 *
 * Normal behaviour for the Merger is to merge two properties with the same name if they contain an array of items.
 * When this annotation is used on a property then this behavior is altered and the associated property is always
 * replaced with the newer version.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Replace
{
}
