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
 * Describes the public interface of the description for a method.
 */
interface MethodInterface extends ElementInterface, TypeInterface
{
    /**
     * @param boolean $abstract
     */
    public function setAbstract($abstract);

    /**
     * @return boolean
     */
    public function isAbstract();

    /**
     * @return Collection
     */
    public function getArguments();

    /**
     * @param boolean $final
     */
    public function setFinal($final);

    /**
     * @return boolean
     */
    public function isFinal();

    /**
     * @param boolean $static
     */
    public function setStatic($static);

    /**
     * @return boolean
     */
    public function isStatic();

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility);
}
