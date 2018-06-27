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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Descriptor representing a global function in a file.
 */
interface FunctionInterface extends ElementInterface, TypeInterface
{
    /**
     * Sets the arguments related to this function.
     */
    public function setArguments(Collection $arguments);

    /**
     * Returns the arguments related to this function.
     *
     * @return Collection
     */
    public function getArguments();
}
