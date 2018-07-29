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
 * Describes the public interface for a description of a File.
 */
interface FileInterface extends ElementInterface, ContainerInterface
{
    public function getHash();

    public function setSource($source);

    /**
     * @return string|null
     */
    public function getSource();

    /**
     * @return Collection
     */
    public function getNamespaceAliases();

    /**
     * @return Collection
     */
    public function getIncludes();

    /**
     * @return Collection
     */
    public function getErrors();
}
