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

namespace phpDocumentor\Parser;

/**
 * Provides the basic exception for the parser package.
 *
 * @link    http://phpdoc.org
 */
class Exception extends \Exception
{
    public const NO_FILES_FOUND = 2;
}
