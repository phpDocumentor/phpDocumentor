<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser\Exception;

use Exception;

/**
 * Exception that is thrown when the parser expects files but is unable to find them.
 */
class FilesNotFoundException extends Exception
{
}
