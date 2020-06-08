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

namespace phpDocumentor\Transformer\Writer\Exception;

use RuntimeException;

/**
 * This exception should be thrown by a Writer when it is missing one of its requirements.
 */
class RequirementMissing extends RuntimeException
{
}
