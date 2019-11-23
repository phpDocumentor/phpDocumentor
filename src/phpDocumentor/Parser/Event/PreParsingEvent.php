<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2019 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event thrown before the parsing of an individual file.
 */
final class PreParsingEvent extends EventAbstract
{
    /** @var integer */
    protected $fileCount;

    public function setFileCount(int $fileCount): self
    {
        $this->fileCount = $fileCount;

        return $this;
    }

    public function getFileCount(): int
    {
        return $this->fileCount;
    }
}
