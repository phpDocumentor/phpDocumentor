<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Exception;

use phpDocumentor\Transformer\Exception;

/**
 * Exception thrown when a template attempts to use a writer that is unknown to phpDocumentor.
 */
class UnknownWriter extends Exception
{
}
