<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Type;

/**
 * Base descriptor for tags that have a type associated with them.
 */
abstract class TypedAbstract extends TagDescriptor
{
    /** @var Type $types */
    protected $types;

    /**
     * Sets a list of types associated with this tag.
     */
    public function setTypes(Type $types = null)
    {
        trigger_error('Use setType, because type is an object', E_USER_DEPRECATED);
        $this->types = $types;
    }

    /**
     * Sets a list of types associated with this tag.
     */
    public function setType(Type $types = null)
    {
        $this->types = $types;
    }


    /**
     * Returns the list of types associated with this tag.
     */
    public function getTypes(): array
    {
        trigger_error('Use getType, because type is an object', E_USER_DEPRECATED);
        return array_filter([$this->types]);
    }

    public function getType()
    {
        return $this->types;
    }
}
