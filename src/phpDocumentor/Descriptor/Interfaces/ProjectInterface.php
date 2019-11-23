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
 * Describes the public interface for the description of a project.
 */
interface ProjectInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    public function getName();

    /**
     * @return Collection
     */
    public function getFiles();

    /**
     * @return Collection
     */
    public function getIndexes();

    /**
     * @return NamespaceInterface
     */
    public function getNamespace();
}
