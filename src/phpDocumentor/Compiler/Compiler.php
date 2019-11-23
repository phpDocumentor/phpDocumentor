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

namespace phpDocumentor\Compiler;

use SplPriorityQueue;

/**
 * Contains a series of compiler steps in a specific order; ready to be executed during transformation.
 */
class Compiler extends SplPriorityQueue
{
    /** @var integer Default priority assigned to Compiler Passes without provided priority */
    const PRIORITY_DEFAULT = 10000;

    public function insert($value, $priority = self::PRIORITY_DEFAULT): bool
    {
        /** @noinspection PhpStrictTypeCheckingInspection phpstorm stubs are incorrect here. */
        return parent::insert($value, $priority);
    }
}
