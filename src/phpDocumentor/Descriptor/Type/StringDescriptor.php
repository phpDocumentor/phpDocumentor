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

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class StringDescriptor implements TypeInterface
{
    /**
     * Returns a human-readable name for this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'string';
    }

    /**
     * Returns a human-readable name for this type.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
