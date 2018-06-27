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
 * Interface representing the common interface for all elements that can contain sub-elements.
 */
interface ContainerInterface
{
    /**
     * @return Collection
     */
    public function getConstants();

    /**
     * @return Collection
     */
    public function getFunctions();

    /**
     * @return Collection
     */
    public function getClasses();

    /**
     * @return Collection
     */
    public function getInterfaces();

    /**
     * @return Collection
     */
    public function getTraits();
}
