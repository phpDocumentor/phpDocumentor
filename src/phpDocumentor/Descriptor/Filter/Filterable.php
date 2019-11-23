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

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;

/**
 * Interface to determine which elements can be filtered and to provide a way to set errors on the descriptor.
 */
interface Filterable extends Descriptor
{
    /**
     * Sets a list of errors on the associated element.
     */
    public function setErrors(Collection $errors): void;
}
